<?php
declare(strict_types=1);

namespace App\Exchanges;

readonly class StockExchangeConfig
{
    public function __construct(
        private string $apiKlinesUrl,
        private array  $symbols,
        private string $pairCode,
        private string $interval,
        private int    $limit,
        private array  $mapping = ['time' => 0, 'close_price' => 4]
    ) {
    }

    public function getKlinesUrl()
    {
        return $this->apiKlinesUrl;
    }

    public function getSymbols(): array
    {
        return $this->symbols;
    }


    public function getPairCode(): string
    {
        return $this->pairCode;
    }


    public function getInterval(): string
    {
        return $this->interval;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }

}