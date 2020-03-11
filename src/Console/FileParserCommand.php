<?php declare(strict_types=1);

namespace App\Console;

use App\Services\Parser;
use App\Services\Cleaner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileParserCommand
{
    private Parser $parser;

    public function __construct(Cleaner $cleaner, Parser $parser)
    {
        $this->parser = $parser;

        $cleaner->clearRepositories();
    }

    public function __invoke(InputInterface $input, OutputInterface $output) : ?int
    {
        $file = $input->getArgument('file');
        $output->writeln(sprintf('Подождите, идет обработка файла <info>%s</info>', $file));

        return $this->parser->__invoke($file);
    }
}
