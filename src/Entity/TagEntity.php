<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tags')]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $value;

    #[ORM\ManyToMany(targetEntity: RouteEntity::class, mappedBy: "tags")]
    private Collection $routes;

    public function getId(): int { return $this->id; }

    public function getValue(): string { return $this->value; }


    /** @return Collection<int, RouteEntity> */
    public function getRoutes(): Collection { return $this->routes; }
}
