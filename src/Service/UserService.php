<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly LoggerInterface             $logger,
        private readonly UserRepository              $userRepository,
        private readonly CourseRepository            $courseRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function create($params): ?int
    {
        $user = new User();
        $user->setLogin($params['login']);
        $hashPassword = $this->passwordHasher->hashPassword($user, $params['password']);
        $user->setPassword($hashPassword);
        $user->setEmail($params['email']);
        if (!empty($params['name'])) {
            $user->setName($params['name']);
        }
        if (!empty($params['lastName'])) {
            $user->setLastName($params['lastName']);
        }
        if (!empty($params['courseId'])) {
            $course = $this->courseRepository->find($params['courseId']);
            if ($course) {
                $user->addCourse($course);
            }
        }

        $roles = $params['roles'] ?? ['ROLE_USER'];
        $user->setRoles($roles);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $user->getId();
    }

    public function getById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->userRepository->findUsers($page, $perPage);
    }

    public function update(int $id, array $params): ?int
    {
        $student = $this->userRepository->find($id);
        if (null == $student) {
            return null;
        }
        if (!empty($params['name']) && $student->getName() != $params['name']) {
            $student->setName($params['name']);
        }
        if (!empty($params['lastName']) && $student->getLastname() != $params['lastName']) {
            $student->setLastname($params['lastName']);
        }
        if (!empty($params['email']) && $student->getEmail() != $params['email']) {
            $student->setEmail($params['email']);
        }

        try {
            $this->entityManager->persist($student);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }

        return $student->getId();
    }

    public function deleteStudent(int $id): bool
    {
        $student = $this->userRepository->find($id);
        if (null === $student) {
            return false;
        }

        $this->entityManager->remove($student);
        $this->entityManager->flush();

        return true;
    }

    public function saveUserFromDTO(User $user, UserDTO $userDTO): ?int
    {
        $user->setName($userDTO->name);
        $user->setLastname($userDTO->lastName);
        $user->setEmail($userDTO->email);
        $user->setLogin($userDTO->login);
        if (strlen($userDTO->password) < 60) {
            $hashPassword = $this->passwordHasher->hashPassword($user, $userDTO->password);
            $user->setPassword($hashPassword);
        } else {
            $user->setPassword($userDTO->password);
        }
        $hashPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashPassword);
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }
}