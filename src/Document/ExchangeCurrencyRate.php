<?php
declare(strict_types=1);

namespace App\Document;

use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: "exchange_currency_rate", repositoryClass: 'App\Repository\ExchangeCurrencyRateRepository')]
class ExchangeCurrencyRate implements ExchangeCurrencyRateInterface
{
    #[ODM\Id(strategy: 'INCREMENT')]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $baseCurrency;

    #[ODM\Field(type: 'string')]
    private string $currency;

    #[ODM\Field(type: 'float')]
    private float $rate;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): ExchangeCurrencyRateInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): ExchangeCurrencyRateInterface
    {
        $this->baseCurrency = $baseCurrency;

        return $this;

    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): ExchangeCurrencyRateInterface
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): ExchangeCurrencyRateInterface
    {
        $this->rate = $rate;

        return $this;
    }
}
