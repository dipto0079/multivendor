<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('project_id');
            $table->integer('label_id');
            $table->string('issue_summery');
            $table->string('issue_description');
            $table->string('issue_type');
            $table->string('issue_reporter');
            $table->string('issue_priority');
            $table->string('issue_original_estimate');
            $table->string('issue_remaining_estimate');
            $table->string('issue_due_date');
            $table->string('issue_link');
            $table->string('issue_assignee');
            $table->string('issue_resolution');
            $table->string('issue_status');
            $table->string('parent_issue');
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
        Schema::dropIfExists('issues');
    }
}
