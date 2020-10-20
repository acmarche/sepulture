<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\Sihl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sihl|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sihl|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sihl[]    findAll()
 * @method Sihl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SihlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sihl::class);
    }

    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('s');

        $qb->orderBy('s.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = [];

        foreach ($results as $type) {
            $types[$type->getNom()] = $type->getId();
        }

        return $types;
    }
}
