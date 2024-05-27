<?php

namespace Alms\Testing\TestCase;

use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Service\ResetInterface;

class ApiTestCase extends WebTestCase
{
    protected KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient(['debug' => false]);

        parent::setUp();

        $this->setUpTraits();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownTraits();

        $this->client = null;
    }

    protected function setUpTraits(): void
    {
        $this->runTraitSetUpOrTearDown('setUp');
    }

    protected function tearDownTraits(): void
    {
        $this->runTraitSetUpOrTearDown('tearDown');
    }

    private function runTraitSetUpOrTearDown(string $method): void
    {
        $ref = new \ReflectionClass(static::class);

        foreach ($ref->getTraits() as $trait) {
            if (\method_exists($this, $name = $method . $trait->getShortName())) {
                $this->{$name}();
            }
        }

        while ($parent = $ref->getParentClass()) {
            foreach ($parent->getTraits() as $trait) {
                if (\method_exists($this, $name = $method . $trait->getShortName())) {
                    $this->{$name}();
                }
            }

            $ref = $parent;
        }
    }
}