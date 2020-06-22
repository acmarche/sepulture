<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tombe.
 *
 * @ORM\Table(name="types")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\TypeSepultureRepository")
 */
class TypeSepulture
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=100, nullable=false, options={"comment" = "patronyme"})
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var Sepulture[]|iterable
     * @ORM\ManyToMany(targetEntity="Sepulture", mappedBy="types")
     */
    private $sepultures;

    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Sepulture[]
     */
    public function getSepultures(): Collection
    {
        return $this->sepultures;
    }

    public function addSepulture(Sepulture $sepulture): self
    {
        if (!$this->sepultures->contains($sepulture)) {
            $this->sepultures[] = $sepulture;
            $sepulture->addType($this);
        }

        return $this;
    }

    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->contains($sepulture)) {
            $this->sepultures->removeElement($sepulture);
            $sepulture->removeType($this);
        }

        return $this;
    }
}
