<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Functions.php';
require_once '../classes/Invoice.php';
require_once '../classes/Mailer.php';

$auth = new Auth();
$auth->requireAuth();
$currentUser = $auth->getUserData();
$functions = Functions::getInstance();
$db = Database::getInstance();
$invoice = new Invoice();
$mailer = new Mailer();

function invoiceClientDisplayName(array $client): string {
    $company = trim((string) ($client['company_name'] ?? ''));
    $contact = trim((string) ($client['contact_person'] ?? ''));
    $email = trim((string) ($client['email'] ?? ''));
    return $company !== '' ? $company : ($contact !== '' ? $contact : $email);
}

function invoiceCollectItemsFromPost(): array {
    $items = [];
    $descriptions = $_POST['item_description'] ?? [];
    $quantities = $_POST['item_quantity'] ?? [];
    $unitPrices = $_POST['item_unit_price'] ?? [];

    foreach ($descriptions as $index => $description) {
        $description = trim((string) $description);
        $quantity = (float) ($quantities[$index] ?? 0);
        $unitPrice = (float) ($unitPrices[$index] ?? 0);

        if ($description === '') {
            continue;
        }

        $quantity = $quantity > 0 ? $quantity : 1;
        $unitPrice = $unitPrice >= 0 ? $unitPrice : 0;

        $items[] = [
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $quantity * $unitPrice,
        ];
    }

    return $items;
}

function invoiceBadgeClass(string $status): string {
    return match ($status) {
        'draft' => 'bg-secondary-subtle text-secondary',
        'sent' => 'bg-primary-subtle text-primary',
        'partial' => 'bg-warning-subtle text-warning',
        'paid' => 'bg-success-subtle text-success',
        'overdue' => 'bg-danger-subtle text-danger',
        'cancelled' => 'bg-dark-subtle text-dark',
        default => 'bg-light text-muted',
    };
}

function invoicePublicUrl(string $token): string {
    return SITE_URL . 'invoice-view.php?token=' . urlencode($token);
}

function invoiceDefaultEmailSubject(array $invoiceData, string $companyName): string {
    return 'Invoice ' . ($invoiceData['invoice_number'] ?? '') . ' from ' . $companyName;
}

function invoiceDefaultEmailMessage(array $invoiceData, string $companyName): string {
    $clientName = trim((string) ($invoiceData['recipient_name'] ?? $invoiceData['client_name'] ?? ''));
    $greeting = $clientName !== '' ? 'Dear ' . $clientName . ',' : 'Hello,';
    return $greeting . "\n\n"
        . 'Please find your invoice ' . ($invoiceData['invoice_number'] ?? '') . ' from ' . $companyName . ".\n"
        . "You can review the full invoice details using the secure link below.\n\n"
        . 'If you have any questions, simply reply to this email and our team will assist you.';
}

