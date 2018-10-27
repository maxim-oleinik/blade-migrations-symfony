<?php namespace Blade\Migrations\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Blade\Migrations\Operation\MakeOperation;

/**
 * Создать файл миграции
 */
class MakeCommand extends Command
{
    /**
     * @var MakeOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param MakeOperation $operation
     */
    public function __construct(MakeOperation $operation)
    {
        $this->operation = $operation;
        parent::__construct();
    }


    /**
     * Config
     */
    protected function configure()
    {
        $this
            ->setName('migrate:make')
            ->setDescription('Create the migration file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration');
    }


    /**
     * Run
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->success($this->operation->run($input->getArgument('name')));
    }
}
