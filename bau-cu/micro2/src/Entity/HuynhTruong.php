<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HuynhTruongRepository")
 */
class HuynhTruong
{
    /**
     * @return Collection|PhieuBau[]
     */
    public function getCacPhieuBauTheoVong($vong): Collection
    {
        $c = Criteria::create();
        $c->andWhere(Criteria::expr()->eq('vong', $vong));
        return $this->cacPhieuBau->matching($c);
    }

    /**
     * @return Collection|PhieuBau[]
     */
    public function getCacPhieuBauTheoVongPhu($vong): Collection
    {
        $c = Criteria::create();
        $c->andWhere(Criteria::expr()->eq('vongphu', $vong));
        return $this->cacPhieuBau->matching($c);
    }

    public function updateVotesHienTai(NhiemKy $nhiemKy)
    {
        $vong = $nhiemKy->getVongHienTai();
        $this->votes = $this->{'vong'.$vong};
    }

    public function updateVoteCount(NhiemKy $nhiemKy)
    {
        $truong = $this;

        if (!empty($nhiemKy)) {
            if ($nhiemKy->isVongPhu()) {
                $cacPbt = $truong->getCacPhieuBauTheoVongPhu($nhiemKy->getVongHienTai());
            } else {
                $cacPbt = $truong->getCacPhieuBauTheoVong($nhiemKy->getVongHienTai());
            }
        } else {
            $cacPbt = $truong->getCacPhieuBau();
        }

        $voteCount = [];
        $vong = $nhiemKy->getVongHienTai();

        if (empty($vong)) {
            throw new \InvalidArgumentException('Invalid Vong');
        }

        $vongKey = 'vong'.$vong;
        if (!array_key_exists($vongKey, $voteCount)) {
            $voteCount[$vongKey] = 0;
        }
        $vongPhuKey = 'vong'.$vong.'phu';
        if ($nhiemKy->isVongPhu() && !array_key_exists($vongPhuKey, $voteCount)) {
            $voteCount[$vongPhuKey] = 0;
        }

        /** @var PhieuBau $pbt */
        foreach ($cacPbt as $pbt) {
            if ($pbt->getCuTri()->getSubmitted()) {
                if (!empty($vongPhu = $pbt->getVongphu())) {
                    $voteCount[$vongPhuKey]++;
                } else {
                    $voteCount[$vongKey]++;
                };

            }
        }

        if ($nhiemKy->isVongPhu()) {
            $truong->{'setVong'.$vong.'phu'}($voteCount[$vongPhuKey]);
        } else {
            $truong->{'setVong'.$vong}($voteCount[$vongKey]);
        }

        $this->updatedAt = new \DateTime();
        return $voteCount;
    }

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
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $cacPhieuBau;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aka;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $christianName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phanDoan;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $chiDoan;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $enabled = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $votes;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $thanhVienId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\HuynhTruongXinRut", mappedBy="huynhTruong", cascade={"persist", "remove"})
     */
    private $huynhTruongXinRut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong1phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong2phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vong3phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phoNoiVu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phoNgoaiVu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xuDoanTruong1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xuDoanTruong2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xuDoanTruong3;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ungCuVienXDT;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ungCuVienXDP;

    /**
     * @ORM\Column(type="boolean")
     */
    private $tanXDT;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tanXDPNoiVu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tanXDPNgoaiVu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdt3;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNoi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNgoai;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNoiphu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNgoaiphu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNoi2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNgoai2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNoi2phu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vongxdpNgoai2phu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ungCuVienXDPNoi2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ungCuVienXDPNgoai2;

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

    public function getAka(): ?string
    {
        return $this->aka;
    }

    public function setAka(?string $aka): self
    {
        $this->aka = $aka;

        return $this;
    }

    public function getChristianName(): ?string
    {
        return $this->christianName;
    }

    public function setChristianName(string $christianName): self
    {
        $this->christianName = $christianName;

        return $this;
    }

    public function getDob(): ?int
    {
        return $this->dob;
    }

