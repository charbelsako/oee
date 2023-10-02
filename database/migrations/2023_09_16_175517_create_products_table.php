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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedTinyInteger('is_ok')->default(1);
            $table->unsignedTinyInteger('start')->default(1);
            $table->unsignedTinyInteger('pause')->default(1);
            $table->unsignedTinyInteger('inspection')->default(1);
            $table->unsignedTinyInteger('breakdown')->default(1);
            $table->unsignedInteger('time');
            $table->decimal('value');
            $table->dateTime('registered_at');
            $table->string('unix_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
