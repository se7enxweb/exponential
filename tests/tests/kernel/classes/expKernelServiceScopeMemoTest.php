<?php
/**
 * expKernelServiceScope::memo(): a value worked out from the settings is kept by a persistent worker only while its
 * settings files are kept (memoEnabled()), and never outlives them: another generation (sync()) and flush() empty
 * the memo, while the kept connections stay. Outside a Velocity worker (here, the command line) every call works
 * the value out again, as before.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expKernelServiceScopeMemoTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expKernelServiceScopeMemoTest extends PHPUnit\Framework\TestCase
{
    public function testOutsideAWorkerEveryCallBuildsTheValue()
    {
        $this->assertFalse( expKernelServiceScope::memoEnabled() );
        $calls = 0;
        $build = function () use ( &$calls ) { return ++$calls; };
        $this->assertSame( 1, expKernelServiceScope::memo( 'memo-test', $build ) );
        $this->assertSame( 2, expKernelServiceScope::memo( 'memo-test', $build ) );
    }

    public function testAnotherGenerationAndFlushEmptyTheMemoButNotTheConnections()
    {
        $store = new expKernelServiceScope();
        $store->sync( 'generation-a' );
        $store->memo['k'] = 'v';
        $store->connections['test']['c'] = 'kept';
        $this->assertFalse( $store->sync( 'generation-a' ) );
        $this->assertSame( 'v', $store->memo['k'] );
        $this->assertTrue( $store->sync( 'generation-b' ) );
        $this->assertSame( array(), $store->memo );
        $store->memo['k'] = 'v';
        $store->flush();
        $this->assertSame( array(), $store->memo );
        $this->assertSame( 'kept', $store->connections['test']['c'] );
    }

    public function testSettingsAreNotKeptWithoutABootedContainer()
    {
        // the command line never boots the web kernel's container
        $this->assertFalse( expKernelServices::workerSettingsKept() );
    }
}
