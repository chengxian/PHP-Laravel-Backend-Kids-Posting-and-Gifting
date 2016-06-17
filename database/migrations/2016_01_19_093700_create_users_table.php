<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary ID
            $table->increments('id');
            // Facebook ID
            $table->string('facebook_id')->unique()->index()->nullable();
            // Twitter ID
            $table->string('twitter_id')->unique()->index()->nullable();
            // Instagram ID
            $table->string('instagram_id')->unique()->index()->nullable();
            // first name
            $table->string('first_name', 50)->nullable();
            // last name
            $table->string('last_name', 50)->nullable();
            // email address
            $table->string('email')->unique()->nullable();
            // password
            $table->string('password', 60)->nullable();
            // phone number
            $table->string('phone', 20)->nullable();

            /* address fields */
            // street
            $table->string('street', 100)->nullable();
            // street1
            $table->string('street1', 100)->nullable();
            // city
            $table->string('city', 50)->nullable();
            // state
            $table->string('state', 50)->nullable();
            // country
            $table->string('country', 50)->nullable();
            // postcode
            $table->string('postcode', 10)->nullable();

            // avatar reference id to medias table
            $table->bigInteger('avatar_id')->unsigned()->nullable();
            $table->foreign('avatar_id')->references('id')->on('media')->onDelete('set null');

            // flag if user is parent
            $table->boolean('is_parent')->default(false);

            // flag if user is admin
            $table->boolean('is_admin')->default(false);

            // flag if user verified his email
            $table->boolean('email_verified')->default(false);

            // Kidgifting TOC
            $table->boolean('accepted_kf_toc')->default(false);
            $table->dateTime('accepted_kf_toc_at')->nullable();

            // full user flag to check user passed all steps of registration
            // 1-9 step numbers
            $table->tinyInteger('full_user')->unsigned()->default(0);

            // status codes of user
            // 1: enabled (default)
            // 2: warning
            // 3: suspended
            // 4: blocked
            $table->tinyInteger('status')->unsigned()->default(1);

            /*
             * Emailage
             */
            $table->boolean('emailage_validated')->default(false);
            $table->boolean('emailage_score')->default(-1);
            $table->boolean('emailage_band')->default(-1);

            $table->rememberToken();
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
        Schema::drop('users');
    }
}
