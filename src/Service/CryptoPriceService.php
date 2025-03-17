<?php
declare(strict_types=1);

namespace App\Service;

use ApiPlatform\Metadata\HttpOperation;
use App\Exchanges\StockExchangeConfig;
use App\Factory\CryptoPriceFactory;
use App\Repository\CryptoPriceRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

readonly class CryptoPriceService
{
    public function __construct(
        private HttpClientInterface   $httpClient,
        private DocumentManager       $dm,
        private StockExchangeConfig   $stockExchangeConfig,
        private CryptoPriceRepository $cryptoPriceRepository,
        private CryptoPriceFactory    $cryptoPriceFactory,
    ) {
    }

    public function fetchSymbolPrices(): array
    {
        $symbols = $this->stockExchangeConfig->getSymbols();
        $pairCode = $this->stockExchangeConfig->getPairCode();
        $interval = $this->stockExchangeConfig->getInterval();
        $limit = $this->stockExchangeConfig->getLimit();
        $result = [];

        foreach ($symbols as $symbol) {
            $queryConfig = ['query' => ['symbol' => $symbol . $pairCode, 'interval'=> $interval, 'limit' => $limit]];
            try {
                $response = $this->httpClient->request(HttpOperation::METHOD_GET, $this->stockExchangeConfig->getKlinesUrl(), $queryConfig);
                $result[$symbol] = $response->toArray();
            } catch (Throwable $e) {
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

        foreach ($data as $symbol => $entry) {
            foreach ($entry as $key => $value) {
                $timestamp = (new \DateTime())->setTimestamp($value[$this->stockExchangeConfig->getMapping()['time']] / 1000);
                $existingPrice = $this->cryptoPriceRepository->findOneBy(['symbol' => $symbol, 'time' => $timestamp]);

                if (!$existingPrice) {
                    $countForUpdate++;
                    $cryptoPrice = $this->cryptoPriceFactory->create(
                        $symbol,
                        floatval($value[$this->stockExchangeConfig->getMapping()['close_price']]),
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