<?php

namespace App\Repository\Interface;

/**
 * Interface for work with crypto coins in db
 * Defines methods for getting crypto coin rates
 * Defines methods for pagination and filtering
 */
interface CryptoPriceRepositoryInterface
{
    public function getCollectionResultArrayBySymbol(string $symbol, int $itemsPerPage, int $offset, string $currency = ''): array;
}
