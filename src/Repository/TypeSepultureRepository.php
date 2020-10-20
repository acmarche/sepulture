<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\TypeSepulture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeSepulture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeSepulture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeSepulture[]    findAll()
 * @method TypeSepulture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeSepultureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeSepulture::class);
    }

    public function getForSearch()
    {
        $qb = $this->createQueryBuilder('t');

        $qb->orderBy('t.nom');
        $query = $qb->getQuery();

        $results = $query->getResult();
        $types = [];

        foreach ($results as $type) {
            $types[$type->getNom()] = $type->getId();
        }

        return $types;
    }
}
