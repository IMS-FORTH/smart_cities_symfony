<?php

namespace App\Controller\Api;
use App\Entity\RouteEntity;
use App\Repository\RouteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/routes')]
class RouteEntityController extends AbstractController
{
    #[Route('/',name: 'routes_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $routes = $em->getRepository(RouteEntity::class)->findAll();
        $data = array_map(fn($r) => [
            'id' => $r->getId(),
            'url' => $this->generateUrl('route_show', ['id' => $r->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $r->getName(),
            'description' => $r->getDescription(),
            'points' => array_map(function ($point) {
                return [
                    'id' => $point->getId(),
                    'name' => $point->getName(),
                    'description' => $point->getDescription(),
                    'mapNumber' => $point->getMapNumber(),
                    'lat'=>$point->getLatAttribute(),
                    'lng'=>$point->getLngAttribute(),
                ];
            }, $r->getPoints()->toArray()),
            'tags' => array_map(function ($tag) {
                return [
                    'id' => $tag->getId(),
                    'value' => $tag->getValue(),
                ];
            }, $r->getTags()->toArray()),
        ], $routes);
        return $this->json($data);
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
    #[Route('/{id}',name: 'route_show',methods: ['GET'])]
    public function show(int $id,EntityManagerInterface $em): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Route not found'], 404);
        }
        return $this->json([
            'id' => $route->getId(),
            'name' => $route->getName(),
            'description' => $route->getDescription(),
            'points' => array_map(function ($point) {
                return [
                    'id' => $point->getId(),
                    'name' => $point->getName(),
                    'description' => $point->getDescription(),
                    'mapNumber' => $point->getMapNumber(),
                    'lat'=>$point->getLatAttribute(),
                    'lng'=>$point->getLngAttribute(),
                ];
            }, $route->getPoints()->toArray()),
            'tags' => array_map(function ($tag) {
                return [
                    'id' => $tag->getId(),
                    'value' => $tag->getValue(),
                ];
            }, $route->getTags()->toArray())
        ]);
    }
    #[Route('/{id}/points',name: 'route_points',methods: ['GET'])]
    public function getPointByRoute(int $id,EntityManagerInterface $em): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Route not found'], 404);
        }
        $points = $route->getPoints()->map(fn($p) => [
            'id' => $p->getId(),
            'url'=>$this->generateUrl('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $p->getName(),
            'description' => $p->getDescription(),
            'mapNumber' => $p->getMapNumber(),
            'lat'=>$p->getLatAttribute(),
            'lng'=>$p->getLngAttribute(),
        ]);
        return $this->json($points);
    }
    #[Route('/{id}/tags',name: 'point_tags',methods: ['GET'])]
    public function getTagsByPoint(int $id,EntityManagerInterface $em): JsonResponse
    {
        $route = $em->getRepository(RouteEntity::class)->find($id);
        if (!$route) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        $tags = $route->getTags()->map(fn($t) => [
            'id' => $t->getId(),
            'value' => $t->getValue(),
        ]);
        return $this->json($tags);
    }
}
