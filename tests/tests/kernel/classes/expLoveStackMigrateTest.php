<?php
/**
 * exp:lovestack migrate (expLoveStack::migrate()) on a LoveStack-shaped SQLite fixture: a dry run writes
 * nothing, --apply sets the version row and records LoveStack's schema version, a second --apply changes
 * nothing, and a database that does not come from LoveStack is refused with a clear message. Content and
 * every other table stay as they were.
 *
 * No live database: the fixture is built in a temporary directory (expLoveStackFixture).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../kernel/classes/explovestack.php';
require_once __DIR__ . '/expLoveStackFixture.php';

class expLoveStackMigrateTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        if ( !class_exists( 'ExponentialSDK' ) )
            require_once __DIR__ . '/../../../../lib/version.php';
        $this->dir = sys_get_temp_dir() . '/exp-lovestack-migrate-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/settings/override', 0777, true );
        mkdir( $this->dir . '/settings/siteaccess/site', 0777, true );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
        {
            $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                    RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $files as $file )
                $file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
            rmdir( $this->dir );
        }
    }

    /**
     * A database handler over SQLite3 with the methods expLoveStack uses; it records every statement.
     */
    private function handler( SQLite3 $sqlite )
    {
        return new class( $sqlite ) {
            public $statements = array();
            public $transactions = 0;
            private $s;
            public function __construct( $s ) { $this->s = $s; }
            public function databaseName() { return 'sqlite'; }
            public function arrayQuery( $sql )
            {
                $this->statements[] = $sql;
                $result = @$this->s->query( $sql );
                if ( $result === false )
                    return false;
                $rows = array();
                while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
                    $rows[] = $row;
                return $rows;
            }
            public function query( $sql )
            {
                $this->statements[] = $sql;
                return $this->s->exec( $sql );
            }
            public function escapeString( $text ) { return SQLite3::escapeString( $text ); }
            public function begin() { $this->s->exec( 'BEGIN' ); ++$this->transactions; }
            public function commit() { $this->s->exec( 'COMMIT' ); }
        };
    }

    private static function siteData( SQLite3 $sqlite )
    {
        $rows = array();
        $result = $sqlite->query( 'SELECT name, value FROM ezsite_data ORDER BY name' );
        while ( $row = $result->fetchArray( SQLITE3_ASSOC ) )
            $rows[$row['name']] = (string)$row['value'];
        return $rows;
    }

    /**
     * A checksum of every table except ezsite_data, to prove content is never touched.
     */
    private static function contentChecksum( SQLite3 $sqlite )
    {
        $sums = array();
        $tables = $sqlite->query( "SELECT name FROM sqlite_master WHERE type='table' AND name <> 'ezsite_data' ORDER BY name" );
        while ( $table = $tables->fetchArray( SQLITE3_ASSOC ) )
        {
            $hash = hash_init( 'md5' );
            $rows = $sqlite->query( 'SELECT * FROM "' . $table['name'] . '" ORDER BY 1' );
            while ( $row = $rows->fetchArray( SQLITE3_NUM ) )
                hash_update( $hash, serialize( $row ) );
            $sums[$table['name']] = hash_final( $hash );
        }
        return $sums;
    }

    public function testDryRunWritesNothing()
    {
        $file = $this->dir . '/lovestack.db';
        expLoveStackFixture::create( $file )->close();
        $before = md5_file( $file );

        $sqlite = new SQLite3( $file );
        $handler = $this->handler( $sqlite );
        $result = ( new expLoveStack( $handler, $this->dir ) )->migrate( false );
        $sqlite->close();

        $this->assertSame( '', $result['refused'] );
        $this->assertTrue( $result['loveStack'] );
        $this->assertFalse( $result['applied'] );
        $this->assertSame( 0, $result['written'] );
        $this->assertSame( array(
            array( 'name' => 'ezpublish-version', 'from' => 'lovestack', 'to' => ExponentialSDK::version() ),
            array( 'name' => 'lovestack-schema-version', 'from' => null, 'to' => '5' ),
        ), $result['writes'] );
        foreach ( $handler->statements as $sql )
            $this->assertMatchesRegularExpression( '/^\s*(SELECT|PRAGMA)\b/i', $sql, 'a dry run only reads' );
        $this->assertSame( 0, $handler->transactions );
        $this->assertSame( $before, md5_file( $file ), 'the database file is unchanged' );
    }

    public function testApplySetsBothRowsAndNothingElse()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $content = self::contentChecksum( $sqlite );
        $result = ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );

        $this->assertSame( '', $result['refused'] );
        $this->assertTrue( $result['applied'] );
        $this->assertSame( 2, $result['written'] );
        $rows = self::siteData( $sqlite );
        $this->assertSame( ExponentialSDK::version(), $rows['ezpublish-version'] );
        $this->assertSame( '6.0.15stable', $rows['ezpublish-version'], 'the version Exponential\'s upgrade SQL writes' );
        $this->assertSame( '5', $rows['lovestack-schema-version'] );
        $this->assertSame( '5', $rows['db-schema-version'], 'LoveStack\'s own counter is kept' );
        $this->assertSame( $content, self::contentChecksum( $sqlite ), 'no other table is changed' );

        // check no longer finds the version row
        $findings = ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->findings();
        $this->assertSame( expLoveStack::STATUS_CLEAR, $findings[0]['status'] );
    }

    public function testSecondApplyChangesNothing()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );
        $rows = self::siteData( $sqlite );

        $handler = $this->handler( $sqlite );
        $result = ( new expLoveStack( $handler, $this->dir ) )->migrate( true );
        $this->assertSame( '', $result['refused'], 'a migrated database is not refused' );
        $this->assertSame( array(), $result['writes'] );
        $this->assertSame( 0, $result['written'] );
        $this->assertSame( 0, $handler->transactions );
        foreach ( $handler->statements as $sql )
            $this->assertMatchesRegularExpression( '/^\s*(SELECT|PRAGMA)\b/i', $sql );
        $this->assertSame( $rows, self::siteData( $sqlite ) );
    }

    public function testAfterTheUpgradeSqlOnlyTheSchemaVersionIsRecorded()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        // what update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql does to the version row
        $sqlite->exec( "UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version'" );
        $result = ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );
        $this->assertSame( array( array( 'name' => 'lovestack-schema-version', 'from' => null, 'to' => '5' ) ), $result['writes'] );
        $this->assertSame( '5', self::siteData( $sqlite )['lovestack-schema-version'] );
    }

    public function testAnOlderVersionRowIsLeftToTheUpgradeSql()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $sqlite->exec( "UPDATE ezsite_data SET value='6.0.0' WHERE name='ezpublish-version'" );
        $result = ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );
        $this->assertSame( '6.0.0', self::siteData( $sqlite )['ezpublish-version'] );
        $this->assertStringContainsString( "'6.0.0'", implode( ' ', $result['notes'] ) );
    }

    public function testNonLoveStackDatabaseIsRefused()
    {
        $file = $this->dir . '/exponential.db';
        expLoveStackFixture::create( $file, false )->close();
        $before = md5_file( $file );

        $sqlite = new SQLite3( $file );
        $handler = $this->handler( $sqlite );
        $result = ( new expLoveStack( $handler, $this->dir ) )->migrate( true );
        $sqlite->close();

        $this->assertFalse( $result['loveStack'] );
        $this->assertFalse( $result['applied'] );
        $this->assertSame( array(), $result['writes'] );
        $this->assertStringContainsString( 'does not come from LoveStack', $result['refused'] );
        $this->assertStringContainsString( "'6.0.15stable'", $result['refused'] );
        $this->assertStringContainsString( 'Nothing was written', $result['refused'] );
        $this->assertSame( 0, $handler->transactions );
        $this->assertSame( $before, md5_file( $file ) );
    }

    public function testSettingsToChangeArePrintedNotWritten()
    {
        file_put_contents( $this->dir . '/settings/override/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nActiveExtensions[]=legacy_2018\n\n[Session]\nSameSite=Strict\n*/ ?>\n" );
        file_put_contents( $this->dir . '/settings/siteaccess/site/image.ini.append.php', "<?php /*\n[ValidUploadFormats]\nMIMEList[]=image/jpeg\n*/ ?>\n" );
        file_put_contents( $this->dir . '/settings/siteaccess/site/error.ini.append.php', "<?php /*\n[HTTPError-404]\nHeaderList[Cache-Control]=no-cache\n*/ ?>\n" );
        $settingsBefore = array( md5_file( $this->dir . '/settings/override/site.ini.append.php' ),
                                 md5_file( $this->dir . '/settings/siteaccess/site/image.ini.append.php' ) );

        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $result = ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );
        $settings = array();
        foreach ( $result['settings'] as $setting )
            $settings[$setting['label']] = $setting['action'];

        $this->assertStringContainsString( 'CookieSameSite', $settings['site.ini [Session] SameSite'] );
        $this->assertStringContainsString( 'deprecation notice', $settings['site.ini [Session] SameSite'], 'the alias is explained' );
        $this->assertStringContainsString( 'AllowedMIMETypes', $settings['image.ini [ValidUploadFormats] MIMEList'] );
        $this->assertStringContainsString( 'ActiveExtensions', $settings['Extension legacy_2018'] );
        $this->assertArrayNotHasKey( 'error.ini [HTTPError-404] HeaderList', $settings, 'a setting Exponential reads under the same name needs no change' );
        $this->assertSame( $settingsBefore, array( md5_file( $this->dir . '/settings/override/site.ini.append.php' ),
                                                   md5_file( $this->dir . '/settings/siteaccess/site/image.ini.append.php' ) ) );
    }

    public function testAValueWithAQuoteIsEscaped()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $sqlite->exec( "UPDATE ezsite_data SET value='5''x' WHERE name='db-schema-version'" );
        ( new expLoveStack( $this->handler( $sqlite ), $this->dir ) )->migrate( true );
        $this->assertSame( "5'x", self::siteData( $sqlite )['lovestack-schema-version'] );
    }
}
