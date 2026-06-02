<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('payment_ref');
            $table->string('delivery_status')->default('en_cours')->after('status');
            $table->timestamp('delivered_at')->nullable()->after('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['notes', 'delivery_status', 'delivered_at']);
        });
    }
};
