<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use JetBrains\PhpStorm\ArrayShape;

/**
 *  Класс сущности "Курсы"
 */
#[ORM\Table(name: 'course')]
#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Index(columns: ['course_name'], name: 'idx__course_name')]
#[ORM\Index(columns: ['created_at'], name: 'idx__course_created_at')]
#[ORM\Index(columns: ['updated_at'], name: 'idx__course_updated_at')]
#[ORM\UniqueConstraint(name: 'idx__course_name_start_finish', columns: ['course_name', 'started_at', 'finished_at'])]
class Course
{
    /** @var int ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    /** @var string Название курса */
    #[ORM\Column(name: 'course_name', type: 'string', length: 255, nullable: false)]
    private string $courseName;

    /** @var DateTime Дата начала курса */
    #[ORM\Column(name: 'started_at', type: 'datetime', nullable: false)]
    private DateTime $startedAt;

    /** @var DateTime Дата окончания курса */
    #[ORM\Column(name: 'finished_at', type: 'datetime', nullable: false)]
    private DateTime $finishedAt;

    /** @var DateTime Дата создания курса */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    /** @var DateTime Дата изменения курса */
    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'update')]
    private DateTime $updatedAt;

    /** @var ArrayCollection|Collection Занятия курса */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'Lesson')]
    private Collection|ArrayCollection $lessons;

    /** @var ArrayCollection|Collection Студенты курса*/
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'courses')]
    #[ORM\JoinTable(name: 'course_users')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private Collection|ArrayCollection $users;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->users = new ArrayCollection();
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
    public function getCourseName(): string
    {
        return $this->courseName;
    }

    /**
     * @param string $courseName
     */
    public function setCourseName(string $courseName): void
    {
        $this->courseName = $courseName;
    }

    /**
     * @return DateTime
     */
    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param DateTime $startedAt
     */
    public function setStartedAt(DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return DateTime
     */
    public function getFinishedAt(): DateTime
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTime $finishedAt
     */
    public function setFinishedAt(DateTime $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
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
     * @param Lesson $lesson
     * @return void
     */
    public function addLesson(Lesson $lesson): void
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
        }
    }

    /**
     * @return Collection
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    /**
     * @param User $user
     * @return void
     */
    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'courseName' => 'string',
        'startedAt' => 'string',
        'finishedAt' => 'string',
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'lessons' => [],
        'users' => [],
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'courseName' => $this->getCourseName(),
            'startedAt' => $this->startedAt->format('Y-m-d'),
            'finishedAt' => $this->finishedAt->format('Y-m-d H:i:s'),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'lessons' => array_map(static fn(Lesson $lesson) => $lesson->toArray(), $this->lessons->toArray()),
            'users' => array_map(
                static fn(User $user) => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'lastName' => $user->getLastname(),
                    'email' => $user->getEmail(),
                ],
                $this->users->toArray()),
        ];
    }

    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getCourse() === $this) {
                $lesson->setCourse(null);
            }
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}