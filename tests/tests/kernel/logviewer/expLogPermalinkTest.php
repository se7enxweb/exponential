<?php
/**
 * The permalinks of the log viewer (doc/eng-us/features/6.0/log-viewer.md, "Permalinks"): setup/logs/view/<key>/(name)/value.
 *
 *  LV-70  encode() / decode(): expViewParameters::encode() (/ ( ) ~ % + ? # as ~XX, the rest readable), and the
 *         links of before (every other byte ~XX) are read the same;
 *         decode() is the inverse, a ~ without two hex digits stays
 *  LV-71  state(): every view parameter read, defaults for the missing, malformed values dropped
 *  LV-72  state() with $encoded=false: the old query string names (q, levels as array or comma list ...)
 *  LV-73  build(): the order of PARAMS, defaults and $omit left out, a plain view is setup/logs/view/<key>
 *  LV-74  round trip: build() then state() gives the same state, for many random states
 *  LV-75  searching() and criteriaInput()
 *  LV-76  (archive)/N is the name of rotation N; (rotation)/N is read as the same; build() writes (archive)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expLogPermalinkTest extends PHPUnit\Framework\TestCase
{
    /** LV-70 */
    public function testEncodeDecode()
    {
        $this->assertSame( 'timeout', expLogPermalink::encode( 'timeout' ) );
        $this->assertSame( 'error,warning', expLogPermalink::encode( 'error,warning' ) );
        $this->assertSame( 'a_b.c-d', expLogPermalink::encode( 'a_b.c-d' ) );
        $this->assertSame( 'foo%20bar', expLogPermalink::encode( 'foo bar' ) );
        $this->assertSame( '~2Fadmin~2Fcontent', expLogPermalink::encode( '/admin/content' ) );
        $this->assertSame( '~28x~29', expLogPermalink::encode( '(x)' ) );
        $this->assertSame( '~7E', expLogPermalink::encode( '~' ) );
        $this->assertSame( '~3F%26=~25~23', expLogPermalink::encode( '?&=%#' ) );
        $this->assertSame( 'M%C3%BCller', expLogPermalink::encode( 'Müller' ) );
        $this->assertSame( '2026-10-17T01:00', expLogPermalink::encode( '2026-10-17T01:00' ) );
        foreach ( array( 'Müller ünd ~ (x)/y', "a\tb", '~2F', 'ERR-0123456789', '', '日本語', '^\d+$' ) as $v )
        {
            $e = expLogPermalink::encode( $v );
            $this->assertSame( expViewParameters::encode( $v ), $e, 'the encoding of every view parameter' );
            $this->assertMatchesRegularExpression( '/^[A-Za-z0-9_.,:@=!*~%\-]*$/', $e, 'only safe characters in a path segment' );
            $this->assertSame( $v, expLogPermalink::decode( urldecode( $e ) ), $v );
        }
        $this->assertSame( 'a~zz', expLogPermalink::decode( 'a~zz' ), 'a ~ without hex digits stays' );
        $this->assertSame( 'a~', expLogPermalink::decode( 'a~' ) );
        $this->assertSame( '/', expLogPermalink::decode( '~2f' ), 'lower case hex digits are read too' );
        // the links of before (every character other than A-Z a-z 0-9 _ . , - as ~XX) are read the same
        $this->assertSame( 'foo bar', expLogPermalink::decode( 'foo~20bar' ) );
        $this->assertSame( 'Müller', expLogPermalink::decode( 'M~C3~BCller' ) );
        $this->assertSame( '?&=%#', expLogPermalink::decode( '~3F~26~3D~25~23' ) );
    }

    /** LV-71 */
    public function testStateFromViewParameters()
    {
        $s = expLogPermalink::state( array(
            'rotation' => '2', 'level' => 'error,WARNING,nope', 'search' => 'foo~20bar', 'regex' => '1', 'case' => 'yes',
            'from' => '2026-10-17T01~3A00', 'to' => '2026-10-17', 'siteaccess' => 'admin', 'uri' => '~2Fadmin~2Fsetup',
            'ip' => '192.0.2.', 'reference' => 'ERR-0123456789', 'scope' => 'kernel', 'rotations' => '1', 'cursor' => '1:2048',
            'offset' => '4096', 'follow' => '0', 'wrap' => '1', 'unmask' => '1', 'done' => 'rotate', 'ok' => '1',
            'Module' => 'ignored', 'unknown' => 'x' ) );
        $this->assertSame( 2, $s['rotation'] );
        $this->assertSame( array( 'error', 'warning' ), $s['levels'] );
        $this->assertSame( 'foo bar', $s['q'] );
        $this->assertTrue( $s['regex'] );
        $this->assertTrue( $s['case'] );
        $this->assertSame( '2026-10-17T01:00', $s['from'] );
        $this->assertSame( '2026-10-17', $s['to'] );
        $this->assertSame( 'admin', $s['siteaccess'] );
        $this->assertSame( '/admin/setup', $s['uri'] );
        $this->assertSame( '192.0.2.', $s['ip'] );
        $this->assertSame( 'ERR-0123456789', $s['reference'] );
        $this->assertSame( 'kernel', $s['scope'] );
        $this->assertTrue( $s['rotations'] );
        $this->assertSame( '1:2048', $s['cursor'] );
        $this->assertSame( 4096, $s['offset'] );
        $this->assertFalse( $s['follow'] );
        $this->assertTrue( $s['wrap'] );
        $this->assertTrue( $s['unmask'] );
        $this->assertSame( 'rotate', $s['done'] );
        $this->assertTrue( $s['ok'] );
        $this->assertSame( array_keys( expLogPermalink::defaults() ), array_keys( $s ), 'no other field' );

        $this->assertSame( expLogPermalink::defaults(), expLogPermalink::state( array() ) );
        $d = expLogPermalink::defaults();
        $this->assertTrue( $d['follow'], 'following is the default' );
        $this->assertSame( -1, $d['offset'] );

        // malformed values fall back to the default
        $bad = expLogPermalink::state( array( 'rotation' => 'x', 'offset' => '-5', 'scope' => 'everything', 'cursor' => '1;2',
                                              'done' => 'drop', 'level' => 'nope', 'follow' => 'maybe', 'search' => "a\x00b\x07c" ) );
        $this->assertSame( 0, $bad['rotation'] );
        $this->assertSame( -1, $bad['offset'] );
        $this->assertSame( 'file', $bad['scope'] );
        $this->assertSame( '', $bad['cursor'] );
        $this->assertSame( '', $bad['done'] );
        $this->assertSame( array(), $bad['levels'] );
        $this->assertFalse( $bad['follow'], 'a flag that is not 1, yes, true or on is off' );
        $this->assertSame( 'abc', $bad['q'], 'control characters are removed' );
        $this->assertSame( -1, expLogPermalink::state( array( 'rotation' => '-1' ) )['rotation'], 'a bare .gz' );
        $this->assertSame( -1, expLogPermalink::state( array( 'rotation' => '-7' ) )['rotation'] );
        $long = expLogPermalink::state( array( 'search' => str_repeat( 'a', 1000 ) ) );
        $this->assertSame( expLogPermalink::MAX_VALUE, strlen( $long['q'] ) );
        $this->assertSame( '', expLogPermalink::state( array( 'search' => array( 'nested' => array( 1 ) ) ) )['q'], 'a nested array is no value' );
    }

    /** LV-72 */
    public function testStateFromTheOldQueryString()
    {
        $s = expLogPermalink::state( array( 'q' => 'a~20b', 'levels' => array( 'error', 'debug' ), 'regex' => '1', 'rotations' => '1',
                                            'scope' => 'all', 'from' => '2026-10-17 01:00' ), false );
        $this->assertSame( 'a~20b', $s['q'], 'not decoded: a form sends plain values' );
        $this->assertSame( array( 'error', 'debug' ), $s['levels'] );
        $this->assertTrue( $s['regex'] );
        $this->assertTrue( $s['rotations'] );
        $this->assertSame( 'all', $s['scope'] );
        $this->assertSame( '2026-10-17 01:00', $s['from'] );
        $this->assertSame( array( 'warning', 'notice' ), expLogPermalink::state( array( 'levels' => 'notice,warning' ), false )['levels'],
                           'the order of expLogParser::$levels' );
        // the view parameter name wins over the field name
        $this->assertSame( 'p', expLogPermalink::state( array( 'search' => 'p', 'q' => 'f' ) )['q'] );
    }

    /** LV-73 */
    public function testBuild()
    {
        $this->assertSame( 'setup/logs/view/error', expLogPermalink::build( 'error' ) );
        $this->assertSame( 'setup/logs/view/debug', expLogPermalink::build( 'debug', expLogPermalink::defaults() ) );
        $this->assertSame( 'setup/logs/view/error/(level)/error/(search)/timeout/(follow)/0',
                           expLogPermalink::build( 'error', array( 'levels' => array( 'error' ), 'q' => 'timeout', 'follow' => false ) ) );
        $all = array( 'rotation' => 1, 'levels' => array( 'error', 'warning' ), 'q' => 'a b', 'regex' => true, 'case' => true,
                      'from' => '2026-10-17', 'to' => '2026-10-18', 'siteaccess' => 'admin', 'uri' => '/x', 'ip' => '192.0.2.1',
                      'reference' => 'ERR-0123456789', 'scope' => 'kernel', 'rotations' => true, 'cursor' => '0:10', 'offset' => 20,
                      'follow' => false, 'wrap' => true, 'unmask' => true, 'done' => 'truncate', 'ok' => true );
        $url = expLogPermalink::build( 'site-storage', $all );
        $this->assertSame( 'setup/logs/view/site-storage/(archive)/1/(level)/error,warning/(search)/a%20b/(regex)/1/(case)/1'
                           . '/(from)/2026-10-17/(to)/2026-10-18/(siteaccess)/admin/(uri)/~2Fx/(ip)/192.0.2.1/(reference)/ERR-0123456789'
                           . '/(scope)/kernel/(rotations)/1/(cursor)/0:10/(offset)/20/(follow)/0/(wrap)/1/(unmask)/1/(done)/truncate/(ok)/1', $url );
        // the names come in the order of PARAMS
        preg_match_all( '/\(([a-z]+)\)/', $url, $m );
        $this->assertSame( array_keys( expLogPermalink::PARAMS ), $m[1] );
        // omitted fields
        $this->assertSame( 'setup/logs/view/error/(search)/x', expLogPermalink::build( 'error', array( 'q' => 'x', 'done' => 'rotate', 'ok' => true, 'unmask' => true ),
                                                                                       array( 'done', 'ok', 'unmask' ) ) );
        // follow on, offset -1, scope file are defaults and left out
        $this->assertSame( 'setup/logs/view/error', expLogPermalink::build( 'error', array( 'follow' => true, 'offset' => -1, 'scope' => 'file', 'levels' => array() ) ) );
        $this->assertSame( 'setup/logs/view/error/(offset)/0', expLogPermalink::build( 'error', array( 'offset' => 0 ) ), 'offset 0 is a real entry' );
        $this->assertSame( 'setup/logs/view/webserver-access', expLogPermalink::build( 'webserver-access' ) );
    }

    /** LV-74 */
    public function testRoundTrip()
    {
        mt_srand( 551 );
        $words = array( 'timeout', 'a b', '/admin/(x)', 'Müller', '~', '^\d+$', 'ERR-0123456789', '192.0.2.', '', 'x,y' );
        for ( $n = 0; $n < 200; $n++ )
        {
            $state = expLogPermalink::defaults();
            $state['rotation'] = mt_rand( -1, 12 );
            $levels = array();
            foreach ( expLogParser::$levels as $l )
                if ( mt_rand( 0, 1 ) )
                    $levels[] = $l;
            $state['levels'] = $levels;
            foreach ( array( 'q', 'from', 'to', 'siteaccess', 'uri', 'ip', 'reference' ) as $f )
                $state[$f] = $words[mt_rand( 0, count( $words ) - 1 )];
            foreach ( array_keys( expLogPermalink::FLAGS ) as $f )
                $state[$f] = (bool)mt_rand( 0, 1 );
            $state['scope'] = array( 'file', 'kernel', 'all' )[mt_rand( 0, 2 )];
            $state['cursor'] = mt_rand( 0, 1 ) ? mt_rand( 0, 9 ) . ':' . mt_rand( 0, 99999 ) : '';
            $state['offset'] = mt_rand( 0, 1 ) ? mt_rand( 0, 1 << 30 ) : -1;
            $state['done'] = array( '', 'rotate', 'truncate', 'delete' )[mt_rand( 0, 3 )];
            $url = expLogPermalink::build( 'error', $state );
            $parts = explode( '/', substr( $url, strlen( 'setup/logs/view/error' ) ) );
            $params = array();
            for ( $i = 1; $i + 1 < count( $parts ); $i += 2 )
                $params[trim( $parts[$i], '()' )] = urldecode( $parts[$i + 1] ); // as the kernel decodes the address
            $this->assertSame( $state, expLogPermalink::state( $params ), $url );
        }
    }

    /** LV-75 */
    public function testSearchingAndCriteria()
    {
        $this->assertFalse( expLogPermalink::searching( expLogPermalink::defaults() ) );
        foreach ( array( 'q' => 'x', 'from' => '2026-10-17', 'to' => '2026-10-17', 'siteaccess' => 'a', 'uri' => '/', 'ip' => '1', 'reference' => 'ERR-0123456789' ) as $f => $v )
            $this->assertTrue( expLogPermalink::searching( array( $f => $v ) + expLogPermalink::defaults() ), $f );
        $this->assertTrue( expLogPermalink::searching( array( 'levels' => array( 'error' ) ) + expLogPermalink::defaults() ) );
        $this->assertFalse( expLogPermalink::searching( array( 'follow' => false, 'wrap' => true, 'offset' => 3 ) + expLogPermalink::defaults() ),
                            'following state is not a search' );
        $in = expLogPermalink::criteriaInput( array( 'q' => 'x', 'levels' => array( 'error' ), 'regex' => true ) + expLogPermalink::defaults() );
        $c = expLogSearch::criteria( $in );
        $this->assertSame( 'x', $c['q'] );
        $this->assertTrue( $c['regex'] );
        $this->assertSame( array( 'error' ), $c['levels'] );
        $this->assertSame( array(), $c['invalid'] );
    }

    /** LV-76 */
    public function testArchiveIsTheNameOfARotation()
    {
        $this->assertSame( 'archive', array_keys( expLogPermalink::PARAMS )[0] );
        $this->assertSame( 2, expLogPermalink::state( array( 'archive' => '2' ) )['rotation'] );
        // (rotation)/N, the name of the first permalinks, is read as the same
        $this->assertSame( 3, expLogPermalink::state( array( 'rotation' => '3' ) )['rotation'] );
        // a view's $Params hold false for a parameter not given: the other name is still read
        $this->assertSame( 4, expLogPermalink::state( array( 'archive' => false, 'rotation' => '4' ) )['rotation'] );
        $this->assertSame( 5, expLogPermalink::state( array( 'archive' => '5', 'rotation' => '4' ) )['rotation'], '(archive) wins' );
        $this->assertSame( -1, expLogPermalink::state( array( 'archive' => '-1' ) )['rotation'], 'a bare .gz' );
        $this->assertSame( 'setup/logs/view/error/(archive)/2', expLogPermalink::build( 'error', array( 'rotation' => 2 ) ) );
        $this->assertSame( 'setup/logs/view/error', expLogPermalink::build( 'error', array( 'rotation' => 0 ) ), 'the current file' );
        $this->assertStringNotContainsString( '(rotation)', expLogPermalink::build( 'error', array( 'rotation' => 7, 'q' => 'x' ) ) );
    }
}
