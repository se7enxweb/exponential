<?php
/**
 * exp:lovestack check (expLoveStack) finds the markers of a database that LoveStack has upgraded,
 * the legacy_2018 extension, LoveStack-only settings and the LoveStack package, on a LoveStack-shaped
 * SQLite fixture and a temporary installation root, and writes nothing.
 *
 * No live database: the fixture is built in a temporary directory (expLoveStackFixture).
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../kernel/classes/explovestack.php';
require_once __DIR__ . '/expLoveStackFixture.php';

class expLoveStackCheckTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    protected function setUp(): void
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $this->dir = sys_get_temp_dir() . '/exp-lovestack-check-' . getmypid() . '-' . mt_rand();
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
     * A database handler over SQLite3 with the two methods the check uses; it records every statement.
     */
    private function handler( SQLite3 $sqlite, $engine = 'sqlite' )
    {
        return new class( $sqlite, $engine ) {
            public $statements = array();
            private $s;
            private $engine;
            public function __construct( $s, $engine ) { $this->s = $s; $this->engine = $engine; }
            public function databaseName() { return $this->engine; }
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
        };
    }

    private static function byId( array $findings )
    {
        $byId = array();
        foreach ( $findings as $finding )
            $byId[$finding['id']][] = $finding;
        return $byId;
    }

    public function testLoveStackDatabaseMarkersAreFound()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $check = new expLoveStack( $this->handler( $sqlite ), $this->dir );
        $findings = self::byId( $check->findings() );

        $this->assertSame( expLoveStack::STATUS_FOUND, $findings['db.version'][0]['status'] );
        $this->assertStringContainsString( "'lovestack'", $findings['db.version'][0]['detail'] );
        $this->assertSame( expLoveStack::STATUS_INFO, $findings['db.schema_version'][0]['status'], 'kept as a record' );
        $this->assertStringContainsString( "'5'", $findings['db.schema_version'][0]['detail'] );
        $this->assertSame( expLoveStack::STATUS_INFO, $findings['db.unique_login'][0]['status'] );
        $this->assertStringContainsString( 'ezuser_login', $findings['db.unique_login'][0]['detail'] );
        $this->assertSame( expLoveStack::STATUS_INFO, $findings['db.data_text_not_null'][0]['status'] );
        $this->assertTrue( expLoveStack::needsAttention( $check->findings() ) );
    }

    public function testExponentialDatabaseIsClear()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/exponential.db', false );
        $check = new expLoveStack( $this->handler( $sqlite ), $this->dir );
        $all = $check->findings();
        $findings = self::byId( $all );

        $this->assertNotSame( expLoveStack::STATUS_FOUND, $findings['db.version'][0]['status'] );
        $this->assertStringContainsString( "'6.0.15stable'", $findings['db.version'][0]['detail'] );
        foreach ( array( 'db.schema_version', 'db.unique_login', 'db.data_text_not_null', 'ext.legacy_2018', 'ini.setting', 'composer.package' ) as $id )
            $this->assertSame( expLoveStack::STATUS_CLEAR, $findings[$id][0]['status'], $id );
        $this->assertFalse( expLoveStack::needsAttention( $all ) );
    }

    public function testFixtureHasTheLoveStackShape()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $this->assertSame( 'lovestack', $sqlite->querySingle( "SELECT value FROM ezsite_data WHERE name='ezpublish-version'" ) );
        $this->assertSame( '5', (string)$sqlite->querySingle( "SELECT value FROM ezsite_data WHERE name='db-schema-version'" ) );
        $this->assertRefused( $sqlite, "INSERT INTO ezuser ( contentobject_id, login, email ) VALUES ( 999001, 'admin', 'x@example.com' )",
                              'UNIQUE', 'a second account with the login admin is refused' );
        $this->assertRefused( $sqlite, "UPDATE ezcontentobject_attribute SET data_text = NULL WHERE id = ( SELECT MIN(id) FROM ezcontentobject_attribute )",
                              'NOT NULL', 'data_text refuses NULL' );
        $this->assertTrue( $sqlite->exec( "INSERT INTO ezcontentobject_attribute ( id, version, contentobject_id, contentclassattribute_id ) VALUES ( 999001, 1, 1, 1 )" ) );
        $this->assertSame( '', $sqlite->querySingle( 'SELECT data_text FROM ezcontentobject_attribute WHERE id = 999001' ), 'an omitted data_text is the empty string' );
        $this->assertGreaterThan( 0, (int)$sqlite->querySingle( "SELECT COUNT(*) FROM sqlite_master WHERE type='index' AND tbl_name='ezcontentobject_attribute'" ),
                                  'the indexes of the rebuilt table are back' );
    }

    private function assertRefused( SQLite3 $sqlite, $sql, $reason, $message )
    {
        try
        {
            $sqlite->exec( $sql );
            $this->fail( $message );
        }
        catch ( Exception $e )
        {
            $this->assertStringContainsString( $reason, $e->getMessage(), $message );
        }
    }

    public function testNothingIsWritten()
    {
        $file = $this->dir . '/lovestack.db';
        expLoveStackFixture::create( $file )->close();
        $this->writeLoveStackSettings();
        $before = array( md5_file( $file ), md5_file( $this->dir . '/settings/override/site.ini.append.php' ) );

        $sqlite = new SQLite3( $file );
        $handler = $this->handler( $sqlite );
        $check = new expLoveStack( $handler, $this->dir );
        $check->findings();
        $this->assertSame( 0, $sqlite->changes() );
        $sqlite->close();

        foreach ( $handler->statements as $sql )
            $this->assertMatchesRegularExpression( '/^\s*(SELECT|PRAGMA)\b/i', $sql );
        $this->assertSame( $before, array( md5_file( $file ), md5_file( $this->dir . '/settings/override/site.ini.append.php' ) ) );
    }

    private function writeLoveStackSettings()
    {
        file_put_contents( $this->dir . '/settings/override/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=ezjscore\nActiveExtensions[]=legacy_2018\n\n" .
                           "[Session]\nSameSite=Strict\n\n[HTTPHeaderSettings]\nSetContentLengthHeader=enabled\n*/ ?>\n" );
        file_put_contents( $this->dir . '/settings/siteaccess/site/image.ini.append.php',
                           "<?php /*\n[ValidUploadFormats]\nMIMEList[]=image/jpeg\n*/ ?>\n" );
        file_put_contents( $this->dir . '/settings/siteaccess/site/error.ini.append.php',
                           "<?php /*\n[HTTPError-404]\nHeaderList[Cache-Control]=no-cache\n*/ ?>\n" );
        file_put_contents( $this->dir . '/composer.json', '{ "require": { "mugoweb/ezpublish-legacy": "^2025.03" } }' );
    }

    public function testSettingsExtensionAndComposerMarkersAreFound()
    {
        $this->writeLoveStackSettings();
        $sqlite = expLoveStackFixture::create( $this->dir . '/exponential.db', false );
        $check = new expLoveStack( $this->handler( $sqlite ), $this->dir );
        $all = $check->findings();
        $findings = self::byId( $all );

        $this->assertSame( expLoveStack::STATUS_FOUND, $findings['ext.legacy_2018'][0]['status'] );
        $this->assertStringContainsString( 'settings/override', $findings['ext.legacy_2018'][0]['detail'] );
        $this->assertSame( expLoveStack::STATUS_FOUND, $findings['composer.package'][0]['status'] );

        $settings = array();
        foreach ( $findings['ini.setting'] as $finding )
            $settings[$finding['label']] = $finding;
        $this->assertSame( expLoveStack::STATUS_FOUND, $settings['site.ini [Session] SameSite']['status'] );
        $this->assertStringContainsString( 'CookieSameSite', $settings['site.ini [Session] SameSite']['action'] );
        $this->assertSame( expLoveStack::STATUS_FOUND, $settings['site.ini [HTTPHeaderSettings] SetContentLengthHeader']['status'] );
        $this->assertSame( expLoveStack::STATUS_FOUND, $settings['image.ini [ValidUploadFormats] MIMEList']['status'] );
        $this->assertStringContainsString( 'siteaccess site', $settings['image.ini [ValidUploadFormats] MIMEList']['detail'] );
        $this->assertSame( expLoveStack::STATUS_INFO, $settings['error.ini [HTTPError-404] HeaderList']['status'], 'a setting of the same name is information' );
        $this->assertTrue( expLoveStack::needsAttention( $all ) );
    }

    public function testSettingsOfExtensionsAreRead()
    {
        mkdir( $this->dir . '/extension/mysite/settings/siteaccess/site', 0777, true );
        file_put_contents( $this->dir . '/extension/mysite/settings/site.ini.append.php', "<?php /*\n[Session]\nSameSite=Lax\n*/ ?>\n" );
        file_put_contents( $this->dir . '/extension/mysite/settings/siteaccess/site/menu.ini.append.php', "<?php /*\n[Leftmenu_my]\nPolicyList_edit_profile[]=user/selfedit\n*/ ?>\n" );
        $sqlite = expLoveStackFixture::create( $this->dir . '/exponential.db', false );
        $check = new expLoveStack( $this->handler( $sqlite ), $this->dir );
        $details = array();
        foreach ( $check->findings() as $finding )
        {
            if ( $finding['id'] === 'ini.setting' )
                $details[$finding['label']] = $finding['status'] . ' ' . $finding['detail'];
        }
        $this->assertSame( 'found set in extension mysite', $details['site.ini [Session] SameSite'] );
        $this->assertSame( 'found set in extension mysite, siteaccess site', $details['menu.ini [Leftmenu_my] PolicyList_edit_profile'] );
    }

    public function testParseIniReadsAppendFiles()
    {
        $this->writeLoveStackSettings();
        $ini = expLoveStack::parseIni( $this->dir . '/settings/override/site.ini.append.php' );
        $this->assertSame( array( 'ezjscore', 'legacy_2018' ), $ini['ExtensionSettings']['ActiveExtensions'] );
        $this->assertSame( 'Strict', $ini['Session']['SameSite'] );
        $this->assertSame( array(), expLoveStack::parseIni( $this->dir . '/missing.ini' ) );
    }

    public function testAnEngineWithoutTheInformationIsUnknownNotAnError()
    {
        $sqlite = expLoveStackFixture::create( $this->dir . '/lovestack.db' );
        $check = new expLoveStack( $this->handler( $sqlite, 'mongodb' ), $this->dir );
        $findings = self::byId( $check->findings() );
        $this->assertSame( expLoveStack::STATUS_UNKNOWN, $findings['db.unique_login'][0]['status'] );
        $this->assertSame( expLoveStack::STATUS_UNKNOWN, $findings['db.data_text_not_null'][0]['status'] );
        // the version row is read the same way on every engine that has ezsite_data
        $this->assertSame( expLoveStack::STATUS_FOUND, $findings['db.version'][0]['status'] );
    }
}
