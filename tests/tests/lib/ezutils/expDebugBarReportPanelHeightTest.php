<?php
/**
 * The resizable panel of the Exp Debug bar, server side (lib/ezutils/classes/expdebugbarreport.php):
 *   - the preference name and limits the script, the server function and the report share
 *   - expDebugBarReport::panelHeight(): whole numbers of pixels only, 0 for the default, held between the limits
 *   - expDebugBarReport::savedPanelHeight(): the saved value read back for a logged-in user, 0 for anything unusable;
 *     for an anonymous visitor always 0, without asking the preferences (they would need a session)
 *   - the rendered report carries the height on #debug-details as data-* attributes (never in a style attribute),
 *     the inline rule sizes the panel from --exp-debug-panel-h within the room above the bar, and the server
 *     function stores the preference after the POST and form token checks
 *
 * No database: the preference reader is passed in, and the report is rendered from a debug instance of its own
 * (no eZUser loaded, so the saved height is the default).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportPanelHeightTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );

    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
    }

    public function testPreferenceNameAndLimits()
    {
        $this->assertSame( 'expdebugbar_panel_height', expDebugBarReport::PANEL_HEIGHT_PREFERENCE );
        $this->assertSame( 120, expDebugBarReport::PANEL_HEIGHT_MIN );
        $this->assertSame( 4000, expDebugBarReport::PANEL_HEIGHT_MAX );
    }

    public static function heights()
    {
        return array(
            'int'                    => array( 420, 420 ),
            'digits'                 => array( '420', 420 ),
            'digits with spaces'     => array( " 360\n", 360 ),
            'zero is the default'    => array( '0', 0 ),
            'int zero'               => array( 0, 0 ),
            'the minimum'            => array( '120', 120 ),
            'the maximum'            => array( '4000', 4000 ),
            'below the minimum'      => array( '50', 120 ),
            'one pixel'              => array( 1, 120 ),
            'above the maximum'      => array( '4001', 4000 ),
            'far above the maximum'  => array( '99999999999999999999', 4000 ),
            'empty'                  => array( '', null ),
            'blank'                  => array( '   ', null ),
            'negative'               => array( '-200', null ),
            'negative int'           => array( -200, null ),
            'fraction'               => array( '300.5', null ),
            'float'                  => array( 300.0, null ),
            'unit'                   => array( '300px', null ),
            'text'                   => array( 'tall', null ),
            'hex'                    => array( '0x1f4', null ),
            'exponent'               => array( '3e2', null ),
            'plus sign'              => array( '+300', null ),
            'false (no preference)'  => array( false, null ),
            'true'                   => array( true, null ),
            'null'                   => array( null, null ),
            'array'                  => array( array( '300' ), null ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('heights')]
    public function testPanelHeightValidatesAndClamps( $value, $expected )
    {
        $this->assertSame( $expected, expDebugBarReport::panelHeight( $value ) );
    }

    public function testSavedPanelHeightReadsThePreference()
    {
        $asked = array();
        $reader = function ( $name ) use ( &$asked ) { $asked[] = $name; return '512'; };
        $this->assertSame( 512, expDebugBarReport::savedPanelHeight( true, $reader ) );
        $this->assertSame( array( 'expdebugbar_panel_height' ), $asked );

        $this->assertSame( 120, expDebugBarReport::savedPanelHeight( true, function () { return '30'; } ) );
        $this->assertSame( 4000, expDebugBarReport::savedPanelHeight( true, function () { return '12000'; } ) );
    }

    public function testSavedPanelHeightFallsBackToTheDefault()
    {
        foreach ( array( false, null, '', 'abc', '-5', '1.5', '0' ) as $stored )
            $this->assertSame( 0, expDebugBarReport::savedPanelHeight( true, function () use ( $stored ) { return $stored; } ), var_export( $stored, true ) );
        $this->assertSame( 0, expDebugBarReport::savedPanelHeight( true, function () { throw new RuntimeException( 'no database' ); } ) );
    }

    /**
     * An anonymous visitor (the bar shown by IP): the preferences are never asked, since eZPreferences keep an
     * anonymous user's values in the session and the debug bar must not start one; the script keeps the height in
     * localStorage instead (every access in try/catch), and a logged-in user's preference is authoritative.
     */
    public function testAnonymousVisitorGetsTheDefaultWithoutAskingThePreferences()
    {
        $asked = 0;
        $reader = function () use ( &$asked ) { $asked++; return '512'; };
        foreach ( array( false, null, 0, 1, '1', 'yes' ) as $loggedIn )
            $this->assertSame( 0, expDebugBarReport::savedPanelHeight( $loggedIn, $reader ), var_export( $loggedIn, true ) );
        $this->assertSame( 0, $asked, 'the preference reader is not called for a visitor who is not logged in' );

        // the script: localStorage for "browser" (anonymous), the server function only for "preference" (logged in)
        $js = file_get_contents( $this->root . '/design/standard/javascript/expdebugbar.js' );
        $this->assertMatchesRegularExpression( "/if \\(store === 'browser'\\) \\{\\s*try \\{ if \\(h\\) window\\.localStorage\\.setItem\\('exp-debug-panel-height', String\\(h\\)\\); else window\\.localStorage\\.removeItem\\('exp-debug-panel-height'\\); \\} catch \\(e\\) \\{\\}\\s*return;\\s*\\}\\s*if \\(store !== 'preference'\\) return;\\s*call\\('panelheight'/", $js );

        // the server function refuses an anonymous visitor before the token check and before eZPreferences
        $source = file_get_contents( $this->root . '/kernel/classes/debugbar/expdebugbarserverfunctions.php' );
        $this->assertMatchesRegularExpression(
            '/function panelheight\( \$args \)\s*\{.*?!\$user->isRegistered\(\).*?self::requirePost\(\);.*?eZPreferences::setValue/s', $source );
    }

    public function testReportCarriesTheHeightAsDataAttributes()
    {
        chdir( $this->root );
        $saved = array();
        foreach ( self::GLOBAL_KEYS as $key )
            $saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $dir = 'var/tmp/phpunit-debugbar-height-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $dir, 0777, true );
        try
        {
            unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
            $debug = new eZDebug();
            foreach ( $debug->LogFiles as $level => $file )
                $debug->LogFiles[$level] = array( $dir, $file[1] );
            $GLOBALS['eZDebugGlobalInstance'] = $debug;
            $GLOBALS['eZDebugEnabled'] = true;
            $GLOBALS['eZDebugLogOnly'] = false;
            ob_start();
            $report = eZDebug::printReport( false, true, true );
            ob_end_clean();
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$key] );
                else
                    $GLOBALS[$key] = $value[0];
            }
            foreach ( glob( $dir . '*' ) as $file )
                unlink( $file );
            rmdir( $dir );
        }

        $this->assertMatchesRegularExpression(
            '#<div id="debug-details" role="region" aria-label="[^"]*" data-exp-debug-panel-height="0" data-exp-debug-panel-store="browser">#',
            $report, 'no user loaded (anonymous): the default height from the server, the browser keeps it; as data attributes' );
        $this->assertDoesNotMatchRegularExpression( '#<div id="debug-details"[^>]*\sstyle=#', $report, 'no style attribute on the panel' );
        // the inline script applies the saved height before the panel opens, the script data carries the limits
        $this->assertStringContainsString( "setProperty('--exp-debug-panel-h', Math.min(4000, Math.max(120, savedHeight)) + 'px')", $report );
        $this->assertStringContainsString( "? read('exp-debug-panel-height') : content.getAttribute('data-exp-debug-panel-height');", $report, 'localStorage only for an anonymous visitor' );
        $this->assertStringContainsString( "if (savedHeight > 0 && String(savedHeight) === String(rawHeight).trim())", $report, 'only a whole number of pixels' );
        $this->assertMatchesRegularExpression( '#"panel":\{"min":120,"max":4000\}#', $report );
    }

    public function testPanelSizeAndHandleStyles()
    {
        // the panel's rules are in the bar's stylesheet (the report prints no <style>)
        $source = file_get_contents( $this->root . '/design/standard/stylesheets/expdebugbar.css' );
        $this->assertMatchesRegularExpression(
            '/#debug-details\.active \{[^}]*\bheight: min\(var\(--exp-debug-panel-h, 60vh\), calc\(100vh - var\(--exp-debug-bar-h, 38px\)\)\);/s',
            $source, 'the saved height, never more than the room above the pinned bar' );

        $css = file_get_contents( $this->root . '/design/standard/stylesheets/expdebugbar.css' );
        $this->assertMatchesRegularExpression( '/#debug \.exp-debug-resizer \{[^}]*cursor: ns-resize;[^}]*touch-action: none;/s', $css );
        $this->assertStringContainsString( '#debug .exp-debug-resizer:focus-visible', $css );
        if ( preg_match( '/#debug \.exp-debug-resizer \{[^}]*\}/s', $css, $m ) )
            $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,8}\b/i', $m[0], 'the handle takes its colours from the bar\'s tokens' );
    }

    public function testScriptHandleIsAnAccessibleSeparator()
    {
        $js = file_get_contents( $this->root . '/design/standard/javascript/expdebugbar.js' );
        foreach ( array( "role: 'separator'", "tabindex: '0'", "'aria-orientation': 'horizontal'", "'aria-valuenow'", "'aria-valuemin'", "'aria-valuemax'",
                         "'pointerdown'", "'dblclick'", "'ArrowUp'", "'ArrowDown'", "'Home'", "'End'", 'BIG_STEP = 64', 'STEP = 16', 'KEY_SAVE_DELAY = 600',
                         "call('panelheight', { height: h }, true)", "window.addEventListener('resize'" ) as $needle )
            $this->assertStringContainsString( $needle, $js );
    }

    public function testServerFunctionStoresThePreferenceAfterTheChecks()
    {
        $source = file_get_contents( $this->root . '/kernel/classes/debugbar/expdebugbarserverfunctions.php' );
        $this->assertMatchesRegularExpression( '/public static function panelheight\( \$args \)\s*\{.*?isRegistered\(\).*?self::requireRead\(\);\s*self::requirePost\(\);/s', $source );
        $this->assertStringContainsString( 'eZPreferences::setValue( expDebugBarReport::PANEL_HEIGHT_PREFERENCE, (string)$height )', $source );
    }
}
