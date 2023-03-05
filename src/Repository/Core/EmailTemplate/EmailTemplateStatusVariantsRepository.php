<?php

namespace App\Repository\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplateStatusVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailTemplateStatusVariants>
 *
 * @method EmailTemplateStatusVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailTemplateStatusVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailTemplateStatusVariants[]    findAll()
 * @method EmailTemplateStatusVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTemplateStatusVariantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailTemplateStatusVariants::class);
    }

    public function save(EmailTemplateStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EmailTemplateStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EmailTemplateStatusVariants[] Returns an array of EmailTemplateStatusVariants objects
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

//    public function findOneBySomeField($value): ?EmailTemplateStatusVariants
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
