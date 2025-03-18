<?php
declare(strict_types=1);

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Document\CryptoPrice\CryptoPriceInterface;
use App\State\CryptoPriceProvider;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['crypto_price:item']]),
        new GetCollection(
            uriTemplate: 'crypto-price/pair-data/{symbol}/{currency}',
            defaults: [
                'symbol' => 'btc',
                'currency' => 'usd',
            ],
            paginationEnabled: true,
            paginationItemsPerPage: 10,
            normalizationContext: ['groups' => ['crypto_price:list']],
            name: 'crypto_price_data',
            provider: CryptoPriceProvider::class
        )
    ]
)]
#[ODM\Document(collection: "crypto_price", repositoryClass: 'App\Repository\CryptoPriceRepository')]
class CryptoPrice implements CryptoPriceInterface
{
    #[Groups(['crypto_price:item', 'crypto_price:list'])]
    #[ODM\Id(strategy: 'INCREMENT')]
    private ?string $id = null;

    #[Groups(['crypto_price:item', 'crypto_price:list'])]
    #[ODM\Field(type: 'string')]
    private string $symbol;

    #[Groups(['crypto_price:item', 'crypto_price:list'])]
    #[ODM\Field(type: 'float')]
    private float $price;

    #[Groups(['crypto_price:item', 'crypto_price:list'])]
    #[ODM\Field(type: 'date')]
    /**
     * @var null|DateTime
     */
    private ?DateTime $time;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): CryptoPriceInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): CryptoPriceInterface
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): CryptoPriceInterface
    {
        $this->price = $price;

        return $this;
    }

    public function getTime(): DateTime
    {
        return $this->time;
    }

    public function setTime(DateTime $time): CryptoPriceInterface
    {
        $this->time = $time;

        return $this;
    }
}
