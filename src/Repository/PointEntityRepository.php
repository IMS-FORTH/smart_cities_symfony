<?php

namespace App\Repository;

use App\Entity\PointEntity;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class PointEntityRepository extends ServiceEntityRepository
{
    private Connection $connection;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointEntity::class);
        $this->connection = $this->getEntityManager()->getConnection();
    }
    public function getLatitude(Uuid $pointId): ?float
    {
        $sql = 'SELECT ST_Y(location) as lat FROM points WHERE id = :id';
        $result = $this->connection->fetchAssociative($sql, ['id' => $pointId]);

        return $result ? (float) $result['lat'] : null;
    }

    public function getLongitude(Uuid $pointId): ?float
    {
        $sql = 'SELECT ST_X(location) as lng FROM points WHERE id = :id';
        $result = $this->connection->fetchAssociative($sql, ['id' => $pointId]);

        return $result ? (float) $result['lng'] : null;
    }

    public function getNearby(float $lat, float $lng, int $radiusMeters):array
    {
        $sql= <<<SQL
SELECT *, ST_Distance(location::geography, ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)::geography) as distance
        FROM points
        WHERE ST_DWithin(
            location::geography,
            ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)::geography,
            :radius
        )
        ORDER BY distance ASC
SQL;
        $stmt= $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('lng', $lng);
        $stmt->bindValue('lat',$lat);
        $stmt->bindValue('radius',$radiusMeters);

        return $stmt->executeQuery()->fetchAllAssociative();

    }
}
