<?php namespace Blade\Migrations\Symfony\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Blade\Migrations\Operation\StatusOperation;

/**
 * Показать список Миграций
 */
class StatusCommand extends Command
{

    /**
     * @var StatusOperation
     */
    protected $operation;

    /**
     * Конструктор
     *
     * @param StatusOperation $operation
     */
    public function __construct(StatusOperation $operation)
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
            ->setName('migrate:status')
            ->setDescription('Show the status of each migration');
    }


    /**
     * Run
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $data = $this->operation->getData();
        if (!$data) {
            $io->error('No migrations found.');
            return;
        }

        $io->table(['', 'ID', 'Date', 'Name'], $data);
    }
}
