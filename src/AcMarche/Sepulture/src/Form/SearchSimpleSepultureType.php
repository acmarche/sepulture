<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Repository\CimetiereRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchSimpleSepultureType extends AbstractType
{
    public function __construct(
        private CimetiereRepository $cimetiereRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cimetieres = $this->cimetiereRepository->getForSearch();

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
                'cimetiere',
                ChoiceType::class,
                [
                    'placeholder' => 'Tous les cimetières',
                    'choices' => $cimetieres,
                    'required' => false,
                ]
            );
    }

}
