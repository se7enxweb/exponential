<?php
/**
 * The clusterpurge cronjob part purges files expired for 30 days, in the unit eZScriptClusterPurge expects
 * (seconds), the same as the command's default.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ClusterPurgeCronjobExpiryTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 5 );
        require_once $root . '/kernel/private/classes/runnable/runnable.php';
        require_once $root . '/kernel/private/classes/runnable/cronjobpart.php';
        require_once $root . '/kernel/private/classes/cronjobs/clusterpurge.php';
        require_once $root . '/kernel/private/classes/ezscriptclusterpurge.php';
    }

    public function testThePartPurgesAfterThirtyDaysInSeconds()
    {
        $this->assertSame( 2592000, \Exponential\Cronjob\Kernel\Clusterpurge::expirySeconds() );
    }

    public function testThePartUsesTheSameValueAsThePurgeHandlersDefault()
    {
        $handler = new eZScriptClusterPurge();
        $this->assertSame( $handler->optExpiry, \Exponential\Cronjob\Kernel\Clusterpurge::expirySeconds() );
    }
}
