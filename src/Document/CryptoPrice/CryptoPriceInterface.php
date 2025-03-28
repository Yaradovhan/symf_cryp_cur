<?php
declare(strict_types=1);

namespace App\Document\CryptoPrice;

use DateTime;

/**
 * Interface that represents a cryptocurrency price.
 */
interface CryptoPriceInterface
{
    public const BASE_CURRENCY_KEY = 'baseCurrency';

    public const CURRENCY_KEY = 'currency';

    public function getId(): ?string;

    public function setId(?string $id): CryptoPriceInterface;

    public function getSymbol(): string;

    public function setSymbol(string $symbol): CryptoPriceInterface;

    public function getPrice(): float;

    public function setPrice(float $price): CryptoPriceInterface;

    public function getTime(): DateTime;

    public function setTime(DateTime $time): CryptoPriceInterface;
}
