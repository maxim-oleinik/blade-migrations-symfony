<?php namespace Blade\Migrations\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Blade\Migrations\Symfony\Log\ConsoleOutputLogger;
use Blade\Migrations\Operation\MigrateOperation;

/**
 * Накатить Миграции
 */
class MigrateCommand extends Command
{
    /**
     * @var MigrateOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param MigrateOperation $operation
     */
    public function __construct(MigrateOperation $operation)
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
            ->setName('migrate:up')
            ->setDescription('Run migrations')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Skip confirmation')
            ->addOption('auto', null, InputOption::VALUE_NONE, 'Run ALL migrations with auto-remove')
            ->addArgument('name', InputArgument::OPTIONAL, 'The path/name of the migration');
    }


    /**
     * Run
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Выставить МАХ уровень сообщений
        $output->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);

        $cmd = $this->operation;
        // Передать логгер в миграцию для дампа sql
        $cmd->setLogger(new ConsoleOutputLogger($output));

        $cmd->setAuto($input->getOption('auto'));
        $cmd->setForce($input->getOption('force'));

        $helper = $this->getHelper('question');
        $cmd->run(function ($migrationTitle) use ($helper, $input, $output) {
            $question = new ConfirmationQuestion($migrationTitle . ' - run? [y/n]: ', false);
            return (bool) $helper->ask($input, $output, $question);
        }, $input->getArgument('name'));
    }
}
