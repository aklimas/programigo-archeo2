<?php

namespace App\Repository\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplateStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTemplateStatus>
 *
 * @method EmailTemplateStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTemplateStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTemplateStatus[]    findAll()
 * @method EmailTemplateStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTemplateStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplateStatus::class);
    }

    public function save(EmailTemplateStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailTemplateStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EmailTemplateStatus[] Returns an array of EmailTemplateStatus objects
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

//    public function findOneBySomeField($value): ?EmailTemplateStatus
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
