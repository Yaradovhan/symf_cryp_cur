<?php
declare(strict_types=1);

namespace App\Factory;

use App\Document\CryptoPrice\CryptoPriceInterface;
use DateTime;

readonly class CryptoPriceFactory
{
    public function __construct(
        private CryptoPriceInterface $cryptoPriceClass
    ) {}

    public function create(string $symbol, float $price, DateTime $time): CryptoPriceInterface
    {
        /** @var CryptoPriceInterface $cryptoPrice */
        $cryptoPrice = new $this->cryptoPriceClass();
        $cryptoPrice->setSymbol($symbol);
        $cryptoPrice->setPrice($price);
        $cryptoPrice->setTime($time);

        return $cryptoPrice;
    }
}
