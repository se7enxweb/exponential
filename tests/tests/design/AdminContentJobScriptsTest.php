<?php
/**
 * The script of the content job progress page in admin4. Checked on the files and,
 * in node, against a small stand-in for the DOM (js/mini_dom.js); no database.
 *
 * - CJS-01: the progress page (page/content_job.js) stops polling with a message of its own when the job is gone or
 *   not the user's (404, 403) and when the session has ended (a redirected request, an HTML page in place of the
 *   JSON); after a network error it waits longer each time, up to a minute; a hidden tab makes no request
 *   (js/content_job.js). The template carries the two messages.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminContentJobScriptsTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminContentJobScriptsTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    private static function read( $path )
    {
        $file = self::root() . '/' . $path;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    /** The JSON lines a harness of js/ printed, by case name */
    private function harness( $script, $file )
    {
        $node = trim( (string)shell_exec( 'command -v node 2>/dev/null' ) );
        if ( $node === '' )
            $this->markTestSkipped( 'node is not installed' );
        $out = (string)shell_exec( escapeshellarg( $node ) . ' ' . escapeshellarg( __DIR__ . "/js/$script" ) . ' '
                                   . escapeshellarg( self::root() . "/$file" ) . ' 2>&1' );
        $cases = array();
        foreach ( array_filter( explode( "\n", $out ) ) as $line )
        {
            $row = json_decode( $line, true );
            $this->assertIsArray( $row, "the harness ran: $out" );
            $cases[$row['name']] = $row;
        }
        return $cases;
    }

    /** CJS-01 */
    public function testTheProgressPageStopsWhenItCannotGoOn()
    {
        $c = $this->harness( 'content_job.js', 'design/admin4/javascript/page/content_job.js' );
        $this->assertCount( 8, $c );
        $this->assertSame( array( 2000 ), $c['running']['delays'], 'a running job is asked again in two seconds' );
        $this->assertSame( 'Working', $c['running']['message'] );
        foreach ( array( 'status 404', 'status 403' ) as $case )
        {
            $this->assertSame( 1, $c[$case]['requests'], "$case: no further request" );
            $this->assertSame( array(), $c[$case]['delays'], $case );
            $this->assertSame( 'Gone', $c[$case]['message'], $case );
            $this->assertSame( 'stopped', $c[$case]['stopped'], $case );
        }
        foreach ( array( 'redirected', 'html' ) as $case )
        {
            $this->assertSame( 1, $c[$case]['requests'], "$case: no further request" );
            $this->assertSame( array(), $c[$case]['delays'], $case );
            $this->assertSame( 'Signed out', $c[$case]['message'], $case );
        }
        $delays = array_map( function ( $d ) { return $d[0]; }, $c['network']['delays'] );
        $this->assertSame( array( 4000, 8000, 16000, 32000, 60000, 60000, 60000 ), $delays, 'longer each time, up to a minute' );
        $this->assertSame( array( 2000 ), $c['network']['after'], 'after an answer two seconds again' );
        $this->assertSame( 'Working', $c['network']['message'] );
        $this->assertSame( array( 4000 ), $c['server error']['first'], 'a server error is passing' );
        $this->assertSame( 2, $c['server error']['requests'] );
        $this->assertSame( 1, $c['hidden']['hidden'], 'no request while the tab is hidden' );
        $this->assertSame( 2, $c['hidden']['shown'], 'one when it is shown again' );
        $this->assertSame( 2, $c['hidden']['again'], 'and only one' );

        $tpl = self::read( 'design/admin4/templates/content/job.tpl' );
        foreach ( array( 'gone', 'signed-out' ) as $key )
            $this->assertMatchesRegularExpression( '/data-text-' . $key . '="\{\'[^\']+\'\|i18n\( \'design\/admin\/content\/job\' \)\|wash\}"/', $tpl, $key );
    }


}
