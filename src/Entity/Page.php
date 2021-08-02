<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert; // gedmo annotations

/**
 * Page.
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\PageRepository")
 */
class Page implements SluggableInterface
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
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $titre = null;

    /**
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $contenu = null;

    /**
     * @Assert\File(
     *     maxSize = "7M"
     * )
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $imageName = null;

    public function __toString()
    {
        return $this->titre;
    }

    public function getSluggableFields(): array
    {
        return ['titre'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }
}
