<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/classes/Functions.php';
require_once __DIR__ . '/classes/Invoice.php';

$functions = Functions::getInstance();
$invoiceModel = new Invoice();

$token = trim((string) ($_GET['token'] ?? ''));
$invoice = $token !== '' ? $invoiceModel->getByToken($token) : null;

if ($invoice) {
    $invoiceModel->markViewedByToken($token);
}

$companyName = trim((string) $functions->getSetting('company_name', 'TPV Construction and Services LTD'));
$companyEmail = trim((string) $functions->getSetting('company_email', 'info@tpvconstruction.com.ng'));
$companyPhone = trim((string) $functions->getSetting('company_phone', '+234 701 234 5678'));
$companyAddress = trim((string) $functions->getSetting('company_address', 'Area 11, Abuja, Nigeria'));
$invoiceTerms = trim((string) $functions->getSetting('invoice_terms', 'Payment is due on or before the due date stated on this invoice.'));
$invoiceFooter = trim((string) $functions->getSetting('invoice_footer', 'Thank you for choosing TPV Construction and Services LTD.'));
$items = $invoice ? $invoiceModel->getItems((int) $invoice['id']) : [];

function publicInvoiceStatusClass(string $status): string {
    return match ($status) {
        'paid' => 'is-paid',
        'partial' => 'is-partial',
        'overdue' => 'is-overdue',
        'cancelled' => 'is-cancelled',
        default => 'is-open',
    };
}

