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

namespace Acme\App\Infrastructure\Persistence\Doctrine\Type;

use Hgraca\PhpExtension\Uuid\Uuid;

/**
 * This class is here just as an example of how to implement a mapper for a type that is not an ID.
 */
final class BinaryUuidType extends AbstractBinaryUuidType
{
    protected function getMappedClass(): string
    {
        return Uuid::class;
    }

    /**
     * By convention, we use the canonical class name in snake case.
     * Since both this and the UuidType map the same class (Uuid), we would have a name collision by following our
     * convention, so we need to override this method.
     */
    public function getName(): string
    {
        return 'binary_uuid';
    }
}
