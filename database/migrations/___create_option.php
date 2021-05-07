<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Option extends Migration
{
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('option_id');
            $table->string('option_name', 191)->unique();
            $table->longText('option_value');
            $table->string('autoload', 20)->default("yes");
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
    }
}
