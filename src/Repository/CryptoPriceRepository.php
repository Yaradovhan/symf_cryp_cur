<?php
declare(strict_types=1);

namespace App\Repository;

use App\Currency\Data;
use App\Document\CryptoPrice;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;
use App\Repository\Interface\CryptoPriceRepositoryInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

class CryptoPriceRepository extends BaseDocumentRepository implements CryptoPriceRepositoryInterface
{
    protected $documentName = CryptoPrice::class;

    public function __construct(
        ManagerRegistry                                 $managerRegistry,
        DocumentManager                                 $documentManager,
        private readonly ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository,
        private readonly Data                           $currencyData
    ) {
        parent::__construct($managerRegistry, $documentManager);
    }

    /**
     * @return CryptoPriceInterface[]
     */
    public function getCollectionResultArrayBySymbol(string $symbol, int $itemsPerPage, int $offset, string $currency = ''): array
    {
        $collectionArray = $this->findBy(['symbol' => strtoupper($symbol)], ['time' => 'DESC'], $itemsPerPage, $offset);

        if ($collectionArray) {
            /** @var ExchangeCurrencyRateInterface $rateData */
            $rateData = $this->exchangeCurrencyRateRepository->getExchangeRateByCurrency($currency);

            /** @var CryptoPriceInterface $item */
            foreach ($collectionArray as $item) {
                $this->currencyData->preparePrice($item, $rateData);
            }
        }

        return $collectionArray;
    }
}
