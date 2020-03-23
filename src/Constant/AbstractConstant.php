<?php

declare(strict_types=1);

namespace App\Constant;

/**
 * The "AbstractCommand" class.
 */
abstract class AbstractConstant
{
    /**
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $rc = new \ReflectionClass(static::class);

        return $rc->getConstants();
    }

    /**
     * @throws \ReflectionException
     */
    public function toList(string $separator = ', '): string
    {
        return implode($separator, $this->toArray());
    }
}
