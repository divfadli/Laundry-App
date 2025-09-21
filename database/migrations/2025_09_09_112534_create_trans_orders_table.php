<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trans_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_customer')->constrained('customers')->onDelete('cascade');
            $table->string('order_code')->unique();
            $table->date('order_date')->nullable();
            $table->date('order_end_date')->nullable();
            $table->tinyInteger('order_status')->default(0); // 0: Baru, 1: Sudah diambil
            $table->integer('order_pay')->default(0);
            $table->integer('order_change')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trans_orders');
    }
};