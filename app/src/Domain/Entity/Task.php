<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\TaskStatus;
use App\Infrastructure\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?int $id = null;
    #[ORM\Column(type: 'string', enumType: TaskStatus::class)]
    #[Groups(['task:read'])]
    private TaskStatus $status;
    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Groups(['task:read'])]
    private int $priority;
    #[ORM\Column(type: 'string')]
    #[Groups(['task:read'])]
    private string $title;
    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['task:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['task:read'])]
    private User $user;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subtasks')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['task:read'])]
    private ?Task $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'])]
    private Collection $subtasks;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
        $this->subtasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setStatus(TaskStatus $status): Task
    {
        $this->status = $status;
        if ($this->isDone()) {
           $this->completedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setPriority(int $priority): Task
    {
        $this->priority = $priority;
        return $this;
    }

    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description): Task
    {
        $this->description = $description;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isDone(): bool
    {
        return $this->status === TaskStatus::DONE;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setParent(?Task $parent): Task
    {
        $this->parent = $parent;
        return $this;
    }

    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function setSubtasks(Collection $subtasks): Task
    {
        $this->subtasks = $subtasks;
        return $this;
    }
}
