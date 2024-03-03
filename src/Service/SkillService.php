<?php

namespace App\Service;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class SkillService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger,
        private readonly SkillRepository        $skillRepository
    )
    {
    }

    public function create(string $skillName): ?int
    {
        $skill = new Skill();
        $skill->setSkill($skillName);

        try {
            $this->entityManager->persist($skill);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка сохранения в БД']);
            return null;
        }
        return $skill->getId();
    }

    public function getById(int $id): ?Skill
    {
        return $this->skillRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->skillRepository->findTasks($page, $perPage);
    }

    public function update(?int $id, ?string $skillName): ?int
    {
        if (null === $id) {
            return null;
        }

        $skill = $this->skillRepository->find($id);
        if (null !== $skillName && $skill->getSkill() != $skillName) {
            $skill->setSkill($skillName);
        }

        try {
            $this->entityManager->persist($skill);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $skill->getId();
    }

    public function deleteSkill(int $id): bool
    {
        $skill = $this->skillRepository->find($id);
        if (null === $skill) {
            return false;
        }

        $this->entityManager->remove($skill);
        $this->entityManager->flush();

        return true;
    }
}