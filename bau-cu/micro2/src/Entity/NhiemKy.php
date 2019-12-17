<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NhiemKyRepository")
 */
class NhiemKy
{
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
    private $name;

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
    private $xdtVong2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $xdtVong3;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getXdtVong2(): ?bool
    {
        return $this->xdtVong2;
    }

    public function setXdtVong2(?bool $xdtVong2): self
    {
        $this->xdtVong2 = $xdtVong2;

        return $this;
    }

    public function getXdtVong3(): ?bool
    {
        return $this->xdtVong3;
    }

    public function setXdtVong3(?bool $xdtVong3): self
    {
        $this->xdtVong3 = $xdtVong3;

        return $this;
    }
}
