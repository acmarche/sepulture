<?php

namespace AcMarche\Sepulture\Form;

use AcMarche\Sepulture\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => ['rows' => 8],
                ]
            )
            ->add(
                'g_recaptcha_response',
                HiddenType::class,
                [
                    'required' => true,
                    'mapped' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Commentaire::class,
            ]
        );
    }
}
