<?php
declare(strict_types=1);

namespace App\Service;

use ApiPlatform\Metadata\HttpOperation;
use App\Exchanges\StockExchangeConfig;
use App\Factory\CryptoPriceFactory;
use App\Repository\CryptoPriceRepository;
use App\Service\Interface\CryptoPriceServiceInterface;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

use function floatval;

readonly class CryptoPriceService implements CryptoPriceServiceInterface
{
    private const BINANCE_TIME_KEY = 0;
    private const BINANCE_CLOSE_PRICE_KEY = 4;

    public function __construct(
        private HttpClientInterface   $httpClient,
        private DocumentManager       $dm,
        private StockExchangeConfig   $stockExchangeConfig,
        private CryptoPriceRepository $cryptoPriceRepository,
        private CryptoPriceFactory    $cryptoPriceFactory
    ) {}

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function fetchSymbolPrices(): array
    {
        $symbols = $this->stockExchangeConfig->getSymbols();
        $pairCode = $this->stockExchangeConfig->getPairCode();
        $interval = $this->stockExchangeConfig->getInterval();
        $limit = $this->stockExchangeConfig->getLimit();
        $result = [];
        $defaultParams = ['interval'=> $interval, 'limit' => $limit];

        foreach ($symbols as $symbol) {
            $queryConfig = ['query' => ['symbol' => $symbol . $pairCode] + $defaultParams];
            $response = $this->httpClient->request(
                HttpOperation::METHOD_GET,
                $this->stockExchangeConfig->getKlinesUrl(),
                $queryConfig
            );

            if ($response->getStatusCode() == Response::HTTP_OK) {
                $result[$symbol] = $response->toArray();
            } else {
                throw new Exception('Bad API response, status code: ' . $response->getStatusCode());
            }
        }

        return $result;
    }

    /**
     * @throws Throwable
     * @throws MongoDBException
     */
    public function savePrices(array $data): void
    {
        $countForUpdate = 0;
        $mapping = $this->stockExchangeConfig->getMapping();
        $timeKey = $mapping['time'] ?? self::BINANCE_TIME_KEY;
        $priceKey = $mapping['close_price'] ?? self::BINANCE_CLOSE_PRICE_KEY;

        foreach ($data as $symbol => $entry) {
            foreach ($entry as $value) {
                $timestamp = (new DateTime())->setTimestamp($value[$timeKey] / 1000);
                $existingPrice = $this->cryptoPriceRepository->findOneBy(['symbol' => $symbol, 'time' => $timestamp]);

                if (!$existingPrice) {
                    $countForUpdate++;
                    $price = floatval($value[$priceKey]);
                    $cryptoPrice = $this->cryptoPriceFactory->create(
                        $symbol,
                        $price,
                        $timestamp
                    );
                    $this->dm->persist($cryptoPrice);
                }
            }
        }

        if ($countForUpdate) {
            $this->dm->flush();
            $this->dm->clear();
        }
    }
}
