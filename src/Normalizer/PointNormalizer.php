<?php

namespace App\Normalizer;

use App\Entity\PointEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PointNormalizer implements NormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof PointEntity) {
            throw new \InvalidArgumentException(sprintf('The data must be an instance of %s', PointEntity::class));
        }
        if($format === 'geojson'){
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$data->getLngAttribute(), $data->getLatAttribute()],
                ],
                'properties' => [
                    'id' => $data->getId(),
                    'url' => $this->urlGenerator->generate('point_show', ['id' => $data->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'route_id' => $data->getRoute()->getId(),
                    'name' => $data->getName(),
                    'description' => $data->getDescription(),
                    'mapNumber' => $data->getMapNumber(),

                ]
            ];
        }
        $objectToNormalize=[
            'id' => $data->getId(),
            'url' => $this->urlGenerator->generate('point_show', ['id' => $data->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'route_id' => $data->getRoute()->getId(),
            'name' => $data->getName(),
            'description' => $data->getDescription(),
            'map_number' => $data->getMapNumber(),
            'lat' => $data->getLatAttribute(),
            'lng' => $data->getLngAttribute(),
        ];
        if($context['with_bibliography'] ?? false)
        {
           $objectToNormalize['bibliographies'] = $data->getBibliographies()->map(fn($b) => [
               'id' => $b->getId(),
               'text' => $b->getText(),
           ]);
        }
        return $objectToNormalize;

    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // TODO: Implement supportsNormalization() method.
        return $data instanceof PointEntity;
    }

    public function getSupportedTypes(?string $format): array
    {
        // TODO: Implement getSupportedTypes() method.
        return [PointEntity::class];
    }
}
