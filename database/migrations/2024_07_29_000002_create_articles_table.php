<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            'articles', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->uuid('blog_id');
                $table->foreign('blog_id')
                    ->references('id')
                    ->on('blogs')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->string('title');
                $table->text('content');
                $table->string('article_cover_img')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['title', 'user_id'], 'unique_article_title_per_user');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
