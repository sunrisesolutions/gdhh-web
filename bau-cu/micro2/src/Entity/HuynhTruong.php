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

    public function updateVoteCount(NhiemKy $nhiemKy)
    {
        $truong = $this;

        if (!empty($nhiemKy)) {
            $cacPbt = $truong->getCacPhieuBauTheoVong($nhiemKy->getVongHienTai());
        } else {
            $cacPbt = $truong->getCacPhieuBau();
        }

        $voteCount = [];
        $vong = $nhiemKy->getVongHienTai();
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
                };
                $voteCount[$vongKey]++;
            }
        }
        $truong->{'setVong'.$vong}($voteCount[$vongKey]);
        if ($nhiemKy->isVongPhu()) {
            $truong->{'setVong'.$vong.'phu'}($voteCount[$vongPhuKey]);
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
}
