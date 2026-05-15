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
        Schema::create('sports_installations_users_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('installations_id');
            $table->timestamps();

            $table->foreign('users_id')
                ->references('id')
                ->on('users');
            $table->foreign('installations_id')
                ->references('id')
                ->on('sports_installations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sports_installations_users_permissions');
    }
};
