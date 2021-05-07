<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Permission extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id();
            $table->integer('role_id')->index();
            $table->integer('menu_id');
            $table->dateTime('created_date')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
