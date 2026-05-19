<?php
/**
 * Professional email template for TPV Construction notifications.
 *
 * Expected keys:
 * - subject: Email subject / main heading
 * - body: Already-prepared HTML body content
 * - company_name: Sender/company display name
 * - preview_text: Hidden inbox preview text
 * - accent_color: Brand accent color
 * - site_url: Public site URL
 */

if (!function_exists('tpvRenderEmailTemplate')) {
    function tpvRenderEmailTemplate(array $data) {
        $subject = htmlspecialchars($data['subject'] ?? 'Notification', ENT_QUOTES, 'UTF-8');
        $body = $data['body'] ?? '';
        $companyName = htmlspecialchars($data['company_name'] ?? 'TPV Construction and Services LTD', ENT_QUOTES, 'UTF-8');
        $previewText = htmlspecialchars($data['preview_text'] ?? $subject, ENT_QUOTES, 'UTF-8');
        $accentColor = htmlspecialchars($data['accent_color'] ?? '#E5363D', ENT_QUOTES, 'UTF-8');
        $siteUrl = htmlspecialchars($data['site_url'] ?? 'https://tpvconstruction.com.ng', ENT_QUOTES, 'UTF-8');
        $year = date('Y');

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{$subject}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;color:#1f2937;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;line-height:1px;font-size:1px;">
    {$previewText}
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#eef2f7;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;max-width:680px;">
          <tr>
            <td style="padding:0 0 18px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td align="left" style="font-size:13px;color:#64748b;">
                    <a href="{$siteUrl}" style="color:#64748b;text-decoration:none;">{$companyName}</a>
                  </td>
                  <td align="right" style="font-size:12px;color:#94a3b8;">
                    Professional notification
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style="background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 18px 45px rgba(15,23,42,0.10);">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td style="background:#111827;padding:30px 34px;border-bottom:4px solid {$accentColor};">
                    <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:1.7px;text-transform:uppercase;color:#fca5a5;">
                      {$companyName}
                    </p>
                    <h1 style="margin:0;font-size:24px;line-height:1.3;font-weight:700;color:#ffffff;">
                      {$subject}
                    </h1>
                  </td>
                </tr>

                <tr>
                  <td style="padding:34px;">
                    {$body}
                  </td>
                </tr>

                <tr>
                  <td style="padding:0 34px 34px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;">
                      <tr>
                        <td style="padding:18px 20px;">
                          <p style="margin:0 0 6px;font-size:13px;font-weight:700;color:#0f172a;">Need help?</p>
                          <p style="margin:0;font-size:13px;line-height:1.6;color:#64748b;">
                            Reply to this email or contact TPV Construction and Services LTD through the official website.
                          </p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:22px 12px 0;">
              <p style="margin:0 0 8px;font-size:12px;line-height:1.6;color:#64748b;">
                &copy; {$year} {$companyName}. All rights reserved.
              </p>
              <p style="margin:0;font-size:11px;line-height:1.6;color:#94a3b8;">
                This message was sent from the TPV Construction management notification system.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
