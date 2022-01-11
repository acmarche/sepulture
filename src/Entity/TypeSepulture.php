<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\TypeSepultureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tombe.
 */
#[ORM\Table(name: 'types')]
#[ORM\Entity(repositoryClass: TypeSepultureRepository::class)]
class TypeSepulture implements Stringable
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;
    #[ORM\Column(name: 'nom', type: 'string', length: 100, nullable: false, options: [
        'comment' => 'patronyme',
    ])]
    #[Assert\NotBlank]
    private ?string $nom = null;
    /**
     * @var Sepulture[]|iterable
     */
    #[ORM\ManyToMany(targetEntity: 'Sepulture', mappedBy: 'types')]
    private iterable $sepultures;

    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->nom;
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
    public function getSepultures(): iterable
    {
        return $this->sepultures;
    }

    public function addSepulture(Sepulture $sepulture): self
    {
        if (! $this->sepultures->contains($sepulture)) {
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
