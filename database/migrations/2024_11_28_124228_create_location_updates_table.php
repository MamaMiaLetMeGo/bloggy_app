<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_updates', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamp('timestamp');
            $table->json('raw_data');
            $table->timestamps();

            $table->index(['device_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_updates');
    }
};