<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity]
#[ORM\Table(name: 'tags')]
class TagEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $value;


    #[ORM\ManyToMany(targetEntity: RouteEntity::class, mappedBy: "tags")]
    private Collection $routes;

    public function __construct()
    {
        $this->routes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): Uuid { return $this->id; }

    public function getValue(): string { return $this->value; }


    /** @return Collection<int, RouteEntity> */
    public function getRoutes(): Collection { return $this->routes; }
}
