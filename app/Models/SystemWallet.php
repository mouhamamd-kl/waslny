<?php

namespace App\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemWallet extends Model implements Wallet
{
    use HasFactory, HasWallet;

    protected $fillable = ['name'];

    protected static function booted()
    {
        static::deleting(function (SystemWallet $systemWallet) {
            if ($systemWallet->transactions()->exists() || $systemWallet->transfers()->exists()) {
                throw new \Exception('Cannot delete a system wallet that has transactions or transfers.');
            }
        });
    }
}
