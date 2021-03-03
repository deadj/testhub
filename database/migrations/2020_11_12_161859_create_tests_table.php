<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->json('tags');
            $table->text('foreword')->nullable();
            $table->integer('minBalls');
            $table->integer('maxBalls')->nullable();
            $table->integer('minutesLimit')->nullable();
            $table->boolean('showWrongAnswers');
            $table->boolean('publicResults');
            $table->integer('userId');
            $table->boolean('done')->default(0);
            $table->integer('countOfParticipants')->default(0);
            $table->integer('countOfPassed')->default(0);
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
        Schema::dropIfExists('tests');
    }
}
