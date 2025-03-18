<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CryptoPriceService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;
use Throwable;

#[AsCommand(name: 'crypto:update-prices')]
#[AsPeriodicTask(frequency: '1 hour', schedule: 'default')]
class UpdateCryptoPricesCommand extends Command
{

    public function __construct(
        private readonly CryptoPriceService $cryptoPriceService,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Crypto prices fetched start');
            $data = $this->cryptoPriceService->fetchSymbolPrices();
            $output->writeln('Crypto prices save start');
            $this->cryptoPriceService->savePrices($data);
            $output->writeln('Crypto prices fetched and saved');
            $this->logger->info('Crypto prices '. implode(', ',array_keys($data)) . ' saved');

            return Command::SUCCESS;
        } catch (Throwable $ex) {
            $output->writeln($ex->getMessage());
            $this->logger->error($ex->getMessage());

            return Command::FAILURE;
        }
    }
}
