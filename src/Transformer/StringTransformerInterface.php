<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * The "StringTransformerInterface" interface.
 */
interface StringTransformerInterface
{
    public function transform(string $value): string;
}
