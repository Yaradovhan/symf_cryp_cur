<?php
declare(strict_types=1);

namespace App\Document\ExchangeCurrencyRate;

interface ExchangeCurrencyRateInterface
{
    public function getBaseCurrency(): string;
    public function setBaseCurrency(string $baseCurrency): ExchangeCurrencyRateInterface;
    public function getCurrency(): string;
    public function setCurrency(string $currency): ExchangeCurrencyRateInterface;
    public function getRate(): float;
    public function setRate(float $rate): ExchangeCurrencyRateInterface;
}