    public function setDob(?int $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getPhanDoan(): ?string
    {
        return $this->phanDoan;
    }

    public function setPhanDoan(?string $phanDoan): self
    {
        $this->phanDoan = $phanDoan;

        return $this;
    }

    public function getChiDoan(): ?string
    {
        return $this->chiDoan;
    }

    public function setChiDoan(?string $chiDoan): self
    {
        $this->chiDoan = $chiDoan;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(?int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getThanhVienId(): ?int
    {
        return $this->thanhVienId;
    }

    public function setThanhVienId(?int $thanhVienId): self
    {
        $this->thanhVienId = $thanhVienId;

        return $this;
    }

    public function getHuynhTruongXinRut(): ?HuynhTruongXinRut
    {
        return $this->huynhTruongXinRut;
    }

    public function setHuynhTruongXinRut(?HuynhTruongXinRut $huynhTruongXinRut): self
    {
        $this->huynhTruongXinRut = $huynhTruongXinRut;

        // set (or unset) the owning side of the relation if necessary
        $newHuynhTruong = null === $huynhTruongXinRut ? null : $this;
        if ($huynhTruongXinRut->getHuynhTruong() !== $newHuynhTruong) {
            $huynhTruongXinRut->setHuynhTruong($newHuynhTruong);
        }

        return $this;
    }

    public function getVong1phu(): ?int
    {
        return $this->vong1phu;
    }

    public function setVong1phu(?int $vong1phu): self
    {
        $this->vong1phu = $vong1phu;

        return $this;
    }

    public function getVong2phu(): ?int
    {
        return $this->vong2phu;
    }

    public function setVong2phu(?int $vong2phu): self
    {
        $this->vong2phu = $vong2phu;

        return $this;
    }

    public function getVong3phu(): ?int
    {
        return $this->vong3phu;
    }

    public function setVong3phu(?int $vong3phu): self
    {
        $this->vong3phu = $vong3phu;

        return $this;
    }

    public function getPhoNoiVu(): ?int
    {
        return $this->phoNoiVu;
    }

    public function setPhoNoiVu(?int $phoNoiVu): self
    {
        $this->phoNoiVu = $phoNoiVu;

        return $this;
    }

    public function getPhoNgoaiVu(): ?int
    {
        return $this->phoNgoaiVu;
    }

    public function setPhoNgoaiVu(?int $phoNgoaiVu): self
    {
        $this->phoNgoaiVu = $phoNgoaiVu;

        return $this;
    }

    public function getUngCuVienXDT(): ?bool
    {
        return $this->ungCuVienXDT;
    }

    public function setUngCuVienXDT(?bool $ungCuVienXDT): self
    {
        $this->ungCuVienXDT = $ungCuVienXDT;

        return $this;
    }

    public function getUngCuVienXDP(): ?bool
    {
        return $this->ungCuVienXDP;
    }

    public function setUngCuVienXDP(?bool $ungCuVienXDP): self
    {
        $this->ungCuVienXDP = $ungCuVienXDP;

        return $this;
    }

    public function getTanXDT(): ?bool
    {
        return $this->tanXDT;
    }

    public function setTanXDT(bool $tanXDT): self
    {
        $this->tanXDT = $tanXDT;

        return $this;
    }

    public function getTanXDPNoiVu(): ?bool
    {
        return $this->tanXDPNoiVu;
    }

    public function setTanXDPNoiVu(?bool $tanXDPNoiVu): self
    {
        $this->tanXDPNoiVu = $tanXDPNoiVu;

        return $this;
    }

    public function getTanXDPNgoaiVu(): ?bool
    {
        return $this->tanXDPNgoaiVu;
    }

    public function setTanXDPNgoaiVu(?bool $tanXDPNgoaiVu): self
    {
        $this->tanXDPNgoaiVu = $tanXDPNgoaiVu;

        return $this;
    }

    public function getVongxdt1(): ?int
    {
        return $this->vongxdt1;
    }

    public function setVongxdt1(?int $vongxdt1): self
    {
        $this->vongxdt1 = $vongxdt1;

        return $this;
    }

    public function getVongxdt2(): ?int
    {
        return $this->vongxdt2;
    }

    public function setVongxdt2(?int $vongxdt2): self
    {
        $this->vongxdt2 = $vongxdt2;

        return $this;
    }

    public function getVongxdt3(): ?int
    {
        return $this->vongxdt3;
    }

    public function setVongxdt3(?int $vongxdt3): self
    {
        $this->vongxdt3 = $vongxdt3;

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

    public function getVongxdpNoi(): ?int
    {
        return $this->vongxdpNoi;
    }

    public function setVongxdpNoi(?int $vongxdpNoi): self
    {
        $this->vongxdpNoi = $vongxdpNoi;

        return $this;
    }

    public function getVongxdpNgoai(): ?int
    {
        return $this->vongxdpNgoai;
    }

    public function setVongxdpNgoai(?int $vongxdpNgoai): self
    {
        $this->vongxdpNgoai = $vongxdpNgoai;

        return $this;
    }

    public function getVongxdpNoiphu(): ?int
    {
        return $this->vongxdpNoiphu;
    }

    public function setVongxdpNoiphu(?int $vongxdpNoiphu): self
    {
        $this->vongxdpNoiphu = $vongxdpNoiphu;

        return $this;
    }

    public function getVongxdpNgoaiphu(): ?int
    {
        return $this->vongxdpNgoaiphu;
    }

    public function setVongxdpNgoaiphu(?int $vongxdpNgoaiphu): self
    {
        $this->vongxdpNgoaiphu = $vongxdpNgoaiphu;

        return $this;
    }

    public function getVongxdpNoi2(): ?int
    {
        return $this->vongxdpNoi2;
    }

    public function setVongxdpNoi2(?int $vongxdpNoi2): self
    {
        $this->vongxdpNoi2 = $vongxdpNoi2;

        return $this;
    }

    public function getVongxdpNgoai2(): ?int
    {
        return $this->vongxdpNgoai2;
    }

    public function setVongxdpNgoai2(?int $vongxdpNgoai2): self
    {
        $this->vongxdpNgoai2 = $vongxdpNgoai2;

        return $this;
    }

    public function getVongxdpNoi2phu(): ?int
    {
        return $this->vongxdpNoi2phu;
    }

    public function setVongxdpNoi2phu(?int $vongxdpNoi2phu): self
    {
        $this->vongxdpNoi2phu = $vongxdpNoi2phu;

        return $this;
    }

    public function getVongxdpNgoai2phu(): ?int
    {
        return $this->vongxdpNgoai2phu;
    }

    public function setVongxdpNgoai2phu(?int $vongxdpNgoai2phu): self
    {
        $this->vongxdpNgoai2phu = $vongxdpNgoai2phu;

        return $this;
    }

    public function getUngCuVienXDPNoi2(): ?bool
    {
        return $this->ungCuVienXDPNoi2;
    }

    public function setUngCuVienXDPNoi2(?bool $ungCuVienXDPNoi2): self
    {
        $this->ungCuVienXDPNoi2 = $ungCuVienXDPNoi2;

        return $this;
    }

    public function getUngCuVienXDPNgoai2(): ?bool
    {
        return $this->ungCuVienXDPNgoai2;
    }

    public function setUngCuVienXDPNgoai2(?bool $ungCuVienXDPNgoai2): self
    {
        $this->ungCuVienXDPNgoai2 = $ungCuVienXDPNgoai2;

        return $this;
    }
}
