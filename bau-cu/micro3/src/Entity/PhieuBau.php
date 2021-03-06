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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CuTri", inversedBy="cacPhienBau")
     */
    private $cuTri;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HuynhTruong", inversedBy="cacPhieuBau", cascade={"persist", "merge"})
     */
    private $huynhTruong;

    /**
     * @ORM\Column(type="integer")
     */
    private $vong;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongphu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ViTri", inversedBy="cacPhieuBau")
     */
    private $viTri;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVongphu(): ?int
    {
        return $this->vongphu;
    }

    public function setVongphu(?int $vongphu): self
    {
        $this->vongphu = $vongphu;

        return $this;
    }

    public function getViTri(): ?ViTri
    {
        return $this->viTri;
    }

    public function setViTri(?ViTri $viTri): self
    {
        $this->viTri = $viTri;

        return $this;
    }
}
