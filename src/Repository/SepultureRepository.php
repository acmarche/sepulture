<?php

namespace AcMarche\Sepulture\Repository;

use AcMarche\Sepulture\Entity\Cimetiere;
use AcMarche\Sepulture\Entity\Sepulture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sepulture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sepulture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sepulture[]    findAll()
 * @method Sepulture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SepultureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sepulture::class);
    }

    /**
     * @param $criteria
     *
     * @return Sepulture[]
     */
    public function search($criteria)
    {
        $parcelle = $criteria['parcelle'] ?? null;
        $visuel = $criteria['visuel'] ?? null;
        $clef = $criteria['clef'] ?? null;
        $types = $criteria['types'] ?? null;
        $materiaux = $criteria['materiaux'] ?? null;
        $cimetiere = $criteria['cimetiere'] ?? null;
        $sihls = $criteria['sihls'] ?? null;
        $combattant14 = $criteria['combattant14'] ?? null;
        $combattant40 = $criteria['combattant40'] ?? null;
        $social = $criteria['social'] ?? null;
        $guerre = $criteria['guerre'] ?? null;
        $annee = $criteria['annee'] ?? null;

        $qb = $this->createQueryBuilder('sepulture');
        $qb->leftJoin('sepulture.types', 'types', 'WITH');
        $qb->leftJoin('sepulture.materiaux', 'materiaux', 'WITH');
        $qb->leftJoin('sepulture.visuel', 'visuel', 'WITH');
        $qb->leftJoin('sepulture.legal', 'legal', 'WITH');
        $qb->leftJoin('sepulture.defunts', 'defunts', 'WITH');
        $qb->leftJoin('sepulture.cimetiere', 'cimetiere', 'WITH');
        $qb->leftJoin('sepulture.sihls', 'sihls', 'WITH');
        $qb->addSelect('types', 'materiaux', 'defunts', 'sihls', 'legal', 'visuel', 'cimetiere');

        if ($parcelle) {
            $qb->andwhere('sepulture.parcelle LIKE :parcelle')
                ->setParameter('parcelle', '%'.$parcelle.'%');
        }

        if ($visuel) {
            $qb->andWhere('visuel.id = :visuel')
                ->setParameter('visuel', $visuel);
        }

        if ($clef) {
            $qb->andWhere(
                '( sepulture.description LIKE :clef OR sepulture.symbole LIKE :clef OR sepulture.epitaphe LIKE :clef OR sepulture.architectural LIKE :clef OR sepulture.contact LIKE :clef )'
            )
                ->setParameter('clef', '%'.$clef.'%');
        }

        if ($types) {
            $qb->andWhere('types.id = :type')
                ->setParameter('type', $types);
        }

        if ($materiaux) {
            $qb->andWhere('materiaux.id = :mat')
                ->setParameter('mat', $materiaux);
        }

        if ($sihls) {
            $qb->andWhere('sihls.id = :sihl')
                ->setParameter('sihl', $sihls);
        }

        if ($cimetiere) {
            $qb->andWhere('cimetiere = :cim')
                ->setParameter('cim', $cimetiere);
        }

        if ($combattant14) {
            $qb->andWhere('sepulture.combattant14 = :combattant14')
                ->setParameter('combattant14', 1);
        }

        if ($combattant40) {
            $qb->andWhere('sepulture.combattant40 = :combattant40')
                ->setParameter('combattant40', 1);
        }

        if ($guerre) {
            $qb->andWhere('sepulture.guerre = :guerre')
                ->setParameter('guerre', 1);
        }

        if ($social) {
            $qb->andWhere('sepulture.sociale = :social')
                ->setParameter('social', 1);
        }

        if ($annee) {
            $qb->andWhere('defunts.date_deces LIKE :annee')
                ->setParameter('annee', '%'.$annee.'%');
        }

        $qb->orderBy('sepulture.parcelle');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getAllOrderByIdDesc(): array
    {
        return $this->findBy([], ['id' => 'desc']);
    }

    /**
     * @return Sepulture[]
     */
    public function getImportanceHistorique(Cimetiere $cimetiere)
    {
        $qb = $this->createQueryBuilder('sepulture');
        $qb->innerJoin('sepulture.sihls', 'sihls', 'ON');
        $qb->leftJoin('sepulture.types', 'types', 'WITH');
        $qb->leftJoin('sepulture.materiaux', 'materiaux', 'WITH');
        $qb->leftJoin('sepulture.visuel', 'visuel', 'WITH');
        $qb->leftJoin('sepulture.legal', 'legal', 'WITH');
        $qb->leftJoin('sepulture.defunts', 'defunts', 'WITH');
        $qb->leftJoin('sepulture.cimetiere', 'cimetiere', 'WITH');
        $qb->addSelect('types', 'materiaux', 'defunts', 'sihls', 'legal', 'visuel', 'cimetiere');

        $qb->andWhere('cimetiere = :cim')
            ->setParameter('cim', $cimetiere);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return Sepulture[]
     */
    public function getAvant1945(Cimetiere $cimetiere)
    {
        $qb = $this->createQueryBuilder('sepulture');
        $qb->innerJoin('sepulture.sihls', 'sihls', 'WITH');
        $qb->leftJoin('sepulture.types', 'types', 'WITH');
        $qb->leftJoin('sepulture.materiaux', 'materiaux', 'WITH');
        $qb->leftJoin('sepulture.visuel', 'visuel', 'WITH');
        $qb->leftJoin('sepulture.legal', 'legal', 'WITH');
        $qb->leftJoin('sepulture.defunts', 'defunts', 'WITH');
        $qb->leftJoin('sepulture.cimetiere', 'cimetiere', 'WITH');
        $qb->addSelect('types', 'materiaux', 'defunts', 'sihls', 'legal', 'visuel', 'cimetiere');

        $qb->andWhere('cimetiere = :cim')
            ->setParameter('cim', $cimetiere);

        $qb->andWhere('sepulture.guerre = 1');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return Sepulture[]
     */
    public function getIndigents()
    {
        $qb = $this->createQueryBuilder('sepulture');
        $qb->leftJoin('sepulture.types', 'types', 'WITH');
        $qb->leftJoin('sepulture.materiaux', 'materiaux', 'WITH');
        $qb->leftJoin('sepulture.visuel', 'visuel', 'WITH');
        $qb->leftJoin('sepulture.legal', 'legal', 'WITH');
        $qb->leftJoin('sepulture.defunts', 'defunts', 'WITH');
        $qb->leftJoin('sepulture.cimetiere', 'cimetiere', 'WITH');
        $qb->leftJoin('sepulture.sihls', 'sihls', 'WITH');
        $qb->addSelect('types', 'materiaux', 'defunts', 'sihls', 'legal', 'visuel', 'cimetiere');

        $qb->andwhere(
            'sepulture.parcelle LIKE :a OR sepulture.parcelle LIKE :b OR sepulture.parcelle LIKE :c OR sepulture.parcelle LIKE :d'
        )
            ->setParameter('a', 'CCA%')
            ->setParameter('b', 'CCB%')
            ->setParameter('c', 'CCC%')
            ->setParameter('d', 'CCD%');

        $qb->orderBy('sepulture.parcelle');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
