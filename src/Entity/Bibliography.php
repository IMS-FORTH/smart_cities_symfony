<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PointEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'bibliographies')]
class Bibliography
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: PointEntity::class, inversedBy: 'bibliographies')]
    #[ORM\JoinColumn(name: "point_id", referencedColumnName: "id")]
    private PointEntity $point;

    #[ORM\Column(type: "text", length: 255)]
    private string $text;

    public function getId():Uuid
    {
        return $this->id;
    }
    public function getPoint(): PointEntity
    {
        return $this->point;
    }

    public function getText():string
    {
        return $this->text;
    }


}
