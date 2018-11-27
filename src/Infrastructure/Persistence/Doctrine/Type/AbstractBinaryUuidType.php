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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Hgraca\PhpExtension\Identity\AbstractUuidId;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * This type mapper stores the UUIDs in binary format, in the DB.
 * This makes it faster to query on the IDs and they take less space (they only take 16 characters as opposed to the
 * UUID 36 characters string).
 * However, if you look at the UUID stored in the DB, you won't be able to see the actual UUID, only its byte string.
 */
abstract class AbstractBinaryUuidType extends UuidBinaryType
{
    use TypeTrait;

    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     *
     * @return null|AbstractUuidId
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var RamseyUuid $ramseyUuid */
        $ramseyUuid = parent::convertToPHPValue($value, $platform);

        if ($ramseyUuid === null) {
            return null;
        }

        return $this->createSpecificObject((string) $ramseyUuid);
    }

    /**
     * @param AbstractUuidId $value
     *
     * @throws \Doctrine\DBAL\Types\ConversionException
     *
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $uuidString = (string) $value;

        return parent::convertToDatabaseValue($uuidString, $platform);
    }
}
