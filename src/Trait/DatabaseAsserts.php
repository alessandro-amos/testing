<?php

declare(strict_types=1);

namespace Alms\Testing\Trait;

use Alms\Testing\Database\EntityAssertion;
use function is_object;

trait DatabaseAsserts
{
    /**
     * Build entity assertion.
     *
     * @param class-string|object $entity
     */
    public function assertEntity(string|object $entity): EntityAssertion
    {
        if (is_object($entity)) {
            $entity = $entity::class;
        }

        return new EntityAssertion($entity, $this->client->getContainer()->get('doctrine.orm.default_entity_manager'));
    }
}
