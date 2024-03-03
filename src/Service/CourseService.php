<?php

namespace App\Service;

use App\Entity\Course;
use App\Repository\CourseRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

class CourseService
{
    private const CACHE_TAG = 'courses';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger,
        private readonly CourseRepository       $courseRepository,
        private readonly TagAwareCacheInterface $cache,
    )
    {
    }

    public function create(string $name, DateTime $start, DateTime $end): ?int
    {
        $course = new Course();
        $course->setCourseName($name);
        $course->setStartedAt($start);
        $course->setFinishedAt($end);

        try {
            $this->entityManager->persist($course);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка сохранения в БД']);
            return null;
        }
        $this->cache->invalidateTags([self::CACHE_TAG]);
        return $course->getId();
    }

    public function getById(int $id): ?Course
    {
        return $this->courseRepository->find($id);
    }

    public function getAll(int $page, int $perPage)
    {
        return $this->cache->get(
            "courses_{$page}_{$perPage}",
            function (ItemInterface $item) use ($page, $perPage) {
                $courses = $this->courseRepository->findCourses($page, $perPage);
                $coursesSerialized = array_map(static fn(Course $course) => $course->toArray(), $courses);
                $item->set($coursesSerialized);
                $item->tag(self::CACHE_TAG);

                return $coursesSerialized;
            }
        );
//        if (!$coursesItem->isHit()) {
//            $courses = $this->courseRepository->findCourses($page, $perPage);
//            $coursesItem->set(array_map(static fn(Course $course) => $course->toArray(), $courses));
//            $this->cacheItemPool->save($coursesItem);
//        }
//
//        return $coursesItem->get();
    }

    public function update(?int $id, ?string $name, ?DateTime $start, ?DateTime $end): ?int
    {
        if (null === $id) {
            return null;
        }

        $course = $this->courseRepository->find($id);

        $values = $name;
        if (null !== $values && $course->getCourseName() != $values) {
            $course->setCourseName($values);
        }
        $values = $start;
        if (null !== $values && $course->getStartedAt() != $values) {
            $course->setStartedAt($values);
        }
        $values = $end;
        if (null !== $values && $course->getFinishedAt() != $values) {
            $course->setFinishedAt($values);
        }

        try {
            $this->entityManager->persist($course);
            $this->entityManager->flush();
        } catch (Throwable $err) {
            $this->logger->error($err, ['message' => 'Ошибка БД']);
            return null;
        }
        return $course->getId();
    }

    public function deleteCourse(int $id): bool
    {
        $course = $this->courseRepository->find($id);
        if (null === $course) {
            return false;
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return true;
    }

}