<?php
/**
 * The editor's load order under another root (kernel/classes/ini/expinieditor.php loadOrder() and effectiveValue(),
 * used by exp:ini get, toggle and the safety check of move under --root, kernel/classes/ini/actions/expinimover.php)
 * against eZINI's own, on a fixture installation under var/tmp/ini-tests/. eZINI's order comes from a child process
 * (fixtures/expiniezinichain.php) that runs the kernel's siteaccess loading inside the fixture root. DB-less.
 *
 *  ILO-01 — the files the editor reads for a siteaccess are eZINI's input files, in the same order: settings/<file>.ini,
 *           the extensions of the siteaccess's ActiveAccessExtensions[] (first listed read last), the extensions'
 *           siteaccess, override__<group> and override directories, settings/siteaccess/<sa>, the extensions of
 *           ActiveExtensions[] (first listed read last), settings/override
 *  ILO-02 — the values in effect are eZINI's: an ActiveExtensions[] extension wins over settings/siteaccess/<sa>,
 *           an ActiveAccessExtensions[] extension loses to it, settings/override wins over both; arrays merge alike
 *  ILO-03 — ExtensionSettingsSiteAccess: the extension siteaccess directories of the named siteaccess are read too
 *  ILO-04 — without a siteaccess only settings/<file>.ini, the ActiveExtensions[] extensions and settings/override
 *  ILO-05 — exp:ini's --root setup (expIniCommand::useRoot()) reads ActiveExtensions[] and each siteaccess's
 *           ActiveAccessExtensions[] apart
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group ini
 */

require_once __DIR__ . '/fixtures/expinienginetestfixtures.php';

#[\PHPUnit\Framework\Attributes\Group('ini')]
class expIniLoadOrderTest extends PHPUnit\Framework\TestCase
{
    const PROBES = array( 'Probe/Who', 'Probe/SaWins', 'Probe/AccessLoses', 'Probe/OverrideWins', 'Probe/Seen' );

    private $root;

    public function setUp(): void
    {
        parent::setUp();
        $this->root = expIniEngineTestFixtures::runDir() . 'load-order-' . substr( md5( $this->name() ), 0, 8 ) . '/';
        $this->build();
        expIniCommand::useRoot( rtrim( $this->root, '/' ) );
    }

    public function tearDown(): void
    {
        expIniEditor::setRoot( null );
        self::remove( rtrim( $this->root, '/' ) );
        parent::tearDown();
    }

    /**
     * Removes a fixture tree. The kernel run in the child process creates var/ folders with the installation's file
     * modes, which on a CI runner left var/log unreadable: an iterator then threw, the tree stayed under var/tmp, and
     * later tests that walk var/ (the caption tests) failed on it (2026-11-01). Each folder is made readable first and
     * nothing here throws.
     *
     * @param string $path
     */
    private static function remove( $path )
    {
        if ( is_link( $path ) || is_file( $path ) )
        {
            @unlink( $path );
            return;
        }
        if ( !is_dir( $path ) )
            return;
        @chmod( $path, 0700 );
        foreach ( (array)@scandir( $path ) as $entry )
        {
            if ( $entry !== '.' && $entry !== '..' && $entry !== '' )
                self::remove( $path . '/' . $entry );
        }
        @rmdir( $path );
    }

