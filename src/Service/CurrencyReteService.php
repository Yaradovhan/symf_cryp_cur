<?php
declare(strict_types=1);

namespace App\Service;

use App\Currency\CurrencyRateData;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Document\ExchangeCurrencyRate;
use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;
use App\Repository\ExchangeCurrencyRateRepository;
use App\Service\Interface\CurrencyReteServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Log\LoggerInterface;
use Throwable;

use function in_array;
use function implode;

readonly class CurrencyReteService implements CurrencyReteServiceInterface
{
    public function __construct(
        private DocumentManager                $dm,
        private ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository,
        private CurrencyRateData               $currencyData,
        private LoggerInterface                $logger
    ) {}

    /**
     * @return mixed[][]
     *
     * Get information about currency from API
     */
    public function fetchLastCurrenciesRate(): array
    {
        return $this->currencyData->getLatestRate();
    }

    /**
     * @var mixed[][] $data ['BTC'=>[...], 'ETH'=>[...]]
     *
     * @throws Throwable
     * @throws MongoDBException
     */
    public function saveCurrencies(array $data): void
    {
        $unsavedCurrencies = [];

        foreach ($data[$this->currencyData->getBaseCurrencyCode()] as $currencyCode => $rateData) {
            if (in_array($currencyCode, $this->currencyData->getEnabledCurrencies(), true)) {
                /** @var ExchangeCurrencyRateInterface $row */
                $row = $this->exchangeCurrencyRateRepository->findOneBy([
                    CryptoPriceInterface::BASE_CURRENCY_KEY => $this->currencyData->getBaseCurrencyCode(),
                    CryptoPriceInterface::CURRENCY_KEY => $currencyCode,
                ]);

                if (!$row) {
                    $row = $this->prepareRow($this->currencyData->getBaseCurrencyCode(), $currencyCode);
                }

                $row->setRate($rateData);
                $this->dm->persist($row);
            } else {
               $unsavedCurrencies[] = $currencyCode;
            }
        }

        $this->logger->info('Unsaved currencies: ' . implode(', ', $unsavedCurrencies));
        $this->dm->flush();
        $this->dm->clear();
    }

    private function prepareRow(string $baseCurrency, string $currencyCode): ExchangeCurrencyRate
    {
        $row = new ExchangeCurrencyRate();
        $row->setBaseCurrency($baseCurrency);
        $row->setCurrency($currencyCode);

        return $row;
    }

    public function cropFloat(float $number, ?int $decimals = 2): float
    {
        return floatval(bcdiv((string)$number, '1', $decimals));
    }
}
