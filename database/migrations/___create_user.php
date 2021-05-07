<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('ID');
            $table->string('user_login', 60)->default('')->index();
            $table->string('user_pass')->default('');
            $table->string('user_nicename', 50)->default('')->index();
            $table->string('user_email', 100)->default('')->unique();
            $table->string('user_url', 100)->default('');
            $table->dateTime('user_registered')->useCurrent();
            $table->string('user_activation_key')->default('');
            $table->string('user_status')->default(0);
            $table->string('display_name', 250)->default('');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
