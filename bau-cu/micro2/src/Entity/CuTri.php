<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CuTriRepository")
 */
class CuTri
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
    private $pin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhieuBau", mappedBy="cuTri")
     */
    private $cacPhienBau;

    public function __construct()
    {
        $this->cacPhienBau = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function setPin(string $pin): self
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * @return Collection|PhieuBau[]
     */
    public function getCacPhienBau(): Collection
    {
        return $this->cacPhienBau;
    }

    public function addCacPhienBau(PhieuBau $cacPhienBau): self
    {
        if (!$this->cacPhienBau->contains($cacPhienBau)) {
            $this->cacPhienBau[] = $cacPhienBau;
            $cacPhienBau->setCuTri($this);
        }

        return $this;
    }

    public function removeCacPhienBau(PhieuBau $cacPhienBau): self
    {
        if ($this->cacPhienBau->contains($cacPhienBau)) {
            $this->cacPhienBau->removeElement($cacPhienBau);
            // set the owning side to null (unless already changed)
            if ($cacPhienBau->getCuTri() === $this) {
                $cacPhienBau->setCuTri(null);
            }
        }

        return $this;
    }
}
