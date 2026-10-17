<?php
/**
 * The toolbar theme of the Exp Debug bar (lib/ezutils/classes/expdebugbarreport.php, debugbar.ini
 * [DebugBarSettings] Theme=toolbar; design/standard/stylesheets/expdebugbar-toolbar.css):
 *   - themeName(): default and toolbar without regard to case, default for anything else
 *   - the default theme draws the chips as before: a title, the text alone, no request block, no popup
 *   - the toolbar theme starts with the request block (status, module/view), draws each block's text and a popup with
 *     the title and its rows, escaped, hidden from assistive technology, and no title tooltip
 *   - requestInfo() and requestChip(): a status only from 100 to 599, amber from 400, red from 500
 *   - debugbar.ini ships Theme=default and registers the setting; the stylesheet has an icon for every key the summary
 *     draws, no external URL, and is linked only in the toolbar theme
 *   - the new words are translated in eng-US and ger-DE
 *
 * No database: the figures are passed in.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportToolbarThemeTest extends PHPUnit\Framework\TestCase
{
    private $root;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 4 );
    }

    private static function summary( array $over = array() )
    {
        return $over + array(
            'time' => 0.25, 'sql_count' => 12, 'sql_ms' => 50.0, 'memory' => 8 * 1048576, 'memory_limit' => 256 * 1048576,
            'templates' => 30, 'warnings' => 2, 'errors' => 0, 'notices' => 5, 'deprecations' => 1, 'levels' => array(),
            'querycache' => null, 'runtime' => null,
            'request' => array( 'status' => 200, 'module' => 'content', 'view' => 'view', 'siteaccess' => 'site', 'node_id' => 2 ),
        );
    }

    private static function draw( array $summary, $theme )
    {
        $method = new ReflectionMethod( 'expDebugBarReport', 'summaryHtml' );
        return $method->invoke( null, $summary, $theme );
    }

    public function testThemeName()
    {
        $this->assertSame( 'toolbar', expDebugBarReport::themeName( 'toolbar' ) );
        $this->assertSame( 'toolbar', expDebugBarReport::themeName( ' Toolbar ' ) );
        $this->assertSame( 'default', expDebugBarReport::themeName( 'default' ) );
        $this->assertSame( 'default', expDebugBarReport::themeName( 'symfony' ) );
        $this->assertSame( 'default', expDebugBarReport::themeName( '' ) );
        $this->assertSame( 'default', expDebugBarReport::themeName( array( 'toolbar' ) ) );
    }

    public function testTheDefaultThemeIsUnchanged()
    {
        $html = self::draw( self::summary(), 'default' );
        $this->assertStringNotContainsString( 'data-key="request"', $html, 'no request block' );
        $this->assertStringNotContainsString( 'exp-debug-chip-info', $html );
        $this->assertStringNotContainsString( 'exp-debug-chip-text', $html );
        $this->assertMatchesRegularExpression( '#<button type="button" class="exp-debug-chip" data-tab="timing" data-key="time" data-level="ok" title="Page time \(normal\)"[^>]*>0\.250 s</button>#', $html );
    }

    public function testTheToolbarThemeDrawsBlocksWithPopups()
    {
        $html = self::draw( self::summary(), 'toolbar' );
        $this->assertStringStartsWith( '<span class="exp-debug-summary" role="group" aria-label="Page summary"><button type="button" class="exp-debug-chip" data-tab="velocity" data-key="request" data-level="ok"', $html,
                                       'the request comes first' );
        $this->assertStringContainsString( '<span class="exp-debug-chip-text">200 content/view</span>', $html );
        $this->assertStringNotContainsString( ' title="', $html, 'the popup takes the place of the tooltip' );
        $this->assertStringContainsString( 'aria-label="Page time: 0.250 s (normal)"', $html, 'the label is kept' );
        $this->assertMatchesRegularExpression( '#data-key="sql"[^>]*><span class="exp-debug-chip-text">12 SQL / 50 ms</span>'
                                               . '<span class="exp-debug-chip-info" aria-hidden="true"><span class="exp-debug-chip-info-title">SQL queries and their time</span>'
                                               . '<span class="exp-debug-chip-info-row"><span>Queries</span><span>12</span></span>'
                                               . '<span class="exp-debug-chip-info-row"><span>Query time</span><span>50\.0 ms</span></span>'
                                               . '<span class="exp-debug-chip-info-row"><span>Share of page time</span><span>20 %</span></span></span></button>#', $html );
        $this->assertStringContainsString( '<span>Memory limit</span><span>256 MB</span>', $html );
        $this->assertStringContainsString( '<span>Notices</span><span>5</span>', $html );
        $this->assertStringContainsString( '<span>Node ID</span><span>2</span>', $html );
        // the counts the minimised tab reads (expdebugbar.js, count()) stay at the start of the text
        $this->assertStringContainsString( 'data-key="warnings" data-level="warn"', $html );
        $this->assertStringContainsString( '<span class="exp-debug-chip-text">2 warn</span>', $html );
    }

    public function testThePopupIsEscaped()
    {
        $html = self::draw( self::summary( array( 'request' => array( 'status' => 404, 'module' => '<b>x</b>', 'view' => 'v"w',
                                                                         'siteaccess' => '', 'node_id' => 0 ) ) ), 'toolbar' );
        $this->assertStringNotContainsString( '<b>x</b>', $html );
        $this->assertStringContainsString( '404 &lt;b&gt;x&lt;/b&gt;/v&quot;w', $html );
        $this->assertStringNotContainsString( '<span>Node ID</span>', $html, 'no node, no row' );
    }

    public function testRequestInfoAndChip()
    {
        $info = expDebugBarReport::requestInfo( 503, array( 'module_name' => 'content', 'function_name' => 'edit' ), array( 'siteaccess' => 'admin', 'node_id' => 5 ) );
        $this->assertSame( array( 'status' => 503, 'module' => 'content', 'view' => 'edit', 'siteaccess' => 'admin', 'node_id' => 5 ), $info );
        $chip = expDebugBarReport::requestChip( $info );
        $this->assertSame( array( 'velocity', 'request', 'high', '503 content/edit' ), array_slice( $chip, 0, 4 ) );

        $this->assertSame( 'warn', expDebugBarReport::requestChip( expDebugBarReport::requestInfo( 403, array() ) )[2] );
        $this->assertSame( 'ok', expDebugBarReport::requestChip( expDebugBarReport::requestInfo( 302, array() ) )[2] );

        $unknown = expDebugBarReport::requestInfo( false, false );
        $this->assertNull( $unknown['status'], 'false (the command line, a persistent worker) is no status' );
        $this->assertSame( '', $unknown['module'] );
        $this->assertNull( expDebugBarReport::requestInfo( 42, null )['status'] );
        $chip = expDebugBarReport::requestChip( $unknown );
        $this->assertSame( array( 'ok', '-' ), array( $chip[2], $chip[3] ), 'nothing known: a dash' );
    }

    public function testTheIniShipsTheDefaultAndTheSetting()
    {
        $ini = file_get_contents( $this->root . '/settings/debugbar.ini' );
        $this->assertMatchesRegularExpression( '/^\[DebugBarSettings\].*?^Theme=default$/ms', $ini );
        $this->assertStringContainsString( "Settings[]=debug_bar_theme\n", $ini );
        $this->assertMatchesRegularExpression( '/^\[Setting_debug_bar_theme\]\nFile=debugbar\.ini\nBlock=DebugBarSettings\nVariable=Theme\nType=enum\nValues\[\]\nValues\[\]=default\nValues\[\]=toolbar$/m', $ini );
    }

    public function testTheStylesheetHasAnIconForEveryKey()
    {
        $css = file_get_contents( $this->root . '/design/standard/stylesheets/expdebugbar-toolbar.css' );
        foreach ( array( 'request', 'time', 'sql', 'querycache', 'memory', 'templates', 'viewcache', 'cacheblocks', 'compiled',
                         'warnings', 'errors', 'deprecations', 'opcache', 'apcu', 'versions' ) as $key )
            $this->assertStringContainsString( '.exp-debug-chip[data-key="' . $key . '"] { --xd-icon: url("data:image/svg+xml,', $css, $key );
        $this->assertDoesNotMatchRegularExpression( '#url\(\s*(?!["\']?data:)#', $css, 'no file and no host is loaded' );
        $this->assertStringNotContainsString( '!important', $css );
        $this->assertStringContainsString( '#debug[data-exp-debug-theme="toolbar"] .exp-debug-chip.exp-debug-end { margin-left: auto; }', $css,
                                           'the chip rule sets margin: 0, the right end is set again' );
        $this->assertDoesNotMatchRegularExpression( '#\]\s+:focus-visible#', $css, 'no focus rule reaches into the panel' );

        $source = file_get_contents( $this->root . '/lib/ezutils/classes/expdebugbarreport.php' );
        // the default theme's markup and data stay as they were: no theme attribute, no request in the page's JSON
        $this->assertStringContainsString( "' data-exp-debug-theme=\"toolbar\"' : ''", $source );
        $this->assertMatchesRegularExpression( "#if \( \\\$theme === 'toolbar' \)\s+\\\$summary\['request'\] = self::requestInfo\(#", $source );
        $this->assertStringContainsString( "if (bar.parentNode.getAttribute('data-exp-debug-theme') === 'toolbar') keepLabel.title", $source );
        $this->assertMatchesRegularExpression( "#if \( \\\$theme === 'toolbar' \)\s+echo self::includeTag\( 'css', 'design/standard/stylesheets/expdebugbar-toolbar\.css' \);#", $source );
    }

    public function testTheNewWordsAreTranslated()
    {
        $words = array( 'Deprecations', 'Module', 'Node ID', 'HTTP status', 'Query time', 'Request', 'Share of page time',
                        'Page time', 'Queries', 'Peak memory', 'Memory limit', 'none', 'Templates used', 'Errors', 'Warnings',
                        'Notices', 'View', 'Siteaccess' );
        foreach ( array( 'eng-US', 'ger-DE' ) as $locale )
        {
            $xml = simplexml_load_file( $this->root . "/share/translations/$locale/translation.ts" );
            $context = $xml->xpath( "/TS/context[name='" . expDebugBarReport::CONTEXT . "']" );
            $this->assertCount( 1, $context, $locale );
            foreach ( $words as $source )
            {
                $found = $context[0]->xpath( "message[source=\"$source\"]/translation" );
                $this->assertCount( 1, $found, "$locale: $source" );
                $this->assertNotSame( '', trim( (string)$found[0] ), "$locale: $source" );
            }
        }
    }
}
