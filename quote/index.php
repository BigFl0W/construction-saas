<?php
require_once '../config/config.php';
require_once '../classes/Mailer.php';
require_once '../classes/Functions.php';

$db = Database::getInstance();
$functions = Functions::getInstance();
$quoteSuccess = isset($_GET['quote_sent']);
$quoteError = '';

function quoteClean($value) {
    return trim((string)$value);
}

function quoteHtml($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function quoteMoney($value) {
    return '₦' . number_format((float)$value, 2);
}

function saveQuoteAttachments($files) {
    $saved = [];
    if (empty($files['name']) || !is_array($files['name'])) {
        return $saved;
    }

    $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $maxSize = 10 * 1024 * 1024;
    $uploadDir = dirname(__DIR__) . '/uploads/quotes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($files['name'] as $index => $name) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if (($files['error'][$index] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($files['size'][$index] ?? 0) > $maxSize) {
            continue;
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $safeName = uniqid('quote_', true) . '_' . preg_replace('/[^a-zA-Z0-9.\-]/', '_', basename($name));
        $relativePath = 'uploads/quotes/' . $safeName;
        if (move_uploaded_file($files['tmp_name'][$index], $uploadDir . $safeName)) {
            $saved[] = [
                'name' => $name,
                'path' => $relativePath,
                'size' => (int)$files['size'][$index]
            ];
        }
    }

    return $saved;
}

function buildQuoteMessage($data, $attachments) {
    $services = !empty($data['services']) ? implode(', ', $data['services']) : 'None selected';
    $attachmentText = empty($attachments)
        ? 'No attachments'
        : implode("\n", array_map(fn($file) => $file['name'] . ' - ' . SITE_URL . $file['path'], $attachments));

    return "New quote request\n\n" .
        "Name: {$data['first_name']} {$data['last_name']}\n" .
        "Email: {$data['email']}\n" .
        "Phone: {$data['phone']}\n" .
        "Company: {$data['company']}\n" .
        "Client Type: {$data['client_type']}\n\n" .
        "Services: {$services}\n" .
        "Project Type: {$data['project_type']}\n" .
        "Project Size: {$data['project_size']}\n" .
        "Location: {$data['project_location']}\n" .
        "Expected Start Date: {$data['start_date']}\n" .
        "Budget: " . quoteMoney($data['budget']) . "\n" .
        "Timeline: {$data['timeline']}\n" .
        "Referral Source: {$data['referral_source']}\n\n" .
        "Description:\n{$data['description']}\n\n" .
        "Attachments:\n{$attachmentText}";
}

