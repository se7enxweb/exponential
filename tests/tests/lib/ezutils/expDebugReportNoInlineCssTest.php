<?php
/**
 * The debug output carries no inline CSS and no versioned asset links (AGENTS.md "Admin design and template
 * styling rules"; owner rule: no ?v= on stylesheet or script links):
 *   - the Exp Debug bar (lib/ezutils/classes/expdebugbarreport.php) and the classic HTML report
 *     (eZDebug::printReportInternal() with UseDebugBar off), rendered with messages, timing points and time
 *     accumulators, and the query cache report (eZDBQueryCache::debugReport()), have no style="" attribute, no
 *     <style> element and no asset URL with a ?v= query;
 *   - the figures of the timing points and the accumulators are cells of class "num", the group rows and the
 *     message headings carry classes instead of <b>/<i>;
 *   - the rules are in the stylesheets: expdebugbar.css (the bar, its --xd-* tokens with a dark value each) and
 *     debug.css (the classic report);
 *   - the plain-text report still works and carries no markup.
 *
 * No database: the reports are rendered from a debug instance of its own, as eZDebugMessagesTest does.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugReportNoInlineCssTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugAlwaysLog', 'eZDebugLogFileEnabled' );

    private $root;
    private $savedGlobals = array();
    private $dir;
    private $debug;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
        chdir( $this->root );
        foreach ( self::GLOBAL_KEYS as $key )
            $this->savedGlobals[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $this->dir = 'var/tmp/phpunit-debug-inline-css-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 ) . '/';
        mkdir( $this->dir, 0777, true );
        unset( $GLOBALS['eZDebugAlwaysLog'], $GLOBALS['eZDebugLogFileEnabled'] );
        $this->debug = new eZDebug();
        foreach ( $this->debug->LogFiles as $level => $file )
            $this->debug->LogFiles[$level] = array( $this->dir, $file[1] );
        $GLOBALS['eZDebugGlobalInstance'] = $this->debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;

        // sample data: one message of each common level, timing points and grouped accumulators
        eZDebug::writeNotice( 'a notice', 'notice-label' );
        eZDebug::writeWarning( 'a <warning>', 'warning-label' );
        eZDebug::writeError( 'an error', 'error-label' );
        eZDebug::addTimingPoint( 'Script start' );
        eZDebug::createAccumulatorGroup( 'sample_total', 'Sample total' );
        eZDebug::accumulatorStart( 'sample_child', 'sample_total', 'Sample child' );
        usleep( 1000 );
        eZDebug::accumulatorStop( 'sample_child' );
        // a group with a time of its own (a time of 0 prints the group row without figures)
        eZDebug::accumulatorStart( 'sample_total' );
        usleep( 1000 );
        eZDebug::accumulatorStop( 'sample_total' );
        eZDebug::addTimingPoint( 'Script end' );
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

    private function render( $asHtml, $useDebugBar )
    {
        $this->debug->UseDebugBar = $useDebugBar;
        ob_start();
        try
        {
            $report = $this->debug->printReportInternal( $asHtml, true, false, true, true, true );
        }
        finally
        {
            $printed = ob_get_clean();
        }
        return (string)$report . $printed;
    }

    /**
     * Fails on any style="" attribute, any <style> element and any asset URL with a ?v= query. Script text
     * (inline <script> blocks) is not markup and is left out of the attribute check.
     */
    private function assertNoInlineCss( $html, $what )
    {
        $markup = preg_replace( '#<script\b[^>]*>.*?</script>#is', '<script></script>', $html );
        $this->assertSame( 0, preg_match_all( '/<style\b/i', $html, $m ), "$what: a <style> element" );
        $this->assertSame( 0, preg_match_all( '/\sstyle\s*=/i', $markup, $m ), "$what: a style=\"\" attribute: " . implode( ', ', $m[0] ) );
        $this->assertSame( 0, preg_match_all( '/\.style\.cssText|setAttribute\(\s*[\'"]style[\'"]|\bstyle\s*:\s*[\'"]/', $html, $m ), "$what: a script that writes a style attribute" );
        preg_match_all( '/\b(?:href|src)\s*=\s*["\']([^"\']*)["\']/i', $markup, $urls );
        foreach ( $urls[1] as $url )
            $this->assertDoesNotMatchRegularExpression( '/[?&](?:amp;)?v=/', $url, "$what: an asset URL with a version query: $url" );
    }

    public function testTheDebugBarHasNoInlineCss()
    {
        $html = $this->render( true, true );
        $this->assertStringContainsString( 'data-exp-debug-bar="2"', $html, 'the Exp Debug bar' );
        $this->assertNoInlineCss( $html, 'Exp Debug bar' );
        $this->assertMatchesRegularExpression( '#<link rel="stylesheet" type="text/css" href="[^"]*/design/standard/stylesheets/expdebugbar\.css">#', $html );
        $this->assertMatchesRegularExpression( '#<script src="[^"]*/design/standard/javascript/expdebugbar\.js" defer></script>#', $html );
        $this->assertLessThan( strpos( $html, '<div id="debug"' ), strpos( $html, 'expdebugbar.css' ), 'the stylesheet comes before the bar' );
        $this->assertFiguresAndHeadingsByClass( $html );
    }

    public function testTheClassicHtmlReportHasNoInlineCss()
    {
        $html = $this->render( true, false );
        $this->assertStringNotContainsString( 'data-exp-debug-bar', $html, 'the classic report' );
        $this->assertStringContainsString( "id='timeaccumulators'", $html );
        $this->assertNoInlineCss( $html, 'classic report' );
        $this->assertFiguresAndHeadingsByClass( $html );
    }

    public function testThePlainTextReportIsUnaffected()
    {
        $text = $this->render( false, true );
        $this->assertStringContainsString( 'a <warning>', $text );
        $this->assertStringContainsString( 'Sample child', $text );
        $this->assertStringNotContainsString( '<td', $text );
        $this->assertStringNotContainsString( 'class=', $text );
    }

    public function testTheQueryCacheReportHasNoInlineCss()
    {
        $source = file_get_contents( $this->root . '/lib/ezdb/classes/ezdbquerycache.php' );
        $this->assertSame( 1, preg_match( '/function debugReport\(.*?\n    }\n/s', $source, $m ), 'eZDBQueryCache::debugReport()' );
        $this->assertDoesNotMatchRegularExpression( '/<style\b|\sstyle\s*=|<b>|<i>|[?&]v=/i', $m[0] );
    }

    private function assertFiguresAndHeadingsByClass( $html )
    {
        $this->assertStringNotContainsString( '<b>', $html );
        $this->assertStringNotContainsString( '<i>', $html );
        $this->assertMatchesRegularExpression( "#<tr class='group'><td class='groupname'>Sample total</td><td class='num'>[0-9.,]+</td>#", $html );
        $this->assertMatchesRegularExpression( "#<tr class='data'><td>Sample child</td><td class='num'>[0-9.,]+</td><td class='num'>[0-9.,]+</td><td class='num'>1</td>#", $html );
        $this->assertMatchesRegularExpression( "#<tr class='data'><td>Script start</td>\s*<td class='num'>[0-9.,]+</td>#", $html );
        $this->assertMatchesRegularExpression( "#<td class='debugheader debuglabel'[^>]*><span>Warning:</span> warning-label</td>\s*<td class='debugheader debugtime'>#", $html );
    }

    public function testTheRulesAreInTheStylesheets()
    {
        $bar = file_get_contents( $this->root . '/design/standard/stylesheets/expdebugbar.css' );
        $classic = file_get_contents( $this->root . '/design/standard/stylesheets/debug.css' );
        foreach ( array( 'expdebugbar.css' => $bar, 'debug.css' => $classic ) as $name => $css )
        {
            $this->assertMatchesRegularExpression( '/#debug td\.num, #debug td\.debugtime \{[^}]*text-align: right;/', $css, "$name: figures right aligned" );
            $this->assertMatchesRegularExpression( '/#debug tr\.group td\.num \{[^}]*font-style: italic;/', $css, "$name: group figures" );
            $this->assertMatchesRegularExpression( '/> h2 \{[^}]*position: fixed;[^}]*background: var\(--xd-bg-bar\);/s', $css, "$name: the pinned bar from the tokens" );
        }

        // the bar's own rules take their colours from the tokens: no colour literal outside the token blocks
        $rules = preg_replace( '/\/\*.*?\*\//s', '', $bar );
        $rules = preg_replace( '/(^|\n)(?:@media \(prefers-color-scheme: dark\) \{\s*)?(?:html\[data-a4-theme="dark"\] )?#debug \{[^}]*--xd-[^}]*\}\s*\}?/', "\n", $rules );
        preg_match( '/#debug > h2 \{[^}]*\}/', $rules, $h2 );
        preg_match( '/#debug-details\.active \{[^}]*\}/', $rules, $panel );
        foreach ( array( $h2[0] ?? '', $panel[0] ?? '' ) as $rule )
        {
            $this->assertNotSame( '', $rule );
            $this->assertDoesNotMatchRegularExpression( '/#[0-9a-f]{3,6}\b|rgba?\(/i', $rule, 'pinned bar and panel use tokens only' );
        }

        // every token of the bar has a value in both dark blocks
        preg_match( '/^#debug \{(.*?)\n\}/ms', $bar, $light );
        preg_match_all( '/(--xd-[\w-]+):/', $light[1], $tokens );
        preg_match( '/@media \(prefers-color-scheme: dark\) \{\s*#debug \{(.*?)\}/s', $bar, $media );
        preg_match( '/html\[data-a4-theme="dark"\] #debug \{(.*?)\}/s', $bar, $admin4 );
        $this->assertNotEmpty( $tokens[1] );
        foreach ( $tokens[1] as $token )
        {
            $this->assertStringContainsString( $token . ':', $media[1], "$token in the prefers-color-scheme dark block" );
            $this->assertStringContainsString( $token . ':', $admin4[1], "$token in admin4's dark block" );
        }
    }
}
