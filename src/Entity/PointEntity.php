<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity]
#[ORM\Table(name: "points")]
class PointEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: RouteEntity::class, inversedBy: 'points')]
    #[ORM\JoinColumn(name: "route_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private RouteEntity $route;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $mapNumber = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $location = null;

    #[ORM\OneToMany(targetEntity: Bibliography::class, mappedBy: "point")]
    private Collection $bibliographies;
    public function getId(): Uuid { return $this->id; }

    public function getRoute(): RouteEntity { return $this->route; }
    public function setRoute(RouteEntity $route): self { $this->route = $route; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }

    public function getMapNumber(): ?int { return $this->mapNumber; }
    public function getLocation(): ?string { return $this->location; }
    /** @return Collection<int, Bibliography> */
    public function getBibliographies(): Collection { return $this->bibliographies; }

    public function getLatAttribute(): ?float
    {

        if (!$this->id) {
            return null;
        }

        $conn = \Doctrine\DBAL\DriverManager::getConnection(['url' => $_ENV['DATABASE_URL']]);
        return $conn->fetchOne('SELECT ST_Y(location) FROM points WHERE id = ?', [$this->id]);
    }
    public function getLngAttribute(): ?float
    {
        if (!$this->id) {
            return null;
        }

        $conn = \Doctrine\DBAL\DriverManager::getConnection(['url' => $_ENV['DATABASE_URL']]);
        return $conn->fetchOne('SELECT ST_X(location) FROM points WHERE id = ?', [$this->id]);
    }

    /*
    public function normalizePoint($options = false): array
    {
        $data=[
            'id' => $this->id,
            'route_id' => $this->route->getId(),
            'name' => $this->name,
            'description' => $this->description,
            'map_number' => $this->mapNumber,
            'lat' => $this->getLatAttribute(),
            'lng' => $this->getLngAttribute(),
        ];
        if($options === true){
            $data['bibliographies'] = $this->getBibliographies()->map(fn($b) => [
                'id' => $b->getId(),
                'text' => $b->getText(),
            ]);
        }
        return $data;
    }
    */


}
