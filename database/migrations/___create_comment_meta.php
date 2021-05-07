<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CommentMeta extends Migration
{
    public function up()
    {
        Schema::create('commentmeta', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('meta_id');
            $table->bigInteger('comment_id')->default('0')->index();
            $table->string('meta_key', 255)->index();
            $table->longText('meta_value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('commentmeta');
    }
}
