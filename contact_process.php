<?php
require_once 'config/config.php';
require_once 'classes/Mailer.php';
require_once 'classes/Functions.php';

session_start();

$db = Database::getInstance();
$functions = Functions::getInstance();

function ensureContactMessageTables($db) {
    $db->query("CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(80),
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
        ip_address VARCHAR(100),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_contact_status (status),
        INDEX idx_contact_email (email),
        INDEX idx_contact_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->query("CREATE TABLE IF NOT EXISTS contact_replies (
        id INT PRIMARY KEY AUTO_INCREMENT,
        submission_id INT NOT NULL,
        admin_user_id INT NULL,
        reply_message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (submission_id) REFERENCES contact_submissions(id) ON DELETE CASCADE,
        INDEX idx_contact_replies_submission (submission_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['contact_submit'])) {
    header('Location: contact-us/');
    exit;
}

function clean($v) {
    return trim((string)$v);
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($phone === '') $errors[] = 'Phone is required.';
if ($subject === '') $errors[] = 'Subject is required.';
if ($message === '') $errors[] = 'Message is required.';

if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_old'] = $_POST;
    header('Location: contact-us/');
    exit;
}

try {
    ensureContactMessageTables($db);

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $db->query(
        "INSERT INTO contact_submissions (name, email, phone, subject, message, status, ip_address, user_agent, created_at)
         VALUES (:name, :email, :phone, :subject, :message, 'unread', :ip, :ua, NOW())",
        [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'ip' => $ip,
            'ua' => $ua,
        ]
    );

    $submissionId = $db->lastInsertId();

    $communicationContent = "Contact form submission\n\n" .
        "Name: {$name}\n" .
        "Email: {$email}\n" .
        "Phone: {$phone}\n" .
        "Subject: {$subject}\n\n" .
        "Message:\n{$message}";

    $db->query(
        "INSERT INTO communications (uuid, direction, type, subject, content, communication_date, attachments, created_at, updated_at)
         VALUES (:uuid, 'inbound', 'email', :subject, :content, NOW(), :attachments, NOW(), NOW())",
        [
            'uuid' => $functions->generateUUID(),
            'subject' => 'Contact Form: ' . $subject,
            'content' => $communicationContent,
            'attachments' => json_encode(['contact_submission_id' => $submissionId])
        ]
    );

    $companyEmail = $functions->getSetting('company_email', 'info@tpvconstruction.com.ng');
    if (!$companyEmail || strpos($companyEmail, 'ironbridge') !== false) {
        $companyEmail = 'info@tpvconstruction.com.ng';
    }

    $mailer = new Mailer();
    $emailSubject = 'Contact Form: ' . $subject;
    $emailBody = '<h2 style="margin:0 0 16px;color:#0f172a;">New Contact Message</h2>' .
        '<p style="margin:0 0 18px;color:#334155;">A new message was submitted from the TPV website contact form.</p>' .
        '<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;color:#334155;">' .
        '<tr><td><strong>Name</strong></td><td>' . htmlspecialchars($name) . '</td></tr>' .
        '<tr><td><strong>Email</strong></td><td><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></td></tr>' .
        '<tr><td><strong>Phone</strong></td><td>' . htmlspecialchars($phone) . '</td></tr>' .
        '<tr><td><strong>Subject</strong></td><td>' . htmlspecialchars($subject) . '</td></tr>' .
        '</table>' .
        '<h3 style="margin:22px 0 8px;color:#0f172a;">Message</h3>' .
        '<p style="white-space:pre-line;margin:0;color:#334155;line-height:1.6;">' . htmlspecialchars($message) . '</p>';

    $companySent = $mailer->send($companyEmail, $emailSubject, $emailBody, $email);

    $autoReplySubject = 'Thank You for Contacting TPV Construction and Services LTD';
    $autoReplyBody = '<h2 style="margin:0 0 16px;color:#0f172a;">Thank You for Reaching Out</h2>' .
        '<p style="margin:0 0 18px;color:#334155;">Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>' .
        '<p style="margin:0 0 18px;color:#334155;">Thank you for contacting <strong>TPV Construction and Services LTD</strong>. We have received your message and our team will review it shortly.</p>' .
        '<p style="margin:0 0 18px;color:#334155;">A member of our team will get back to you within <strong>24 hours</strong> to assist with your inquiry. We appreciate your patience and look forward to helping you.</p>' .
        '<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;color:#334155;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">' .
        '<tr><td><strong>Subject</strong></td><td>' . htmlspecialchars($subject) . '</td></tr>' .
        '<tr><td><strong>Message</strong></td><td style="white-space:pre-line;">' . htmlspecialchars($message) . '</td></tr>' .
        '</table>' .
        '<p style="margin:18px 0 0;font-size:13px;color:#64748b;">If you have any urgent concerns, please call our head office directly.</p>' .
        '<p style="margin:8px 0 0;font-size:13px;color:#64748b;">Best regards,<br><strong>TPV Construction and Services LTD</strong></p>';

    $autoReplySent = $mailer->send($email, $autoReplySubject, $autoReplyBody);

    if (!$companySent || !$autoReplySent) {
        error_log('Contact form email warning. Company sent: ' . ($companySent ? 'yes' : 'no') . '; auto reply sent: ' . ($autoReplySent ? 'yes' : 'no'));
    }

    header('Location: contact-us/?sent=1');
    exit;

} catch (Exception $e) {
    error_log('Contact form error: ' . $e->getMessage());
    $_SESSION['contact_errors'] = ['We could not submit your message. Please try again.'];
    $_SESSION['contact_old'] = $_POST;
    header('Location: contact-us/');
    exit;
}
