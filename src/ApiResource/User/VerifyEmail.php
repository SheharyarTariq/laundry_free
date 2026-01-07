<?php

namespace App\ApiResource\User;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Processor\User\VerifyProcessor;
use App\Processor\User\ResendVerificationProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
  operations: [
    new Post(
      uriTemplate: '/verify',
      processor: VerifyProcessor::class,
      validate: false,
      output: false,
      normalizationContext: [
        'groups' => ['VerifyEmail:V$Verify']
      ],
      denormalizationContext: [
        'groups' => ['VerifyEmail:W$Verify']
      ],
    ),
    new Post(
      uriTemplate: '/resend-verification',
      processor: ResendVerificationProcessor::class,
      validate: false,
      output: false,
      normalizationContext: [
        'groups' => ['VerifyEmail:V$Resend']
      ],
      denormalizationContext: [
        'groups' => ['VerifyEmail:W$Resend']
      ],
    ),
  ]
)]

class VerifyEmail
{
  #[Assert\Email]
  #[Assert\NotBlank]
  #[Groups([
    'VerifyEmail:W$Verify',
    'VerifyEmail:W$Resend',
  ])]
  public string $email;

  #[Assert\NotBlank(groups: ['VerifyEmail:Verify'])]
  #[Assert\Length(
    min: 4,
    max: 4,
    exactMessage: 'Verification code must be exactly {{ limit }} digits'
  )]
  #[Groups([
    'VerifyEmail:W$Verify',
  ])]
  public ?string $code = null;
}
