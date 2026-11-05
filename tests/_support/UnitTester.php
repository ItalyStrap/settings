<?php

declare(strict_types=1);

use Codeception\Actor;
use _generated\UnitTesterActions;

/**
 * The actor of the unit suite, WordPress is not loaded.
 *
 * @SuppressWarnings(PHPMD)
 */
class UnitTester extends Actor
{
    use UnitTesterActions;
}
