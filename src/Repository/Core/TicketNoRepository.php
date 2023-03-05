<?php

namespace App\Repository\Core;

use App\Entity\Core\TicketNo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketNo>
 *
 * @method TicketNo|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketNo|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketNo[]    findAll()
 * @method TicketNo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketNoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketNo::class);
    }

    public function save(TicketNo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TicketNo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByDate($workflow)
    {
        $date = new \DateTime();
        $dateString = $date->format('Y-m').'-%';
        $qb = $this->createQueryBuilder('e');
        $qb
            ->andWhere('e.workflow = :workflow')
            ->setParameter('workflow', $workflow)
            ->andWhere('e.date LIKE :from')
            ->setParameter('from', $dateString)
        ;
        $qb->setMaxResults(1);
        $result = $qb->getQuery()->getResult();

        if ($result) {
            return $result[0];
        } else {
            return false;
        }
    }

//    /**
//     * @return TicketNo[] Returns an array of TicketNo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TicketNo
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
