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
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\Type;
use Hgraca\PhpExtension\Enum\AbstractEnum;
use Hgraca\PhpExtension\ScalarObjectInterface;

abstract class AbstractEnumType extends Type
{
    use TypeTrait;

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $allowedValues = "'" . implode("', '", $this->getValues()) . "'";

        if ($platform instanceof SqlitePlatform) {
            $name = $fieldDeclaration['name'];

            return "TEXT CHECK( $name IN ($allowedValues) )";
        }

        return "ENUM($allowedValues)";
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null && $this->allowsNullValues()) {
            return null;
        }

        if ($value instanceof ScalarObjectInterface) {
            $value = $value->toScalar();
        }

        if (!\in_array($value, $this->getValues(), true)) {
            $type = $this->getName();
            throw new \InvalidArgumentException("Invalid value '$value' for type '$type'.");
        }

        return $value;
    }

    protected function createSpecificObject($value)
    {
        /** @var string|AbstractEnum $class */
        $class = $this->getMappedClass();

        return $class::get($value);
    }

    private function getValues(): array
    {
        /** @var string|AbstractEnum $class */
        $class = $this->getMappedClass();

        return $class::getValidOptions();
    }
}
