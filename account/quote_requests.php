<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';
require_once '../classes/Mailer.php';
require_once '../classes/Functions.php';
require_once '../classes/QuoteRequest.php';

$auth = new Auth();
$auth->requireAuth();

$db = Database::getInstance();
$functions = Functions::getInstance();
$currentUser = $auth->getUserData();
$quoteRequests = new QuoteRequest();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !$auth->verifyCSRF($_POST['csrf_token'])) {
        $_SESSION['toast_error'] = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $requestId = (int) ($_POST['request_id'] ?? 0);

        if ($action === 'mark_read' && $requestId) {
            $db->query("UPDATE quote_requests SET status = 'read' WHERE id = :id AND status = 'unread'", ['id' => $requestId]);
            $_SESSION['toast_success'] = 'Quote request marked as read.';
        } elseif ($action === 'mark_unread' && $requestId) {
            $db->query("UPDATE quote_requests SET status = 'unread' WHERE id = :id", ['id' => $requestId]);
            $_SESSION['toast_success'] = 'Quote request marked as unread.';
        } elseif ($action === 'archive' && $requestId) {
            $db->query("UPDATE quote_requests SET status = 'archived' WHERE id = :id", ['id' => $requestId]);
            $_SESSION['toast_success'] = 'Quote request archived.';
        } elseif ($action === 'delete' && $requestId) {
            $db->query("DELETE FROM quote_requests WHERE id = :id", ['id' => $requestId]);
            $_SESSION['toast_success'] = 'Quote request deleted.';
        } elseif ($action === 'reply' && $requestId && !empty($_POST['reply_message'])) {
            $replyMessage = trim((string) $_POST['reply_message']);
            $request = $db->query("SELECT * FROM quote_requests WHERE id = :id", ['id' => $requestId])->fetch();

            if ($request) {
                $db->query(
                    "INSERT INTO quote_request_replies (request_id, admin_user_id, reply_message, created_at)
                     VALUES (:request_id, :admin_user_id, :reply_message, NOW())",
                    [
                        'request_id' => $requestId,
                        'admin_user_id' => $currentUser['id'] ?? null,
                        'reply_message' => $replyMessage
                    ]
                );

                $mailer = new Mailer();
                $emailSubject = 'Re: Quote Request - ' . ($request['project_type'] ?: 'TPV Construction');
                $adminName = trim((string) (($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')));
                if ($adminName === '') {
                    $adminName = 'TPV Construction Team';
                }
                $emailBody = '<h2 style="margin:0 0 16px;color:#0f172a;">Reply to Your Quote Request</h2>' .
                    '<p style="margin:0 0 18px;color:#334155;">Dear <strong>' . htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) . '</strong>,</p>' .
                    '<p style="margin:0 0 18px;color:#334155;">Thank you for requesting a quote from <strong>TPV Construction and Services LTD</strong>. Here is our response from <strong>' . htmlspecialchars($adminName) . '</strong>:</p>' .
                    '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:20px;margin:0 0 18px;">' .
                    '<p style="white-space:pre-line;margin:0;color:#334155;line-height:1.6;">' . nl2br(htmlspecialchars($replyMessage)) . '</p>' .
                    '</div>' .
                    '<p style="margin:18px 0 0;font-size:13px;color:#64748b;">Project type: <strong>' . htmlspecialchars($request['project_type']) . '</strong><br>Location: <strong>' . htmlspecialchars($request['project_location']) . '</strong></p>' .
                    '<p style="margin:18px 0 0;font-size:13px;color:#64748b;">Best regards,<br><strong>TPV Construction and Services LTD</strong></p>';

                $sent = $mailer->send($request['email'], $emailSubject, $emailBody);

                if ($sent) {
                    $db->query(
                        "INSERT INTO communications (uuid, direction, type, subject, content, communication_date, attachments, created_at, updated_at)
                         VALUES (:uuid, 'outbound', 'email', :subject, :content, NOW(), :attachments, NOW(), NOW())",
                        [
                            'uuid' => $functions->generateUUID(),
                            'subject' => $emailSubject,
                            'content' => "Reply to quote request\n\nTo: {$request['first_name']} {$request['last_name']} <{$request['email']}>\n\n{$replyMessage}",
                            'attachments' => json_encode(['quote_request_id' => $requestId])
                        ]
                    );
                    $db->query("UPDATE quote_requests SET status = 'replied' WHERE id = :id", ['id' => $requestId]);
                    $_SESSION['toast_success'] = 'Reply sent to ' . htmlspecialchars($request['email']) . ' successfully.';
                } else {
                    $_SESSION['toast_error'] = 'Reply was saved, but email delivery failed. Please check SMTP settings.';
                }
            } else {
                $_SESSION['toast_error'] = 'Quote request not found.';
            }
        }
    }

    header('Location: quote_requests.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

$filters = [];
$where = [];
$params = [];
$focusRequestId = isset($_GET['focus']) ? (int) $_GET['focus'] : 0;

if (isset($_GET['status']) && in_array($_GET['status'], ['unread', 'read', 'replied', 'archived'], true)) {
    $filters['status'] = $_GET['status'];
    $where[] = 'qr.status = :status';
    $params['status'] = $_GET['status'];
}
if (isset($_GET['search']) && trim((string) $_GET['search']) !== '') {
    $filters['search'] = trim((string) $_GET['search']);
    $where[] = '(CONCAT(qr.first_name, " ", qr.last_name) LIKE :search OR qr.email LIKE :search2 OR qr.phone LIKE :search3 OR qr.project_type LIKE :search4 OR qr.project_location LIKE :search5 OR qr.description LIKE :search6)';
    $params['search'] = '%' . $filters['search'] . '%';
    $params['search2'] = '%' . $filters['search'] . '%';
    $params['search3'] = '%' . $filters['search'] . '%';
    $params['search4'] = '%' . $filters['search'] . '%';
    $params['search5'] = '%' . $filters['search'] . '%';
    $params['search6'] = '%' . $filters['search'] . '%';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$requests = $db->query(
    "SELECT qr.*
     FROM quote_requests qr
     $whereClause
     ORDER BY qr.created_at DESC",
    $params
)->fetchAll();

$totalCount = (int) $db->query("SELECT COUNT(*) FROM quote_requests")->fetchColumn();
$unreadCount = (int) $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'unread'")->fetchColumn();
$readCount = (int) $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'read'")->fetchColumn();
$repliedCount = (int) $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'replied'")->fetchColumn();
$archivedCount = (int) $db->query("SELECT COUNT(*) FROM quote_requests WHERE status = 'archived'")->fetchColumn();

$replies = [];
if (!empty($requests)) {
    $ids = array_column($requests, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $replyRows = $db->query(
        "SELECT * FROM quote_request_replies WHERE request_id IN ($placeholders) ORDER BY created_at ASC",
        $ids
    )->fetchAll();
    foreach ($replyRows as $reply) {
        $replies[$reply['request_id']][] = $reply;
    }
}

function quoteRequestDecode($value) {
    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? $decoded : [];
}

$csrfToken = $auth->csrfFieldValue();
$pageActive = 'quote_requests';
$pageTitle = 'TPV Construction and Services LTD · Quote Requests';
require 'inc/admin_header.php';
?>

<div data-pages="parallax">
    <div class="container-fluid p-l-15 p-r-15 sm-p-l-0 sm-p-r-0">
        <div class="inner">
            <ol class="breadcrumb sm-p-b-5">
                <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active">Quote Requests</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid p-l-15 p-r-15 p-t-0 p-b-25">
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon text-primary me-3"><i class="fas fa-file-signature"></i></div>
                <div><h6 class="text-muted mb-1">Total</h6><h4 class="mb-0 fw-bold"><?php echo number_format($totalCount); ?></h4></div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon text-danger me-3"><i class="fas fa-circle"></i></div>
                <div><h6 class="text-muted mb-1">Unread</h6><h4 class="mb-0 fw-bold"><?php echo number_format($unreadCount); ?></h4></div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon text-info me-3"><i class="fas fa-envelope-open-text"></i></div>
                <div><h6 class="text-muted mb-1">Read</h6><h4 class="mb-0 fw-bold"><?php echo number_format($readCount); ?></h4></div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon text-success me-3"><i class="fas fa-reply"></i></div>
                <div><h6 class="text-muted mb-1">Replied</h6><h4 class="mb-0 fw-bold"><?php echo number_format($repliedCount); ?></h4></div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon text-secondary me-3"><i class="fas fa-box-archive"></i></div>
                <div><h6 class="text-muted mb-1">Archived</h6><h4 class="mb-0 fw-bold"><?php echo number_format($archivedCount); ?></h4></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <div class="card-title mb-0"><i class="fas fa-calculator me-2"></i> Quote Requests</div>
        </div>
        <div class="card-body">
            <div class="contact-toolbar">
                <form method="get" class="contact-search-wrap">
                    <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" placeholder="Search by name, email, phone, project or location...">
                </form>
                <form method="get" class="contact-filter-form">
                    <?php if (!empty($filters['search'])): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>">
                    <?php endif; ?>
                    <select name="status" class="form-select">
                        <option value="">All status</option>
                        <option value="unread" <?php echo ($filters['status'] ?? '') === 'unread' ? 'selected' : ''; ?>>Unread</option>
                        <option value="read" <?php echo ($filters['status'] ?? '') === 'read' ? 'selected' : ''; ?>>Read</option>
                        <option value="replied" <?php echo ($filters['status'] ?? '') === 'replied' ? 'selected' : ''; ?>>Replied</option>
                        <option value="archived" <?php echo ($filters['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="quote_requests.php" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center"><i class="fas fa-undo me-1"></i>Reset</a>
                </form>
            </div>

            <?php if (empty($requests)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-signature fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No quote requests found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <?php
                    $services = quoteRequestDecode($request['services']);
                    $attachments = quoteRequestDecode($request['attachments']);
                    $requestName = trim($request['first_name'] . ' ' . $request['last_name']);
                    ?>
                    <div id="quote-request-<?php echo (int) $request['id']; ?>" class="message-card mb-3 <?php echo $request['status'] === 'unread' ? 'unread' : ''; ?> <?php echo $focusRequestId === (int) $request['id'] ? 'focused' : ''; ?>">
                        <div class="message-header">
                            <div class="d-flex flex-wrap justify-content-between align-items-center w-100">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($request['status'] === 'unread'): ?>
                                        <span class="unread-dot"></span>
                                    <?php endif; ?>
                                    <div class="sender-avatar"><?php echo strtoupper(substr($request['first_name'] ?: '?', 0, 1)); ?></div>
                                    <div>
                                        <strong class="sender-name"><?php echo htmlspecialchars($requestName ?: 'Quote lead'); ?></strong>
                                        <span class="text-muted small">&lt;<?php echo htmlspecialchars($request['email']); ?>&gt;</span>
                                        <span class="text-muted small ms-2"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($request['phone']); ?></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-muted"><?php echo date('M j, Y g:ia', strtotime($request['created_at'])); ?></span>
                                    <span class="status-badge status-<?php echo $request['status']; ?>"><?php echo ucfirst($request['status']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="message-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                <h6 class="fw-semibold mb-0 subject-line"><?php echo htmlspecialchars($request['project_type']); ?></h6>
                                <div class="text-muted small">
                                    <?php echo htmlspecialchars($request['project_location']); ?>
                                    <?php if (!empty($request['budget'])): ?>
                                        &middot; Budget: <strong><?php echo htmlspecialchars('₦' . number_format((float) $request['budget'], 2)); ?></strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Client Type</div><div class="fw-medium"><?php echo htmlspecialchars($request['client_type']); ?></div></div>
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Company</div><div class="fw-medium"><?php echo htmlspecialchars($request['company'] ?: 'Not provided'); ?></div></div>
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Project Size</div><div class="fw-medium"><?php echo htmlspecialchars($request['project_size'] ?: 'Not specified'); ?></div></div>
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Expected Start</div><div class="fw-medium"><?php echo htmlspecialchars($request['start_date'] ?: 'Not specified'); ?></div></div>
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Timeline</div><div class="fw-medium"><?php echo htmlspecialchars($request['timeline'] ?: 'Not specified'); ?></div></div>
                                <div class="col-md-6"><div class="text-muted small text-uppercase mb-1">Referral Source</div><div class="fw-medium"><?php echo htmlspecialchars($request['referral_source'] ?: 'Not specified'); ?></div></div>
                            </div>

                            <?php if (!empty($services)): ?>
                                <div class="mb-3">
                                    <div class="text-muted small text-uppercase mb-2">Requested Services</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($services as $service): ?>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($service); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <div class="text-muted small text-uppercase mb-2">Project Description</div>
                                <p class="mb-0 message-content"><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                            </div>

                            <?php if (!empty($attachments)): ?>
                                <div class="mb-3">
                                    <div class="text-muted small text-uppercase mb-2">Attachments</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($attachments as $attachment): ?>
                                            <?php if (!empty($attachment['path'])): ?>
                                                <a class="btn btn-sm btn-outline-secondary" href="../<?php echo htmlspecialchars($attachment['path']); ?>" target="_blank" rel="noopener">
                                                    <i class="fas fa-paperclip me-1"></i><?php echo htmlspecialchars($attachment['name'] ?? basename($attachment['path'])); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($replies[$request['id']])): ?>
                                <hr class="my-3">
                                <div class="conversation-thread">
                                    <?php foreach ($replies[$request['id']] as $reply): ?>
                                        <div class="reply-item">
                                            <div class="reply-avatar"><i class="fas fa-reply-all"></i></div>
                                            <div>
                                                <div class="fw-semibold mb-1">Admin <span class="text-muted small ms-1"><?php echo date('M j, Y g:ia', strtotime($reply['created_at'])); ?></span></div>
                                                <div class="text-dark"><?php echo nl2br(htmlspecialchars($reply['reply_message'])); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="message-actions">
                            <?php if ($request['status'] === 'unread'): ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-envelope-open me-1"></i>Mark Read</button>
                                </form>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                    <input type="hidden" name="action" value="mark_unread">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-circle me-1"></i>Mark Unread</button>
                                </form>
                            <?php endif; ?>

                            <button type="button" class="btn btn-outline-success" data-bs-toggle="collapse" data-bs-target="#reply-box-<?php echo (int) $request['id']; ?>">
                                <i class="fas fa-reply me-1"></i>Reply
                            </button>

                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <input type="hidden" name="action" value="archive">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-box-archive me-1"></i>Archive</button>
                            </form>

                            <form method="post" onsubmit="return confirm('Delete this quote request?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash me-1"></i>Delete</button>
                            </form>
                        </div>
                        <div class="collapse" id="reply-box-<?php echo (int) $request['id']; ?>">
                            <div class="p-3 border-top">
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                    <input type="hidden" name="action" value="reply">
                                    <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                    <label class="form-label small fw-semibold">Reply to <?php echo htmlspecialchars($request['email']); ?></label>
                                    <textarea name="reply_message" rows="4" class="form-control mb-3" placeholder="Write your response here..." required></textarea>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Send Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: #fff;
    border: 1px solid #e9eef5;
    border-radius: 18px;
    padding: 18px 20px;
    box-shadow: 0 10px 24px -22px rgba(15, 23, 42, 0.35);
}
.stat-icon {
    width: auto;
    height: auto;
    background: transparent !important;
    border-radius: 0;
    padding: 0;
    box-shadow: none !important;
    font-size: 1.75rem;
    line-height: 1;
}
.stat-card h6 {
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 4px !important;
}
.stat-card h4 {
    color: #0f172a;
    letter-spacing: -0.03em;
}
.contact-toolbar {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    padding: 6px 0 2px;
}
.contact-search-wrap {
    min-width: 0;
    width: 100%;
}
.contact-search-wrap .form-control {
    width: 100%;
    height: 48px;
    border-radius: 14px;
    border: 1px solid #d9e2ef;
    background: #ffffff;
    padding: 0 16px;
    font-size: 0.95rem;
    box-shadow: 0 10px 22px -24px rgba(15, 23, 42, 0.25);
}
.contact-search-wrap .form-control::placeholder { color: #94a3b8; }
.contact-search-wrap .form-control:focus {
    border-color: rgba(212,161,62,0.45);
    box-shadow: 0 0 0 4px rgba(212,161,62,0.12);
}
.contact-filter-form {
    display: grid;
    grid-template-columns: 160px auto auto;
    align-items: center;
    gap: 8px;
}
.contact-filter-form .form-select,
.contact-filter-form .btn {
    min-height: 48px;
    border-radius: 14px;
    font-weight: 600;
}
.contact-filter-form .form-select {
    width: 100%;
    border: 1px solid #d9e2ef;
    background: #ffffff;
    box-shadow: 0 10px 22px -24px rgba(15, 23, 42, 0.25);
    padding-left: 14px;
    padding-right: 38px;
    font-size: 0.95rem;
}
.contact-filter-form .btn {
    min-width: 0;
    padding: 0 14px;
    font-size: 0.92rem;
    white-space: nowrap;
    box-shadow: none;
}
.message-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.message-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
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
.message-body { padding: 16px 18px; }
.message-actions {
    padding: 12px 18px;
    border-top: 1px solid #f1f5f9;
    background: #fafbfc;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.unread-dot {
    width: 10px;
    height: 10px;
    background: #dc2626;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.sender-avatar {
    width: 38px;
    height: 38px;
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
.subject-line { color: #1e293b; }
.message-content {
    color: #475569;
    line-height: 1.7;
    font-size: 14px;
}
.conversation-thread {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.reply-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
}
.reply-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #dcfce7;
    color: #15803d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
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
@media (max-width: 1200px) {
    .contact-toolbar { grid-template-columns: 1fr; }
    .contact-filter-form {
        grid-template-columns: 1fr 120px 120px;
        width: 100%;
    }
}
@media (max-width: 768px) {
    .contact-toolbar { grid-template-columns: 1fr; gap: 10px; }
    .contact-filter-form {
        grid-template-columns: 1fr 1fr;
        width: 100%;
        gap: 8px;
    }
    .contact-filter-form select { grid-column: 1 / -1; }
    .contact-filter-form .btn,
    .contact-filter-form .form-select,
    .contact-search-wrap .form-control {
        width: 100%;
        min-height: 46px;
        border-radius: 12px;
        font-size: 0.88rem;
    }
    .message-header { padding: 12px 14px; }
    .message-body { padding: 14px; }
    .message-actions { padding: 12px 14px; }
    .message-actions form,
    .message-actions .btn {
        flex: 1 1 calc(50% - 4px);
    }
    .sender-avatar {
        width: 34px;
        height: 34px;
        font-size: 14px;
    }
}
@keyframes focusedMessagePulse {
    0% { box-shadow: 0 0 0 0 rgba(212,161,62,0.32); }
    100% { box-shadow: 0 0 0 18px rgba(212,161,62,0); }
}
</style>

<?php require 'inc/admin_footer.php'; ?>
