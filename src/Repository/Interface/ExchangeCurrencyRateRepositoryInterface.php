<?php

namespace App\Repository\Interface;

use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;

/**
 * Interface for work with exchange currency in db
 * Defines methods for getting currency rates
 */
interface ExchangeCurrencyRateRepositoryInterface
{
    /**
     * @param string $currency
     *
     * @return ExchangeCurrencyRateInterface|null
     *
     * Get exchange rate by currency
     */
    public function getExchangeRateByCurrency(string $currency): ?ExchangeCurrencyRateInterface;
}