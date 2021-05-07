<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogOperation extends Migration
{
    public function up()
    {
        Schema::create('log_operations', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id();
            $table->integer('manager_id')->index();
            $table->integer('menu_id');
            $table->text('info');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_operations');
    }
}
