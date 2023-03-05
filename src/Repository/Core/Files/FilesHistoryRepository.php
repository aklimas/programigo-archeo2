<?php

namespace App\Repository\Core\Files;

use App\Entity\Core\Files\FilesHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<FilesHistory>
 *
 * @method FilesHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilesHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilesHistory[]    findAll()
 * @method FilesHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilesHistoryRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, FilesHistory::class);

        $this->security = $security;
    }

    public function save(FilesHistory $entity, bool $flush = false): void
    {
        $entity->setUser($this->security->getUser());
        $entity->setDate(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FilesHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FilesHistory[] Returns an array of FilesHistory objects
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

//    public function findOneBySomeField($value): ?FilesHistory
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
