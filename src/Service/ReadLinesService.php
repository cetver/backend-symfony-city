<?php

declare(strict_types=1);

namespace App\Service;

/**
 * The "ReadLinesService" class.
 */
class ReadLinesService
{
    private $handle;

    public function __construct(string $filepath)
    {
        $this->handle = \Safe\fopen($filepath, 'r');
    }

    public function __destruct()
    {
        \Safe\fclose($this->handle);
    }

    public function lines()
    {
        while ($line = fgets($this->handle)) {
            yield $line;
        }
    }
}
