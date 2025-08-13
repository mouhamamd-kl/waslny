<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\MoneyCode\MoneyCodeRequest;
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
            return ApiResponse::sendResponsePaginated($moneyCodes, MoneyCodeResource::class, trans_fallback('messages.money_code.list', 'Money codes retrieved successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.generic', 'An error occurred. Please try again later'), 500, $e->getMessage());
        }
    }

    public function store(MoneyCodeRequest $request)
    {
        try {
            $moneyCode = $this->moneyCodeService->createMoneyCode($request->validated());
            return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode), trans_fallback('messages.money_code.created', 'Money code created successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Creation failed'), 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        $moneyCode = $this->moneyCodeService->findMoneyCode($id);

        if (!$moneyCode) {
            return ApiResponse::sendResponseError(trans_fallback('messages.money_code.error.not_found', 'Money code not found'), 404);
        }

        return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode->load('rider')), trans_fallback('messages.money_code.retrieved', 'Money code retrieved successfully'));
    }

    public function redeem(Request $request)
    {
        try {
            $request->validate(['code' => 'required|string']);
            /** @var Rider $rider */
            $rider = $request->user();
            $moneyCode = $this->moneyCodeService->redeemMoneyCode($request->input('code'), $rider);
            return ApiResponse::sendResponseSuccess(new MoneyCodeResource($moneyCode), trans_fallback('messages.money_code.redeemed', 'Money code redeemed successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.money_code.error.invalid', 'Invalid or already used money code'), 422, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $moneyCode = $this->moneyCodeService->findMoneyCode($id);

        if (!$moneyCode) {
            return ApiResponse::sendResponseError(trans_fallback('messages.money_code.error.not_found', 'Money code not found'), 404);
        }

        try {
            $this->moneyCodeService->delete($moneyCode->id);
            return ApiResponse::sendResponseSuccess([], trans_fallback('messages.money_code.deleted', 'Money code deleted successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.delete_failed', 'Deletion failed'), 500, $e->getMessage());
        }
    }
}
