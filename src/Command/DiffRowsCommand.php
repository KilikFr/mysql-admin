<?php

namespace App\Command;

use App\DTO\Table;
use App\Repository\ServerRepository;
use App\Services\DiffRowService;
use App\Services\ServerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DiffRowsCommand extends Command
{
    protected static $defaultName = 'app:diff:rows';
    protected static $defaultDescription = 'Compare table\'s rows between two tables';

    private ServerService $serverService;
    private ServerRepository $serverRepository;
    private DiffRowService $diffRowService;

    private SymfonyStyle $io;
    private bool $outputWithFix = false;

    public function __construct(
        ServerService $serverService,
        ServerRepository $serverRepository,
        DiffRowService $diffRowService
    ) {
        parent::__construct();
        $this->serverService = $serverService;
        $this->serverRepository = $serverRepository;
        $this->diffRowService = $diffRowService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('master', InputArgument::REQUIRED, 'Master server name')
            ->addArgument('slave', InputArgument::REQUIRED, 'Slave server name')
            ->addArgument('database', InputArgument::REQUIRED, 'Database name')
            ->addArgument('table', InputArgument::REQUIRED, 'Table name')
            ->addOption('slave-database', null, InputOption::VALUE_REQUIRED, 'Use a different database on slave')
            ->addOption('slave-table', null, InputOption::VALUE_REQUIRED, 'Use a different table on slave')
            ->addOption(
                'where',
                null,
                InputOption::VALUE_REQUIRED,
                'Add a constraint (ex: id < 1234) to select query',
                '1'
            )
            ->addOption('with-fix', null, InputOption::VALUE_NONE, 'Output queries');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $masterName = $input->getArgument('master');
        $master = $this->serverRepository->findOneByName($masterName);
        if (null === $master) {
            $io->error(sprintf('master server %s not found', $masterName));

            return self::FAILURE;
        }

        $slaveName = $input->getArgument('slave');
        $slave = $this->serverRepository->findOneByName($slaveName);
        if (null === $slave) {
            $io->error(sprintf('slave server %s not found', $slaveName));

            return self::FAILURE;
        }

        $masterDatabase = $input->getArgument('database');
        $masterTableName = $input->getArgument('table');

        if ($input->getOption('slave-database')) {
            $slaveDatabase = $input->getOption('slave-database');
        } else {
            $slaveDatabase = $masterDatabase;
        }

        if ($input->getOption('slave-table')) {
            $slaveTable = $input->getOption('slave-table');
        } else {
            $slaveTableName = $masterTableName;
        }

        $masterTable = $this->serverService->describeTable($master, $masterDatabase, $masterTableName);
        if ($masterTable->getPrimary() === null) {
            $io->error('primary key not found');

            return self::FAILURE;
        }

        $slaveTable = $this->serverService->describeTable($slave, $slaveDatabase, $slaveTableName);
        if ($slaveTable->getPrimary() === null) {
            $io->error('primary key not found');

            return self::FAILURE;
        }

        if ($input->getOption('with-fix')) {
            $this->outputWithFix = true;
        }

        $nbDifferences = $this->diffRowService->diffRows(
            $masterTable,
            $slaveTable,
            $input->getOption('where'),
            [$this, 'displayDiff']
        );

        if ($nbDifferences > 0) {
            $io->error(sprintf('%d differences found', $nbDifferences));

            return self::FAILURE;
        } else {
            $io->success('no differences found');

            return Command::SUCCESS;
        }
    }

    public function displayDiff(Table $masterTable, Table $slaveTable, int $result, $masterRow, $slaveRow)
    {
        switch ($result) {
            case DiffRowService::DIFF_MISSING_SLAVE:
                $this->io->writeln(sprintf('# - %s', $masterRow[0]));
                // @todo use $this->outputWithFix option to add INSERT
                break;
            case DiffRowService::DIFF_EQUAL:
                // ignored
                break;
            case DiffRowService::DIFF_MISSING_MASTER:
                $this->io->writeln(sprintf('# + %s', $slaveRow[0]));
                // @todo use $this->outputWithFix option to add DELETE
                break;
            case DiffRowService::DIFF_DIFFERENT:
                $this->io->writeln(sprintf('# ! %s', $masterRow[0]));
                // @todo use $this->outputWithFix option to add UPDATE
                break;
            default:
                throw new \InvalidArgumentException(sprintf('unknown compareRows result %d', $result));
        }
    }
}
