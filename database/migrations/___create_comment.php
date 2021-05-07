<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Comment extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('comment_ID');
            $table->bigInteger('comment_post_ID')->index('comment_post_ID');
            $table->string('comment_author')->default('0');
            $table->string('comment_author_email', 100)->index();
            $table->string('comment_author_url', 200);
            $table->string('comment_author_IP', 100);
            $table->dateTime('comment_date')->useCurrent();
            $table->dateTime('comment_date_gmt')->index()->useCurrent();
            $table->text('comment_content');
            $table->integer('comment_karma')->default('0');
            $table->string('comment_approved', 20)->default('1');
            $table->string('comment_agent', 2550)->default('');
            $table->string('comment_type', 20)->default('comment');
            $table->bigInteger('comment_parent', false)->index();
            $table->bigInteger('user_id', false)->index();
            $table->index(['comment_approved', 'comment_date_gmt',]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }

}
