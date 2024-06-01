<?php

namespace Alms\Testing;

use Alms\Testing\Constraints\ArraySubset;
use ArrayAccess;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @internal This class is not meant to be used or overwritten outside the framework itself.
 */
abstract class Assert extends PHPUnit
{
    public static function assertArraySubset($subset, $array, bool $checkForIdentity = false, string $msg = ''): void
    {
        if (! (is_array($subset) || $subset instanceof ArrayAccess)) {
            throw new \InvalidArgumentException('array or ArrayAccess');
        }

        if (! (is_array($array) || $array instanceof ArrayAccess)) {
            throw new \InvalidArgumentException('array or ArrayAccess');
        }

        $constraint = new ArraySubset($subset, $checkForIdentity);

        PHPUnit::assertThat($array, $constraint, $msg);
    }
}
