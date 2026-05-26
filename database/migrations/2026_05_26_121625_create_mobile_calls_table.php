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
        Schema::create('mobile_calls', function (Blueprint $table) {
            $table->id();

            $table->string('db_no')->unique();
            $table->string('campaign_id');

            $table->date('call_date');

            $table->unsignedBigInteger('start_epoch');
            $table->unsignedBigInteger('end_epoch');

            $table->unsignedInteger('length_in_sec');

            $table->string('user');
            $table->string('status_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_calls');
    }
};
