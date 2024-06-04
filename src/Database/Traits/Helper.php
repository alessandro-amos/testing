<?php

declare(strict_types=1);

namespace  Alms\Testing\Database\Traits;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

trait Helper
{
    private ?ORMPurgerInterface $purger = null;

    public function getDatabaseCleaner(): ORMPurgerInterface
    {
        if ($this->purger === null) {
            $this->purger = new ORMPurger($this->getEntityManager());
        }

        return $this->purger;
    }


    public function getEntityManager(): EntityManagerInterface
    {
        return $this->client->getContainer()->get('doctrine.orm.default_entity_manager');
    }


    public function cleanEntityManager(): void
    {
        $this->getEntityManager()->clear();
    }

    public function getRepositoryFor(object|string $entity): ObjectRepository
    {
        return $this->getEntityManager()->getRepository($entity);
    }

    public function persist(object $entity): void
    {
        $em = $this->getEntityManager();

        $em->persist($entity);
        $em->flush();
    }
}
