<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserMeta extends Migration
{
    public function up()
    {
        Schema::create('usermeta', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('umeta_id');
            $table->bigInteger('user_id')->index();
            $table->string('meta_key', 191)->index();
            $table->longText('meta_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usermeta');
    }
}
