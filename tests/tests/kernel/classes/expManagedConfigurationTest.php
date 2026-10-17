<?php
/**
 * site.ini [ConfigurationSettings] ManagedByDeployment: with the switch enabled the views that write
 * settings, templates, code or packages answer like a disabled view, and eZINI::save() refuses in a web request.
 * No database needed.
 *
 *  MC-01 - Every entry of LOCKED_VIEWS is a view that exists in kernel/<module>/module.php
 *  MC-02 - eZModule::accessAllowed() locks the listed views with the switch, leaves them open without it
 *  MC-03 - Read-only views (settings/view, package/list, setup/cache, template list) stay open with the switch
 *  MC-04 - refusesWrite(): only in a web request, never on the command line, and only with the switch enabled
 *  MC-05 - eZINI::save() refuses in a web request before anything is written; the command line still writes
 *  MC-06 - settings/site.ini ships the switch disabled
 *  MC-07 - The module name is matched without case (/Settings/edit on a case-insensitive filesystem)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expManagedConfigurationTest extends PHPUnit\Framework\TestCase
{
    private $previous = null;

    public function setUp(): void
    {
        parent::setUp();
        $ini = eZINI::instance();
        $this->previous = $ini->hasVariable( 'ConfigurationSettings', 'ManagedByDeployment' )
            ? $ini->variable( 'ConfigurationSettings', 'ManagedByDeployment' ) : null;
    }

    public function tearDown(): void
    {
        $ini = eZINI::instance();
        if ( $this->previous === null )
            $ini->removeSetting( 'ConfigurationSettings', 'ManagedByDeployment' );
        else
            $ini->setVariable( 'ConfigurationSettings', 'ManagedByDeployment', $this->previous );
        parent::tearDown();
    }

    private function setSwitch( $value )
    {
        eZINI::instance()->setVariable( 'ConfigurationSettings', 'ManagedByDeployment', $value );
    }

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    /** @return string[] the view names of kernel/<module>/module.php */
    private static function viewsOf( $module )
    {
        $file = self::root() . '/kernel/' . $module . '/module.php';
        if ( !is_file( $file ) )
            return array();
        $read = static function ( $file )
        {
            $Module = array();
            $ViewList = array();
            $FunctionList = array();
            include $file;
            return array_keys( $ViewList );
        };
        return $read( $file );
    }

    public function testEveryLockedViewExists()
    {
        $missing = array();
        foreach ( expManagedConfiguration::LOCKED_VIEWS as $entry )
        {
            list( $module, $view ) = explode( '/', $entry, 2 );
            if ( !in_array( $view, self::viewsOf( $module ), true ) )
                $missing[] = $entry;
        }
        $this->assertSame( array(), $missing, 'LOCKED_VIEWS names views that do not exist' );
    }

    public static function lockedViews()
    {
        $cases = array();
        foreach ( expManagedConfiguration::LOCKED_VIEWS as $entry )
            $cases[$entry] = array( $entry );
        return $cases;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'lockedViews' )]
    public function testLockedViewIsDisabledWithTheSwitch( $entry )
    {
        $this->setSwitch( 'enabled' );
        $check = eZModule::accessAllowed( new eZURI( $entry . '/some/parameter' ) );
        $this->assertFalse( $check['result'], "$entry with the switch" );
        $this->assertTrue( $check['view_checked'] );

        $this->setSwitch( 'disabled' );
        $check = eZModule::accessAllowed( new eZURI( $entry ) );
        $this->assertTrue( $check['result'], "$entry without the switch" );
    }

    public function testReadOnlyViewsStayOpen()
    {
        $this->setSwitch( 'Enabled' );
        foreach ( array( 'settings/view', 'package/list', 'package/view', 'setup/cache', 'setup/info',
                         'visual/templatelist', 'visual/templateview', 'content/view/full/2' ) as $uri )
        {
            $check = eZModule::accessAllowed( new eZURI( $uri ) );
            $this->assertTrue( $check['result'], "$uri stays open" );
        }
    }

    public function testRefusesWriteOnlyInAWebRequestWithTheSwitch()
    {
        $this->setSwitch( 'enabled' );
        $this->assertTrue( expManagedConfiguration::refusesWrite( null, 'fpm-fcgi' ) );
        $this->assertTrue( expManagedConfiguration::refusesWrite( null, 'apache2handler' ) );
        $this->assertFalse( expManagedConfiguration::refusesWrite( null, 'cli' ) );
        $this->assertFalse( expManagedConfiguration::refusesWrite(), 'the test runs on the command line' );

        foreach ( array( 'disabled', '', 'true', 'yes' ) as $value )
        {
            $this->setSwitch( $value );
            $this->assertFalse( expManagedConfiguration::refusesWrite( null, 'fpm-fcgi' ), "value '$value'" );
        }
    }

    public function testModuleNameIsMatchedWithoutCase()
    {
        // on a case-insensitive filesystem /Settings/edit finds kernel/settings/module.php
        $this->setSwitch( 'enabled' );
        $this->assertTrue( expManagedConfiguration::isLockedView( 'Settings', 'edit' ) );
        $this->assertTrue( expManagedConfiguration::isLockedView( 'PACKAGE', 'install' ) );
        $this->assertFalse( eZModule::accessAllowed( new eZURI( 'Settings/edit' ) )['result'] );
        $this->assertFalse( expManagedConfiguration::isLockedView( 'settings', '' ) );
        $this->assertFalse( expManagedConfiguration::isLockedView( 'settings', false ) );
    }

    /**
     * Runs $save with refusesWrite() deciding for $sapi, in a directory of its own that is removed afterwards.
     * @return array( bool result, string[] files left in the directory )
     */
    private function saveAs( $sapi, callable $save )
    {
        $dir = sys_get_temp_dir() . '/expmc-' . getmypid() . '-' . bin2hex( random_bytes( 4 ) );
        mkdir( $dir, 0700 );
        expManagedConfiguration::$sapi = $sapi;
        try
        {
            $result = $save( $dir );
            $files = array_values( array_diff( scandir( $dir ), array( '.', '..' ) ) );
        }
        finally
        {
            expManagedConfiguration::$sapi = null;
            foreach ( glob( $dir . '/{,.}*', GLOB_BRACE ) as $file )
                if ( is_file( $file ) )
                    unlink( $file );
            rmdir( $dir );
        }
        return array( $result, $files );
    }

    public function testIniSaveRefusesInAWebRequestWithTheSwitch()
    {
        $this->setSwitch( 'enabled' );
        $save = function ( $dir ) { return eZINI::instance()->save( 'probe.ini', false, false, false, $dir ); };

        list( $result, $files ) = $this->saveAs( 'fpm-fcgi', $save );
        $this->assertFalse( $result, 'refused in a web request' );
        $this->assertSame( array(), $files, 'neither the file, a temporary file nor a backup is written' );

        list( $result, $files ) = $this->saveAs( 'cli', $save );
        $this->assertTrue( $result, 'the command line still writes' );
        $this->assertSame( array( 'probe.ini' ), $files );

        $this->setSwitch( 'disabled' );
        list( $result, $files ) = $this->saveAs( 'fpm-fcgi', $save );
        $this->assertTrue( $result, 'a web request writes without the switch' );
        $this->assertSame( array( 'probe.ini' ), $files );
    }

    public function testSiteIniShipsTheSwitchDisabled()
    {
        // read as text: the value of the running siteaccess may come from an override
        $source = file_get_contents( self::root() . '/settings/site.ini' );
        $this->assertMatchesRegularExpression( '/^\[ConfigurationSettings\]\n(?:(?!\[)[^\n]*\n)*?ManagedByDeployment=disabled$/m',
            $source, 'settings/site.ini sets [ConfigurationSettings] ManagedByDeployment=disabled' );
    }
}
