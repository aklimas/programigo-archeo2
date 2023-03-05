<?php

namespace App\Repository\Core;

use App\Entity\Core\Logs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Logs>
 *
 * @method Logs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logs[]    findAll()
 * @method Logs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogsRepository extends ServiceEntityRepository
{
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(
        ManagerRegistry $registry,
        RequestStack $requestStack,
        Security $security)
    {
        parent::__construct($registry, Logs::class);

        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function save(Logs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Logs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add($array = null)
    {
        $user = $this->security->getUser();
        if (null == $user) {
            $user_ = 'root';
        } else {
            $user_ = $user->getEmail();
        }

        if (empty($array['name'])) {
            $array['name'] = $this->requestStack->getCurrentRequest()->attributes->get('_controller');
        }
        if (empty($array['value'])) {
            $array['value'] = 'Brak opisu';
        }
        if (empty($array['status'])) {
            $array['status'] = 'SUCCESS';
        }
        if (empty($array['user'])) {
            $array['user'] = $user_;
        }

        $event = new Logs();
        $event->setName($array['name']);
        $event->setValue($array['value']);
        $event->setDate($array['date']);
        $event->setDateEnd(new \DateTime());
        $event->setStatus($array['status']);
        $event->setUser($array['user']);

        $this->save($event, true);
    }
}