$pageTitle = $invoice ? $invoice['invoice_number'] . ' · ' . $companyName : 'Invoice Not Found · ' . $companyName;
$replySubject = $invoice ? rawurlencode('Re: Invoice ' . $invoice['invoice_number']) : rawurlencode('Invoice enquiry');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <meta name="description" content="Secure invoice view for <?php echo htmlspecialchars($companyName); ?>">
    <style>
        :root {
            --bg: #eef4fb;
            --panel: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #dbe5f0;
            --accent: #ef4444;
            --accent-dark: #b91c1c;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(239, 68, 68, 0.08), transparent 30%),
                linear-gradient(180deg, #f7fbff 0%, var(--bg) 100%);
            color: var(--ink);
        }
        .wrap {
            max-width: 1120px;
            margin: 0 auto;
            padding: 28px 18px 42px;
        }
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 62%, #334155 100%);
            color: #fff;
            border-radius: 32px;
            padding: 28px;
            box-shadow: var(--shadow);
        }
        .hero-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-start;
        }
        .eyebrow {
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.72;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.2rem);
            line-height: 0.95;
            letter-spacing: -0.04em;
        }
        .hero p {
            margin: 14px 0 0;
            max-width: 640px;
            color: rgba(255,255,255,0.78);
            line-height: 1.7;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 700;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
        }
        .status-pill.is-paid { background: rgba(22,163,74,.18); color: #d1fae5; }
        .status-pill.is-partial { background: rgba(217,119,6,.18); color: #fde68a; }
        .status-pill.is-overdue { background: rgba(220,38,38,.22); color: #fecaca; }
        .status-pill.is-cancelled { background: rgba(15,23,42,.28); color: #e2e8f0; }
        .status-pill.is-open { background: rgba(255,255,255,0.12); color: #fff; }
        .grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
            margin-top: 24px;
        }
        .panel {
            background: var(--panel);
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 18px 50px rgba(15,23,42,.08);
            border: 1px solid rgba(148,163,184,.16);
        }
        .section-title {
            margin: 0 0 18px;
            font-size: 1.06rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 18px;
        }
        .details div { min-width: 0; }
        .details small {
            display: block;
            font-size: 0.73rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 6px;
            font-weight: 700;
        }
        .details strong, .details span {
            display: block;
            font-size: 1rem;
            line-height: 1.5;
            word-break: break-word;
        }
        .totals { display: grid; gap: 12px; }
        .totals .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--line);
            padding-bottom: 12px;
        }
        .totals .row:last-child { border-bottom: 0; padding-bottom: 0; }
        .totals .row strong { font-size: 1.02rem; }
        .totals .grand strong { color: var(--accent-dark); font-size: 1.15rem; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            text-align: left;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }
        td {
            padding: 14px 0;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }
        td:last-child, th:last-child { text-align: right; }
        .note, .terms {
            color: var(--muted);
            line-height: 1.8;
            white-space: pre-line;
        }
        .cta-stack {
            display: grid;
            gap: 14px;
        }
        .cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .cta:hover { transform: translateY(-1px); }
        .cta-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 18px 36px rgba(239,68,68,.24);
        }
        .cta-secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
        }
        .meta-box {
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 18px;
            background: #fbfdff;
        }
        .error-state {
            max-width: 720px;
            margin: 8vh auto 0;
            text-align: center;
        }
        .error-state h1 {
            margin: 0 0 10px;
            font-size: 2.2rem;
            letter-spacing: -0.04em;
        }
        .error-state p {
            color: var(--muted);
            line-height: 1.8;
        }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .details { grid-template-columns: 1fr; }
            .hero { padding: 22px; border-radius: 24px; }
            .panel { border-radius: 22px; padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <?php if (!$invoice): ?>
            <div class="panel error-state">
                <div class="eyebrow">Invoice Lookup</div>
                <h1>Invoice Not Available</h1>
                <p>The invoice link is invalid or no longer available. If you expected to receive an invoice, please contact <?php echo htmlspecialchars($companyName); ?> and we will resend it.</p>
                <div class="cta-stack" style="max-width:320px;margin:28px auto 0;">
                    <a class="cta cta-primary" href="mailto:<?php echo htmlspecialchars($companyEmail); ?>?subject=<?php echo $replySubject; ?>">Email Support</a>
                    <a class="cta cta-secondary" href="tel:<?php echo htmlspecialchars($companyPhone); ?>">Call <?php echo htmlspecialchars($companyPhone); ?></a>
                </div>
            </div>
        <?php else: ?>
            <section class="hero">
                <div class="hero-top">
                    <div>
                        <div class="eyebrow">Secure Invoice View</div>
                        <h1><?php echo htmlspecialchars($invoice['invoice_number']); ?></h1>
                        <p>This invoice was issued by <?php echo htmlspecialchars($companyName); ?>. You can review the full breakdown below and reply to the email you received if you need any clarification.</p>
                    </div>
                    <div class="status-pill <?php echo htmlspecialchars(publicInvoiceStatusClass($invoice['status'])); ?>">
                        Status: <?php echo htmlspecialchars(ucfirst($invoice['status'])); ?>
                    </div>
                </div>
            </section>

            <div class="grid">
                <section class="panel">
                    <h2 class="section-title">Invoice Details</h2>
                    <div class="details">
                        <div><small>Billed To</small><strong><?php echo htmlspecialchars($invoice['recipient_name'] ?: ($invoice['client_name'] ?? '')); ?></strong></div>
                        <div><small>Recipient Email</small><span><?php echo htmlspecialchars($invoice['recipient_email'] ?: ($invoice['client_email'] ?? '')); ?></span></div>
                        <div><small>Invoice Date</small><span><?php echo htmlspecialchars($functions->formatDate($invoice['invoice_date'], 'M j, Y')); ?></span></div>
                        <div><small>Due Date</small><span><?php echo htmlspecialchars($functions->formatDate($invoice['due_date'], 'M j, Y')); ?></span></div>
                        <div><small>Project</small><span><?php echo htmlspecialchars($invoice['project_name'] ?? 'Not linked'); ?></span></div>
                        <div><small>Reference</small><span><?php echo htmlspecialchars($invoice['invoice_number']); ?></span></div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                    <td><?php echo number_format((float) $item['quantity'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($functions->formatCurrency((float) $item['unit_price'])); ?></td>
                                    <td><?php echo htmlspecialchars($functions->formatCurrency((float) ($item['line_total'] ?? ((float) $item['quantity'] * (float) $item['unit_price'])))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if (!empty($invoice['notes'])): ?>
                        <div class="meta-box" style="margin-top:20px;">
                            <h3 class="section-title" style="margin-bottom:10px;">Notes</h3>
                            <div class="note"><?php echo htmlspecialchars($invoice['notes']); ?></div>
                        </div>
                    <?php endif; ?>
                </section>

                <aside class="panel">
                    <h2 class="section-title">Summary</h2>
                    <div class="totals">
                        <div class="row"><span>Subtotal</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $invoice['subtotal'])); ?></strong></div>
                        <div class="row"><span>Tax</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $invoice['tax'])); ?></strong></div>
                        <div class="row"><span>Total Invoice</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $invoice['total'])); ?></strong></div>
                        <div class="row"><span>Amount Paid</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $invoice['amount_paid'])); ?></strong></div>
                        <div class="row grand"><span>Balance Due</span><strong><?php echo htmlspecialchars($functions->formatCurrency((float) $invoice['total'] - (float) $invoice['amount_paid'])); ?></strong></div>
                    </div>

                    <div class="meta-box" style="margin-top:22px;">
                        <h3 class="section-title" style="margin-bottom:10px;">Need Help?</h3>
                        <div class="cta-stack">
                            <a class="cta cta-primary" href="mailto:<?php echo htmlspecialchars($companyEmail); ?>?subject=<?php echo $replySubject; ?>">Reply About This Invoice</a>
                            <a class="cta cta-secondary" href="tel:<?php echo htmlspecialchars($companyPhone); ?>">Call <?php echo htmlspecialchars($companyPhone); ?></a>
                        </div>
                    </div>

                    <div class="meta-box" style="margin-top:22px;">
                        <h3 class="section-title" style="margin-bottom:10px;">Issuer</h3>
                        <div class="note"><?php echo htmlspecialchars($companyName); ?></div>
                        <div class="note"><?php echo htmlspecialchars($companyEmail); ?></div>
                        <div class="note"><?php echo htmlspecialchars($companyPhone); ?></div>
                        <div class="note"><?php echo htmlspecialchars($companyAddress); ?></div>
                    </div>

                    <?php if ($invoiceTerms !== ''): ?>
                        <div class="meta-box" style="margin-top:22px;">
                            <h3 class="section-title" style="margin-bottom:10px;">Terms</h3>
                            <div class="terms"><?php echo htmlspecialchars($invoiceTerms); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($invoiceFooter !== ''): ?>
                        <div style="margin-top:18px;color:var(--muted);line-height:1.8;"><?php echo nl2br(htmlspecialchars($invoiceFooter)); ?></div>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
