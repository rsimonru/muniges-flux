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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('export_model', 255);
            $table->json('filters');
            $table->string('path', 255)->nullable();
            $table->integer('size')->default(0);
            $table->string('file_name', 75);
            $table->integer('num_records');
            $table->integer('processed')->default(0);
            $table->dateTime('queued_at')->index()->nullable();
            $table->dateTime('start_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->dateTime('downloaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
