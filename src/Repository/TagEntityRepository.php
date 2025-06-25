<?php

namespace App\Repository;



use App\Entity\TagEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class TagEntityRepository extends ServiceEntityRepository
{

    private Connection $conn;
    private EntityManagerInterface $em;


    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em)
    {
        parent::__construct($registry, TagEntity::class);
        $this->conn = $this->getEntityManager()->getConnection();
        $this->em = $em;
    }
    public function findNearbyTags(float $lng, float $lat, int $radiusMeters): array
    {
        $sql = <<<SQL
        SELECT DISTINCT t.*
        FROM tags t
        JOIN route_tag rt ON rt.tag_id = t.id
        JOIN routes r ON r.id = rt.route_id
        JOIN points p ON p.route_id = r.id
        WHERE ST_DWithin(
            p.location::geography,
            ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)::geography,
            :radius
        )
    SQL;


        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue('lng', $lng);
        $stmt->bindValue('lat', $lat);
        $stmt->bindValue('radius', $radiusMeters);

        return $stmt->executeQuery()->fetchAllAssociative();
    }

}
