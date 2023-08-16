<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTencentVideoTranscode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tencent_video_transcode', function (Blueprint $table) {
            $table->id();
            $table->string('file_id', 64)->default('');
            $table->string('template_name', 32)->default('')->comment('转码任务名');
            $table->tinyInteger('status')->default(0)->comment('状态[0:等待处理,5:失败,9:成功]');
            $table->timestamps();

            $table->index(['file_id'], 'file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tencent_video_transcode');
    }
}
