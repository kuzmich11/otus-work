<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 *  Класс сущности "Навыки"
 */
#[ORM\Table(name: 'skill')]
#[ORM\Entity(repositoryClass: 'App\Repository\SkillRepository')]
#[ORM\Index(columns: ['skill'], name: 'idx__skill_name')]
#[ORM\UniqueConstraint(name: 'idx__skill_name', columns: ['skill'])]
class Skill
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var string Навык */
    #[ORM\Column(name: 'skill', type: 'string', length: 255, nullable: false)]
    private string $skill;

    /** @var ArrayCollection|Collection Баллы навыков */
    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: 'ScoreSkill')]
    private Collection|ArrayCollection $scoreSkills;

    /** @var ArrayCollection|Collection Соотношения */
    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: 'Ratio')]
    private Collection|ArrayCollection $ratios;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->scoreSkills = new ArrayCollection();
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
    public function getSkill(): string
    {
        return $this->skill;
    }

    /**
     * @param string $skill
     */
    public function setSkill(string $skill): void
    {
        $this->skill = $skill;
    }


    /**
     * @param ScoreTask $score
     * @return void
     */
    public function addScoreSkill(ScoreTask $score): void
    {
        if (!$this->scoreSkills->contains($score)) {
            $this->scoreSkills->add($score);
        }
    }

    /**
     * @return Collection<int, ScoreSkill>
     */
    public function getScoreSkills(): Collection
    {
        return $this->scoreSkills;
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
        'skill' => 'string',
        'scoreSkill' => [],
        'ratio' => [],
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'skill' => $this->skill,
            'scoreSkill' => array_map(static fn(ScoreSkill $scoreSkill) => $scoreSkill->toArray(), $this->scoreSkills->toArray()),
            'ratio' => array_map(static fn(Ratio $ratio) => $ratio->toArray(), $this->ratios->toArray()),
        ];
    }

    public function removeScoreSkill(ScoreSkill $scoreSkill): static
    {
        if ($this->scoreSkills->removeElement($scoreSkill)) {
            // set the owning side to null (unless already changed)
            if ($scoreSkill->getSkill() === $this) {
                $scoreSkill->setSkill(null);
            }
        }

        return $this;
    }

    public function removeRatio(Ratio $ratio): static
    {
        if ($this->ratios->removeElement($ratio)) {
            // set the owning side to null (unless already changed)
            if ($ratio->getSkill() === $this) {
                $ratio->setSkill(null);
            }
        }

        return $this;
    }
}