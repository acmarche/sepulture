<?php

namespace AcMarche\Sepulture\Form\Rw;

use AcMarche\Sepulture\Service\Rw;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class Rw1945Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $status = Rw::a1945();

        $builder
            ->add(
                'monument',
                ChoiceType::class,
                [
                    'choices' => array_flip($status),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }
}
