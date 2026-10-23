<?php
/**
 * The admin scripts that run on plain JavaScript over Exponential UI (the relation upload and search, the sub items
 * table, queued publishing) find what they call on every admin design: admin, admin4 and admin4l (which falls back
 * to admin4).
 *
 * - each page that requests one of these scripts also gets the exp:: packs it needs, in its own ezscript_require or
 *   through BackendJavaScriptList, which the design's page_head_script.tpl loads in the head;
 * - the installed Exponential UI defines every member the scripts call (Exp.util.extend, Exp.util.request,
 *   Exp.dialog.create, Exp.upload.create, Exp.datatable.create, ...). Exp.util and the create() functions came with
 *   expui's plain JavaScript modules; with an older expui the packs load and each script stops with
 *   "Exponential UI (...) is not on the page" (alpha, 23.10.2026, after the admin4 scripts landed without them).
 *
 * DB-less. Run: php vendor/bin/phpunit tests/tests/design/AdminPlainScriptsExpUIPacksTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/AdminFiles.php';

class AdminPlainScriptsExpUIPacksTest extends PHPUnit\Framework\TestCase
{
    /** Script => the Exp members it calls */
    const SCRIPTS = array(
        'expajaxuploader.js' => array( 'Exp.util.extend', 'Exp.io', 'Exp.dialog.create', 'Exp.upload.create' ),
        'ezajaxrelations_jquery.js' => array( 'Exp.ready', 'Exp.util.request' ),
        'ezajaxsubitems_expdatatable.js' => array( 'Exp.util.extend', 'Exp.datatable.create' ),
        'subitems_priority_drag.js' => array( 'Exp.util.extend', 'Exp.datatable.create' ),
        'ezasynchronouspublishing.js' => array( 'Exp.util.extend', 'Exp.io' ),
    );

    /** Exp member => the packer key that brings it, and the file of expui that defines it with a pattern for that */
    const MEMBERS = array(
        'Exp.ready' => array( 'exp::core', 'core.js', '/\bExp\.ready\s*=\s*function/' ),
        'Exp.util.extend' => array( 'exp::core', 'core.js', '/\bExp\.util\s*=\s*\{.{0,600}?\bextend\s*:/s' ),
        'Exp.util.request' => array( 'exp::core', 'core.js', '/\bExp\.util\s*=\s*\{.{0,600}?\brequest\s*:/s' ),
        'Exp.io' => array( 'exp::io', 'io.js', '/\bExp\.io\s*=\s*\{/' ),
        'Exp.dialog.create' => array( 'exp::dialog', 'dialog.js', '/\bExp\.dialog\s*=\s*\{.{0,600}?\bcreate\s*:/s' ),
        'Exp.upload.create' => array( 'exp::upload', 'upload.js', '/\bExp\.upload\s*=\s*\{.{0,600}?\bcreate\s*:/s' ),
        'Exp.datatable.create' => array( 'exp::datatable', 'datatable.js', '/\bExp\.datatable\s*=\s*\{.{0,600}?\bcreate\s*:/s' ),
    );

    /** The admin designs' template directories, in the order a design falls back (admin4l -> admin4) */
    const DESIGNS = array(
        'admin' => array( 'design/admin/templates', 'design/standard/templates' ),
        'admin4' => array( 'design/admin4/templates', 'design/standard/templates' ),
        'admin4l' => array( 'design/admin4l/templates', 'design/admin4/templates', 'design/standard/templates' ),
    );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function expuiScripts()
    {
        $dir = self::root() . '/extension/expui/design/standard/javascript/exp';
        if ( !is_dir( $dir ) )
            self::markTestSkipped( 'Exponential UI (extension/expui) is not installed here' );
        return $dir;
    }

    /** The BackendJavaScriptList entries of the installation: settings/design.ini and the extensions' appends */
    private static function backendList()
    {
        $list = array();
        $files = array_merge( array( self::root() . '/settings/design.ini' ), glob( self::root() . '/extension/*/settings/design.ini.append.php' ) ?: array() );
        foreach ( $files as $file )
        {
            if ( preg_match_all( '/^BackendJavaScriptList\[\]=(\S+)\s*$/m', AdminFiles::read( $file ), $m ) )
                $list = array_merge( $list, $m[1] );
        }
        return $list;
    }

    /** exp::core::shared counts as exp::core */
    private static function packs( array $entries )
    {
        return array_map( function ( $e ) { return $e === 'exp::core::shared' ? 'exp::core' : $e; }, $entries );
    }

    /** The template a design uses for a relative path: the first directory of its fallback chain that has it */
    private static function resolve( $design, $relative )
    {
        foreach ( self::DESIGNS[$design] as $dir )
        {
            if ( is_file( self::root() . "/$dir/$relative" ) )
                return "$dir/$relative";
        }
        return null;
    }

    /** Every template path (relative to its templates directory) of the admin designs that requests $script */
    private static function requestingTemplates( $script )
    {
        $found = array();
        foreach ( array( 'design/admin/templates', 'design/admin4/templates', 'design/admin4l/templates', 'design/standard/templates' ) as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . "/$dir", FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $file )
            {
                if ( substr( $file, -4 ) !== '.tpl' )
                    continue;
                if ( preg_match( '/ezscript_require\([^)]*[\'"]' . preg_quote( $script, '/' ) . '[\'"]/', file_get_contents( $file ) ) )
                    $found[] = substr( $file, strlen( self::root() . "/$dir/" ) );
            }
        }
        return array_unique( $found );
    }

    public function testEachAdminDesignHeadLoadsBackendJavaScriptList()
    {
        foreach ( array_keys( self::DESIGNS ) as $design )
        {
            $head = self::resolve( $design, 'page_head_script.tpl' );
            $this->assertNotNull( $head, "$design: no page_head_script.tpl" );
            $this->assertStringContainsString( "'BackendJavaScriptList'", AdminFiles::read( self::root() . "/$head" ), "$design ($head) loads no BackendJavaScriptList" );
        }
    }

    public function testEachPageRequestingAScriptGetsItsPacks()
    {
        $backend = self::packs( self::backendList() );
        $checked = 0;
        foreach ( self::SCRIPTS as $script => $members )
        {
            foreach ( self::requestingTemplates( $script ) as $relative )
            {
                foreach ( array_keys( self::DESIGNS ) as $design )
                {
                    $tpl = self::resolve( $design, $relative );
                    if ( $tpl === null )
                        continue;
                    $source = AdminFiles::read( self::root() . "/$tpl" );
                    if ( !preg_match( '/ezscript_require\(\s*(array\(.*?\)|[\'"][^\'"]+[\'"])\s*\)/s', $source, $call )
                        || strpos( $call[1], $script ) === false )
                        continue;
                    preg_match_all( '/[\'"]([^\'"]+)[\'"]/', $call[1], $m );
                    $own = self::packs( $m[1] );
                    foreach ( $members as $member )
                    {
                        $pack = self::MEMBERS[$member][0];
                        $this->assertTrue( in_array( $pack, $own, true ) || in_array( $pack, $backend, true ),
                            "$design: $tpl requests $script, which calls $member, but neither it nor BackendJavaScriptList requests $pack" );
                        $checked++;
                    }
                }
            }
        }
        $this->assertGreaterThan( 0, $checked, 'no template requests any of the scripts: the check ran over nothing' );
    }

    public function testInstalledExponentialUIDefinesWhatTheScriptsCall()
    {
        $dir = self::expuiScripts();
        foreach ( self::SCRIPTS as $script => $members )
        {
            foreach ( $members as $member )
            {
                list( , $file, $pattern ) = self::MEMBERS[$member];
                $this->assertSame( 1, preg_match( $pattern, AdminFiles::read( "$dir/$file" ) ),
                    "$script calls $member, which the installed Exponential UI ($file) does not define: it needs the expui release with the plain JavaScript modules" );
            }
        }
    }

    /** The scripts still say so in the console when a member is missing, rather than throwing on a page */
    public function testScriptsNameTheirPacksWhenExpIsMissing()
    {
        foreach ( array( 'design/admin4/javascript/expajaxuploader.js', 'design/admin4/javascript/ezajaxrelations_jquery.js', 'design/standard/javascript/ezasynchronouspublishing.js' ) as $file )
            $this->assertStringContainsString( 'is not on the page', AdminFiles::read( self::root() . "/$file" ), $file );
    }
}
