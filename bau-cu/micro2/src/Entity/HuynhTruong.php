<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HuynhTruongRepository")
 */
class HuynhTruong
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong4;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong5;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhieuBau", mappedBy="huynhTruong")
     */
    private $cacPhieuBau;

    public function __construct()
    {
        $this->cacPhieuBau = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVong1(): ?int
    {
        return $this->vong1;
    }

    public function setVong1(?int $vong1): self
    {
        $this->vong1 = $vong1;

        return $this;
    }

    public function getVong2(): ?int
    {
        return $this->vong2;
    }

    public function setVong2(?int $vong2): self
    {
        $this->vong2 = $vong2;

        return $this;
    }

    public function getVong3(): ?int
    {
        return $this->vong3;
    }

    public function setVong3(?int $vong3): self
    {
        $this->vong3 = $vong3;

        return $this;
    }

    public function getVong4(): ?int
    {
        return $this->vong4;
    }

    public function setVong4(?int $vong4): self
    {
        $this->vong4 = $vong4;

        return $this;
    }

    public function getVong5(): ?int
    {
        return $this->vong5;
    }

    public function setVong5(?int $vong5): self
    {
        $this->vong5 = $vong5;

        return $this;
    }

    /**
     * @return Collection|PhieuBau[]
     */
    public function getCacPhieuBau(): Collection
    {
        return $this->cacPhieuBau;
    }

    public function addCacPhieuBau(PhieuBau $cacPhieuBau): self
    {
        if (!$this->cacPhieuBau->contains($cacPhieuBau)) {
            $this->cacPhieuBau[] = $cacPhieuBau;
            $cacPhieuBau->setHuynhTruong($this);
        }

        return $this;
    }

    public function removeCacPhieuBau(PhieuBau $cacPhieuBau): self
    {
        if ($this->cacPhieuBau->contains($cacPhieuBau)) {
            $this->cacPhieuBau->removeElement($cacPhieuBau);
            // set the owning side to null (unless already changed)
            if ($cacPhieuBau->getHuynhTruong() === $this) {
                $cacPhieuBau->setHuynhTruong(null);
            }
        }

        return $this;
    }
}
