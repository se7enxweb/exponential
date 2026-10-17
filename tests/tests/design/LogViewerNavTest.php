<?php
/**
 * The buttons of the log viewer's pages (design:setup/logs_nav.tpl): one row on every page, primary buttons, the
 * current page marked.
 *
 *  LN-01  the row renders Logs, Error digest, Log health, then Manage logs (only with setup/managelogs), all
 *         .a4-btn.a4-btn-primary links, and exactly the current one carries aria-current="page" and is-current
 *  LN-02  every page of the viewer includes the row with its own current item (logs, manage, health, digest; the
 *         archive viewer is setup/logs), in both designs, and the two copies of each template are the same
 *  LN-03  the row's styles are in the module stylesheet (tokens only), not inline
 *  LN-04  every page is heading, then the row as the first thing of its content, then the intro (drawn by the row's
 *         include), so the row stays at the same place when switching between them
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class LogViewerNavTest extends PHPUnit\Framework\TestCase
{
    const NAV = 'design/admin4/templates/setup/logs_nav.tpl';
    const ACCESS = "fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managelogs' ) )";

    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        return (string)file_get_contents( self::root() . '/' . $path );
    }

    /** The row with the access check given as $stub_manage */
    private function render( $current, $manage, $intro = null )
    {
        $source = self::read( self::NAV );
        $this->assertSame( 1, substr_count( $source, self::ACCESS ), 'one access check' );
        $dir = sys_get_temp_dir() . '/lognav-' . getmypid();
        @mkdir( $dir, 0700, true );
        $file = $dir . '/nav.tpl';
        file_put_contents( $file, str_replace( self::ACCESS, '$stub_manage', $source ) );
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'log_nav_current', $current );
        $tpl->setVariable( 'stub_manage', $manage );
        if ( $intro !== null )
            $tpl->setVariable( 'log_nav_intro', $intro );
        $html = $tpl->fetch( $file );
        $tpl->unsetVariable( 'log_nav_current' );
        $tpl->unsetVariable( 'stub_manage' );
        $tpl->unsetVariable( 'log_nav_intro' );
        unlink( $file );
        rmdir( $dir );
        return $html;
    }

    /** LN-01 */
    public function testRowAndCurrentItem()
    {
        foreach ( array( 'logs', 'manage', 'health', 'digest' ) as $current )
        {
            $html = $this->render( $current, true );
            preg_match_all( '#<a class="([^"]*)" href="([^"]*)"( aria-current="page")?>#', $html, $m, PREG_SET_ORDER );
            $this->assertCount( 4, $m, $html );
            $urls = array_map( function ( $x ) { return preg_replace( '#^.*/setup/#', 'setup/', $x[2] ); }, $m );
            $this->assertSame( array( 'setup/logs', 'setup/logsdigest', 'setup/logshealth', 'setup/logsmanage' ), $urls, 'Logs, Error digest, Log health, Manage logs' );
            $marked = 0;
            foreach ( $m as $i => $a )
            {
                $this->assertStringContainsString( 'a4-btn a4-btn-primary', $a[1] );
                if ( !empty( $a[3] ) )
                {
                    $marked++;
                    $this->assertStringContainsString( 'is-current', $a[1] );
                    $this->assertSame( array( 'logs', 'digest', 'health', 'manage' )[$i], $current );
                }
            }
            $this->assertSame( 1, $marked, "one current item for $current" );
            $this->assertStringContainsString( '<nav class="a4-setup-log-nav"', $html );
        }
        $html = $this->render( 'logs', true, 'An intro & more' );
        $this->assertGreaterThan( strpos( $html, '</nav>' ), strpos( $html, '<p class="a4-intro a4-setup-log-intro">An intro &amp; more</p>' ),
                                  'the intro comes after the row, washed' );
        $this->assertStringNotContainsString( 'a4-setup-log-intro', $this->render( 'logs', true ), 'no intro, no paragraph' );
        $html = $this->render( 'health', false );
        $this->assertStringNotContainsString( 'setup/logsmanage', $html, 'Manage logs only with setup/managelogs' );
        $this->assertSame( 3, substr_count( $html, 'a4-btn-primary' ) );
    }

    /** LN-02 */
    public function testEveryPageIncludesTheRow()
    {
        $pages = array( 'logs' => 'logs', 'logs_manage' => 'manage', 'logs_manage_confirm' => 'manage', 'logs_health' => 'health',
                        'logs_digest' => 'digest' );
        foreach ( $pages as $template => $current )
        {
            $a4 = self::read( "design/admin4/templates/setup/$template.tpl" );
            $include = "{include uri='design:setup/logs_nav.tpl' log_nav_current='$current'";
            $this->assertSame( 1, substr_count( $a4, $include ), $template );
            // LN-04 the order: heading, then the row as the first thing of the content box, then the intro (only through the row)
            $title = strpos( $a4, 'class="context-title"' );
            $box = strpos( $a4, '<div class="box-bc"><div class="box-ml"><div class="box-content">' );
            $row = strpos( $a4, $include );
            $this->assertNotFalse( $title, $template );
            $this->assertTrue( $title < $box && $box < $row, "$template: heading, then the content box, then the row" );
            $open = strlen( '<div class="box-bc"><div class="box-ml"><div class="box-content">' );
            $between = substr( $a4, $box + $open, $row - $box - $open );
            $this->assertSame( '', trim( preg_replace( '/\{\*.*?\*\}/s', '', $between ) ), "$template: nothing drawn between the box and the row" );
            $this->assertStringNotContainsString( '<p class="a4-intro"', $a4, "$template: the intro is drawn by the row's include" );
            $this->assertSame( $a4, self::read( "design/admin/templates/setup/$template.tpl" ), "$template: both designs the same" );
        }
        $this->assertSame( self::read( self::NAV ), self::read( 'design/admin/templates/setup/logs_nav.tpl' ) );
        $this->assertStringNotContainsString( '<ul class="a4-setup-inline a4-setup-log-tools">', self::read( 'design/admin4/templates/setup/logs.tpl' ),
                                              'no second row of text links' );
    }

    /** LN-03 */
    public function testStyles()
    {
        $css = self::read( 'design/admin4/stylesheets/modules/setup.css' );
        $this->assertStringContainsString( '.a4-setup-log-nav-list { display: flex; flex-wrap: wrap; justify-content: flex-start;', $css );
        $this->assertStringContainsString( '.a4-setup-log-nav-btn.is-current', $css );
        $this->assertStringNotContainsString( 'style=', self::read( self::NAV ) );
    }
}
