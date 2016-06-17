<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // comment id : refer to comments table
            $table->bigInteger('comment_id')->unsigned();
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');

            // attachment file id : refer to media table
            $table->bigInteger('attachment_id')->unsigned();
            $table->foreign('attachment_id')->references('id')->on('media')->onDelete('cascade');

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
        Schema::drop('comment_attachments');
    }
}
