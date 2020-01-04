<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NhiemKyRepository")
 */
class NhiemKy
{
    const REQUIRED_VOTES_VONG_1 = 10;

    public function dangBauCu()
    {
        return ($this->vong1 || $this->vong2 || $this->vong3 || $this->vong4 || $this->vong5 || $this->vongxdt1 || $this->vongxdt2 || $this->vongxdt3 || $this->vongxdpNoi || $this->vongxdpNgoai);
    }

    public function getRequiredVotes($vong = null): int
    {
        if (empty($vong)) {
            return self::REQUIRED_VOTES_VONG_1;
        }

        return $this->{'requiredVotesVong'.$vong};
    }

    public function isVongPhu()
    {
        if ($this->vong1phu || $this->vong2phu || $this->vong3phu) {
            return true;
        }
        return false;
    }

    public function getVongHienTai()
    {
        if ($this->vong1) {
            return 1;
        }
        if ($this->vong2) {
            return 2;
        }
        if ($this->vong3) {
            return 3;
        }
        if ($this->vongxdt1) {
            return 'xdt1';
        }
        if ($this->vongxdt2) {
            return 'xdt2';
        }
        if ($this->vongxdt3) {
            return 'xdt3';
        }
        if ($this->vongxdpNoi) {
            return 'xdpNoi';
        }
        if ($this->vongxdpNgoai) {
            return 'xdpNgoai';
        }
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVong1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVong2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVong3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong1;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong3;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong4;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong5;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVong1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVong2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVong3;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong1phu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong2phu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vong3phu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $viTri;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xdtVong1SoCuTri;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xdtVong2SoCuTri;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xdtVong3SoCuTri;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVongxdt1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVongxdt2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVongxdt3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVongxdt1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVongxdt2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVongxdt3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt1phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt2phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt3phu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdt1;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdt2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdt3;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdpNoi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdpNgoai;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVongxdpNoi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $requiredVotesVongxdpNgoai;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVongxdpNoi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $topVongxdpNgoai;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdpNoiphu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vongxdpNgoaiphu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequiredVotesVong1(): ?int
    {
        return $this->requiredVotesVong1;
    }

    public function setRequiredVotesVong1(?int $requiredVotesVong1): self
    {
        $this->requiredVotesVong1 = $requiredVotesVong1;

        return $this;
    }

    public function getRequiredVotesVong2(): ?int
    {
        return $this->requiredVotesVong2;
    }

    public function setRequiredVotesVong2(?int $requiredVotesVong2): self
    {
        $this->requiredVotesVong2 = $requiredVotesVong2;

        return $this;
    }

    public function getRequiredVotesVong3(): ?int
    {
        return $this->requiredVotesVong3;
    }

    public function setRequiredVotesVong3(?int $requiredVotesVong3): self
    {
        $this->requiredVotesVong3 = $requiredVotesVong3;

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

    public function getVong1(): ?bool
    {
        return $this->vong1;
    }

    public function setVong1(?bool $vong1): self
    {
        $this->vong1 = $vong1;

        return $this;
    }

    public function getVong2(): ?bool
    {
        return $this->vong2;
    }

    public function setVong2(?bool $vong2): self
    {
        $this->vong2 = $vong2;

        return $this;
    }

    public function getVong3(): ?bool
    {
        return $this->vong3;
    }

    public function setVong3(?bool $vong3): self
    {
        $this->vong3 = $vong3;

        return $this;
    }

    public function getVong4(): ?bool
    {
        return $this->vong4;
    }

    public function setVong4(?bool $vong4): self
    {
        $this->vong4 = $vong4;

        return $this;
    }

    public function getVong5(): ?bool
    {
        return $this->vong5;
    }

    public function setVong5(?bool $vong5): self
    {
        $this->vong5 = $vong5;

        return $this;
    }

    public function getTopVong1(): ?int
    {
        return $this->topVong1;
    }

    public function setTopVong1(?int $topVong1): self
    {
        $this->topVong1 = $topVong1;

        return $this;
    }

    public function getTopVong2(): ?int
    {
        return $this->topVong2;
    }

    public function setTopVong2(?int $topVong2): self
    {
        $this->topVong2 = $topVong2;

        return $this;
    }

    public function getTopVong3(): ?int
    {
        return $this->topVong3;
    }

    public function setTopVong3(?int $topVong3): self
    {
        $this->topVong3 = $topVong3;

        return $this;
    }

    public function getVong1phu(): ?bool
    {
        return $this->vong1phu;
    }

    public function setVong1phu(?bool $vong1phu): self
    {
        $this->vong1phu = $vong1phu;

        return $this;
    }

    public function getVong2phu(): ?bool
    {
        return $this->vong2phu;
    }

    public function setVong2phu(?bool $vong2phu): self
    {
        $this->vong2phu = $vong2phu;

        return $this;
    }

    public function getVong3phu(): ?bool
    {
        return $this->vong3phu;
    }

    public function setVong3phu(?bool $vong3phu): self
    {
        $this->vong3phu = $vong3phu;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getViTri(): ?bool
    {
        return $this->viTri;
    }

    public function setViTri(?bool $viTri): self
    {
        $this->viTri = $viTri;

        return $this;
    }

    public function getXdtVong1(): ?bool
    {
        return $this->xdtVong1;
    }

    public function setXdtVong1(?bool $xdtVong1): self
    {
        $this->xdtVong1 = $xdtVong1;

        return $this;
    }

    public function getXdtVong1SoCuTri(): ?int
    {
        return $this->xdtVong1SoCuTri;
    }

    public function setXdtVong1SoCuTri(?int $xdtVong1SoCuTri): self
    {
        $this->xdtVong1SoCuTri = $xdtVong1SoCuTri;

        return $this;
    }

    public function getXdtVong2SoCuTri(): ?int
    {
        return $this->xdtVong2SoCuTri;
    }

    public function setXdtVong2SoCuTri(?int $xdtVong2SoCuTri): self
    {
        $this->xdtVong2SoCuTri = $xdtVong2SoCuTri;

        return $this;
    }

    public function getXdtVong3SoCuTri(): ?int
    {
        return $this->xdtVong3SoCuTri;
    }

    public function setXdtVong3SoCuTri(?int $xdtVong3SoCuTri): self
    {
        $this->xdtVong3SoCuTri = $xdtVong3SoCuTri;

        return $this;
    }

    public function getRequiredVotesVongxdt1(): ?int
    {
        return $this->requiredVotesVongxdt1;
    }

    public function setRequiredVotesVongxdt1(?int $requiredVotesVongxdt1): self
    {
        $this->requiredVotesVongxdt1 = $requiredVotesVongxdt1;

        return $this;
    }

    public function getRequiredVotesVongxdt2(): ?int
    {
        return $this->requiredVotesVongxdt2;
    }

    public function setRequiredVotesVongxdt2(?int $requiredVotesVongxdt2): self
    {
        $this->requiredVotesVongxdt2 = $requiredVotesVongxdt2;

        return $this;
    }

    public function getRequiredVotesVongxdt3(): ?int
    {
        return $this->requiredVotesVongxdt3;
    }

    public function setRequiredVotesVongxdt3(?int $requiredVotesVongxdt3): self
    {
        $this->requiredVotesVongxdt3 = $requiredVotesVongxdt3;

        return $this;
    }

    public function getTopVongxdt1(): ?int
    {
        return $this->topVongxdt1;
    }

    public function setTopVongxdt1(?int $topVongxdt1): self
    {
        $this->topVongxdt1 = $topVongxdt1;

        return $this;
    }

    public function getTopVongxdt2(): ?int
    {
        return $this->topVongxdt2;
    }

    public function setTopVongxdt2(?int $topVongxdt2): self
    {
        $this->topVongxdt2 = $topVongxdt2;

        return $this;
    }

    public function getTopVongxdt3(): ?int
    {
        return $this->topVongxdt3;
    }

    public function setTopVongxdt3(?int $topVongxdt3): self
    {
        $this->topVongxdt3 = $topVongxdt3;

        return $this;
    }

    public function getVongxdt1phu(): ?int
    {
        return $this->vongxdt1phu;
    }

    public function setVongxdt1phu(?int $vongxdt1phu): self
    {
        $this->vongxdt1phu = $vongxdt1phu;

        return $this;
    }

    public function getVongxdt2phu(): ?int
    {
        return $this->vongxdt2phu;
    }

    public function setVongxdt2phu(?int $vongxdt2phu): self
    {
        $this->vongxdt2phu = $vongxdt2phu;

        return $this;
    }

    public function getVongxdt3phu(): ?int
    {
        return $this->vongxdt3phu;
    }

    public function setVongxdt3phu(?int $vongxdt3phu): self
    {
        $this->vongxdt3phu = $vongxdt3phu;

        return $this;
    }

    public function getVongxdt1(): ?bool
    {
        return $this->vongxdt1;
    }

    public function setVongxdt1(?bool $vongxdt1): self
    {
        $this->vongxdt1 = $vongxdt1;

        return $this;
    }

    public function getVongxdt2(): ?bool
    {
        return $this->vongxdt2;
    }

    public function setVongxdt2(?bool $vongxdt2): self
    {
        $this->vongxdt2 = $vongxdt2;

        return $this;
    }

    public function getVongxdt3(): ?bool
    {
        return $this->vongxdt3;
    }

    public function setVongxdt3(?bool $vongxdt3): self
    {
        $this->vongxdt3 = $vongxdt3;

        return $this;
    }

    public function getVongxdpNoi(): ?bool
    {
        return $this->vongxdpNoi;
    }

    public function setVongxdpNoi(?bool $vongxdpNoi): self
    {
        $this->vongxdpNoi = $vongxdpNoi;

        return $this;
    }

    public function getVongxdpNgoai(): ?bool
    {
        return $this->vongxdpNgoai;
    }

    public function setVongxdpNgoai(?bool $vongxdpNgoai): self
    {
        $this->vongxdpNgoai = $vongxdpNgoai;

        return $this;
    }

    public function getRequiredVotesVongxdpNoi(): ?int
    {
        return $this->requiredVotesVongxdpNoi;
    }

    public function setRequiredVotesVongxdpNoi(?int $requiredVotesVongxdpNoi): self
    {
        $this->requiredVotesVongxdpNoi = $requiredVotesVongxdpNoi;

        return $this;
    }

    public function getRequiredVotesVongxdpNgoai(): ?int
    {
        return $this->requiredVotesVongxdpNgoai;
    }

    public function setRequiredVotesVongxdpNgoai(?int $requiredVotesVongxdpNgoai): self
    {
        $this->requiredVotesVongxdpNgoai = $requiredVotesVongxdpNgoai;

        return $this;
    }

    public function getTopVongxdpNoi(): ?int
    {
        return $this->topVongxdpNoi;
    }

    public function setTopVongxdpNoi(?int $topVongxdpNoi): self
    {
        $this->topVongxdpNoi = $topVongxdpNoi;

        return $this;
    }

    public function getTopVongxdpNgoai(): ?int
    {
        return $this->topVongxdpNgoai;
    }

    public function setTopVongxdpNgoai(?int $topVongxdpNgoai): self
    {
        $this->topVongxdpNgoai = $topVongxdpNgoai;

        return $this;
    }

    public function getVongxdpNoiphu(): ?bool
    {
        return $this->vongxdpNoiphu;
    }

    public function setVongxdpNoiphu(?bool $vongxdpNoiphu): self
    {
        $this->vongxdpNoiphu = $vongxdpNoiphu;

        return $this;
    }

    public function getVongxdpNgoaiphu(): ?bool
    {
        return $this->vongxdpNgoaiphu;
    }

    public function setVongxdpNgoaiphu(?bool $vongxdpNgoaiphu): self
    {
        $this->vongxdpNgoaiphu = $vongxdpNgoaiphu;

        return $this;
    }
}
