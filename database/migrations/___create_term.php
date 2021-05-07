<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Term extends Migration
{
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('term_id');
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->bigInteger('term_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
