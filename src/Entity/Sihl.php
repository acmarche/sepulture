<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sihl Sepulture Interet Historique Locale.
 *
 * @ORM\Table(name="sihl")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\SihlRepository")
 */
class Sihl implements SluggableInterface
{
    use SluggableTrait;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $nom = null;

    /**
     * @var Sepulture[]|iterable
     * @ORM\ManyToMany(targetEntity="Sepulture", mappedBy="sihls")
     * */
    private Collection $sepultures;

    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
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
    public function getSepultures(): iterable
    {
        return $this->sepultures;
    }

    public function addSepulture(Sepulture $sepulture): self
    {
        if (!$this->sepultures->contains($sepulture)) {
            $this->sepultures[] = $sepulture;
            $sepulture->addSihl($this);
        }

        return $this;
    }

    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->contains($sepulture)) {
            $this->sepultures->removeElement($sepulture);
            $sepulture->removeSihl($this);
        }

        return $this;
    }
}
