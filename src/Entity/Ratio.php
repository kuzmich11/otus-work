<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *  Класс сущности "Соотношение навыки-задания"
 */
#[ORM\Table(name: 'ratio')]
#[ORM\Entity(repositoryClass: 'App\Repository\RatioRepository')]
#[ORM\Index(columns: ['task_id'],name: 'idx__ratio_task_id')]
#[ORM\Index(columns: ['skill_id'],name: 'idx__ratio_skill_id')]
#[ORM\UniqueConstraint(name: 'idx__ratio_task_skill', columns: ['task_id', 'skill_id'])]
class Ratio
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var Task Задание */
    #[ORM\ManyToOne(targetEntity: 'Task', inversedBy: 'ratios')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private Task $task;

    /** @var Skill Навык */
    #[ORM\ManyToOne(targetEntity: 'Skill', inversedBy: 'ratios')]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id')]
    private Skill $skill;

    /** @var int Соотношение */
    #[ORM\Column(name: 'ratio', type: 'integer', nullable: false)]
    #[Assert\Range(minMessage: 'Минимум 1%', maxMessage: 'Максимум 100%', min: 1, max: 100)]
    private int $ratio;

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
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }

    /**
     * @param Skill $skill
     */
    public function setSkill(Skill $skill): void
    {
        $this->skill = $skill;
    }

    /**
     * @return int
     */
    public function getRatio(): int
    {
        return $this->ratio;
    }

    /**
     * @param int $ratio
     */
    public function setRatio(int $ratio): void
    {
        $this->ratio = $ratio;
    }

    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'task_id' => 'int',
        'skill_id' => 'int',
        'ratio' => 'int',
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'task_id' => $this->task->getId(),
            'skill_id' => $this->skill->getId(),
            'ratio' => $this->getRatio(),
        ];
    }
}