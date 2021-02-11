<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('User', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('companyId');
            $table->string('email');
            $table->string('password');
            $table->string('indicMobile');
            $table->string('mobile');
            $table->string('emailValidated')
                ->nullable()
                ->default(null);
            $table->timestamp('emailValidatedExp')
                ->nullable()
                ->default(null);
            $table->string('resetPassword')
                ->nullable()
                ->default(null);
            $table->timestamp('resetPasswordExp')
                ->nullable()
                ->default(null);
            $table->timestamp('lastLogin')
                ->nullable()
                ->default(null);

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
        Schema::dropIfExists('User');
    }
}
