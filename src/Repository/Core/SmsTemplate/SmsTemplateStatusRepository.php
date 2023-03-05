<?php

namespace App\Repository\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplateStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsTemplateStatus>
 *
 * @method SmsTemplateStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTemplateStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTemplateStatus[]    findAll()
 * @method SmsTemplateStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTemplateStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTemplateStatus::class);
    }

    public function save(SmsTemplateStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsTemplateStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SmsTemplateStatus[] Returns an array of SmsTemplateStatus objects
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

//    public function findOneBySomeField($value): ?SmsTemplateStatus
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
