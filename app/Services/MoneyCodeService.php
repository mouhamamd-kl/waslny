<?php

namespace App\Services;

use App\Models\MoneyCode;
use App\Models\Rider;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class MoneyCodeService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new MoneyCode);
    }

    public function createMoneyCode(array $data): MoneyCode
    {
        if (empty($data['code'])) {
            $data['code'] = MoneyCode::generateCode();
        }

        return $this->create($data);
    }

    public function redeemMoneyCode(string $code, Rider $rider): MoneyCode
    {
        $moneyCode = $this->getMoneyCodeByCode($code);

        if (!$moneyCode || $moneyCode->used_at) {
            throw new Exception('Invalid or already used money code.');
        }

        $rider->deposit($moneyCode->value);

        $moneyCode->update([
            'rider_id' => $rider->id,
            'used_at' => now(),
        ]);

        return $moneyCode;
    }

    public function invalidateMoneyCode(string $code): MoneyCode
    {
        $moneyCode = $this->getMoneyCodeByCode($code);

        if (!$moneyCode) {
            throw new Exception('Money code not found.');
        }

        $moneyCode->update(['used_at' => now()]);

        return $moneyCode;
    }

    public function getMoneyCodeByCode(string $code): ?MoneyCode
    {
        return $this->model->where('code', $code)->first();
    }

    public function listMoneyCodes(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->paginatedList(filters: $filters, perPage: $perPage);
    }
}
