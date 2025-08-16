<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnsureCleanDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        try {
            $this->resetDatabaseConnection();
        } catch (\Exception $e) {
            Log::error("Connection reset failed: " . $e->getMessage());
        }
        
        return $next($request);
    }

    private function resetDatabaseConnection(): void
    {
        try {
            // End any existing transactions
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Test connection stability
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            Log::info("Resetting database connection", ['error' => $e->getMessage()]);
            
            // Force complete connection reset
            DB::purge();
            DB::reconnect();
            
            // Verify new connection
            DB::connection()->getPdo();
        }
    }
}