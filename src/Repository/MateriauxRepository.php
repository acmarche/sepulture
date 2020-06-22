<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\Materiaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Materiaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Materiaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Materiaux[]    findAll()
 * @method Materiaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MateriauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Materiaux::class);
    }
}
