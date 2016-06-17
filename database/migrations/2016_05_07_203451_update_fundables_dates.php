<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFundablesDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fundables', function (Blueprint $table) {
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
        if (Schema::hasColumn('fundables', 'deleted_at')) {
            Schema::table('fundables', function ($table) {
                $table->dropColumn('deleted_at');
            });
        }

        if (Schema::hasColumn('fundables', 'created_at')) {
            Schema::table('fundables', function ($table) {
                $table->dropColumn('created_at');
            });
        }

        if (Schema::hasColumn('fundables', 'updated_at')) {
            Schema::table('fundables', function ($table) {
                $table->dropColumn('updated_at');
            });
        }
    }
}
