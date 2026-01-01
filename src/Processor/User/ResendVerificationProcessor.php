<?php

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\EmailService;
use App\Repository\UserRepository;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class ResendVerificationProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly EmailService $emailService,
    private readonly RequestStack $requestStack,
    private readonly UserRepository $userRepository
  ) {}

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
  {
    $request = $this->requestStack->getCurrentRequest();
    $payload = json_decode($request->getContent(), true);

    $email = $payload['email'] ?? null;
    Assert::notEmpty($email, 'Email is required');

    $user = $this->userRepository->findOneBy(['email' => $email]);

    if (!$user) {
      throw new NotFoundHttpException('User not found');
    }

    if ($user->isActive) {
      return new JsonResponse([
        'success' => false,
        'message' => 'Account is already verified'
      ]);
    }

    $verificationCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    $user->verificationCode = $verificationCode;

    $user->verificationCodeExpiry = time() + (15 * 60);

    $this->entityManager->flush();

    $emailSent = $this->emailService->sendVerificationCode($user->email, $verificationCode);

    if (!$emailSent) {
      throw new \RuntimeException('Failed to send verification email. Please try again.');
    }

    // Track when verification email was resent
    $user->emailVerificationSentAt = new \DateTimeImmutable();
    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'message' => 'Verification code has been resent to your email'
    ]);
  }
}
