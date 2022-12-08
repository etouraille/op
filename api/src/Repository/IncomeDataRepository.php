<?php

namespace App\Repository;

use App\Entity\IncomeData;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IncomeData>
 *
 * @method IncomeData|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomeData|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomeData[]    findAll()
 * @method IncomeData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncomeData::class);
    }

    public function save(IncomeData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IncomeData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findForUser(User $user) {
        return $this->createQueryBuilder('b')
            ->join('b.user', 'u', 'WITH', 'u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy('b.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;

    }



//    /**
//     * @return IncomeData[] Returns an array of IncomeData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IncomeData
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
