<?php

declare(strict_types=1);

namespace  Alms\Testing\Database\Traits;

use  Alms\Testing\Database\Cleaner;
use  Alms\Testing\Database\Strategy\PurgerStrategy;
use Cycle\Database\DatabaseProviderInterface;

trait PurgeDatabase
{
    private ?PurgerStrategy $refreshStrategy = null;

    /**
     * Refresh database after each test.
     */
    public function refreshDatabase(): void
    {
        $this->beforePurgingDatabase();

        $this->getPurgeStrategy()->purge();

        $this->afterPurgingDatabase();
    }

    protected function tearDownRefreshDatabase(string $database = null, array $except = []): void
    {
        $this->getPurgeStrategy()->setExcept($except);
        $this->refreshDatabase();
    }

    protected function getPurgeStrategy(): PurgerStrategy
    {
        $container = $this->client->getContainer();

        if ($this->refreshStrategy === null) {
            $this->refreshStrategy = new PurgerStrategy(
                em: $container->get('doctrine.orm.default_entity_manager'),
            );
        }

        return $this->refreshStrategy;
    }

    /**
     * Perform any work that should take place before the database has started refreshing.
     */
    protected function beforePurgingDatabase(): void
    {
        // ...
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     */
    protected function afterPurgingDatabase(): void
    {
        // ...
    }
}
