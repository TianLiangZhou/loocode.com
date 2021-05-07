<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TermRelationship extends Migration
{
    public function up()
    {
        Schema::create('term_relationships', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->bigInteger('object_id');
            $table->bigInteger('term_taxonomy_id');
            $table->integer('term_order');
            $table->unique(['object_id', 'term_taxonomy_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('term_relationships');
    }
}
