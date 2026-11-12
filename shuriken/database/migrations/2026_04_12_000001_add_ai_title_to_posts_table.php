<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('posts', 'ai_title')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->string('ai_title')->nullable()->after('ai_content');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('ai_title');
        });
    }
};
