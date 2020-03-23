<?php

declare(strict_types=1);

namespace App\Transformer;

/**
 * The "ShortMd5" class.
 */
class ShortMd5Transformer implements StringTransformerInterface
{
    public function transform(string $value): string
    {
        $md5 = md5($value);

        return substr($md5, 0, 6);
    }
}
