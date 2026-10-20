<?php
/**
 * The exphelp module's access (doc/eng-us/features/6.0/help-system.md): every view needs the policy function read, the
 * module is registered, and Anonymous is never given it - not by AnonymousAccessList, not by PolicyOmitList, not by
 * the clean install data and not by an update script. Read from the files; no database.
 *
 *  HP-01  module.php declares the views dashboard, viewer, search, asset, report, each with functions = read
 *  HP-02  FunctionList has read (and nothing that bypasses it)
 *  HP-03  settings/module.ini ModuleList[] contains exphelp
 *  HP-04  settings/site.ini: exphelp is in neither AnonymousAccessList[] nor PolicyOmitList[]
 *  HP-05  the clean data and every update SQL: no policy of the Anonymous role (1) for exphelp or for every module
 *  HP-06  the view scripts check the policy or rely on the module's functions, and never answer without it
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expHelpModulePolicyTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    /** @return array the $ViewList and $FunctionList of kernel/exphelp/module.php, read in a closure */
    private static function module()
    {
        $file = self::root() . '/kernel/exphelp/module.php';
        $read = static function ( $file )
        {
            $Module = $ViewList = $FunctionList = null;
            include $file;
            return array( 'module' => $Module, 'views' => $ViewList, 'functions' => $FunctionList );
        };
        return $read( $file );
    }

    /** HP-01 */
    public function testEveryViewNeedsRead()
    {
        $m = self::module();
        $this->assertIsArray( $m['views'] );
        foreach ( array( 'dashboard', 'viewer', 'search', 'asset', 'report' ) as $view )
        {
            $this->assertArrayHasKey( $view, $m['views'], 'view ' . $view );
        }
        foreach ( $m['views'] as $name => $view )
        {
            $this->assertArrayHasKey( 'functions', $view, $name . ' must name its policy function' );
            // the context help JSON of help maintained as content has its own function (doc/eng-us/features/6.0/help-content.md)
            $expected = $name === 'context' ? array( 'context' ) : array( 'read' );
            $this->assertSame( $expected, array_values( (array)$view['functions'] ), $name . ' needs exphelp/' . $expected[0] );
        }
    }

    /** HP-02 */
    public function testFunctionList()
    {
        $m = self::module();
        $this->assertSame( array( 'read', 'context' ), array_keys( $m['functions'] ) );
        $this->assertSame( 'read', expHelp::POLICY_FUNCTION );
    }

    /** HP-03 */
    public function testRegisteredInModuleIni()
    {
        $text = file_get_contents( self::root() . '/settings/module.ini' );
        $this->assertMatchesRegularExpression( '/^ModuleList\[\]=exphelp\s*$/m', $text );
    }

    /** HP-04 */
    public function testNotOpenToAnonymous()
    {
        $text = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertDoesNotMatchRegularExpression( '/^AnonymousAccessList\[\]=exphelp\b/m', $text );
        $this->assertDoesNotMatchRegularExpression( '/^PolicyOmitList\[\]=exphelp\b/m', $text );
        // the settings shipped for the admin siteaccesses of the kickstart packages
        foreach ( glob( self::root() . '/settings/siteaccess/*/site.ini.append.php' ) ?: array() as $file )
        {
            $t = file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/^(AnonymousAccessList|PolicyOmitList)\[\]=exphelp\b/m', $t, $file );
        }
    }

    /**
     * the policies (role_id, module_name) of every INSERT INTO ezpolicy, whatever the column order
     * @return array[]
     */
    private static function policies( $sql )
    {
        $out = array();
        if ( !preg_match_all( '/INSERT\s+INTO\s+ezpolicy\s*\(([^)]*)\)\s*VALUES\s*(.*?);\s*$/ims', $sql, $m, PREG_SET_ORDER ) )
            return $out;
        foreach ( $m as $insert )
        {
            $cols = array_map( 'trim', explode( ',', strtolower( $insert[1] ) ) );
            if ( !preg_match_all( '/\(([^()]*)\)/s', $insert[2], $rows ) )
                continue;
            foreach ( $rows[1] as $row )
            {
                $vals = array_map( static function ( $v ) { return trim( trim( $v ), "'\"" ); }, str_getcsv( $row, ',', "'", '\\' ) );
                if ( count( $vals ) !== count( $cols ) )
                    continue;
                $out[] = array_combine( $cols, $vals );
            }
        }
        return $out;
    }

    /** HP-05 */
    public function testNoAnonymousPolicyInInstallOrUpdateData()
    {
        $files = array_merge( array( self::root() . '/kernel/sql/common/cleandata.sql' ),
                              glob( self::root() . '/update/database/*/6.0/*.sql' ) ?: array() );
        $seen = 0;
        foreach ( $files as $file )
        {
            if ( !is_file( $file ) )
                continue;
            foreach ( self::policies( file_get_contents( $file ) ) as $p )
            {
                $seen++;
                if ( !isset( $p['role_id'], $p['module_name'] ) || (int)$p['role_id'] !== 1 )
                    continue;
                $this->assertNotSame( 'exphelp', $p['module_name'], basename( $file ) . ' grants exphelp to Anonymous' );
                $this->assertNotSame( '*', $p['module_name'], basename( $file ) . ' grants every module to Anonymous' );
            }
        }
        $this->assertGreaterThan( 0, $seen, 'the clean data was parsed' );
        // an update script that grants exphelp names the role by name, never Anonymous
        foreach ( glob( self::root() . '/update/common/scripts/6.0/*' ) ?: array() as $file )
        {
            $t = (string)file_get_contents( $file );
            if ( stripos( $t, 'exphelp' ) === false )
                continue;
            $this->assertDoesNotMatchRegularExpression( '/Anonymous/i', $t, basename( $file ) . ' must not give exphelp to Anonymous' );
        }
    }

    /** HP-06 */
    public function testViewScriptsDoNotOpenThemselves()
    {
        foreach ( glob( self::root() . '/kernel/exphelp/*.php' ) as $file )
        {
            $t = file_get_contents( $file );
            // a view that runs without the module's policy check would set this or skip the access result
            $this->assertStringNotContainsString( 'PolicyOmit', $t, basename( $file ) );
            $this->assertDoesNotMatchRegularExpression( '/hasAccessTo\(\s*[\'"]exphelp[\'"]\s*,\s*[\'"](?!read)/', $t, basename( $file ) );
        }
    }
}
