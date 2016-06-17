<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDummyFundingContributionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('funding_contributions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('funding_contributions_user_id_foreign');
			$table->integer('child_id')->unsigned()->index('funding_contributions_child_id_foreign');
			$table->float('amount')->unsigned();
			$table->boolean('is_gift')->default(0);
			$table->string('gift_message')->nullable();

			$table->float('fee_percent')->nullable();
			$table->float('fee_amount')->nullable();
			$table->float('contribution_percent')->nullable();
			$table->float('contribution_amount')->nullable();
			$table->integer('fundable_id')->unsigned()->nullable()->index('funding_contributions_fundable_id_foreign');
			$table->string('status')->default('queued');
			$table->integer('transfers_id')->unsigned()->index()->nullable();
			$table->string('transfers_type')->index()->nullable();

			$table->boolean('is_enabled')->default(1);
			$table->boolean('kf_approved')->default(1);
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('funding_contributions');
	}

}
