<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CuTriRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="search_idx", columns={"pin"})})
 * @ORM\HasLifecycleCallbacks()
 */
class CuTri
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getCacViTriChuaBau($dsViTri = [])
    {
        $c = Criteria::create();
        /** @var ViTri $viTri */
        foreach ($dsViTri as $vtKey => $viTri) {
            /** @var PhieuBau $pb */
            foreach ($this->cacPhienBau as $pb) {
                if ($pb->getViTri() === $viTri) {
                    unset($dsViTri[$vtKey]);
                }
            }
        }
        return $dsViTri;
    }

    public function __clone()
    {
        $this->cacPhienBau = new ArrayCollection();
    }

    public function findPhieuBauChoTruong(HuynhTruong $huynhTruong)
    {
        /** @var PhieuBau $pb */
        foreach ($this->cacPhienBau as $pb) {
            if ($pb->getHuynhTruong() === $huynhTruong) {
                return $huynhTruong;
            }
        }
        return null;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateData(EventArgs $event, NhiemKy $nhiemKy = null)
    {
        if ($event instanceof LifecycleEventArgs) {
            if (empty($nhiemKy)) {
                $nhiemKy = $event->getEntityManager()->getRepository(NhiemKy::class)->findOneByEnabled(true);
            }
        }

        if ($this->submitted) {
            /** @var PhieuBau $pb */
            foreach ($this->cacPhienBau as $pb) {
                $truong = $pb->getHuynhTruong();
                $truong->updateVoteCount($nhiemKy);
            }
        }
    }

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $pin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhieuBau", mappedBy="cuTri", cascade={"persist", "merge"})
     */
    private $cacPhienBau;

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $pinFormatted;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $submitted = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

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

    public function getPinFormatted(): ?string
    {
        return $this->pinFormatted;
    }

    public function setPinFormatted(string $pinFormatted): self
    {
        $this->pinFormatted = $pinFormatted;

        return $this;
    }

    public function getSubmitted(): ?bool
    {
        return $this->submitted;
    }

    public function setSubmitted(bool $submitted): self
    {
        $this->submitted = $submitted;

        return $this;
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
}