    /**
     * settings/override activates extfirst and extsecond (ActiveExtensions[]); the siteaccess site activates
     * accfirst and accsecond (ActiveAccessExtensions[]), grp__en activates accfirst, admin takes the extension
     * settings of site (ExtensionSettingsSiteAccess). inactive is in no list. Every file sets [Probe] Who to its
     * own name and adds it to Seen[].
     */
    private function build()
    {
        $php = function ( $id, $extra = '' ) {
            return "<?php /* #?ini charset=\"utf-8\"?\n\n{$extra}[Probe]\nWho=$id\nSeen[]=$id\n*/ ?>\n";
        };
        $files = array(
            'settings/site.ini' => "#?ini charset=\"utf-8\"?\n\n[ExtensionSettings]\nExtensionDirectory=extension\nExtensionOrdering=disabled\n" .
                "RequiredExtensions[]\nActiveExtensions[]\nActiveAccessExtensions[]\n\n[SiteAccessSettings]\nAvailableSiteAccessList[]\n" .
                "AvailableSiteAccessList[]=site\nAvailableSiteAccessList[]=admin\nAvailableSiteAccessList[]=grp__en\n\n" .
                "[Probe]\nWho=default\nSaWins=default\nAccessLoses=default\nOverrideWins=default\nSeen[]\nSeen[]=default\n",
            'settings/override/site.ini.append.php' => $php( 'override',
                "[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=extfirst\nActiveExtensions[]=extsecond\n\n" ),
            'settings/siteaccess/site/site.ini.append.php' => $php( 'siteaccess-site',
                "[ExtensionSettings]\nActiveAccessExtensions[]=accfirst\nActiveAccessExtensions[]=accsecond\n\n" ),
            'settings/siteaccess/admin/site.ini.append.php' => $php( 'siteaccess-admin',
                "[SiteAccessSettings]\nExtensionSettingsSiteAccess=site\n\n" ),
            'settings/siteaccess/grp__en/site.ini.append.php' => $php( 'siteaccess-grp__en',
                "[ExtensionSettings]\nActiveAccessExtensions[]=accfirst\n\n" ),
            'extension/extfirst/settings/site.ini.append.php' => $php( 'extfirst' ),
            'extension/extfirst/settings/siteaccess/site/site.ini.append.php' => $php( 'extfirst-sa-site' ),
            'extension/extfirst/settings/siteaccess/grp__en/site.ini.append.php' => $php( 'extfirst-sa-grp__en' ),
            'extension/extfirst/settings/override/site.ini.append.php' => $php( 'extfirst-override' ),
            'extension/extfirst/settings/override__grp/site.ini.append.php' => $php( 'extfirst-override__grp' ),
            'extension/extsecond/settings/site.ini.append.php' => $php( 'extsecond' ),
            'extension/extsecond/settings/siteaccess/site/site.ini.append.php' => $php( 'extsecond-sa-site' ),
            'extension/extsecond/settings/siteaccess/admin/site.ini.append.php' => $php( 'extsecond-sa-admin' ),
            'extension/accfirst/settings/site.ini.append.php' => $php( 'accfirst' ),
            'extension/accfirst/settings/siteaccess/site/site.ini.append.php' => $php( 'accfirst-sa-site' ),
            'extension/accsecond/settings/site.ini.append.php' => $php( 'accsecond' ),
            'extension/inactive/settings/site.ini.append.php' => $php( 'inactive' ),
            'var/.keep' => '',
        );
        foreach ( $files as $path => $content )
        {
            if ( !is_dir( dirname( $this->root . $path ) ) )
                mkdir( dirname( $this->root . $path ), 0755, true );
            file_put_contents( $this->root . $path, $content );
        }
        // the variables whose winner tells the order apart
        $this->append( 'settings/siteaccess/site/site.ini.append.php', "SaWins=siteaccess-site\nAccessLoses=siteaccess-site\nOverrideWins=siteaccess-site\n" );
        $this->append( 'extension/extsecond/settings/site.ini.append.php', "SaWins=extsecond\nOverrideWins=extsecond\n" );
        $this->append( 'extension/accfirst/settings/site.ini.append.php', "AccessLoses=accfirst\n" );
        $this->append( 'settings/override/site.ini.append.php', "OverrideWins=override\n" );
    }

    /** Adds lines at the end of the [Probe] block, which every fixture file ends with. */
    private function append( $path, $lines )
    {
        $content = file_get_contents( $this->root . $path );
        file_put_contents( $this->root . $path, str_replace( "*/ ?>\n", $lines . "*/ ?>\n", $content ) );
    }

