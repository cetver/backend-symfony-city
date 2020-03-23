<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * The "WritableDirNameValidator" class.
 */
class WritableDirNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof WritableDirName) {
            throw new UnexpectedTypeException($constraint, WritableDirName::class);
        }

        if (is_string($value)) {
            $dir = dirname($value);
            if (!is_writable($dir)) {
                $this->context->buildViolation($constraint->message)
                              ->setParameter('{{ directory }}', $dir)
                              ->setCode(WritableDirName::IS_WRITABLE_DIR_NAME_ERROR)
                              ->addViolation();
            }
        }
    }
}
