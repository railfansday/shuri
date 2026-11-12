<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keyword_id');
            $table->string('url');
            $table->string('link');
            $table->string('title');
            $table->string('thumbnail');
            $table->string('size');
            $table->string('desc')->nullable();
            $table->string('filetype')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('domain')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
};
