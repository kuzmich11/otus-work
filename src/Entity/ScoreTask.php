<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 *  Класс сущности "Баллы за задания"
 */
#[ORM\Table(name: 'score_task')]
#[ORM\Entity(repositoryClass: 'App\Repository\ScoreTaskRepository')]
#[ORM\Index(columns: ['user_id'],name: 'idx__score_task_user_id')]
#[ORM\Index(columns: ['task_id'],name: 'idx__score_task_task_id')]
#[ORM\UniqueConstraint(name: 'idx__score_task_user_task', columns: ['user_id', 'task_id'])]
class ScoreTask
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var User Студент */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'scoreTasks')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    /** @var Task Задание */
    #[ORM\ManyToOne(targetEntity: 'Task', inversedBy: 'scoreTasks')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private Task $task;

    /** @var int Баллы */
    #[ORM\Column(name: 'score', type: 'integer', nullable: false)]
    private int $score;

    /** @var DateTime Дата выполнения задания */
    #[ORM\Column(name: 'completed_at', type: 'datetime', nullable: false)]
    private DateTime $completedAt;

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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     */
    public function setTask(?Task $task): void
    {
        $this->task = $task;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    /**
     * @return DateTime
     */
    public function getCompletedAt(): DateTime
    {
        return $this->completedAt;
    }

    /**
     * @param DateTime $completedAt
     */
    public function setCompletedAt(DateTime $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'user_id' => 'int',
        'task_id' => 'int',
        'score' => 'int',
        'completedAt' => 'string',
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->user->getId(),
            'task_id' => $this->task->getId(),
            'score' => $this->getScore(),
            'completedAt' => $this->completedAt->format('Y-m-d H:i:s'),
        ];
    }
}