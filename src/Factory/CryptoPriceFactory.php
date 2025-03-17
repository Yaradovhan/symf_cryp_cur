<?php
declare(strict_types=1);

namespace App\Factory;

use App\Document\CryptoPrice\CryptoPriceInterface;

class CryptoPriceFactory
{
    private string $cryptoPriceClass;

    public function __construct(string $cryptoPriceClass)
    {
        if (!is_subclass_of($cryptoPriceClass, CryptoPriceInterface::class)) {
            throw new \InvalidArgumentException("Class $cryptoPriceClass must implement CryptoPriceInterface");
        }

        $this->cryptoPriceClass = $cryptoPriceClass;
    }

    public function create(string $symbol, float $price, \DateTime $time): CryptoPriceInterface
    {
        /** @var CryptoPriceInterface $cryptoPrice */
        $cryptoPrice = new $this->cryptoPriceClass();
        $cryptoPrice->setSymbol($symbol);
        $cryptoPrice->setPrice($price);
        $cryptoPrice->setTime($time);

        return $cryptoPrice;
    }

}
