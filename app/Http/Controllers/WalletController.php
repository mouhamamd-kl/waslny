<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\SystemWalletResource;
use App\Services\SystemWalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    protected SystemWalletService $systemWalletService;

    public function __construct(SystemWalletService $systemWalletService)
    {
        $this->systemWalletService = $systemWalletService;
    }

    /**
     * Display the system wallet's balance.
     */
    public function show()
    {
        try {
            $balance = $this->systemWalletService->getBalance();
            return ApiResponse::sendResponseSuccess(['balance' => $balance], 'System wallet balance retrieved successfully.');
        } catch (Exception $e) {
            return ApiResponse::sendResponseError('Failed to retrieve wallet balance: ' . $e->getMessage());
        }
    }

    /**
     * Credit funds to the system wallet.
     */
    public function credit(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
            ]);

            $wallet = $this->systemWalletService->credit($validated['amount']);
            return ApiResponse::sendResponseSuccess(new SystemWalletResource($wallet), 'Funds credited successfully.');
        } catch (ValidationException $e) {
            return ApiResponse::sendResponseError($e->getMessage(), 422);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError('Credit operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Debit funds from the system wallet.
     */
    public function debit(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
            ]);

            $wallet = $this->systemWalletService->debit($validated['amount']);
            return ApiResponse::sendResponseSuccess(new SystemWalletResource($wallet), 'Funds debited successfully.');
        } catch (ValidationException $e) {
            return ApiResponse::sendResponseError($e->getMessage(), 422);
        } catch (Exception $e) {
            return ApiResponse::sendResponseError('Debit operation failed: ' . $e->getMessage());
        }
    }
}
