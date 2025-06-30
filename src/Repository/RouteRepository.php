<?php


namespace App\Repository;
use App\Entity\RouteEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<RouteEntity>
 */
class RouteRepository extends ServiceEntityRepository
{
    private Connection $connection;
    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em)
    {
        parent::__construct($registry, RouteEntity::class);
        $this->connection = $this->getEntityManager()->getConnection();
        $this->em = $em;
    }
    public function getRoutesNearby(float $lng, float $lat, int $radiusMeters):array
    {
        $sql= <<<SQL
SELECT DISTINCT r.*
        FROM routes r
        JOIN points p ON r.id = p.route_id
        WHERE ST_DWithin(
            p.location::geography,
            ST_SetSRID(ST_MakePoint(:lng, :lat), 4326)::geography,
            :radius
        )
SQL;

        $stmt =$this->connection->prepare($sql);
        $stmt->bindValue('lng', $lng);
        $stmt->bindValue('lat', $lat);
        $stmt->bindValue('radius', $radiusMeters);

        $routes = $stmt->executeQuery()->fetchAllAssociative();
        $routeIds = array_column($routes, 'id');
        if (empty($routeIds)) {
            return [];
        }

        // Step 3: Get all points for those routes
        $sqlPoints = <<<SQL
            SELECT id, route_id, name, description, map_number,
                   ST_X(location) as lng,
                   ST_Y(location) as lat
            FROM points
            WHERE route_id IN (:routeIds)
        SQL;
        $points = $this->connection->executeQuery(
            $sqlPoints,
            ['routeIds' => $routeIds],
            ['routeIds' => Connection::PARAM_INT_ARRAY]
        )->fetchAllAssociative();


        // Step 4: Group points by route_id
        $pointsByRoute = [];
        foreach ($points as $point) {
            $pointsByRoute[$point['route_id']][] = $point;
        }

        // Step 5: Attach points to routes
        foreach ($routes as &$route) {
            $route['points'] = $pointsByRoute[$route['id']] ?? [];
        }

        return $routes;
    }




}
