<?php
/**
 * The exp:ini command (bin/php/ini.php) end to end: every action run as a real process against a temporary
 * installation root (--root), never the real settings. Guide doc/bc/6.0/console-exp-ini.md.
 *
 *  IC-01 — --help and "help <action>" exit 0 and name every action; no action is a usage error (1)
 *  IC-02 — An unknown action is a usage error (1) that lists the registered actions; so is a missing argument
 *  IC-03 — set: a plain variable in global, line-preserving (comments, other variables, order kept); a hash entry;
 *          setting the same value again changes nothing
 *  IC-04 — set creates a missing file (the .ini.append.php wrapper) and a missing block; --no-create refuses (2)
 *  IC-05 — add appends to an array, a value already there is not added twice; clear writes the reset line
 *  IC-06 — rem: a whole variable, one array value, one hash entry; a missing variable or file is 2; remove = rem
 *  IC-07 — toggle: enabled<->disabled, true<->false, 1<->0 keeping the case; a value that is no switch is refused (3)
 *  IC-08 — get: in one scope, in effect, a hash entry, a missing one (2)
 *  IC-09 — where: every file that sets it in load order with its scope, and the value in effect
 *  IC-10 — list: the blocks and the variables of a block, in one scope
 *  IC-11 — scopes: global, default, the siteaccesses and the extensions of the root; actions: every built-in
 *  IC-12 — Scopes: siteaccess:<sa>, bare <sa>, extension:<ext>, extension:<ext>:siteaccess:<sa> write their files;
 *          an unknown siteaccess is refused (3); the default scope is refused (3) unless --allow-default
 *  IC-13 — --dry-run prints the diff and writes nothing (on the default scope too)
 *  IC-14 — --json prints one object with ok, code, action, message, data, for success and for every failure kind
 *  IC-15 — Secrets are masked in get, list, where, the diff and the JSON unless --show-secrets
 *  IC-16 — A backup is written before each write, none with --no-backup; the ini cache is not cleared with --root
 *  IC-17 — copy: a plain value and a whole array from one scope to another; a missing source is 2
 *  IC-18 — The setting syntaxes: a/B/V, a.ini:B.V, "a.ini [B] V" quoted and as three words; "--" before a value
 *          that starts with "-"
 *
 * Each test builds its own root under var/tmp/ini/b/.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/iniroot.php';

class IniCommandTest extends PHPUnit\Framework\TestCase
{
    /** @var string the installation (cwd of the processes) */
    private static $installation;

    /** @var string the temporary root of the current test */
    private $root;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 6 );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->root = self::$installation . '/var/tmp/ini/b/command-' . getmypid() . '-' . substr( md5( $this->name() ), 0, 8 );
        iniCommandTestRoot::build( $this->root );
    }

    protected function tearDown(): void
    {
        if ( $this->root && is_dir( $this->root ) && strpos( $this->root, '/var/tmp/ini/b/' ) !== false )
            iniCommandTestRoot::remove( $this->root );
    }

    // ── Running the command ─────────────────────────────────────────────────

    /**
     * Runs "php bin/php/ini.php <args> --root=<root> --allow-root-user".
     *
     * @param array $args
     * @param bool $withRoot
     * @return array( int code, string stdout, string stderr )
     */
    private function ini( array $args, $withRoot = true )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/ini.php' ), $args );
        if ( $withRoot )
            $command[] = '--root=' . $this->root;
        $command[] = '--allow-root-user';
        $command[] = '--no-colors';
        // the values after "--" are taken literally: options go before it
        if ( ( $i = array_search( '--', $args, true ) ) !== false )
        {
            $command = array_merge( array( PHP_BINARY, 'bin/php/ini.php' ), array_slice( $args, 0, $i ),
                                    $withRoot ? array( '--root=' . $this->root ) : array(),
                                    array( '--allow-root-user', '--no-colors' ), array_slice( $args, $i ) );
        }
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        $err = trim( str_replace( 'With great power comes great responsibility.', '', $err ) );
        return array( $code, $out, $err );
    }

    private function json( array $args, $withRoot = true )
    {
        list( $code, $out, $err ) = $this->ini( array_merge( $args, array( '--json' ) ), $withRoot );
        $data = json_decode( $out, true );
        $this->assertIsArray( $data, "JSON expected from exp:ini " . implode( ' ', $args ) . ":\n$out\n$err" );
        $this->assertSame( $code, $data['code'], 'the exit code is the JSON code' );
        return $data;
    }

    private function file( $path )
    {
        return file_get_contents( $this->root . '/' . $path );
    }

    private function assertExit( $expected, array $result, $what = '' )
    {
        $this->assertSame( $expected, $result[0], $what . "\nstdout: " . $result[1] . "\nstderr: " . $result[2] );
    }

    const OVERRIDE = 'settings/override/site.ini.append.php';

    // ── Tests ───────────────────────────────────────────────────────────────

    /** IC-01 */
    public function testHelp()
    {
        $r = $this->ini( array( '--help' ), false );
        $this->assertExit( 0, $r );
        foreach ( array( 'get', 'set', 'add', 'rem', 'remove', 'clear', 'toggle', 'copy', 'move', 'move-all', 'where', 'list', 'scopes', 'actions' ) as $a )
            $this->assertMatchesRegularExpression( '/^  ' . $a . '\s/m', $r[1], "the help names $a" );
        foreach ( array( 'Exit codes', 'Scopes', '--dry-run', '--show-secrets', 'siteaccess:<sa>', 'extension:<ext>:siteaccess:<sa>' ) as $t )
            $this->assertStringContainsString( $t, $r[1] );

        $r = $this->ini( array( 'help', 'toggle' ), false );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'exp:ini toggle <file>/<Block>/<Variable> <scope>', $r[1] );

        $r = $this->ini( array( 'rem', '--help' ), false );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'Alias: remove', $r[1] );

        $this->assertExit( 1, $this->ini( array(), false ), 'no action' );
    }

    /** IC-02 */
    public function testUsageErrors()
    {
        $r = $this->ini( array( 'frobnicate', 'x' ) );
        $this->assertExit( 1, $r );
        $this->assertStringContainsString( 'unknown action "frobnicate"', $r[2] );
        $this->assertStringContainsString( 'get, set, add, rem', $r[2] );

        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x' ) );
        $this->assertExit( 1, $r, 'missing scope' );
        $this->assertStringContainsString( 'missing <scope>', $r[2] );
        $this->assertStringContainsString( 'Usage: exp:ini set', $r[2] );

        $this->assertExit( 1, $this->ini( array( 'set', 'site.ini/SiteSettings', 'x', 'global' ) ), 'no variable' );
        $this->assertExit( 1, $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x', 'global', 'extra' ) ), 'extra argument' );
        $this->assertExit( 1, $this->ini( array( 'set', 'site.ini/A/List[]', 'x', 'global' ) ), 'set on an array' );
    }

    /** IC-03 */
    public function testSetIsLinePreserving()
    {
        $before = $this->file( self::OVERRIDE );
        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'My site', 'global' ) );
        $this->assertExit( 0, $r );
        $after = $this->file( self::OVERRIDE );
        $this->assertSame( str_replace( 'SiteName=Override site', 'SiteName=My site', $before ), $after, 'only that line changed' );
        $this->assertStringContainsString( 'INI cache: not cleared (--root)', $r[1] );

        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'My site', 'global' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'Nothing to change', $r[1] );
        $this->assertSame( $after, $this->file( self::OVERRIDE ) );

        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/SiteAccessSettings/RelatedSiteAccessList[admin]', 'admin', 'siteaccess:admin' ) ) );
        $this->assertStringContainsString( "RelatedSiteAccessList[admin]=admin", $this->file( 'settings/siteaccess/admin/site.ini.append.php' ) );
    }

    /** IC-04 */
    public function testSetCreatesFileAndBlock()
    {
        $r = $this->ini( array( 'set', 'design.ini/ExtensionSettings/DesignExtensions[x]', 'y', 'global' ) );
        $this->assertExit( 0, $r );
        $created = $this->file( 'settings/override/design.ini.append.php' );
        $this->assertStringStartsWith( '<?php /* #?ini charset="utf-8"?', $created );
        $this->assertStringContainsString( "[ExtensionSettings]\nDesignExtensions[x]=y", $created );
        $this->assertMatchesRegularExpression( '#\*/ \?>\s*$#', $created );
        $this->assertStringContainsString( '(created)', $r[1] );

        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/NewBlock/NewVar', 'v', 'global' ) ) );
        $this->assertMatchesRegularExpression( '/\[NewBlock\]\nNewVar=v/', $this->file( self::OVERRIDE ) );

        $r = $this->ini( array( 'set', 'menu.ini/X/Y', 'z', 'global', '--no-create' ) );
        $this->assertExit( 2, $r );
        $this->assertFileDoesNotExist( $this->root . '/settings/override/menu.ini.append.php' );
    }

    /** IC-05 */
    public function testAddAndClear()
    {
        $this->assertExit( 0, $this->ini( array( 'add', 'site.ini/ExtensionSettings/ActiveExtensions[]', 'myext', 'global' ) ) );
        $this->assertStringContainsString( "ActiveExtensions[]=fixtureext\nActiveExtensions[]=myext", $this->file( self::OVERRIDE ) );

        $before = $this->file( self::OVERRIDE );
        $r = $this->ini( array( 'add', 'site.ini/ExtensionSettings/ActiveExtensions', 'myext', 'global' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'Nothing to change', $r[1] );
        $this->assertSame( $before, $this->file( self::OVERRIDE ) );

        $this->assertExit( 0, $this->ini( array( 'clear', 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]', 'siteaccess:site' ) ) );
        $this->assertStringContainsString( "AvailableSiteAccessList[]\n", $this->file( 'settings/siteaccess/site/site.ini.append.php' ) );
        $this->assertExit( 1, $this->ini( array( 'add', 'site.ini/A/Map[k]', 'v', 'global' ) ), 'add on a hash entry' );
    }

    /** IC-06 */
    public function testRem()
    {
        $this->assertExit( 0, $this->ini( array( 'rem', 'site.ini/SiteSettings/SiteURL', 'global' ) ) );
        $this->assertStringNotContainsString( 'SiteURL=', $this->file( self::OVERRIDE ) );
        $this->assertStringContainsString( "# the name\nSiteName=Override site", $this->file( self::OVERRIDE ), 'the rest stays' );
        $this->assertExit( 2, $this->ini( array( 'rem', 'site.ini/SiteSettings/SiteURL', 'global' ) ), 'gone already' );

        $this->assertExit( 0, $this->ini( array( 'rem', 'site.ini/SiteAccessSettings/RelatedSiteAccessList[]', 'admin', 'siteaccess:site' ) ) );
        $site = $this->file( 'settings/siteaccess/site/site.ini.append.php' );
        $this->assertStringNotContainsString( 'RelatedSiteAccessList[]=admin', $site );
        $this->assertStringContainsString( 'RelatedSiteAccessList[]=site', $site );

        $this->assertExit( 0, $this->ini( array( 'remove', 'site.ini/FixtureSettings/Map[a]', 'extension:fixtureext' ) ), 'alias, hash key' );
        $ext = $this->file( 'extension/fixtureext/settings/site.ini.append.php' );
        $this->assertStringNotContainsString( 'Map[a]', $ext );
        $this->assertStringContainsString( 'Map[b]=2', $ext );

        // a block: only once it is empty, or with --force
        $this->assertExit( 3, $this->ini( array( 'rem', 'site.ini/DebugSettings', 'global' ) ), 'still has settings' );
        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/EmptyLater/V', 'x', 'global' ) ) );
        $this->assertExit( 0, $this->ini( array( 'rem', 'site.ini/EmptyLater/V', 'global' ) ) );
        $this->assertStringContainsString( '[EmptyLater]', $this->file( self::OVERRIDE ), 'rem of a variable keeps the header' );
        $this->assertExit( 0, $this->ini( array( 'rem', 'site.ini/EmptyLater', 'global' ) ) );
        $this->assertStringNotContainsString( '[EmptyLater]', $this->file( self::OVERRIDE ) );
        $this->assertExit( 0, $this->ini( array( 'rem', 'site.ini/DebugSettings', 'global', '--force' ) ) );
        $this->assertStringNotContainsString( '[DebugSettings]', $this->file( self::OVERRIDE ) );
        $this->assertExit( 2, $this->ini( array( 'rem', 'site.ini/NoSuchBlock', 'global' ) ) );

        $this->assertExit( 2, $this->ini( array( 'rem', 'menu.ini/X/Y', 'global' ) ), 'no such file' );
        $this->assertFileDoesNotExist( $this->root . '/settings/override/menu.ini.append.php', 'rem never creates' );
        $this->assertExit( 2, $this->ini( array( 'rem', 'site.ini/FixtureSettings/List[]', 'three', 'extension:fixtureext' ) ) );
    }

    /** IC-07 */
    public function testToggle()
    {
        $r = $this->ini( array( 'toggle', 'site.ini/DebugSettings/DebugOutput', 'global' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'Enabled -> Disabled', $r[1] );
        $this->assertStringContainsString( "DebugOutput=Disabled\n", $this->file( self::OVERRIDE ), 'the case is kept' );

        $this->assertExit( 0, $this->ini( array( 'toggle', 'site.ini/TemplateSettings/Debug', 'siteaccess:admin' ) ) );
        $this->assertStringContainsString( "Debug=false\n", $this->file( 'settings/siteaccess/admin/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'toggle', 'site.ini/DebugSettings/Level', 'global' ) ) );
        $this->assertStringContainsString( "Level=0\n", $this->file( self::OVERRIDE ) );

        // not in the scope's file: the value in effect (here settings/site.ini of the root) is flipped into it
        $this->assertExit( 0, $this->ini( array( 'toggle', 'site.ini/ContentSettings/ViewCaching', 'global' ) ) );
        $this->assertStringContainsString( "ViewCaching=disabled\n", $this->file( self::OVERRIDE ) );
        $this->assertExit( 2, $this->ini( array( 'toggle', 'site.ini/ContentSettings/NoSwitchAnywhere', 'global' ) ), 'no file sets it' );
        $r = $this->ini( array( 'toggle', 'site.ini/ContentSettings/ViewCaching', 'siteaccess:site', '--dry-run' ), false );
        $this->assertExit( 0, $r, 'this installation, dry run' );
        $this->assertMatchesRegularExpression( '/^\+ViewCaching=(enabled|disabled)$/m', $r[1] );

        $before = $this->file( self::OVERRIDE );
        $this->assertExit( 3, $this->ini( array( 'toggle', 'site.ini/DebugSettings/Mode', 'global' ) ), 'no switch' );
        $this->assertSame( $before, $this->file( self::OVERRIDE ) );
    }

    /** IC-08 */
    public function testGet()
    {
        $r = $this->ini( array( 'get', 'site.ini/SiteSettings/SiteName', 'global' ) );
        $this->assertExit( 0, $r );
        $this->assertSame( "Override site\n", $r[1] );

        $r = $this->ini( array( 'get', 'site.ini/SiteSettings/SiteName', 'default' ) );
        $this->assertSame( "Default site\n", $r[1] );

        $r = $this->ini( array( 'get', 'site.ini/FixtureSettings/Map[b]', 'extension:fixtureext' ) );
        $this->assertExit( 0, $r );
        $this->assertSame( "2\n", $r[1] );

        $r = $this->ini( array( 'get', 'site.ini/FixtureSettings/List', 'extension:fixtureext' ) );
        $this->assertSame( "List[]=one\nList[]=two\n", $r[1] );

        $this->assertExit( 2, $this->ini( array( 'get', 'site.ini/SiteSettings/Nope', 'global' ) ) );

        // in effect: this installation, read only
        $data = $this->json( array( 'get', 'site.ini/SiteSettings/SiteName' ), false );
        $this->assertSame( 0, $data['code'] );
        $this->assertNotNull( $data['data']['value'], 'a value in effect' );
        $this->assertExit( 2, $this->ini( array( 'get', 'site.ini/SiteSettings/NoSuchVariableIC08' ), false ) );
        $this->assertSame( "Override site\n", $this->ini( array( 'get', 'site.ini/SiteSettings/SiteName' ) )[1], 'in effect under --root: the override wins' );
    }

    /** IC-09: on this installation, read only (the settings in effect are merged by eZINI, which knows no --root) */
    public function testWhere()
    {
        $data = $this->json( array( 'where', 'site.ini/SiteSettings/SiteName', 'site' ), false );
        $this->assertSame( 0, $data['code'] );
        $this->assertTrue( $data['data']['found'] );
        $this->assertSame( 'site', $data['data']['siteaccess'] );
        $paths = array_column( $data['data']['files'], 'path' );
        $this->assertSame( 'settings/site.ini', $paths[0], 'the defaults first' );
        $this->assertSame( 'default', $data['data']['files'][0]['placement'] );
        $last = end( $data['data']['files'] );
        $this->assertSame( $last['value'], $data['data']['effective'], 'the last file wins for a plain value' );

        $r = $this->ini( array( 'where', 'site.ini/SiteSettings/SiteName', 'site' ), false );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( ' 1. settings/site.ini', $r[1] );
        $this->assertStringContainsString( 'In effect:', $r[1] );
        $this->assertExit( 2, $this->ini( array( 'where', 'site.ini/SiteSettings/NoSuchVariableIC09', 'site' ), false ) );
        $this->assertExit( 3, $this->ini( array( 'where', 'site.ini/SiteSettings/SiteName', 'nosuchsiteaccess' ), false ) );
        $this->assertExit( 1, $this->ini( array( 'where', 'site.ini/SiteSettings/SiteName' ) ), 'not with --root' );
    }

    /** IC-10 */
    public function testList()
    {
        $r = $this->ini( array( 'list', 'site.ini', 'global' ) );
        $this->assertExit( 0, $r );
        foreach ( array( '[SiteSettings]', '[ExtensionSettings]', '[DatabaseSettings]', '[DebugSettings]' ) as $b )
            $this->assertStringContainsString( $b, $r[1] );

        $r = $this->ini( array( 'list', 'site.ini/FixtureSettings', 'extension:fixtureext' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( "List[]=one\nList[]=two", $r[1] );
        $this->assertStringContainsString( "Map[a]=1", $r[1] );

        $this->assertExit( 2, $this->ini( array( 'list', 'site.ini/Nope', 'global' ) ) );
        $this->assertExit( 2, $this->ini( array( 'list', 'menu.ini', 'global' ) ) );
    }

    /** IC-11 */
    public function testScopesAndActions()
    {
        $data = $this->json( array( 'scopes' ) );
        $names = array_column( $data['data']['scopes'], 'name' );
        foreach ( array( 'global', 'default', 'siteaccess:site', 'siteaccess:admin', 'extension:fixtureext',
                         'extension:fixtureext:siteaccess:admin' ) as $s )
            $this->assertContains( $s, $names );

        $data = $this->json( array( 'actions' ) );
        $actions = array_column( $data['data']['actions'], 'name' );
        foreach ( array_keys( expIniActionRegistry::BUILT_IN ) as $a )
            $this->assertContains( $a, $actions );
        $this->assertSame( array(), $data['data']['problems'] );
    }

    /** IC-12 */
    public function testScopeKinds()
    {
        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'Bare', 'admin' ) ), 'bare siteaccess' );
        $this->assertStringContainsString( 'SiteName=Bare', $this->file( 'settings/siteaccess/admin/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/FixtureSettings/Flag', 'on', 'extension:fixtureext' ) ) );
        $this->assertStringContainsString( 'Flag=on', $this->file( 'extension/fixtureext/settings/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/FixtureSettings/Flag', 'off', 'extension:fixtureext:siteaccess:admin' ) ) );
        $this->assertStringContainsString( 'Flag=off', $this->file( 'extension/fixtureext/settings/siteaccess/admin/site.ini.append.php' ) );

        $this->assertExit( 3, $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x', 'siteaccess:nosuchsa' ) ) );
        $this->assertExit( 3, $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x', 'extension:nosuchext' ) ) );

        $before = $this->file( 'settings/site.ini' );
        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x', 'default' ) );
        $this->assertExit( 3, $r );
        $this->assertSame( $before, $this->file( 'settings/site.ini' ) );
        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'x', 'default', '--allow-default' ) ) );
        $this->assertStringContainsString( "SiteName=x\n", $this->file( 'settings/site.ini' ) );
    }

    /** IC-13 */
    public function testDryRun()
    {
        $before = $this->file( self::OVERRIDE );
        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'Dry', 'global', '--dry-run' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( '-SiteName=Override site', $r[1] );
        $this->assertStringContainsString( '+SiteName=Dry', $r[1] );
        $this->assertStringContainsString( 'nothing written', $r[1] );
        $this->assertSame( $before, $this->file( self::OVERRIDE ) );
        $this->assertDirectoryDoesNotExist( $this->root . '/var/backup' );

        $defaults = $this->file( 'settings/site.ini' );
        $r = $this->ini( array( 'set', 'site.ini/SiteSettings/SiteName', 'Dry', 'default', '--dry-run' ) );
        $this->assertExit( 0, $r, 'a dry run on the defaults shows the change' );
        $this->assertStringContainsString( '+SiteName=Dry', $r[1] );
        $this->assertSame( $defaults, $this->file( 'settings/site.ini' ) );
    }

    /** IC-14 */
    public function testJson()
    {
        $data = $this->json( array( 'set', 'site.ini/SiteSettings/SiteName', 'Json', 'global' ) );
        $this->assertTrue( $data['ok'] );
        $this->assertSame( 'set', $data['action'] );
        $this->assertSame( 'global', $data['data']['scope'] );
        $this->assertTrue( $data['data']['changed'] );
        $this->assertSame( 'settings/override/site.ini.append.php', $data['data']['path'] );
        $this->assertStringContainsString( '+SiteName=Json', $data['data']['diff'] );

        foreach ( array( array( array( 'frobnicate' ), 1 ),
                         array( array( 'set', 'site.ini/S/V', 'x' ), 1 ),
                         array( array( 'rem', 'site.ini/S/V', 'global' ), 2 ),
                         array( array( 'set', 'site.ini/S/V', 'x', 'siteaccess:nosuchsa' ), 3 ),
                         array( array( 'toggle', 'site.ini/DebugSettings/Mode', 'global' ), 3 ) ) as $case )
        {
            $data = $this->json( $case[0] );
            $this->assertFalse( $data['ok'] );
            $this->assertSame( $case[1], $data['code'], implode( ' ', $case[0] ) );
            $this->assertNotSame( '', $data['message'] );
        }
    }

    /** IC-15 */
    public function testSecretsAreMasked()
    {
        $r = $this->ini( array( 'get', 'site.ini/DatabaseSettings/Password', 'global' ) );
        $this->assertSame( "********\n", $r[1] );
        $r = $this->ini( array( 'get', 'site.ini/DatabaseSettings/Password', 'global', '--show-secrets' ) );
        $this->assertSame( "s3cretpw\n", $r[1] );

        $r = $this->ini( array( 'list', 'site.ini/DatabaseSettings', 'global' ) );
        $this->assertStringContainsString( 'Password=********', $r[1] );
        $this->assertStringNotContainsString( 's3cretpw', $r[1] );

        // where on this installation: every value masked (asserted without printing any of them)
        list( $code, $out ) = $this->ini( array( 'where', 'site.ini/DatabaseSettings/Password', 'site', '--json' ), false );
        $data = json_decode( $out, true );
        $this->assertSame( 0, $code );
        $values = array_merge( array_column( $data['data']['files'], 'value' ), array( $data['data']['effective'] ) );
        $masked = array_filter( $values, function ( $v ) { return $v === '' || $v === '********'; } );
        $this->assertTrue( count( $masked ) === count( $values ), 'every Password value of where is masked' );
        list( , $text ) = $this->ini( array( 'where', 'site.ini/DatabaseSettings/Password', 'site' ), false );
        $this->assertTrue( preg_match_all( '/Password=(?!\*{8}$)\S+$/m', $text ) === 0, 'the text output masks them too' );

        $r = $this->ini( array( 'set', 'site.ini/DatabaseSettings/Password', 'newpw', 'global', '--dry-run' ) );
        $this->assertStringNotContainsString( 's3cretpw', $r[1] );
        $this->assertStringNotContainsString( 'newpw', $r[1] );
        $this->assertStringContainsString( '+Password=********', $r[1] );

        $data = $this->json( array( 'set', 'site.ini/DatabaseSettings/Password', 'newpw', 'global' ) );
        $this->assertStringNotContainsString( 'newpw', json_encode( $data ) );
        $this->assertStringContainsString( 'Password=newpw', $this->file( self::OVERRIDE ), 'written as given' );
    }

    /** IC-16 */
    public function testBackupAndCache()
    {
        $before = $this->file( self::OVERRIDE );
        $data = $this->json( array( 'set', 'site.ini/SiteSettings/SiteName', 'Backed up', 'global' ) );
        $this->assertNotEmpty( $data['data']['backup'] );
        $this->assertFileExists( $data['data']['backup'] );
        $this->assertSame( $before, file_get_contents( $data['data']['backup'] ), 'the backup is the file before the write' );
        $this->assertFalse( $data['data']['cache_cleared'], 'never with --root' );

        $data = $this->json( array( 'set', 'site.ini/SiteSettings/SiteName', 'No backup', 'global', '--no-backup' ) );
        $this->assertEmpty( $data['data']['backup'] );

        $this->assertStringContainsString( 'exp:velocity restart', expIniCommandContext::reloadHint( true ) );
        $this->assertStringContainsString( 'exp:cache ini', expIniCommandContext::reloadHint( false ) . ( defined( 'EZP_INI_FILEMTIME_CHECK' ) && !EZP_INI_FILEMTIME_CHECK ? '' : ' exp:cache ini' ) );
    }

    /** IC-17 */
    public function testCopy()
    {
        $this->assertExit( 0, $this->ini( array( 'copy', 'site.ini/SiteSettings/SiteName', 'global', 'siteaccess:admin' ) ) );
        $this->assertStringContainsString( 'SiteName=Override site', $this->file( 'settings/siteaccess/admin/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'copy', 'site.ini/FixtureSettings/List', 'extension:fixtureext', 'global' ) ) );
        $this->assertStringContainsString( "List[]\nList[]=one\nList[]=two", $this->file( self::OVERRIDE ) );
        $r = $this->ini( array( 'copy', 'site.ini/FixtureSettings/List', 'extension:fixtureext', 'global' ) );
        $this->assertExit( 0, $r );
        $this->assertStringContainsString( 'Nothing to change', $r[1], 'copying again changes nothing' );

        $this->assertExit( 0, $this->ini( array( 'copy', 'site.ini/FixtureSettings/Map[b]', 'extension:fixtureext', 'siteaccess:site' ) ) );
        $this->assertStringContainsString( 'Map[b]=2', $this->file( 'settings/siteaccess/site/site.ini.append.php' ) );

        $this->assertExit( 2, $this->ini( array( 'copy', 'site.ini/SiteSettings/Nope', 'global', 'siteaccess:admin' ) ) );
        $this->assertExit( 1, $this->ini( array( 'copy', 'site.ini/SiteSettings/SiteName', 'global', 'global' ) ) );
    }

    /** IC-18 */
    public function testSettingSyntaxes()
    {
        $this->assertSame( "Override site\n", $this->ini( array( 'get', 'site:SiteSettings.SiteName', 'global' ) )[1] );
        $this->assertSame( "Override site\n", $this->ini( array( 'get', 'site.ini [SiteSettings] SiteName', 'global' ) )[1] );
        $this->assertSame( "Override site\n", $this->ini( array( 'get', 'site.ini', '[SiteSettings]', 'SiteName', 'global' ) )[1] );
        $this->assertSame( "Override site\n", $this->ini( array( 'get', 'site/SiteSettings/SiteName', 'global' ) )[1] );

        $this->assertExit( 0, $this->ini( array( 'set', 'site.ini/DebugSettings/Offset', '--', '-1', 'global' ) ) );
        $this->assertStringContainsString( "Offset=-1\n", $this->file( self::OVERRIDE ) );
    }
}
