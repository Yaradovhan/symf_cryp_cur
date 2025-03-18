<?php

namespace App\Repository\Interface;

/**
 * Interface for work with exchange currency in db
 * Defines methods for getting currency rates
 */
interface ExchangeCurrencyRateRepositoryInterface
{

    public function getExchangeRateByCurrency(string $currency);
}