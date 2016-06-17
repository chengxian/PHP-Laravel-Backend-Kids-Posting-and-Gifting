<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');

            // sender id : refer to users table
            $table->integer('sender_id')->unsigned();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');

            // receiver id : refer to users table
            $table->integer('receiver_id')->unsigned();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');

            // receiver id : refer to users table
            $table->integer('child_id')->unsigned()->nullable();
            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');

            /**
             * Push types
             *  1. "invited"                    when the user was invited by another user 
             *  2. "saving_account_submitted"   when the user submit his saving account
             *  3. "saving_account_approved"    when the user's saving account was approved so that parent account is full.
             *  4. "post_liked"                 when user's post was liked by FF user
             *  5. "post_commented"             when user's post was commented by FF user
             *  6. "gift_sent"                  when user send the gift to a child
             *  7. "gift_received"              when user's children received gift from FF user
             *  8. "contribution_sent"          when user send the contribution to a child
             *  9. "contribution_received"      when user's children received contribution from FF user
             *  10. "recurring_payment_setup"   ???
             *  11. "child_followed"            when user's children was followed by FF user.
             *  12. "followed_child_posted"     when the post of following children was posted
             *  13. "micro_deposit"             ???
             */
            $table->string('type', 30);

            // text
            $table->string('text');

            // custom data
            $table->text('custom_data')->nullable();

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
        Schema::drop('notifications');
    }
}
