<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 *  Класс сущности "Баллы навыков"
 */
#[ORM\Table(name: 'score_skill')]
#[ORM\Entity(repositoryClass: 'App\Repository\ScoreSkillRepository')]
#[ORM\Index(columns: ['user_id'],name: 'idx__score_skill_user_id')]
#[ORM\Index(columns: ['skill_id'],name: 'idx__score_skill_skill_id')]
#[ORM\UniqueConstraint(name: 'idx__score_skill_user_skill', columns: ['user_id', 'skill_id'])]
class ScoreSkill
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var User Студент */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'scoreSkills')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    /** @var Skill Навык */
    #[ORM\ManyToOne(targetEntity: 'Skill', inversedBy: 'scoreSkills')]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id')]
    private Skill $skill;

    /** @var int Баллы */
    #[ORM\Column(name: 'score', type: 'integer', nullable: false)]
    private int $score;

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
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'user_id' => 'int',
        'skill_id' => 'int',
        'score' => 'int',
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->getId(),
            'skill_id' => $this->skill->getId(),
            'score' => $this->score,
        ];
    }
}