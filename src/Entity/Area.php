<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Provider\Area\AreaPostcodesProvider;

#[ApiResource(
    security: "is_granted('ROLE_ADMIN')",
    operations: [
        new Post(
            normalizationContext: [
                'groups' => ['Area:V$Create']
            ],
            denormalizationContext: [
                'groups' => ['Area:W$Create']
            ],
        ),

        new Delete(),

        new Get(
            normalizationContext: [
                'groups' => ['Area:V$Detail']
            ],
        ),

        new GetCollection(
            normalizationContext: [
                'groups' => ['Area:V$List']
            ],
        ),

        new GetCollection(
            uriTemplate: '/areas/{id}/postcodes',
            provider: AreaPostcodesProvider::class,
            normalizationContext: [
                'groups' => ['Postcode:V$List']
            ],
        ),

        new Put(
            normalizationContext: [
                'groups' => ['Area:V$Update']
            ],
            denormalizationContext: [
                'groups' => ['Area:W$Update']
            ],
        )
    ]
)]

#[UniqueEntity(fields: ['area'], message: 'This area name already exists.')]
#[ORM\Entity]
#[ORM\Table(name: '`area`')]
#[ORM\UniqueConstraint(name: 'area__area', fields: ['area'])] //Todo: here is it correct: area__area className_fieldName
#[ORM\HasLifecycleCallbacks]
class Area
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'Area:V$Create',
        'Area:V$Detail',
        'Area:V$List',
        'Area:V$Update'
    ])]
    public Uuid $id;

    #[ORM\Column(length: 255)]
    #[Groups([
        'Area:V$Create',
        'Area:V$Detail',
        'Area:V$List',
        'Area:V$Update',
        'Area:W$Create',
        'Area:W$Update'
    ])]
    #[Assert\NotBlank(message: 'Area name is required')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Area name must be at least {{ limit }} characters long',
        maxMessage: 'Area name cannot be longer than {{ limit }} characters'
    )]
    public string $area;

    /**
     * @var Collection<int, Postcode>
     */
    #[ORM\OneToMany(targetEntity: Postcode::class, mappedBy: 'area', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups([
        'Area:V$Detail', 
        'Area:V$Postcodes'
    ])]
    private Collection $postcodes;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
        'Area:V$Create',
        'Area:V$Detail',
        'Area:V$List'
    ])]
    public \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
        'Area:V$Create',
        'Area:V$Detail',
        'Area:V$List'
    ])]
    public \DateTime $updatedAt;

    public function __construct()
    {
        $this->postcodes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection<int, Postcode>
     */
    public function getPostcodes(): Collection
    {
        return $this->postcodes;
    }

    public function addPostcode(Postcode $postcode): static
    {
        if (!$this->postcodes->contains($postcode)) {
            $this->postcodes->add($postcode);
            $postcode->setArea($this);
        }

        return $this;
    }

    public function removePostcode(Postcode $postcode): static
    {
        if ($this->postcodes->removeElement($postcode)) {
            // set the owning side to null (unless already changed)
            if ($postcode->getArea() === $this) {
                $postcode->setArea(null);
            }
        }

        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
