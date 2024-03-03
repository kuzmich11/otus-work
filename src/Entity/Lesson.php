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
 *  Класс сущности "Занятия"
 */
#[ORM\Table(name: 'lesson')]
#[ORM\Entity(repositoryClass: 'App\Repository\LessonRepository')]
#[ORM\Index(columns: ['lesson_name'], name: 'idx__lesson_name')]
#[ORM\Index(columns: ['course_id'], name: 'idx__lesson_course')]
#[ORM\Index(columns: ['created_at'],name: 'idx__lesson_created_at')]
#[ORM\Index(columns: ['updated_at'],name: 'idx__lesson_updated_at')]
#[ORM\UniqueConstraint(name: 'idx__lesson_name_course', columns: ['lesson_name', 'course_id'])]
class Lesson
{
    /** @var int|null ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /** @var string Название занятия */
    #[ORM\Column(name: 'lesson_name', type: 'string', length: 255, nullable: false)]
    private string $lessonName;

    /** @var Course Связанный курс */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'lessons')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private Course $course;

    /** @var DateTime Дата создания */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    /** @var DateTime Дата изменения */
    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'update')]
    private DateTime $updatedAt;

    /** @var ArrayCollection|Collection Задания */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: 'Task')]
    private Collection|ArrayCollection $tasks;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
    public function getLessonName(): string
    {
        return $this->lessonName;
    }

    /**
     * @param string $lessonName
     */
    public function setLessonName(string $lessonName): void
    {
        $this->lessonName = $lessonName;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return void
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = DateTime::createFromFormat('U', (string)time());
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return void
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = DateTime::createFromFormat('U', (string)time());
    }

    /**
     * @param Task $task
     * @return void
     */
    public function addTask(Task $task): void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
        }
    }

    /**
     * @return Collection
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'lessonName' => 'string',
        'course' => [],
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'tasks' => ['id' => 'int|null', 'login' => 'string']
        ])]
    public function toArray(): array
    {
        return [
                'id' => $this->getId(),
                'lessonName' => $this->getLessonName(),
                'course' => isset($this->course) ? ['id' => $this->course->getId(), 'courseName' => $this->course->getCourseName()] : null,
                'createdAt' => isset($this->createdAt) ? $this->getCreatedAt()->format('Y-m-d H:i:s') : '',
                'updatedAt' => isset($this->updatedAt) ? $this->getUpdatedAt()->format('Y-m-d H:i:s') : '',
                'tasks' => array_map(
                    static fn(Task $task) => ['id' => $task->getId(), 'task' => $task->getTask()],
                    $this->tasks->toArray()),
            ];
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getLesson() === $this) {
                $task->setLesson(null);
            }
        }

        return $this;
    }
}