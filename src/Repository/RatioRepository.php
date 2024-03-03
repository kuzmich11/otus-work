<?php

namespace App\Repository;

use App\Entity\Ratio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Репозиторий соотношений навыков и заданий
 *
 * @extends ServiceEntityRepository<Ratio>
 *
 * @method Ratio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ratio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ratio[]    findAll()
 * @method Ratio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatioRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ratio::class);
    }

    public function findRatio($page, $perPage)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->orderBy('r.id')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);
        return $qb->getQuery()->getResult();
    }

    public function findByTask(int $taskId)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.task = :taskId')
            ->setParameter('taskId', $taskId);
        return $qb->getQuery()->getResult();
    }
}
