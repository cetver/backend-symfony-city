<?php

declare(strict_types=1);

namespace App\Factory;

use App\Service\ReadLinesService;

/**
 * The "ReadLinesServiceFactory" class.
 */
class ReadLinesServiceFactory
{
    public function create(string $filepath)
    {
        return new ReadLinesService($filepath);
    }
}
