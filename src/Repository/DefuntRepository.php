<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\Defunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Defunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Defunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Defunt[]    findAll()
 * @method Defunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefuntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Defunt::class);
    }

    /**
     * @return Defunt[]
     */
    public function findAllGroupByName()
    {
        /**
         * SELECT ANY_VALUE(d.id), d.nom, count(d.nom) as lignes
         * FROM defunts d GROUP BY d.nom ORDER BY d.nom ASC.
         */
        $qb = $this->createQueryBuilder('d');
        //$qb->select('ANY_VALUE(d.id) as id, d.nom, count(d.nom) as lignes');
        $qb->select('d.id, d.nom, count(d.nom) as lignes');
        $qb->groupBy('d.nom');
        $qb->orderBy('d.nom');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    private function t()
    {
        $sql = "select ANY_VALUE(d.id) as id, d.nom, count(d.nom) as lignes from AcMarche\Sepulture\Entity\Defunt d group by d.nom";

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery($sql);
    }
}
