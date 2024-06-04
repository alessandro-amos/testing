<?php

namespace Alms\Testing\TestCase;

use Alms\Testing\Trait\Helper;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    use Helper;

    protected KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();

        parent::setUp();

        $this->setUpTraits();
    }

    protected function tearDown(): void
    {
        $this->tearDownTraits();

        parent::tearDown();

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