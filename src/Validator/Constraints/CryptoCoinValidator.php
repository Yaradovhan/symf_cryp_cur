<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CryptoCoinValidator extends ConstraintValidator
{

    public function validate(mixed $value, Constraint $constraint)
    {
        if (true) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}