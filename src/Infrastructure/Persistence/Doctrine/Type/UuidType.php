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
final class UuidType extends AbstractUuidType
{
    protected function getMappedClass(): string
    {
        return Uuid::class;
    }
}
