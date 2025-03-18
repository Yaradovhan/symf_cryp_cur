<?php
declare(strict_types=1);

namespace App\Repository;

use App\Currency\CurrencyRateData;
use App\Document\CryptoPrice;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Repository\Interface\CryptoPriceRepositoryInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

use function strtoupper;

class CryptoPriceRepository extends BaseDocumentRepository implements CryptoPriceRepositoryInterface
{
    protected $documentName = CryptoPrice::class;

    public function __construct(
        ManagerRegistry                                 $managerRegistry,
        DocumentManager                                 $documentManager,
        private readonly ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository,
        private readonly CurrencyRateData               $currencyData
    ) {
        parent::__construct($managerRegistry, $documentManager);
    }

    /**
     * @inheritDoc
     */
    public function getCollectionResultArrayBySymbol(
        string $symbol,
        int $itemsPerPage,
        int $offset,
        string $currency = ''
    ): array {
        $collectionArray = $this->findBy(['symbol' => strtoupper($symbol)], ['time' => 'DESC'], $itemsPerPage, $offset);

        if ($collectionArray) {
            $rateData = $this->exchangeCurrencyRateRepository->getExchangeRateByCurrency($currency);

            if ($rateData) {
                /** @var CryptoPriceInterface $item */
                foreach ($collectionArray as $item) {
                    $this->currencyData->preparePrice($item, $rateData);
                }
            }
        }

        return $collectionArray;
    }
}
