<?php

declare(strict_types=1);

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * The "WritableDirName" class.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class WritableDirName extends Constraint
{
    const IS_WRITABLE_DIR_NAME_ERROR = '2322db7e-c430-4682-ada6-f1affe8458ca';
    protected static $errorNames = [
        self::IS_WRITABLE_DIR_NAME_ERROR => 'IS_WRITABLE_DIR_NAME_ERROR',
    ];
    public $message = 'The "{{ directory }}" directory is not writable.';
}
