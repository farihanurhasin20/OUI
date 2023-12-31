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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->double('shipping_charge')->nullable();
            $table->double('order_amount', 10, 2);
            $table->double('total_price', 10, 2);
            $table->enum('payment_status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->enum('current_status', ['Pending', 'Packing', 'Delivery', 'Delivered', 'Canceled'])->default('Pending');
            $table->string('pay_now_qr')->nullable();
            $table->string('customer_sms')->nullable();
            $table->string('rider_sms')->nullable();
            $table->string('invoice_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
