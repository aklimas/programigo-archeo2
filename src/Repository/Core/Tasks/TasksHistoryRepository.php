<?php

namespace App\Repository\Core\Tasks;

use App\Entity\Core\Tasks\TasksHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<TasksHistory>
 *
 * @method TasksHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TasksHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TasksHistory[]    findAll()
 * @method TasksHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksHistoryRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, TasksHistory::class);

        $this->security = $security;
    }

    public function save(TasksHistory $entity, bool $flush = false): void
    {
        $entity->setUser($this->security->getUser());
        $entity->setDate(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TasksHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TasksHistory[] Returns an array of TasksHistory objects
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

//    public function findOneBySomeField($value): ?TasksHistory
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
