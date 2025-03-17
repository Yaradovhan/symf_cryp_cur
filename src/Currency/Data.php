<?php
declare(strict_types=1);

namespace App\Currency;

use ApiPlatform\Metadata\HttpOperation;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class Data
{
    public const BASE_CURRENCY_CODE = 'usd';
    public function __construct(
        private readonly string              $apiLatestUsdRateUrl,
        private readonly array               $enabledCurrencies,
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getLatestRate($currency = self::BASE_CURRENCY_CODE): array
    {
        try {
            $rate = $this->httpClient->request(HttpOperation::METHOD_GET, $this->prepareUpdateRateUrl($currency));
            return $rate->toArray();

        } catch (Throwable $e) {
            return [];
        }
    }

    private function prepareUpdateRateUrl(string $currency): string
    {
        return str_replace('%1', strtolower($currency), $this->apiLatestUsdRateUrl);
    }

    public function getEnabledCurrencies(): array
    {
        return $this->enabledCurrencies;
    }
}