<?php

namespace App\Processors;

use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Repository\ExchangeCurrencyRateRepository;
use App\Service\CurrencyReteService;

readonly class PreparePriceProcessor implements ResultProcessorInterface
{

    public function __construct(
        private ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository,
        private CurrencyReteService            $currencyReteService,
    ) {}

    /**
     * @inheritDoc
     */
    public function process(array $data, array $additional = []): array
    {
        if ($data && isset($additional['currency'])) {
            foreach ($data as $item) {
                $rateData = $this->exchangeCurrencyRateRepository->getExchangeRateByCurrency($additional['currency']);
                /** @var CryptoPriceInterface $data */
                $price = $item->getPrice() * $rateData->getRate();
                $item->setPrice($this->currencyReteService->cropFloat($price));
            }
        }

        return $data;
    }
}