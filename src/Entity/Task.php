<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"tasks:read"}},
 *     denormalizationContext={"groups"={"tasks:write"}},
 *     attributes={"pagination_items_per_page"=5},
 *     collectionOperations={
 *          "get",
 *          "post" = { "security_post_denormalize" = "is_granted('TASK_CREATE', object)" }
 *     },
 *     itemOperations={
 *          "get" = { "security" = "is_granted('TASK_READ', object)" },
 *          "put" = { "security" = "is_granted('TASK_EDIT', object)" },
 *          "delete" = { "security" = "is_granted('TASK_DELETE', object)" }
 *     },
 * )
 * @ApiFilter(DateFilter::class, properties={"date"})
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\EntityListeners({"App\Doctrine\Listeners\TaskSetCreatorListener"})
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tasks:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"tasks:read","tasks:write"})
     * @Assert\NotBlank
     * @Assert\Type("\DateTimeInterface")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tasks:read","tasks:write"})
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"tasks:read","tasks:write"})
     */
    private $description;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"tasks:read","tasks:write"})
     */
    private $timeEstimate;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"tasks:read"})
     */
    private $timeActual;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"tasks:read","tasks:write"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"tasks:read","tasks:write"})

     */
    private $creator;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getTimeEstimate(): ?int
    {
        return $this->timeEstimate;
    }

    public function setTimeEstimate(?int $timeEstimate): self
    {
        $this->timeEstimate = $timeEstimate;

        return $this;
    }

    public function getTimeActual(): ?int
    {
        return $this->timeActual;
    }

    public function setTimeActual(?int $timeActual): self
    {
        $this->timeActual = $timeActual;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}
