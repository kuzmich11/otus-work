<?php

namespace App\Service;

use App\Entity\Ratio;
use App\Repository\RatioRepository;
use App\Repository\SkillRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class RatioService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly RatioRepository $ratioRepository,
        private readonly TaskRepository $taskRepository,
        private readonly SkillRepository $skillRepository,
    )
    {
    }

    public function create(int $taskId, int $skillId, int $count): ?int
    {
        $task = $this->taskRepository->find($taskId);
        $skill = $this->skillRepository->find($skillId);
        if (!$task || !$skill) {
            return null;
        }
        
        $ratio = new Ratio();
        $ratio->setTask($task);
        $ratio->setSkill($skill);
        $ratio->setRatio($count);

        try {
            $this->entityManager->persist($ratio);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка сохранения в БД']);
            return null;
        }
        return $ratio->getId();
    }

    public function getById(int $id): ?Ratio
    {
        return $this->ratioRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->ratioRepository->findRatio($page, $perPage);
    }

    public function update(?int $id, ?int $taskId, ?int $skillId, ?int $count)
    {
        $task = $this->taskRepository->find($taskId);
        $skill = $this->skillRepository->find($skillId);
        if (null === $id) {
            return null;
        }

        $ratio = $this->ratioRepository->find($id);

        if (null !== $task && $ratio->getTask() !== $task) {
            $ratio->setTask($task);
        }
        if (null !== $skill && $ratio->getSkill() !== $skill) {
            $ratio->setSkill($skill);
        }
        if (null !== $count && $ratio->getRatio() != $count) {
            $ratio->setRatio($count);
        }

        try {
            $this->entityManager->persist($ratio);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $ratio->getId();
    }

    public function deleteRatio(int $id): bool
    {
        $ratio = $this->ratioRepository->find($id);
        if (null === $ratio) {
            return false;
        }

        $this->entityManager->remove($ratio);
        $this->entityManager->flush();

        return true;
    }
}