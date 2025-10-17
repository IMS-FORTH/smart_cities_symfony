<?php


namespace App\Controller\Api;

use App\Entity\PointEntity;
use App\Normalizer\PointNormalizer;
use App\Repository\PointEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api/points')]
class PointEntityController extends AbstractController
{
    #[Route('/',name: 'points_list',methods: ['GET'])]
    public function index(EntityManagerInterface $em,PointNormalizer $normalizer): JsonResponse
    {
        $points = $em->getRepository(PointEntity::class)->findAll();
        $data = array_map(/**
         * @throws ExceptionInterface
         */ fn($p) => [$normalizer->normalize($p,context: ['with_bibliography'=>true])],$points);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/geojson',name: 'points_geojson',methods: ['GET'])]
    public function geojsonAll(EntityManagerInterface $em,PointEntityRepository $pointEntityRepository,PointNormalizer $normalizer): JsonResponse
    {
        $points = $em->getRepository(PointEntity::class)->findAll();
        $features = [];
        foreach ($points as $point) {
            $features[] = $normalizer->normalize($point,format: 'geojson');
        }
        return $this->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}/geojson',name: 'point_geojson',methods: ['GET'])]
    public function geojsonSingle(Uuid $id,PointEntityRepository $pointEntityRepository,PointNormalizer $normalizer): JsonResponse
    {
        $point = $pointEntityRepository->find($id);
        if (!$point) {
            return $this->json(['error' => 'Point not found'], 404);
        }
//        return $this->json($this->pointToFeature($point, $pointEntityRepository));
        return $this->json($normalizer->normalize($point,format: 'geojson'));
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

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}',name: 'point_show',methods: ['GET'])]
    public function show(Uuid $id,EntityManagerInterface $em,PointNormalizer $normalizer): JsonResponse
    {
        $point = $em->getRepository(PointEntity::class)->find($id);
        if (!$point) {
            return $this->json(['error' => 'Point not found'], 404);
        }
        return $this->json($normalizer->normalize($point,context: ['with_bibliography'=>true]));
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


}
