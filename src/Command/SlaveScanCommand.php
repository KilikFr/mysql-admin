<?php

namespace App\Command;

use App\Repository\ServerRepository;
use App\Services\SlaveService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SlaveScanCommand extends Command
{
    protected static $defaultName = 'app:slave:scan';
    protected static $defaultDescription = 'Scan slave(s) status';

    private SlaveService $slaveService;
    private ServerRepository $serverRepository;

    public function __construct(SlaveService $slaveService, ServerRepository $serverRepository)
    {
        parent::__construct();
        $this->slaveService = $slaveService;
        $this->serverRepository = $serverRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('servers', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Servers to scan')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Option to scan all servers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $serversToScan = new ArrayCollection();

        $serverNames = $input->getArgument('servers');

        if ($serverNames) {
            foreach ($serverNames as $serverName) {
                $server = $this->serverRepository->findOneByName($serverName);
                if (null == $server) {
                    $io->error(sprintf('server %s not found', $serverName));

                    return self::FAILURE;
                }
                if ($serversToScan->contains($server)) {
                    $io->error(sprintf('server %s given twice', $serverName));

                    return self::FAILURE;
                }
                $serversToScan->add($server);
            }
        }

        if ($input->getOption('all')) {
            if ($serverNames) {
                $io->error('servers names should not be supplied with --all option');

                return self::FAILURE;
            }
            $serversToScan = $this->serverRepository->findAll();
        }

        if (count($serversToScan) == 0) {
            $io->warning('no servers to scan');

            return self::FAILURE;
        }

        $progress = new ProgressBar($output, count($serversToScan));

        foreach ($serversToScan as $server) {
            try {
                $slaveStatuses = $this->slaveService->showSlaveStatus($server);
                foreach ($slaveStatuses as $slaveStatus) {
                    $io->writeln(
                        sprintf(
                            'channel %s: %d %d',
                            $slaveStatus->getChannelName(),
                            $slaveStatus->getSlaveIoRunning(),
                            $slaveStatus->getSlaveSqlRunning()
                        )
                    );
                }
            } catch (\Exception $e) {
                $io->error(sprintf('error scanning %s: %s', $server->getName(), $e->getMessage()));
            }
        }


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
