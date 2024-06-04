<?php

declare(strict_types=1);

namespace  Alms\Testing\Database\Strategy;

use Alms\Testing\Database\DatabaseState;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Use the `createMigration` parameter set to `false` if you want to use production application migrations.
 * No new migrations will be created and no migrations will be deleted.
 *
 * Use the `createMigration` parameter set to `true` if you want to use test application migrations.
 * Migrations will be created before the test is executed and deleted after execution.
 */
class MigrationStrategy
{
    protected Application $console;

    public function __construct(
        protected KernelInterface $kernel,
        protected bool            $createMigrations = false
    )
    {
        $this->console = new Application($kernel);
    }

    public function migrate(): void
    {
        if (!DatabaseState::$migrated) {
            $commandTester = new CommandTester(
                command: $this->console->find('doctrine:migrations:migrate')
            );
            $commandTester->execute(['--force']);
        }
        DatabaseState::$migrated = true;
    }

    public function rollback(): void
    {
        $commandTester = new CommandTester(
            command: $this->console->find(' doctrine:migrations:migrate')
        );
        $commandTester->execute(['0']);

        DatabaseState::$migrated = false;
    }
}
