<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("posts", function (Blueprint $table) {
            $table->id();
            $table->foreignId("keyword_id");
            $table->string("category")->nullable();
            $table->string("title")->nullable();
            $table->string('ai_content')->nullable();
            $table->string('ai_title')->nullable();
            $table->string("slug")->nullable();
            $table->string("json_ld")->nullable();
            $table->string("content")->nullable();
            $table->string("markdown")->nullable();
            $table->text("tags")->nullable();
            $table->timestamp("published_at")->nullable();
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
        Schema::dropIfExists("posts");
    }
};
