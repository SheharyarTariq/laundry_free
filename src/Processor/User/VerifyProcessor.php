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
use App\Repository\UserRepository;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('api_platform.state_processor')]
class VerifyProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly RequestStack $requestStack,
    private readonly UserRepository $userRepository
  ) {}

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
  {
    $request = $this->requestStack->getCurrentRequest();
    $payload = json_decode($request->getContent(), true);

    $email = $payload['email'] ?? null;
    $code = $payload['code'] ?? null;

    Assert::notEmpty($email, 'Email is required');
    Assert::notEmpty($code, 'Code is required');

    $user = $this->userRepository->findOneBy(['email' => $email]);

    Assert::notNull($user, 'User not found');

    Assert::false($user->isActive, 'Account is already verified');

    Assert::same($user->verificationCode, $code, 'Invalid verification code');

    Assert::greaterThan($user->verificationCodeExpiry, time(), 'Verification code has expired. Please request a new one.');

    $user->isActive = true;
    $user->verificationCode = '';
    $user->verificationCodeExpiry = 0;
    $user->emailVerifiedAt = new \DateTimeImmutable();

    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'message' => 'Account verified successfully',
      'isActive' => true
    ]);
  }
}
