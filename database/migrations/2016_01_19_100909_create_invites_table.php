<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            // user id to invite other user ( refernce to user table )
            $table->integer('from_user_id')->unsigned();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');

            // user id to be invited by other user ( refernce to user table )
            $table->integer('to_user_id')->unsigned()->nullable();
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('set null');

            // email addres of the invited user
            $table->string('email');

            // invite code to indentify who is invited by whom
            $table->string('invite_code', 100)->index();

            // flag if this invite is accpeted or not
            $table->boolean('accepted')->default(false);

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
        Schema::drop('invites');
    }
}
