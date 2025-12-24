<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Send verification code email to user
     * 
     * @param string $toEmail Recipient email address
     * @param string $verificationCode 4-digit verification code
     * @return bool Success status
     */
    public function sendVerificationCode(string $toEmail, string $verificationCode): bool
    {
        try {
            $email = (new Email())
                ->from($_ENV['EMAIL_FROM'] ?? 'noreply@laundry-free.com')
                ->to($toEmail)
                ->subject('Verify Your Account - Laundry Free')
                ->html($this->getVerificationEmailTemplate($verificationCode));

            $this->mailer->send($email);

            $this->logger->info('Verification email sent successfully', [
                'email' => $toEmail,
                'code' => $verificationCode
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send verification email', [
                'email' => $toEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Generate HTML email template for verification code
     * 
     * @param string $verificationCode
     * @return string HTML content
     */
    private function getVerificationEmailTemplate(string $verificationCode): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .container {
                    background-color: #f9f9f9;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #4CAF50;
                    margin: 0;
                }
                .code-box {
                    background-color: #fff;
                    border: 2px solid #4CAF50;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    margin: 30px 0;
                }
                .verification-code {
                    font-size: 36px;
                    font-weight: bold;
                    color: #4CAF50;
                    letter-spacing: 8px;
                    margin: 10px 0;
                }
                .message {
                    font-size: 16px;
                    color: #555;
                    margin: 20px 0;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                }
                .warning {
                    color: #e74c3c;
                    font-size: 14px;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🧺 Laundry Free</h1>
                    <p>Email Verification</p>
                </div>
                
                <div class="message">
                    <p>Thank you for registering with Laundry Free!</p>
                    <p>To complete your registration, please use the following verification code:</p>
                </div>
                
                <div class="code-box">
                    <p style="margin: 0; font-size: 14px; color: #777;">Your Verification Code</p>
                    <div class="verification-code">{$verificationCode}</div>
                </div>
                
                <div class="message">
                    <p>Enter this code in the app to activate your account.</p>
                    <p class="warning">⚠️ This code will expire in 15 minutes.</p>
                </div>
                
                <div class="footer">
                    <p>If you didn't request this code, please ignore this email.</p>
                    <p>&copy; 2024 Laundry Free. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Send password reset code email to user
     * 
     * @param string $toEmail Recipient email address
     * @param string $resetCode 4-digit reset code
     * @return bool Success status
     */
    public function sendPasswordResetCode(string $toEmail, string $resetCode): bool
    {
        try {
            $email = (new Email())
                ->from($_ENV['EMAIL_FROM'] ?? 'noreply@laundry-free.com')
                ->to($toEmail)
                ->subject('Password Reset Code - Laundry Free')
                ->html($this->getPasswordResetEmailTemplate($resetCode));

            $this->mailer->send($email);

            $this->logger->info('Password reset email sent successfully', [
                'email' => $toEmail,
                'code' => $resetCode
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send password reset email', [
                'email' => $toEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Generate HTML email template for password reset code
     * 
     * @param string $resetCode
     * @return string HTML content
     */
    private function getPasswordResetEmailTemplate(string $resetCode): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .container {
                    background-color: #f9f9f9;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #FF6B6B;
                    margin: 0;
                }
                .code-box {
                    background-color: #fff;
                    border: 2px solid #FF6B6B;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    margin: 30px 0;
                }
                .reset-code {
                    font-size: 36px;
                    font-weight: bold;
                    color: #FF6B6B;
                    letter-spacing: 8px;
                    margin: 10px 0;
                }
                .message {
                    font-size: 16px;
                    color: #555;
                    margin: 20px 0;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                }
                .warning {
                    color: #e74c3c;
                    font-size: 14px;
                    margin-top: 20px;
                }
                .security-notice {
                    background-color: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🧺 Laundry Free</h1>
                    <p>Password Reset Request</p>
                </div>
                
                <div class="message">
                    <p>We received a request to reset your password.</p>
                    <p>Use the following code to reset your password:</p>
                </div>
                
                <div class="code-box">
                    <p style="margin: 0; font-size: 14px; color: #777;">Your Reset Code</p>
                    <div class="reset-code">{$resetCode}</div>
                </div>
                
                <div class="message">
                    <p>Enter this code along with your new password to complete the reset.</p>
                    <p class="warning">⚠️ This code will expire in 15 minutes.</p>
                </div>
                
                <div class="security-notice">
                    <p style="margin: 0;"><strong>🔒 Security Notice:</strong></p>
                    <p style="margin: 5px 0 0 0;">If you didn't request this password reset, please ignore this email and your password will remain unchanged.</p>
                </div>
                
                <div class="footer">
                    <p>This is an automated message, please do not reply.</p>
                    <p>&copy; 2024 Laundry Free. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
