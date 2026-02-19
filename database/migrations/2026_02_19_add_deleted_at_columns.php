<?php
// database/migrations/2026_02_19_add_deleted_at_columns.php
// Version: NO FOREIGN KEY (Manual SQL already executed)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: Kolom sudah ditambahkan manual via SQL.
     * Migration ini hanya untuk record keeping di migrations table.
     */
    public function up(): void
    {
        // Check if columns already exist (dari manual SQL)
        $logUploadHasColumns = Schema::hasColumn('log_upload', 'deleted_at') 
            && Schema::hasColumn('log_upload', 'deleted_by');
        
        $transaksiHasColumn = Schema::hasColumn('penjualan_transaksi', 'deleted_at');
        
        $detailHasColumn = Schema::hasColumn('penjualan_transaksi_detail', 'deleted_at');
        
        // Jika belum ada, tambahkan (tanpa foreign key)
        if (!$logUploadHasColumns) {
            Schema::table('log_upload', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
                
                // Add index for query performance
                $table->index('deleted_at');
                $table->index('deleted_by');
            });
        }
        
        if (!$transaksiHasColumn) {
            Schema::table('penjualan_transaksi', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable();
                $table->index('deleted_at');
            });
        }
        
        if (!$detailHasColumn) {
            Schema::table('penjualan_transaksi_detail', function (Blueprint $table) {
                $table->timestamp('deleted_at')->nullable();
                $table->index('deleted_at');
            });
        }
        
        // Log success
        \Log::info('Soft delete columns migration completed', [
            'log_upload_ready' => $logUploadHasColumns,
            'transaksi_ready' => $transaksiHasColumn,
            'detail_ready' => $detailHasColumn,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop columns if needed
        Schema::table('log_upload', function (Blueprint $table) {
            if (Schema::hasColumn('log_upload', 'deleted_at')) {
                $table->dropIndex(['deleted_at']);
                $table->dropIndex(['deleted_by']);
                $table->dropColumn(['deleted_at', 'deleted_by']);
            }
        });
        
        Schema::table('penjualan_transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('penjualan_transaksi', 'deleted_at')) {
                $table->dropIndex(['deleted_at']);
                $table->dropColumn('deleted_at');
            }
        });
        
        Schema::table('penjualan_transaksi_detail', function (Blueprint $table) {
            if (Schema::hasColumn('penjualan_transaksi_detail', 'deleted_at')) {
                $table->dropIndex(['deleted_at']);
                $table->dropColumn('deleted_at');
            }
        });
    }
};