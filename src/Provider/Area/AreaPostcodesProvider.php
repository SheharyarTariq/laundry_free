<?php

namespace App\Provider\Area;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Area;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AreaPostcodesProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $areaId = $uriVariables['id'] ?? null;

        if (!$areaId) {
            throw new NotFoundHttpException('Area ID is required');
        }

        $area = $this->entityManager->getRepository(Area::class)->find($areaId);

        if (!$area) {
            throw new NotFoundHttpException('Area not found');
        }

        // Return the postcodes collection
        return $area->getPostcodes()->toArray();
    }
}
