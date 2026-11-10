<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\OssuaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OssuaireRepository::class)]
#[ORM\Table(name: 'ossuaire')]
class Ossuaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180)]
    private $nom;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: Cimetiere::class, inversedBy: 'ossuaires')]
    private $cimetiere;

    #[ORM\OneToMany(mappedBy: 'ossuaire', targetEntity: Sepulture::class)]
    private $sepultures;

    #[ORM\ManyToOne(targetEntity: Document::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Document $document;

    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }

    public function __toString(): string
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
            $sepulture->setOssuaire($this);
        }

        return $this;
    }

    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->removeElement($sepulture)) {
            // set the owning side to null (unless already changed)
            if ($sepulture->getOssuaire() === $this) {
                $sepulture->setOssuaire(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getCimetiere()
    {
        return $this->cimetiere;
    }

    public function setCimetiere($cimetiere): void
    {
        $this->cimetiere = $cimetiere;
    }

}
