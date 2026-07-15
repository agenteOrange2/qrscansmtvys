<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('bitrix_deal_id')->nullable()->after('notas');
            $table->string('bitrix_status', 20)->nullable()->index()->after('bitrix_deal_id');
            $table->string('bitrix_error', 500)->nullable()->after('bitrix_status');
            $table->unsignedTinyInteger('bitrix_attempts')->default(0)->after('bitrix_error');
            $table->timestamp('bitrix_synced_at')->nullable()->after('bitrix_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('scan_groups', function (Blueprint $table) {
            $table->dropColumn([
                'bitrix_deal_id',
                'bitrix_status',
                'bitrix_error',
                'bitrix_attempts',
                'bitrix_synced_at',
            ]);
        });
    }
};
