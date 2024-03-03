<?php

namespace App\Service;

use App\DTO\AddLessonsDTO;
use App\Entity\Lesson;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class LessonService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly LessonRepository $lessonRepository,
        private readonly CourseRepository $courseRepository
    )
    {
    }

    public function create(string $name, int $courseId): ?Lesson
    {
        $course = $this->courseRepository->find($courseId);
        if (null === $course) {
            return null;
        }
        $lesson = new Lesson();
        $lesson->setLessonName($name);
        $lesson->setCourse($course);

        try {
            $this->entityManager->persist($lesson);
            $this->entityManager->flush();
        } catch (\Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка сохранения в БД']);
            return null;
        }
        return $lesson;
    }

    public function getById(int $id): ?Lesson
    {
        return $this->lessonRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->lessonRepository->findLessons($page, $perPage);
    }

    public function update(?int $id, ?string $name, ?int $courseId): ?Lesson
    {
        if (null === $id) {
            return null;
        }
        $lesson = $this->lessonRepository->find($id);
        if (null === $lesson) {
            return null;
        }
        $course = $courseId ? $this->courseRepository->find($courseId) : null;

        if (null !== $name && $lesson->getLessonName() != $name) {
            $lesson->setLessonName($name);
        }
        if (null !== $course && $lesson->getCourse() !== $course) {
            $lesson->setCourse($course);
        }

        try {
            $this->entityManager->persist($lesson);
            $this->entityManager->flush();
        } catch (\Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $lesson;
    }

    public function delete(int $id): bool
    {
        $lesson = $this->lessonRepository->find($id);
        if (null === $lesson) {
            return false;
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        return true;
    }

    public function addLessons(int $courseId, string $lessonName, int $count): int
    {
        $createdLessons = 0;
        for ($i = 0; $i < $count; $i++) {
            $lessonId = $this->create("{$lessonName}_#$i", $courseId);
            if ($lessonId !== null) {
                $createdLessons++;
            }
        }

        return $createdLessons;
    }

    public function getLessonsMessages(int $courseId, string $lessonName, int $count): array
    {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = (new AddLessonsDTO($courseId, "$lessonName#$i", 1))->toAMQPMessage();
        }

        return $result;
    }
}