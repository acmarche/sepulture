<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Materiaux.
 *
 * @ORM\Table(name="materiaux")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\MateriauxRepository")
 */
class Materiaux
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
     * @ORM\Column(name="nom", type="string", length=100, nullable=false, options={"comment" = "nom"})
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var Sepulture[]|null
     * @ORM\ManyToMany(targetEntity="Sepulture", mappedBy="materiaux")
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
            $sepulture->addMateriaux($this);
        }

        return $this;
    }

    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->contains($sepulture)) {
            $this->sepultures->removeElement($sepulture);
            $sepulture->removeMateriaux($this);
        }

        return $this;
    }
}
