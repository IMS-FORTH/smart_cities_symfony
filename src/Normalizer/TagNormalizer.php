<?php

namespace App\Normalizer;


use App\Entity\TagEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TagNormalizer implements NormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function normalize($data,$format=null,array $context = []):array
    {
        if (!$data instanceof TagEntity) {
            throw new \InvalidArgumentException(sprintf('The data must be an instance of %s', TagEntity::class));
        }
        return [
            'id' => $data->getId(),
            'value' => $data->getValue(),
            'url' => $this->urlGenerator->generate('tag_show', ['id' => $data->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'routes' => $data->getRoutes()->map(fn($r) => [
                'id' => $r->getId(),
                'url' => $this->urlGenerator->generate('route_show', ['id' => $r->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'name' => $r->getName(),
                'description' => $r->getDescription(),
                'points' => $r->getPoints()->map(fn($p) => [
                    'id' => $p->getId(),
                    'url' => $this->urlGenerator->generate('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'name' => $p->getName(),
                    'description' => $p->getDescription(),
                ])
            ])
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // TODO: Implement supportsNormalization() method.
        return $data instanceof TagEntity;
    }

    public function getSupportedTypes(?string $format): array
    {
        // TODO: Implement getSupportedTypes() method.
        return [TagEntity::class];
    }
}
