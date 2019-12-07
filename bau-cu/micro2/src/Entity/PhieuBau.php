<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhieuBauRepository")
 */
class PhieuBau
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CuTri", inversedBy="cacPhienBau")
     */
    private $cuTri;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HuynhTruong", inversedBy="cacPhieuBau")
     */
    private $huynhTruong;

    /**
     * @ORM\Column(type="integer")
     */
    private $vong;

    public function getCuTri(): ?CuTri
    {
        return $this->cuTri;
    }

    public function setCuTri(?CuTri $cuTri): self
    {
        $this->cuTri = $cuTri;

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

    public function setVong(int $vong): self
    {
        $this->vong = $vong;

        return $this;
    }


}
