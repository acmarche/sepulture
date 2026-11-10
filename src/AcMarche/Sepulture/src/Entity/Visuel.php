<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\VisuelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Visuel.
 */
#[ORM\Table(name: 'visuel')]
#[ORM\Entity(repositoryClass: VisuelRepository::class)]
class Visuel implements Stringable
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;
    #[ORM\Column(name: 'nom', type: 'string', length: 100, nullable: false, options: [
        'comment' => 'nom',
    ])]
    #[Assert\NotBlank]
    private ?string $nom = null;
    /**
     * @var Sepulture[]|iterable
     */
    #[ORM\OneToMany(targetEntity: 'Sepulture', mappedBy: 'visuel')]
    private Collection $sepultures;

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
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
        if (! $this->sepultures->contains($sepulture)) {
            $this->sepultures[] = $sepulture;
            $sepulture->setVisuel($this);
        }

        return $this;
    }

    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->contains($sepulture)) {
            $this->sepultures->removeElement($sepulture);
            // set the owning side to null (unless already changed)
            if ($sepulture->getVisuel() === $this) {
                $sepulture->setVisuel(null);
            }
        }

        return $this;
    }
}
