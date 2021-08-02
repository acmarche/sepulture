<?php

namespace AcMarche\Sepulture\Repository;

use Doctrine\ORM\QueryBuilder;
use AcMarche\Sepulture\Entity\Legal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Legal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legal[]    findAll()
 * @method Legal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legal::class);
    }

    public function getForList(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.nom', 'ASC');

        return $qb;
    }
}
