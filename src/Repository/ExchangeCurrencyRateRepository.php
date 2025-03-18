<?php
declare(strict_types=1);

namespace App\Repository;

use App\Document\ExchangeCurrencyRate;
use App\Repository\Interface\ExchangeCurrencyRateRepositoryInterface;

class ExchangeCurrencyRateRepository extends BaseDocumentRepository implements ExchangeCurrencyRateRepositoryInterface
{
    protected $documentName = ExchangeCurrencyRate::class;

    public function getExchangeRateByCurrency(string $currency)
    {
        return $this->findOneBy(['currency' => $currency]);
    }
}