function invoiceBuildEmailBody(array $invoiceData, array $items, array $company, string $customMessage, string $publicUrl, string $terms, string $footer): string {
    $rows = '';
    foreach ($items as $item) {
        $rows .= '<tr>'
            . '<td style="padding:14px 16px;border-bottom:1px solid #e2e8f0;color:#0f172a;font-size:14px;line-height:1.6;">' . htmlspecialchars($item['description']) . '</td>'
            . '<td style="padding:14px 16px;border-bottom:1px solid #e2e8f0;color:#475569;text-align:center;font-size:14px;">' . number_format((float) $item['quantity'], 2) . '</td>'
            . '<td style="padding:14px 16px;border-bottom:1px solid #e2e8f0;color:#475569;text-align:right;font-size:14px;">' . htmlspecialchars($company['format_currency']((float) $item['unit_price'])) . '</td>'
            . '<td style="padding:14px 16px;border-bottom:1px solid #e2e8f0;color:#0f172a;text-align:right;font-size:14px;font-weight:700;">' . htmlspecialchars($company['format_currency']((float) ($item['line_total'] ?? 0))) . '</td>'
            . '</tr>';
    }

    if ($rows === '') {
        $rows = '<tr><td colspan="4" style="padding:14px 16px;color:#64748b;border-bottom:1px solid #e2e8f0;font-size:14px;">Invoice items will be available on the secure invoice page.</td></tr>';
    }

    $recipientName = trim((string) ($invoiceData['recipient_name'] ?? $invoiceData['client_name'] ?? ''));
    $recipientEmail = trim((string) ($invoiceData['recipient_email'] ?? $invoiceData['client_email'] ?? ''));
    $projectName = trim((string) ($invoiceData['project_name'] ?? ''));
    $balance = (float) $invoiceData['total'] - (float) $invoiceData['amount_paid'];

    $body = '<div style="margin:0 0 24px;padding:0 0 22px;border-bottom:1px solid #e2e8f0;">';
    $body .= '<div style="font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin:0 0 10px;">Invoice Notice</div>';
    $body .= '<div style="font-size:32px;line-height:1.02;font-weight:800;color:#0f172a;letter-spacing:-0.04em;margin:0 0 10px;">' . htmlspecialchars($invoiceData['invoice_number']) . '</div>';
    $body .= '<div style="font-size:15px;line-height:1.8;color:#475569;">' . nl2br(htmlspecialchars($customMessage)) . '</div>';
    $body .= '</div>';

    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0;margin:0 0 22px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;">';
    $body .= '<tr>';
    $body .= '<td style="padding:18px 20px;border-right:1px solid #e2e8f0;vertical-align:top;">'
        . '<div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Billed To</div>'
        . '<div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px;">' . htmlspecialchars($recipientName !== '' ? $recipientName : 'Invoice Recipient') . '</div>'
        . ($recipientEmail !== '' ? '<div style="font-size:14px;color:#475569;">' . htmlspecialchars($recipientEmail) . '</div>' : '')
        . ($projectName !== '' ? '<div style="font-size:13px;color:#64748b;margin-top:8px;">Project: ' . htmlspecialchars($projectName) . '</div>' : '')
        . '</td>';
    $body .= '<td style="padding:18px 20px;vertical-align:top;">'
        . '<div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Issued By</div>'
        . '<div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px;">' . htmlspecialchars($company['name']) . '</div>'
        . '<div style="font-size:14px;color:#475569;">' . htmlspecialchars($company['email']) . '</div>'
        . '<div style="font-size:14px;color:#475569;">' . htmlspecialchars($company['phone']) . '</div>'
        . '<div style="font-size:13px;color:#64748b;margin-top:8px;line-height:1.6;">' . nl2br(htmlspecialchars($company['address'])) . '</div>'
        . '</td>';
    $body .= '</tr>';
    $body .= '</table>';

    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0;margin:0 0 22px;background:#ffffff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;">';
    $body .= '<tr>'
        . '<td style="padding:18px 14px;text-align:center;border-right:1px solid #e2e8f0;">'
        . '<div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Invoice Date</div>'
        . '<div style="font-size:18px;font-weight:700;color:#0f172a;">' . htmlspecialchars(date('M j, Y', strtotime($invoiceData['invoice_date']))) . '</div>'
        . '</td>'
        . '<td style="padding:18px 14px;text-align:center;border-right:1px solid #e2e8f0;">'
        . '<div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Due Date</div>'
        . '<div style="font-size:18px;font-weight:700;color:#0f172a;">' . htmlspecialchars(date('M j, Y', strtotime($invoiceData['due_date']))) . '</div>'
        . '</td>'
        . '<td style="padding:18px 14px;text-align:center;border-right:1px solid #e2e8f0;">'
        . '<div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Total Invoice</div>'
        . '<div style="font-size:22px;font-weight:800;color:#0f172a;">' . htmlspecialchars($company['format_currency']((float) $invoiceData['total'])) . '</div>'
        . '</td>'
        . '<td style="padding:18px 14px;text-align:center;">'
        . '<div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:8px;">Balance Due</div>'
        . '<div style="font-size:22px;font-weight:800;color:#dc2626;">' . htmlspecialchars($company['format_currency']($balance)) . '</div>'
        . '</td>'
        . '</tr>';
    $body .= '</table>';

    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 20px;background:#ffffff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden;">';
    $body .= '<thead><tr style="background:#0f172a;">'
        . '<th style="padding:13px 16px;text-align:left;color:#ffffff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;">Description</th>'
        . '<th style="padding:13px 16px;text-align:center;color:#ffffff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;">Qty</th>'
        . '<th style="padding:13px 16px;text-align:right;color:#ffffff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;">Unit Price</th>'
        . '<th style="padding:13px 16px;text-align:right;color:#ffffff;font-size:11px;letter-spacing:.08em;text-transform:uppercase;">Line Total</th>'
        . '</tr></thead><tbody>' . $rows . '</tbody></table>';

    $body .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:0;margin:0 0 24px;background:#fbfdff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;">';
    $body .= '<tr><td style="padding:12px 18px;color:#64748b;font-size:14px;">Subtotal</td><td style="padding:12px 18px;color:#0f172a;font-size:14px;font-weight:700;text-align:right;">' . htmlspecialchars($company['format_currency']((float) $invoiceData['subtotal'])) . '</td></tr>';
    $body .= '<tr><td style="padding:12px 18px;color:#64748b;font-size:14px;border-top:1px solid #e2e8f0;">Tax</td><td style="padding:12px 18px;color:#0f172a;font-size:14px;font-weight:700;text-align:right;border-top:1px solid #e2e8f0;">' . htmlspecialchars($company['format_currency']((float) $invoiceData['tax'])) . '</td></tr>';
    $body .= '<tr><td style="padding:12px 18px;color:#64748b;font-size:14px;border-top:1px solid #e2e8f0;">Amount Paid</td><td style="padding:12px 18px;color:#0f172a;font-size:14px;font-weight:700;text-align:right;border-top:1px solid #e2e8f0;">' . htmlspecialchars($company['format_currency']((float) $invoiceData['amount_paid'])) . '</td></tr>';
    $body .= '<tr><td style="padding:14px 18px;color:#0f172a;font-size:14px;font-weight:800;border-top:1px solid #e2e8f0;">Outstanding Balance</td><td style="padding:14px 18px;color:#dc2626;font-size:16px;font-weight:800;text-align:right;border-top:1px solid #e2e8f0;">' . htmlspecialchars($company['format_currency']($balance)) . '</td></tr>';
    $body .= '</table>';

    $body .= '<div style="margin:0 0 10px;text-align:center;">'
        . '<a href="' . htmlspecialchars($publicUrl) . '" style="display:inline-block;padding:15px 24px;border-radius:999px;background:#ef4444;color:#ffffff;text-decoration:none;font-weight:800;font-size:14px;letter-spacing:.01em;">Review Invoice Securely</a>'
        . '</div>';
    $body .= '<div style="margin:0 0 24px;text-align:center;font-size:13px;line-height:1.8;color:#64748b;">If the button does not open, copy and paste this link into your browser:<br><a href="' . htmlspecialchars($publicUrl) . '" style="color:#2563eb;text-decoration:none;">' . htmlspecialchars($publicUrl) . '</a></div>';

    if ($terms !== '') {
        $body .= '<div style="margin:0 0 16px;padding:18px 20px;border:1px solid #e2e8f0;border-radius:18px;background:#ffffff;">'
            . '<div style="font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;font-weight:700;margin-bottom:10px;">Invoice Terms</div>'
            . '<div style="font-size:14px;line-height:1.8;color:#475569;">' . nl2br(htmlspecialchars($terms)) . '</div>'
            . '</div>';
    }
    if ($footer !== '') {
        $body .= '<div style="font-size:13px;line-height:1.8;color:#64748b;">' . nl2br(htmlspecialchars($footer)) . '</div>';
    }

    return $body;
}

