<?php

declare(strict_types=1);

/**
 * 生成消息签名
 * @param array $message 消息内容
 * @param string $secret 双方约定的密钥
 * @return string
 */
function generateSign(array $message, string $secret = ''): string
{
    unset($message['sign']);
    
    ksort($message);
    
    $stringToSign = http_build_query($message) . '&secret=' . $secret;
    
    return hash_hmac('sha256', $stringToSign, $secret);
}

/**
 * 验证消息签名
 * 
 * @param array $message 收到的消息
 * @param string $secret 双方约定的密钥
 * @return bool
 */
function verifySign(array $message, string $secret = ''): bool
{
    if (!isset($message['sign'])) {
        return false;
    }
    
    $receivedSign = $message['sign'];
    $calculatedSign = generateSign($message, $secret);
    
    return hash_equals($calculatedSign, $receivedSign);
}

function withAdditional(string $message = 'success', int $code = 0): array
{
    return [
        'code'    => $code,
        'message' => $message,
    ];
}

function generateRandomCode(int $length = 8): string
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $result;
}