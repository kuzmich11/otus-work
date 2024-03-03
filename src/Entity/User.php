<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *  Класс сущности "Пользователи"
 */
#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: 'App\Repository\UserRepository')]
#[ORM\Index(columns: ['name'], name: 'idx__users_name')]
#[ORM\Index(columns: ['lastname'], name: 'idx__users_lastname')]
#[ORM\Index(columns: ['created_at'], name: 'idx__users_created_at')]
#[ORM\Index(columns: ['updated_at'], name: 'idx__users_updated_at')]
#[ORM\UniqueConstraint(name: 'idx__users_login', columns: ['login'])]
#[ORM\UniqueConstraint(name: 'idx__users_email', columns: ['email'])]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    /** @var int|null ID */
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /** @var string|null Имя */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $name;

    /** @var string|null Фамилия */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $lastname;

    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: false)]
    private string $login;

    #[ORM\Column(type: 'string', length: 120, nullable: false)]
    private string $password;

    /** @var string Email */
    #[ORM\Column(type: 'string', length: 45, nullable: false)]
    #[Assert\Email(message: "Не корректный адрес электронной почты")]
    private string $email;

    #[ORM\Column(type: 'json', length: 1024, nullable: false)]
    private array $roles = [];

    /** @var DateTime Дата создания */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    /** @var DateTime Дата изменения */
    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'update')]
    private DateTime $updatedAt;

    /** @var ArrayCollection|Collection Курсы */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'users')]
    private Collection|ArrayCollection $courses;

    /** @var ArrayCollection|Collection Баллы за задания */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'ScoreTask')]
    private Collection|ArrayCollection $scoreTasks;

    /** @var ArrayCollection|Collection Баллы навыков */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'ScoreSkill')]
    private Collection|ArrayCollection $scoreSkills;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->scoreTasks = new ArrayCollection();
        $this->scoreSkills = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param ?string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param ?string $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
     * @param Course $course
     * @return void
     */
    public function addCourse(Course $course): void
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addUser($this);
        }
    }

    /**
     * @return Collection<int, ScoreTask>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
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

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            $course->removeUser($this);
        }

        return $this;
    }

    public function removeScoreTask(ScoreTask $scoreTask): static
    {
        if ($this->scoreTasks->removeElement($scoreTask)) {
            // set the owning side to null (unless already changed)
            if ($scoreTask->getUser() === $this) {
                $scoreTask->setUser(null);
            }
        }

        return $this;
    }

    public function removeScoreSkill(ScoreSkill $scoreSkill): static
    {
        if ($this->scoreSkills->removeElement($scoreSkill)) {
            // set the owning side to null (unless already changed)
            if ($scoreSkill->getUser() === $this) {
                $scoreSkill->setUser(null);
            }
        }

        return $this;
    }


    /**
     * Преобразовать в массив
     *
     * @return array
     */
    #[ArrayShape([
        'id' => 'int|null',
        'name' => 'string',
        'lastname' => 'string',
        'email' => 'string',
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'courses' => [],
        'scoreTask' => [],
        'scoreSkill' => [],
    ])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'courses' => array_map(
                static fn(Course $course) => ['id' => $course->getId(), 'courseName' => $course->getCourseName()],
                $this->courses->toArray()
            ),
            'scoreTask' => array_map(
                static fn(ScoreTask $scoreTask) => $scoreTask->toArray(),
                $this->scoreTasks->toArray()),
            'scoreSkill' => array_map(
                static fn(ScoreSkill $scoreSkill) => $scoreSkill->toArray(),
                $this->scoreSkills->toArray()),
        ];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }
}