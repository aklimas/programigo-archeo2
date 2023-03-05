<?php

namespace App\Repository\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactsListStatus>
 *
 * @method ContactsListStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactsListStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactsListStatus[]    findAll()
 * @method ContactsListStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsListStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactsListStatus::class);
    }

    public function save(ContactsListStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactsListStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
