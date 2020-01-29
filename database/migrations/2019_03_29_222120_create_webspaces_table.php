<?php /** @noinspection ALL */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webspaces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('webhost_id');
            $table->string('plan');
            $table->string('plesk_url')->nullable();
            $table->integer('plesk_id')->nullable();
            $table->string('plesk_username')->nullable();
            $table->string('plesk_password', 255)->nullable();
            $table->integer('plesk_customer_id')->nullable()->default(0);;
            $table->string('configuration', 255)->default('[]');
            $table->string('installed')->default(0);
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
        Schema::dropIfExists('webspaces');
    }
}
