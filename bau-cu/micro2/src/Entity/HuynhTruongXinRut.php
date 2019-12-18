<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HuynhTruongXinRutRepository")
 */
class HuynhTruongXinRut
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\HuynhTruong", inversedBy="huynhTruongXinRut", cascade={"persist", "remove"})
     */
    private $huynhTruong;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getHuynhTruong(): ?HuynhTruong
    {
        return $this->huynhTruong;
    }

    public function setHuynhTruong(?HuynhTruong $huynhTruong): self
    {
        $this->huynhTruong = $huynhTruong;

        return $this;
    }

    public function getVong(): ?int
    {
        return $this->vong;
    }

    public function setVong(?int $vong): self
    {
        $this->vong = $vong;

        return $this;
    }
}
