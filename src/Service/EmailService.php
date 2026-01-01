<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class EmailService
{
  public function __construct(
    private readonly MailerInterface $mailer,
    private readonly LoggerInterface $logger,
    private readonly Environment $twig
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
      $htmlContent = $this->twig->render('emails/verification.html.twig', [
        'verificationCode' => $verificationCode
      ]);

      $email = (new Email())
        ->from($_ENV['EMAIL_FROM'] ?? 'noreply@laundry-free.com')
        ->to($toEmail)
        ->subject('Verify Your Account - Laundry Free')
        ->html($htmlContent);

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
   * Send password reset code email to user
   * 
   * @param string $toEmail Recipient email address
   * @param string $resetCode 4-digit reset code
   * @return bool Success status
   */
  public function sendPasswordResetCode(string $toEmail, string $resetCode): bool
  {
    try {
      $htmlContent = $this->twig->render('emails/password_reset.html.twig', [
        'resetCode' => $resetCode
      ]);

      $email = (new Email())
        ->from($_ENV['EMAIL_FROM'] ?? 'noreply@laundry-free.com')
        ->to($toEmail)
        ->subject('Password Reset Code - Laundry Free')
        ->html($htmlContent);

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
}
