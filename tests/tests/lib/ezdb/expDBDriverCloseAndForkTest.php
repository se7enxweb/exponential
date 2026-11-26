<?php
/**
 * The database drivers before a fork: the Velocity warm-up renders pages in the pool's parent, closes the
 * parent's connection and then forks its workers. No database server is needed: the drivers are created
 * without connecting, and a MongoDB\Client does not connect until it is used.
 *
 *  DC-01 - expMongoDB keeps one client per connection URI for the process: asked twice, the same client
 *  DC-02 - expMongoDB in another process (a fork after connecting) never gets the parent's client
 *  DC-03 - expMongoDB::close() releases the client: the next getClient() makes a new one, and the instance
 *          reads as not connected
 *  DC-04 - expMongoDB::close() releases only its own URI: another database's client stays
 *  DC-05 - eZPostgreSQLDB::close() without a connection, and twice, does not throw and leaves it closed
 *
 * The same with a real fork and a MongoDB server: a one-off check script of the change.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
class expDBDriverCloseAndForkTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        if ( class_exists( 'expMongoDB' ) )
        {
            $pid = new ReflectionProperty( 'expMongoDB', 'ClientsPid' );
            $pid->setValue( null, null );
        }
    }

    /** mongodb/mongodb is only suggested by composer.json: a copy on disk that is not autoloaded is loaded here */
    private static function loadMongoLibrary()
    {
        $src = dirname( __DIR__, 4 ) . '/vendor/mongodb/mongodb/src';
        if ( class_exists( 'MongoDB\Client' ) || !extension_loaded( 'mongodb' ) || !is_file( "$src/Client.php" ) )
            return;
        spl_autoload_register( function ( $class ) use ( $src ) {
            if ( strncmp( $class, 'MongoDB\\', 8 ) === 0 && is_file( $file = $src . '/' . str_replace( '\\', '/', substr( $class, 8 ) ) . '.php' ) )
                require $file;
        } );
        require_once "$src/functions.php";
    }

    private function mongo( $database = 'exp_close_test' )
    {
        self::loadMongoLibrary();
        if ( !class_exists( 'MongoDB\Client' ) || !extension_loaded( 'mongodb' ) )
            $this->markTestSkipped( 'ext-mongodb and mongodb/mongodb are needed' );
        $db = ( new ReflectionClass( 'expMongoDB' ) )->newInstanceWithoutConstructor();
        $db->Server = '127.0.0.1';
        $db->Port = 27017;
        $db->User = '';
        $db->Password = '';
        $db->DB = $database;
        $db->IsConnected = true;
        return $db;
    }

    public function testSameClientForTheSameUriInOneProcess()
    {
        $db = $this->mongo();
        $this->assertSame( $db->getClient(), $db->getClient() );
        $this->assertSame( $db->getClient(), $this->mongo()->getClient(), 'a second instance on the same URI' );
        $this->assertTrue( $db->hasClient() );
    }

    public function testAnotherProcessNeverGetsTheParentsClient()
    {
        $db = $this->mongo();
        $parent = $db->getClient();
        // what a fork looks like from inside the child: the clients were made under another process id
        $pid = new ReflectionProperty( 'expMongoDB', 'ClientsPid' );
        $pid->setValue( null, getmypid() + 100000 );
        $this->assertFalse( $db->hasClient(), 'the parent\'s client does not count as this process\'s' );
        $child = $db->getClient();
        $this->assertNotSame( $parent, $child );
        $this->assertSame( getmypid(), $pid->getValue() );
        $this->assertSame( $child, $db->getClient() );
    }

    public function testCloseReleasesTheClient()
    {
        $db = $this->mongo();
        $first = $db->getClient();
        $db->close();
        $this->assertFalse( $db->hasClient() );
        $this->assertFalse( $db->IsConnected );
        $this->assertNotSame( $first, $db->getClient() );
        $db->close();
        $db->close();
        $this->assertFalse( $db->hasClient(), 'close() twice is harmless' );
    }

    public function testCloseReleasesOnlyItsOwnUri()
    {
        $one = $this->mongo( 'exp_close_test_one' );
        $two = $this->mongo( 'exp_close_test_two' );
        $one->getClient();
        $kept = $two->getClient();
        $one->close();
        $this->assertFalse( $one->hasClient() );
        $this->assertTrue( $two->hasClient() );
        $this->assertSame( $kept, $two->getClient() );
    }

    public function testPostgreSQLCloseWithoutConnection()
    {
        if ( !function_exists( 'pg_close' ) )
            $this->markTestSkipped( 'The pgsql extension is not loaded' );
        $db = ( new ReflectionClass( 'eZPostgreSQLDB' ) )->newInstanceWithoutConstructor();
        $db->DBConnection = false;
        $db->IsConnected = true;
        $db->close();
        $db->close();
        $this->assertFalse( $db->DBConnection );
        $this->assertFalse( $db->IsConnected );
    }
}