$companyName = trim((string) $functions->getSetting('company_name', 'TPV Construction and Services LTD'));
$companyEmail = trim((string) $functions->getSetting('company_email', 'info@tpvconstruction.com.ng'));
$companyPhone = trim((string) $functions->getSetting('company_phone', '+234 701 234 5678'));
$companyAddress = trim((string) $functions->getSetting('company_address', 'Area 11, Abuja, Nigeria'));
$invoiceTerms = trim((string) $functions->getSetting('invoice_terms', 'Payment is due on or before the due date stated on the invoice.'));
$invoiceFooter = trim((string) $functions->getSetting('invoice_footer', 'Thank you for choosing TPV Construction and Services LTD.'));

$companyContext = [
    'name' => $companyName,
    'email' => $companyEmail,
    'phone' => $companyPhone,
    'address' => $companyAddress,
    'format_currency' => fn(float $amount): string => $functions->formatCurrency($amount),
];

$clients = $db->query("SELECT id, company_name, contact_person, email FROM clients WHERE deleted_at IS NULL ORDER BY company_name, contact_person")->fetchAll();
$projects = $db->query("SELECT id, name FROM projects WHERE deleted_at IS NULL ORDER BY name")->fetchAll();
$clientsById = [];
foreach ($clients as $clientRow) {
    $clientsById[(int) $clientRow['id']] = $clientRow;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$auth->verifyCSRF($_POST['csrf_token'] ?? '')) {
        $_SESSION['toast_error'] = 'Security validation failed. Please try again.';
        header('Location: invoices.php');
        exit;
    }

    try {
        if (in_array($_POST['action'], ['create', 'update'], true)) {
            $clientId = (int) ($_POST['client_id'] ?? 0);
            $client = $clientsById[$clientId] ?? null;
            $items = invoiceCollectItemsFromPost();
            $tax = max(0, (float) ($_POST['tax'] ?? 0));
            $subtotal = array_sum(array_map(fn($item) => (float) $item['line_total'], $items));
            $total = $subtotal + $tax;

            if (!$client) {
                throw new Exception('Please select a valid client.');
            }
            if (empty($_POST['invoice_date']) || empty($_POST['due_date'])) {
                throw new Exception('Invoice date and due date are required.');
            }
            if (empty($items)) {
                throw new Exception('Add at least one invoice line item before saving.');
            }

            $recipientName = trim((string) ($_POST['recipient_name'] ?? ''));
            if ($recipientName === '') {
                $recipientName = invoiceClientDisplayName($client);
            }
            $recipientEmail = trim((string) ($_POST['recipient_email'] ?? ''));
            if ($recipientEmail === '') {
                $recipientEmail = trim((string) ($client['email'] ?? ''));
            }

            $payload = [
                'invoice_number' => trim((string) ($_POST['invoice_number'] ?? '')),
                'project_id' => ($_POST['project_id'] ?? '') !== '' ? (int) $_POST['project_id'] : null,
                'client_id' => $clientId,
                'recipient_name' => $recipientName,
                'recipient_email' => $recipientEmail,
                'invoice_date' => $_POST['invoice_date'],
                'due_date' => $_POST['due_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $_POST['status'] ?? 'draft',
                'notes' => trim((string) ($_POST['notes'] ?? '')),
                'email_subject' => trim((string) ($_POST['email_subject'] ?? '')),
                'email_message' => trim((string) ($_POST['email_message'] ?? '')),
                'created_by' => $currentUser['id'] ?? null,
            ];

            if ($_POST['action'] === 'create') {
                $invoiceId = $invoice->create($payload);
                $invoice->syncItems($invoiceId, $items);
                $_SESSION['toast_success'] = 'Invoice created successfully.';
            } else {
                $invoiceId = (int) ($_POST['id'] ?? 0);
                $existing = $invoice->getById($invoiceId);
                if (!$existing) {
                    throw new Exception('Invoice not found.');
                }
                $payload['amount_paid'] = (float) ($existing['amount_paid'] ?? 0);
                $invoice->update($invoiceId, $payload);
                $invoice->syncItems($invoiceId, $items);
                $invoice->updateInvoicePaidAmount($invoiceId);
                $_SESSION['toast_success'] = 'Invoice updated successfully.';
            }

            header('Location: invoices.php?action=view&id=' . $invoiceId);
            exit;
        }

        if ($_POST['action'] === 'add_payment') {
            $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
            $amount = (float) ($_POST['amount'] ?? 0);
            if ($invoiceId <= 0 || $amount <= 0) {
                throw new Exception('A valid invoice and payment amount are required.');
            }

            $invoice->addPayment([
                'invoice_id' => $invoiceId,
                'payment_date' => $_POST['payment_date'],
                'amount' => $amount,
                'payment_method' => $_POST['payment_method'],
                'reference_number' => trim((string) ($_POST['reference_number'] ?? '')),
                'notes' => trim((string) ($_POST['notes'] ?? '')),
            ]);

            $_SESSION['toast_success'] = 'Payment recorded successfully.';
            header('Location: invoices.php?action=view&id=' . $invoiceId);
            exit;
        }

        if ($_POST['action'] === 'send_email') {
            $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
            $invoiceData = $invoice->getById($invoiceId);
            if (!$invoiceData) {
                throw new Exception('Invoice not found.');
            }

            $items = $invoice->getItems($invoiceId);
            $recipientEmail = trim((string) ($_POST['recipient_email'] ?? $invoiceData['recipient_email'] ?? $invoiceData['client_email'] ?? ''));
            $subject = trim((string) ($_POST['email_subject'] ?? ''));
            $message = trim((string) ($_POST['email_message'] ?? ''));

            if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Enter a valid recipient email before sending.');
            }
            if ($subject === '') {
                $subject = invoiceDefaultEmailSubject($invoiceData, $companyName);
            }
            if ($message === '') {
                $message = invoiceDefaultEmailMessage($invoiceData, $companyName);
            }

            $publicUrl = invoicePublicUrl($invoiceData['public_token']);
            $emailBody = invoiceBuildEmailBody($invoiceData, $items, $companyContext, $message, $publicUrl, $invoiceTerms, $invoiceFooter);
            $sent = $mailer->send($recipientEmail, $subject, $emailBody, $companyEmail);

            if (!$sent) {
                throw new Exception('Invoice email could not be sent. Please confirm your mail settings.');
            }

            $invoice->markAsSent($invoiceId, $recipientEmail, $subject, $message);
            $db->query(
                "INSERT INTO communications (
                    uuid, project_id, client_id, direction, type, subject, content, communication_date, attachments, created_by, created_at, updated_at
                ) VALUES (
                    :uuid, :project_id, :client_id, 'outbound', 'email', :subject, :content, NOW(), :attachments, :created_by, NOW(), NOW()
                )",
                [
                    'uuid' => $functions->generateUUID(),
                    'project_id' => $invoiceData['project_id'] ?: null,
                    'client_id' => $invoiceData['client_id'] ?: null,
                    'subject' => $subject,
                    'content' => $message,
                    'attachments' => json_encode([
                        'invoice_id' => $invoiceId,
                        'invoice_number' => $invoiceData['invoice_number'],
                        'recipient_email' => $recipientEmail,
                        'public_url' => $publicUrl,
                    ]),
                    'created_by' => $currentUser['id'] ?? null,
                ]
            );

            $_SESSION['toast_success'] = 'Invoice emailed successfully to ' . $recipientEmail . '.';
            header('Location: invoices.php?action=view&id=' . $invoiceId);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['toast_error'] = 'Error: ' . $e->getMessage();
        $redirectId = isset($_POST['id']) ? (int) $_POST['id'] : (int) ($_POST['invoice_id'] ?? 0);
        if ($redirectId > 0) {
            header('Location: invoices.php?action=view&id=' . $redirectId);
        } else {
            header('Location: invoices.php');
        }
        exit;
    }
}

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    try {
        $invoice->delete((int) $_GET['id']);
        $_SESSION['toast_success'] = 'Invoice deleted successfully.';
    } catch (Exception $e) {
        $_SESSION['toast_error'] = 'Error deleting invoice: ' . $e->getMessage();
    }
    header('Location: invoices.php');
    exit;
}

