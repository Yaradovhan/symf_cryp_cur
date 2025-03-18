<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CurrencyReteService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;
use Throwable;

#[AsCommand(
    name: 'app:update-currency-rate',
    description: 'Update currency rate'
)]
#[AsPeriodicTask(frequency: '1 day', schedule: 'default')]
class UpdateCurrencyRateCommand extends Command
{
    public function __construct(
        private readonly CurrencyReteService $currencyReteService,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Currencies fetch start');
            $currencies = $this->currencyReteService->fetchLastCurrenciesRate();
            $output->writeln('Currencies save start');
            $this->currencyReteService->saveCurrencies($currencies);
            $output->writeln('Currencies fetched and saved');

            return Command::SUCCESS;
        } catch (Throwable $ex) {
            $output->writeln($ex->getMessage());
            $this->logger->error($ex->getMessage());

            return Command::FAILURE;
        }
    }
}
