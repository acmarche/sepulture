<?php

namespace AcMarche\Sepulture\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UtilisateurEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('plainPassword');
    }

    public function getParent(): ?string
    {
        return UserType::class;
    }
}