$stats = $invoice->getStats();
$invoices = $invoice->getAll();
$action = $_GET['action'] ?? '';
$viewData = null;
$viewItems = [];
$viewPayments = [];
$editData = null;
$editItems = [];

if ($action === 'view' && isset($_GET['id'])) {
    $viewData = $invoice->getById((int) $_GET['id']);
    if ($viewData) {
        $viewItems = $invoice->getItems((int) $_GET['id']);
        $viewPayments = $invoice->getPayments((int) $_GET['id']);
    }
}

if ($action === 'new' || ($action === 'edit' && isset($_GET['id']))) {
    if ($action === 'edit') {
        $editData = $invoice->getById((int) $_GET['id']);
        $editItems = $editData ? $invoice->getItems((int) $_GET['id']) : [];
    }
}

$newInvoiceNumber = $invoice->generateInvoiceNumber();
$pageActive = 'invoices';
$pageTitle = 'TPV Construction and Services LTD · Invoices';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-25 p-r-25 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php">TPV Construction and Services LTD</a></li>
                <li class="breadcrumb-item"><a href="#">Financial</a></li>
                <li class="breadcrumb-item active">Invoices</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-25 p-r-25 p-t-0 p-b-25 sm-padding-10">
    <style>
        .invoice-shell { display:grid; gap:24px; }
        .invoice-hero { background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#334155 100%); border-radius:28px; padding:30px; color:#fff; box-shadow:0 24px 60px rgba(15,23,42,.18); }
        .invoice-hero h1 { margin:0; font-size:2rem; font-weight:800; letter-spacing:-.03em; }
        .invoice-hero p { margin:10px 0 0; max-width:720px; color:rgba(255,255,255,.78); }
        .invoice-actions { display:flex; gap:12px; flex-wrap:wrap; margin-top:22px; }
        .invoice-stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; }
        .invoice-stat-card { background:#fff; border-radius:22px; padding:22px; box-shadow:0 18px 42px rgba(15,23,42,.08); border:1px solid rgba(148,163,184,.15); }
        .invoice-stat-label { font-size:.78rem; text-transform:uppercase; letter-spacing:.08em; color:#64748b; margin-bottom:10px; font-weight:700; }
        .invoice-stat-value { font-size:1.8rem; font-weight:800; color:#0f172a; line-height:1; }
        .invoice-stat-meta { margin-top:8px; color:#64748b; font-size:.95rem; }
        .invoice-panel { background:#fff; border-radius:26px; box-shadow:0 18px 42px rgba(15,23,42,.08); border:1px solid rgba(148,163,184,.14); overflow:hidden; }
        .invoice-panel-head { padding:22px 26px; border-bottom:1px solid #eef2f7; display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
        .invoice-panel-head h2 { margin:0; font-size:1.08rem; font-weight:800; color:#0f172a; }
        .invoice-panel-body { padding:24px 26px; }
        .invoice-summary-grid { display:grid; grid-template-columns:1.2fr .8fr; gap:22px; }
        .invoice-card-soft { border:1px solid #e2e8f0; border-radius:22px; padding:20px; background:#fbfdff; }
        .invoice-kv { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; }
        .invoice-kv small { display:block; color:#64748b; text-transform:uppercase; letter-spacing:.08em; font-size:.72rem; font-weight:700; margin-bottom:6px; }
        .invoice-kv strong, .invoice-kv span { color:#0f172a; font-size:1rem; }
        .invoice-total-stack { display:grid; gap:12px; }
        .invoice-total-row { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #eef2f7; }
        .invoice-total-row:last-child { border-bottom:0; padding-bottom:0; }
        .invoice-total-row strong { color:#0f172a; }
        .invoice-table th { font-size:.78rem; text-transform:uppercase; letter-spacing:.08em; color:#64748b; border-bottom:1px solid #e2e8f0; }
        .invoice-table td { vertical-align:middle; }
        .invoice-muted { color:#64748b; }
        .invoice-public-link { font-size:.92rem; color:#475569; word-break:break-all; }
        .invoice-status-pill { display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:8px 14px; font-size:.83rem; font-weight:700; }
        .invoice-form-grid { display:grid; grid-template-columns:repeat(12,1fr); gap:16px; }
        .invoice-col-3, .invoice-col-4, .invoice-col-6, .invoice-col-8, .invoice-col-12 { grid-column:span 12; }
        .invoice-item-row { display:grid; grid-template-columns:1.9fr .55fr .8fr auto; gap:12px; margin-bottom:12px; }
        .invoice-empty { text-align:center; color:#64748b; padding:40px 12px; }
        @media (min-width: 992px) {
            .invoice-col-3 { grid-column:span 3; }
            .invoice-col-4 { grid-column:span 4; }
            .invoice-col-6 { grid-column:span 6; }
            .invoice-col-8 { grid-column:span 8; }
            .invoice-col-12 { grid-column:span 12; }
        }
        @media (max-width: 991px) {
            .invoice-summary-grid { grid-template-columns:1fr; }
            .invoice-item-row { grid-template-columns:1fr; }
        }
    </style>

    <div class="invoice-shell">
        <section class="invoice-hero">
            <h1>Invoice Desk</h1>
            <p>Create polished invoices, email them directly to clients or organizations, track delivery, and give recipients a secure page they can open and respond to.</p>
            <div class="invoice-actions">
                <a href="invoices.php?action=new" class="btn btn-danger"><i class="fas fa-plus me-2"></i>New Invoice</a>
                <a href="communications.php" class="btn btn-light"><i class="fas fa-comments me-2"></i>View Communications</a>
            </div>
        </section>

        <div class="invoice-stat-grid">
            <div class="invoice-stat-card">
                <div class="invoice-stat-label">Outstanding Balance</div>
                <div class="invoice-stat-value"><?php echo htmlspecialchars($functions->formatCurrency((float) ($stats['balance_total'] ?? 0))); ?></div>
                <div class="invoice-stat-meta"><?php echo (int) ($stats['outstanding_invoices'] ?? 0); ?> invoices still open</div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-label">Paid Invoices</div>
                <div class="invoice-stat-value"><?php echo (int) ($stats['paid_invoices'] ?? 0); ?></div>
                <div class="invoice-stat-meta"><?php echo htmlspecialchars($functions->formatCurrency((float) ($stats['amount_paid_total'] ?? 0))); ?> collected</div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-label">Draft Queue</div>
                <div class="invoice-stat-value"><?php echo (int) ($stats['draft_invoices'] ?? 0); ?></div>
                <div class="invoice-stat-meta">Ready to finalize and send</div>
            </div>
            <div class="invoice-stat-card">
                <div class="invoice-stat-label">Overdue</div>
                <div class="invoice-stat-value"><?php echo (int) ($stats['overdue_invoices'] ?? 0); ?></div>
                <div class="invoice-stat-meta">Needs follow-up</div>
            </div>
        </div>

        <?php if ($viewData): ?>
            <section class="invoice-panel">
                <div class="invoice-panel-head">
                    <div>
                        <h2><?php echo htmlspecialchars($viewData['invoice_number']); ?></h2>
                        <div class="invoice-muted">Created for <?php echo htmlspecialchars($viewData['client_name'] ?? 'Unknown client'); ?></div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="invoice-status-pill <?php echo invoiceBadgeClass($viewData['status']); ?>"><?php echo htmlspecialchars(ucfirst($viewData['status'])); ?></span>
                        <a href="<?php echo htmlspecialchars(invoicePublicUrl($viewData['public_token'])); ?>" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">Open client view</a>
                        <a href="invoices.php?action=edit&id=<?php echo (int) $viewData['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="invoices.php" class="btn btn-light btn-sm">Back to list</a>
                    </div>
                </div>
                <div class="invoice-panel-body">
                    <div class="invoice-summary-grid">
                        <div class="invoice-card-soft">
                            <div class="invoice-kv">
                                <div><small>Recipient</small><strong><?php echo htmlspecialchars($viewData['recipient_name'] ?: ($viewData['client_name'] ?? '')); ?></strong></div>
                                <div><small>Email</small><span><?php echo htmlspecialchars($viewData['recipient_email'] ?: ($viewData['client_email'] ?? '-')); ?></span></div>
                                <div><small>Project</small><span><?php echo htmlspecialchars($viewData['project_name'] ?? 'Not linked'); ?></span></div>
                                <div><small>Invoice Date</small><span><?php echo htmlspecialchars($functions->formatDate($viewData['invoice_date'], 'M j, Y')); ?></span></div>
                                <div><small>Due Date</small><span><?php echo htmlspecialchars($functions->formatDate($viewData['due_date'], 'M j, Y')); ?></span></div>
                                <div><small>Sent</small><span><?php echo !empty($viewData['last_emailed_at']) ? htmlspecialchars($functions->formatDate($viewData['last_emailed_at'], 'M j, Y g:i A')) : 'Not emailed yet'; ?></span></div>
                                <div><small>Viewed</small><span><?php echo !empty($viewData['viewed_at']) ? htmlspecialchars($functions->formatDate($viewData['viewed_at'], 'M j, Y g:i A')) : 'Not opened yet'; ?></span></div>
                                <div><small>Public Link</small><span class="invoice-public-link"><?php echo htmlspecialchars(invoicePublicUrl($viewData['public_token'])); ?></span></div>
                            </div>
                            <?php if (!empty($viewData['notes'])): ?>
                                <div class="mt-4">
                                    <small class="d-block mb-2 text-uppercase text-muted fw-semibold">Internal Notes</small>
                                    <div class="invoice-muted"><?php echo nl2br(htmlspecialchars($viewData['notes'])); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="invoice-card-soft">
                            <div class="invoice-total-stack">
                                <div class="invoice-total-row"><span>Subtotal</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $viewData['subtotal'])); ?></strong></div>
                                <div class="invoice-total-row"><span>Tax</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $viewData['tax'])); ?></strong></div>
                                <div class="invoice-total-row"><span>Total</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $viewData['total'])); ?></strong></div>
                                <div class="invoice-total-row"><span>Amount Paid</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $viewData['amount_paid'])); ?></strong></div>
                                <div class="invoice-total-row"><span>Balance</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $viewData['total'] - (float) $viewData['amount_paid'])); ?></strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-1">
                        <div class="col-lg-8">
                            <div class="invoice-card-soft">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="h6 fw-bold mb-0">Line Items</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table invoice-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Line Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($viewItems as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                                    <td><?php echo number_format((float) $item['quantity'], 2); ?></td>
                                                    <td><?php echo htmlspecialchars($functions->formatCurrency((float) $item['unit_price'])); ?></td>
                                                    <td><?php echo htmlspecialchars($functions->formatCurrency((float) ($item['line_total'] ?? ((float) $item['quantity'] * (float) $item['unit_price'])))); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="invoice-card-soft">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="h6 fw-bold mb-0">Send Invoice</h3>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#sendInvoiceModal" data-invoice-id="<?php echo (int) $viewData['id']; ?>" data-recipient-email="<?php echo htmlspecialchars($viewData['recipient_email'] ?: ($viewData['client_email'] ?? '')); ?>" data-subject="<?php echo htmlspecialchars($viewData['email_subject'] ?: invoiceDefaultEmailSubject($viewData, $companyName)); ?>" data-message="<?php echo htmlspecialchars($viewData['email_message'] ?: invoiceDefaultEmailMessage($viewData, $companyName)); ?>">Send Now</button>
                                </div>
                                <div class="invoice-muted mb-3">Recipients receive a polished invoice email plus a secure invoice page link. Replies go back to your configured company mailbox.</div>
                                <div class="invoice-kv">
                                    <div><small>Recipient Email</small><span><?php echo htmlspecialchars($viewData['recipient_email'] ?: ($viewData['client_email'] ?? 'Not set')); ?></span></div>
                                    <div><small>Email Subject</small><span><?php echo htmlspecialchars($viewData['email_subject'] ?: invoiceDefaultEmailSubject($viewData, $companyName)); ?></span></div>
                                </div>
                                <div class="mt-3">
                                    <small class="d-block mb-2 text-uppercase text-muted fw-semibold">Client Link</small>
                                    <a href="<?php echo htmlspecialchars(invoicePublicUrl($viewData['public_token'])); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars(invoicePublicUrl($viewData['public_token'])); ?></a>
                                </div>
                            </div>

                            <div class="invoice-card-soft mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="h6 fw-bold mb-0">Record Payment</h3>
                                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="<?php echo (int) $viewData['id']; ?>" data-invoice-number="<?php echo htmlspecialchars($viewData['invoice_number']); ?>" data-balance="<?php echo (float) $viewData['total'] - (float) $viewData['amount_paid']; ?>">Add Payment</button>
                                </div>
                                <?php if ($viewPayments): ?>
                                    <div class="d-grid gap-3">
                                        <?php foreach ($viewPayments as $payment): ?>
                                            <div class="border rounded-4 p-3">
                                                <div class="fw-bold"><?php echo htmlspecialchars($functions->formatCurrency((float) $payment['amount'])); ?></div>
                                                <div class="invoice-muted small"><?php echo htmlspecialchars($functions->formatDate($payment['payment_date'], 'M j, Y')); ?> · <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_method']))); ?></div>
                                                <?php if (!empty($payment['reference_number'])): ?><div class="small mt-1"><?php echo htmlspecialchars($payment['reference_number']); ?></div><?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="invoice-muted">No payments recorded yet.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($action === 'new' || $editData): ?>
            <?php
            $formInvoice = $editData ?: [
                'invoice_number' => $newInvoiceNumber,
                'project_id' => '',
                'client_id' => '',
                'recipient_name' => '',
                'recipient_email' => '',
                'invoice_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+14 days')),
                'tax' => '0.00',
                'status' => 'draft',
                'notes' => '',
                'email_subject' => '',
                'email_message' => '',
            ];
            $formItems = $editItems ?: [[
                'description' => '',
                'quantity' => 1,
                'unit_price' => '',
            ]];
            ?>
            <section class="invoice-panel">
                <div class="invoice-panel-head">
                    <div>
                        <h2><?php echo $editData ? 'Edit Invoice' : 'Create New Invoice'; ?></h2>
                        <div class="invoice-muted">Build the invoice, save it, then email it from the detail view.</div>
                    </div>
                    <a href="invoices.php" class="btn btn-light btn-sm">Back to list</a>
                </div>
                <div class="invoice-panel-body">
                    <form method="POST" id="invoiceEditorForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($auth->generateCSRF()); ?>">
                        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
                        <?php if ($editData): ?><input type="hidden" name="id" value="<?php echo (int) $editData['id']; ?>"><?php endif; ?>

                        <div class="invoice-form-grid">
                            <div class="invoice-col-3">
                                <label class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" name="invoice_number" value="<?php echo htmlspecialchars($formInvoice['invoice_number']); ?>" readonly>
                            </div>
                            <div class="invoice-col-3">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" name="invoice_date" value="<?php echo htmlspecialchars($formInvoice['invoice_date']); ?>" required>
                            </div>
                            <div class="invoice-col-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date" value="<?php echo htmlspecialchars($formInvoice['due_date']); ?>" required>
                            </div>
                            <div class="invoice-col-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <?php foreach (['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'] as $status): ?>
                                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($formInvoice['status'] ?? 'draft') === $status ? 'selected' : ''; ?>><?php echo htmlspecialchars(ucfirst($status)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="invoice-col-6">
                                <label class="form-label">Client</label>
                                <select class="form-select" name="client_id" id="invoiceClientSelect" required>
                                    <option value="">Select a client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo (int) $client['id']; ?>"
                                            data-recipient-name="<?php echo htmlspecialchars(invoiceClientDisplayName($client)); ?>"
                                            data-recipient-email="<?php echo htmlspecialchars($client['email'] ?? ''); ?>"
                                            <?php echo (string) ($formInvoice['client_id'] ?? '') === (string) $client['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(invoiceClientDisplayName($client)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="invoice-col-6">
                                <label class="form-label">Project</label>
                                <select class="form-select" name="project_id">
                                    <option value="">Not linked to a project</option>
                                    <?php foreach ($projects as $project): ?>
                                        <option value="<?php echo (int) $project['id']; ?>" <?php echo (string) ($formInvoice['project_id'] ?? '') === (string) $project['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($project['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="invoice-col-6">
                                <label class="form-label">Recipient Name</label>
                                <input type="text" class="form-control" name="recipient_name" id="invoiceRecipientName" value="<?php echo htmlspecialchars($formInvoice['recipient_name'] ?? ''); ?>" placeholder="Client or organization name">
                            </div>
                            <div class="invoice-col-6">
                                <label class="form-label">Recipient Email</label>
                                <input type="email" class="form-control" name="recipient_email" id="invoiceRecipientEmail" value="<?php echo htmlspecialchars($formInvoice['recipient_email'] ?? ''); ?>" placeholder="billing@example.com">
                            </div>

                            <div class="invoice-col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Line Items</label>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addInvoiceItemBtn"><i class="fas fa-plus me-2"></i>Add item</button>
                                </div>
                                <div id="invoiceItemsContainer">
                                    <?php foreach ($formItems as $item): ?>
                                        <div class="invoice-item-row">
                                            <input type="text" class="form-control" name="item_description[]" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>" placeholder="Item or service description">
                                            <input type="number" step="0.01" min="0" class="form-control item-quantity" name="item_quantity[]" value="<?php echo htmlspecialchars((string) ($item['quantity'] ?? 1)); ?>" placeholder="Qty">
                                            <input type="number" step="0.01" min="0" class="form-control item-price" name="item_unit_price[]" value="<?php echo htmlspecialchars((string) ($item['unit_price'] ?? '')); ?>" placeholder="Unit price">
                                            <button type="button" class="btn btn-outline-danger remove-item-btn">Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="invoice-col-3">
                                <label class="form-label">Tax</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="invoiceTaxInput" name="tax" value="<?php echo htmlspecialchars((string) ($formInvoice['tax'] ?? '0.00')); ?>">
                            </div>
                            <div class="invoice-col-3">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control" id="invoiceSubtotalDisplay" value="<?php echo htmlspecialchars($functions->formatCurrency((float) ($editData['subtotal'] ?? 0))); ?>" readonly>
                            </div>
                            <div class="invoice-col-3">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control" id="invoiceTotalDisplay" value="<?php echo htmlspecialchars($functions->formatCurrency((float) ($editData['total'] ?? 0))); ?>" readonly>
                            </div>

                            <div class="invoice-col-12">
                                <label class="form-label">Internal Notes</label>
                                <textarea class="form-control" name="notes" rows="4" placeholder="Optional internal notes or payment instructions for your team"><?php echo htmlspecialchars($formInvoice['notes'] ?? ''); ?></textarea>
                            </div>

                            <div class="invoice-col-6">
                                <label class="form-label">Default Email Subject</label>
                                <input type="text" class="form-control" name="email_subject" value="<?php echo htmlspecialchars($formInvoice['email_subject'] ?? ''); ?>" placeholder="Invoice subject used when sending">
                            </div>
                            <div class="invoice-col-6">
                                <label class="form-label">Default Email Message</label>
                                <textarea class="form-control" name="email_message" rows="4" placeholder="Optional message to prefill when sending"><?php echo htmlspecialchars($formInvoice['email_message'] ?? ''); ?></textarea>
                            </div>

                            <div class="invoice-col-12 d-flex justify-content-end gap-2">
                                <a href="invoices.php" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-danger"><?php echo $editData ? 'Update Invoice' : 'Save Invoice'; ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <section class="invoice-panel">
            <div class="invoice-panel-head">
                <div>
                    <h2>All Invoices</h2>
                    <div class="invoice-muted">Monitor billing status, delivery, and client access in one place.</div>
                </div>
                <a href="invoices.php?action=new" class="btn btn-danger btn-sm"><i class="fas fa-plus me-2"></i>New Invoice</a>
            </div>
            <div class="invoice-panel-body">
                <?php if (empty($invoices)): ?>
                    <div class="invoice-empty">No invoices yet. Create your first invoice to start sending polished billing emails.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" data-table id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Recipient</th>
                                    <th>Total</th>
                                    <th>Balance</th>
                                    <th>Due</th>
                                    <th>Delivery</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['invoice_number']); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($row['project_name'] ?? 'No project linked'); ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($row['recipient_name'] ?: ($row['client_name'] ?? '-')); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($row['recipient_email'] ?: ($row['client_email'] ?? 'No email')); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($functions->formatCurrency((float) $row['total'])); ?></td>
                                        <td><?php echo htmlspecialchars($functions->formatCurrency((float) $row['total'] - (float) $row['amount_paid'])); ?></td>
                                        <td><?php echo htmlspecialchars($functions->formatDate($row['due_date'], 'M j, Y')); ?></td>
                                        <td>
                                            <?php if (!empty($row['last_emailed_at'])): ?>
                                                <div class="small fw-semibold text-success">Emailed</div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($functions->formatDate($row['last_emailed_at'], 'M j, Y')); ?></div>
                                                <div class="small text-muted"><?php echo !empty($row['viewed_at']) ? 'Viewed by client' : 'Awaiting client open'; ?></div>
                                            <?php else: ?>
                                                <div class="small fw-semibold text-muted">Not sent</div>
                                                <div class="small text-muted">Draft delivery state</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="invoice-status-pill <?php echo invoiceBadgeClass($row['status']); ?>"><?php echo htmlspecialchars(ucfirst($row['status'])); ?></span></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="invoices.php?action=view&id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fas fa-eye"></i></a>
                                                <a href="invoices.php?action=edit&id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-pen"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Send Email"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sendInvoiceModal"
                                                    data-invoice-id="<?php echo (int) $row['id']; ?>"
                                                    data-recipient-email="<?php echo htmlspecialchars($row['recipient_email'] ?: ($row['client_email'] ?? '')); ?>"
                                                    data-subject="<?php echo htmlspecialchars($row['email_subject'] ?: invoiceDefaultEmailSubject($row, $companyName)); ?>"
                                                    data-message="<?php echo htmlspecialchars($row['email_message'] ?: invoiceDefaultEmailMessage($row, $companyName)); ?>">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" title="Record Payment" data-bs-toggle="modal" data-bs-target="#paymentModal" data-invoice-id="<?php echo (int) $row['id']; ?>" data-invoice-number="<?php echo htmlspecialchars($row['invoice_number']); ?>" data-balance="<?php echo (float) $row['total'] - (float) $row['amount_paid']; ?>"><i class="fas fa-dollar-sign"></i></button>
                                                <a href="invoices.php?action=delete&id=<?php echo (int) $row['id']; ?>" class="btn btn-sm btn-outline-dark" title="Delete" onclick="return confirmAction(this, 'Delete this invoice?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="sendInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Invoice Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($auth->generateCSRF()); ?>">
                <input type="hidden" name="action" value="send_email">
                <input type="hidden" name="invoice_id" id="sendInvoiceId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" name="recipient_email" id="sendInvoiceRecipient" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reply-To Mailbox</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($companyEmail); ?>" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="email_subject" id="sendInvoiceSubject" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="7" name="email_message" id="sendInvoiceMessage" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Send Invoice</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($auth->generateCSRF()); ?>">
                <input type="hidden" name="action" value="add_payment">
                <input type="hidden" name="invoice_id" id="paymentInvoiceId">
                <div class="mb-3">
                    <label class="form-label">Invoice</label>
                    <input type="text" id="paymentInvoiceNumber" class="form-control" disabled>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" value="<?php echo htmlspecialchars(date('Y-m-d')); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="amount" id="paymentAmount" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="check">Check</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reference Number</label>
                        <input type="text" class="form-control" name="reference_number">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="4" name="notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var currency = function (value) {
        var amount = parseFloat(value || 0);
        return 'NGN ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    var itemsContainer = document.getElementById('invoiceItemsContainer');
    var taxInput = document.getElementById('invoiceTaxInput');
    var subtotalDisplay = document.getElementById('invoiceSubtotalDisplay');
    var totalDisplay = document.getElementById('invoiceTotalDisplay');

    function recalcInvoiceTotals() {
        if (!itemsContainer || !subtotalDisplay || !totalDisplay) return;
        var subtotal = 0;
        itemsContainer.querySelectorAll('.invoice-item-row').forEach(function (row) {
            var qty = parseFloat(row.querySelector('.item-quantity')?.value || 0);
            var price = parseFloat(row.querySelector('.item-price')?.value || 0);
            subtotal += qty * price;
        });
        var tax = parseFloat(taxInput?.value || 0);
        subtotalDisplay.value = currency(subtotal);
        totalDisplay.value = currency(subtotal + tax);
    }

    if (itemsContainer) {
        itemsContainer.addEventListener('input', recalcInvoiceTotals);
        itemsContainer.addEventListener('click', function (event) {
            if (!event.target.classList.contains('remove-item-btn')) return;
            if (itemsContainer.querySelectorAll('.invoice-item-row').length === 1) {
                event.target.closest('.invoice-item-row').querySelectorAll('input').forEach(function (input, index) {
                    input.value = index === 1 ? '1' : '';
                });
            } else {
                event.target.closest('.invoice-item-row').remove();
            }
            recalcInvoiceTotals();
        });
    }
    if (taxInput) taxInput.addEventListener('input', recalcInvoiceTotals);

    var addItemBtn = document.getElementById('addInvoiceItemBtn');
    if (addItemBtn && itemsContainer) {
        addItemBtn.addEventListener('click', function () {
            var row = document.createElement('div');
            row.className = 'invoice-item-row';
            row.innerHTML = '<input type="text" class="form-control" name="item_description[]" placeholder="Item or service description">'
                + '<input type="number" step="0.01" min="0" class="form-control item-quantity" name="item_quantity[]" value="1" placeholder="Qty">'
                + '<input type="number" step="0.01" min="0" class="form-control item-price" name="item_unit_price[]" placeholder="Unit price">'
                + '<button type="button" class="btn btn-outline-danger remove-item-btn">Remove</button>';
            itemsContainer.appendChild(row);
        });
    }

    var clientSelect = document.getElementById('invoiceClientSelect');
    var recipientName = document.getElementById('invoiceRecipientName');
    var recipientEmail = document.getElementById('invoiceRecipientEmail');
    if (clientSelect && recipientName && recipientEmail) {
        clientSelect.addEventListener('change', function () {
            var selected = clientSelect.options[clientSelect.selectedIndex];
            if (!recipientName.value.trim()) {
                recipientName.value = selected.getAttribute('data-recipient-name') || '';
            }
            if (!recipientEmail.value.trim()) {
                recipientEmail.value = selected.getAttribute('data-recipient-email') || '';
            }
        });
    }

    recalcInvoiceTotals();

    var sendModal = document.getElementById('sendInvoiceModal');
    if (sendModal) {
        sendModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('sendInvoiceId').value = button.getAttribute('data-invoice-id') || '';
            document.getElementById('sendInvoiceRecipient').value = button.getAttribute('data-recipient-email') || '';
            document.getElementById('sendInvoiceSubject').value = button.getAttribute('data-subject') || '';
            document.getElementById('sendInvoiceMessage').value = button.getAttribute('data-message') || '';
        });
    }

    var paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('paymentInvoiceId').value = button.getAttribute('data-invoice-id') || '';
            document.getElementById('paymentInvoiceNumber').value = button.getAttribute('data-invoice-number') || '';
            document.getElementById('paymentAmount').value = button.getAttribute('data-balance') || '';
        });
    }
});
</script>

<?php require 'inc/admin_footer.php'; ?>
