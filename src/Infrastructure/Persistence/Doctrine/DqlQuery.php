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

namespace Acme\App\Infrastructure\Persistence\Doctrine;

use Acme\App\Core\Port\Persistence\QueryInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;

final class DqlQuery implements QueryInterface
{
    /**
     * @var Query
     */
    private $filters;

    /**
     * @var int
     */
    private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setHydrationMode(int $hydrationMode): void
    {
        $this->hydrationMode = $hydrationMode;
    }

    public function getHydrationMode(): int
    {
        return $this->hydrationMode;
    }
}
