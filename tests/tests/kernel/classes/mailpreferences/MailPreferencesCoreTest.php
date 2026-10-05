<?php
/**
 * The e-mail preference system's core (kernel/classes/mailpreferences): categories, preferences, the consent log,
 * suppression, tokens and the mail gate in eZMailTransport::send(). Live style: the tests run on the installation they
 * find (no test database), with addresses on mptest.invalid and one test user (remote id mptest-user) that are removed
 * again; where there is no installation (CI) they are skipped.
 *
 * NO MAIL LEAVES THE SERVER. The mail transport is forced to "file" in the process's own ini (never written to a
 * settings file) and the mail goes to var/tmp/mailpreferences-mail/<run>/; setUpBeforeClass() and setUp() refuse to
 * run when the transport is not the file transport. Every address is on the reserved .invalid domain.
 *
 *  MP-01  The mail transport is the file transport
 *  MP-02  Categories: the INI list, essential and optional, opt-in defaults, frequencies, double opt-in; admin rows
 *  MP-03  Opt-in: a new person has every optional category off and every essential one on
 *  MP-04  set() on and off, and the consent log row of each (source, wording, IP, actor, siteaccess, old/new)
 *  MP-05  The master switch keeps the categories dormant and gives them back
 *  MP-06  Double opt-in: pending, the confirmation mail, no mail while pending, confirm() switches on
 *  MP-07  An expired confirmation: confirm() says expired; cleanup() turns the pending category off
 *  MP-08  Tokens: round trip, purpose, tampering, expiry, malformed, no readable address
 *  MP-09  Suppression: only a salted hash is stored; check, add, lift; a suppressed address gets no optional mail
 *  MP-10  The gate: mail without a category is sent unchanged and logged; an essential sender is logged as essential
 *  MP-11  The gate: optional mail only to who switched it on, one mail each, footer, List-Unsubscribe and -Post
 *  MP-12  The gate: essential mail always goes, without footer; all blocked: nothing written, true answered
 *  MP-13  The footer: organisation name and postal address; in an HTML mail before </body>
 *  MP-14  Unsubscribe by link: a category link switches the category off, a link without category the master switch
 *  MP-15  Export (JSON array, CSV) and erase (preferences gone, consent log anonymised, the withdrawal kept)
 *  MP-16  The consent log CSV: filters and formula cells defused
 *  MP-17  Retention: old rows of people who are gone are removed, rows of people who are there are kept
 *  MP-18  Send me a link: the mail with the manage link, the rate limit
 *  MP-19  A user: fromUser(), the user's address resolves to the account, the e-mail address change double opt-in
 *  MP-20  The commands: --help of each, status --json, gate test, preferences show (address hidden)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

class MailPreferencesCoreTest extends PHPUnit\Framework\TestCase
{
    const DOMAIN = 'mptest.invalid';

    private static $installation;
    private static $mailDir;
    private static $logFile;
    private static $user;
    private static $userObjectID = 0;
    private static $iniBackup = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expMailPreferencesService::tableExists( 'expmail_preference' ) )
            self::markTestSkipped( 'needs the expmail_* tables (database update)' );
        self::$mailDir = 'var/tmp/mailpreferences-mail/mptest-' . getmypid() . '-' . time();
        self::$logFile = self::$installation . '/var/tmp/mailpreferences-mail/mptest-' . getmypid() . '-gate.jsonl';
        self::forceFileTransport();
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        self::clean();
        self::removeUser();
        self::makeUser();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$installation === null )
            return;
        chdir( self::$installation );
        self::forceFileTransport();
        self::clean();
        self::removeUser();
        expMailGate::setLogFileForTest( null );
        foreach ( array_merge( glob( self::$mailDir . '/*' ) ?: array(), array( self::$logFile ) ) as $file )
            @unlink( $file );
        @rmdir( self::$mailDir );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        self::forceFileTransport();
        expMailGate::setLogFileForTest( self::$logFile );
        expMailCategoryRegistry::reset();
        self::clean();
        foreach ( glob( self::$mailDir . '/*.mail' ) ?: array() as $f )
            @unlink( $f );
        @unlink( self::$logFile );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        foreach ( self::$iniBackup as $key => $value )
        {
            list( $block, $var ) = explode( '/', $key );
            $ini->setVariable( $block, $var, $value );
        }
        self::$iniBackup = array();
        expMailCategoryRegistry::reset();
    }

    // ------------------------------------------------------------------ the guard and helpers

    private static function forceFileTransport()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'MailSettings', 'Transport', 'file' );
        $ini->setVariable( 'MailSettings', 'FileTransportDirectory', self::$mailDir );
        $ini->setVariable( 'MailSettings', 'DebugSending', 'disabled' );
        if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
            throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
    }

    private function setIni( $block, $var, $value )
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $key = "$block/$var";
        if ( !array_key_exists( $key, self::$iniBackup ) )
            self::$iniBackup[$key] = $ini->hasVariable( $block, $var ) ? $ini->variable( $block, $var ) : '';
        $ini->setVariable( $block, $var, $value );
    }

    private static function address( $key )
    {
        return 'mptest-' . $key . '@' . self::DOMAIN;
    }

    private static function addresses()
    {
        $out = array();
        foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'user', 'user-new', 'csv' ) as $k )
            $out[] = self::address( $k );
        return $out;
    }

    /** removes every row the test can have made */
    private static function clean()
    {
        $db = eZDB::instance();
        $keys = array();
        foreach ( self::addresses() as $email )
            $keys[] = 'a:' . expMailSuppression::hash( $email );
        if ( self::$userObjectID )
            $keys[] = 'u:' . self::$userObjectID;
        $anon = array();
        foreach ( $keys as $k )
            $anon[] = 'x:' . substr( hash( 'sha256', $k . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $in = function ( array $values ) use ( $db ) {
            return "'" . implode( "','", array_map( array( $db, 'escapeString' ), $values ) ) . "'";
        };
        $db->query( 'DELETE FROM expmail_preference WHERE recipient_key IN (' . $in( $keys ) . ')' );
        $db->query( 'DELETE FROM expmail_pending WHERE recipient_key IN (' . $in( $keys ) . ')' );
        $db->query( 'DELETE FROM expmail_consent_log WHERE recipient_key IN (' . $in( array_merge( $keys, $anon ) ) . ") OR email LIKE '%@" . self::DOMAIN . "'" );
        $hashes = array_map( function ( $k ) { return substr( $k, 2 ); }, array_slice( $keys, 0, count( self::addresses() ) ) );
        $db->query( 'DELETE FROM expmail_suppression WHERE email_hash IN (' . $in( $hashes ) . ')' );
        $db->query( "DELETE FROM expmail_category WHERE identifier LIKE 'mptest%'" );
    }

    private static function makeUser()
    {
        $login = 'mptest-user';
        $password = bin2hex( random_bytes( 16 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . self::address( 'user' ) . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        $admin = eZUser::fetchByName( 'admin' );
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => $admin->attribute( 'contentobject_id' ),
            'remote_id' => 'mptest-user', 'attributes' => array( 'first_name' => 'MPTEST', 'last_name' => 'User', 'user_account' => $account ) ) );
        if ( !$object )
            throw new RuntimeException( 'MPTEST: the test user could not be created' );
        self::$userObjectID = (int)$object->attribute( 'id' );
        eZUser::cleanupCache();
        self::$user = eZUser::fetch( self::$userObjectID );
    }

    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( 'mptest-user' );
        if ( $object )
        {
            $id = (int)$object->attribute( 'id' );
            $db = eZDB::instance();
            $db->query( "DELETE FROM expmail_preference WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_pending WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_consent_log WHERE user_id = $id OR recipient_key = 'u:$id'" );
            $object->purge();
        }
        self::$user = null;
    }

    private function recipient( $key )
    {
        $r = expMailRecipient::fromAddress( self::address( $key ) );
        $this->assertNotNull( $r );
        return $r;
    }

    private function context( $source = 'page', $wording = 'MPTEST wording' )
    {
        $c = new expConsentContext( $source, $wording, '192.0.2.10', 0, 'mptest' );
        return $c;
    }

    /** @return string[] file contents of the mail written so far */
    private function mails()
    {
        $out = array();
        $files = glob( self::$mailDir . '/*.mail' ) ?: array();
        sort( $files );
        foreach ( $files as $f )
            $out[] = (string)file_get_contents( $f );
        return $out;
    }

    private function mailsTo( $email )
    {
        return array_values( array_filter( $this->mails(), function ( $m ) use ( $email ) { return strpos( $m, $email ) !== false; } ) );
    }

    private function newMail( array $to, $category, $html = false )
    {
        $mail = new eZMail();
        $mail->setSender( 'mptest-sender@' . self::DOMAIN );
        foreach ( $to as $t )
            $mail->addBcc( $t );
        $mail->setSubject( 'MPTEST mail' );
        if ( $html )
        {
            $mail->setContentType( 'text/html' );
            $mail->setBody( '<html><body><p>MPTEST body</p></body></html>' );
        }
        else
            $mail->setBody( "MPTEST body\n" );
        if ( $category !== null )
            $mail->setCategory( $category );
        return $mail;
    }

    private function logLines()
    {
        if ( !is_file( self::$logFile ) )
            return array();
        return array_map( function ( $l ) { return json_decode( $l, true ); }, file( self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );
    }

    private function headerOf( $mail, $name )
    {
        return preg_match( '/^' . preg_quote( $name, '/' ) . ':\s*(.*)$/mi', $mail, $m ) ? trim( $m[1] ) : null;
    }

    // ------------------------------------------------------------------ MP-01

    public function testTheTransportIsTheFileTransport()
    {
        $this->assertSame( 'file', trim( eZINI::instance()->variable( 'MailSettings', 'Transport' ) ) );
        $this->assertSame( self::$mailDir, eZINI::instance()->variable( 'MailSettings', 'FileTransportDirectory' ) );
        $mail = $this->newMail( array( self::address( 'a' ) ), null );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $this->assertCount( 1, $this->mails() );
    }

    // ------------------------------------------------------------------ MP-02

    public function testCategoriesFromTheSettings()
    {
        $r = expMailCategoryRegistry::instance();
        foreach ( array( 'content', 'collaboration', 'newsletter', 'marketing', 'system' ) as $id )
        {
            $c = $r->get( $id );
            $this->assertNotNull( $c, $id );
            $this->assertFalse( $c->essential, $id );
            $this->assertFalse( $c->defaultOn, "$id is off by default (opt-in)" );
        }
        foreach ( array( 'security', 'orders', 'legal', 'admin' ) as $id )
            $this->assertTrue( $r->get( $id )->essential, $id );
        $this->assertTrue( $r->get( 'newsletter' )->doubleOptIn );
        $this->assertTrue( $r->get( 'marketing' )->doubleOptIn );
        $this->assertFalse( $r->get( 'content' )->doubleOptIn );
        $this->assertSame( array( 'immediate', 'daily', 'weekly' ), $r->get( 'content' )->frequencies );
        $this->assertSame( array(), $r->get( 'newsletter' )->frequencies );
        $this->assertSame( 'ini', $r->get( 'content' )->source );
        $this->assertArrayNotHasKey( 'security', $r->optional() );
        $this->assertArrayHasKey( 'security', $r->essential() );
    }

    public function testAdminCategoriesAndOverrides()
    {
        $r = expMailCategoryRegistry::instance();
        $this->assertTrue( $r->saveAdmin( new expMailCategory( 'mptestevents', array( 'name' => 'MPTEST events', 'description' => 'd', 'defaultOn' => true ) ) ) );
        $c = $r->get( 'mptestevents' );
        $this->assertSame( 'admin', $c->source );
        // stored as asked, but opt-out is refused unless [CategorySettings] AllowDefaultOn=enabled (MPH-04)
        $this->setIni( 'CategorySettings', 'AllowDefaultOn', 'disabled' );
        expMailCategoryRegistry::reset();
        $this->assertFalse( $r->get( 'mptestevents' )->defaultOn, 'DefaultOn=true is refused' );
        $this->assertArrayHasKey( 'mptestevents', $r->refusedDefaultOn() );
        $this->setIni( 'CategorySettings', 'AllowDefaultOn', 'enabled' );
        expMailCategoryRegistry::reset();
        $this->assertTrue( $r->get( 'mptestevents' )->defaultOn, 'kept where the site allows opt-out' );
        $this->assertArrayNotHasKey( 'mptestevents', $r->refusedDefaultOn() );
        $this->setIni( 'CategorySettings', 'AllowDefaultOn', 'disabled' );
        expMailCategoryRegistry::reset();
        // an INI category's essential flag cannot be changed in the admin
        $this->assertTrue( $r->saveAdmin( new expMailCategory( 'mptestevents', array( 'name' => 'MPTEST events renamed', 'essential' => true ) ) ) );
        $this->assertSame( 'MPTEST events renamed', $r->get( 'mptestevents' )->name );
        $this->assertTrue( $r->removeAdmin( 'mptestevents' ) );
        $this->assertNull( $r->get( 'mptestevents' ) );
        $this->assertFalse( $r->saveAdmin( new expMailCategory( '_master' ) ) );
    }

    // ------------------------------------------------------------------ MP-03, MP-04

    public function testOptInDefaults()
    {
        $p = expMailPreferences::forRecipient( $this->recipient( 'a' ) );
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
        {
            $this->assertFalse( $p->isOn( $id ), $id );
            $this->assertFalse( $p->allows( $id ), $id );
        }
        foreach ( expMailCategoryRegistry::instance()->essential() as $id => $c )
            $this->assertTrue( $p->allows( $id ), $id );
        $this->assertTrue( $p->masterOn() );
        $this->assertSame( 'unknown_category', $p->decision( 'mptestnothing' ) );
    }

    public function testSetAndTheConsentLog()
    {
        $recipient = $this->recipient( 'a' );
        $p = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( 'on', $p->set( 'content', true, $this->context( 'page', 'MPTEST: Content notifications [x]' ) ) );
        $this->assertTrue( $p->allows( 'content' ) );
        $this->assertSame( 'on', $p->set( 'content', true, $this->context() ), 'a second on changes nothing' );
        $this->assertSame( 'off', $p->set( 'content', false, $this->context( 'link', 'MPTEST unsubscribe' ) ) );
        $this->assertFalse( $p->allows( 'content' ) );
        $rows = expConsentLog::fetchForRecipient( $recipient );
        $this->assertCount( 2, $rows );
        $off = $rows[0];
        $on = $rows[1];
        $this->assertSame( 'on', $on->attribute( 'action' ) );
        $this->assertSame( 'off', $on->attribute( 'old_value' ) );
        $this->assertSame( 'on', $on->attribute( 'new_value' ) );
        $this->assertSame( 'page', $on->attribute( 'source' ) );
        $this->assertSame( 'MPTEST: Content notifications [x]', $on->attribute( 'wording' ) );
        $this->assertSame( '192.0.2.10', $on->attribute( 'ip' ) );
        $this->assertSame( 'mptest', $on->attribute( 'siteaccess' ) );
        $this->assertSame( self::address( 'a' ), $on->attribute( 'email' ) );
        $this->assertSame( 'content', $on->attribute( 'category' ) );
        $this->assertGreaterThan( time() - 60, (int)$on->attribute( 'created' ) );
        $this->assertSame( 'off', $off->attribute( 'action' ) );
        $this->assertSame( 'link', $off->attribute( 'source' ) );
        $this->assertSame( 'on', $off->attribute( 'old_value' ) );
        // frequency
        $this->assertSame( 'immediate', $p->frequency( 'content' ) );
        $this->assertTrue( $p->setFrequency( 'content', 'weekly', $this->context() ) );
        $this->assertSame( 'weekly', expMailPreferences::forRecipient( $recipient )->frequency( 'content' ) );
        try
        {
            $p->setFrequency( 'content', 'hourly', $this->context() );
            $this->fail( 'an unknown frequency is refused' );
        }
        catch ( InvalidArgumentException $e )
        {
        }
        try
        {
            $p->set( 'security', false, $this->context() );
            $this->fail( 'an essential category cannot be switched' );
        }
        catch ( InvalidArgumentException $e )
        {
        }
    }

    // ------------------------------------------------------------------ MP-05

    public function testMasterSwitchKeepsCategoriesDormant()
    {
        $p = expMailPreferences::forRecipient( $this->recipient( 'b' ) );
        $p->set( 'content', true, $this->context() );
        $p->set( 'collaboration', true, $this->context() );
        $this->assertTrue( $p->setMaster( false, $this->context( 'page', 'MPTEST all off' ) ) );
        $this->assertFalse( $p->masterOn() );
        $this->assertTrue( $p->isOn( 'content' ), 'the category stays on, dormant' );
        $this->assertFalse( $p->allows( 'content' ) );
        $this->assertSame( 'master_off', $p->decision( 'collaboration' ) );
        $this->assertTrue( $p->allows( 'security' ), 'essential mail still goes' );
        $fresh = expMailPreferences::forRecipient( $this->recipient( 'b' ) );
        $this->assertTrue( $fresh->setMaster( true, $this->context() ) );
        $this->assertTrue( $fresh->allows( 'content' ) );
        $this->assertTrue( $fresh->allows( 'collaboration' ) );
        $actions = array_map( function ( $r ) { return $r->attribute( 'action' ); }, expConsentLog::fetchForRecipient( $this->recipient( 'b' ) ) );
        $this->assertContains( 'master_off', $actions );
        $this->assertContains( 'master_on', $actions );
    }

    // ------------------------------------------------------------------ MP-06, MP-07

    public function testDoubleOptIn()
    {
        $recipient = $this->recipient( 'c' );
        $p = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( 'pending_confirmation', $p->set( 'newsletter', true, $this->context( 'page', 'MPTEST newsletter [x]' ) ) );
        $this->assertTrue( $p->isPending( 'newsletter' ) );
        $this->assertFalse( $p->allows( 'newsletter' ) );
        $this->assertSame( 'pending', $p->decision( 'newsletter' ) );
        $mails = $this->mailsTo( self::address( 'c' ) );
        $this->assertCount( 1, $mails, 'one confirmation mail' );
        $this->assertSame( 'security', $this->headerOf( $mails[0], 'X-Exp-Mail-Category' ), 'the confirmation is essential mail' );
        $this->assertNull( $this->headerOf( $mails[0], 'List-Unsubscribe' ) );
        $this->assertSame( 1, preg_match( '#/mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ), $mails[0] );
        // a newsletter mail does not go while pending
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'c' ) ), 'newsletter' ) ) );
        $this->assertCount( 1, $this->mailsTo( self::address( 'c' ) ) );
        // confirming
        $result = expMailPreferencesService::confirm( $m[1], $this->context( 'confirm', 'MPTEST confirm button' ) );
        $this->assertSame( 'confirmed', $result['result'] );
        $this->assertSame( 'category', $result['kind'] );
        $this->assertTrue( expMailPreferences::forRecipient( $recipient )->allows( 'newsletter' ) );
        $this->assertSame( 'already', expMailPreferencesService::confirm( $m[1], $this->context( 'confirm' ) )['result'] );
        $actions = array_map( function ( $r ) { return $r->attribute( 'action' ) . ':' . $r->attribute( 'source' ); }, expConsentLog::fetchForRecipient( $recipient ) );
        $this->assertSame( array( 'confirm:confirm', 'pending:page' ), $actions );
        // a context of another source is recorded as confirm
        $this->assertSame( 'invalid', expMailPreferencesService::confirm( 'm1nonsense' . str_repeat( 'x', 60 ) )['result'] );
    }

    public function testExpiredConfirmation()
    {
        $recipient = $this->recipient( 'd' );
        $p = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( 'pending_confirmation', $p->set( 'marketing', true, $this->context() ) );
        $pending = expMailPendingRow::fetchForKey( $recipient->key(), 'category', 'marketing' );
        $this->assertCount( 1, $pending );
        $pending[0]->setAttribute( 'expires', time() - 10 );
        $pending[0]->store();
        $mails = $this->mailsTo( self::address( 'd' ) );
        $this->assertSame( 1, preg_match( '#/mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        $this->assertSame( 'expired', expMailPreferencesService::confirm( $m[1], $this->context( 'confirm' ) )['result'] );
        // a second pending row for the cleanup
        $p2 = expMailPreferences::forRecipient( $this->recipient( 'e' ) );
        $p2->set( 'marketing', true, $this->context() );
        $rows = expMailPendingRow::fetchForKey( $this->recipient( 'e' )->key() );
        $rows[0]->setAttribute( 'expires', time() - 10 );
        $rows[0]->store();
        $this->assertSame( 'pending', expMailPreferences::forRecipient( $this->recipient( 'e' ) )->state( 'marketing' ) );
        $r = expConsentLog::cleanup( true );
        $this->assertGreaterThanOrEqual( 1, $r['pending'] );
        $this->assertSame( 'pending', expMailPreferences::forRecipient( $this->recipient( 'e' ) )->state( 'marketing' ), 'a dry run changes nothing' );
        expConsentLog::cleanup();
        $this->assertSame( 'off', expMailPreferences::forRecipient( $this->recipient( 'e' ) )->state( 'marketing' ) );
        $this->assertCount( 0, expMailPendingRow::fetchForKey( $this->recipient( 'e' )->key() ) );
    }

    // ------------------------------------------------------------------ MP-08

    public function testTokens()
    {
        $recipient = $this->recipient( 'a' );
        $token = expMailToken::create( $recipient, 'unsubscribe', 'content' );
        $this->assertMatchesRegularExpression( '/^m1[A-Za-z0-9_-]+$/', $token );
        $this->assertStringNotContainsString( 'mptest', $token );
        $this->assertStringNotContainsString( base64_encode( self::address( 'a' ) ), $token, 'the address cannot be read from the link' );
        $payload = expMailToken::verify( $token, 'unsubscribe' );
        $this->assertSame( 'content', $payload['category'] );
        $this->assertSame( self::address( 'a' ), $payload['email'] );
        $this->assertSame( 0, $payload['expires'], 'unsubscribe links do not expire by default' );
        $this->assertSame( $recipient->key(), expMailRecipient::fromToken( $token )->key() );
        // purpose
        $this->assertNull( expMailToken::verify( $token, 'manage' ) );
        $this->assertSame( 'purpose', expMailToken::lastError() );
        $this->assertNull( expMailRecipient::fromToken( $token, 'confirm' ) );
        // tampering: every position changed is refused
        foreach ( array( 5, 20, strlen( $token ) - 3 ) as $pos )
        {
            $bad = $token;
            $bad[$pos] = $bad[$pos] === 'A' ? 'B' : 'A';
            $this->assertNull( expMailToken::verify( $bad, 'unsubscribe' ), "changed at $pos" );
            $this->assertContains( expMailToken::lastError(), array( 'tampered', 'malformed' ) );
        }
        // expiry
        $short = expMailToken::create( $recipient, 'manage', null, 60 );
        $this->assertNotNull( expMailToken::decode( $short ) );
        $this->assertNull( expMailToken::decode( $short, time() + 120 ) );
        $this->assertSame( 'expired', expMailToken::lastError() );
        $this->assertGreaterThan( 0, expMailToken::verify( expMailToken::create( $recipient, 'confirm' ), 'confirm' )['expires'], 'confirm links expire' );
        // malformed
        foreach ( array( '', 'm1', 'x1' . str_repeat( 'A', 60 ), 'm1' . str_repeat( '!', 60 ) ) as $bad )
        {
            $this->assertNull( expMailToken::decode( $bad ) );
            $this->assertSame( 'malformed', expMailToken::lastError() );
        }
        try
        {
            expMailToken::create( $recipient, 'login' );
            $this->fail( 'unknown purpose' );
        }
        catch ( InvalidArgumentException $e )
        {
        }
        $this->assertStringStartsWith( 'https://', expMailToken::url( 'manage', $token ) );
    }

    // ------------------------------------------------------------------ MP-09

    public function testSuppressionStoresOnlyAHash()
    {
        $email = self::address( 'f' );
        $this->assertFalse( expMailSuppression::isSuppressed( $email ) );
        $this->assertTrue( expMailSuppression::add( '  ' . strtoupper( $email ) . ' ', 'bounce', 'MPTEST 550 for ' . $email ) );
        $this->assertTrue( expMailSuppression::isSuppressed( $email ), 'case and spaces do not matter' );
        $this->assertSame( 'bounce', expMailSuppression::reason( $email ) );
        $hash = expMailSuppression::hash( $email );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{64}$/', $hash );
        $this->assertNotSame( hash( 'sha256', $email ), $hash, 'salted with the site secret' );
        $row = expMailSuppression::fetchByHash( $hash );
        $this->assertNotNull( $row );
        $all = json_encode( $row->attribute( 'email_hash' ) . $row->attribute( 'note' ) . $row->attribute( 'reason' ) );
        $this->assertStringNotContainsString( 'mptest-f', $all, 'the address is not stored, not even in the note' );
        // no optional mail, essential mail still goes
        $p = expMailPreferences::forRecipient( $this->recipient( 'f' ) );
        $p->set( 'content', true, $this->context( 'admin' ) );
        $this->assertSame( 'suppressed', $p->decision( 'content' ) );
        $this->assertTrue( $p->allows( 'orders' ) );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( $email ), 'content' ) ) );
        $this->assertCount( 0, $this->mailsTo( $email ) );
        $this->assertTrue( expMailSuppression::lift( $hash ) );
        $this->assertFalse( expMailSuppression::isSuppressed( $email ) );
        $this->assertFalse( expMailSuppression::lift( $email ) );
        try
        {
            expMailSuppression::add( $email, 'whim' );
            $this->fail( 'unknown reason' );
        }
        catch ( InvalidArgumentException $e )
        {
        }
        // unsubscribe from all suppresses; switching on again on the page lifts it
        expMailPreferencesService::unsubscribeAll( $this->recipient( 'f' ), $this->context( 'link' ) );
        $this->assertSame( 'unsubscribe_all', expMailSuppression::reason( $email ) );
        expMailPreferences::forRecipient( $this->recipient( 'f' ) )->setMaster( true, $this->context( 'page' ) );
        $this->assertFalse( expMailSuppression::isSuppressed( $email ) );
    }

    // ------------------------------------------------------------------ MP-10 .. MP-13: the gate

    public function testUncategorisedMailIsSentUnchanged()
    {
        $mail = $this->newMail( array( self::address( 'a' ), self::address( 'b' ) ), null );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $mails = $this->mails();
        $this->assertCount( 1, $mails, 'one mail to both, as before' );
        $this->assertStringContainsString( self::address( 'a' ), $mails[0] );
        $this->assertStringContainsString( self::address( 'b' ), $mails[0] );
        $this->assertNull( $this->headerOf( $mails[0], 'List-Unsubscribe' ) );
        $this->assertNull( $this->headerOf( $mails[0], 'X-Exp-Mail-Category' ) );
        $this->assertStringNotContainsString( '/mailpreferences/', $mails[0] );
        $this->assertStringEndsWith( "MPTEST body\r\n", $mails[0] );
        $log = $this->logLines();
        $last = end( $log );
        $this->assertSame( 'uncategorised', $last['d'] );
        $this->assertSame( 'no_category', $last['why'] );
        $this->assertStringContainsString( 'MailPreferencesCoreTest.php', (string)$last['s'] );
        $this->assertSame( 'uncategorised', expMailGate::lastResult()['decision'] );
        // the same sender listed as essential
        $this->setIni( 'GateSettings', 'EssentialSenders', array( 'MailPreferencesCoreTest' ) );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'a' ) ), null ) ) );
        $log = $this->logLines();
        $last = end( $log );
        $this->assertSame( 'essential', $last['d'] );
        $this->assertStringNotContainsString( '@', json_encode( $log ), 'no address in the gate log' );
    }

    public function testOptionalMailOnlyToWhoSwitchedItOn()
    {
        $this->setIni( 'FooterSettings', 'OrganisationName', 'MPTEST Organisation' );
        $this->setIni( 'FooterSettings', 'OrganisationAddress', 'MPTEST Street 1, 12345 Town' );
        expMailPreferences::forRecipient( $this->recipient( 'a' ) )->set( 'content', true, $this->context() );
        expMailPreferences::forRecipient( $this->recipient( 'b' ) )->set( 'content', true, $this->context() );
        $mail = $this->newMail( array( self::address( 'a' ), self::address( 'b' ), self::address( 'c' ) ), 'content' );
        $check = expMailGate::check( $mail );
        $this->assertTrue( $check['allow'] );
        $this->assertSame( array( 'allow', 'allow', 'off' ), array_column( $check['recipients'], 'decision' ) );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $this->assertCount( 2, $this->mails(), 'one mail for each allowed recipient' );
        $this->assertCount( 0, $this->mailsTo( self::address( 'c' ) ) );
        foreach ( array( 'a', 'b' ) as $k )
        {
            $mails = $this->mailsTo( self::address( $k ) );
            $this->assertCount( 1, $mails, $k );
            $m = $mails[0];
            $other = $k === 'a' ? 'b' : 'a';
            $this->assertStringNotContainsString( self::address( $other ), $m, 'no other recipient in the mail' );
            $this->assertSame( 'content', $this->headerOf( $m, 'X-Exp-Mail-Category' ) );
            $this->assertSame( 'List-Unsubscribe=One-Click', $this->headerOf( $m, 'List-Unsubscribe-Post' ) );
            $lu = $this->headerOf( $m, 'List-Unsubscribe' );
            $this->assertSame( 1, preg_match( '#^<(https://[^>]+/mailpreferences/unsubscribe/(m1[A-Za-z0-9_-]+))>$#', (string)$lu, $match ), (string)$lu );
            $payload = expMailToken::verify( $match[2], 'unsubscribe' );
            $this->assertSame( self::address( $k ), $payload['email'], 'the link is the recipient\'s own' );
            $this->assertSame( 'content', $payload['category'] );
            $this->assertStringContainsString( $match[1], $m, 'the unsubscribe link is in the footer too' );
            $this->assertMatchesRegularExpression( '#/mailpreferences/manage/m1[A-Za-z0-9_-]+#', $m );
            $this->assertStringContainsString( 'MPTEST Organisation', $m );
            $this->assertStringContainsString( 'MPTEST Street 1, 12345 Town', $m );
        }
        $r = expMailGate::lastResult();
        $this->assertSame( 2, $r['sent'] );
        $this->assertSame( array( 'off' ), $r['blocked'] );
        $this->assertSame( 'partly_blocked', $r['decision'] );
        // the mail object has its recipients and body back
        $this->assertCount( 3, $mail->bccElements() );
        $this->assertSame( "MPTEST body\n", $mail->body( false ) );
        $decisions = array_column( $this->logLines(), 'd' );
        $this->assertSame( 2, count( array_keys( $decisions, 'sent' ) ) );
        $this->assertSame( 1, count( array_keys( $decisions, 'blocked' ) ) );
    }

    public function testEssentialMailAndAllBlocked()
    {
        $mail = $this->newMail( array( self::address( 'g' ) ), 'orders' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $mails = $this->mailsTo( self::address( 'g' ) );
        $this->assertCount( 1, $mails );
        $this->assertSame( 'orders', $this->headerOf( $mails[0], 'X-Exp-Mail-Category' ) );
        $this->assertNull( $this->headerOf( $mails[0], 'List-Unsubscribe' ) );
        $this->assertStringNotContainsString( '/mailpreferences/', $mails[0] );
        // all blocked: nothing written, the sender is told it went
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'g' ), self::address( 'h' ) ), 'marketing' ) ) );
        $this->assertCount( 1, $this->mails() );
        $this->assertSame( 'blocked', expMailGate::lastResult()['decision'] );
        // a header instead of setCategory() works the same
        $mail = $this->newMail( array( self::address( 'h' ) ), null );
        $mail->addExtraHeader( 'X-Exp-Mail-Category', 'marketing' );
        $this->assertSame( 'marketing', expMailGate::category( $mail ) );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $this->assertCount( 1, $this->mails() );
        // an unknown category is sent as before
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'h' ) ), 'mptestunknown' ) ) );
        $this->assertCount( 1, $this->mailsTo( self::address( 'h' ) ) );
        $this->assertSame( 'unknown_category', expMailGate::lastResult()['decision'] );
        // the gate disabled: optional mail goes (with its footer), a suppressed address still gets nothing
        $this->setIni( 'GateSettings', 'Gate', 'disabled' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'g' ) ), 'marketing' ) ) );
        $this->assertCount( 2, $this->mailsTo( self::address( 'g' ) ) );
    }

    public function testHtmlFooterBeforeTheBodyEnd()
    {
        $this->setIni( 'FooterSettings', 'OrganisationName', 'MPTEST Organisation' );
        $this->setIni( 'FooterSettings', 'OrganisationAddress', 'MPTEST Street 1' );
        expMailPreferences::forRecipient( $this->recipient( 'a' ) )->set( 'system', true, $this->context() );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'a' ) ), 'system', true ) ) );
        $mails = $this->mailsTo( self::address( 'a' ) );
        $this->assertCount( 1, $mails );
        $body = $mails[0];
        $footerAt = strpos( $body, 'MPTEST Organisation' );
        $this->assertNotFalse( $footerAt );
        $this->assertLessThan( strripos( $body, '</body>' ), $footerAt, 'the footer is inside the body' );
        $this->assertGreaterThan( strpos( $body, 'MPTEST body' ), $footerAt );
        $this->assertMatchesRegularExpression( '#href="https://[^"]+/mailpreferences/unsubscribe/m1#', $body );
        // the built-in footer (no template) carries the same
        $this->setIni( 'FooterSettings', 'Template', '' );
        $text = expMailGate::footer( expMailCategoryRegistry::instance()->get( 'system' ), array( 'manage' => 'https://x.invalid/m', 'unsubscribe' => 'https://x.invalid/u' ), false );
        $this->assertStringContainsString( 'https://x.invalid/u', $text );
        $this->assertStringContainsString( 'https://x.invalid/m', $text );
        $this->assertStringContainsString( 'MPTEST Organisation, MPTEST Street 1', $text );
    }

    // ------------------------------------------------------------------ MP-14

    public function testUnsubscribeByLink()
    {
        $recipient = $this->recipient( 'b' );
        $p = expMailPreferences::forRecipient( $recipient );
        $p->set( 'content', true, $this->context() );
        $p->set( 'system', true, $this->context() );
        $r = expMailPreferencesService::unsubscribe( expMailToken::create( $recipient, 'unsubscribe', 'content' ), $this->context( 'link', 'MPTEST one click' ) );
        $this->assertSame( 'unsubscribed', $r['result'] );
        $this->assertSame( 'content', $r['category'] );
        $p = expMailPreferences::forRecipient( $recipient );
        $this->assertFalse( $p->isOn( 'content' ) );
        $this->assertTrue( $p->allows( 'system' ) );
        $r = expMailPreferencesService::unsubscribe( expMailToken::create( $recipient, 'unsubscribe' ), $this->context( 'link' ) );
        $this->assertSame( '', $r['category'] );
        $p = expMailPreferences::forRecipient( $recipient );
        $this->assertFalse( $p->masterOn() );
        $this->assertTrue( $p->isOn( 'system' ) );
        $this->assertFalse( $p->allows( 'system' ) );
        $this->assertSame( 'invalid', expMailPreferencesService::unsubscribe( expMailToken::create( $recipient, 'manage' ) )['result'] );
        $rows = expConsentLog::fetchForRecipient( $recipient );
        $this->assertSame( 'master_off', $rows[0]->attribute( 'action' ) );
        $this->assertSame( 'link', $rows[0]->attribute( 'source' ) );
        $this->assertSame( 'MPTEST one click', $rows[1]->attribute( 'wording' ) );
    }

    // ------------------------------------------------------------------ MP-15, MP-16

    public function testExportAndErase()
    {
        $recipient = $this->recipient( 'e' );
        $p = expMailPreferences::forRecipient( $recipient );
        $p->set( 'content', true, $this->context() );
        $p->set( 'content', false, $this->context( 'link' ) );
        $p->set( 'system', true, $this->context() );
        $export = $p->export();
        $this->assertSame( self::address( 'e' ), $export['recipient']['email'] );
        $this->assertSame( 'on', $export['master'] );
        $states = array_column( $export['categories'], 'state', 'identifier' );
        $this->assertSame( 'off', $states['content'] );
        $this->assertSame( 'on', $states['system'] );
        $this->assertSame( 'on', $states['security'] );
        $this->assertCount( 3, $export['consent_log'] );
        $this->assertNotFalse( json_encode( $export ) );
        $csv = expMailPreferences::exportToCsv( $export );
        $this->assertStringContainsString( 'category,system,on', $csv );
        $this->assertStringContainsString( 'consent_log,', $csv );

        $r = $p->erase( $this->context( 'admin', 'MPTEST erasure request' ) );
        $this->assertSame( 2, $r['preferences'] );
        $this->assertSame( 4, $r['anonymised'], 'three changes and the erase row' );
        $this->assertCount( 0, expMailPreferenceRow::fetchForKey( $recipient->key() ) );
        $this->assertCount( 0, expConsentLog::fetchForRecipient( $recipient ), 'nothing is found by the person any more' );
        $anon = 'x:' . substr( hash( 'sha256', $recipient->key() . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $rows = expConsentLog::fetchList( array( 'recipient_key' => $anon ), 0, 0 );
        $this->assertCount( 4, $rows );
        foreach ( $rows as $row )
        {
            $this->assertSame( '', $row->attribute( 'email' ) );
            $this->assertSame( '', $row->attribute( 'ip' ) );
            $this->assertSame( 0, (int)$row->attribute( 'user_id' ) );
            $this->assertSame( 1, (int)$row->attribute( 'anonymised' ) );
        }
        $actions = array_map( function ( $r ) { return $r->attribute( 'action' ) . ':' . $r->attribute( 'category' ); }, $rows );
        $this->assertContains( 'off:content', $actions, 'the withdrawal is kept' );
        $this->assertContains( 'erase:', $actions );
    }

    public function testConsentLogCsv()
    {
        $recipient = $this->recipient( 'csv' );
        expMailPreferences::forRecipient( $recipient )->set( 'content', true, $this->context( 'page', '=HYPERLINK("http://x.invalid")' ) );
        expMailPreferences::forRecipient( $recipient )->set( 'system', true, $this->context( 'signup', 'MPTEST sign-up box' ) );
        $csv = expConsentLog::exportCsv( array( 'email' => strtoupper( self::address( 'csv' ) ) ) );
        $lines = array_values( array_filter( explode( "\n", $csv ), 'strlen' ) );
        $this->assertSame( implode( ',', expConsentLog::CSV_COLUMNS ), $lines[0] );
        $this->assertCount( 3, $lines );
        $this->assertStringContainsString( "'=HYPERLINK", $csv, 'a formula is not run by a spreadsheet' );
        $only = expConsentLog::exportCsv( array( 'email' => self::address( 'csv' ), 'source' => 'signup' ) );
        $this->assertCount( 2, array_filter( explode( "\n", $only ), 'strlen' ) );
        $this->assertSame( 2, expConsentLog::countList( array( 'recipient_key' => $recipient->key() ) ) );
    }

    // ------------------------------------------------------------------ MP-17

    public function testRetentionCleanup()
    {
        $db = eZDB::instance();
        $gone = $this->recipient( 'g' );   // no preference left: gone
        $here = $this->recipient( 'h' );   // a preference: still here
        expMailPreferences::forRecipient( $here )->set( 'content', true, $this->context() );
        expMailPreferences::forRecipient( $gone )->set( 'content', true, $this->context() );
        $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '" . $db->escapeString( $gone->key() ) . "'" );
        $old = time() - 4 * 365 * 86400;
        $db->query( "UPDATE expmail_consent_log SET created = $old WHERE recipient_key IN ( '" . $db->escapeString( $gone->key() ) . "', '" . $db->escapeString( $here->key() ) . "' )" );
        $dry = expConsentLog::cleanup( true );
        $this->assertGreaterThanOrEqual( 1, $dry['consent'] );
        $this->assertCount( 1, expConsentLog::fetchForRecipient( $gone ), 'a dry run removes nothing' );
        expConsentLog::cleanup();
        $this->assertCount( 0, expConsentLog::fetchForRecipient( $gone ) );
        $this->assertCount( 1, expConsentLog::fetchForRecipient( $here ), 'the log of a person who is there is kept' );
    }

    // ------------------------------------------------------------------ MP-18

    public function testSendMeALink()
    {
        $this->setIni( 'TokenSettings', 'RequestLinkLimit', '2' );
        $this->assertSame( 'invalid', expMailPreferencesService::requestLink( 'not an address' ) );
        $this->assertSame( 'sent', expMailPreferencesService::requestLink( self::address( 'a' ) ) );
        $mails = $this->mailsTo( self::address( 'a' ) );
        $this->assertCount( 1, $mails );
        $this->assertSame( 'security', $this->headerOf( $mails[0], 'X-Exp-Mail-Category' ) );
        $this->assertSame( 1, preg_match( '#/mailpreferences/manage/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        $payload = expMailToken::verify( $m[1], 'manage' );
        $this->assertSame( self::address( 'a' ), $payload['email'] );
        $this->assertGreaterThan( time(), $payload['expires'], 'the link expires' );
        $this->assertSame( 'sent', expMailPreferencesService::requestLink( self::address( 'a' ) ) );
        $this->assertSame( 'rate_limited', expMailPreferencesService::requestLink( self::address( 'a' ) ) );
        $this->assertCount( 2, $this->mailsTo( self::address( 'a' ) ) );
        expMailSuppression::add( self::address( 'b' ), 'complaint' );
        $this->assertSame( 'blocked', expMailPreferencesService::requestLink( self::address( 'b' ) ) );
        $this->assertCount( 0, $this->mailsTo( self::address( 'b' ) ) );
    }

    // ------------------------------------------------------------------ MP-19

    public function testUserRecipientAndAddressChange()
    {
        $this->assertNotNull( self::$user );
        $r = expMailRecipient::fromUser( self::$user );
        $this->assertSame( 'u:' . self::$userObjectID, $r->key() );
        $this->assertSame( self::$userObjectID, $r->userId() );
        $this->assertSame( $r->key(), expMailRecipient::fromAddress( strtoupper( self::address( 'user' ) ) )->key(), 'the address of an account is the account' );
        $this->assertSame( 'a:', substr( expMailRecipient::fromAddress( self::address( 'user' ), false )->key(), 0, 2 ) );
        expMailPreferences::forRecipient( $r )->set( 'content', true, $this->context() );
        $mail = $this->newMail( array( self::address( 'user' ) ), 'content' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $mails = $this->mailsTo( self::address( 'user' ) );
        $this->assertCount( 1, $mails );
        $this->assertSame( 1, preg_match( '#/mailpreferences/unsubscribe/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        $payload = expMailToken::verify( $m[1], 'unsubscribe' );
        $this->assertSame( self::$userObjectID, $payload['user'] );
        $this->assertSame( '', $payload['email'], 'a user link carries the id, not the address' );

        // the address change, confirmed by the new address
        $this->assertSame( 'invalid', expMailPreferencesService::requestEmailChange( self::$user, 'nope', $this->context() ) );
        $this->assertSame( 'unchanged', expMailPreferencesService::requestEmailChange( self::$user, self::address( 'user' ), $this->context() ) );
        $this->assertSame( 'in_use', expMailPreferencesService::requestEmailChange( self::$user, eZUser::fetchByName( 'admin' )->attribute( 'email' ), $this->context() ) );
        $this->assertSame( 'pending_confirmation', expMailPreferencesService::requestEmailChange( self::$user, self::address( 'user-new' ), $this->context() ) );
        $this->assertSame( self::address( 'user' ), eZUser::fetch( self::$userObjectID )->attribute( 'email' ), 'nothing changes before the confirmation' );
        $mails = $this->mailsTo( self::address( 'user-new' ) );
        $this->assertCount( 1, $mails );
        $this->assertSame( 1, preg_match( '#/mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        $result = expMailPreferencesService::confirm( $m[1], $this->context( 'confirm' ) );
        $this->assertSame( 'confirmed', $result['result'] );
        $this->assertSame( 'email_change', $result['kind'] );
        eZUser::cleanupCache();
        $this->assertSame( self::address( 'user-new' ), eZUser::fetch( self::$userObjectID )->attribute( 'email' ) );
        // back, for the other tests
        $u = eZUser::fetch( self::$userObjectID );
        $u->setAttribute( 'email', self::address( 'user' ) );
        $u->store();
        self::$user = $u;
    }

    // ------------------------------------------------------------------ MP-20

    /** @return array( int code, string output ) */
    private function command( $script, array $args )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/' . $script . '.php' ), $args, array( '--allow-root-user', '--no-colors' ) );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        if ( class_exists( 'eZDBQueryCache' ) )
            eZDBQueryCache::clearAll();
        return array( $code, $out );
    }

    public function testCommands()
    {
        foreach ( array( 'mailstatus' => '--json', 'mailpreferences' => '--category', 'mailsuppression' => '--reason',
                         'mailconsent' => '--dry-run', 'mailgate' => '--write' ) as $script => $option )
        {
            list( $code, $out ) = $this->command( $script, array( '--help' ) );
            $this->assertSame( 0, $code, $script . ': ' . $out );
            $this->assertStringContainsString( $option, $out, $script );
        }
        list( $code, $out ) = $this->command( 'mailstatus', array( '--json' ) );
        $data = json_decode( substr( $out, (int)strpos( $out, '{' ), strrpos( $out, '}' ) - (int)strpos( $out, '{' ) + 1 ), true );
        $this->assertIsArray( $data, $out );
        $this->assertTrue( $data['tables']['expmail_preference'] );
        $this->assertArrayHasKey( 'gate_24h', $data );

        expMailPreferences::forRecipient( $this->recipient( 'a' ) )->set( 'content', true, $this->context() );
        list( $code, $out ) = $this->command( 'mailpreferences', array( 'show', '--email=' . self::address( 'a' ) ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringNotContainsString( self::address( 'a' ), $out, 'the address is hidden' );
        $this->assertMatchesRegularExpression( '/content\s+on/', $out );
        list( $code, $out ) = $this->command( 'mailgate', array( '--category=content', '--to=' . self::address( 'a' ) . ',' . self::address( 'b' ) ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'allow', $out );
        $this->assertStringContainsString( 'off', $out );
        $this->assertCount( 0, $this->mails(), 'the gate test without --write sends nothing' );
        list( $code, $out ) = $this->command( 'mailconsent', array( 'cleanup', '--dry-run' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'Would remove', $out );
    }
}
