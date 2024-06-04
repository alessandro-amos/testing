<?php

declare(strict_types=1);

namespace Alms\Testing\Database\Strategy;

use Doctrine\Bundle\FixturesBundle\Purger\ORMPurgerFactory;
use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PurgerStrategy
{
    protected ORMPurgerInterface $purger;
    public function __construct(
        protected EntityManagerInterface $em,
        protected bool                   $purgeWithTruncate = true,
        protected array                  $except = []
    )
    {
        $this->purger = new ORMPurger($em, $this->except);

        $this->purger->setPurgeMode(
            $this->purgeWithTruncate ?
                ORMPurger::PURGE_MODE_TRUNCATE :
                ORMPurger::PURGE_MODE_DELETE
        );
    }

    public function purge(): void
    {
        $this->purger->purge();
    }


    public function setExcept(array $except = []): void
    {
        $this->except = $except;
    }
}
