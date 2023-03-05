<?php

namespace App\Repository\Core\Tasks;

use App\Entity\Core\Tasks\Tasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tasks>
 *
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    private $status;

    public function __construct(ManagerRegistry $registry, TasksStatusRepository $tasksStatusRepository)
    {
        parent::__construct($registry, Tasks::class);
        $this->status = $tasksStatusRepository;
    }

    public function save(Tasks $entity, bool $flush = false): void
    {
        if (null == $entity->getDateAdd()) {
            $entity->setDateAdd(new \DateTime());
        }

        if (null == $entity->getStatus()) {
            $entity->setStatus($this->status->findOneBy(['value' => 'new']));
        }

        $entity->setDateModify(new \DateTime());

        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tasks $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Tasks[] Returns an array of Tasks objects
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

//    public function findOneBySomeField($value): ?Tasks
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
