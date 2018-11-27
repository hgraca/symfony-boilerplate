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

namespace Acme\App\Infrastructure\Persistence;

use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class TransactionSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_PRIORITY = 30;

    /**
     * @var TransactionServiceInterface
     */
    private $transactionService;

    /**
     * @var int
     */
    private static $priority = self::DEFAULT_PRIORITY;

    public function __construct(
        TransactionServiceInterface $transactionService,
        int $requestTransactionSubscriberPriority = self::DEFAULT_PRIORITY
    ) {
        $this->transactionService = $transactionService;
        self::$priority = $requestTransactionSubscriberPriority;
    }

    /**
     * Return the subscribed events, their methods and possibly their priorities
     * (the higher the priority the earlier the method is called).
     *
     * @see http://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['startTransaction', self::$priority],
            ConsoleEvents::COMMAND => ['startTransaction', self::$priority],

            KernelEvents::RESPONSE => ['finishTransaction', self::$priority],
            ConsoleEvents::TERMINATE => ['finishTransaction', self::$priority],

            // In the case that both the Exception and Response events are triggered, we want to make sure the
            // transaction is rolled back before trying to commit it, so the priority is higher than for the response.
            KernelEvents::EXCEPTION => ['rollbackTransaction', self::$priority + 1],
            ConsoleEvents::ERROR => ['rollbackTransaction', self::$priority + 1],
        ];
    }

    public function startTransaction(): void
    {
        $this->transactionService->startTransaction();
    }

    public function finishTransaction(): void
    {
        // This is is when the ORM writes all staged changes to the DB so we should do this only once in a request,
        // and only if the use case was successful.
        // If we would use a command bus, we would do this in one of its middlewares.
        $this->transactionService->finishTransaction();
    }

    public function rollbackTransaction(): void
    {
        $this->transactionService->rollbackTransaction();
    }
}
