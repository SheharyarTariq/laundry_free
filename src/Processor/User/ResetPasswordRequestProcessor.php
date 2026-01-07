<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\User\ResetPassword;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AutoconfigureTag('api_platform.state_processor')]
class ResetPasswordRequestProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly EmailService $emailService,
    private readonly UserRepository $userRepository
  ) {}

  /**
   * @param ResetPassword $data
   */
  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
  {
    $user = $this->userRepository->findOneBy(['email' => $data->email]);

    if (!$user) {
      return new JsonResponse([
        'success' => true,
        'message' => 'If the email exists, a password reset code has been sent',
      ]);
    }

    $resetCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    $user->verificationCode = $resetCode;

    $user->verificationCodeExpiry = time() + (15 * 60);
    $user->passwordResetRequestedAt = new \DateTimeImmutable();

    $this->entityManager->flush();

    $emailSent = $this->emailService->sendPasswordResetCode($user->email, $resetCode);

    if (!$emailSent) {
      throw new \RuntimeException('Failed to send password reset email. Please try again.');
    }

    return new JsonResponse([
      'success' => true,
      'message' => 'Password reset code has been sent to your email',
    ]);
  }
}
