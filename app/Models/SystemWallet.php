<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet as BaseWallet;

class SystemWallet extends BaseWallet
{
    protected $fillable = ['name', 'slug', 'balance'];

}
