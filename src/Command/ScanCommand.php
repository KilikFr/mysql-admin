<?php

namespace App\Command;

use App\Repository\ServerRepository;
use App\Services\ServerService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanCommand extends Command
{
    protected static $defaultName = 'app:scan';
    protected static $defaultDescription = 'Scan servers(s) status';

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

        if (0 == count($serversToScan)) {
            $io->warning('no servers to scan');

            return self::FAILURE;
        }

        $progress = new ProgressBar($output, count($serversToScan));
        $progress->display();

        foreach ($serversToScan as $server) {
            try {
                $this->serverService->scan($server);
                $progress->advance();
            } catch (\Exception $e) {
                $io->error(sprintf('error scanning %s: %s', $server->getName(), $e->getMessage()));
            }
        }
        $progress->display();

        $io->writeln('');
        $io->success('scan done.');

        return Command::SUCCESS;
    }
}
