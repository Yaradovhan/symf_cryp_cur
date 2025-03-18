<?php
declare(strict_types=1);

namespace App\Document\ExchangeCurrencyRate;

/**
 * Interface that represents a currency exchange rate.
 */
interface ExchangeCurrencyRateInterface
{
    public function getId(): ?string;

    public function setId(?string $id): ExchangeCurrencyRateInterface;

    public function getBaseCurrency(): string;

    public function setBaseCurrency(string $baseCurrency): ExchangeCurrencyRateInterface;

    public function getCurrency(): string;

    public function setCurrency(string $currency): ExchangeCurrencyRateInterface;

    public function getRate(): float;

    public function setRate(float $rate): ExchangeCurrencyRateInterface;

}