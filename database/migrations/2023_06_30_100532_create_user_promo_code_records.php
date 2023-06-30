<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPromoCodeRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_promo_code_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('code_id')->default(0);
            $table->integer('order_id')->default(0);
            $table->integer('original_amount')->default(0)->comment('优惠码实际面值');
            $table->integer('discount')->default(0)->comment('优惠码抵扣面值');
            $table->timestamp('created_at')->nullable()->default(null);
            $table->softDeletes();

            $table->index(['user_id', 'code_id'], 'u_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_promo_code_records');
    }
}
