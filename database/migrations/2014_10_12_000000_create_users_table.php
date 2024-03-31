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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('password');
            $table->char("onetime_token", 6)->nullable(); // ワンタイムトークン
            $table->dateTime("onetime_expiration")->nullable(); // ワンタイムトークンの有効期限
            $table->tinyInteger("status")->default(0)->comment('1:本登録');
            $table->string('google_id')->nullable();
            $table->unique(['email', 'status']);//ユニーク制約違反→違反コード1062
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
