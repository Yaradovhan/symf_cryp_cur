<?php
declare(strict_types=1);

namespace App\Repository;

use App\Document\CryptoPrice;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\Processors\ResultProcessorInterface;
use App\Repository\Interface\CryptoPriceRepositoryInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

use function strtoupper;

class CryptoPriceRepository extends BaseDocumentRepository implements CryptoPriceRepositoryInterface
{
    protected $documentName = CryptoPrice::class;

    public function __construct(
        ManagerRegistry        $managerRegistry,
        DocumentManager        $documentManager,
        private readonly array $processors
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
            /** @var CryptoPriceInterface $item */
                foreach ($this->processors as $processor) {
                    /** @var ResultProcessorInterface $processor */
                    $collectionArray = $processor->process($collectionArray, ['currency' => $currency]);
                }
        }

        return $collectionArray;
    }
}
