<?php
/**
 * eZTemplateDesignResource::allDesignBases() and overrideArray() without a siteaccess given as null or '' (the
 * template list reads the siteaccess from a session variable that may never have been set): the current
 * siteaccess, as with false, and no "md5(): Passing null" deprecation from the design location cache name.
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZTemplateDesignResourceNoSiteAccessTest extends PHPUnit\Framework\TestCase
{
    private $designLocationCache;

    public static function setUpBeforeClass(): void
    {
        // The cluster handler is started outside the tests: starting it installs a handler of its own
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        // The cache whose file name is built from the siteaccess name
        $ini = eZINI::instance( 'site.ini' );
        $this->designLocationCache = $ini->variable( 'DesignSettings', 'DesignLocationCache' );
        $ini->setVariable( 'DesignSettings', 'DesignLocationCache', 'enabled' );
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] );
    }

    protected function tearDown(): void
    {
        eZINI::instance( 'site.ini' )->setVariable( 'DesignSettings', 'DesignLocationCache', $this->designLocationCache );
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] );
    }

    public static function noSiteAccess()
    {
        return array( 'null' => array( null ), 'empty' => array( '' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider('noSiteAccess')]
    public function testNoSiteAccessIsTheCurrentOneWithoutDeprecation( $siteAccess )
    {
        $current = eZTemplateDesignResource::allDesignBases( false );
        // Asked again from the design location cache, not from the bases kept in memory by the first call
        unset( $GLOBALS['eZTemplateDesignResourceBases'], $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] );
        $deprecations = array();
        set_error_handler( function ( $errno, $errstr ) use ( &$deprecations ) {
            $deprecations[] = $errstr;
            return true;
        }, E_DEPRECATED );
        try
        {
            $bases = eZTemplateDesignResource::allDesignBases( $siteAccess );
        }
        finally
        {
            restore_error_handler();
        }
        $this->assertSame( array(), $deprecations );
        $this->assertSame( $current, $bases );
        $this->assertArrayNotHasKey( '', (array)( $GLOBALS['eZTemplateDesignResourceSiteAccessBases'] ?? array() ) );
    }
}
