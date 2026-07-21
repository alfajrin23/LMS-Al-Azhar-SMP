<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spp_id')->constrained('spps');
            $table->foreignId('orang_tua_id')->constrained('orang_tua');
            $table->date('tanggal_bayar');
            $table->decimal('jumlah', 12, 2);
            $table->string('metode', 50)->default('transfer');
            $table->string('bukti')->nullable();
            $table->enum('status', ['confirmed', 'pending'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
