<?php

namespace App\Logger;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Context\ApplicationContext;

/**
 * @method static void info( string $message , array $context = [])
 * @method static void emergency( string $message , array $context = [] )
 * @method static void alert( string $message , array $context = [] )
 * @method static void critical( string $message , array $context = [] )
 * @method static void error( string $message , array $context = [] )
 * @method static void warning( string $message , array $context = [])
 * @method static void notice( string $message , array $context = [])
 * @method static void debug( string $message , array $context = [] )
 * @method static void log( $level , string $message , array $context = [] )
 */

class UserLogger
{
    public static function __callStatic($name, $arguments)
    {
        $logger = self::get();

        if (method_exists($logger, $name)) {
            return $logger->$name(...$arguments);
        }
    }

    public static function get(string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name, 'user');
    }
}
