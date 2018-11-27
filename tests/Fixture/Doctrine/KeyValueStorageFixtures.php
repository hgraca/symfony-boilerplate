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

namespace Acme\App\Test\Fixture\Doctrine;

use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Test\Fixture\FixturesTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Coen Mooij
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class KeyValueStorageFixtures extends Fixture implements DependentFixtureInterface
{
    use FixturesTrait;

    private const SQLITE = 'sqlite';
    private const TABLE = 'KeyValueStorage';
    private const KEY_KEY = 'key';
    private const VALUE_KEY = 'value';
    public const KEY_VALUE_PAIRS = [
        'namespace_key_1' => self::VALUE_KEY,
    ];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param ObjectManager|EntityManagerInterface $manager
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function load(ObjectManager $manager): void
    {
        $this->connection = $manager->getConnection();

        if ($this->connection->getDatabasePlatform()->getName() === self::SQLITE) {
            foreach (self::KEY_VALUE_PAIRS as $key => $value) {
                $this->insertPair($key, $value);
            }
        }
        $this->createServiceProWithInvitesHasPushEnabled();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createServiceProWithInvitesHasPushEnabled(): void
    {
        /* @var User $user */
        $user = $this->getReference(UserFixtures::JANE);

        $this->insertPair('push_enabled_' . $user->getId(), '1');
        $this->insertPair('sms_enabled_' . $user->getId(), '0');
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function insertPair(string $key, string $value): void
    {
        $this->connection->insert(
            self::TABLE,
            $this->escapeKeys(
                [
                    self::KEY_KEY => $key,
                    self::VALUE_KEY => $value,
                ]
            )
        );
    }

    private function escapeKeys(array $data): array
    {
        $escapedData = [];
        foreach ($data as $key => $value) {
            $escapedData["`{$key}`"] = $value;
        }

        return $escapedData;
    }
}
