<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('description');
            $table->decimal('amount', 10, 4);
            $table->string('mtid')->nullable();
            $table->enum('state', ['PENDING', 'SUCCESS', 'ERROR'])->default('PENDING');
            $table->enum('type', ['PAYPAL', 'PAYSAFECARD', 'SOFORT', 'INTERN'])->default('INTERN');
            $table->enum('typ', ['OWN', 'API'])->default('OWN');
            $table->string('url_ok')->nullable();
            $table->string('url_nok')->nullable();
            $table->string('url_notify')->nullable();
            $table->string('token')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
