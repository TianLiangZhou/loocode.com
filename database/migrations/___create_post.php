<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Post extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('ID');
            $table->bigInteger('post_author')->index();
            $table->dateTime('post_date');
            $table->dateTime('post_date_gmt');
            $table->longText('post_content');
            $table->text('post_title');
            $table->text('post_excerpt');
            $table->string('post_status', 20)->default('publish');
            $table->string('comment_status', 20)->default('open');
            $table->string('ping_status', 20)->default('open');
            $table->string('post_password', 255)->default('');
            $table->string('post_name', 200)->index()->default('');
            $table->text('to_ping');
            $table->text('pinged');
            $table->dateTime('post_modified');
            $table->dateTime('post_modified_gmt');
            $table->longText('post_content_filtered');
            $table->bigInteger('post_parent')->index()->default('0');
            $table->string('guid')->default('');
            $table->integer('menu_order')->default(0);
            $table->string('post_type', 20)->default('post');
            $table->string('post_mime_type', 100)->default('');
            $table->bigInteger('comment_count')->default(0);
            $table->index(['post_type', 'post_status', 'post_date', 'ID'], 'type_status_date_ID');
        });
    }

    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
