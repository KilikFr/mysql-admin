<?php

namespace App\Command;

use App\DTO\TableStatus;
use App\Repository\ServerRepository;
use App\Services\ServerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DiffTablesCommand extends Command
{
    protected static $defaultName = 'app:diff:tables';
    protected static $defaultDescription = 'Compare tables checksum between two servers';

    private ServerService $serverService;
    private ServerRepository $serverRepository;

    public function __construct(ServerService $serverService, ServerRepository $serverRepository)
    {
        parent::__construct();
        $this->serverService = $serverService;
        $this->serverRepository = $serverRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('master', InputArgument::REQUIRED, 'Master server to scan')
            ->addArgument('slave', InputArgument::REQUIRED, 'Slave server to scan')
            ->addArgument('databases', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Databases to scan')
            ->addOption('all-databases', null, InputOption::VALUE_NONE, 'Option to scan all databases')
            ->addOption(
                'ignore-database',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Databases to skip'
            )
            ->addOption(
                'ignore-table',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Tables to skip (full format: database.table)'
            )
            ->addOption('max-scan', null, InputOption::VALUE_REQUIRED, 'Max scan attempts', 5)
            ->addOption('wait-scan', null, InputOption::VALUE_REQUIRED, 'Wait (seconds) between 2 attempts', 5)
            ->addOption('json-fix', null, InputOption::VALUE_NONE, 'Fix MySQL 5.7 checksum json bug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ignoreDatabases = [
            'information_schema',
            'mysql',
            'performance_schema',
            'sys',
        ];

        $io = new SymfonyStyle($input, $output);

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

        $databases = $input->getArgument('databases');

        if ($databases) {
            $databasesToScan = $databases;
        }

        if ($input->getOption('all-databases')) {
            if ($databases) {
                $io->error('databases names should not be supplied with --all-databases option');

                return self::FAILURE;
            }
        }

        if ($input->getOption('ignore-database')) {
            foreach ($input->getOption('ignore-database') as $ignoreDatabase) {
                $ignoreDatabases[] = $ignoreDatabase;
            }
        }

        $io->writeln('scanning master databases');
        $masterDatabases = $this->serverService->showDatabases($master);

        $io->writeln('scanning slave databases');
        $slaveDatabases = $this->serverService->showDatabases($slave);

        if ($input->getOption('all-databases')) {
            $databasesToScan = $masterDatabases;
        }

        // remove ignore databases from scope
        $databasesToScan = array_diff($databasesToScan, $ignoreDatabases);

        /** @var TableStatus[]|array $tablesToScan */
        $tablesToScan = [];
        foreach ($databasesToScan as $databaseToScan) {
            foreach ($this->serverService->showTables($master, $databaseToScan) as $tableToScan) {
                $tableStatus = new TableStatus();
                $tableStatus->setTable($this->serverService->describeTable($master, $databaseToScan, $tableToScan));

                $tablesToScan[] = $tableStatus;
            }
        }
        if (0 == count($tablesToScan)) {
            $io->warning('no tables to scan');

            return self::FAILURE;
        }

        $checksumOptions = 0;
        if ($input->getOption('json-fix')) {
            $checksumOptions |= ServerService::CHECKUM_OPTION_JSON_FIX;
        }

        $maxScan = $input->getOption('max-scan');

        for ($scan = 0; $scan < $maxScan; ++$scan) {
            $io->writeln(sprintf('computing checksum - pass %d / %d', $scan + 1, $maxScan));
            $progress = new ProgressBar($output, count($tablesToScan));
            $progress->display();

            /** @var $slaveTableStatus */
            $slaveTableStatus = [];

            foreach ($tablesToScan as $tableKey => $masterTable) {
                if (!isset($slaveTableStatus[$tableKey])) {
                    $slaveTableStatus[$tableKey] = (clone $masterTable);
                    $slaveTableStatus[$tableKey]->getTable()->setServer($slave);
                }
                $slaveTable = $slaveTableStatus[$tableKey];
                try {
                    $masterTable->setLastChecksum($this->serverService->tableChecksum($masterTable->getTable(), $checksumOptions));
                } catch (\Exception $e) {
                    $io->error(
                        sprintf(
                            'error scanning %s.%s on master: %s',
                            $masterTable->getTable()->getDatabase(),
                            $masterTable->getTable()->getName(),
                            $e->getMessage()
                        )
                    );

                    return self::FAILURE;
                }
                try {
                    $slaveTable->setLastChecksum($this->serverService->tableChecksum($slaveTable->getTable(), $checksumOptions));
                } catch (\Exception $e) {
                    $io->error(
                        sprintf(
                            'error scanning %s.%s on slave: %s',
                            $tableToScan->getTable()->getDatabase(),
                            $tableToScan->getTable()->getName(),
                            $e->getMessage()
                        )
                    );

                    return self::FAILURE;
                }

                // if checksum is OK
                if ($masterTable->getLastChecksum() === $slaveTable->getLastChecksum()) {
                    // remove from list
                    unset($tablesToScan[$tableKey]);
                }

                $progress->advance();
            }
            $progress->display();

            $io->writeln('');

            if (0 == count($tablesToScan)) {
                $io->writeln('all tables are identical');
                break;
            }

            if (count($tablesToScan) > 0) {
                $io->writeln(sprintf('%d tables are different between master and slave', count($tablesToScan)));
                $table = new Table($output);
                $table->setHeaders(['database', 'table', 'master', 'slave']);
                foreach ($tablesToScan as $tableKey => $tableToScan) {
                    $slaveTable = $slaveTableStatus[$tableKey];
                    $table->addRow(
                        [
                            $tableToScan->getTable()->getDatabase(),
                            $tableToScan->getTable()->getName(),
                            $tableToScan->getLastChecksum(),
                            $slaveTable->getLastChecksum(),
                        ]
                    );
                }
                $table->render();

                // only when remaining scans
                if ($scan < $maxScan - 1) {
                    $io->writeln('waiting before next scan');
                    sleep($input->getOption('wait-scan'));
                }
            }
        }

        $io->success('scan done.');

        return Command::SUCCESS;
    }
}
