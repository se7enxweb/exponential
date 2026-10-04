<?php
/**
 * Which outdated packages an update would install (eZUpdateManager::classifyUpdates and parseUpgrades): read from the output
 * of `composer update --dry-run`, not from composer outdated's "semver-safe-update", which does not look at composer.json.
 * Samples in fixtures/. The live installation, no test database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezupdate/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';

class ezUpdateInstallabilityTest extends expServicesCoreTestCase
{
    protected function sample( $name )
    {
        return (string)file_get_contents( __DIR__ . '/fixtures/' . $name );
    }

    protected function outdated()
    {
        $data = json_decode( $this->sample( 'composer_outdated_semver_safe.json' ), true );
        $list = array();
        foreach ( $data['installed'] as $p )
        {
            $list[] = array( 'name' => $p['name'], 'version' => $p['version'], 'latest' => $p['latest'],
                             'status' => $p['latest-status'], 'description' => $p['description'] );
        }
        return $list;
    }

    protected function byName( array $list )
    {
        $by = array();
        foreach ( $list as $p ) $by[$p['name']] = $p;
        return $by;
    }

    public function testParsesUpgradeLines()
    {
        $u = eZUpdateManager::parseUpgrades( $this->sample( 'composer_update_dry_run_one_blocked.log' ) );
        $this->assertSame( array( 'symfony/console' => 'v7.1.5', 'vendor/library' => '1.3.0' ), $u );
    }

    public function testNoUpgradeLinesGiveNothing()
    {
        $this->assertSame( array(), eZUpdateManager::parseUpgrades( $this->sample( 'composer_update_nothing_to_do.log' ) ) );
    }

    public function testSemverSafeButPinnedIsBlocked()
    {
        $c = array( 'phpunit/phpunit' => '13.0.0', 'symfony/console' => '^7.1' );
        $r = $this->byName( eZUpdateManager::classifyUpdates( $this->outdated(), $this->sample( 'composer_update_dry_run_one_blocked.log' ), true, $c ) );
        $this->assertTrue( $r['symfony/console']['installable'] );
        $this->assertFalse( $r['symfony/console']['blocked'] );
        $this->assertSame( 'v7.1.5', $r['symfony/console']['would_install'] );
        $this->assertFalse( $r['phpunit/phpunit']['installable'] );
        $this->assertTrue( $r['phpunit/phpunit']['blocked'] );
        $this->assertSame( '13.0.0', $r['phpunit/phpunit']['constraint'] );
        // composer outdated still says semver-safe-update: that is what the label no longer trusts
        $this->assertSame( 'semver-safe-update', $r['phpunit/phpunit']['status'] );
    }

    public function testFailedDryRunKnowsNothing()
    {
        $r = eZUpdateManager::classifyUpdates( $this->outdated(), 'Your requirements could not be resolved', false, array() );
        foreach ( $r as $p )
        {
            $this->assertFalse( $p['installable'] );
            $this->assertFalse( $p['blocked'] );
            $this->assertTrue( $p['unknown'] );
        }
    }

    public function testUpdateArgumentsNameOnlyValidPackages()
    {
        $m = eZUpdateManager::getInstance();
        $a = $m->updateArguments( false, 'auto', array( 'symfony/console', '--evil', 'x y', 'symfony/console', 'vendor/library' ) );
        $this->assertSame( 'update', $a[0] );
        $this->assertSame( array( 'symfony/console', 'vendor/library' ), array_slice( $a, -2 ) );
        $this->assertNotContains( '--evil', $a );
    }

    public function testLockMismatchesAreNamed()
    {
        $n = eZUpdateManager::parseLockMismatches( $this->sample( 'composer_update_dry_run_lock_mismatch.log' ) );
        $this->assertSame( array( 'vendor/library', 'vendor/other' ), $n );
        $this->assertSame( array(), eZUpdateManager::parseLockMismatches( $this->sample( 'composer_update_dry_run_one_blocked.log' ) ) );
    }
}
