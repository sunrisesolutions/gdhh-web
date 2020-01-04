<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ViTriRepository")
 */
class ViTri
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhieuBau", mappedBy="viTri")
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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
            $cacPhieuBau->setViTri($this);
        }

        return $this;
    }

    public function removeCacPhieuBau(PhieuBau $cacPhieuBau): self
    {
        if ($this->cacPhieuBau->contains($cacPhieuBau)) {
            $this->cacPhieuBau->removeElement($cacPhieuBau);
            // set the owning side to null (unless already changed)
            if ($cacPhieuBau->getViTri() === $this) {
                $cacPhieuBau->setViTri(null);
            }
        }

        return $this;
    }
}
