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
        Schema::create('humidities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedTinyInteger('btn1')->default(1);
            $table->unsignedTinyInteger('btn2')->default(1);
            $table->unsignedTinyInteger('btn3')->default(1);
            $table->unsignedTinyInteger('btn4')->default(1);
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
        Schema::dropIfExists('humidities');
    }
};
