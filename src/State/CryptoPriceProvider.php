<?php
declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CryptoPriceRepository;

readonly class CryptoPriceProvider implements ProviderInterface
{
    public function __construct(
        private CryptoPriceRepository $repository,
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        try {
            $symbol = $uriVariables['symbol'];
            $currency = $uriVariables['currency'];
            $page = $context['filters']['page'] ?? 1;
            $itemsPerPage = isset($context['filters']['itemsPerPage']) ? (int)$context['filters']['itemsPerPage'] : $operation->getPaginationItemsPerPage();
            $offset = ($page - 1) * $itemsPerPage;

            return $this->repository->getCollectionBySymbol($symbol, $itemsPerPage, $offset, $currency);
        } catch (\Throwable $exception) {
            return [];
        }
    }
}