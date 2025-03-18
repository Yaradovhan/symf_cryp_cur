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
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;
use function floatval;

readonly class CryptoPriceService implements CryptoPriceServiceInterface
{
    public function __construct(
        private HttpClientInterface   $httpClient,
        private DocumentManager       $dm,
        private StockExchangeConfig   $stockExchangeConfig,
        private CryptoPriceRepository $cryptoPriceRepository,
        private CryptoPriceFactory    $cryptoPriceFactory,
        private LoggerInterface $logger
    ) {}

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

            try {
                $response = $this->httpClient->request(HttpOperation::METHOD_GET, $this->stockExchangeConfig->getKlinesUrl(), $queryConfig);
                $result[$symbol] = $response->toArray();
            } catch (Throwable $e) {
                $this->logger->error($e->getMessage());
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
        $timeKey = $this->stockExchangeConfig->getMapping()['time'] ?? 0;
        $priceKey = $this->stockExchangeConfig->getMapping()['close_price'] ?? 4;

        foreach ($data as $symbol => $entry) {
            foreach ($entry as $key => $value) {
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
