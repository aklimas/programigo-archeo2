<?php

namespace App\Repository\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactsList>
 *
 * @method ContactsList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactsList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactsList[]    findAll()
 * @method ContactsList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactsList::class);
    }

    public function save(ContactsList $entity, bool $flush = false): void
    {
        if (null == $entity->getDateAdd()) {
            $entity->setDateAdd(new \DateTime());
        }
        $entity->setDateModify(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactsList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactsList[] Returns an array of ContactsList objects
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

//    public function findOneBySomeField($value): ?ContactsList
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
