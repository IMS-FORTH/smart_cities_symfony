<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'routes')]
class RouteEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;
    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", length: 255)]
    private string $description;


    #[ORM\OneToMany(targetEntity: PointEntity::class, mappedBy: "route")]
    private Collection $points;

    #[ORM\ManyToMany(targetEntity: TagEntity::class, inversedBy: 'routes')]
    #[ORM\JoinTable(
        name: "route_tag",
        joinColumns: [new ORM\JoinColumn(name: "route_id", referencedColumnName: "id", onDelete: "CASCADE")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "tag_id", referencedColumnName: "id", onDelete: "CASCADE")]
    )]
    private Collection $tags;

    public function __construct()
    {
        $this->points = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /** @return Collection<int, TagEntity> */
    public function getTags(): Collection { return $this->tags; }
    public function getId(): Uuid { return $this->id; }

    public function getName(): string { return $this->name; }

    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }

    /**@return Collection<int, PointEntity>  */
    public function getPoints(): Collection { return $this->points; }
}
