<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Thing;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Thing>
 *
 * @method Thing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thing[]    findAll()
 * @method Thing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thing::class);
    }

    public function save(Thing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Thing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Thing[] Returns an array of Thing objects
     */
    public function getOutThingsForUser($userId): array

    {
        // filter on reservation
        // on prends si
        // si reservation.owner = user et endDate > date et startDate < date et state null
        // si reservation.owner != user et endDat > date et startDate

        // on ne prends pas si il existe une reservation a state = 1 ( sorti )
        // et endDate => date et startDate <= date
        return $this->createQueryBuilder('t')
            ->innerJoin('t.reservations', 'r', 'WITH', 'r.state = 1 AND r.startDate <= :date ')
            ->innerJoin('r.owner', 'o' , 'WITH', 'o.id = :userId')
            ->setParameter('date', new \DateTime())
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllExceptPending($filter=null) {
        if($filter) {
            return $this->createQueryBuilder('t')
                ->andWhere('t.name LIKE :like OR t.description LIKE :like')
                ->setParameter('like', '%'. $filter . '%')
                ->getQuery()
                ->getResult()
            ;
        } else {

            return $this->createQueryBuilder('t')
                ->andWhere('t.status IS NULL')
                //->andWhere('NOT EXISTS (SELECT r FROM \App\Entity\Reservation r)')
                ->getQuery()
                ->getResult();
        }
    }

    public function findPending($delta = 10)  {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.reservations', 'r', 'WITH', 'r.backDate IS NULL and r.endDate <= :date')
            ->setParameter('date', (new \DateTime())->modify("-{$delta} days"))
            ->getQuery()
            ->getResult()
        ;

    }

    public function waitingForUser(User $user) {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.reservations', 'r', 'WITH', 'r.state IS NULL or r.state = 0')
            ->innerJoin('r.owner', 'o', 'WITH', 'o.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function currentForUser(User $user) {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.reservations', 'r', 'WITH', 'r.state = 1 ')
            ->innerJoin('r.owner', 'o', 'WITH', 'o.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function doneForUser(User $user) {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.reservations', 'r', 'WITH', 'r.state = 2 ')
            ->innerJoin('r.owner', 'o', 'WITH', 'o.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
            ;
    }


//    public function findOneBySomeField($value): ?Thing
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
