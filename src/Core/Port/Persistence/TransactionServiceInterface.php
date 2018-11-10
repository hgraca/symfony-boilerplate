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

namespace Acme\App\Core\Port\Persistence;

interface TransactionServiceInterface
{
    public function startTransaction(): void;

    /**
     * This is is when the ORM writes all staged changes, to the DB
     *      so we should do this only once in a request, and only if the use case command was successful.
     */
    public function finishTransaction(): void;

    public function rollbackTransaction(): void;
}
