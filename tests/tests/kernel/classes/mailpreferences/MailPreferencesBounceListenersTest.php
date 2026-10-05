<?php
/**
 * The listeners of the bounce reader ([BounceSettings] MessageListeners[] of mailpreferences.ini): every message the
 * reader reads is given to them after the kernel's own classification and suppression, with the dry-run flag.
 *
 *  BL-01  A listener gets every message with its classification; a hard bounce is already suppressed when it hears of it
 *  BL-02  A message a listener takes is marked handled (deleted with AfterRead=delete), the description says so
 *  BL-03  A dry run is passed on, and nothing is suppressed
 *  BL-04  A listener that throws is logged; the reading, the suppression and the other listeners go on
 *  BL-05  No listeners, an unknown class or a class without mailMessage(): nothing is handled, nothing fails
 *
 * Live style: the reader reads the fixture messages in fixtures/listeners/ (no mailbox, no mail is sent), the
 * addresses are on mplisten.invalid and their suppression entries and consent log rows are removed again. Skipped
 * where there is no installation (CI).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

class MailPreferencesBounceListenersTestListener
{
    public static $calls = array();
    public static $take = true;

    public static function mailMessage( $raw, $classification, $dryRun )
    {
        $hardAddress = 'mplisten-hard@mplisten.invalid';
        self::$calls[] = array( 'kind' => $classification['kind'], 'dry' => $dryRun,
                                'suppressed_before' => expMailSuppression::isSuppressed( $hardAddress ),
                                'subject' => preg_match( '/^Subject: (.*)$/m', $raw, $m ) ? trim( $m[1] ) : '' );
        return self::$take && $classification['kind'] === 'none';
    }
}

class MailPreferencesBounceListenersTestThrower
{
    public static function mailMessage( $raw, $classification, $dryRun )
    {
        throw new RuntimeException( 'listener failure (test)' );
    }
}

class MailPreferencesBounceListenersTestNoMethod
{
}

class MailPreferencesBounceListenersTest extends PHPUnit\Framework\TestCase
{
    const HARD = 'mplisten-hard@mplisten.invalid';
    const PERSON = 'mplisten-person@mplisten.invalid';

    private static $installation;
    private $saved = null;
    private $hadSetting = false;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expMailPreferencesService::tableExists( 'expmail_suppression' ) )
            self::markTestSkipped( 'needs the expmail_* tables (database update)' );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/mailpreferences-listeners-mail' );
        if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
        $mp = eZINI::instance( 'mailpreferences.ini' );
        $this->hadSetting = $mp->hasVariable( 'BounceSettings', 'MessageListeners' );
        $this->saved = $this->hadSetting ? $mp->variable( 'BounceSettings', 'MessageListeners' ) : null;
        MailPreferencesBounceListenersTestListener::$calls = array();
        MailPreferencesBounceListenersTestListener::$take = true;
        $this->clean();
    }

    protected function tearDown(): void
    {
        $mp = eZINI::instance( 'mailpreferences.ini' );
        if ( $this->hadSetting )
            $mp->setVariable( 'BounceSettings', 'MessageListeners', $this->saved );
        else
            $mp->removeSetting( 'BounceSettings', 'MessageListeners' );
        $this->clean();
    }

    private function clean()
    {
        $db = eZDB::instance();
        foreach ( array( self::HARD, self::PERSON ) as $email )
        {
            $db->query( "DELETE FROM expmail_suppression WHERE email_hash = '" . $db->escapeString( expMailSuppression::hash( $email ) ) . "'" );
            $db->query( "DELETE FROM expmail_consent_log WHERE email = '" . $db->escapeString( $email ) . "'" );
        }
    }

    private function listeners( array $classes )
    {
        eZINI::instance( 'mailpreferences.ini' )->setVariable( 'BounceSettings', 'MessageListeners', $classes );
    }

    private function fixtures()
    {
        return __DIR__ . '/fixtures/listeners';
    }

    /** BL-01, BL-02 */
    public function testListenersGetEveryMessageAfterTheSuppression()
    {
        $this->listeners( array( 'MailPreferencesBounceListenersTestListener' ) );
        $run = expMailBounceReader::runFiles( array( $this->fixtures() ), false );
        $this->assertTrue( $run['ok'] );
        $this->assertSame( 2, $run['counts']['messages'] );
        $this->assertSame( 1, $run['counts']['hard'] );
        $this->assertSame( 1, $run['counts']['other'] );
        $calls = MailPreferencesBounceListenersTestListener::$calls;
        $this->assertCount( 2, $calls, 'both messages reach the listener' );
        $byKind = array();
        foreach ( $calls as $call )
            $byKind[$call['kind']] = $call;
        $this->assertTrue( $byKind['hard']['suppressed_before'], 'the kernel suppressed the hard bounce before the listener heard of it' );
        $this->assertFalse( $byKind['hard']['dry'] );
        $this->assertSame( 'subscribe', $byKind['none']['subject'] );
        $this->assertSame( 'bounce', expMailSuppression::reason( self::HARD ) );
        $handled = array();
        foreach ( $run['items'] as $item )
            $handled[$item['kind']] = $item['handled'];
        $this->assertFalse( $handled['hard'], 'the listener did not take the bounce' );
        $this->assertTrue( $handled['none'], 'the listener took the other message' );
        $line = expMailBounceReader::describe( array( 'kind' => 'none', 'detail' => '', 'addresses' => array(), 'suppressed' => 0, 'handled' => true ), 'x.eml' );
        $this->assertStringContainsString( 'taken by a listener', $line );
    }

    /** BL-03 */
    public function testDryRunIsPassedOnAndChangesNothing()
    {
        $this->listeners( array( 'MailPreferencesBounceListenersTestListener' ) );
        $run = expMailBounceReader::runFiles( array( $this->fixtures() ), true );
        $this->assertTrue( $run['ok'] );
        $this->assertCount( 2, MailPreferencesBounceListenersTestListener::$calls );
        foreach ( MailPreferencesBounceListenersTestListener::$calls as $call )
            $this->assertTrue( $call['dry'], 'the listener is told it is a dry run' );
        $this->assertFalse( expMailSuppression::isSuppressed( self::HARD ), 'a dry run suppresses nothing' );
    }

    /** BL-04 */
    public function testAThrowingListenerStopsNothing()
    {
        $this->listeners( array( 'MailPreferencesBounceListenersTestThrower', 'MailPreferencesBounceListenersTestListener' ) );
        $run = expMailBounceReader::runFiles( array( $this->fixtures() ), false );
        $this->assertTrue( $run['ok'] );
        $this->assertSame( 0, $run['counts']['errors'] );
        $this->assertSame( 'bounce', expMailSuppression::reason( self::HARD ), 'the kernel still suppresses' );
        $this->assertCount( 2, MailPreferencesBounceListenersTestListener::$calls, 'the listener after the failing one still runs' );
    }

    /** BL-05 */
    public function testWithoutUsableListenersNothingIsHandled()
    {
        foreach ( array( array(), array( 'NoSuchMailPreferencesListenerClass', '' ), array( 'MailPreferencesBounceListenersTestNoMethod' ) ) as $classes )
        {
            $this->listeners( $classes );
            $run = expMailBounceReader::runFiles( array( $this->fixtures() ), true );
            $this->assertTrue( $run['ok'] );
            foreach ( $run['items'] as $item )
                $this->assertFalse( $item['handled'], implode( ',', $classes ) );
        }
        eZINI::instance( 'mailpreferences.ini' )->removeSetting( 'BounceSettings', 'MessageListeners' );
        $run = expMailBounceReader::runFiles( array( $this->fixtures() ), true );
        $this->assertTrue( $run['ok'], 'no setting at all' );
    }
}
