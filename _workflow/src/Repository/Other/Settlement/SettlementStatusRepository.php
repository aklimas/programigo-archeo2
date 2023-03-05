<?php

namespace App\Repository\Other\Settlement;

use App\Entity\Other\Settlement\SettlementStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SettlementStatus>
 *
 * @method SettlementStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettlementStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettlementStatus[]    findAll()
 * @method SettlementStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettlementStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SettlementStatus::class);
    }

    public function save(SettlementStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SettlementStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
