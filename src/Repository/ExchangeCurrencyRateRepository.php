<?php
declare(strict_types=1);

namespace App\Repository;

use App\Document\ExchangeCurrencyRate;

class ExchangeCurrencyRateRepository extends BaseDocumentRepository
{
    protected $documentName = ExchangeCurrencyRate::class;

    public function getExchangeRateByCurrency(string $currency)
    {
        return $this->findOneBy(['currency' => $currency]);
    }
}
