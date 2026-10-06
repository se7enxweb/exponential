<?php
/**
 * ezpRestDbConfig::lazyDbHelper(), the database connection of the REST interface's persistent objects (OAuth tokens):
 * the DSN built from site.ini [DatabaseSettings] for each database implementation, SQLite files and memory, an
 * unknown implementation refused, and user names and passwords with characters that have a meaning in a DSN, which
 * must come back unchanged when the DSN is parsed.
 *
 * No database: nothing connects. The settings are set on the loaded site.ini and put back.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group rest
 */

class ezpRestDbConfigTestDouble extends ezpRestDbConfig
{
    public static function dsn()
    {
        return self::lazyDbHelper();
    }
}

class ezpRestDbConfigTest extends PHPUnit\Framework\TestCase
{
    private $saved = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'ezcDbFactory' ) )
            $this->markTestSkipped( 'the Database component of the Zeta Components is not installed' );
        chdir( dirname( __DIR__, 5 ) );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'DatabaseSettings', $name );
            else
                $ini->setVariable( 'DatabaseSettings', $name, $value[0] );
        }
        $this->saved = array();
    }

    private function database( $type, $server, $port, $user, $password, $name )
    {
        $ini = eZINI::instance();
        $values = array( 'DatabaseImplementation' => $type, 'Server' => $server, 'Port' => $port, 'User' => $user,
                         'Password' => $password, 'Database' => $name );
        foreach ( $values as $setting => $value )
        {
            if ( !array_key_exists( $setting, $this->saved ) )
                $this->saved[$setting] = $ini->hasVariable( 'DatabaseSettings', $setting ) ? array( $ini->variable( 'DatabaseSettings', $setting ) ) : null;
            $ini->setVariable( 'DatabaseSettings', $setting, $value );
        }
    }

    public static function implementationProvider()
    {
        return array(
            array( 'ezmysqli', 'mysql' ), array( 'mysqli', 'mysql' ), array( 'ezmysql', 'mysql' ), array( 'mysql', 'mysql' ),
            array( 'ezpostgresql', 'pgsql' ), array( 'postgresql', 'pgsql' ), array( 'pgsql', 'pgsql' ),
            array( 'ezoracle', 'oracle' ), array( 'oracle', 'oracle' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('implementationProvider')]
    public function testImplementationGivesTheDsnType( $implementation, $type )
    {
        $this->database( $implementation, 'db.k1.example.invalid', '', 'k1user', 'k1pass', 'k1db' );
        $this->assertSame( "$type://k1user:k1pass@db.k1.example.invalid/k1db", ezpRestDbConfigTestDouble::dsn() );
    }

    public function testPortIsAddedToTheHost()
    {
        $this->database( 'ezmysqli', 'db.k1.example.invalid', '3307', 'u', 'p', 'd' );
        $parsed = ezcDbFactory::parseDSN( ezpRestDbConfigTestDouble::dsn() );
        $this->assertSame( 'db.k1.example.invalid', $parsed['hostspec'] );
        $this->assertSame( '3307', (string)$parsed['port'] );
        $this->assertSame( 'd', $parsed['database'] );
    }

    public function testUnknownImplementationIsRefused()
    {
        $this->database( 'k1db', 'h', '', 'u', 'p', 'd' );
        $this->expectException( Exception::class );
        $this->expectExceptionMessage( "Unknown / unmapped DB type 'k1db'" );
        ezpRestDbConfigTestDouble::dsn();
    }

    public function testSqliteFileAndMemory()
    {
        $this->database( 'sqlite3', '', '', '', '', ':memory:' );
        $this->assertSame( array( 'phptype' => 'sqlite', 'port' => 'memory' ), ezpRestDbConfigTestDouble::dsn() );
        $this->database( 'sqlite', '', '', '', '', '/srv/k1/site.db' );
        $this->assertSame( array( 'phptype' => 'sqlite', 'database' => '/srv/k1/site.db' ), ezpRestDbConfigTestDouble::dsn() );
        $this->database( 'sqlite3', '', '', '', '', 'k1.db' );
        $this->assertSame( array( 'phptype' => 'sqlite', 'database' => getcwd() . '/' . eZSQLite3DB::filePath( 'k1.db' ) ),
                           ezpRestDbConfigTestDouble::dsn() );
    }

    public static function credentialProvider()
    {
        return array(
            'plain' => array( 'k1user', 'secret' ),
            'percent' => array( 'k1user', '50%off%2Fx' ),
            'at and slash' => array( 'k1user', 'p@ss/w:rd' ),
            'colon in user' => array( 'k1:user', 'p' ),
            'empty password' => array( 'k1user', '' ),
            'spaces and plus' => array( 'k1 user', 'a+b c' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('credentialProvider')]
    public function testCredentialsComeBackUnchanged( $user, $password )
    {
        $this->database( 'ezmysqli', 'db.k1.example.invalid', '', $user, $password, 'k1db' );
        $parsed = ezcDbFactory::parseDSN( ezpRestDbConfigTestDouble::dsn() );
        $this->assertSame( $user, $parsed['username'] );
        $this->assertSame( $password, (string)$parsed['password'] );
        $this->assertSame( 'db.k1.example.invalid', $parsed['hostspec'] );
        $this->assertSame( 'k1db', $parsed['database'] );
    }
}
