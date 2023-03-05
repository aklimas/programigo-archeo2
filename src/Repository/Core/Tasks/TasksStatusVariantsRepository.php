<?php

namespace App\Repository\Core\Tasks;

use App\Entity\Core\Tasks\TasksStatusVariants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TasksStatusVariants>
 *
 * @method TasksStatusVariants|null find($id, $lockMode = null, $lockVersion = null)
 * @method TasksStatusVariants|null findOneBy(array $criteria, array $orderBy = null)
 * @method TasksStatusVariants[]    findAll()
 * @method TasksStatusVariants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksStatusVariantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TasksStatusVariants::class);
    }

    public function save(TasksStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TasksStatusVariants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TasksStatusVariants[] Returns an array of TasksStatusVariants objects
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

//    public function findOneBySomeField($value): ?TasksStatusVariants
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
