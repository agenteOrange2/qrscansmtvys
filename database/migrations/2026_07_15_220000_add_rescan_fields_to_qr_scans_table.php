<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->unsignedSmallInteger('rescan_count')->default(0)->after('bitrix_synced_at');
            $table->timestamp('last_scanned_at')->nullable()->after('rescan_count');
        });
    }

    public function down(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropColumn(['rescan_count', 'last_scanned_at']);
        });
    }
};
