<?php

namespace App\Repository;

use App\Entity\Reaction;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reaction>
 *
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    public function save(Reaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function countReaction(Session $sessionId): array|float|int|string
	{
		$rows = $this->createQueryBuilder('r')
			->select('r.type COUNT(r.id) as nb')
			->where('r.session = :session_id')
			->setParameter('session_id', $sessionId)
			->groupBy('r.type')
			->getQuery()
			->getArrayResult();

		$data = [];

		foreach ($rows as $row){
			$data[$row['type']] = (int) $row['nb'];
		}
		return $data;
	}

//    /**
//     * @return Reaction[] Returns an array of Reaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reaction
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
