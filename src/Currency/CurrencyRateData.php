<?php
declare(strict_types=1);

namespace App\Currency;

use ApiPlatform\Metadata\HttpOperation;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

use function str_replace;
use function strtolower;
use function strval;
use function floatval;
use function bcdiv;

readonly class CurrencyRateData
{
    public function __construct(
        private string              $apiLatestUsdRateUrl,
        private HttpClientInterface $httpClient,
        private LoggerInterface     $logger,
        private array               $enabledCurrencies = [],
        private string              $baseCurrency = 'usd'
    ) {}

    /**
     * @param string $method GET/POST
     *
     * @return mixed[][]
     *
     * Fetches the latest exchange rate for the base currency.
     */
    public function getLatestRate(string $method = HttpOperation::METHOD_GET): array
    {
        try {
            $rate = $this->httpClient->request($method, $this->prepareUpdateRateUrl($this->baseCurrency));

            return $rate->toArray();
        } catch (Throwable $e) {
            $this->logger->error($e);

            return [];
        }
    }

    /**
     * @param string $currency usd|eur
     *
     * @return string
     */
    private function prepareUpdateRateUrl(string $currency): string
    {
        return str_replace('%1', strtolower($currency), $this->apiLatestUsdRateUrl);
    }

    public function getEnabledCurrencies(): array
    {
        return $this->enabledCurrencies;
    }

    public function preparePrice(CryptoPriceInterface $item, ExchangeCurrencyRateInterface $rateData): void
    {
        $price = $item->getPrice() * $rateData->getRate();
        $item->setPrice($this->cropFloat(strval($price)));
    }

    private function cropFloat(string $number, ?int $decimals = 2): float
    {
        return floatval(bcdiv($number, '1', $decimals));
    }

    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrency;
    }
}
