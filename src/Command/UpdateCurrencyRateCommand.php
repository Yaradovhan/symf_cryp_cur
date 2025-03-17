<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CurrencyReteService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-currency-rate',
    description: 'Update currency rate'
)]
class UpdateCurrencyRateCommand extends Command
{
    private CurrencyReteService $currencyReteService;

    public function __construct(CurrencyReteService $currencyReteService)
    {
        parent::__construct();
        $this->currencyReteService = $currencyReteService;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $currencies = $this->currencyReteService->fetchCurrencies();
            $this->currencyReteService->saveCurrencies($currencies);
            $output->writeln('Currencies fetched and saved');

            return Command::SUCCESS;
        } catch (\Throwable $ex) {
            $output->writeln($ex->getMessage());
            //todo write to logfile
            return Command::FAILURE;
        }
    }
}