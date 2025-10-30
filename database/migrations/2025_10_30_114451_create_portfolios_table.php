<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stock_symbol');
            $table->decimal('quantity', 10, 4)->default(0.0000);
            $table->timestamps();

            $table->unique(['user_id', 'stock_symbol']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
