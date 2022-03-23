<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Doctrine\OrmCrudTrait;
use AcMarche\Sepulture\Entity\Cimetiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cimetiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cimetiere|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                      method User[]    findAll()
 * @method Cimetiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CimetiereRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cimetiere::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], [
            'nom' => 'ASC',
        ]);
    }

    /**
     * @param $criteria
     *
     * @return Cimetiere[]
     */
    public function search($criteria): array
    {
        $nom = $criteria['nom'] ?? null;

        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.sepultures', 's', 'WITH');
        $qb->addSelect('s');

        if ($nom) {
            $qb->andwhere('s.parcelle LIKE :parcelle')
                ->setParameter('parcelle', '%'.$nom.'%');
        }

        $qb->orderBy('c.nom');

        $query = $qb->getQuery();
        //$query_string = $query->getSQL();
        //echo $query_string;

        $results = $query->getResult();

        return $results;
    }

    public function getForSearch(): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->orderBy('c.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $cimetieres = [];

        foreach ($results as $type) {
            $cimetieres[$type->getNom()] = $type->getId();
        }

        return $cimetieres;
    }

    public function getForList(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.nom', 'ASC');

        return $qb;
    }
}
