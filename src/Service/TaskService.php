<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\LessonRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TaskService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly LessonRepository $lessonRepository,
        private readonly TaskRepository $taskRepository

    )
    {
    }

    public function create(string $taskName, int $lessonId): ?int
    {
        $lesson = $this->lessonRepository->find($lessonId);
        if (null === $lesson) {
            return null;
        }
        $task = new Task();
        $task->setTask($taskName);
        $task->setLesson($lesson);

        try {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        } catch (\Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка сохранения в БД']);
            return null;
        }
        return $task->getId();
    }

    public function getById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->taskRepository->findTasks($page, $perPage);
    }

    public function update(?int $id, ?string $taskName, ?int $lessonId): ?int
    {
        if (null === $id) {
            return null;
        }
        $lesson = $lessonId ? $this->lessonRepository->find($lessonId) : null;
        $task = $this->taskRepository->find($id);

        if (null !== $taskName && $task->getTask() != $taskName) {
            $task->setTask($taskName);
        }
        if (null !== $lesson && $task->getLesson() !== $lesson) {
            $task->setLesson($lesson);
        }

        try {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
        } catch (\Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $task->getId();
    }

    public function deleteUser(int $id): bool
    {
        $task = $this->taskRepository->find($id);
        if (null === $task) {
            return false;
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return true;
    }
}