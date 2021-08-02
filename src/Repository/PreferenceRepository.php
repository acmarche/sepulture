<?php

namespace AcMarche\Sepulture\Repository;

use Doctrine\ORM\NonUniqueResultException;
use AcMarche\Sepulture\Entity\Preference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Preference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preference[]    findAll()
 * @method Preference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preference::class);
    }

    public function persist(Preference $preference): void
    {
        $this->_em->persist($preference);
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function remove(Preference $preference): void
    {
        $this->_em->remove($preference);
        $this->save();
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
        $username = isset($criteria['username']) ? $criteria['username'] : null;
        $clef = isset($criteria['clef']) ? $criteria['clef'] : null;

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
