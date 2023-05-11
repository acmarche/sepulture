<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Doctrine\OrmCrudTrait;
use AcMarche\Sepulture\Entity\History;
use AcMarche\Sepulture\Entity\Sepulture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function findByFiche(Sepulture $sepulture): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.sepulture', 'sepulture', 'WITH')
            ->addSelect('sepulture')
            ->andWhere('h.fiche = :sepulture')
            ->setParameter('sepulture', $sepulture)
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function search(?string $fiche, ?string $madeBy, ?string $property): array
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.fiche', 'fiche', 'WITH')
            ->addSelect('fiche');

        if ($fiche) {
            $qb->andWhere('fiche.societe LIKE :fiche')
                ->setParameter('fiche', '%'.$fiche.'%');
        }

        if ($madeBy) {
            $qb->andWhere('h.made_by LIKE :madeBy')
                ->setParameter('madeBy', '%'.$madeBy.'%');
        }

        if ($property) {
            $qb->andWhere('h.property LIKE :property')
                ->setParameter('property', '%'.$property.'%');
        }

        return $qb
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function findOrdered(): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.fiche', 'fiche', 'WITH')
            ->addSelect('fiche')
            ->orderBy('h.createdAt', 'DESC')
            ->setMaxResults(200)
            //  ->groupBy('h.fiche')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return History[] Returns an array of History objects
     */
    public function findModifiedByToken(string $date): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.fiche', 'fiche', 'WITH')
            ->addSelect('fiche')
            ->andWhere('h.createdAt LIKE :date')
            ->setParameter('date', $date.'%')
            ->andWhere('h.made_by = :token')
            ->setParameter('token', 'token')
            ->orderBy('h.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
