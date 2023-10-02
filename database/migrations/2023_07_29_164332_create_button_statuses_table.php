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
        Schema::create('button_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedTinyInteger('start');
            $table->unsignedTinyInteger('pause');
            $table->unsignedTinyInteger('inspection');
            $table->unsignedTinyInteger('breakdown');
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
        Schema::dropIfExists('button_statuses');
    }
};
