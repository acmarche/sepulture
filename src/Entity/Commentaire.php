<?php

namespace AcMarche\Sepulture\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commentaire.
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="AcMarche\Sepulture\Repository\CommentaireRepository")
 */
class Commentaire implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=false)
     */
    protected $remarques;

    /**
     * @var Sepulture|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Sepulture\Entity\Sepulture", inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $sepulture;

    /**
     * @var string|null
     */
    private $captcha;

    public function __toString()
    {
        return 'Fait le '.$this->createdAt->format('d-m-Y H:i').' par '.$this->nom.' '.$this->email;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(string $remarques): self
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getSepulture(): ?Sepulture
    {
        return $this->sepulture;
    }

    public function setSepulture(?Sepulture $sepulture): self
    {
        $this->sepulture = $sepulture;

        return $this;
    }
}
