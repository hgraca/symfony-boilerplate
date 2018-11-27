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

use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Hgraca\PhpExtension\Helper\ClassHelper;

final class DqlPersistenceService implements QueryServiceInterface, PersistenceServiceInterface, TransactionServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var bool
     */
    private $autoCommit;

    public function __construct(EntityManagerInterface $entityManager, bool $autoCommit = true)
    {
        $this->entityManager = $entityManager;
        $this->autoCommit = $autoCommit;
    }

    public function __invoke(DqlQuery $dqlQuery): ResultCollectionInterface
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        foreach ($dqlQuery->getFilters() as [$method, $arguments]) {
            $methodName = ClassHelper::extractCanonicalMethodName($method);
            $queryBuilder->$methodName(...$arguments);
        }

        $doctrineQuery = $queryBuilder->getQuery();
        $doctrineQuery->setHydrationMode($dqlQuery->getHydrationMode());

        return new ResultCollection($doctrineQuery->execute());
    }

    public function canHandle(): string
    {
        return DqlQuery::class;
    }

    public function upsert($entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
    }

    public function startTransaction(): void
    {
        if (!$this->autoCommit) {
            $this->entityManager->getConnection()->beginTransaction();
        }
    }

    /**
     * @throws ConnectionException
     */
    public function finishTransaction(): void
    {
        $this->entityManager->flush();
        if (!$this->autoCommit && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->commit();
        }
    }

    /**
     * @throws ConnectionException
     */
    public function rollbackTransaction(): void
    {
        if (!$this->autoCommit && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollBack();
        }
        $this->entityManager->clear();
    }
}
