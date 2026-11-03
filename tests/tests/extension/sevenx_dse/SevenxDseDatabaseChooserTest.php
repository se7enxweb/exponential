<?php
/**
 * "Change database" in the DS Editor (the dse_switch link of dse/dashboard) opens the database chooser, not the
 * dashboard's warning again, once the warning was confirmed in this session. No database.
 *
 * - The dse_switch handler keeps the confirmation (dse_ack) and sends the browser to /(dse_choose)/1; without the
 *   confirmation it sends it to the bare dashboard, whose warning comes first. "Connect to another database" and
 *   AdminNeo's "Logout" land on the chooser too. (dse_choose) never reaches AdminNeo's parameters.
 * - The chooser (dse/chooser.tpl) opens the site's database with one click (dse_open_main, only after the
 *   confirmation, with the login from site.ini), lists the logins remembered in the session with the current one
 *   marked, and stands above AdminNeo's login form with every driver. No inline CSS.
 * - Dashboard::currentLogin() names the connection of a request only when AdminNeo holds a login for it, and
 *   Dashboard::rememberedLogins() lists the session's logins at view-parameter addresses without any password.
 * - The chooser's strings are in every catalogue, translated (untranslated/ excepted).
 *
 * Skips when extension/sevenx_dse is not installed; SEVENX_DSE_EXTENSION_DIR=<dir> checks a clone instead.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/sevenx_dse/SevenxDseDatabaseChooserTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class SevenxDseDatabaseChooserTest extends PHPUnit\Framework\TestCase
{
    const VIEW = 'classes/runnable/views/dse/dashboard.php';
    const VIEW_CLASS = 'Exponential\View\Extension\SevenxDse\Dse\Dashboard';
    const CHOOSER = 'design/standard/templates/dse/chooser.tpl';

    private static $root;
    private static $ext;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 4 );
        self::$ext = getenv( 'SEVENX_DSE_EXTENSION_DIR' ) ?: self::$root . '/extension/sevenx_dse';
    }

    protected function setUp(): void
    {
        if ( !is_file( self::$ext . '/' . self::CHOOSER ) || !is_file( self::$ext . '/' . self::VIEW ) )
        {
            $this->markTestSkipped( 'extension/sevenx_dse with the database chooser is not installed' );
        }
    }

    private static function read( $file )
    {
        return (string)file_get_contents( self::$ext . '/' . $file );
    }

    private static function viewClass()
    {
        if ( !class_exists( self::VIEW_CLASS, false ) )
        {
            if ( !class_exists( 'Exponential\Runnable\ModuleView' ) )
            {
                self::markTestSkipped( 'the kernel runnable classes are not autoloadable' );
            }
            require_once self::$ext . '/' . self::VIEW;
        }
        return self::VIEW_CLASS;
    }

    public function testChangeDatabaseOpensTheChooserAfterTheConfirmation()
    {
        $view = self::read( self::VIEW );
        $this->assertSame( 1, preg_match( '~hash_equals\( \$_SESSION\[\'dse_switch_token\'\].*?exit;~s', $view, $m ) );
        $handler = $m[0];
        $this->assertStringNotContainsString( "unset( \$_SESSION['dse_ack'] )", $handler, 'dse_switch no longer asks for the warning again' );
        $this->assertStringContainsString( "( !empty( \$_SESSION['dse_ack'] ) ? self::CHOOSER_PATH : '' )", $handler );
        $this->assertStringContainsString( "const CHOOSER_PATH = '/(dse_choose)/1';", $view );
        $this->assertStringContainsString( "\$_dseChoose && !empty( \$_SESSION['dse_ack'] )", $view, 'the chooser only after the confirmation' );
        $this->assertSame( 1, preg_match( "~unset\( \\\$_dseParams\['dse_choose'\] \);\s*\\\$_GET = \\\$_dseParams;~", $view ), '(dse_choose) is not handed to AdminNeo' );
        // "Connect to another database" and AdminNeo's "Logout" land on the chooser
        $this->assertSame( 1, preg_match( "~if \( isset\( \\\$_POST\['dse_ack_warning'\] \) \) \{.*?self::CHOOSER_PATH~s", $view ) );
        $this->assertSame( 1, preg_match( "~if \( \\\$_dseLogout \) \{.*?self::CHOOSER_PATH~s", $view ) );
        // the main database from the chooser only after the confirmation
        $this->assertSame( 1, preg_match( "~if \( isset\( \\\$_POST\['dse_open_main'\] \) \) \{\s*if \( empty\( \\\$_SESSION\['dse_ack'\] \)~", $view ) );
    }

    public function testChooserTemplate()
    {
        $tpl = self::read( self::CHOOSER );
        $this->assertStringContainsString( 'name="dse_open_main"', $tpl );
        $this->assertStringContainsString( '$login.current', $tpl );
        $this->assertStringContainsString( "'Connected now'|i18n( 'extension/sevenx_dse' )", $tpl );
        $this->assertStringContainsString( 'a4-cards', $tpl );
        $this->assertStringNotContainsString( '<style', $tpl );
        $this->assertSame( 0, preg_match( '/\sstyle\s*=/i', $tpl ) );
        $this->assertStringNotContainsString( 'password', strtolower( preg_replace( '/\{\*.*?\*\}/s', '', $tpl ) ), 'no password reaches the chooser' );
        $this->assertStringContainsString( "design:dse/chooser.tpl", self::read( self::VIEW ) );
    }

    public function testCurrentLogin()
    {
        $class = self::viewClass();
        $pwds = array( 'sqlite' => array( 'main' => array( '' => '' ), 'var/tmp/x.db' => array( '' => null ) ) );
        $this->assertSame( array( 'sqlite', 'main', '', 'var/storage/sqlite3/site.db' ),
            $class::currentLogin( array( 'sqlite' => 'main', 'username' => '', 'db' => 'var/storage/sqlite3/site.db' ), $pwds ) );
        $this->assertNull( $class::currentLogin( array( 'sqlite' => 'var/tmp/x.db', 'username' => '' ), $pwds ), 'a forgotten login' );
        $this->assertNull( $class::currentLogin( array( 'sqlite' => 'main' ), $pwds ), 'the login form, no username yet' );
        $this->assertNull( $class::currentLogin( array( 'mysql' => 'db', 'username' => 'u' ), $pwds ) );
        $this->assertNull( $class::currentLogin( array( 'sqlite' => 'main', 'username' => '' ), null ) );
    }

    public function testRememberedLoginsCarryNoPassword()
    {
        $class = self::viewClass();
        $secret = 'not-printed-' . bin2hex( random_bytes( 4 ) );
        $pwds = array(
            'sqlite' => array( 'main' => array( '' => '' ), 'var/tmp/other.db' => array( '' => '' ) ),
            'mysql'  => array( 'db.example:3306' => array( 'editor' => $secret, 'gone' => null ) ),
        );
        $dbs = array( 'mysql' => array( 'db.example:3306' => array( 'editor' => array( 'shop' => true, 'blog' => true ) ) ) );
        $logins = $class::rememberedLogins( $pwds, $dbs, array( 'mysql', 'db.example:3306', 'editor', 'blog' ), '/admin/dse/dashboard' );

        $this->assertCount( 4, $logins, 'main, the other SQLite file, two databases of the MySQL login; the forgotten login is left out' );
        $this->assertStringNotContainsString( $secret, serialize( $logins ) );
        $this->assertTrue( $logins[0]['is_main'] );
        $this->assertSame( '/admin/dse/dashboard/(sqlite)/main/(username)/~', $logins[0]['url'] );
        $this->assertStringStartsWith( '/admin/dse/dashboard/(sqlite)/~', $logins[1]['url'], 'a path travels encoded' );
        $this->assertStringNotContainsString( '?', implode( ' ', array_column( $logins, 'url' ) ) );
        $current = array_values( array_filter( $logins, function ( $l ) { return $l['current']; } ) );
        $this->assertCount( 1, $current );
        $this->assertSame( array( 'MySQL/MariaDB', 'editor', 'blog' ), array( $current[0]['driver'], $current[0]['username'], $current[0]['db'] ) );
        $this->assertSame( array(), $class::rememberedLogins( null, null, null, '/admin/dse/dashboard' ) );
    }

    public function testChooserStringsAreTranslatedEverywhere()
    {
        preg_match_all( "/\\{'((?:[^'\\\\]|\\\\.)+)'\\|i18n\\( 'extension\\/sevenx_dse'/", self::read( self::CHOOSER ), $m );
        $strings = array_unique( $m[1] );
        $this->assertGreaterThanOrEqual( 6, count( $strings ) );
        $catalogues = glob( self::$ext . '/translations/*/translation.ts' );
        $this->assertGreaterThan( 2, count( $catalogues ) );
        foreach ( $catalogues as $file )
        {
            $locale = basename( dirname( $file ) );
            $ts = (string)file_get_contents( $file );
            foreach ( $strings as $string )
            {
                $source = '<source>' . htmlspecialchars( $string, ENT_NOQUOTES ) . '</source>';
                $this->assertStringContainsString( $source, $ts, "$locale lacks: $string" );
                if ( $locale !== 'untranslated' )
                {
                    $this->assertSame( 1, preg_match( '~' . preg_quote( $source, '~' ) . '\s*<translation>[^<]+</translation>~', $ts ), "$locale does not translate: $string" );
                }
            }
        }
    }
}
