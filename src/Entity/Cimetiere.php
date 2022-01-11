<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\CimetiereRepository;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert; // gedmo annotations
/**
 * Sepulture.
 */
#[ORM\Table(name: 'cimetieres')]
#[ORM\Entity(repositoryClass: CimetiereRepository::class)]
class Cimetiere implements SluggableInterface, Stringable
{
    use SluggableTrait;
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;
    #[ORM\Column(name: 'nom', type: 'string', length: 150, unique: true)]
    #[Assert\NotBlank]
    private ?string $nom = null;
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;
    /**
     * @var Sepulture[]|array
     */
    #[ORM\OneToMany(targetEntity: 'Sepulture', mappedBy: 'cimetiere', cascade: ['remove'])]
    protected iterable $sepultures;
    #[Assert\File(maxSize: '7M')]
    protected ?File $planFile = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $planName = null;
    #[Assert\File(maxSize: '7M')]
    protected ?File $imageFile = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $imageName = null;
    protected int $ihsCount=0;
    protected int $a1945Count=0;
    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }
    public function getSluggableFields(): array
    {
        return ['nom'];
    }
    public function getPlanFile(): ?File
    {
        return $this->planFile;
    }
    public function setPlanFile(File $planFile): void
    {
        $this->planFile = $planFile;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }
    public function getIhsCount(): int
    {
        return $this->ihsCount;
    }
    public function setIhsCount(int $ihsCount): void
    {
        $this->ihsCount = $ihsCount;
    }
    public function getA1945Count(): int
    {
        return $this->a1945Count;
    }
    public function setA1945Count(int $a1945Count): void
    {
        $this->a1945Count = $a1945Count;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getPlanName(): ?string
    {
        return $this->planName;
    }
    public function setPlanName(?string $planName): self
    {
        $this->planName = $planName;

        return $this;
    }
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

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
            $sepulture->setCimetiere($this);
        }

        return $this;
    }
    public function removeSepulture(Sepulture $sepulture): self
    {
        if ($this->sepultures->contains($sepulture)) {
            $this->sepultures->removeElement($sepulture);
            // set the owning side to null (unless already changed)
            if ($sepulture->getCimetiere() === $this) {
                $sepulture->setCimetiere(null);
            }
        }

        return $this;
    }
}
