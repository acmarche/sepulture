<?php

namespace AcMarche\Sepulture\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OnlyYear extends Constraint
{
    public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[a-zA-Za0-9]+$/', $value, $matches)) {
            $this->context->addViolation(
                    $constraint->message, ['%string%' => $value]
            );
        }
    }
}
