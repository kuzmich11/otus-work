<?php

namespace App\Repository;

use App\Entity\ScoreSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий баллов навыков
 *
 * @extends ServiceEntityRepository<ScoreSkill>
 *
 * @method ScoreSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreSkill[]    findAll()
 * @method ScoreSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreSkillRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScoreSkill::class);
    }

    /**
     * Получить сумму баллов по курсу и студенту
     *
     * @param int $courseId
     * @param int $studentId
     * @return int|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findTotalScoreByCourseAndStudent(int $courseId, int $studentId): ?int
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('SUM(s.score) AS total')
            ->where('s.student = st.id')
            ->innerJoin('s.student', 'st', 'WITH', 'st.id = :studentId')
            ->setParameter('studentId', $studentId)
            ->innerJoin('st.courses', 'c', 'WITH', 'c.id = :courseId')
            ->setParameter('courseId', $courseId);

        return $qb->getQuery()->getSingleScalarResult();
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
