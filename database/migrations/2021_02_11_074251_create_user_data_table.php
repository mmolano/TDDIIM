<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('UserData', function (Blueprint $table) {
            $table->bigInteger('userId');
            $table->string('firstName');
            $table->string('lastName');
            $table->date('dateOfBirth')
                ->nullable()
                ->default(null);
            $table->integer('gender');
            $table->string('language')
                ->default('fr_FR');

            $table->primary(['userId']);

            $table->timestamp('createdAt')
                ->useCurrent();
            if (env('APP_ENV') === 'testing') {
                $table->timestamp('updatedAt')
                    ->useCurrent();
            } else {
                $table->timestamp('updatedAt')
                    ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('UserData');
    }
}
