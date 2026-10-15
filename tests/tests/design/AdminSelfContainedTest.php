<?php
/**
 * admin4 works with no other admin design present (doc/features/6.0/admin4-design.md). Every template a view of the
 * kernel fetches or an admin4 template includes ("design:<path>.tpl") is found in admin4 itself or in
 * design/standard, never only in design/admin or design/admin3; every override template that an override.ini of the
 * tree names (MatchFile) is in admin4's or standard's override folder when a classic admin design has it. A path
 * found in no design at all (an extension's, or a front-end template) is not this test's concern.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminSelfContainedTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminSelfContainedTest extends PHPUnit\Framework\TestCase
{
    /** Where admin4 finds a template itself */
    const OWN = array( 'admin4', 'standard' );

    /** The admin designs admin4 must not need */
    const OTHERS = array( 'admin3', 'admin' );

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * Returns the files under a directory of the root whose name ends in $suffix
     */
    private static function files( $dir, $suffix )
    {
        $found = array();
        $path = self::root() . '/' . $dir;
        if ( !is_dir( $path ) )
            return $found;
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) ) as $file )
        {
            if ( substr( $file->getFilename(), -strlen( $suffix ) ) === $suffix )
                $found[] = $file->getPathname();
        }
        return $found;
    }

    /**
     * Returns the template paths named as "design:<path>.tpl" in a source text, each once
     */
    public static function designReferences( $source )
    {
        preg_match_all( '#design:([A-Za-z0-9_./-]+\.tpl)#', $source, $m );
        return array_values( array_unique( $m[1] ) );
    }

    /**
     * Returns the paths below $sub (templates or override/templates) that a classic admin design has and admin4 and
     * standard do not, from a list of path => where it is named
     */
    private static function onlyInOtherDesigns( array $references, $sub )
    {
        $root = self::root();
        $missing = array();
        foreach ( $references as $path => $where )
        {
            foreach ( self::OWN as $design )
                if ( is_file( "$root/design/$design/$sub/$path" ) )
                    continue 2;
            foreach ( self::OTHERS as $design )
            {
                if ( is_file( "$root/design/$design/$sub/$path" ) )
                {
                    $missing[] = "$path (only in design/$design; named in $where)";
                    continue 2;
                }
            }
        }
        sort( $missing );
        return $missing;
    }

    public function testFindsTheReferences()
    {
        $this->assertSame( array( 'audit/menu.tpl', 'parts/x.tpl' ),
            self::designReferences( "{include uri='design:audit/menu.tpl'} \$tpl->fetch( \"design:parts/x.tpl\" ); 'design:audit/menu.tpl'" ) );
    }

    public function testEveryTemplateAdminFourNeedsIsInAdminFourOrStandard()
    {
        $root = self::root();
        $sources = array_merge( self::files( 'kernel', '.php' ), self::files( 'lib', '.php' ),
                                self::files( 'design/admin4/templates', '.tpl' ), self::files( 'design/admin4/override/templates', '.tpl' ) );
        $references = array();
        foreach ( $sources as $file )
        {
            foreach ( self::designReferences( (string)file_get_contents( $file ) ) as $path )
            {
                if ( !isset( $references[$path] ) )
                    $references[$path] = substr( $file, strlen( $root ) + 1 );
            }
        }
        $this->assertGreaterThan( 300, count( $references ), 'the scan found the views\' and templates\' references' );
        $this->assertArrayHasKey( 'audit/dashboard.tpl', $references );
        $this->assertSame( array(), self::onlyInOtherDesigns( $references, 'templates' ),
            'admin4 would take these templates from another admin design: copy them into design/admin4/templates' );
    }

    public function testEveryOverrideTemplateOfTheTreeIsInAdminFourOrStandard()
    {
        $root = self::root();
        $references = array();
        foreach ( array_merge( glob( "$root/settings/{,*/,*/*/}override.ini*", GLOB_BRACE ) ?: array(),
                               glob( "$root/extension/*/settings/{,*/,*/*/}override.ini*", GLOB_BRACE ) ?: array() ) as $file )
        {
            preg_match_all( '/^MatchFile=(\S+)/m', (string)file_get_contents( $file ), $m );
            foreach ( $m[1] as $path )
            {
                if ( !isset( $references[$path] ) )
                    $references[$path] = substr( $file, strlen( $root ) + 1 );
            }
        }
        $this->assertSame( array(), self::onlyInOtherDesigns( $references, 'override/templates' ),
            'admin4 would take these override templates from another admin design: copy them into design/admin4/override/templates' );
    }
}
