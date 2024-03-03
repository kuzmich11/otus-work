<?php

namespace App\Service;

use App\Entity\ScoreSkill;
use App\Entity\ScoreTask;
use App\Repository\RatioRepository;
use App\Repository\ScoreSkillRepository;
use App\Repository\ScoreTaskRepository;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ScoreService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger,
        private readonly ScoreTaskRepository    $scoreTaskRepository,
        private readonly ScoreSkillRepository   $scoreSkillRepository,
        private readonly RatioRepository        $ratioRepository,
        private readonly UserRepository         $studentRepository,
        private readonly TaskRepository         $taskRepository,
    )
    {
    }

    public function create(int $taskId, int $studentId, int $score, DateTime $completedAt): ?int
    {
        $task = $this->taskRepository->find($taskId);
        $student = $this->studentRepository->find($studentId);
        if (!$task || !$student) {
            return null;
        }

        $scoreTask = new ScoreTask();
        $scoreTask->setTask($task);
        $scoreTask->setUser($student);
        $scoreTask->setScore($score);
        $scoreTask->setCompletedAt($completedAt);
        $this->addToDb($scoreTask);

        $ratios = $this->ratioRepository->findByTask($taskId);
        foreach ($ratios as $ratio) {
            $skill = $ratio->getSkill();
            if ($scoreSkillEntity = $this->scoreSkillRepository->findOneBy(['student' => $studentId, 'skill' => $skill])) {
                $scoreSkill = $scoreSkillEntity->getScore() + (int)(round($score / 100 * $ratio->getRatio()));
            } else {
                $scoreSkillEntity = new ScoreSkill();
                $scoreSkill = (int)(round($score / 100 * $ratio->getRatio()));
                $scoreSkillEntity->setSkill($skill);
                $scoreSkillEntity->setUser($student);
            }
            $scoreSkillEntity->setScore($scoreSkill);
            $this->addToDb($scoreSkillEntity);
        }
        return $scoreTask->getId();
    }

    private function addToDb($object): void
    {
        try {
            $this->entityManager->persist($object);
            $this->entityManager->flush();
        } catch (\Throwable $err) {
            $this->logger->error($err->getMessage(), ['message' => 'Ошибка сохранения в БД']);
        }
    }

    public function getScoreTask(int $page, int $perPage)
    {
        return $this->scoreTaskRepository->findScore($page, $perPage);
    }

    public function getScoreSkill(int $page, int $perPage)
    {
        return $this->scoreSkillRepository->findScore($page, $perPage);
    }

    public function update(?int $studentId, ?int $taskId, ?int $score, ?DateTime $completedAt): ?int
    {
        $scoreTask = $this->scoreTaskRepository->findOneBy(['student' => $studentId, 'task' => $taskId]);
        if (!$scoreTask) {
            return null;
        }
        if (null !== $score && $scoreTask->getScore() !== $score) {
            $ratios = $this->ratioRepository->findByTask($taskId);
            foreach ($ratios as $ratio) {
                $skill = $ratio->getSkill();
                $scoreSkillEntity = $this->scoreSkillRepository->findOneBy(['student' => $studentId, 'skill' => $skill]);
                $scoreSkill = $scoreSkillEntity->getScore() - (int)(round($scoreTask->getScore() / 100 * $ratio->getRatio()));
                $scoreSkill = $scoreSkill + (int)(round($score / 100 * $ratio->getRatio()));
                $scoreSkillEntity->setScore($scoreSkill);
                $this->addToDb($scoreSkillEntity);
            }
            $scoreTask->setScore($score);
        }
        if (null !== $completedAt && $scoreTask->getCompletedAt() != $completedAt) {
            $scoreTask->setCompletedAt($completedAt);
        }
        $this->addToDb($scoreTask);
        return $scoreTask->getId();
    }

    public function deleteCourse(int $studentId, int $taskId): ?bool
    {
        $scoreTask = $this->scoreTaskRepository->findOneBy(['student' => $studentId, 'task' => $taskId]);
        if (!$scoreTask) {
            return null;
        }

        $ratios = $this->ratioRepository->findByTask($taskId);
        foreach ($ratios as $ratio) {
            $skill = $ratio->getSkill();
            $scoreSkillEntity = $this->scoreSkillRepository->findOneBy(['student' => $studentId, 'skill' => $skill]);
            $scoreSkill = $scoreSkillEntity->getScore() - (int)(round($scoreTask->getScore() / 100 * $ratio->getRatio()));
            $scoreSkill = max($scoreSkill, 0);
            $scoreSkillEntity->setScore($scoreSkill);
            $this->addToDb($scoreSkillEntity);
        }
        $this->entityManager->remove($scoreTask);
        $this->entityManager->flush();
        return true;
    }
}