function buildQuoteEmailBody($data, $attachments) {
    $services = !empty($data['services']) ? implode(', ', array_map('quoteHtml', $data['services'])) : 'None selected';
    $attachmentsHtml = '<p style="margin:0;color:#64748b;">No attachments uploaded.</p>';
    if (!empty($attachments)) {
        $items = '';
        foreach ($attachments as $file) {
            $url = SITE_URL . $file['path'];
            $items .= '<li><a href="' . quoteHtml($url) . '">' . quoteHtml($file['name']) . '</a></li>';
        }
        $attachmentsHtml = '<ul style="margin:0;padding-left:18px;">' . $items . '</ul>';
    }

    return '<h2 style="margin:0 0 16px;color:#0f172a;">New Quote Request</h2>' .
        '<p style="margin:0 0 18px;color:#334155;">A new quote request was submitted from the TPV website.</p>' .
        '<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;color:#334155;">' .
        '<tr><td><strong>Name</strong></td><td>' . quoteHtml($data['first_name'] . ' ' . $data['last_name']) . '</td></tr>' .
        '<tr><td><strong>Email</strong></td><td><a href="mailto:' . quoteHtml($data['email']) . '">' . quoteHtml($data['email']) . '</a></td></tr>' .
        '<tr><td><strong>Phone</strong></td><td>' . quoteHtml($data['phone']) . '</td></tr>' .
        '<tr><td><strong>Company</strong></td><td>' . quoteHtml($data['company']) . '</td></tr>' .
        '<tr><td><strong>Client Type</strong></td><td>' . quoteHtml($data['client_type']) . '</td></tr>' .
        '<tr><td><strong>Services</strong></td><td>' . $services . '</td></tr>' .
        '<tr><td><strong>Project Type</strong></td><td>' . quoteHtml($data['project_type']) . '</td></tr>' .
        '<tr><td><strong>Project Size</strong></td><td>' . quoteHtml($data['project_size']) . '</td></tr>' .
        '<tr><td><strong>Location</strong></td><td>' . quoteHtml($data['project_location']) . '</td></tr>' .
        '<tr><td><strong>Start Date</strong></td><td>' . quoteHtml($data['start_date']) . '</td></tr>' .
        '<tr><td><strong>Budget</strong></td><td>' . quoteHtml(quoteMoney($data['budget'])) . '</td></tr>' .
        '<tr><td><strong>Timeline</strong></td><td>' . quoteHtml($data['timeline']) . '</td></tr>' .
        '<tr><td><strong>Referral</strong></td><td>' . quoteHtml($data['referral_source']) . '</td></tr>' .
        '</table>' .
        '<h3 style="margin:22px 0 8px;color:#0f172a;">Project Description</h3>' .
        '<p style="white-space:pre-line;margin:0 0 18px;color:#334155;line-height:1.6;">' . quoteHtml($data['description']) . '</p>' .
        '<h3 style="margin:22px 0 8px;color:#0f172a;">Attachments</h3>' . $attachmentsHtml;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quote_submit'])) {
    $quoteData = [
        'first_name' => quoteClean($_POST['first_name'] ?? ''),
        'last_name' => quoteClean($_POST['last_name'] ?? ''),
        'email' => quoteClean($_POST['email'] ?? ''),
        'phone' => quoteClean($_POST['phone'] ?? ''),
        'company' => quoteClean($_POST['company'] ?? ''),
        'client_type' => quoteClean($_POST['client_type'] ?? ''),
        'services' => array_values(array_filter(array_map('quoteClean', $_POST['services'] ?? []))),
        'project_type' => quoteClean($_POST['project_type'] ?? ''),
        'project_size' => quoteClean($_POST['project_size'] ?? ''),
        'project_location' => quoteClean($_POST['project_location'] ?? ''),
        'start_date' => quoteClean($_POST['start_date'] ?? ''),
        'budget' => quoteClean($_POST['budget'] ?? '0'),
        'timeline' => quoteClean($_POST['timeline'] ?? ''),
        'description' => quoteClean($_POST['description'] ?? ''),
        'referral_source' => quoteClean($_POST['referral_source'] ?? '')
    ];

    if ($quoteData['first_name'] === '' || $quoteData['last_name'] === '' || !filter_var($quoteData['email'], FILTER_VALIDATE_EMAIL) || $quoteData['phone'] === '' || $quoteData['client_type'] === '' || $quoteData['project_type'] === '' || $quoteData['project_location'] === '' || $quoteData['description'] === '') {
        $quoteError = 'Please complete all required fields with a valid email address.';
    } else {
        try {
            $attachments = saveQuoteAttachments($_FILES['attachments'] ?? []);
            $message = buildQuoteMessage($quoteData, $attachments);
            $subject = 'Quote Request: ' . $quoteData['project_type'] . ' - ' . $quoteData['first_name'] . ' ' . $quoteData['last_name'];
            $companyEmail = $functions->getSetting('company_email', 'info@tpvconstruction.com.ng');
            if (!$companyEmail || strpos($companyEmail, 'ironbridge') !== false) {
                $companyEmail = 'info@tpvconstruction.com.ng';
            }

            $db->query(
                "INSERT INTO communications (uuid, direction, type, subject, content, communication_date, attachments, created_at, updated_at)
                 VALUES (:uuid, 'inbound', 'email', :subject, :content, NOW(), :attachments, NOW(), NOW())",
                [
                    'uuid' => $functions->generateUUID(),
                    'subject' => $subject,
                    'content' => $message,
                    'attachments' => json_encode($attachments)
                ]
            );

            $mailer = new Mailer();
            $sent = $mailer->send($companyEmail, $subject, buildQuoteEmailBody($quoteData, $attachments), $quoteData['email']);
            if (!$sent) {
                $quoteError = 'Your quote was saved, but the email notification could not be sent. Please contact us directly if urgent.';
            } else {
                header('Location: index.php?quote_sent=1');
                exit;
            }
        } catch (Exception $e) {
            error_log('Quote request error: ' . $e->getMessage());
            $quoteError = 'We could not submit your quote request. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get a Free Quote – TPV Construction and Services LTD</title>
    <meta name="description" content="Request a free, no-obligation construction quote from TPV Construction and Services LTD. Tell us about your project and we'll respond within 24 hours.">
    <link rel='dns-prefetch' href='//fonts.googleapis.com'>

    <style id='wp-img-auto-sizes-contain-inline-css'>img:is([sizes=auto i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px; }</style>
    <style id='wp-emoji-styles-inline-css'>img.wp-smiley, img.emoji { display: inline !important; border: none !important; }</style>
    <style id='classic-theme-styles-inline-css'>/*!*/ .wp-block-button__link { color:#fff; background-color:#32373c; border-radius:9999px; }</style>
    <style id='global-styles-inline-css'>:root{--wp--preset--aspect-ratio--square:1; }</style>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Outfit', sans-serif !important; }

        /* ══════════════════════════════════════════════
           FIX: Force ElementsKit desktop nav to display.
           By default the menu-container is off-canvas (hidden).
           ElementsKit JS would normally convert it to inline at
           widths > data-responsive-breakpoint (1024px).
           We replicate that here so the nav is always visible
           on desktop without relying on the JS transformation.
           ══════════════════════════════════════════════ */
        @media (min-width: 1025px) {
            /* Show the nav container as inline/flex instead of off-canvas */
            #ekit-megamenu-header-menu.elementskit-menu-offcanvas-elements {
                position: relative !important;
                top: auto !important; left: auto !important;
                width: auto !important; height: auto !important;
                background: transparent !important;
                box-shadow: none !important;
                transform: none !important;
                transition: none !important;
                display: flex !important;
                align-items: center !important;
                visibility: visible !important;
                opacity: 1 !important;
                overflow: visible !important;
                padding: 0 !important;
                pointer-events: auto !important;
                z-index: auto !important;
            }
            /* Show the <ul> as a horizontal row */
            #ekit-megamenu-header-menu .elementskit-navbar-nav {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                list-style: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            /* Each nav item inline */
            #ekit-megamenu-header-menu .elementskit-navbar-nav > li {
                display: flex !important;
                align-items: center !important;
                position: relative !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            /* Hide the identity panel (close btn shown only in mobile) */
            .elementskit-nav-identity-panel {
                display: none !important;
            }
            /* Keep hamburger hidden at desktop */
            .elementskit-menu-hamburger {
                display: none !important;
            }
            /* Overlay hidden at desktop */
            .ekit-nav-menu--overlay {
                display: none !important;
            }
        }

        /* ── Quote page header: white background ── */
        .ekit-template-content-header .elementor-element-3c0e001,
        .ekit-template-content-header .elementor-element-159e7cf {
            background-color: #ffffff !important;
        }
        /* Ensure nav link text is dark and readable on white */
        .ekit-template-content-header .ekit-menu-nav-link,
        .ekit-template-content-header .elementskit-navbar-nav > li > a {
            color: #1e293b !important;
        }
        .ekit-template-content-header .elementskit-navbar-nav > li > a:hover,
        .ekit-template-content-header .elementskit-navbar-nav > li:hover > a {
            color: #E5363D !important;
        }

        /* ── Quote Hero ── */
        .tpv-quote-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
            padding: 90px 20px 60px;
            text-align: center;
        }
        .tpv-quote-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(229,54,61,0.2) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 50%, rgba(229,54,61,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .tpv-quote-hero::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, #E5363D, transparent);
        }
        .tpv-quote-hero-inner {
            position: relative;
            z-index: 1;
            max-width: 820px;
            margin: 0 auto;
        }
        .tpv-quote-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(229,54,61,0.15);
            color: #ff7373;
            border: 1px solid rgba(229,54,61,0.3);
            border-radius: 50px;
            padding: 6px 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 22px;
        }
        .tpv-quote-hero h1 {
            font-size: clamp(2rem, 5vw, 3.4rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            margin-bottom: 18px;
            letter-spacing: -1.5px;
        }
        .tpv-quote-hero h1 em { color: #E5363D; font-style: normal; }
        .tpv-quote-hero p {
            font-size: 1.1rem;
            color: #94a3b8;
            line-height: 1.7;
            max-width: 580px;
            margin: 0 auto 28px;
        }
        .tpv-hero-stats {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .tpv-hero-stat-num { font-size: 2rem; font-weight: 900; color: #E5363D; line-height: 1; }
        .tpv-hero-stat-lbl { font-size: 12px; color: #64748b; margin-top: 4px; letter-spacing: 0.5px; }
        .tpv-breadcrumb { margin-top: 22px; }
        .tpv-breadcrumb ol { list-style: none; display: flex; justify-content: center; gap: 8px; align-items: center; flex-wrap: wrap; padding: 0; margin: 0; }
        .tpv-breadcrumb li { color: #64748b; font-size: 13px; }
        .tpv-breadcrumb a { color: #94a3b8; text-decoration: none; }
        .tpv-breadcrumb a:hover { color: #E5363D; }

        /* ── Layout ── */
        .tpv-quote-wrap {
            max-width: 1220px;
            margin: 0 auto;
            padding: 56px 24px 80px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 36px;
            align-items: start;
        }
        @media(max-width:1060px){ .tpv-quote-wrap{ grid-template-columns:1fr; } }

        /* ── Form Card ── */
        .tpv-form-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 44px 40px;
            box-shadow: 0 20px 60px -20px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
        }
        @media(max-width:600px){ .tpv-form-card{ padding:26px 18px; } }
        .tpv-form-card h2 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; letter-spacing: -0.5px; }
        .tpv-form-card > p { font-size: 14px; color: #64748b; margin-bottom: 32px; line-height: 1.6; }

        .tpv-divider {
            font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
            color: #E5363D; margin: 28px 0 18px;
            display: flex; align-items: center; gap: 10px;
        }
        .tpv-divider::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg,#fde8e9,transparent); }

        .tpv-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        @media(max-width:580px){ .tpv-row{ grid-template-columns:1fr; } }
        .tpv-fg { margin-bottom: 20px; }
        .tpv-label {
            display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px;
        }
        .tpv-label .req { color: #E5363D; margin-left: 2px; }
        .tpv-input {
            width: 100%; background: #f8fafc; border: 2px solid #e2e8f0;
            border-radius: 12px; padding: 12px 15px; font-size: 14px;
            font-family: 'Outfit', sans-serif; color: #1e293b;
            transition: all 0.25s ease; outline: none; appearance: none;
            box-sizing: border-box;
        }
        .tpv-input:focus { background: #fff; border-color: #E5363D; box-shadow: 0 0 0 4px rgba(229,54,61,0.08); }
        .tpv-input::placeholder { color: #94a3b8; }
        textarea.tpv-input { min-height: 130px; resize: vertical; line-height: 1.6; }
        select.tpv-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2364748b' d='M6 8L0 0h12z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center; padding-right: 38px; cursor: pointer;
        }

        /* Service grid */
        .tpv-svc-grid {
            display: grid; grid-template-columns: repeat(auto-fill,minmax(155px,1fr)); gap: 10px; margin-bottom: 4px;
        }
        .tpv-svc-cb { display: none; }
        .tpv-svc-lbl {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; gap: 7px; padding: 14px 10px;
            background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px;
            cursor: pointer; font-size: 12px; font-weight: 600; color: #374151;
            min-height: 82px; transition: all 0.22s ease;
        }
        .tpv-svc-lbl .ico { font-size: 22px; line-height: 1; }
        .tpv-svc-cb:checked + .tpv-svc-lbl { border-color: #E5363D; background: #fff5f5; color: #E5363D; box-shadow: 0 4px 14px rgba(229,54,61,0.15); }
        .tpv-svc-lbl:hover { border-color: #fca5a5; background: #fff8f8; }

        /* Budget */
        .tpv-budget-val { text-align: center; font-size: 1.8rem; font-weight: 800; color: #E5363D; margin: 8px 0 12px; letter-spacing: -1px; }
        .tpv-budget-val span { font-size: 0.9rem; color: #94a3b8; font-weight: 500; }
        input[type="range"].tpv-slider { width: 100%; accent-color: #E5363D; height: 5px; border-radius: 3px; cursor: pointer; }
        .tpv-range-labels { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: 6px; }

        /* Timeline */
        .tpv-timeline-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(120px,1fr)); gap: 8px; }
        .tpv-tl-rb { display: none; }
        .tpv-tl-lbl {
            display: flex; align-items: center; justify-content: center; text-align: center;
            padding: 10px 8px; background: #f8fafc; border: 2px solid #e2e8f0;
            border-radius: 12px; cursor: pointer; font-size: 12px; font-weight: 600; color: #374151;
            transition: all 0.22s ease;
        }
        .tpv-tl-rb:checked + .tpv-tl-lbl { border-color: #E5363D; background: #fff5f5; color: #E5363D; }
        .tpv-tl-lbl:hover { border-color: #fca5a5; }

        /* File upload */
        .tpv-upload {
            border: 2px dashed #e2e8f0; border-radius: 14px; padding: 26px 18px;
            text-align: center; cursor: pointer; transition: all 0.25s ease;
            background: #f8fafc; position: relative;
        }
        .tpv-upload:hover { border-color: #E5363D; background: #fff8f8; }
        .tpv-upload input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
        .tpv-upload-ico { font-size: 28px; margin-bottom: 8px; }
        .tpv-upload-txt { font-size: 13px; color: #64748b; }
        .tpv-upload-txt strong { color: #E5363D; }
        .tpv-upload-hint { font-size: 11px; color: #94a3b8; margin-top: 5px; }

        /* Submit */
        .tpv-submit-btn {
            display: block; width: 100%;
            background: linear-gradient(135deg, #E5363D 0%, #c0121a 100%);
            color: #ffffff; font-family: 'Outfit', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            padding: 16px 40px; border: none; border-radius: 50px; cursor: pointer;
            transition: all 0.3s ease; box-shadow: 0 10px 30px -5px rgba(229,54,61,0.4);
            margin-top: 8px;
        }
        .tpv-submit-btn:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -5px rgba(229,54,61,0.5); }
        .tpv-submit-note { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 12px; }
        .tpv-alert {
            border-radius: 14px; padding: 14px 16px; margin-bottom: 22px;
            font-size: 14px; line-height: 1.5; border: 1px solid transparent;
        }
        .tpv-alert-error { background: #fff1f2; color: #9f1239; border-color: #fecdd3; }

        /* Success */
        .tpv-success { display: none; text-align: center; padding: 60px 20px; }
        .tpv-success-ico {
            width: 78px; height: 78px;
            background: linear-gradient(135deg, #E5363D, #b91c1c);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 22px; font-size: 34px; color: #fff;
            animation: tpv-pop 0.5s cubic-bezier(0.175,0.885,0.32,1.275);
        }
        @keyframes tpv-pop { from{transform:scale(0)} to{transform:scale(1)} }
        .tpv-success h3 { font-size: 1.7rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .tpv-success p { color: #64748b; font-size: 14px; line-height: 1.7; }
        .tpv-back-btn {
            display: inline-block; margin-top: 20px; padding: 13px 34px;
            background: #E5363D; color: #fff; border-radius: 50px;
            font-weight: 700; font-size: 14px; text-decoration: none;
        }

        /* ── Sidebar ── */
        .tpv-sidebar { display: flex; flex-direction: column; gap: 22px; }
        @media(max-width:1060px){ .tpv-sidebar{ display: grid; grid-template-columns: 1fr 1fr; } }
        @media(max-width:580px){ .tpv-sidebar{ grid-template-columns:1fr; } }

        .tpv-card {
            background: #fff; border-radius: 22px; padding: 26px 24px;
            box-shadow: 0 8px 30px -10px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
        }
        .tpv-card-title {
            font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 16px;
            display: flex; align-items: center; gap: 10px;
        }
        .tpv-card-ico {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #fff0f0, #ffe4e4);
            border-radius: 9px; display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .tpv-why-list { list-style: none; display: flex; flex-direction: column; gap: 12px; padding: 0; margin: 0; }
        .tpv-why-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #374151; line-height: 1.55; }
        .tpv-check { width: 20px; height: 20px; background: #fff0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #E5363D; font-size: 11px; margin-top: 1px; }

        .tpv-steps { list-style: none; display: flex; flex-direction: column; gap: 0; padding: 0; margin: 0; }
        .tpv-step { display: flex; gap: 12px; padding-bottom: 18px; position: relative; }
        .tpv-step:last-child { padding-bottom: 0; }
        .tpv-step::before { content: ''; position: absolute; left: 13px; top: 28px; bottom: 0; width: 2px; background: linear-gradient(180deg,#fde8e9,transparent); }
        .tpv-step:last-child::before { display: none; }
        .tpv-step-n { width: 28px; height: 28px; border-radius: 50%; background: #E5363D; color: #fff; font-size: 12px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; z-index: 1; }
        .tpv-step-txt strong { font-size: 13px; font-weight: 700; color: #1e293b; display: block; }
        .tpv-step-txt span { font-size: 12px; color: #64748b; }

        .tpv-contact-card {
            background: linear-gradient(135deg, #E5363D 0%, #b91c1c 100%);
            border-radius: 18px; padding: 22px 20px; color: #fff;
        }
        .tpv-contact-card h4 { font-size: 15px; font-weight: 800; margin-bottom: 14px; }
        .tpv-contact-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 13px; }
        .tpv-contact-item:last-child { margin-bottom: 0; }
        .tpv-contact-item-ico {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(255,255,255,0.55);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #c81e25;
            box-shadow: 0 10px 20px -14px rgba(15,23,42,0.55);
            flex-shrink: 0;
        }
        .tpv-contact-item a { color: rgba(255,255,255,0.92); text-decoration: none; font-weight: 600; }
        .tpv-contact-item p { color: rgba(255,255,255,0.65); font-size: 11px; margin: 0; }

        .tpv-testimonial { background: linear-gradient(145deg,#f8fafc,#fff); border-left: 4px solid #E5363D; border-radius: 0 14px 14px 0; padding: 16px 14px; }
        .tpv-testimonial blockquote { font-size: 13px; color: #374151; line-height: 1.65; font-style: italic; margin: 0 0 10px; }

        .tpv-t-author { display: flex; align-items: center; gap: 10px; }
        .tpv-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg,#E5363D,#b91c1c); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 12px; }
        .tpv-author-name { font-size: 12px; font-weight: 700; color: #0f172a; }
        .tpv-author-role { font-size: 11px; color: #64748b; }
    </style>

    <!-- Same stylesheet stack as all other pages -->
    <link rel='stylesheet' id='elementor-frontend-css' href='../wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.35.3' media='all'>
    <link rel='stylesheet' id='ekit-widget-styles-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/widget-styles.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='ekit-responsive-css' href='../wp-content/plugins/elementskit-lite/widgets/init/assets/css/responsive.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='TPV-css-variable-css' href='../wp-content/themes/tpv/assets/css/css-variable.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='fontawesome-css' href='../wp-content/themes/tpv/assets/css/all.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='bootstrap-css' href='../wp-content/themes/tpv/assets/css/bootstrap.min.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='TPV-style-css' href='../wp-content/themes/tpv/style.css?ver=1.0.8' media='all'>
    <link rel='stylesheet' id='elementor-icons-ekiticons-css' href='../wp-content/plugins/elementskit-lite/modules/elementskit-icon-pack/assets/css/ekiticons.css?ver=3.7.9' media='all'>
    <link rel='stylesheet' id='elementor-post-225-css' href='../wp-content/uploads/elementor/css/post-225.css?ver=1770715449' media='all'>
    <link rel='stylesheet' id='elementor-post-1688-css' href='../wp-content/uploads/elementor/css/post-1688.css?ver=1770715449' media='all'>

    <script src="../wp-includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script src="../wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>

    <script>var elementskit_module_parallax_url = "https://tpvconstruction.com.ng/wp-content/plugins/elementskit/modules/parallax/";</script>
    <style>.e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded):not(.e-no-lazyload) * { background-image: none !important; }</style>
    <link rel="icon" href="../wp-content/uploads/2024/06/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" href="../wp-content/uploads/2024/06/favicon.png">
</head>

<body class="wp-singular page-template page-template-elementor_header_footer page wp-custom-logo tt-magic-cursor elementor-default elementor-template-full-width">
    <div class="preloader"><div class="loading-container"><div class="loading"></div><div id="loading-icon"><img src="../wp-content/themes/tpv/assets/images/loader.png" alt=""></div></div></div>
    <div id="magic-cursor"><div id="ball"></div></div>
    <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

    <?php include('../includes/quote_header.php'); ?>

    <!-- Hero -->
    <section class="tpv-quote-hero" id="content">
        <div class="tpv-quote-hero-inner">
            <div class="tpv-quote-badge">⭐ Free No-Obligation Estimate</div>
            <h1>Start Your <em>Dream Project</em><br>With a Free Quote</h1>
            <p>Fill in the form and our expert team will prepare a tailored cost estimate for your construction project within 24 hours.</p>
            <div class="tpv-hero-stats">
                <div><div class="tpv-hero-stat-num">24h</div><div class="tpv-hero-stat-lbl">Response Time</div></div>
                <div><div class="tpv-hero-stat-num">500+</div><div class="tpv-hero-stat-lbl">Projects Delivered</div></div>
                <div><div class="tpv-hero-stat-num">100%</div><div class="tpv-hero-stat-lbl">Free & No Obligation</div></div>
            </div>
            <nav class="tpv-breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="../">Home</a></li>
                    <li>›</li>
                    <li>Get a Free Quote</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main -->
    <div class="tpv-quote-wrap">

        <!-- FORM -->
        <div class="tpv-form-card">
            <div id="tpvQuoteFormWrap" <?php echo $quoteSuccess ? 'style="display:none;"' : ''; ?>>
                <h2>Tell Us About Your Project</h2>
                <p>The more detail you provide, the more accurate your estimate will be. Fields marked <span style="color:#E5363D">*</span> are required.</p>

                <?php if ($quoteError): ?>
                    <div class="tpv-alert tpv-alert-error"><?php echo quoteHtml($quoteError); ?></div>
                <?php endif; ?>

                <form id="tpvQuoteForm" method="POST" action="index.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="quote_submit" value="1">

                    <div class="tpv-divider">Personal Information</div>
                    <div class="tpv-row">
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qFirst">First Name <span class="req">*</span></label>
                            <input id="qFirst" name="first_name" type="text" class="tpv-input" placeholder="e.g. Chukwuemeka" required>
                        </div>
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qLast">Last Name <span class="req">*</span></label>
                            <input id="qLast" name="last_name" type="text" class="tpv-input" placeholder="e.g. Okonkwo" required>
                        </div>
                    </div>
                    <div class="tpv-row">
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qEmail">Email Address <span class="req">*</span></label>
                            <input id="qEmail" name="email" type="email" class="tpv-input" placeholder="you@example.com" required>
                        </div>
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qPhone">Phone Number <span class="req">*</span></label>
                            <input id="qPhone" name="phone" type="tel" class="tpv-input" placeholder="080XXXXXXXX" required>
                        </div>
                    </div>
                    <div class="tpv-row">
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qCompany">Company / Organisation</label>
                            <input id="qCompany" name="company" type="text" class="tpv-input" placeholder="(Optional)">
                        </div>
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qClientType">Client Type <span class="req">*</span></label>
                            <select id="qClientType" name="client_type" class="tpv-input" required>
                                <option value="" disabled selected>Select…</option>
                                <option>Individual / Private</option>
                                <option>Corporate Organisation</option>
                                <option>Government / Public Sector</option>
                                <option>NGO / Non-Profit</option>
                                <option>Real Estate Developer</option>
                            </select>
                        </div>
                    </div>

                    <div class="tpv-divider">Services Required</div>
                    <p style="font-size:13px;color:#64748b;margin-bottom:14px;">Select all services that apply.</p>
                    <div class="tpv-svc-grid">
                        <div><input class="tpv-svc-cb" type="checkbox" id="s1" name="services[]" value="Building Construction"><label class="tpv-svc-lbl" for="s1"><span class="ico">🏗️</span>Building Construction</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s2" name="services[]" value="Architecture & Design"><label class="tpv-svc-lbl" for="s2"><span class="ico">📐</span>Architecture & Design</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s3" name="services[]" value="Building Renovation"><label class="tpv-svc-lbl" for="s3"><span class="ico">🔨</span>Building Renovation</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s4" name="services[]" value="Flooring & Roofing"><label class="tpv-svc-lbl" for="s4"><span class="ico">🏠</span>Flooring & Roofing</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s5" name="services[]" value="Building Maintenance"><label class="tpv-svc-lbl" for="s5"><span class="ico">🔧</span>Building Maintenance</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s6" name="services[]" value="Project Management"><label class="tpv-svc-lbl" for="s6"><span class="ico">📋</span>Project Management</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s7" name="services[]" value="Real Estate"><label class="tpv-svc-lbl" for="s7"><span class="ico">🏢</span>Real Estate</label></div>
                        <div><input class="tpv-svc-cb" type="checkbox" id="s8" name="services[]" value="Other"><label class="tpv-svc-lbl" for="s8"><span class="ico">✳️</span>Other / Mixed</label></div>
                    </div>

                    <div class="tpv-divider">Project Details</div>
                    <div class="tpv-row">
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qPType">Project Type <span class="req">*</span></label>
                            <select id="qPType" name="project_type" class="tpv-input" required>
                                <option value="" disabled selected>Select type…</option>
                                <option>Residential (Single Family)</option>
                                <option>Residential (Multi-Unit / Flats)</option>
                                <option>Commercial Office Building</option>
                                <option>Industrial / Warehouse</option>
                                <option>Retail / Shopping Complex</option>
                                <option>Government / Public Infrastructure</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qPSize">Project Size (sqm)</label>
                            <select id="qPSize" name="project_size" class="tpv-input">
                                <option value="" disabled selected>Select size…</option>
                                <option>Small (Under 100 sqm)</option>
                                <option>Medium (100–499 sqm)</option>
                                <option>Large (500–1,999 sqm)</option>
                                <option>Very Large (2,000+ sqm)</option>
                                <option>Not sure yet</option>
                            </select>
                        </div>
                    </div>
                    <div class="tpv-row">
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qState">Project State <span class="req">*</span></label>
                            <select id="qState" name="project_location" class="tpv-input" required>
                                <option value="" disabled selected>Select state…</option>
                                <?php $states=["Abia","Adamawa","Akwa Ibom","Anambra","Bauchi","Bayelsa","Benue","Borno","Cross River","Delta","Ebonyi","Edo","Ekiti","Enugu","FCT – Abuja","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamfara"]; foreach($states as $s) echo "<option>$s</option>"; ?>
                            </select>
                        </div>
                        <div class="tpv-fg">
                            <label class="tpv-label" for="qStart">Expected Start Date</label>
                            <input id="qStart" name="start_date" type="date" class="tpv-input" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="tpv-divider">Estimated Budget (₦)</div>
                    <div class="tpv-budget-val" id="tpvBudgetVal">₦5,000,000 <span>– indicative</span></div>
                    <input type="range" class="tpv-slider" id="tpvBudget" name="budget" min="500000" max="500000000" step="500000" value="5000000">
                    <div class="tpv-range-labels"><span>₦500K</span><span>₦500M+</span></div>

                    <div class="tpv-divider" style="margin-top:26px;">Desired Completion Timeline</div>
                    <div class="tpv-timeline-grid">
                        <div><input class="tpv-tl-rb" type="radio" id="tl1" name="timeline" value="Under 3 months"><label class="tpv-tl-lbl" for="tl1">Under 3 months</label></div>
                        <div><input class="tpv-tl-rb" type="radio" id="tl2" name="timeline" value="3–6 months"><label class="tpv-tl-lbl" for="tl2">3–6 months</label></div>
                        <div><input class="tpv-tl-rb" type="radio" id="tl3" name="timeline" value="6–12 months"><label class="tpv-tl-lbl" for="tl3">6–12 months</label></div>
                        <div><input class="tpv-tl-rb" type="radio" id="tl4" name="timeline" value="1–2 years"><label class="tpv-tl-lbl" for="tl4">1–2 years</label></div>
                        <div><input class="tpv-tl-rb" type="radio" id="tl5" name="timeline" value="Over 2 years"><label class="tpv-tl-lbl" for="tl5">Over 2 years</label></div>
                        <div><input class="tpv-tl-rb" type="radio" id="tl6" name="timeline" value="Flexible"><label class="tpv-tl-lbl" for="tl6">Flexible</label></div>
                    </div>

                    <div class="tpv-divider" style="margin-top:26px;">Project Description & Files</div>
                    <div class="tpv-fg">
                        <label class="tpv-label" for="qDesc">Describe Your Project <span class="req">*</span></label>
                        <textarea id="qDesc" name="description" class="tpv-input" placeholder="Describe your goals, requirements, preferred materials, finishes, or anything that will help us prepare an accurate quote…" required></textarea>
                    </div>
                    <div class="tpv-fg">
                        <label class="tpv-label">Attach Files (Optional)</label>
                        <div class="tpv-upload" id="tpvUpload">
                            <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="tpv-upload-ico">📎</div>
                            <div class="tpv-upload-txt"><strong>Click to upload</strong> or drag & drop</div>
                            <div class="tpv-upload-hint">Plans, drawings, photos — PDF, Word, JPG (max 10MB each)</div>
                        </div>
                        <div id="tpvFileNames" style="margin-top:8px;font-size:12px;color:#64748b;"></div>
                    </div>
                    <div class="tpv-fg">
                        <label class="tpv-label" for="qHear">How Did You Hear About Us?</label>
                        <select id="qHear" name="referral_source" class="tpv-input">
                            <option value="" disabled selected>Select one…</option>
                            <option>Google / Online Search</option>
                            <option>Social Media</option>
                            <option>Referral / Word of Mouth</option>
                            <option>Previous Client</option>
                            <option>TV / Radio / Billboard</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <button type="submit" class="tpv-submit-btn" id="tpvSubmitBtn">🚀 &nbsp; Submit Quote Request</button>
                    <p class="tpv-submit-note">🔒 Your information is secure and will never be shared. We respond within 24 hours.</p>
                </form>
            </div>

            <!-- Success state -->
            <div class="tpv-success" id="tpvSuccess" <?php echo $quoteSuccess ? 'style="display:block;"' : ''; ?>>
                <div class="tpv-success-ico">✓</div>
                <h3>Quote Request Sent!</h3>
                <p>Thank you! Our team has received your project details and will prepare a tailored estimate within <strong>24 hours</strong>.</p>
                <a href="../" class="tpv-back-btn">← Back to Home</a>
            </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="tpv-sidebar">
            <div class="tpv-card">
                <h3 class="tpv-card-title"><div class="tpv-card-ico">⭐</div>Why Choose TPV?</h3>
                <ul class="tpv-why-list">
                    <li class="tpv-why-item"><div class="tpv-check">✓</div><span>Over 10 years of proven construction expertise across Nigeria</span></li>
                    <li class="tpv-why-item"><div class="tpv-check">✓</div><span>Transparent pricing — no hidden fees or surprise charges</span></li>
                    <li class="tpv-why-item"><div class="tpv-check">✓</div><span>Certified engineers, architects & project managers on every job</span></li>
                    <li class="tpv-why-item"><div class="tpv-check">✓</div><span>On-time delivery with stringent quality assurance</span></li>
                    <li class="tpv-why-item"><div class="tpv-check">✓</div><span>12-month post-construction workmanship guarantee</span></li>
                </ul>
            </div>

            <div class="tpv-card">
                <h3 class="tpv-card-title"><div class="tpv-card-ico">🗂️</div>How It Works</h3>
                <ol class="tpv-steps">
                    <li class="tpv-step"><div class="tpv-step-n">1</div><div class="tpv-step-txt"><strong>Submit Your Request</strong><span>Fill in this form with your project details.</span></div></li>
                    <li class="tpv-step"><div class="tpv-step-n">2</div><div class="tpv-step-txt"><strong>Expert Review</strong><span>Our team analyses your requirements within 24h.</span></div></li>
                    <li class="tpv-step"><div class="tpv-step-n">3</div><div class="tpv-step-txt"><strong>Receive Your Estimate</strong><span>We send a detailed, itemised cost estimate.</span></div></li>
                    <li class="tpv-step"><div class="tpv-step-n">4</div><div class="tpv-step-txt"><strong>Consultation Call</strong><span>Our PM calls to discuss and refine details.</span></div></li>
                    <li class="tpv-step"><div class="tpv-step-n">5</div><div class="tpv-step-txt"><strong>Break Ground!</strong><span>We mobilise and your project begins.</span></div></li>
                </ol>
            </div>

            <div class="tpv-contact-card">
                <h4>Prefer to Talk Directly?</h4>
                <div class="tpv-contact-item"><div class="tpv-contact-item-ico">📞</div><div><a href="tel:09097128241">09097128241</a><p>Abuja / Ogun offices</p></div></div>
                <div class="tpv-contact-item"><div class="tpv-contact-item-ico">📞</div><div><a href="tel:08069418816">08069418816</a><p>Nasarawa office</p></div></div>
                <div class="tpv-contact-item"><div class="tpv-contact-item-ico">📞</div><div><a href="tel:08104830712">08104830712</a><p>Lagos office</p></div></div>
                <div class="tpv-contact-item"><div class="tpv-contact-item-ico">✉️</div><div><a href="mailto:info@tpvconstruction.com.ng">info@tpvconstruction.com.ng</a><p>Email us anytime</p></div></div>
            </div>

            <div class="tpv-testimonial">
                <blockquote>"TPV Construction and Services LTD delivered our three-storey commercial complex on time and within budget. The quality surpassed expectations — highly recommended!"</blockquote>
                <div class="tpv-t-author">
                    <div class="tpv-avatar">AO</div>
                    <div><div class="tpv-author-name">Alhaji Abubakar O.</div><div class="tpv-author-role">Real Estate Developer, Abuja</div></div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Same script stack as contact-us -->
    <script>
        const lazyloadRunObserver = () => {
            const lazyloadBackgrounds = document.querySelectorAll(`.e-con.e-parent:not(.e-lazyloaded)`);
            const lazyloadBackgroundObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('e-lazyloaded');
                        lazyloadBackgroundObserver.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '200px 0px 200px 0px' });
            lazyloadBackgrounds.forEach((el) => lazyloadBackgroundObserver.observe(el));
        };
        ['DOMContentLoaded','elementor/lazyload/observe'].forEach(e => document.addEventListener(e, lazyloadRunObserver));
    </script>
    <script src="../wp-includes/js/dist/hooks.min.js?ver=dd5603f07f9220ed27f1"></script>
    <script src="../wp-includes/js/dist/i18n.min.js?ver=c26c3dc7bed366793375"></script>
    <script>if(typeof wp !== 'undefined' && wp.i18n) { wp.i18n.setLocaleData({ 'text direction\u0004ltr': ['ltr'] }); }</script>
    <script src="../wp-content/themes/tpv/assets/js/SmoothScroll.js?ver=1.0.8"></script>
    <script src="../wp-content/themes/tpv/assets/js/gsap.min.js?ver=1.0.8"></script>
    <script src="../wp-content/themes/tpv/assets/js/magiccursor.js?ver=1.0.8"></script>
    <script src="../wp-content/themes/tpv/assets/js/SplitText.js?ver=1.0.8"></script>
    <script src="../wp-content/themes/tpv/assets/js/ScrollTrigger.min.js?ver=1.0.8"></script>
    <script src="../wp-content/themes/tpv/assets/js/function.js?ver=1.0.8"></script>
    <script src="../wp-content/plugins/elementskit-lite/libs/framework/assets/js/frontend-script.js?ver=3.7.9"></script>
    <script src="../wp-content/plugins/elementskit-lite/widgets/init/assets/js/widget-scripts.js?ver=3.7.9"></script>

    <script>
    jQuery(document).ready(function($) {
        'use strict';

        /* Budget slider */
        var $sl = $('#tpvBudget'), $dv = $('#tpvBudgetVal');
        function fmtBudget(v) {
            v = parseInt(v);
            if (v >= 1e9) return '₦' + (v/1e9).toFixed(1) + 'B';
            if (v >= 1e6) return '₦' + (v/1e6).toFixed(1) + 'M';
            return '₦' + (v/1e3).toFixed(0) + 'K';
        }
        function updateSlider(val) {
            var pct = ((val - $sl.attr('min')) / ($sl.attr('max') - $sl.attr('min'))) * 100;
            $sl.css('background','linear-gradient(90deg,#E5363D '+pct+'%,#e2e8f0 '+pct+'%)');
        }
        $sl.on('input', function() {
            $dv.html(fmtBudget(this.value) + ' <span>– indicative</span>');
            updateSlider(this.value);
        });
        updateSlider($sl.val());

        /* File names */
        $('input[type="file"]').on('change', function() {
            var names = Array.from(this.files).map(function(f){ return '📄 ' + f.name; }).join('<br>');
            $('#tpvFileNames').html(names);
        });

        /* Form submit */
        $('#tpvQuoteForm').on('submit', function(e) {
            var first = null;
            $(this).find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    $(this).css('border-color','#E5363D');
                    if (!first) first = $(this);
                } else {
                    $(this).css('border-color','');
                }
            });
            if (first) {
                e.preventDefault();
                $('html,body').animate({ scrollTop: first.offset().top - 120 }, 400);
                return;
            }
            $('#tpvSubmitBtn').prop('disabled', true).html('⏳ &nbsp; Sending…');
        });
        $('#tpvQuoteForm').on('input change', '[required]', function() {
            if ($(this).val().trim()) $(this).css('border-color','');
        });
    });
    </script>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
