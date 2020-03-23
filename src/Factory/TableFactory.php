<?php

declare(strict_types=1);

namespace App\Factory;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The "TableFactory" class.
 */
class TableFactory
{
    public function create(OutputInterface $output, array $headers, array $rows)
    {
        $table = new Table($output);
        $table->setHeaders($headers)->setRows($rows);

        return $table;
    }
}
