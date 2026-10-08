<?php
/**
 * eZTemplateDesignResource::allDesignBases( null ) with DesignLocationCache=enabled (Setup > Templates passes null for
 * "the current siteaccess") is the current siteaccess's design list:
 *   - no "md5(): Passing null to parameter #1" deprecation
 *   - the list is cached under the current siteaccess's name, not under the empty name of a script without a
 *     siteaccess (which then held the design list of whichever siteaccess called it with null first)
 *
 * Writes only a design base cache file under a throwaway cache directory.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZTemplateDesignResourceNullSiteAccessTest extends PHPUnit\Framework\TestCase
{
    private $savedAccess;
    private $savedCacheDir;
    private $dir;

    protected function setUp(): void
    {
        $this->savedAccess = $GLOBALS['eZCurrentAccess'] ?? null;
        $name = 'phpunit-design-base-null-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        $ini = eZINI::instance( 'site.ini' );
        $this->savedCacheDir = array( $ini->variable( 'DesignSettings', 'DesignLocationCache' ), $ini->variable( 'FileSettings', 'CacheDir' ) );
        $ini->setVariable( 'DesignSettings', 'DesignLocationCache', 'enabled' );
        $ini->setVariable( 'FileSettings', 'CacheDir', $name );
        $this->dir = eZSys::cacheDirectory();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] );
        $GLOBALS['eZCurrentAccess'] = array( 'name' => 'b316designtest', 'type' => 0 );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'site.ini' );
        $ini->setVariable( 'DesignSettings', 'DesignLocationCache', $this->savedCacheDir[0] );
        $ini->setVariable( 'FileSettings', 'CacheDir', $this->savedCacheDir[1] );
        if ( $this->savedAccess === null )
            unset( $GLOBALS['eZCurrentAccess'] );
        else
            $GLOBALS['eZCurrentAccess'] = $this->savedAccess;
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] );
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        @rmdir( $this->dir );
    }

    public function testNullSiteAccessIsTheCurrentOne()
    {
        $deprecations = array();
        set_error_handler( function ( $no, $str ) use ( &$deprecations ) { $deprecations[] = $str; return true; }, E_DEPRECATED );
        try
        {
            $bases = eZTemplateDesignResource::allDesignBases( null );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $deprecations );
        $this->assertIsArray( $bases );
        $this->assertNotEmpty( $bases );
        $cacheDir = eZSys::cacheDirectory();
        $this->assertFileExists( $cacheDir . '/' . eZTemplateDesignResource::DESIGN_BASE_CACHE_NAME . md5( 'b316designtest' ) . '.php' );
        $this->assertFileDoesNotExist( $cacheDir . '/' . eZTemplateDesignResource::DESIGN_BASE_CACHE_NAME . md5( '' ) . '.php' );
    }
}
