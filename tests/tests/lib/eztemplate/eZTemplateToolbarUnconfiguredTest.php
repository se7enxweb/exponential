<?php
/**
 * {tool_bar name=<name>} for a toolbar the siteaccess does not configure (no [Toolbar_<name>] in toolbar.ini) draws
 * no tools and logs nothing. The admin pagelayout names admin_right and admin_developer; a siteaccess that only falls
 * back to the admin design (a Platform Legacy project's front siteaccess rendering a legacy 404) has neither group,
 * and each page logged "Undefined group: 'Toolbar_admin_right' in toolbar.ini" and the same for admin_developer.
 *
 * Interpreted and compiled. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/lib/eztemplate/eZTemplateToolbarUnconfiguredTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group eztemplate
 */

class eZTemplateToolbarUnconfiguredTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugLogFileEnabled' );
    private $dir;
    private $saved = array();
    private $compile;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/toolbar_unconfigured_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $debug = new eZDebug();
        $debug->ShowTypes = eZDebug::SHOW_ALL;
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::setLogFileEnabled( false );
        $this->compile = eZINI::instance()->variable( 'TemplateSettings', 'TemplateCompile' );
        $this->assertFalse( eZINI::instance( 'toolbar.ini' )->hasGroup( 'Toolbar_w630_not_configured' ) );
    }

    protected function tearDown(): void
    {
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplateCompile', $this->compile );
        foreach ( $this->saved as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        eZTemplate::resetInstance();
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testInterpreted()
    {
        $this->assertDrawsNothingAndLogsNothing( 'disabled' );
    }

    public function testCompiled()
    {
        $this->assertDrawsNothingAndLogsNothing( 'enabled' );
    }

    private function assertDrawsNothingAndLogsNothing( $compile )
    {
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplateCompile', $compile );
        $file = $this->dir . '/toolbar_' . $compile . '.tpl';
        file_put_contents( $file, "[{tool_bar name='w630_not_configured' view='full'}]" );
        eZTemplate::resetInstance();
        $this->assertSame( '[]', trim( eZTemplate::factory()->fetch( $file ) ) );

        $errors = array();
        foreach ( eZDebug::instance()->DebugStrings as $entry )
            if ( $entry['Level'] === eZDebug::LEVEL_ERROR )
                $errors[] = $entry['String'];
        $this->assertSame( array(), $errors, 'no "Undefined group" error' );
    }
}
