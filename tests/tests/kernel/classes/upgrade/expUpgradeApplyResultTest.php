<?php
/**
 * The end of exp:upgrade --apply: a run whose steps all applied ends with PASS and exit 0; a run that fails afterwards
 * (the verification) ends with FAIL, its exit code and the reason on the same line. Needs no installation.
 *
 *  UR-01 a passed run: PASS
 *  UR-02 a failed verification: FAIL (exit 1) with the failed checks and their items on the last line
 *  UR-03 the reason of a verification: only the failed checks, the first items, empty when all passed or skipped
 *  UR-04 a smoke address that does not answer (a server restarting) is asked again; a real error status is not
 *  UR-05 an address that never answers still fails the check
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUpgradeApplyResultTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 5 );
        foreach ( array( 'expupgradestep', 'expupgraderegistry', 'expupgrade', 'expupgradeverifyafter' ) as $f )
            require_once $root . "/kernel/classes/upgrade/$f.php";
        require_once $root . '/kernel/private/classes/runnable/runnable.php';
        require_once $root . '/kernel/private/classes/runnable/command.php';
        require_once $root . '/kernel/private/classes/commands/upgradebase.php';
    }

    private static function report( $exit, array $errors = array(), $pending = 0 )
    {
        return array( 'exit' => $exit, 'errors' => $errors,
                      'summary' => array( 'pending' => $pending, 'done' => 1, 'not-needed' => 0, 'failed' => 0, 'unknown' => 0, 'applied' => 1 ) );
    }

    /** UR-01 */
    public function testPassedRunEndsWithPass()
    {
        $this->assertSame( 'PASS', \Exponential\Command\Kernel\UpgradeBase::resultLine( self::report( expUpgrade::EXIT_OK ) ) );
    }

    /** UR-02 */
    public function testFailedVerificationNamesTheReasonOnTheLastLine()
    {
        $verify = array( 'ok' => false, 'checks' => array(
            'steps' => array( 'ok' => false, 'detail' => '1 step(s) drifted or failed', 'items' => array( '6.0.15-review-hold drifted (recorded as done, its effect is missing)' ) ),
            'urls' => array( 'ok' => true, 'detail' => '5 address(es) answered', 'items' => array( 'https://example.com/: 200' ) ),
            'tests' => array( 'ok' => null, 'detail' => 'skipped (--no-tests)', 'items' => array() ),
        ) );
        $error = 'the verification after the upgrade failed: ' . expUpgradeVerifyAfter::failureReason( $verify );
        $line = \Exponential\Command\Kernel\UpgradeBase::resultLine( self::report( expUpgrade::EXIT_FAILED, array( $error ) ) );
        $this->assertStringStartsWith( 'FAIL (exit 1): the verification after the upgrade failed: steps: 1 step(s) drifted or failed', $line );
        $this->assertStringContainsString( '6.0.15-review-hold drifted', $line );
        $this->assertStringNotContainsString( 'urls', $line );

        $this->assertSame( 'FAIL (exit 1): 2 step(s) pending (--fail-on-pending)',
                           \Exponential\Command\Kernel\UpgradeBase::resultLine( self::report( expUpgrade::EXIT_FAILED, array(), 2 ) ) );
        $this->assertSame( 'FAIL (exit 4)', \Exponential\Command\Kernel\UpgradeBase::resultLine( array( 'errors' => array() ) ) );
    }

    /** UR-03 */
    public function testFailureReason()
    {
        $this->assertSame( '', expUpgradeVerifyAfter::failureReason( array( 'ok' => true, 'checks' => array(
            'steps' => array( 'ok' => true, 'detail' => 'no drift', 'items' => array() ),
            'tests' => array( 'ok' => null, 'detail' => 'skipped', 'items' => array() ) ) ) ) );
        $this->assertSame( 'urls: 1 of 5 address(es) did not answer as they should (a: 200, b: 200, c: 0, ...); errorlog: 1 fatal line(s)',
                           expUpgradeVerifyAfter::failureReason( array( 'ok' => false, 'checks' => array(
                               'urls' => array( 'ok' => false, 'detail' => '1 of 5 address(es) did not answer as they should', 'items' => array( 'a: 200', 'b: 200', 'c: 0', 'd: 200' ) ),
                               'errorlog' => array( 'ok' => false, 'detail' => '1 fatal line(s)', 'items' => array() ) ) ) ) );
    }

    /** UR-04 */
    public function testNoAnswerIsAskedAgain()
    {
        $asked = array();
        $answers = array( 'https://a.example/' => array( 0, 0, 200 ), 'https://b.example/' => array( 503, 200 ), 'https://c.example/' => array( 500, 200 ) );
        $status = function ( $url ) use ( &$asked, &$answers ) {
            $asked[] = $url;
            return array_shift( $answers[$url] );
        };
        $check = expUpgradeVerifyAfter::urls( array_keys( $answers ), 4, 0, $status );
        $this->assertFalse( $check['ok'], 'a 500 is a real error, not asked again' );
        $this->assertSame( array( 'https://a.example/: 200', 'https://b.example/: 200', 'https://c.example/: 500' ), $check['items'] );
        $this->assertSame( array( 3, 2, 1 ), array_values( array_map( function ( $u ) use ( $asked ) { return count( array_keys( $asked, $u, true ) ); }, array_keys( $answers ) ) ) );
    }

    /** UR-05 */
    public function testNeverAnsweringFails()
    {
        $n = 0;
        $check = expUpgradeVerifyAfter::urls( array( 'https://down.example/' ), 3, 0, function () use ( &$n ) { $n++; return 0; } );
        $this->assertFalse( $check['ok'] );
        $this->assertSame( 3, $n );
        $this->assertSame( array( 'https://down.example/: no answer' ), $check['items'] );
    }
}
