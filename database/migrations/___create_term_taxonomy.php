<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TermTaxonomy extends Migration
{
    public function up()
    {
        Schema::create('term_taxonomy', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_520_ci';
            $table->id('term_taxonomy_id');
            $table->bigInteger('term_id')->default(0);
            $table->string('taxonomy', 32)->default('');
            $table->longText('description');
            $table->bigInteger('parent')->default(0);
            $table->bigInteger('count')->default(0);
            $table->index(['term_id', 'taxonomy']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('term_taxonomy');
    }
}
