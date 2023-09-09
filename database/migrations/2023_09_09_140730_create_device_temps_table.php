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
        Schema::create('device_temps', function (Blueprint $table) {
            $table->id();
            $table->string('prefix');
            $table->string('uuid')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedTinyInteger('status')->default(251)
                ->comment('251=pending,252=added');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_temps');
    }
};
