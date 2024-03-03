<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use JetBrains\PhpStorm\ArrayShape;

/**
 *  Класс сущности "Задания"
 */
#[ORM\Table(name: 'task')]
#[ORM\Entity(repositoryClass: 'App\Repository\TaskRepository')]
#[ORM\Index(columns: ['task'], name: 'idx__task_name')]
#[ORM\Index(columns: ['lesson_id'],name: 'idx__task_lesson_id')]
#[ORM\Index(columns: ['created_at'], name: 'idx__task_created_at')]
#[ORM\Index(columns: ['updated_at'], name: 'idx__task_updated_at')]
#[ORM\UniqueConstraint(name: 'idx__task_lesson_task', columns: ['lesson_id', 'task'])]
class Task
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var string Название задания */
    #[ORM\Column(name: 'task', type: 'string', nullable: false)]
    private string $task;

    /** @var DateTime Дата создания */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    /** @var DateTime Дата изменения */
    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'update')]
    private DateTime $updatedAt;

    /** @var Lesson Занятие */
    #[ORM\ManyToOne(targetEntity: 'Lesson', inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id')]
    private Lesson $lesson;

    /** @var Collection|ArrayCollection Баллы за задания */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: 'ScoreTask')]
    private Collection $scoreTasks;

    /** @var Collection|ArrayCollection Соотношения заданий и навыков */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: 'Ratio')]
    private Collection $ratios;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->scoreTasks = new ArrayCollection();
        $this->ratios = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * @param string $task
     */
    public function setTask(string $task): void
    {
        $this->task = $task;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Lesson
     */
    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson|null $lesson
     */
    public function setLesson(?Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }

    /**
     * @param ScoreTask $score
     * @return void
     */
    public function addScoreTask(ScoreTask $score): void
    {
        if (!$this->scoreTasks->contains($score)) {
            $this->scoreTasks->add($score);
        }
    }

    /**
     * @return Collection<int, ScoreTask>
     */
    public function getScoreTasks(): Collection
    {
        return $this->scoreTasks;
    }

    /**
     * @param Ratio $ratio
     * @return void
     */
    public function addRatio(Ratio $ratio): void
    {
        if (!$this->ratios->contains($ratio)) {
            $this->ratios->add($ratio);
        }
    }

    /**
     * @return Collection<int, Ratio>
     */
    public function getRatios(): Collection
    {
        return $this->ratios;
    }

    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'task' => 'string',
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'lesson' => ['id' => 'int|null', 'lessonName' => 'string', 'createdAt' => 'string', 'updatedAt' => 'string'],
        'scoreTask' => [],
        'ratio' => []
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'task' => $this->getTask(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'lesson' => $this->lesson->toArray(),
            'scoreTask' => array_map(static fn(ScoreTask $scoreTask) => $scoreTask->toArray(), $this->scoreTasks->toArray()),
            'ratio' => array_map(static fn(Ratio $ratio) => $ratio->toArray(), $this->ratios->toArray()),
        ];
    }

    public function removeScoreTask(ScoreTask $scoreTask): static
    {
        if ($this->scoreTasks->removeElement($scoreTask)) {
            // set the owning side to null (unless already changed)
            if ($scoreTask->getTask() === $this) {
                $scoreTask->setTask(null);
            }
        }

        return $this;
    }

    public function removeRatio(Ratio $ratio): static
    {
        if ($this->ratios->removeElement($ratio)) {
            // set the owning side to null (unless already changed)
            if ($ratio->getTask() === $this) {
                $ratio->setTask(null);
            }
        }

        return $this;
    }
}