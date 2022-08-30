<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_update_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->required()->constrained('employees');
            $table->foreignId('month_id')->required()->constrained('months');
            $table->string('year', 4)->required()->default(date('Y'));
            $table->string('application')->nullable();
            $table->smallInteger('achievements')->nullable()->default(0);
            $table->smallInteger('leave_days')->required()->default(0);
            $table->smallInteger('late_morning')->required()->default(0);
            $table->smallInteger('late_evening')->required()->default(0);
            $table->smallInteger('late_minutes')->required()->default(0);
            $table->integer('allowance')->nullable()->default(0);
            $table->integer('additional_insentives')->nullable()->default(0);
            $table->integer('additional_insentives_tax')->nullable()->default(0);
            $table->integer('bpjs_kes_amount')->nullable()->default(0);
            $table->integer('bpjs_kenaker_amount')->nullable()->default(0);
            $table->integer('loan')->nullable()->default(0);
            $table->integer('other_deduction')->nullable()->default(0);
            $table->integer('tax_amount')->nullable()->default(0);
            $table->integer('total_salary')->nullable()->default(0);
            $table->text('notes')->nullable()->default('-');
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
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
        Schema::dropIfExists('monthly_data');
    }
};
