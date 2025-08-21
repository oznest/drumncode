<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\TaskStatus;
use App\Domain\Event\StatusChangedEvent;
use App\Domain\EventReleaseInterface;
use App\Domain\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Domain\Event\SubtaskAddedEvent;

class Task implements EventReleaseInterface
{
    use EventsTrait;

    #[Groups(['task:read'])]
    private ?int $id = null;

    #[Groups(['task:read'])]
    private TaskStatus $status ;

    #[Groups(['task:read'])]
    private ?int $priority;

    #[Groups(['task:read'])]
    private string $title;

    #[Groups(['task:read'])]
    private ?string $description = null;

    #[Groups(['task:read'])]
    private \DateTimeImmutable $createdAt;

    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[Groups(['task:read'])]
    private User $user;

    private ?Task $parent = null;

    #[Groups(['task:read'])]
    private Collection $subtasks;

    public function __construct(
        User $user,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $completedAt = null,
    ) {
        $this->user = $user;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->subtasks = new ArrayCollection();
        $this->status = TaskStatus::TODO;
        if ($completedAt) {
            $this->completedAt = $completedAt;
        }
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setStatus(TaskStatus $status): Task
    {
        if ($status === TaskStatus::DONE && $this->hasActiveSubtasks()) {
            throw new \LogicException('This task has active subtasks.');
        }

        $this->status = $status;
        if ($this->isDone()) {
            $this->completedAt = new \DateTimeImmutable();
        }

        $this->record(new StatusChangedEvent($this->id ?? 0));

        return $this;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setPriority(int $priority): Task
    {
        if ($priority < 1 || $priority > 5) {
            throw new \InvalidArgumentException('Priority must be between 1 and 5');
        }
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
        if (count($this->subtasks)) {
            foreach ($this->subtasks as $subtask) {
                if (!$subtask->isDone()) {
                    return false;
                }
            }
        }

        return $this->status === TaskStatus::DONE;
    }

    public function hasActiveSubtasks(): bool
    {
        if (count($this->subtasks)) {
            foreach ($this->subtasks as $subtask) {
                if (!$subtask->isDone()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setParent(?Task $parent): Task
    {
        $this->parent = $parent;
        if ($parent) {
            $parent->addSubtask($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function addSubtask(self $task): void
    {
        if (!$this->subtasks->contains($task)) {
            $this->subtasks[] = $task;
            $task->setParent($this);
            $this->record(new SubtaskAddedEvent($this, $task));
        }
    }

    public function removeSubtask(self $task): void
    {
        if ($this->subtasks->removeElement($task)) {
            if ($task->getParent() === $this) {
                $task->setParent(null);
            }
        }
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user === $this->getUser() && !$this->isDone();
    }
}
