<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\MoneyCodeRequest;
use App\Http\Resources\MoneyCodeResource;
use App\Models\MoneyCode;
use App\Models\Rider;
use App\Services\MoneyCodeService;
use Exception;
use Illuminate\Http\Request;

class MoneyCodeController extends Controller
{
    protected MoneyCodeService $moneyCodeService;

    public function __construct(MoneyCodeService $moneyCodeService)
    {
        $this->moneyCodeService = $moneyCodeService;
    }

    public function index(Request $request)
    {
        try {
            $moneyCodes = $this->moneyCodeService->listMoneyCodes(
                filters: $request->input('filters', []),
                perPage: $request->input('perPage', 10)
            );
            return ApiResponse::sendResponsePaginated($moneyCodes, MoneyCodeResource::class, 'Money codes retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function store(MoneyCodeRequest $request)
    {
        try {
            $moneyCode = $this->moneyCodeService->createMoneyCode($request->validated());
            return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode), 'Money code created successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function show(MoneyCode $moneyCode)
    {
        return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode->load('rider')), 'Money code retrieved successfully');
    }

    public function redeem(Request $request)
    {
        try {
            $request->validate(['code' => 'required|string']);
            /** @var Rider $rider */
            $rider = $request->user();
            $moneyCode = $this->moneyCodeService->redeemMoneyCode($request->input('code'), $rider);
            return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode), 'Money code redeemed successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }

    public function destroy(MoneyCode $moneyCode)
    {
        try {
            $this->moneyCodeService->delete($moneyCode->id);
            return ApiResponse::sendResponseSuccess([], 'Money code deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError($e->getMessage());
        }
    }
}
