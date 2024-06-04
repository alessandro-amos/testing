<?php

declare(strict_types=1);

namespace  Alms\Testing\Database\Traits;

use  Alms\Testing\Database\Strategy\TransactionStrategy;

trait Transactions
{
    private ?TransactionStrategy $transactionStrategy = null;


    public function beginTransaction(): void
    {
        $this->beforeBeginTransaction();

        $this->getTransactionStrategy()->begin();

        $this->afterBeginTransaction();
    }

    public function rollbackTransaction(): void
    {
        $this->beforeRollbackTransaction();

        $this->getTransactionStrategy()->rollback();

        $this->afterRollbackTransaction();
    }

    protected function setUpTransactions(): void
    {
        $this->beginTransaction();
    }

    protected function tearDownTransactions(): void
    {
        $this->rollbackTransaction();
    }

    protected function getTransactionStrategy(): TransactionStrategy
    {
        $container = $this->client->getContainer();

        if ($this->transactionStrategy === null) {
            $this->transactionStrategy = new TransactionStrategy(
                em: $container->get('doctrine.orm.default_entity_manager'),
                kernel: $this->client->getKernel(),
            );
        }

        return $this->transactionStrategy;
    }

    /**
     * Perform any work before the database transaction has started
     */
    protected function beforeBeginTransaction(): void
    {
        // ...
    }

    /**
     * Perform any work after the database transaction has started
     */
    protected function afterBeginTransaction(): void
    {
        // ...
    }

    /**
     * Perform any work before rolling back the transaction
     */
    protected function beforeRollbackTransaction(): void
    {
        // ...
    }

    /**
     * Perform any work after rolling back the transaction
     */
    protected function afterRollbackTransaction(): void
    {
        // ...
    }
}
