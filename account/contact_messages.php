<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Mailer.php';
require_once '../classes/Functions.php';

$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$functions = Functions::getInstance();
$currentUser = $auth->getUserData();

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

ensureContactMessageTables($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !$auth->verifyCSRF($_POST['csrf_token'])) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $submissionId = (int)($_POST['submission_id'] ?? 0);

        if ($action === 'mark_read' && $submissionId) {
            $db->query("UPDATE contact_submissions SET status = 'read' WHERE id = :id AND status = 'unread'", ['id' => $submissionId]);
            $_SESSION['toast_success'] = 'Message marked as read.';
        } elseif ($action === 'mark_unread' && $submissionId) {
            $db->query("UPDATE contact_submissions SET status = 'unread' WHERE id = :id", ['id' => $submissionId]);
            $_SESSION['toast_success'] = 'Message marked as unread.';
        } elseif ($action === 'archive' && $submissionId) {
            $db->query("UPDATE contact_submissions SET status = 'archived' WHERE id = :id", ['id' => $submissionId]);
            $_SESSION['toast_success'] = 'Message archived.';
        } elseif ($action === 'delete' && $submissionId) {
            $db->query("DELETE FROM contact_submissions WHERE id = :id", ['id' => $submissionId]);
            $_SESSION['toast_success'] = 'Message deleted.';
        } elseif ($action === 'reply' && $submissionId && !empty($_POST['reply_message'])) {
            $replyMessage = trim($_POST['reply_message']);
            $submission = $db->query("SELECT * FROM contact_submissions WHERE id = :id", ['id' => $submissionId])->fetch();

            if ($submission) {
                $db->query(
                    "INSERT INTO contact_replies (submission_id, admin_user_id, reply_message, created_at) VALUES (:sid, :uid, :msg, NOW())",
                    ['sid' => $submissionId, 'uid' => $currentUser['id'], 'msg' => $replyMessage]
                );

                $mailer = new Mailer();
                $emailSubject = 'Re: ' . $submission['subject'];
                $adminName = htmlspecialchars(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? 'Team'));
                $emailBody = '<h2 style="margin:0 0 16px;color:#0f172a;">Reply from TPV Construction and Services LTD</h2>' .
                    '<p style="margin:0 0 18px;color:#334155;">Dear <strong>' . htmlspecialchars($submission['name']) . '</strong>,</p>' .
                    '<p style="margin:0 0 18px;color:#334155;">Thank you for reaching out. Here is our response from <strong>' . $adminName . '</strong>:</p>' .
                    '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px;margin:0 0 18px;">' .
                    '<p style="white-space:pre-line;margin:0;color:#334155;line-height:1.6;">' . nl2br(htmlspecialchars($replyMessage)) . '</p>' .
                    '</div>' .
                    '<hr style="border:none;border-top:1px solid #e2e8f0;margin:18px 0;">' .
                    '<p style="margin:0 0 8px;font-size:13px;color:#64748b;"><strong>Your original message:</strong></p>' .
                    '<blockquote style="margin:0;padding:12px 16px;background:#f1f5f9;border-left:3px solid #E5363D;border-radius:6px;font-size:13px;color:#475569;white-space:pre-line;">' .
                    htmlspecialchars($submission['message']) . '</blockquote>' .
                    '<p style="margin:18px 0 0;font-size:13px;color:#64748b;">Best regards,<br><strong>TPV Construction and Services LTD</strong></p>';

                $sent = $mailer->send($submission['email'], $emailSubject, $emailBody);
                if ($sent) {
                    $db->query(
                        "INSERT INTO communications (uuid, direction, type, subject, content, communication_date, attachments, created_by, created_at, updated_at)
                         VALUES (:uuid, 'outbound', 'email', :subject, :content, NOW(), :attachments, :created_by, NOW(), NOW())",
                        [
                            'uuid' => $functions->generateUUID(),
                            'subject' => $emailSubject,
                            'content' => "Reply to contact message\n\nTo: {$submission['name']} <{$submission['email']}>\nSubject: {$emailSubject}\n\n{$replyMessage}",
                            'attachments' => json_encode(['contact_submission_id' => $submissionId]),
                            'created_by' => $currentUser['id'] ?? null
                        ]
                    );
                    $db->query("UPDATE contact_submissions SET status = 'replied' WHERE id = :id", ['id' => $submissionId]);
                    $_SESSION['toast_success'] = 'Reply sent to ' . htmlspecialchars($submission['email']) . ' successfully.';
                } else {
                    $_SESSION['toast_error'] = 'Reply was saved, but email delivery failed. Please check Resend/API settings.';
                }
            } else {
                $_SESSION['toast_error'] = 'Submission not found.';
            }
        }
    }
    header('Location: contact_messages.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

$filters = [];
$where = [];
$params = [];
$focusMessageId = isset($_GET['focus']) ? (int) $_GET['focus'] : 0;

if (isset($_GET['status']) && in_array($_GET['status'], ['unread', 'read', 'replied', 'archived'])) {
    $filters['status'] = $_GET['status'];
    $where[] = 'cs.status = :status';
    $params['status'] = $_GET['status'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
    $where[] = '(cs.name LIKE :search OR cs.email LIKE :search2 OR cs.subject LIKE :search3 OR cs.message LIKE :search4)';
    $params['search'] = '%' . $_GET['search'] . '%';
    $params['search2'] = '%' . $_GET['search'] . '%';
    $params['search3'] = '%' . $_GET['search'] . '%';
    $params['search4'] = '%' . $_GET['search'] . '%';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$messages = $db->query(
    "SELECT cs.*, (SELECT COUNT(*) FROM contact_replies WHERE submission_id = cs.id) AS reply_count
     FROM contact_submissions cs
     $whereClause
     ORDER BY cs.created_at DESC",
    $params
)->fetchAll();

$totalCount = $db->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn();
$unreadCount = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'unread'")->fetchColumn();
$readCount = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'read'")->fetchColumn();
$repliedCount = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'replied'")->fetchColumn();
$archivedCount = $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'archived'")->fetchColumn();

$replies = [];
if (!empty($messages)) {
    $ids = array_column($messages, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $repliesData = $db->query(
        "SELECT cr.*, u.first_name, u.last_name
         FROM contact_replies cr
         LEFT JOIN users u ON cr.admin_user_id = u.id
         WHERE cr.submission_id IN ($placeholders)
         ORDER BY cr.created_at ASC",
        $ids
    )->fetchAll();
    foreach ($repliesData as $r) {
        $replies[$r['submission_id']][] = $r;
    }
}

$csrfToken = $auth->csrfFieldValue();

$pageActive = 'contact_messages';
$pageTitle = 'TPV Construction and Services LTD · Contact Messages';
require 'inc/admin_header.php';
?>
<div data-pages="parallax">
    <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Contact Messages</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-15 p-r-15 p-t-0 p-b-25">
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total</h6>
                    <h4 class="mb-0 fw-bold"><?php echo number_format($totalCount); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fas fa-circle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Unread</h6>
                    <h4 class="mb-0 fw-bold"><?php echo number_format($unreadCount); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-envelope-open"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Read</h6>
                    <h4 class="mb-0 fw-bold"><?php echo number_format($readCount); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-reply-all"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Replied</h6>
                    <h4 class="mb-0 fw-bold"><?php echo number_format($repliedCount); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary me-3">
                    <i class="fas fa-archive"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Archived</h6>
                    <h4 class="mb-0 fw-bold"><?php echo number_format($archivedCount); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white py-3">
            <div class="card-title mb-2 mb-sm-0">
                <i class="fas fa-inbox me-2"></i> Contact Messages
            </div>
        </div>
        <div class="card-body">
            <div class="row filter-row m-b-20">
                <div class="col-md-4 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill" id="searchBox" placeholder="Search by name, email, subject...">
                        <span class="input-group-append">
                            <button class="btn btn-outline-secondary rounded-pill" type="button"><i class="fas fa-search"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-md-8 text-md-end">
                    <form method="get" class="d-inline-flex gap-2 flex-wrap justify-content-end">
                        <select name="status" class="form-select form-select-sm" style="width:140px;">
                            <option value="">All status</option>
                            <option value="unread" <?php echo ($filters['status'] ?? '') === 'unread' ? 'selected' : ''; ?>>Unread</option>
                            <option value="read" <?php echo ($filters['status'] ?? '') === 'read' ? 'selected' : ''; ?>>Read</option>
                            <option value="replied" <?php echo ($filters['status'] ?? '') === 'replied' ? 'selected' : ''; ?>>Replied</option>
                            <option value="archived" <?php echo ($filters['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-filter me-1"></i>Filter</button>
                        <a href="contact_messages.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="fas fa-undo me-1"></i>Reset</a>
                    </form>
                </div>
            </div>

            <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No messages found.</p>
            </div>
            <?php else: ?>
            <?php foreach ($messages as $msg): ?>
            <div id="message-<?php echo (int) $msg['id']; ?>" class="message-card mb-3 <?php echo $msg['status'] === 'unread' ? 'unread' : ''; ?> <?php echo $focusMessageId === (int) $msg['id'] ? 'focused' : ''; ?>">
                <div class="message-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center w-100">
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($msg['status'] === 'unread'): ?>
                            <span class="unread-dot"></span>
                            <?php endif; ?>
                            <div class="sender-avatar">
                                <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <strong class="sender-name"><?php echo htmlspecialchars($msg['name']); ?></strong>
                                <span class="text-muted small">&lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;</span>
                                <?php if ($msg['phone']): ?>
                                <span class="text-muted small ms-2"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($msg['phone']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted"><?php echo date('M j, Y g:ia', strtotime($msg['created_at'])); ?></span>
                            <span class="status-badge status-<?php echo $msg['status']; ?>"><?php echo ucfirst($msg['status']); ?></span>
                        </div>
                    </div>
                </div>
                <div class="message-body">
                    <h6 class="fw-semibold mb-2 subject-line"><?php echo htmlspecialchars($msg['subject']); ?></h6>
                    <p class="mb-0 message-content"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    <textarea class="msg-store-<?php echo $msg['id']; ?>" style="display:none;"><?php echo htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>

                    <?php if (!empty($replies[$msg['id']])): ?>
                    <hr class="my-3">
                    <div class="conversation-thread">
                        <?php foreach ($replies[$msg['id']] as $reply): ?>
                        <div class="reply-item">
                            <div class="reply-avatar">
                                <i class="fas fa-reply-all"></i>
                            </div>
                            <div class="reply-content">
                                <div class="reply-meta">
                                    <strong><?php echo htmlspecialchars(($reply['first_name'] ?? '') . ' ' . ($reply['last_name'] ?? 'Admin')); ?></strong>
                                    <span class="text-muted small"><?php echo date('M j, Y g:ia', strtotime($reply['created_at'])); ?></span>
                                </div>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['reply_message'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="message-footer">
                    <?php if ($msg['status'] === 'unread'): ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="action" value="mark_read">
                        <input type="hidden" name="submission_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" class="btn btn-sm action-btn" title="Mark as read"><i class="fas fa-envelope-open"></i> Mark Read</button>
                    </form>
                    <?php elseif ($msg['status'] === 'read'): ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="action" value="mark_unread">
                        <input type="hidden" name="submission_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" class="btn btn-sm action-btn" title="Mark as unread"><i class="fas fa-circle"></i> Mark Unread</button>
                    </form>
                    <?php endif; ?>
                    <button class="btn btn-sm action-btn reply-btn text-success" data-id="<?php echo $msg['id']; ?>" data-name="<?php echo htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8'); ?>" data-email="<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>" data-subject="<?php echo htmlspecialchars($msg['subject'], ENT_QUOTES, 'UTF-8'); ?>" title="Reply"><i class="fas fa-reply"></i> Reply</button>
                    <?php if ($msg['status'] !== 'archived'): ?>
                    <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="action" value="archive">
                        <input type="hidden" name="submission_id" value="<?php echo $msg['id']; ?>">
                        <button type="submit" class="btn btn-sm action-btn" title="Archive"><i class="fas fa-archive"></i> Archive</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" class="d-inline" onsubmit="return confirmAction(this, 'Delete this message permanently?');">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="submission_id" value="<?php echo $msg['id']; ?>">
                        <button type="button" class="btn btn-sm action-btn text-danger" title="Delete" onclick="return confirmAction(this, 'Delete this message permanently?')"><i class="fas fa-trash-alt"></i> Delete</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="submission_id" id="replySubmissionId" value="">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-reply text-success me-2"></i>Reply to <span id="replyName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="original-message-card">
                                <h6 class="fw-semibold mb-2"><i class="fas fa-quote-left me-1 text-muted"></i>Original Message</h6>
                                <div class="original-sender mb-2">
                                    <strong id="replyFrom"></strong>
                                    <br><small class="text-muted" id="replyEmail"></small>
                                </div>
                                <div class="original-subject mb-2">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" id="replySubject"></span>
                                </div>
                                <div class="original-body">
                                    <p id="replyMessage" style="white-space:pre-line;font-size:13px;color:#475569;"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="form-label fw-semibold">Your Reply <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reply_message" rows="10" placeholder="Type your reply here..." required></textarea>
                            </div>
                            <p class="small text-muted mt-2 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                An email will be sent to <strong id="replyEmailInfo"></strong> with your reply.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-paper-plane me-1"></i>Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.message-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.message-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.message-card.unread {
    border-left: 4px solid #dc2626;
    background: #fffcfc;
}
.message-card.focused {
    border-color: rgba(212,161,62,0.55);
    box-shadow: 0 0 0 3px rgba(212,161,62,0.15), 0 16px 34px -24px rgba(212,161,62,0.5);
    animation: focusedMessagePulse 1.8s ease-out 1;
}
.message-header {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
}
.unread-dot {
    width: 10px; height: 10px;
    background: #dc2626;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.sender-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #E5363D, #c62828);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
}
.message-body {
    padding: 16px 18px;
}
.subject-line {
    color: #1e293b;
}
.message-content {
    color: #475569;
    line-height: 1.7;
    font-size: 14px;
}
.conversation-thread {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.reply-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}
.reply-avatar {
    width: 32px; height: 32px;
    background: rgba(22,163,74,0.1);
    color: #16a34a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
}
.reply-content {
    flex: 1;
}
.reply-meta {
    margin-bottom: 4px;
    font-size: 13px;
}
.reply-content p {
    font-size: 13px;
    color: #475569;
    line-height: 1.6;
}
.message-footer {
    padding: 10px 18px;
    border-top: 1px solid #f1f5f9;
    background: #fafbfc;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.action-btn {
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    border-radius: 8px;
    padding: 4px 12px;
    font-size: 13px;
    transition: all 0.15s;
}
.action-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.status-unread { background: rgba(220,38,38,0.1); color: #dc2626; }
.status-read { background: rgba(37,99,235,0.1); color: #2563eb; }
.status-replied { background: rgba(22,163,74,0.1); color: #16a34a; }
.status-archived { background: rgba(100,116,139,0.1); color: #64748b; }

.original-message-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
    height: 100%;
}
.original-body {
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
    background: #fff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}
@keyframes focusedMessagePulse {
    0% { box-shadow: 0 0 0 0 rgba(212,161,62,0.32); }
    100% { box-shadow: 0 0 0 18px rgba(212,161,62,0); }
}
</style>

<?php
$focusMessageIdJs = (int) $focusMessageId;
$extraScripts = <<<HEREDOC
<script>
$(function() {
    $(document).on('click', '.reply-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var subject = $(this).data('subject');
        var message = $('.msg-store-' + id).text();

        $('#replySubmissionId').val(id);
        $('#replyName').text(name);
        $('#replyFrom').text(name);
        $('#replyEmail').text('<' + email + '>');
        $('#replySubject').text(subject);
        $('#replyMessage').text(message);
        $('#replyEmailInfo').text(email);
        $('#replyModal textarea[name="reply_message"]').val('');
        $('#replyModal').modal('show');
    });

    var focusMessageId = {$focusMessageIdJs};
    if (focusMessageId) {
        var focusedMessage = document.getElementById('message-' + focusMessageId);
        if (focusedMessage) {
            setTimeout(function() {
                focusedMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 150);
        }
    }
});
</script>
HEREDOC;

require 'inc/admin_footer.php'; ?>
