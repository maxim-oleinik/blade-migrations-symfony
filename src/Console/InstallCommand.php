<?php namespace Blade\Migrations\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Blade\Migrations\Repository\DbRepository;

/**
 * Создать таблицу Миграций
 */
class InstallCommand extends Command
{
    /**
     * The repository instance.
     *
     * @var DbRepository
     */
    protected $repository;

    /**
     * Конструктор
     *
     * @param  DbRepository $repository
     */
    public function __construct(DbRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }


    /**
     * Config
     */
    protected function configure()
    {
        $this
            ->setName('migrate:install')
            ->setDescription('Create the migration table');
    }


    /**
     * Run
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->repository->install();
        $io->success('Migration table created successfully');
    }
}
