<?php
declare(strict_types=1);

namespace App\Currency;

use ApiPlatform\Metadata\HttpOperation;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        private array               $enabledCurrencies = [],
        private string              $baseCurrency = 'usd'
    ) {}

    /**
     * Fetches the latest exchange rate for the base currency.
     *
     * @param string $method GET/POST
     *
     * @return mixed[][]
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function getLatestRate(string $method = HttpOperation::METHOD_GET): array
    {
        $response = $this->httpClient->request($method, $this->prepareUpdateRateUrl($this->baseCurrency));

        if ($response->getStatusCode() == Response::HTTP_OK) {
            return $response->toArray();
        } else {
            throw new Exception('Bad API response, status code: ' . $response->getStatusCode());
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

    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrency;
    }
}