    /** eZINI's own input files and values for a siteaccess (null: none), from the child process. */
    private function ezini( $siteAccess )
    {
        $command = array_merge( array( PHP_BINARY, __DIR__ . '/fixtures/expiniezinichain.php', $this->root,
                                       $siteAccess === null ? '-' : $siteAccess, 'site.ini' ), self::PROBES );
        $p = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $this->root );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $p );
        $data = json_decode( trim( $out ), true );
        $this->assertSame( 0, $code, "eZINI child process failed\nstdout: $out\nstderr: $err" );
        $this->assertIsArray( $data, "no JSON from the eZINI child process\nstdout: $out\nstderr: $err" );
        return $data;
    }

    /** The files of the editor's load order that exist, relative to the fixture root. */
    private function editorFiles( $siteAccess )
    {
        $files = array();
        foreach ( expIniEditor::loadOrder( $siteAccess ) as $scope )
            if ( is_file( $scope->path( 'site' ) ) )
                $files[] = $scope->relativePath( 'site' );
        return $files;
    }

    private function editorValues( $siteAccess )
    {
        $values = array();
        foreach ( self::PROBES as $probe )
        {
            list( $block, $variable ) = explode( '/', $probe );
            $values[$probe] = expIniEditor::effectiveValue( 'site', $block, $variable, $siteAccess );
        }
        return $values;
    }

    /** ILO-01, ILO-02 */
    public function testSiteAccessWithAccessExtensions()
    {
        $ezini = $this->ezini( 'site' );
        $expected = array(
            'settings/site.ini',
            'extension/accsecond/settings/site.ini.append.php',
            'extension/accfirst/settings/site.ini.append.php',
            'extension/accfirst/settings/siteaccess/site/site.ini.append.php',
            'extension/extsecond/settings/siteaccess/site/site.ini.append.php',
            'extension/extfirst/settings/siteaccess/site/site.ini.append.php',
            'extension/extfirst/settings/override/site.ini.append.php',
            'settings/siteaccess/site/site.ini.append.php',
            'extension/extsecond/settings/site.ini.append.php',
            'extension/extfirst/settings/site.ini.append.php',
            'settings/override/site.ini.append.php',
        );
        $this->assertSame( $expected, $ezini['files'], 'the fixture shows eZINI\'s order' );
        $this->assertSame( $ezini['files'], $this->editorFiles( 'site' ) );
        $this->assertSame( $ezini['values'], $this->editorValues( 'site' ) );
        $this->assertSame( 'extsecond', $ezini['values']['Probe/SaWins'], 'ActiveExtensions[] wins over settings/siteaccess/<sa>' );
        $this->assertSame( 'siteaccess-site', $ezini['values']['Probe/AccessLoses'], 'ActiveAccessExtensions[] loses to it' );
        $this->assertSame( 'override', $ezini['values']['Probe/OverrideWins'] );
    }

    /** ILO-01: a siteaccess named <group>__<name> also reads extension/<ext>/settings/override__<group> */
    public function testGroupSiteAccess()
    {
        $ezini = $this->ezini( 'grp__en' );
        $this->assertContains( 'extension/extfirst/settings/override__grp/site.ini.append.php', $ezini['files'] );
        $this->assertSame( $ezini['files'], $this->editorFiles( 'grp__en' ) );
        $this->assertSame( $ezini['values'], $this->editorValues( 'grp__en' ) );
    }

    /** ILO-03 */
    public function testExtensionSettingsSiteAccess()
    {
        $ezini = $this->ezini( 'admin' );
        $this->assertContains( 'extension/extfirst/settings/siteaccess/site/site.ini.append.php', $ezini['files'] );
        $this->assertNotContains( 'extension/accfirst/settings/site.ini.append.php', $ezini['files'], 'admin has no access extensions' );
        $this->assertSame( $ezini['files'], $this->editorFiles( 'admin' ) );
        $this->assertSame( $ezini['values'], $this->editorValues( 'admin' ) );
    }

    /** ILO-04 */
    public function testNoSiteAccess()
    {
        $ezini = $this->ezini( null );
        $this->assertSame( array( 'settings/site.ini', 'extension/extsecond/settings/site.ini.append.php',
                                  'extension/extfirst/settings/site.ini.append.php', 'settings/override/site.ini.append.php' ),
                           $ezini['files'] );
        $this->assertSame( $ezini['files'], $this->editorFiles( null ) );
        $this->assertSame( $ezini['values'], $this->editorValues( null ) );
    }

    /** ILO-05 */
    public function testRootSetupSeparatesTheLists()
    {
        $this->assertSame( array( 'extfirst', 'extsecond' ), expIniEditor::defaultExtensions() );
        $this->assertSame( array( 'accfirst', 'accsecond' ), expIniEditor::accessExtensions( 'site' ) );
        $this->assertSame( array( 'accfirst' ), expIniEditor::accessExtensions( 'grp__en' ) );
        $this->assertSame( array(), expIniEditor::accessExtensions( 'admin' ) );
        $active = expIniEditor::activeExtensions();
        sort( $active );
        $this->assertSame( array( 'accfirst', 'accsecond', 'extfirst', 'extsecond' ), $active );
        $this->assertTrue( expIniEditor::scope( 'extension:accfirst' )->isActive() );
        $this->assertFalse( expIniEditor::scope( 'extension:inactive' )->isActive() );
    }
}
