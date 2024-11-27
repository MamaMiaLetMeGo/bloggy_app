<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('author_name');  // For guest comments
            $table->string('author_email')->nullable();
            $table->foreignId('user_id')->nullable(); // For authenticated users
            $table->foreignId('post_id');
            $table->foreignId('parent_id')->nullable(); // For nested replies
            $table->boolean('is_approved')->default(false);
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes(); // For comment moderation
        });
    
        // For comment likes
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id');
            $table->foreignId('user_id')->nullable(); // Allow guest likes with IP
            $table->string('ip_address');
            $table->timestamps();
    
            $table->unique(['comment_id', 'ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('comments');
    }
}
