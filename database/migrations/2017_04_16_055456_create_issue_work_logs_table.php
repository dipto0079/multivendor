<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueWorkLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_work_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('issue_id');
            $table->integer('logged_by');
            $table->string('comment');
            $table->string('time_spent');
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
        Schema::dropIfExists('issue_work_logs');
    }
}
