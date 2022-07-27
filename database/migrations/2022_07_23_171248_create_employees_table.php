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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->required();
            $table->string('nik',16)->required();
            $table->string('email',50)->required();
            $table->string('phone',14)->required();
            $table->string('nip',25)->nullable();
            $table->foreignId('position_id')->required()->constrained('positions');
            $table->integer('main_salary')->required()->default(0);
            $table->foreignId('account_bank_id')->required()->constrained('banks');
            $table->string('account_name')->required();
            $table->integer('account_number')->required()->default(0);
            $table->integer('workdays')->required()->default(24);
            $table->boolean('parking_status')->nullable()->default(false);
            $table->integer('parking_amount')->nullable()->default(0);
            $table->integer('insurance_amount')->nullable()->default(0);
            $table->integer('bpjs_kes_amount')->nullable()->default(0);
            $table->integer('bpjs_kenaker_amount')->nullable()->default(0);
            $table->integer('tax_amount')->nullable()->default(0);
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
        Schema::dropIfExists('employees');
    }
};
