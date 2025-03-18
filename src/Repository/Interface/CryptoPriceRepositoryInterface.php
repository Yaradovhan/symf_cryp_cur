<?php

namespace App\Repository\Interface;

use App\Document\CryptoPrice\CryptoPriceInterface;

/**
 * Interface for work with crypto coins in db
 * Defines methods for getting crypto coin rates
 * Defines methods for pagination and filtering
 */
interface CryptoPriceRepositoryInterface
{
    /**
     * @param string $symbol BTC|ETH etc.
     * @param int $itemsPerPage
     * @param int $offset
     * @param string $currency usd|eur etc.
     *
     * @return CryptoPriceInterface[]
     *
     * Get array collection with filtered, sorted items
     */
    public function getCollectionResultArrayBySymbol(
        string $symbol,
        int $itemsPerPage,
        int $offset,
        string $currency = ''
    ): array;
}
