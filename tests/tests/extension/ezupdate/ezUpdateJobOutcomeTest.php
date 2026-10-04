<?php
/**
 * What a finished ezupdate run came to (eZUpdateJob::outcomeOf): up to date, changed, or failed, read from
 * Composer's output. The output samples are in fixtures/. The live installation, no test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';

class ezUpdateJobOutcomeTest extends expServicesCoreTestCase
{
    protected function sample( $name )
    {
        return (string)file_get_contents( __DIR__ . '/fixtures/' . $name );
    }

    public function testNothingToDoIsUpToDate()
    {
        $r = eZUpdateJob::outcomeOf( $this->sample( 'composer_update_nothing_to_do.log' ), 0 );
        $this->assertSame( 'uptodate', $r['state'] );
    }

    public function testZeroOperationsIsUpToDate()
    {
        $r = eZUpdateJob::outcomeOf( "Lock file operations: 0 installs, 0 updates, 0 removals\n", 0 );
        $this->assertSame( 'uptodate', $r['state'] );
    }

    public function testUpdateIsChangedWithCounts()
    {
        $r = eZUpdateJob::outcomeOf( $this->sample( 'composer_update_one_update.log' ), 0 );
        $this->assertSame( 'changed', $r['state'] );
        $this->assertSame( array( 'installs' => 0, 'updates' => 1, 'removals' => 0 ), $r['counts'] );
    }

    public function testInstallsAndRemovalsAreCounted()
    {
        $r = eZUpdateJob::outcomeOf( $this->sample( 'composer_require_installs.log' ), 0 );
        $this->assertSame( 'changed', $r['state'] );
        $this->assertSame( array( 'installs' => 2, 'updates' => 0, 'removals' => 1 ), $r['counts'] );
    }

    public function testNonZeroExitIsFailedWhateverTheOutput()
    {
        $r = eZUpdateJob::outcomeOf( $this->sample( 'composer_update_nothing_to_do.log' ), 2 );
        $this->assertSame( 'failed', $r['state'] );
    }
}
