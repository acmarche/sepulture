<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Repository\CimetiereRepository;
use AcMarche\Sepulture\Repository\SihlRepository;
use AcMarche\Sepulture\Repository\TypeSepultureRepository;
use AcMarche\Sepulture\Repository\VisuelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSepultureType extends AbstractType
{
    private TypeSepultureRepository $typeSepultureRepository;
    private CimetiereRepository $cimetiereRepository;
    private VisuelRepository $visuelRepository;
    private SihlRepository $sihlRepository;

    public function __construct(
        TypeSepultureRepository $typeSepultureRepository,
        CimetiereRepository $cimetiereRepository,
        VisuelRepository $visuelRepository,
        SihlRepository $sihlRepository
    ) {
        $this->typeSepultureRepository = $typeSepultureRepository;
        $this->cimetiereRepository = $cimetiereRepository;
        $this->visuelRepository = $visuelRepository;
        $this->sihlRepository = $sihlRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = $this->typeSepultureRepository->getForSearch();
        $cimetieres = $this->cimetiereRepository->getForSearch();
        $visuels = $this->visuelRepository->getForSearch();
        $sihls = $this->sihlRepository->getForSearch();

        $builder
            ->add(
                'parcelle',
                SearchType::class,
                [
                    'attr' => [
                        'placeholder' => 'Numéro de parcelle',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'visuel',
                ChoiceType::class,
                [
                    'choices' => $visuels,
                    'required' => false,
                    'placeholder' => 'Aspect visuel',
                ]
            )
            ->add(
                'types',
                ChoiceType::class,
                [
                    'placeholder' => 'Type',
                    'choices' => $types,
                    'required' => false,
                ]
            )
            ->add(
                'sihls',
                ChoiceType::class,
                [
                    'placeholder' => 'Intérêt historique',
                    'choices' => $sihls,
                    'required' => false,
                ]
            )
            ->add(
                'cimetiere',
                ChoiceType::class,
                [
                    'placeholder' => 'Tous les cimetières',
                    'choices' => $cimetieres,
                    'required' => false,
                ]
            )
            ->add(
                'clef',
                SearchType::class,
                [
                    'attr' => [
                        'placeholder' => 'Mot clef / Nom défunt',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'combattant14',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => ['title' => 'Ancien combattant 14-18'],
                ]
            )
            ->add(
                'combattant40',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => ['title' => 'Ancien combattant 40-45'],
                ]
            )
            ->add(
                'social',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => ['title' => 'Fonction sociale'],
                ]
            )
            ->add(
                'guerre',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => ['title' => '1er immu avant 45'],
                ]
            ) ->add(
                'annee',
                IntegerType::class,
                [
                    'attr' => [
                        'placeholder' => 'Année de décès',
                    ],
                    'required' => false,
                    'label'=>false
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
