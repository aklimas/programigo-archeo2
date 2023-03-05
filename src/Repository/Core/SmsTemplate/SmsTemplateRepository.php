<?php

namespace App\Repository\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsTemplate>
 *
 * @method SmsTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTemplate[]    findAll()
 * @method SmsTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTemplate::class);
    }

    public function save(SmsTemplate $entity, bool $flush = false): void
    {
        // var_dump($entity->getDateAdd());

        if (null === $entity->getDateAdd()) {
            $entity->setDateAdd(new \DateTime());
        }

        $entity->setDateModify(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SmsTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SmsTemplate[] Returns an array of SmsTemplate objects
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

//    public function findOneBySomeField($value): ?SmsTemplate
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
