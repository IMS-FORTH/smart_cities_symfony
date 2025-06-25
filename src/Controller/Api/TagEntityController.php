<?php

namespace App\Controller\Api;


use App\Entity\TagEntity;
use App\Repository\TagEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/tags')]
class TagEntityController extends AbstractController
{

    #[Route('/',name: 'tags_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
       $tags = $em->getRepository(TagEntity::class)->findAll();
       $data = array_map(fn($t) => [
           'id'=>$t->getId(),
           'url'=>$this->generateUrl('tag_show', ['id' => $t->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
           'value'=>$t->getValue(),
           'routes'=>$t->getRoutes()->map(fn($r) => [
               'id'=>$r->getId(),
               'url'=>$this->generateUrl('route_show', ['id' => $r->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
               'name'=>$r->getName(),
               'description'=>$r->getDescription(),
               'points'=>$r->getPoints()->map(fn($p) => [
                   'id'=>$p->getId(),
                   'url'=>$this->generateUrl('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                   'name'=>$p->getName(),
                   'description'=>$p->getDescription(),
                   'mapNumber'=>$p->getMapNumber(),
               ])->toArray()
           ])->toArray()
       ]
       ,$tags);
       return $this->json($data);
    }
    #[Route('/nearby', methods: ['GET'])]
    public function getNearbyTags(Request $request, TagEntityRepository $tagRepository): JsonResponse
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
    #[Route('/{id}',name: 'tag_show',methods: ['GET'])]
    public function show(int $id,EntityManagerInterface $em): JsonResponse
    {
        $tag = $em->getRepository(TagEntity::class)->find($id);
        if (!$tag) {
            return $this->json(['error' => 'Tag not found'], 404);
        }
        return $this->json([
            'id' => $tag->getId(),
            'value' => $tag->getValue(),
            'routes'=>$tag->getRoutes()->map(fn($r) => [
                'id'=>$r->getId(),
                'url'=>$this->generateUrl('route_show', ['id' => $r->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'name'=>$r->getName(),
                'description'=>$r->getDescription(),
                'points'=>$r->getPoints()->map(fn($p) => [
                    'id'=>$p->getId(),
                    'url'=>$this->generateUrl('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'name'=>$p->getName(),
                    'description'=>$p->getDescription(),
                ])
            ])
        ]);
    }

    #[Route('/{id}/routes',name: 'tag_routes',methods: ['GET'])]
    public function getRoutesByTag(int $id,EntityManagerInterface $em): JsonResponse
    {
        $tag = $em->getRepository(TagEntity::class)->find($id);
        if (!$tag) {
            return $this->json(['error' => 'Tag not found'], 404);
        }
        $routes = $tag->getRoutes()->map(fn($r) => [
            'id'=>$r->getId(),
            'url'=>$this->generateUrl('route_show', ['id' => $r->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'name'=>$r->getName(),
            'description'=>$r->getDescription(),
            'points'=>$r->getPoints()->map(fn($p) => [
                'id'=>$p->getId(),
                'url'=>$this->generateUrl('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'name'=>$p->getName(),
                'description'=>$p->getDescription(),
            ])
        ]);
        return $this->json($routes);
    }
}
