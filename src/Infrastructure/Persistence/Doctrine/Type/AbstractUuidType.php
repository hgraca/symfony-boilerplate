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

use Doctrine\DBAL\Types\GuidType;

/**
 * It is preferable to use the AbstractBinaryUuidType, because the querying will be faster and the space taken will
 * be less.
 * However, if you need to look at the DB and see the actual UUID there, this is the mapper you should use.
 */
abstract class AbstractUuidType extends GuidType
{
    use TypeTrait;
}
