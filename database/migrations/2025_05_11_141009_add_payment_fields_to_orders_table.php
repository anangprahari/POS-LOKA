<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('user_id');
            }
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'discount_type')) {
                $table->string('discount_type')->default('fixed')->after('discount');
            }
            if (!Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('discount_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn(['payment_method', 'discount', 'discount_type', 'note']);
        });
    }
};
