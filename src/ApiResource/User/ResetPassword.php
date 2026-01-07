<?php

namespace App\ApiResource\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Processor\User\ChangePasswordProcessor;
use App\Processor\User\ResetPasswordConfirmProcessor;
use App\Processor\User\ResetPasswordRequestProcessor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  operations: [
    new Post(
      uriTemplate: '/reset-password/request',
      processor: ResetPasswordRequestProcessor::class,
      validationContext: ['groups' => ['Default', 'Valid(ResetPassword:Request)']],
      denormalizationContext: [
        'groups' => [
          'ResetPassword:Request',
        ],
      ],
      status: Response::HTTP_OK,
    ),
    new Post(
      uriTemplate: '/reset-password/confirm',
      processor: ResetPasswordConfirmProcessor::class,
      validationContext: ['groups' => ['Default', 'Valid(ResetPassword:Confirm)']],
      denormalizationContext: [
        'groups' => [
          'ResetPassword:Confirm',
        ],
      ],
      status: Response::HTTP_OK,
    ),
    new Post(
      uriTemplate: '/change-password',
      processor: ChangePasswordProcessor::class,
      validationContext: ['groups' => ['Default', 'Valid(ResetPassword:Change)']],
      denormalizationContext: [
        'groups' => [
          'ResetPassword:W$ChangePassword',
        ],
      ],
      status: Response::HTTP_OK,
    ),
  ]
)]
class ResetPassword
{
  #[Assert\NotBlank(groups: ['Valid(ResetPassword:Request)', 'Valid(ResetPassword:Confirm)'])]
  #[Groups(['ResetPassword:Request', 'ResetPassword:Confirm'])]
  public string $email;

  #[Assert\NotBlank(groups: ['Valid(ResetPassword:Confirm)'])]
  #[Groups(['ResetPassword:Confirm'])]
  public string $token;

  #[Assert\NotBlank(groups: ['Valid(ResetPassword:Reset)', 'Valid(ResetPassword:Confirm)', 'Valid(ResetPassword:Change)'])]
  #[Groups(['ResetPassword:W$ChangePassword', 'ResetPassword:Confirm'])]
  public string $newPassword;

  #[Assert\NotBlank(groups: ['Valid(ResetPassword:Change)'])]
  #[Groups(['ResetPassword:W$ChangePassword'])]
  public string $currentPassword;
}
