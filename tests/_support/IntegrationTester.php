<?php

declare(strict_types=1);

use Codeception\Actor;
use _generated\IntegrationTesterActions;

/**
 * The actor of the integration suite, WordPress is loaded.
 *
 * @SuppressWarnings(PHPMD)
 */
class IntegrationTester extends Actor
{
    use IntegrationTesterActions;
}
