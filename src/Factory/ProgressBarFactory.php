<?php

declare(strict_types=1);

namespace App\Factory;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The "ProgressBarFactory" class.
 */
class ProgressBarFactory
{
    public function create(OutputInterface $output, int $max = 0, float $minSecondsBetweenRedraws = 0.1)
    {
        $progressBar = new ProgressBar($output, $max, $minSecondsBetweenRedraws);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% Elapsed: %elapsed:6s% Memory: %memory:6s%');
        $progressBar->setOverwrite(true);
        $progressBar->start();

        return $progressBar;
    }
}
