<?php

namespace AcMarche\Sepulture\Entity;

use AcMarche\Sepulture\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ORM\Table(name: 'history')]
class History implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Sepulture::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Sepulture $sepulture = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $made_by = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $property = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $old_value = null;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $new_value = null;

    public function __construct(
        ?Sepulture $sepulture,
        ?string $made_by,
        ?string $property,
        ?string $old_value,
        ?string $new_value
    ) {
        $this->sepulture = $sepulture;
        $this->property = $property;
        $this->made_by = $made_by;
        $this->old_value = substr($old_value, 0, 250);
        $this->new_value = substr($new_value, 0, 250);
    }

    public function getMadeBy(): ?string
    {
        return $this->made_by;
    }

    public function setMadeBy(?string $made_by): self
    {
        $this->made_by = $made_by;

        return $this;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(?string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->old_value;
    }

    public function setOldValue(?string $old_value): self
    {
        $this->old_value = $old_value;

        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->new_value;
    }

    public function setNewValue(?string $new_value): self
    {
        $this->new_value = $new_value;

        return $this;
    }
}
