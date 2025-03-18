<?php
declare(strict_types=1);

namespace App\Repository;

use App\Document\ExchangeCurrencyRate;
use App\Document\ExchangeCurrencyRate\ExchangeCurrencyRateInterface;
use App\Repository\Interface\ExchangeCurrencyRateRepositoryInterface;

class ExchangeCurrencyRateRepository extends BaseDocumentRepository implements ExchangeCurrencyRateRepositoryInterface
{
    protected $documentName = ExchangeCurrencyRate::class;

    /**
     * @inheritDoc
     */
    public function getExchangeRateByCurrency(string $currency): ?ExchangeCurrencyRateInterface
    {
        return $this->findOneBy(['currency' => $currency]);
    }
}
