<?php
declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CryptoPriceRepository;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class CryptoPriceProvider implements ProviderInterface
{
    public function __construct(
        private CryptoPriceRepository $repository,
        private LoggerInterface       $logger
    ) {}

    /**
     * @inheritDoc
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        try {
            $symbol = $uriVariables['symbol'];
            $currency = $uriVariables['currency'];
            $page = $context['filters']['page'] ?? 1;
            $itemsPerPage = isset($context['filters']['itemsPerPage'])
                ? (int)$context['filters']['itemsPerPage']
                : $operation->getPaginationItemsPerPage();
            $offset = ($page - 1) * $itemsPerPage;

            return $this->repository->getCollectionResultArrayBySymbol($symbol, $itemsPerPage, $offset, $currency);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
