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

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class UserKeyValueStorage implements UserKeyValueStorageInterface
{
    /**
     * @var KeyValueStorageInterface
     */
    private $storage;

    public function __construct(KeyValueStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function get(UserId $userId, string $namespace, string $key): ?string
    {
        return $this->storage->get($namespace, $this->generateUserKey($userId, $key));
    }

    public function set(UserId $userId, string $namespace, string $key, string $value): void
    {
        $this->storage->set($namespace, $this->generateUserKey($userId, $key), $value);
    }

    public function has(UserId $userId, string $namespace, string $key): bool
    {
        return $this->storage->has($namespace, $this->generateUserKey($userId, $key));
    }

    private function generateUserKey(UserId $userId, string $key): string
    {
        return sprintf('%s-%s', $key, (string) $userId);
    }
}
