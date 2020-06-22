<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\ContactRw;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactRw|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRw|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRw[]    findAll()
 * @method ContactRw[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRwRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactRw::class);
    }

    public function findOne(): ?ContactRw
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
