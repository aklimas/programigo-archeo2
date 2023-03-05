<?php

namespace App\Repository\Other\Settlement;

use App\Entity\Other\Settlement\SettlementStatusVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SettlementStatusVariants>
 *
 * @method SettlementStatusVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettlementStatusVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettlementStatusVariants[]    findAll()
 * @method SettlementStatusVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettlementStatusVariantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettlementStatusVariants::class);
    }

    public function save(SettlementStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SettlementStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SettlementStatusVariants[] Returns an array of SettlementStatusVariants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SettlementStatusVariants
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
