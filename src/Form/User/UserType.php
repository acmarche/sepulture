<?php

namespace AcMarche\Sepulture\Form\User;

use AcMarche\Sepulture\Entity\User;
use AcMarche\Sepulture\Security\SecurityData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = SecurityData::getRoles();

        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'plainPassword',
                TextType::class,
                [
                    'label' => 'Mot de passe',
                    'attr' => ['autocomplete' => 'new-password'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
