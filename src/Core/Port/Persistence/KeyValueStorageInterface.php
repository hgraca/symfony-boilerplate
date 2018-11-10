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

/**
 * @author Coen Moij
 * @author Kasper Agg
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface KeyValueStorageInterface
{
    public function get(string $namespace, string $key): ?string;

    public function set(string $namespace, string $key, string $value): void;

    public function has(string $namespace, string $key): bool;

    public function remove(string $namespace, string $key): void;
}
