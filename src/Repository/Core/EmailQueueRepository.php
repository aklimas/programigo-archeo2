<?php

namespace App\Repository\Core;

use App\Entity\Core\EmailQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailQueue>
 *
 * @method EmailQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailQueue[]    findAll()
 * @method EmailQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailQueueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailQueue::class);
    }

    public function save(EmailQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByInMinute()
    {
        $date = new \DateTime();

        return $this->createQueryBuilder('a')
            ->Where('a.status = :status')
            ->setParameter('status', 0)
            ->andWhere('a.postDate BETWEEN :from AND :to')
            ->setParameter('from', $date->format('1970-01-01 H:i:00'))
            ->setParameter('to', $date->format('1970-01-01 H:i:59'))
            ->getQuery()
            ->getResult();
    }

    public function findByDate($from, $to, $status = 1)
    {
        return $this->createQueryBuilder('a')
            ->Where('a.status = :status')
            ->setParameter('status', $status)
            ->andWhere('a.postDate BETWEEN :from AND :to')
            ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getResult();
    }

    public function getPosted($campaings)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->Where('a.compaigns = :compaigns')
            ->setParameter('compaigns', $campaings)
            ->andWhere($qb->expr()->isNotNull('a.postDate'))
            ->getQuery()
            ->getResult();
    }

    public function getReaded($campaings)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->Where('a.compaigns = :compaigns')
            ->setParameter('compaigns', $campaings)
            ->andWhere($qb->expr()->isNotNull('a.readDate'))
            ->getQuery()
            ->getResult();
    }

    public function getError($campaings)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb
            ->Where('a.compaigns = :compaigns')
            ->setParameter('compaigns', $campaings)
            ->andWhere('a.status = 0')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return EmailQueue[] Returns an array of EmailQueue objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmailQueue
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
