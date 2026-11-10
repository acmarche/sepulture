<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Doctrine\OrmCrudTrait;
use AcMarche\Sepulture\Entity\Preference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Preference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preference[]    findAll()
 * @method Preference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreferenceRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preference::class);
    }

    /**
     * @param $criteria
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function getCimetiereDefault($criteria)
    {
        $username = $criteria['username'] ?? null;
        $clef = $criteria['clef'] ?? null;

        $qb = $this->createQueryBuilder('preference');

        if ($username) {
            $qb->andwhere('preference.username :username')
                ->setParameter('username', $username);

            $qb->andwhere('preference.clef :clef')
                ->setParameter('clef', 'cimetiere');
        }

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}
