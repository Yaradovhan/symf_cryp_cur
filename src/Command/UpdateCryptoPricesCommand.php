<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CryptoPriceService;
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
    private CryptoPriceService $cryptoPriceService;

    public function __construct(CryptoPriceService $cryptoPriceService)
    {
        parent::__construct();
        $this->cryptoPriceService = $cryptoPriceService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $data = $this->cryptoPriceService->fetchSymbolPrices();
            $this->cryptoPriceService->savePrices($data);
            $output->writeln('Crypto prices fetched and saved');

            return Command::SUCCESS;
        } catch (Throwable $ex) {
            $output->writeln($ex->getMessage());
            //todo write to logfile
            return Command::FAILURE;
        }
    }
}
