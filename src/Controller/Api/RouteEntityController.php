<?php

namespace App\Controller\Api;
use App\Entity\RouteEntity;
use App\Normalizer\PointNormalizer;
use App\Normalizer\RouteNormalizer;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/routes')]
class RouteEntityController extends AbstractController
{
    #[Route('/',name: 'routes_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em,RouteNormalizer $normalizer): JsonResponse
    {
        $routes = $em->getRepository(RouteEntity::class)->findAll();
        $data=array_map(/**
         * @throws ExceptionInterface
         */ fn($r) => [$normalizer->normalize($r)],$routes);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}/geojson',name: 'route_geojson',methods: ['GET'])]
    public function geoJson(Uuid $id,EntityManagerInterface $em,PointNormalizer $normalizer): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if(!$route){
            return $this->json(['error' => 'Route not found'], 404);
        }
        $features=[];
        $points=$route->getPoints()->toArray();
        foreach ($points as $point) {
            $features[] = $normalizer->normalize($point,format: 'geojson');
        }
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
        return $this->json($geojson);
    }


    #[Route('/nearby', name: 'routes_nearby', methods: ['GET'])]
    public function nearby(Request $request, RouteRepository $routeRepository): JsonResponse
    {
        $lat = (float) $request->query->get('lat');
        $lng = (float) $request->query->get('lng');
        $radius = (float) $request->query->get('radius', 1000);

        if (!$lat || !$lng) {
            return $this->json(['error' => 'lat and lng are required'], 400);
        }

        $routes = $routeRepository->getRoutesNearby($lng, $lat, $radius);

        return $this->json($routes);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}',name: 'route_show',methods: ['GET'])]
    public function show(Uuid $id,EntityManagerInterface $em,RouteNormalizer $normalizer): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Route not found'], 404);
        }
        return $this->json($normalizer->normalize($route));
    }
    #[Route('/{id}/points',name: 'route_points',methods: ['GET'])]
    public function getPointByRoute(Uuid $id,EntityManagerInterface $em,PointNormalizer $normalizer): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Route not found'], 404);
        }
        $points = $route->getPoints()->map(fn($p) => $normalizer->normalize($p));
        return $this->json($points);
    }
    #[Route('/{id}/tags',name: 'route_tags',methods: ['GET'])]
    public function getTagsByRoute(Uuid $id,EntityManagerInterface $em): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        $tags = $route->getTags()->map(fn($t) => [
            'id' => $t->getId(),
            'url'=>$this->generateUrl('tag_show', ['id' => $t->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $t->getName(),
        ]);
        return $this->json($tags);
    }
}
