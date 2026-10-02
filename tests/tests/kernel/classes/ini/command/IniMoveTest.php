<?php
/**
 * exp:ini move and move-all end to end, as real processes against a temporary installation root (--root).
 * Guide doc/bc/6.0/console-exp-ini.md, "Moving settings into an extension".
 *
 *  IM-01 — A block moves with its comments and blank lines, in order; the source loses it; an emptied source keeps
 *          its file and wrapper
 *  IM-02 — Between the scope kinds: global -> extension, siteaccess -> extension siteaccess, extension -> global
 *  IM-03 — A whole file, and move-all (every file of a siteaccess; --files)
 *  IM-04 — --only moves some variables of a block; the others stay
 *  IM-05 — A merge conflict: the source's value wins by default, the target's with --keep-target
 *  IM-06 — A move that changes a value in effect is refused (3), names the file that wins and is rolled back
 *          byte for byte (a file the move created is moved aside); --force keeps it
 *  IM-07 — An extension that does not exist: refused without --create-extension; created but not active: refused
 *          with the exact activation command; --create-extension --activate creates extension.xml, ezinfo.php and
 *          settings/ and activates it (ActiveExtensions[] in global, ActiveAccessExtensions[] for a siteaccess
 *          target); ActiveExtensions never moves into an extension
 *  IM-08 — --dry-run shows both diffs and the summary and writes nothing; --json carries the totals
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/iniroot.php';

class IniMoveTest extends PHPUnit\Framework\TestCase
{
    const OVERRIDE = 'settings/override/site.ini.append.php';
    const EXT = 'extension/fixtureext/settings/site.ini.append.php';

    private static $installation;
    private $root;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 6 );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $this->root = self::$installation . '/var/tmp/ini/b/move-' . getmypid() . '-' . substr( md5( $this->name() ), 0, 8 );
        iniCommandTestRoot::build( $this->root );
        // a block with comments and blank lines, set nowhere else
        $override = $this->file( self::OVERRIDE );
        $this->put( self::OVERRIDE, str_replace( "*/ ?>\n", "\n[MoveSettings]\n# about A\nA=1\n\n# about B\nB=2\nList[]\nList[]=x\n*/ ?>\n", $override ) );
        $this->put( 'settings/siteaccess/admin/content.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[VersionView]\nAvailableSiteDesignList[]\nAvailableSiteDesignList[]=admin\n*/ ?>\n" );
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->root ) )
            iniCommandTestRoot::remove( $this->root );
    }

    private function file( $path )
    {
        return file_get_contents( $this->root . '/' . $path );
    }

    private function put( $path, $content )
    {
        if ( !is_dir( dirname( $this->root . '/' . $path ) ) )
            mkdir( dirname( $this->root . '/' . $path ), 0755, true );
        file_put_contents( $this->root . '/' . $path, $content );
    }

    private function ini( array $args )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/ini.php' ), $args,
                                array( '--root=' . $this->root, '--allow-root-user', '--no-colors' ) );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        return array( $code, $out, trim( str_replace( 'With great power comes great responsibility.', '', $err ) ) );
    }

    private function assertExit( $expected, array $r, $what = '' )
    {
        $this->assertSame( $expected, $r[0], $what . "\nstdout: " . $r[1] . "\nstderr: " . $r[2] );
        return $r;
    }

    /** IM-01 */
    public function testBlockMovesWithCommentsInOrder()
    {
        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:fixtureext' ) ) );
        $this->assertStringContainsString( "[MoveSettings]\n# about A\nA=1\n\n# about B\nB=2\nList[]\nList[]=x\n", $this->file( self::EXT ) );
        $this->assertStringNotContainsString( 'MoveSettings', $this->file( self::OVERRIDE ) );
        $this->assertStringContainsString( "[DebugSettings]\nDebugOutput=Enabled", $this->file( self::OVERRIDE ), 'the rest stays' );
        $this->assertStringContainsString( 'Moved 1 block, 3 variables, 1 file', $r[1] );

        // the last block of a file: the source keeps its file and wrapper
        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/TemplateSettings', 'siteaccess:admin', 'extension:fixtureext:siteaccess:admin' ) ) );
        $admin = $this->file( 'settings/siteaccess/admin/site.ini.append.php' );
        $this->assertStringStartsWith( '<?php /* #?ini charset="utf-8"?', $admin );
        $this->assertStringContainsString( '*/ ?>', $admin );
        $this->assertStringNotContainsString( '[TemplateSettings]', $admin );
    }

    /** IM-02 */
    public function testScopeKinds()
    {
        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/TemplateSettings', 'siteaccess:admin', 'extension:fixtureext:siteaccess:admin' ) ) );
        $this->assertStringContainsString( "[TemplateSettings]\nDebug=true", $this->file( 'extension/fixtureext/settings/siteaccess/admin/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/FixtureSettings', 'extension:fixtureext', 'global' ) ) );
        $this->assertStringContainsString( "[FixtureSettings]\nList[]\nList[]=one\nList[]=two\nMap[a]=1\nMap[b]=2", $this->file( self::OVERRIDE ) );
        $this->assertStringNotContainsString( 'FixtureSettings', $this->file( self::EXT ) );

        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/DebugSettings', 'global', 'extension:fixtureext' ) ) );
        $this->assertStringContainsString( "[DebugSettings]\nDebugOutput=Enabled\nLevel=1\nMode=sometimes", $this->file( self::EXT ) );

        // global into one siteaccess: the other siteaccess would lose it
        $r = $this->assertExit( 3, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'siteaccess:site' ) ) );
        $this->assertStringContainsString( 'for siteaccess admin', $r[1] . $r[2] );
    }

    /** IM-03 */
    public function testWholeFileAndMoveAll()
    {
        $before = $this->file( 'settings/siteaccess/admin/site.ini.append.php' );
        $r = $this->assertExit( 0, $this->ini( array( 'move-all', 'siteaccess:admin', 'extension:fixtureext:siteaccess:admin', '--files=content' ) ) );
        $this->assertStringContainsString( 'AvailableSiteDesignList[]=admin', $this->file( 'extension/fixtureext/settings/siteaccess/admin/content.ini.append.php' ) );
        $this->assertSame( $before, $this->file( 'settings/siteaccess/admin/site.ini.append.php' ), '--files: only content.ini' );

        $this->put( 'settings/siteaccess/admin/menu.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[MenuSettings]\nAvailableMenuArray[]=TopOnly\n*/ ?>\n" );
        $r = $this->assertExit( 0, $this->ini( array( 'move-all', 'siteaccess:admin', 'extension:fixtureext:siteaccess:admin' ) ) );
        $this->assertStringContainsString( 'Moved 2 blocks, 2 variables, 2 files', $r[1], 'content.ini is empty now: nothing to move' );
        foreach ( array( 'site', 'menu' ) as $f )
            $this->assertFileExists( $this->root . "/extension/fixtureext/settings/siteaccess/admin/$f.ini.append.php" );

        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini', 'extension:fixtureext:siteaccess:admin', 'siteaccess:admin' ) ), 'a whole file back' );
        $this->assertStringContainsString( "[TemplateSettings]\nDebug=true", $this->file( 'settings/siteaccess/admin/site.ini.append.php' ) );
    }

    /** IM-04 */
    public function testOnly()
    {
        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:fixtureext', '--only=A,List' ) ) );
        $ext = $this->file( self::EXT );
        $this->assertStringContainsString( "[MoveSettings]\nA=1\nList[]\nList[]=x", $ext );
        $this->assertStringNotContainsString( 'B=2', $ext );
        $override = $this->file( self::OVERRIDE );
        $this->assertStringContainsString( 'B=2', $override );
        $this->assertStringNotContainsString( "\nA=1", $override );
        $this->assertStringContainsString( 'Moved 1 block, 2 variables, 1 file', $r[1] );
        $this->assertExit( 2, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:fixtureext', '--only=Nope' ) ) );
        $this->assertExit( 1, $this->ini( array( 'move', 'site.ini', 'global', 'extension:fixtureext', '--only=A' ) ) );
    }

    /** IM-05 */
    public function testMergeConflict()
    {
        $this->put( self::EXT, str_replace( "*/ ?>\n", "\n[MoveSettings]\nA=9\nC=3\n*/ ?>\n", $this->file( self::EXT ) ) );
        // global wins over the extension before and after: the source's A=1 replaces A=9
        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:fixtureext' ) ) );
        $this->assertStringContainsString( 'Conflict: [MoveSettings] A: source "1", target "9"', $r[1] );
        $this->assertStringContainsString( '1 conflict merged', $r[1] );
        $ext = $this->file( self::EXT );
        $this->assertStringContainsString( 'C=3', $ext );
        $this->assertStringContainsString( 'A=1', $ext );
        $this->assertStringNotContainsString( 'A=9', $ext );

        // the other way, keeping the target: global's own value stays, and it is what is in effect anyway
        $this->put( self::OVERRIDE, str_replace( "*/ ?>\n", "\n[MoveSettings]\nA=5\n*/ ?>\n", $this->file( self::OVERRIDE ) ) );
        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'extension:fixtureext', 'global', '--keep-target' ) ) );
        $this->assertStringContainsString( "the target's kept", $r[1] );
        $override = $this->file( self::OVERRIDE );
        $this->assertStringContainsString( 'A=5', $override );
        $this->assertStringContainsString( 'C=3', $override );
        $this->assertStringNotContainsString( 'A=1', $override );
    }

    /** IM-06 */
    public function testRefusedAndRolledBack()
    {
        $override = $this->file( self::OVERRIDE );
        $ext = $this->file( self::EXT );
        // SiteName: the override wins over settings/siteaccess/site today; in the extension it would lose
        $r = $this->assertExit( 3, $this->ini( array( 'move', 'site.ini/SiteSettings', 'global', 'extension:fixtureext' ) ) );
        $this->assertStringContainsString( 'Refused and rolled back', $r[1] . $r[2] );
        $this->assertStringContainsString( 'settings/siteaccess/site/site.ini.append.php wins', $r[1] . $r[2] );
        $this->assertSame( $override, $this->file( self::OVERRIDE ), 'the source is as it was' );
        $this->assertSame( $ext, $this->file( self::EXT ), 'the target is as it was' );

        // a target file the move created is moved aside, not left behind
        $r = $this->assertExit( 3, $this->ini( array( 'move', 'site.ini/SiteSettings', 'global', 'extension:fixtureext:siteaccess:admin' ) ) );
        $this->assertFileDoesNotExist( $this->root . '/extension/fixtureext/settings/siteaccess/admin/site.ini.append.php' );
        $this->assertNotEmpty( glob( $this->root . '/var/backup/ini/*-rollback/extension/fixtureext/settings/siteaccess/admin/site.ini.append.php' ) );
        $this->assertSame( $override, $this->file( self::OVERRIDE ) );

        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/SiteSettings', 'global', 'extension:fixtureext', '--force' ) ) );
        $this->assertStringContainsString( 'kept with --force', $r[1] );
        $this->assertStringContainsString( 'SiteName=Override site', $this->file( self::EXT ) );
    }

    /** IM-07 */
    public function testCreateAndActivateExtension()
    {
        $r = $this->assertExit( 3, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:mysite' ) ) );
        $this->assertStringContainsString( '--create-extension', $r[1] . $r[2] );
        $this->assertDirectoryDoesNotExist( $this->root . '/extension/mysite' );

        $r = $this->assertExit( 3, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:mysite', '--create-extension' ) ) );
        $this->assertStringContainsString( 'exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] mysite global', $r[1] . $r[2] );
        $this->assertFileExists( $this->root . '/extension/mysite/extension.xml' );

        $this->assertExit( 1, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:My-Site', '--create-extension' ) ), 'a-z0-9_ only' );

        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:mysite', '--create-extension', '--activate' ) ) );
        $this->assertStringContainsString( "ActiveExtensions[]=mysite", $this->file( self::OVERRIDE ) );
        $this->assertStringContainsString( "[MoveSettings]\n# about A\nA=1", $this->file( 'extension/mysite/settings/site.ini.append.php' ) );

        $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/DebugSettings', 'global', 'extension:newsite', '--create-extension', '--activate' ) ) );
        $info = $this->file( 'extension/newsite/ezinfo.php' );
        foreach ( array( 'class newsiteInfo', 'public static function info()', "'Version' => \"1.0.0\"",
                         'GNU General Public License v2.0 (or any later version)', '7x & Exponential Foundation' ) as $t )
            $this->assertStringContainsString( $t, $info );
        exec( escapeshellarg( PHP_BINARY ) . ' -l ' . escapeshellarg( $this->root . '/extension/newsite/ezinfo.php' ), $o, $lint );
        $this->assertSame( 0, $lint );
        $xml = simplexml_load_file( $this->root . '/extension/newsite/extension.xml' );
        $this->assertSame( '1.0.0', (string)$xml->metadata->version );
        $this->assertSame( fileowner( $this->root . '/extension' ), fileowner( $this->root . '/extension/newsite/settings' ) );

        // a siteaccess target: ActiveAccessExtensions[] of that siteaccess; the activation itself stays behind
        $r = $this->assertExit( 0, $this->ini( array( 'move-all', 'siteaccess:admin', 'extension:adminext:siteaccess:admin', '--create-extension', '--activate' ) ) );
        $admin = $this->file( 'settings/siteaccess/admin/site.ini.append.php' );
        $this->assertStringContainsString( "[ExtensionSettings]\nActiveAccessExtensions[]=adminext", $admin );
        $this->assertStringNotContainsString( 'ActiveAccessExtensions', $this->file( 'extension/adminext/settings/siteaccess/admin/site.ini.append.php' ) );
        $this->assertStringContainsString( "[TemplateSettings]\nDebug=true", $this->file( 'extension/adminext/settings/siteaccess/admin/site.ini.append.php' ) );
    }

    /** IM-08 */
    public function testDryRunAndJson()
    {
        $override = $this->file( self::OVERRIDE );
        $r = $this->assertExit( 0, $this->ini( array( 'move', 'site.ini/MoveSettings', 'global', 'extension:mysite', '--create-extension', '--activate', '--dry-run' ) ) );
        $this->assertStringContainsString( '+[MoveSettings]', $r[1], 'the target diff' );
        $this->assertStringContainsString( '-[MoveSettings]', $r[1], 'the source diff' );
        $this->assertStringContainsString( '+ActiveExtensions[]=mysite', $r[1], 'the activation diff' );
        $this->assertStringContainsString( 'Dry run: would move 1 block, 3 variables, 1 file', $r[1] );
        $this->assertSame( $override, $this->file( self::OVERRIDE ) );
        $this->assertDirectoryDoesNotExist( $this->root . '/extension/mysite' );

        list( $code, $out ) = $this->ini( array( 'move-all', 'siteaccess:admin', 'extension:fixtureext:siteaccess:admin', '--json' ) );
        $data = json_decode( $out, true );
        $this->assertSame( 0, $code, $out );
        $this->assertSame( array( 'blocks' => 2, 'variables' => 2, 'files' => 2, 'conflicts' => 0 ), $data['data']['totals'] );
        $this->assertSame( array( 'content', 'site' ), array_column( $data['data']['files'], 'file' ) );
    }
}
