<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Menu extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id();
            $table->integer('parent_id')->index();
            $table->string('name', 128);
            $table->tinyInteger('hidden')->default(0);
            $table->mediumInteger('weight')->default(0);
            $table->string('class', 64)->default('');
            $table->string('url')->default('');
            $table->string('link', 128)->default('');
            $table->integer('object_id')->default(0);
            $table->dateTime('created_date');
        });
    }
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
