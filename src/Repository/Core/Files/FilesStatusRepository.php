<?php

namespace App\Repository\Core\Files;

use App\Entity\Core\Files\FilesStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FilesStatus>
 *
 * @method FilesStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilesStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilesStatus[]    findAll()
 * @method FilesStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilesStatus::class);
    }

    public function save(FilesStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FilesStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
