<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('')->comment('名称');
            $table->char('mobile', 15)->unique()->default('')->comment('手机号码');
            $table->string('password')->default('')->comment('密码');
            $table->string('avatar')->default('')->comment('头像地址');
            $table->string('email')->default('')->comment('邮箱');
            $table->char('gender', 10)->default('未知')->comment('性别');
            $table->tinyInteger('status')->default(1)->comment('状态，1：正常，2：异常');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');

            $table->datetimes();
            $table->softDeletes();
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
