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

namespace Acme\App\Test\Framework;

use DOMDocument;
use Hgraca\PhpExtension\DateTime\DateTimeGenerator;
use Hgraca\PhpExtension\Uuid\UuidGenerator;

trait AppTestTrait
{
    /**
     * @after
     */
    public function resetDateTimeGenerator(): void
    {
        DateTimeGenerator::reset();
    }

    /**
     * @after
     */
    public function resetUuidGenerator(): void
    {
        UuidGenerator::reset();
    }

    public function assertValidHtml(string $html): void
    {
        $doc = new DOMDocument();

        if ($doc->loadHTML($html) === false) {
            self::fail('The provided string is not valid HTML.');
        }
    }
}
