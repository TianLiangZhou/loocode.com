<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Role extends Migration
{

    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id();
            $table->string('name', 64)->index();
            $table->dateTime('created_date')->useCurrent();
            $table->dateTime('updated_date')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
