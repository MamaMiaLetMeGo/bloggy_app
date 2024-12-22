<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            // Make user_id nullable and add email field
            $table->foreignId('user_id')->nullable()->change();
            $table->string('email')->nullable()->unique()->after('user_id');
            $table->timestamp('verified_at')->nullable()->after('email');
            
            // Make subscription types nullable for guest subscribers
            $table->boolean('travel_updates')->nullable()->change();
            $table->boolean('sailing_updates')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->foreignId('user_id')->change();
            $table->dropColumn(['email', 'verified_at']);
            $table->boolean('travel_updates')->change();
            $table->boolean('sailing_updates')->change();
        });
    }
};
