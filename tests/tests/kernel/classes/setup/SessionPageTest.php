<?php
/**
 * The Setup > Sessions page's pure helpers (\Exponential\View\Kernel\Setup\Session): session references that do
 * not give a key away, keeping the viewer's own session out of a removal, the cleaning of ids, searches, sort keys
 * and windows, LIKE escaping, durations in words and one list row as the template gets it. No database, no session.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use Exponential\View\Kernel\Setup\Session;

class SessionPageTest extends PHPUnit\Framework\TestCase
{
    public function testAReferenceIsShortStableAndDoesNotContainTheKey()
    {
        $key = 'abcdef0123456789abcdef0123';
        $ref = Session::sessionRef( $key );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{16}$/', $ref );
        $this->assertSame( $ref, Session::sessionRef( $key ) );
        $this->assertNotSame( $ref, Session::sessionRef( $key . 'x' ) );
        $this->assertStringNotContainsString( substr( $key, 0, 8 ), $ref );
    }

    public function testTheHintShowsFourCharacters()
    {
        $this->assertSame( "abcd\u{2026}", Session::keyHint( 'abcdef0123456789' ) );
        $this->assertSame( '', Session::keyHint( '' ) );
    }

    public function testReferencesAreMappedOnlyToKeysOfTheList()
    {
        $keys = array( 'k1aaaa', 'k2bbbb', 'k3cccc' );
        $refs = array( Session::sessionRef( 'k3cccc' ), Session::sessionRef( 'someone-elses' ), 'nonsense' );
        $this->assertSame( array( 'k3cccc' ), Session::keysForRefs( $refs, $keys ) );
        $this->assertSame( array(), Session::keysForRefs( array(), $keys ) );
    }

    public function testTheOwnSessionIsLeftOutAndReported()
    {
        $this->assertSame( array( array( 'a', 'c' ), true ), Session::withoutKey( array( 'a', 'own', 'c' ), 'own' ) );
        $this->assertSame( array( array( 'a' ), false ), Session::withoutKey( array( 'a' ), 'own' ) );
        $this->assertSame( array( array( 'a' ), false ), Session::withoutKey( array( 'a' ), '' ), 'no session: nothing is left out' );
    }

    public function testIdsArePositiveWholeNumbersOnce()
    {
        $this->assertSame( array( 14, 10 ), Session::cleanIDs( array( '14', 10, '14', '-3', '0', 'x', '1.5', array( 2 ), ' 10 ' ) ) );
    }

    public function testSearchIsTrimmedBoundedAndWithoutControlCharacters()
    {
        $this->assertSame( 'ann lee', Session::cleanSearch( "  ann\t\n lee \x00" ) );
        $this->assertSame( 100, mb_strlen( Session::cleanSearch( str_repeat( 'ä', 300 ) ) ) );
        $this->assertSame( '', Session::cleanSearch( array( 'x' ) ) );
    }

    public function testLikeWildcardsAreEscapedWithTheExclamationMark()
    {
        $this->assertSame( '50!% of a!_b !!', Session::escapeLike( '50% of a_b !' ) );
    }

    public function testSortOrderAndWindowAcceptOnlyKnownValues()
    {
        $this->assertSame( 'login', Session::cleanSort( 'login', Session::SORT_COLUMNS, 'idle' ) );
        $this->assertSame( 'idle', Session::cleanSort( 'login; DROP TABLE ezsession', Session::SORT_COLUMNS, 'idle' ) );
        $this->assertSame( 'idle', Session::cleanSort( array( 'login' ), Session::SORT_COLUMNS, 'idle' ) );
        $this->assertSame( 'desc', Session::cleanOrder( 'desc' ) );
        $this->assertSame( 'asc', Session::cleanOrder( 'DESC' ) );
        $this->assertSame( 'desc', Session::cleanOrder( '', 'desc' ) );
        $this->assertSame( 'day', Session::cleanWindow( 'day' ) );
        $this->assertSame( 'session', Session::cleanWindow( 'year' ) );
    }

    public function testWindowsStartAtTheTimeouts()
    {
        $now = 1800000000;
        $this->assertSame( $now - 3600, Session::windowStart( 'hour', $now, 3600, 259200 ) );
        $this->assertSame( $now - 86400, Session::windowStart( 'day', $now, 3600, 259200 ) );
        $this->assertSame( $now - 259200, Session::windowStart( 'session', $now, 3600, 259200 ) );
        $this->assertSame( $now - 60, Session::windowStart( 'hour', $now, 0, 0 ), 'an empty timeout still means a minute' );
    }

    public function testDurationsInWords()
    {
        $this->assertSame( '45 s', Session::durationText( 45 ) );
        $this->assertSame( '12 min', Session::durationText( 12 * 60 + 5 ) );
        $this->assertSame( '1 h', Session::durationText( 3600 ) );
        $this->assertSame( '3 h 5 min', Session::durationText( 3 * 3600 + 5 * 60 ) );
        $this->assertSame( '3 d', Session::durationText( 259200 ) );
        $this->assertSame( '2 d 4 h', Session::durationText( 2 * 86400 + 4 * 3600 + 59 ) );
        $this->assertSame( '0 s', Session::durationText( -5 ) );
    }

    public function testARowKeepsTheOldFieldsAndAddsTheNewOnes()
    {
        $now = 1800000000;
        $row = Session::describeRow( array( 'user_id' => '14', 'expiration_time' => $now + 259200 - 125, 'session_key' => 'abcdef012345',
                                            'count' => '2', 'login' => 'admin', 'email' => 'a@example.invalid', 'name' => 'Admin' ), $now, 259200 );
        $this->assertSame( 14, $row['user_id'] );
        $this->assertSame( 2, $row['count'] );
        $this->assertSame( $now - 125, $row['idle_time'] );
        $this->assertSame( $now - 125, $row['last_activity'] );
        $this->assertSame( 125, $row['idle_seconds'] );
        $this->assertSame( '2 min', $row['idle_text'] );
        $this->assertSame( array( 'hour' => 0, 'minute' => '02', 'second' => '05' ), $row['idle'] );
        $this->assertSame( Session::sessionRef( 'abcdef012345' ), $row['ref'] );
        $this->assertSame( "abcd\u{2026}", $row['key_hint'] );
        $this->assertFalse( $row['expired'] );
        $this->assertSame( 'Admin', $row['name'] );

        $one = Session::describeRow( array( 'user_id' => 14, 'expiration_time' => $now - 1, 'session_key' => 'k', 'count' => 1 ), $now, 259200, false );
        $this->assertArrayNotHasKey( 'count', $one, 'rows of one user carry no count' );
        $this->assertTrue( $one['expired'] );
    }
}
