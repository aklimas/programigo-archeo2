<?php

namespace App\Repository\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplateStatusVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsTemplateStatusVariants>
 *
 * @method SmsTemplateStatusVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTemplateStatusVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTemplateStatusVariants[]    findAll()
 * @method SmsTemplateStatusVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTemplateStatusVariantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTemplateStatusVariants::class);
    }

    public function save(SmsTemplateStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsTemplateStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SmsTemplateStatusVariants[] Returns an array of SmsTemplateStatusVariants objects
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

//    public function findOneBySomeField($value): ?SmsTemplateStatusVariants
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
