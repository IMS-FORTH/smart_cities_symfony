<?php

namespace App\Normalizer;


use App\Entity\RouteEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RouteNormalizer implements NormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof RouteEntity)
        {
            throw new \InvalidArgumentException(sprintf('The data must be an instance of %s', RouteEntity::class));
        }
        return [
            'id' => $data->getId(),
            'url' => $this->urlGenerator->generate('route_show', ['id' => $data->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $data->getName(),
            'description' => $data->getDescription(),
            'points' => $data->getPoints()->map(fn($p) => [
                'id' => $p->getId(),
                'url' => $this->urlGenerator->generate('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'name' => $p->getName(),
                'description' => $p->getDescription()
                ]),
            'tags' => $data->getTags()->map(fn($t) => [
                'id' => $t->getId(),
                'name' => $t->getName(),
                'url' => $this->urlGenerator->generate('tag_show', ['id' => $t->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]),
            ];

    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof RouteEntity;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [RouteEntity::class];
    }
}
