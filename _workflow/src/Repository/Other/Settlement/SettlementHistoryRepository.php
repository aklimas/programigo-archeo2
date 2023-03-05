<?php

namespace App\Repository\Other\Settlement;

use App\Entity\Other\Settlement\SettlementHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<SettlementHistory>
 *
 * @method SettlementHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettlementHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettlementHistory[]    findAll()
 * @method SettlementHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettlementHistoryRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, SettlementHistory::class);

        $this->security = $security;
    }

    public function save(SettlementHistory $entity, bool $flush = false): void
    {
        $entity->setUser($this->security->getUser());
        $entity->setDate(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SettlementHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SettlementHistory[] Returns an array of SettlementHistory objects
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

//    public function findOneBySomeField($value): ?SettlementHistory
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
