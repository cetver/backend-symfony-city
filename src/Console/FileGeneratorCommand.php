<?php declare(strict_types=1);

namespace App\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\Generator;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;

class FileGeneratorCommand
{
    private Generator $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function __invoke(InputInterface $input, OutputInterface $output) : void
    {
        $lines = (int) $input->getOption('lines');

        if ($lines <= 0) {
            $helper = new SymfonyQuestionHelper();
            $question = new Question('Сколько строк необходимо сгенерировать?');
            $lines = (int)$helper->ask($input, $output, $question);
            if ($lines <= 0) {
                throw new \InvalidArgumentException(sprintf(
                    'Указано недопустимое число строк: %d',
                    $lines
                ));
            }
        }

        $file = $input->getArgument('file');
        $output->writeln(sprintf('Подождите, идет генерация файла <info>%s</info>', $file));

        $size = $this->generator->__invoke($file, $lines);

        if ($size !== null) {
            $output->writeln(sprintf(
                'Спасибо за ожидание. Итоговый размер файла: <info>%d байт</info>', $size
            ));
        } else {
            $output->writeln('<error>Во время генерации видимо произошла ошибка</error>');
        }
    }
}
