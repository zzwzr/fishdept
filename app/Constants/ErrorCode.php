<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum ErrorCode: int
{
    use EnumConstantsTrait;

    // ========== 4xx 客户端错误 ==========
    #[Message("未授权，请登录！")]
    case UNAUTHORIZED = 401;

    #[Message("禁止访问！")]
    case FORBIDDEN = 403;

    #[Message("资源不存在！")]
    case NOT_FOUND = 404;

    #[Message("请求方法不允许！")]
    case METHOD_NOT_ALLOWED = 405;

    #[Message("参数验证错误！")]
    case UNPROCESSABLE_ENTITY = 422;

    #[Message("资源已存在，发生冲突！")]
    case CONFLICT = 409;

    // ========== 5xx 服务端错误 ==========
    #[Message("Server Error！")]
    case SERVER_ERROR = 500;

    #[Message("服务不可用，请稍后重试！")]
    case SERVICE_UNAVAILABLE = 503;

    #[Message("操作失败")]
    case BUSINESS_ERROR = 10000;
}
