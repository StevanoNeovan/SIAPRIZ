<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetMysqlUserContext
{
    /**
     * Set @current_user_id di MySQL session agar trigger log_audit
     * bisa tahu siapa user yang sedang melakukan aksi.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $userId = auth()->user()->id_pengguna;
            // Set MySQL user-defined variable untuk session ini
            DB::statement("SET @current_user_id = {$userId}");
        } else {
            DB::statement("SET @current_user_id = NULL");
        }

        return $next($request);
    }
}