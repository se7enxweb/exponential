<?php
/**
 * The optional category handler method erased( expMailRecipient, expConsentContext ): expMailPreferences::erase() and
 * the account removal tell every handler after the preference system's own erasure, once per handler class, and a
 * handler that fails never stops the erasure. Live style: the installation's own database, addresses on
 * mperase.invalid, categories registered in this process only; everything the test wrote is removed again.
 *
 *  MPE-01  erase() calls erased() of every handler class once, after the preferences are gone
 *  MPE-02  a handler that throws is logged; the erasure and the other handlers go on
 *  MPE-03  a handler without erased() is skipped; notifyErased() names the classes it called
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

class MailPreferencesErasedHookTest extends PHPUnit\Framework\TestCase
{
    const DOMAIN = 'mperase.invalid';

    private static $installation;
    private $addresses = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expMailPreferencesService::tableExists( 'expmail_preference' ) || !expMailPreferencesService::tableExists( 'expmail_consent_log' ) )
            self::markTestSkipped( 'needs the expmail_* tables (database update)' );
    }

    public function setUp(): void
    {
        parent::setUp();
        mpErasedHookRecorder::$calls = array();
        $registry = expMailCategoryRegistry::instance();
        $registry->register( new expMailCategory( 'mpe_one', array( 'name' => 'MPE one', 'source' => 'extension', 'handlerClass' => 'mpErasedHookRecorder' ) ) );
        $registry->register( new expMailCategory( 'mpe_two', array( 'name' => 'MPE two', 'source' => 'extension', 'handlerClass' => 'mpErasedHookRecorder' ) ) );
        $registry->register( new expMailCategory( 'mpe_fail', array( 'name' => 'MPE fail', 'source' => 'extension', 'handlerClass' => 'mpErasedHookThrower' ) ) );
        $registry->register( new expMailCategory( 'mpe_plain', array( 'name' => 'MPE plain', 'source' => 'extension', 'handlerClass' => 'mpErasedHookWithout' ) ) );
    }

    public function tearDown(): void
    {
        $db = eZDB::instance();
        foreach ( $this->addresses as $email )
        {
            $r = expMailRecipient::fromAddress( $email, false );
            $key = $r->key();
            $anon = 'x:' . substr( hash( 'sha256', $key . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
            foreach ( array( $key, $anon ) as $k )
            {
                $k = $db->escapeString( $k );
                $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$k'" );
                $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$k'" );
                $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '$k'" );
            }
        }
        // the categories registered for this test leave the process's registry again
        $registry = expMailCategoryRegistry::instance();
        $property = new ReflectionProperty( 'expMailCategoryRegistry', 'registered' );
        $property->setAccessible( true );
        $registered = $property->getValue( $registry );
        foreach ( array( 'mpe_one', 'mpe_two', 'mpe_fail', 'mpe_plain' ) as $id )
            unset( $registered[$id] );
        $property->setValue( $registry, $registered );
        expMailCategoryRegistry::reset();
        parent::tearDown();
    }

    private function recipient( $label )
    {
        $email = 'mpe-' . getmypid() . '-' . $label . '@' . self::DOMAIN;
        $this->addresses[] = $email;
        return expMailRecipient::fromAddress( $email, false );
    }

    public function testEraseTellsEveryHandlerClassOnceAfterThePreferencesAreGone()
    {
        $r = $this->recipient( 'once' );
        $prefs = expMailPreferences::forRecipient( $r );
        $prefs->set( 'mpe_one', true, expConsentContext::system( 'MPE one on', 'page' ) );
        $prefs->set( 'mpe_two', true, expConsentContext::system( 'MPE two on', 'page' ) );
        $this->assertSame( expMailPreferences::ON, $prefs->state( 'mpe_one' ) );
        mpErasedHookRecorder::$calls = array();

        $result = $prefs->erase( expConsentContext::system( 'MPE erase' ) );

        $this->assertSame( 2, $result['preferences'], 'the kernel erased its own rows' );
        $this->assertCount( 1, mpErasedHookRecorder::$calls, 'one call for the class, although it serves two categories' );
        $call = mpErasedHookRecorder::$calls[0];
        $this->assertSame( $r->key(), $call['key'] );
        $this->assertSame( 'system', $call['source'] );
        $this->assertSame( 0, $call['stored'], 'the handler is called after the preferences are removed' );
    }

    public function testAThrowingHandlerIsLoggedAndTheErasureGoesOn()
    {
        $r = $this->recipient( 'throw' );
        $prefs = expMailPreferences::forRecipient( $r );
        $prefs->set( 'mpe_fail', true, expConsentContext::system( 'MPE fail on', 'page' ) );
        $result = $prefs->erase( expConsentContext::system( 'MPE erase' ) );
        $this->assertSame( 1, $result['preferences'] );
        $this->assertSame( expMailPreferences::OFF, expMailPreferences::forRecipient( $r )->state( 'mpe_fail' ), 'erased' );
        $this->assertCount( 1, mpErasedHookRecorder::$calls, 'the other handler was still told' );
    }

    public function testNotifyErasedNamesTheHandlersWithTheMethodAndSkipsTheOthers()
    {
        $r = $this->recipient( 'names' );
        $called = expMailPreferences::notifyErased( $r, expConsentContext::system( 'MPE direct' ) );
        $this->assertContains( 'mpErasedHookRecorder', $called );
        $this->assertContains( 'mpErasedHookThrower', $called, 'called, even though it failed' );
        $this->assertNotContains( 'mpErasedHookWithout', $called, 'no erased(): skipped' );
        $this->assertSame( count( $called ), count( array_unique( $called ) ) );
    }
}

/** Records the erased() calls. */
class mpErasedHookRecorder implements expMailCategoryHandler
{
    public static $calls = array();

    public function stateFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context ) {}

    public function erased( expMailRecipient $recipient, expConsentContext $context )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT COUNT(*) AS c FROM expmail_preference WHERE recipient_key = '" . $db->escapeString( $recipient->key() ) . "'" );
        self::$calls[] = array( 'key' => $recipient->key(), 'source' => $context->source, 'stored' => (int)$rows[0]['c'] );
    }
}

/** A handler whose erased() fails. */
class mpErasedHookThrower implements expMailCategoryHandler
{
    public function stateFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context ) {}

    public function erased( expMailRecipient $recipient, expConsentContext $context )
    {
        throw new RuntimeException( 'MPE handler failure (expected by the test)' );
    }
}

/** A handler without erased(). */
class mpErasedHookWithout implements expMailCategoryHandler
{
    public function stateFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category ) { return null; }
    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context ) {}
}
