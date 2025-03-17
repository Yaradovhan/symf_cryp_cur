<?php
declare(strict_types=1);

namespace App\Repository;

use App\Document\CryptoPrice;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

class CryptoPriceRepository extends BaseDocumentRepository
{
    private ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository;

    public function __construct(ManagerRegistry $managerRegistry, DocumentManager $documentManager, \App\Repository\ExchangeCurrencyRateRepository $exchangeCurrencyRateRepository)
    {
        parent::__construct($managerRegistry, $documentManager);
        $this->exchangeCurrencyRateRepository = $exchangeCurrencyRateRepository;
    }

    protected $documentName = CryptoPrice::class;

    public function getCollectionBySymbol($symbol, $itemsPerPage, $offset, $currency): ?array
    {
        $collection = $this->findBy(['symbol' => strtoupper($symbol)], ['time' => 'DESC'], $itemsPerPage, $offset);
        $rateData = $this->exchangeCurrencyRateRepository->getExchangeRateByCurrency($currency);

        foreach ($collection as $item) {
            $price = $item->getPrice() * $rateData->getRate();
            $item->setPrice($this->cropFloat(strval($price)));
        }

        return $collection;
    }

    private function cropFloat(string $number, $decimals = 2)
    {
        return floatval(bcdiv($number, '1', $decimals));
    }

}
