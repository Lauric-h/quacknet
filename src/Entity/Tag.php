<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Quack::class, mappedBy="tags", cascade={"persist"})
     */
    private $Quack;

    public function __construct()
    {
        $this->Quack = new ArrayCollection();
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

    /**
     * @return Collection|Quack[]
     */
    public function getQuack(): Collection
    {
        return $this->Quack;
    }

    public function addQuack(Quack $quack): self
    {
        if (!$this->Quack->contains($quack)) {
            $this->Quack[] = $quack;
        }

        return $this;
    }

    public function removeQuack(Quack $quack): self
    {
        $this->Quack->removeElement($quack);

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}
