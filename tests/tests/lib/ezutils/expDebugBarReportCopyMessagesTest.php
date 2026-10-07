<?php
/**
 * The "Copy Messages" button of the Exp Debug bar's Messages tab (lib/ezutils/classes/expdebugbarreport.php and
 * design/standard/javascript/expdebugbar.js):
 *   - the rendered report carries the button (a real button, hidden until the script shows it), its status line,
 *     and the message rows the script reads (header row with level, source and time, body row with the text)
 *   - every word the script passes to t() is in expDebugBarReport::scriptStrings(), and the button's words are
 *     translated in the kernel's eng-US and ger-DE translations (context design/standard/debugbar)
 *   - the script copies with navigator.clipboard and falls back to a textarea and execCommand('copy')
 *
 * No database: the report is rendered from a debug instance of its own, as eZDebugMessagesTest does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportCopyMessagesTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );

    private $root;
    private $savedGlobals = array();
    private $dir;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        chdir( $this->root );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-debugbar-copy-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $debug = new eZDebug();
        foreach ( $debug->LogFiles as $level => $file )
            $debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
        foreach ( glob( $this->dir . '*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testReportCarriesTheButtonAndTheRowsItCopies()
    {
        eZDebug::writeError( 'Invalid objectAttribute: id =  version = 1', 'eZImageAliasHandler::storeDOMTree' );
        eZDebug::writeWarning( 'a <warning> for the copy', 'copy-label' );

        ob_start();
        $report = eZDebug::printReport( false, true, true );
        $this->assertSame( '', ob_get_clean(), 'nothing printed when the report is returned' );
        $this->assertIsString( $report );

        // the button: a real button, styled as the bar's buttons, hidden until the script shows it
        $this->assertMatchesRegularExpression(
            '#<button type="button" class="exp-debug-button exp-debug-copy-messages" hidden>Copy Messages</button>#', $report );
        $this->assertMatchesRegularExpression( '#<span class="exp-debug-copy-status" role="status" aria-live="polite"></span>#', $report );
        $this->assertSame( 1, substr_count( $report, 'exp-debug-copy-messages' ) );

        // on the Messages tab, next to the level buttons and before the messages
        $panel = strpos( $report, 'id="exp-debug-panel-messages"' );
        $levels = strpos( $report, 'class="exp-debug-levels"' );
        $button = strpos( $report, 'exp-debug-copy-messages' );
        $table = strpos( $report, "id='main-debug-table'" );
        $this->assertNotFalse( $panel );
        $this->assertTrue( $panel < $levels && $levels < $button && $button < $table, 'button between the level buttons and the messages' );
        $this->assertLessThan( strpos( $report, 'id="exp-debug-panel-settings"' ), $button, 'button on the Messages tab' );

        // the rows the script reads: header (level, source, time) and body (text), both with the level
        $this->assertMatchesRegularExpression(
            "#<tr class='error' data-level='error'><td class='debugheader'[^>]*><b><span>Error:</span> eZImageAliasHandler::storeDOMTree</b></td>"
            . "<td class='debugheader' style=\"text-align:right;\">\w{3} \d\d \d{4} \d\d:\d\d:\d\d</td></tr>"
            . "<tr class='debugbody' data-level='error'><td colspan='2'><pre>Invalid objectAttribute: id =  version = 1</pre></td></tr>#", $report );
        $this->assertStringContainsString( '<pre>a &lt;warning&gt; for the copy</pre>', $report );
    }

    public function testEveryScriptWordIsListedAndTheButtonIsTranslated()
    {
        $js = file_get_contents( $this->root . '/design/standard/javascript/expdebugbar.js' );
        preg_match_all( "/\\bt\\('((?:[^'\\\\]|\\\\.)*)'/", $js, $m );
        $this->assertNotEmpty( $m[1] );
        $listed = expDebugBarReport::scriptStrings();
        foreach ( array_unique( $m[1] ) as $source )
            $this->assertContains( stripslashes( $source ), $listed, "t('$source') in expdebugbar.js" );
        foreach ( array( 'Copied 1 message', 'Copied %count messages', 'No messages to copy.', 'Could not copy the messages.' ) as $source )
            $this->assertContains( $source, $listed );

        $words = array( 'Copy Messages', 'Copied 1 message', 'Copied %count messages', 'No messages to copy.', 'Could not copy the messages.' );
        foreach ( array( 'eng-US', 'ger-DE' ) as $locale )
        {
            $xml = simplexml_load_file( $this->root . "/share/translations/$locale/translation.ts" );
            $this->assertNotFalse( $xml, $locale );
            $context = $xml->xpath( "/TS/context[name='" . expDebugBarReport::CONTEXT . "']" );
            $this->assertCount( 1, $context, "$locale has one context " . expDebugBarReport::CONTEXT );
            foreach ( $words as $source )
            {
                $found = $context[0]->xpath( "message[source=\"$source\"]/translation" );
                $this->assertCount( 1, $found, "$locale: $source" );
                $this->assertNotSame( '', trim( (string)$found[0] ), "$locale: $source" );
            }
        }
    }

    public function testScriptCopiesShownMessagesWithAFallback()
    {
        $js = file_get_contents( $this->root . '/design/standard/javascript/expdebugbar.js' );
        $this->assertStringContainsString( "querySelector('.exp-debug-copy-messages')", $js );
        $this->assertStringContainsString( 'navigator.clipboard.writeText(text)', $js );
        $this->assertStringContainsString( "document.execCommand('copy')", $js );
        // only the shown entries: the level and the filter box hide rows with these classes
        $this->assertStringContainsString( "tr.classList.contains('exp-debug-level-hidden') || tr.classList.contains('exp-debug-filter-hidden')", $js );
        $this->assertStringContainsString( "entries.join('\\n\\n')", $js );
    }
}
