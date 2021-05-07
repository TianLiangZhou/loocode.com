<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TermMeta extends Migration
{
    public function up()
    {
        Schema::create('termmeta', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('meta_id');
            $table->bigInteger('term_id')->index();
            $table->string('meta_key')->index();
            $table->longText('meta_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('termmeta');
    }
}
