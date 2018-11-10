<?php

declare(strict_types=1);

/*
 * This file is part of my Symfony boilerplate,
 * following the Explicit Architecture principles.
 *
 * @link https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together
 * @link https://herbertograca.com/2018/07/07/more-than-concentric-layers/
 *
 * (c) Herberto Graça
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Core\Port\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class StaticLoggerFacade
{
    /** @var LoggerInterface */
    private static $logger;

    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function emergency($message, array $context = []): void
    {
        self::getLogger()->emergency($message, $context);
    }

    public static function alert($message, array $context = []): void
    {
        self::getLogger()->alert($message, $context);
    }

    public static function critical($message, array $context = []): void
    {
        self::getLogger()->critical($message, $context);
    }

    public static function error($message, array $context = []): void
    {
        self::getLogger()->emergency($message, $context);
    }

    public static function warning($message, array $context = []): void
    {
        self::getLogger()->warning($message, $context);
    }

    public static function notice($message, array $context = []): void
    {
        self::getLogger()->notice($message, $context);
    }

    public static function info($message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }

    public static function debug($message, array $context = []): void
    {
        self::getLogger()->debug($message, $context);
    }

    public static function log($level, $message, array $context = []): void
    {
        self::getLogger()->log($level, $message, $context);
    }

    private static function getLogger(): LoggerInterface
    {
        if (!self::$logger) {
            self::$logger = new NullLogger();
        }

        return self::$logger;
    }
}
