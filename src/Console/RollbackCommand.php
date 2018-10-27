<?php namespace Blade\Migrations\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Blade\Migrations\Operation\RollbackOperation;
use Blade\Migrations\Symfony\Log\ConsoleOutputLogger;

/**
 * Откатить Миграцию
 */
class RollbackCommand extends Command
{
    protected $signature = 'migrate:rollback
        {--f|force : Skip confirmation}
        {--id=  : Rollback selected migration by ID}
        {--load-file : Read SQL from file, not DB}';


    /**
     * @var RollbackOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param RollbackOperation $operation
     */
    public function __construct(RollbackOperation $operation)
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
            ->setName('migrate:rollback')
            ->setDescription('Rollback migrations')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Skip confirmation')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Rollback selected migration by ID')
            ->addOption('load-file', null, InputOption::VALUE_NONE, 'Read SQL from file, not DB');
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

        $cmd->setForce($input->getOption('force'));

        $helper = $this->getHelper('question');
        $cmd->run(function ($migrationTitle) use ($helper, $input, $output) {
            $question = new ConfirmationQuestion($migrationTitle . ' - run? [y/n]: ', false);
            return (bool) $helper->ask($input, $output, $question);
        }, $input->getOption('id'), $input->getOption('load-file'));
    }
}
