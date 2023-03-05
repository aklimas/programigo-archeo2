<?php

namespace App\Repository\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<ContactsListHistory>
 *
 * @method ContactsListHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactsListHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactsListHistory[]    findAll()
 * @method ContactsListHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsListHistoryRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, ContactsListHistory::class);

        $this->security = $security;
    }

    public function save(ContactsListHistory $entity, bool $flush = false): void
    {
        $entity->setUser($this->security->getUser());
        $entity->setDate(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactsListHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ContactsListHistory[] Returns an array of ContactsListHistory objects
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

//    public function findOneBySomeField($value): ?ContactsListHistory
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
