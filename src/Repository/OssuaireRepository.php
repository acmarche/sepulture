<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Doctrine\OrmCrudTrait;
use AcMarche\Sepulture\Entity\Ossuaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ossuaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ossuaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ossuaire[]    findAll()
 * @method Ossuaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OssuaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ossuaire::class);
    }

    /**
     * @return array|Ossuaire[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('ossuaire')
            ->leftJoin('ossuaire.sepultures', 'sepultures', 'WITH')
            ->addSelect('sepultures')
            ->orderBy('ossuaire.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
