<?php

namespace App\Repository\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListStatusVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactsListStatusVariants>
 *
 * @method ContactsListStatusVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactsListStatusVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactsListStatusVariants[]    findAll()
 * @method ContactsListStatusVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsListStatusVariantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactsListStatusVariants::class);
    }

    public function save(ContactsListStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactsListStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactsListStatusVariants[] Returns an array of ContactsListStatusVariants objects
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

//    public function findOneBySomeField($value): ?ContactsListStatusVariants
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
