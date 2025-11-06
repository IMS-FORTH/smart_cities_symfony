<?php

namespace App\Controller\Api;


use App\Entity\TagEntity;
use App\Normalizer\RouteNormalizer;
use App\Normalizer\TagNormalizer;
use App\Repository\TagEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/tags')]
class TagEntityController extends AbstractController
{

    #[Route('/',name: 'tags_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em,TagNormalizer $normalizer): JsonResponse
    {
       $tags = $em->getRepository(TagEntity::class)->findAll();
        $data = array_map(/**
         * @throws ExceptionInterface
         */ fn($t) => $normalizer->normalize($t),$tags);
       return $this->json($data);
    }
    #[Route('/nearby',name: 'tags_nearby' ,methods: ['GET'])]
    public function getNearbyTags(Request $request, TagEntityRepository $tagRepository,TagNormalizer $normalizer): JsonResponse
    {
        $lat = (float) $request->query->get('lat');
        $lng = (float) $request->query->get('lng');
        $radius = (int) $request->query->get('radius', 1000); // default: 1000 meters

        if (!$lat || !$lng) {
            return $this->json(['error' => 'Missing lat/lng'], 400);
        }

        $tags = $tagRepository->findNearbyTags($lng, $lat, $radius);
        return $this->json($tags);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}',name: 'tag_show',methods: ['GET'])]
    public function show(Uuid $id,EntityManagerInterface $em,TagNormalizer $normalizer): JsonResponse
    {
        $tag = $em->getRepository(TagEntity::class)->find($id);
        if (!$tag) {
            return $this->json(['error' => 'Tag not found'], 404);
        }
        $data = $normalizer->normalize($tag);
        return $this->json($data);
    }

    #[Route('/{id}/routes',name: 'tag_routes',methods: ['GET'])]
    public function getRoutesByTag(Uuid $id,EntityManagerInterface $em,RouteNormalizer $normalizer): JsonResponse
    {
        $tag = $em->getRepository(TagEntity::class)->find($id);
        if (!$tag) {
            return $this->json(['error' => 'Tag not found'], 404);
        }
        $routes = $tag->getRoutes()->map(fn($r) => $normalizer->normalize($r));
        return $this->json($routes);
    }
}
