<?php

namespace App\Processors;

use App\Document\CryptoPrice\CryptoPriceInterface;

interface ResultProcessorInterface
{
    /**
     * @param CryptoPriceInterface[] $data
     * @param array $additional
     *
     * @return array
     */
    public function process(array $data, array $additional = []): array;
}