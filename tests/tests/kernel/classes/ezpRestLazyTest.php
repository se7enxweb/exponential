<?php
/**
 * File containing the ezpRestLazyTest class.
 *
 * The lazy database set-up of the REST layer: its ezcBaseInit callbacks come
 * back after a persistent worker has reset every class's static properties,
 * and a SQLite site gets a DSN for its own database file.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpRestLazyTest extends ezpTestCase
{
    public function tearDown(): void
    {
        ezpINIHelper::restoreINISettings();
        parent::tearDown();
    }

    protected function callbackMap()
    {
        $map = new ReflectionProperty( 'ezcBaseInit', 'callbackMap' );
        return $map;
    }

    public function testCallbacksAreRegisteredAgainAfterAReset()
    {
        $this->assertTrue( class_exists( 'ezpRestDbConfig' ) );
        $map = $this->callbackMap();
        $saved = $map->getValue();

        // What an application server worker does between two requests.
        $map->setValue( null, array() );
        ezpRestDbConfig::registerCallbacks();
        $now = $map->getValue();
        $this->assertSame( 'ezpRestDbConfig', $now['ezcInitDatabaseInstance'] ?? null );
        $this->assertSame( 'ezpRestPoConfig', $now['ezcInitPersistentSessionInstance'] ?? null );

        // Again, with both registered: no ezcBaseInitCallbackConfiguredException.
        ezpRestDbConfig::registerCallbacks();
        $this->assertSame( $now, $map->getValue() );

        $map->setValue( null, $saved );
    }

    public function testSqliteDsnNamesTheSitesDatabaseFile()
    {
        $helper = new ReflectionMethod( 'ezpRestDbConfig', 'lazyDbHelper' );

        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'DatabaseImplementation', 'sqlite3' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Database', 'rest-test.db' );
        $this->assertSame(
            array( 'phptype' => 'sqlite', 'database' => getcwd() . '/var/storage/sqlite3/rest-test.db' ),
            $helper->invoke( null )
        );

        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Database', '/srv/data/site.db' );
        $this->assertSame( array( 'phptype' => 'sqlite', 'database' => '/srv/data/site.db' ), $helper->invoke( null ) );

        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Database', ':memory:' );
        $this->assertSame( array( 'phptype' => 'sqlite', 'port' => 'memory' ), $helper->invoke( null ) );
    }

    public function testMysqlDsnIsUnchanged()
    {
        $helper = new ReflectionMethod( 'ezpRestDbConfig', 'lazyDbHelper' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'DatabaseImplementation', 'ezmysqli' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Server', 'db.example' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Port', '3307' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'User', 'u' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Password', 'p' );
        ezpINIHelper::setINISetting( 'site.ini', 'DatabaseSettings', 'Database', 'd' );
        $this->assertSame( 'mysql://u:p@db.example:3307/d', $helper->invoke( null ) );
    }
}

?>
