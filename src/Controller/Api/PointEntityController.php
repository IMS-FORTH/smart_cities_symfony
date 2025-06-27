<?php


namespace App\Controller\Api;

use App\Entity\PointEntity;
use App\Repository\PointEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/points')]
class PointEntityController extends AbstractController
{
    #[Route('/',name: 'points_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $points = $em->getRepository(PointEntity::class)->findAll();
        $data = array_map(fn($p) => [
           'id'=>$p->getId(),
           'url'=>$this->generateUrl('point_show', ['id' => $p->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
           'name'=>$p->getName(),
           'description'=>$p->getDescription(),
           'mapNumber'=>$p->getMapNumber(),
            'lat'=>$p->getLatAttribute(),
            'lng'=>$p->getLngAttribute(),
            'bibliographies'=>$p->getBibliographies()->map(fn($b) => [
                'id' => $b->getId(),
                'text' => $b->getText(),
            ])
        ],$points);
        return $this->json($data);
    }
    #[Route('/geojson',name: 'geojson_all',methods: ['GET'])]
    public function geojsonAll(EntityManagerInterface $em,PointEntityRepository $pointEntityRepository): JsonResponse
    {
        $points = $em->getRepository(PointEntity::class)->findAll();
        $features = [];
        foreach ($points as $point) {
            $features[] = $this->pointToFeature($point, $pointEntityRepository);
        }
        return $this->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);

    }

    #[Route('/{id}/geojson',name: 'geojson_single',methods: ['GET'])]
    public function geojsonSingle(Uuid $id,PointEntityRepository $pointEntityRepository): JsonResponse
    {
        $point = $pointEntityRepository->find($id);
        if (!$point) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        return $this->json($this->pointToFeature($point, $pointEntityRepository));

    }

    #[Route('/nearby', name: 'points_nearby', methods: ['GET'])]
    public function getNearbyPoints(Request $request, PointEntityRepository $pointEntityRepository):JsonResponse
    {
        $lat =(float) $request->query->get('lat');
        $lng = (float)$request->query->get('lng');
        $radius =(float) $request->query->get('radius',1000);
        if (!$lat || !$lng) {
            return $this->json(['error' => 'lat and lng are required'], 400);
        }
        $points = $pointEntityRepository->getNearby($lat, $lng, $radius);
        return $this->json($points);
    }

    #[Route('/{id}',name: 'point_show',methods: ['GET'])]
    public function show(Uuid $id,EntityManagerInterface $em): JsonResponse
    {
        $point = $em->getRepository(PointEntity::class)->find($id);
        if (!$point) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        return $this->json([
            'id' => $point->getId(),
            'name' => $point->getName(),
            'description' => $point->getDescription(),
            'mapNumber' => $point->getMapNumber(),
            'lat'=>$point->getLatAttribute(),
            'lng'=>$point->getLngAttribute(),
            'bibliographies'=>$point->getBibliographies()->map(fn($b) => [
                'id' => $b->getId(),
                'text' => $b->getText(),
            ])
        ]);
    }

    #[Route('/{id}/bibliographies',name: 'point_bibliography',methods: ['GET'])]
    public function getBibliographyByPoint(Uuid $id,EntityManagerInterface $em): JsonResponse
    {
        $point = $em->getRepository(PointEntity::class)->find($id);
        if (!$point) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        $bibliography = $point->getBibliographies()->map(fn($b) => [
            'id' => $b->getId(),
            'text' => $b->getText(),
        ]);
        return $this->json($bibliography);
    }


    private function pointToFeature(PointEntity $point,PointEntityRepository $pointEntityRepository): array
    {
        $lat = $pointEntityRepository->getLatitude($point->getId());
        $lng = $pointEntityRepository->getLongitude($point->getId());

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$lng, $lat],
            ],
            'properties' => [
                'id' => $point->getId(),
                'name' => $point->getName(),
                'description' => $point->getDescription(),
                'map_number' => $point->getMapNumber(),
            ]
        ];
    }

}
