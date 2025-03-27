<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CryptoCoin extends Constraint
{
    public $message = 'Crypto Coin is invalid';
}