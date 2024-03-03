<?php

namespace App\Repository;

use App\Entity\ScoreTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий баллов за задания
 *
 * @extends ServiceEntityRepository<ScoreTask>
 *
 * @method ScoreTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreTask[]    findAll()
 * @method ScoreTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreTaskRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScoreTask::class);
    }

    /**
     * Получить сумму баллов по курсу и студенту
     *
     * @param int $courseId ID курса
     * @param int $studentId ID студента
     *
     * @return int|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findTotalScoreByCourseAndStudent(int $courseId, int $studentId): ?int
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('SUM(s.score) AS total')
            ->where('s.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->innerJoin('s.task', 't', 'WITH', 't.id = s.task')
            ->innerJoin('t.lesson', 'l', 'WITH', 'l.course = :courseId')
            ->setParameter('courseId', $courseId);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Получить сумму баллов студентов по курсу
     *
     * @param int $courseId ID курса
     *
     * @return int|null
     */
    public function findScoreStudentByCourse(int $courseId): ?int
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('st.id', 'SUM(s.score) AS total')
            ->innerJoin('s.student', 'st', 'with', 'st.id = s.student')
            ->innerJoin('s.task', 't', 'WITH', 't.id = s.task')
            ->innerJoin('t.lesson', 'l', 'WITH', 'l.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->groupBy('st.id');
        return $qb->getQuery()->getResult();
    }

    public function findScore(int $page, int $perPage)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->orderBy('s.id')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);
        return $qb->getQuery()->getResult();
    }
}
