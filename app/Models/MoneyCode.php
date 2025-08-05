<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MoneyCode extends Model
{
    use HasFactory;
    use FilterScope, ActiveScope;
    protected $guarded = ['id'];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function scopeUsed(Builder $query): Builder
    {
        return $query->whereNotNull('used_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('used_at');
    }

    public static function generateCode(int $length = 10): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
