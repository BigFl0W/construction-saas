<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/includes/email-template.php';

class Mailer {
    private $fromEmail;
    private $fromName;
    private $companyName;
    private $resendApiKey;

    public function __construct() {
        $this->fromEmail = getenv('RESEND_FROM_EMAIL') ?: 'TPV Construction <onboarding@resend.dev>';
        $this->fromName = 'TPV Construction and Services LTD';
        $this->companyName = 'TPV Construction and Services LTD';
        $this->resendApiKey = getenv('RESEND_API_KEY') ?: 're_DFJ5tJNL_KqzKcxPwbfuuuJGpXwHAaJKg';
    }

    public function send($to, $subject, $body, $replyTo = null) {
        $html = $this->buildTemplate($subject, $body);
        return $this->sendWithResend($to, $subject, $html, $replyTo);
    }

    private function sendWithResend($to, $subject, $html, $replyTo = null) {
        if (empty($this->resendApiKey)) {
            error_log('Resend API key is not configured.');
            return false;
        }
        if (!function_exists('curl_init')) {
            error_log('Resend email failed. PHP cURL extension is not available.');
            return false;
        }

        $payload = [
            'from' => $this->fromEmail,
            'to' => [$to],
            'subject' => $subject,
            'html' => $html
        ];

        if ($replyTo) {
            $payload['reply_to'] = [$replyTo];
        }

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->resendApiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            error_log('Resend email failed. HTTP: ' . $httpCode . ' Error: ' . $error . ' Response: ' . $response);
            return false;
        }

        return true;
    }

    private function buildTemplate($subject, $body) {
        return tpvRenderEmailTemplate([
            'subject' => $subject,
            'body' => $body,
            'company_name' => $this->companyName,
            'preview_text' => strip_tags($subject),
            'site_url' => defined('SITE_URL') ? SITE_URL : 'https://tpvconstruction.com.ng'
        ]);
    }

    public function sendToClient($clientEmail, $clientName, $subject, $message) {
        $body = "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>Dear <strong>" . htmlspecialchars($clientName) . "</strong>,</p>";
        $body .= "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>" . nl2br(htmlspecialchars($message)) . "</p>";
        $body .= "<p style='margin:16px 0 0;font-size:14px;color:#6b7a8f;'>Best regards,<br>{$this->fromName}</p>";
        return $this->send($clientEmail, $subject, $body);
    }

    public function sendToEmployee($employeeEmail, $employeeName, $subject, $message) {
        $body = "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>Dear <strong>" . htmlspecialchars($employeeName) . "</strong>,</p>";
        $body .= "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>" . nl2br(htmlspecialchars($message)) . "</p>";
        $body .= "<p style='margin:16px 0 0;font-size:14px;color:#6b7a8f;'>Best regards,<br>{$this->fromName}</p>";
        return $this->send($employeeEmail, $subject, $body);
    }

    public function sendToSupplier($supplierEmail, $supplierName, $subject, $message) {
        $body = "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>Dear <strong>" . htmlspecialchars($supplierName) . "</strong>,</p>";
        $body .= "<p style='margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;'>" . nl2br(htmlspecialchars($message)) . "</p>";
        $body .= "<p style='margin:16px 0 0;font-size:14px;color:#6b7a8f;'>Best regards,<br>{$this->fromName}</p>";
        return $this->send($supplierEmail, $subject, $body);
    }
}
