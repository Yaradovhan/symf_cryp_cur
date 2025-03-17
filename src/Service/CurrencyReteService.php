<?php
declare(strict_types=1);

namespace App\Service;

use App\Currency\Data;
use App\Document\ExchangeCurrencyRate;
use App\Repository\ExchangeCurrencyRateRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Throwable;

readonly class CurrencyReteService
{
    public function __construct(
        private DocumentManager                $dm,
        private ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository,
        private Data                           $currencyData
    ) {}

    public function fetchCurrencies(): array
    {
        return $this->currencyData->getLatestRate();
    }

    /**
     * @throws Throwable
     * @throws MongoDBException
     */
    public function saveCurrencies($data): void
    {
        foreach ($data[Data::BASE_CURRENCY_CODE] as $currency => $rateData) {
            if (in_array($currency, $this->currencyData->getEnabledCurrencies(), true)) {
                /** @var \App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface $row */
                $row = $this->exchangeCurrencyRateRepository->findOneBy(['baseCurrency' => Data::BASE_CURRENCY_CODE, 'currency' => $currency]);
                if (!$row) {
                    $row = new ExchangeCurrencyRate();
                    $row->setBaseCurrency(Data::BASE_CURRENCY_CODE);
                    $row->setCurrency($currency);
                }
                $row->setRate($rateData);
                $this->dm->persist($row);
            }
        }

        $this->dm->flush();
        $this->dm->clear();
    }

}
