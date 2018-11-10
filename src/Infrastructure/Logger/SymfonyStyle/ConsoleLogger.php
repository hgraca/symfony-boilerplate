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

namespace Acme\App\Infrastructure\Logger\SymfonyStyle;

use Hgraca\ContextMapper\Core\Port\Logger\UnknownLoggerLevelException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function in_array;
use function json_encode;

final class ConsoleLogger implements LoggerInterface
{
    private const DEFAULT_LOGGING_LEVEL_MAP = [
        OutputInterface::VERBOSITY_QUIET => [],
        OutputInterface::VERBOSITY_NORMAL => ['info', 'error', 'critical', 'alert', 'emergency'],
        OutputInterface::VERBOSITY_VERBOSE => ['warning', 'info', 'error', 'critical', 'alert', 'emergency'],
        OutputInterface::VERBOSITY_VERY_VERBOSE => ['notice', 'info', 'error', 'critical', 'alert', 'emergency'],
        OutputInterface::VERBOSITY_DEBUG => ['debug', 'notice', 'warning', 'info', 'error', 'critical', 'alert', 'emergency'],
    ];

    /**
     * @var SymfonyStyle
     */
    private $io;

    private $loggingLevelMap;

    public function __construct(SymfonyStyle $io, array $loggingLevelMap = self::DEFAULT_LOGGING_LEVEL_MAP)
    {
        $this->io = $io;
        $this->loggingLevelMap = $loggingLevelMap;
    }

    public function emergency($message, array $context = []): void
    {
        if (!$this->shouldLog('emergency')) {
            return;
        }
        $this->io->error($this->writeMessage($message, $context));
    }

    public function alert($message, array $context = []): void
    {
        if (!$this->shouldLog('alert')) {
            return;
        }
        $this->io->error($this->writeMessage($message, $context));
    }

    public function critical($message, array $context = []): void
    {
        if (!$this->shouldLog('critical')) {
            return;
        }
        $this->io->error($this->writeMessage($message, $context));
    }

    public function error($message, array $context = []): void
    {
        if (!$this->shouldLog('error')) {
            return;
        }
        $this->io->error($this->writeMessage($message, $context));
    }

    public function warning($message, array $context = []): void
    {
        if (!$this->shouldLog('warning')) {
            return;
        }
        $this->io->warning($this->writeMessage($message, $context));
    }

    public function notice($message, array $context = []): void
    {
        if (!$this->shouldLog('notice')) {
            return;
        }
        $this->io->caution($this->writeMessage($message, $context));
    }

    public function info($message, array $context = []): void
    {
        if (!$this->shouldLog('')) {
            return;
        }
        $this->io->note($this->writeMessage($message, $context));
    }

    public function debug($message, array $context = []): void
    {
        if (!$this->shouldLog('debug')) {
            return;
        }
        $this->io->comment($this->writeMessage($message, $context));
    }

    public function log($level, $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        switch ($level) {
            case 'emergency':
                $this->emergency($message, $context);
                break;
            case 'alert':
                $this->alert($message, $context);
                break;
            case 'critical':
                $this->critical($message, $context);
                break;
            case 'error':
                $this->error($message, $context);
                break;
            case 'warning':
                $this->warning($message, $context);
                break;
            case 'notice':
                $this->notice($message, $context);
                break;
            case 'info':
                $this->info($message, $context);
                break;
            case 'debug':
                $this->debug($message, $context);
                break;
            default:
                throw new UnknownLoggerLevelException($level);
        }
    }

    private function shouldLog(string $level): bool
    {
        return in_array($level, $this->loggingLevelMap[$this->io->getVerbosity()], true);
    }

    private function writeMessage(string $message, array $context): string
    {
        return $message . "\nContext:\n" . json_encode($context);
    }
}
