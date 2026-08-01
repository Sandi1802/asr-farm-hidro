<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bandar_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('bandar_products')->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained('bandar_partners')->onDelete('set null');
            $table->enum('type', ['in', 'out', 'wasted']); // in = beli, out = jual, wasted = busuk/terbuang
            $table->float('quantity');
            $table->decimal('price', 15, 2)->default(0); // Total harga transaksi
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bandar_transactions');
    }
};
