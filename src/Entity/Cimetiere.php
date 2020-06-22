<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert; // gedmo annotations

/**
 * Sepulture.
 *
 * @ORM\Table(name="cimetieres")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\CimetiereRepository")
 */
class Cimetiere implements SluggableInterface
{
    use SluggableTrait;

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
     * @ORM\Column(name="nom", type="string", length=150, unique=true)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var Sepulture|null
     * @ORM\OneToMany(targetEntity="Sepulture", mappedBy="cimetiere", cascade={"remove"})
     */
    protected $sepultures;

    /**
     * @Assert\File(
     *     maxSize = "7M"
     * )
     *
     * @var File|null
     */
    protected $planFile;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $planName;

    /**
     * @Assert\File(
     *     maxSize = "7M"
     * )
     *
     * @var File|null
     */
    protected $imageFile;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $imageName;

    /**
     * @var int
     */
    protected $ihsCount=0;

    /**
     * @var int
     */
    protected $a1945Count=0;

    public function __construct()
    {
        $this->sepultures = new ArrayCollection();
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    /**
     * @return File|null
     */
    public function getPlanFile()
    {
        return $this->planFile;
    }

    public function setPlanFile(File $planFile)
    {
        $this->planFile = $planFile;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return int
     */
    public function getIhsCount(): int
    {
        return $this->ihsCount;
    }

    /**
     * @param int $ihsCount
     */
    public function setIhsCount(int $ihsCount): void
    {
        $this->ihsCount = $ihsCount;
    }

    /**
     * @return int
     */
    public function getA1945Count(): int
    {
        return $this->a1945Count;
    }

    /**
     * @param int $a1945Count
     */
    public function setA1945Count(int $a1945Count): void
    {
        $this->a1945Count = $a1945Count;
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
    public function getSepultures(): Collection
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
