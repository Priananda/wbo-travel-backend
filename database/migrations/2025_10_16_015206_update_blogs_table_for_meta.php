<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'category')) {
                $table->string('category')->default('Uncategorized');
            }
            if (!Schema::hasColumn('blogs', 'author_email')) {
                $table->string('author_email')->nullable();
            }
            if (!Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
            if (!Schema::hasColumn('blogs', 'comments_count')) {
                $table->integer('comments_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['category', 'author_email', 'published_at', 'comments_count']);
        });
    }
};
