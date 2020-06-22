<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\Visuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Visuel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visuel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visuel[]    findAll()
 * @method Visuel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisuelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visuel::class);
    }

    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('v');

        $qb->orderBy('v.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = [];

        foreach ($results as $type) {
            $types[$type->getNom()] = $type->getId();
        }

        return $types;
    }

    public function getForList()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.nom', 'ASC');

        return $qb;
    }
}
