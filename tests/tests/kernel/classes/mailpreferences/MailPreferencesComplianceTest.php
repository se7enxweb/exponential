<?php
/**
 * The law checklist of the e-mail preferences (doc/specifications/6.0/mail-preferences-compliance.md): one test per
 * requirement of CAN-SPAM, GDPR + ePrivacy, CCPA/CPRA, CASL and RFC 8058 that is not already proven by
 * MailPreferencesCoreTest (MP-01 .. MP-20). The checklist names the test of every requirement. Not legal advice.
 *
 * Live style, like MailPreferencesCoreTest: the tests run on the installation they find (no test database), with
 * addresses on mplaw.invalid and one test user (remote id mplaw-user) that are removed again; without an
 * installation (CI) they are skipped.
 *
 * NO MAIL LEAVES THE SERVER. The mail transport is forced to "file" in the process's own ini (never written to a
 * settings file) and the mail goes to var/tmp/mailpreferences-mail/<run>/; setUpBeforeClass() and setUp() refuse to
 * run when the transport is not the file transport. Every address is on the reserved .invalid domain. The one test
 * that talks to the web server (LAW-24) posts an unsubscribe, which sends nothing.
 *
 *  LAW-01  CAN-SPAM, CASL: every optional category's mail carries an unsubscribe link and the RFC 8058 headers
 *  LAW-02  CAN-SPAM, CASL: a multipart mail gets the footer in its text and its HTML part
 *  LAW-03  CAN-SPAM, CASL: an unsubscribe is honoured at once: the very next mail is not sent
 *  LAW-04  CAN-SPAM (30 days), CASL (60 days): the unsubscribe and manage links in a mail do not expire
 *  LAW-05  CAN-SPAM, CASL: sender identity and postal address in the footer; the status warns while they are empty
 *  LAW-06  CAN-SPAM: the gate never changes From, Reply-To or Subject; it only adds its own headers
 *  LAW-07  CAN-SPAM: no login, fee or other step: the link alone unsubscribes, a person without an account too
 *  LAW-08  GDPR, ePrivacy: opt-in: every optional category of the settings is off by default, also in the status
 *  LAW-09  GDPR: no pre-ticked boxes on the registration form, also not after the form came back with an error
 *  LAW-10  GDPR: nothing ticked at sign-up stores nothing; a ticked box is recorded with the text shown
 *  LAW-11  GDPR, ePrivacy, CASL: marketing and newsletters need a confirmed (double) opt-in, also from sign-up
 *  LAW-12  GDPR: specific: one category on leaves every other one off; the main switch changes no category
 *  LAW-13  GDPR, CCPA: withdrawal is as easy as consent: one form post each way, both recorded the same way
 *  LAW-14  GDPR, CCPA: the page shows every optional category unticked and an equal "turn off" button (no dark pattern)
 *  LAW-15  GDPR: informed: the consent log keeps the exact text shown on the page for each category
 *  LAW-16  GDPR, CCPA: right of access: the export holds the categories, pending confirmations and the whole log
 *  LAW-17  GDPR, CCPA: erasure keeps the proof of the withdrawal and removes the person from the log
 *  LAW-18  GDPR: an administrator's change on a person's request is recorded as "admin" with the administrator
 *  LAW-19  GDPR: the suppression list cannot be read back into addresses (hash with the site secret)
 *  LAW-20  CASL, GDPR: a hard bounce or complaint stops even a requested link and a double opt-in mail
 *  LAW-21  RFC 8058: List-Unsubscribe is one https URI in angle brackets; List-Unsubscribe-Post is exact
 *  LAW-22  RFC 8058: the List-Unsubscribe link of each recipient is its own, also for Cc and To recipients
 *  LAW-23  RFC 8058 / CAN-SPAM: optional mail with SplitRecipients disabled still points to the preference page
 *  LAW-24  RFC 8058: the one-click POST over HTTP without cookies unsubscribes; GET only shows a button (live web)
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

use Exponential\Service\MailPreferencesPage;

class MailPreferencesComplianceTest extends PHPUnit\Framework\TestCase
{
    const DOMAIN = 'mplaw.invalid';

    private static $installation;
    private static $mailDir;
    private static $logFile;
    private static $user;
    private static $userObjectID = 0;
    private static $iniBackup = array();
    private static $postBackup = array();

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expMailPreferencesService::tableExists( 'expmail_preference' ) )
            self::markTestSkipped( 'needs the expmail_* tables (database update)' );
        self::$mailDir = 'var/tmp/mailpreferences-mail/mplaw-' . getmypid() . '-' . time();
        self::$logFile = self::$installation . '/var/tmp/mailpreferences-mail/mplaw-' . getmypid() . '-gate.jsonl';
        self::forceFileTransport();
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        self::removeUser();
        self::clean();
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
        self::$postBackup = $_POST;
        $this->setIni( 'FooterSettings', 'OrganisationName', 'MPLAW Organisation' );
        $this->setIni( 'FooterSettings', 'OrganisationAddress', 'MPLAW Street 1, 12345 Town' );
    }

    protected function tearDown(): void
    {
        $_POST = self::$postBackup;
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
        return 'mplaw-' . $key . '@' . self::DOMAIN;
    }

    private static function addresses()
    {
        $out = array();
        foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'http', 'user' ) as $k )
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
        $hashes = array_map( function ( $k ) { return substr( $k, 2 ); }, $keys );
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
        $db->query( 'DELETE FROM expmail_suppression WHERE email_hash IN (' . $in( $hashes ) . ')' );
    }

    private static function makeUser()
    {
        $login = 'mplaw-user';
        $password = bin2hex( random_bytes( 16 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . self::address( 'user' ) . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        $admin = eZUser::fetchByName( 'admin' );
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => $admin->attribute( 'contentobject_id' ),
            'remote_id' => 'mplaw-user', 'attributes' => array( 'first_name' => 'MPLAW', 'last_name' => 'User', 'user_account' => $account ) ) );
        if ( !$object )
            throw new RuntimeException( 'MPLAW: the test user could not be created' );
        self::$userObjectID = (int)$object->attribute( 'id' );
        eZUser::cleanupCache();
        self::$user = eZUser::fetch( self::$userObjectID );
    }

    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( 'mplaw-user' );
        if ( $object )
        {
            $id = (int)$object->attribute( 'id' );
            $db = eZDB::instance();
            $db->query( "DELETE FROM expmail_preference WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_pending WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_consent_log WHERE user_id = $id OR recipient_key = 'u:$id' OR actor_user_id = $id" );
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

    private function context( $source = 'page', $wording = 'MPLAW wording' )
    {
        return new expConsentContext( $source, $wording, '192.0.2.20', 0, 'mplaw' );
    }

    /** @return string[] the mail written so far */
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
        $mail->setSender( 'mplaw-sender@' . self::DOMAIN, 'MPLAW Sender' );
        foreach ( $to as $t )
            $mail->addBcc( $t );
        $mail->setSubject( 'MPLAW subject' );
        if ( $html )
        {
            $mail->setContentType( 'text/html' );
            $mail->setBody( '<html><body><p>MPLAW body</p></body></html>' );
        }
        else
            $mail->setBody( "MPLAW body\n" );
        if ( $category !== null )
            $mail->setCategory( $category );
        return $mail;
    }

    private function headerOf( $mail, $name )
    {
        return preg_match( '/^' . preg_quote( $name, '/' ) . ':\s*(.*)$/mi', $mail, $m ) ? trim( $m[1] ) : null;
    }

    /** a person who has the category on, at once (import: the double opt-in was done elsewhere) */
    private function subscribe( $key, $category )
    {
        $p = expMailPreferences::forRecipient( $this->recipient( $key ) );
        $this->assertSame( 'on', $p->set( $category, true, $this->context( 'import', 'MPLAW import' ) ) );
        return $p;
    }

    /** @return string the one-click link of the only mail to $email */
    private function unsubscribeLinkOf( $email )
    {
        $mails = $this->mailsTo( $email );
        $this->assertCount( 1, $mails, $email );
        $this->assertSame( 1, preg_match( '#^List-Unsubscribe:\s*<(https://[^>]+/mailpreferences/unsubscribe/(m1[A-Za-z0-9_-]+))>#mi', $mails[0], $m ), $mails[0] );
        return $m[2];
    }

    /** posts a form to MailPreferencesPage::handlePost() as the page would */
    private function post( expMailPreferences $prefs, array $post, $source = 'page' )
    {
        $_POST = $post;
        $notices = MailPreferencesPage::handlePost( $prefs, eZHTTPTool::instance(), $source );
        $_POST = array();
        return $notices;
    }

    // ------------------------------------------------------------------ LAW-01 .. LAW-07: CAN-SPAM, CASL

    public function testEveryOptionalCategoryCarriesAnUnsubscribe()
    {
        $optional = array_keys( expMailCategoryRegistry::instance()->optional() );
        $this->assertNotEmpty( $optional );
        foreach ( $optional as $id )
        {
            foreach ( glob( self::$mailDir . '/*.mail' ) ?: array() as $f )
                @unlink( $f );
            $this->subscribe( 'a', $id );
            $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'a' ) ), $id ) ), $id );
            $mails = $this->mailsTo( self::address( 'a' ) );
            $this->assertCount( 1, $mails, $id );
            $token = $this->unsubscribeLinkOf( self::address( 'a' ) );
            $this->assertSame( 'List-Unsubscribe=One-Click', $this->headerOf( $mails[0], 'List-Unsubscribe-Post' ), $id );
            $this->assertStringContainsString( '/mailpreferences/unsubscribe/' . $token, substr( $mails[0], strpos( $mails[0], 'MPLAW body' ) ), "$id: the link is in the body too" );
            $this->assertSame( $id, expMailToken::verify( $token, 'unsubscribe' )['category'], $id );
        }
    }

    public function testMultipartMailGetsTheFooterInEveryTextPart()
    {
        // a body given as an ezcMailPart (the SMTP and sendmail transports send it; the file transport writes only
        // a text body, so the decorated part is looked at directly)
        $mail = new eZMail();
        $mail->setSender( 'mplaw-sender@' . self::DOMAIN );
        $mail->setReceiver( self::address( 'b' ) );
        $mail->setSubject( 'MPLAW subject' );
        $html = new ezcMailText( '<html><body><p>MPLAW html part</p></body></html>', 'utf-8' );
        $html->subType = 'html';
        $original = new ezcMailMultipartAlternative( new ezcMailText( "MPLAW plain part\n", 'utf-8' ), $html );
        $mail->Mail->body = $original;
        expMailGate::decorate( $mail, $this->recipient( 'b' ), 'marketing' );
        $this->assertNotSame( $original, $mail->Mail->body, 'the sender\'s part is not changed' );
        list( $plain, $htmlPart ) = $mail->Mail->body->getParts();
        foreach ( array( 'plain' => $plain->text, 'html' => $htmlPart->text ) as $kind => $text )
        {
            $this->assertStringContainsString( 'MPLAW Street 1, 12345 Town', $text, "$kind part: postal address" );
            $this->assertStringContainsString( 'MPLAW Organisation', $text, "$kind part: sender" );
            $this->assertStringContainsString( '/mailpreferences/unsubscribe/m1', $text, "$kind part: unsubscribe link" );
        }
        $this->assertMatchesRegularExpression( '#href="https://[^"]+/mailpreferences/unsubscribe/m1#', $htmlPart->text );
        $this->assertLessThan( strripos( $htmlPart->text, '</body>' ), strpos( $htmlPart->text, 'MPLAW Organisation' ) );
        $this->assertStringNotContainsString( '<a ', $plain->text, 'no HTML in the text part' );
        $this->assertSame( 'List-Unsubscribe=One-Click', $mail->Mail->getHeader( 'List-Unsubscribe-Post' ) );
    }

    public function testUnsubscribeIsHonouredAtOnce()
    {
        $this->subscribe( 'c', 'newsletter' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'c' ) ), 'newsletter' ) ) );
        $token = $this->unsubscribeLinkOf( self::address( 'c' ) );
        $this->assertSame( 'unsubscribed', expMailPreferencesService::unsubscribe( $token, $this->context( 'link', 'MPLAW one click' ) )['result'] );
        // the next mail, in the same second: not sent, and the log says why
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'c' ) ), 'newsletter' ) ) );
        $this->assertCount( 1, $this->mailsTo( self::address( 'c' ) ) );
        $this->assertSame( 'blocked', expMailGate::lastResult()['decision'] );
        $this->assertSame( array( 'off' ), expMailGate::lastResult()['blocked'] );
        // a second click on the same link is harmless
        $this->assertSame( 'unsubscribed', expMailPreferencesService::unsubscribe( $token, $this->context( 'link' ) )['result'] );
    }

    public function testLinksInMailDoNotExpire()
    {
        $this->subscribe( 'd', 'content' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'd' ) ), 'content' ) ) );
        $mails = $this->mailsTo( self::address( 'd' ) );
        $token = $this->unsubscribeLinkOf( self::address( 'd' ) );
        $this->assertSame( 1, preg_match( '#/mailpreferences/manage/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        foreach ( array( 31, 61, 3650 ) as $days )
        {
            $this->assertNotNull( expMailToken::decode( $token, time() + $days * 86400 ), "unsubscribe link after $days days" );
            $this->assertNotNull( expMailToken::decode( $m[1], time() + $days * 86400 ), "manage link after $days days" );
        }
        // the shipped defaults: unsubscribe and manage never expire
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $ttl = $ini->variable( 'TokenSettings', 'TTL' );
        $this->assertSame( '0', (string)$ttl['unsubscribe'] );
        $this->assertSame( '0', (string)$ttl['manage'] );
    }

    public function testSenderIdentityAndPostalAddress()
    {
        $this->subscribe( 'e', 'system' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'e' ) ), 'system' ) ) );
        $mail = $this->mailsTo( self::address( 'e' ) )[0];
        $this->assertStringContainsString( 'MPLAW Organisation', $mail );
        $this->assertStringContainsString( 'MPLAW Street 1, 12345 Town', $mail );
        $this->assertStringContainsString( 'mplaw-sender@' . self::DOMAIN, (string)$this->headerOf( $mail, 'From' ) );
        // in a text mail every link ends its line, or the mail program makes it a link to a wrong address
        $body = substr( $mail, strpos( $mail, 'MPLAW body' ) );
        $this->assertMatchesRegularExpression( '#/mailpreferences/unsubscribe/m1[A-Za-z0-9_-]+\r?\n#', $body, $body );
        $this->assertMatchesRegularExpression( '#/mailpreferences/manage/m1[A-Za-z0-9_-]+\r?\n#', $body, $body );
        $this->assertMatchesRegularExpression( '#^MPLAW Organisation\r?$#m', $body, 'the sender on a line of its own' );
        $codes = array_column( expMailPreferencesService::status()['problems'], 1 );
        $this->assertNotContains( 'footer_missing', $codes );
        $this->setIni( 'FooterSettings', 'OrganisationAddress', '' );
        $codes = array_column( expMailPreferencesService::status()['problems'], 1 );
        $this->assertContains( 'footer_missing', $codes, 'the status warns while the postal address is empty' );
        $this->assertStringContainsString( 'postal address', expMailPreferencesService::problemText( array( 'warning', 'footer_missing', '' ) ) );
    }

    public function testTheGateDoesNotChangeFromReplyToOrSubject()
    {
        $this->subscribe( 'f', 'marketing' );
        $mail = $this->newMail( array( self::address( 'f' ) ), 'marketing' );
        $mail->setReplyTo( 'mplaw-reply@' . self::DOMAIN );
        $mail->addExtraHeader( 'X-MPLAW', 'kept' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $m = $this->mailsTo( self::address( 'f' ) )[0];
        $this->assertSame( 'MPLAW subject', $this->headerOf( $m, 'Subject' ) );
        $this->assertStringContainsString( 'mplaw-sender@' . self::DOMAIN, (string)$this->headerOf( $m, 'From' ) );
        $this->assertStringContainsString( 'mplaw-reply@' . self::DOMAIN, (string)$this->headerOf( $m, 'Reply-To' ) );
        $this->assertSame( 'kept', $this->headerOf( $m, 'X-MPLAW' ) );
        // the mail object the sender keeps is unchanged too
        $this->assertSame( 'MPLAW subject', $mail->subject() );
        $this->assertSame( "MPLAW body\n", $mail->body( false ) );
    }

    public function testNoLoginOrOtherStepToUnsubscribe()
    {
        // a person without an account, and a user: the link alone is enough
        $this->subscribe( 'g', 'content' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'g' ) ), 'content' ) ) );
        $token = $this->unsubscribeLinkOf( self::address( 'g' ) );
        $anonymous = eZUser::fetch( (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' ) );
        $admin = eZUser::currentUser();
        eZUser::setCurrentlyLoggedInUser( $anonymous, $anonymous->attribute( 'contentobject_id' ) );
        try
        {
            $this->assertSame( 'unsubscribed', expMailPreferencesService::unsubscribe( $token, $this->context( 'link' ) )['result'] );
            $user = expMailRecipient::fromUser( self::$user );
            expMailPreferences::forRecipient( $user )->set( 'content', true, $this->context( 'import' ) );
            $r = expMailPreferencesService::unsubscribe( expMailToken::create( $user, 'unsubscribe', 'content' ), $this->context( 'link' ) );
            $this->assertSame( 'unsubscribed', $r['result'] );
            $this->assertFalse( expMailPreferences::forRecipient( $user )->isOn( 'content' ) );
        }
        finally
        {
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        }
        $this->assertFalse( expMailPreferences::forRecipient( $this->recipient( 'g' ) )->isOn( 'content' ) );
        // the module: the unsubscribe view has no policy and is reachable without login
        $policyOmit = (array)eZINI::instance()->variable( 'RoleSettings', 'PolicyOmitList' );
        $this->assertContains( 'mailpreferences/unsubscribe', $policyOmit );
        $this->assertContains( 'mailpreferences/manage', $policyOmit );
    }

    // ------------------------------------------------------------------ LAW-08 .. LAW-15: GDPR, ePrivacy, CCPA

    public function testOptInByDefault()
    {
        foreach ( expMailCategoryRegistry::instance()->iniCategories() as $id => $c )
            if ( !$c->essential )
                $this->assertFalse( $c->defaultOn, "$id is off until the person turns it on" );
        $user = expMailRecipient::fromUser( self::$user );
        $p = expMailPreferences::forRecipient( $user );
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
            if ( !$c->defaultOn && !$c->handlerClass )
                $this->assertFalse( $p->allows( $id ), "a new account gets no $id mail" );
    }

    public function testNoPreTickedBoxesOnTheRegistrationForm()
    {
        $_POST = array();
        $boxes = MailPreferencesPage::signupCategories( eZHTTPTool::instance() );
        $this->assertNotEmpty( $boxes );
        $this->assertSame( array_keys( expMailCategoryRegistry::instance()->optional() ), array_column( $boxes, 'identifier' ), 'one box per optional category' );
        foreach ( $boxes as $b )
            $this->assertFalse( $b['checked'], $b['identifier'] . ' is not ticked' );
        // the form shown again after an error keeps exactly what the person ticked
        $_POST = array( 'MailPreferenceCategory' => array( 'content' ) );
        $boxes = array_column( MailPreferencesPage::signupCategories( eZHTTPTool::instance() ), 'checked', 'identifier' );
        $this->assertTrue( $boxes['content'] );
        $this->assertSame( array( 'content' ), array_keys( array_filter( $boxes ) ) );
        // the template renders the boxes unticked
        $r = expMailPreferencesService::renderTemplate( 'design:mailpreferences/parts/signup.tpl',
            array( 'categories' => MailPreferencesPage::signupCategories( ( function () { $_POST = array(); return eZHTTPTool::instance(); } )() ) ) );
        $this->assertNotNull( $r );
        $this->assertGreaterThan( 0, substr_count( $r['body'], 'name="MailPreferenceCategory[]"' ) );
        $this->assertStringNotContainsString( 'checked', $r['body'] );
    }

    public function testSignupStoresOnlyWhatWasTicked()
    {
        $_POST = array( 'MailPreferenceSignupShown' => '1' );
        $this->assertSame( 0, MailPreferencesPage::storeSignup( self::$user, eZHTTPTool::instance() ) );
        $user = expMailRecipient::fromUser( self::$user );
        $this->assertCount( 0, expMailPreferenceRow::fetchForKey( $user->key() ), 'nothing ticked: nothing stored' );
        $this->assertCount( 0, expConsentLog::fetchForRecipient( $user ) );
        // a forged essential category or an unknown one is ignored
        $_POST = array( 'MailPreferenceSignupShown' => '1', 'MailPreferenceCategory' => array( 'content', 'security', 'mplawnothing' ) );
        $this->assertSame( 1, MailPreferencesPage::storeSignup( self::$user, eZHTTPTool::instance() ) );
        $rows = expConsentLog::fetchForRecipient( $user );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'signup', $rows[0]->attribute( 'source' ) );
        $this->assertSame( 'on', $rows[0]->attribute( 'action' ) );
        $category = expMailCategoryRegistry::instance()->get( 'content' );
        $this->assertSame( MailPreferencesPage::tr( 'E-mail from us (optional)' ) . ': ' . MailPreferencesPage::categoryWording( $category ),
                           $rows[0]->attribute( 'wording' ), 'the text the form showed' );
    }

    public function testMarketingNeedsAConfirmedOptIn()
    {
        foreach ( array( 'newsletter', 'marketing' ) as $id )
            $this->assertTrue( expMailCategoryRegistry::instance()->get( $id )->doubleOptIn, $id );
        $_POST = array( 'MailPreferenceSignupShown' => '1', 'MailPreferenceCategory' => array( 'marketing' ) );
        $this->assertSame( 1, MailPreferencesPage::storeSignup( self::$user, eZHTTPTool::instance() ) );
        $user = expMailRecipient::fromUser( self::$user );
        $p = expMailPreferences::forRecipient( $user );
        $this->assertSame( 'pending', $p->state( 'marketing' ) );
        $this->assertFalse( $p->allows( 'marketing' ) );
        $mails = $this->mailsTo( self::address( 'user' ) );
        $this->assertCount( 1, $mails, 'the confirmation mail' );
        $this->assertSame( 'security', $this->headerOf( $mails[0], 'X-Exp-Mail-Category' ) );
        // no marketing mail until confirmed
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'user' ) ), 'marketing' ) ) );
        $this->assertCount( 1, $this->mailsTo( self::address( 'user' ) ) );
        $this->assertSame( array( 'pending' ), expMailGate::lastResult()['blocked'] );
        $this->assertSame( 1, preg_match( '#/mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', $mails[0], $m ) );
        $this->assertSame( 'confirmed', expMailPreferencesService::confirm( $m[1], $this->context( 'confirm', 'MPLAW yes' ) )['result'] );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'user' ) ), 'marketing' ) ) );
        $this->assertCount( 2, $this->mailsTo( self::address( 'user' ) ) );
        $actions = array_map( function ( $r ) { return $r->attribute( 'action' ) . ':' . $r->attribute( 'source' ); }, expConsentLog::fetchForRecipient( $user ) );
        $this->assertSame( array( 'confirm:confirm', 'pending:signup' ), $actions, 'the request and the confirmation are both on record' );
    }

    public function testConsentIsSpecific()
    {
        $p = expMailPreferences::forRecipient( $this->recipient( 'h' ) );
        $p->set( 'content', true, $this->context() );
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
            if ( $id !== 'content' && !$c->handlerClass )
                $this->assertSame( 'off', $p->state( $id ), "$id stays off" );
        $before = array();
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
            $before[$id] = $p->state( $id );
        $p->setMaster( false, $this->context() );
        $p->setMaster( true, $this->context() );
        $after = array();
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
            $after[$id] = $p->state( $id );
        $this->assertSame( $before, $after, 'the main switch turns no category on or off' );
    }

    public function testWithdrawalIsAsEasyAsConsent()
    {
        $prefs = expMailPreferences::forRecipient( $this->recipient( 'a' ) );
        $shown = array_keys( expMailCategoryRegistry::instance()->optional() );
        // on: one post
        $notices = $this->post( $prefs, array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => $shown, 'Category' => array( 'content' => '1' ) ) );
        $this->assertSame( 'success', $notices[0]['type'] );
        $this->assertTrue( $prefs->isOn( 'content' ) );
        // off: one post, the same form, nothing to type, no second page
        $notices = $this->post( $prefs, array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => $shown ) );
        $this->assertSame( 'success', $notices[0]['type'] );
        $this->assertFalse( $prefs->isOn( 'content' ) );
        // all off: one post
        $this->post( $prefs, array( 'MailPreferencesForm' => 'master', 'MasterOffButton' => '1' ) );
        $this->assertFalse( $prefs->masterOn() );
        $rows = expConsentLog::fetchForRecipient( $this->recipient( 'a' ) );
        $this->assertSame( array( 'master_off', 'off', 'on' ), array_map( function ( $r ) { return $r->attribute( 'action' ); }, $rows ) );
        foreach ( $rows as $row )
        {
            $this->assertSame( 'page', $row->attribute( 'source' ), 'on and off are recorded the same way' );
            $this->assertNotSame( '', $row->attribute( 'wording' ) );
        }
    }

    public function testThePageHasNoDarkPattern()
    {
        $prefs = expMailPreferences::forRecipient( $this->recipient( 'b' ) );
        $vars = MailPreferencesPage::templateVariables( $prefs, 'token', 'mailpreferences/manage/x', false );
        $r = expMailPreferencesService::renderTemplate( 'design:mailpreferences/parts/preferences.tpl', $vars );
        $this->assertNotNull( $r );
        $html = $r['body'];
        foreach ( expMailCategoryRegistry::instance()->optional() as $id => $c )
            $this->assertMatchesRegularExpression( '#<input[^>]+name="Category\[' . preg_quote( $id, '#' ) . '\]"#', $html, "$id has its own box" );
        $this->assertDoesNotMatchRegularExpression( '#<input[^>]+name="Category\[[^>]*checked#', $html, 'nothing ticked for a new person' );
        $this->assertStringContainsString( 'name="MasterOffButton"', $html, 'turning everything off is one button' );
        foreach ( expMailCategoryRegistry::instance()->essential() as $id => $c )
            $this->assertDoesNotMatchRegularExpression( '#name="Category\[' . preg_quote( $id, '#' ) . '\]"#', $html, "essential $id is not a box" );
        // once on, turning off again is the same box
        $prefs->set( 'content', true, $this->context() );
        $vars = MailPreferencesPage::templateVariables( $prefs, 'token', 'mailpreferences/manage/x', false );
        $html = expMailPreferencesService::renderTemplate( 'design:mailpreferences/parts/preferences.tpl', $vars )['body'];
        $this->assertMatchesRegularExpression( '#<input[^>]+name="Category\[content\]"[^>]*checked#', $html );
    }

    public function testTheLogKeepsTheTextShown()
    {
        $prefs = expMailPreferences::forRecipient( $this->recipient( 'c' ) );
        $this->post( $prefs, array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => array( 'collaboration' ),
                                    'Category' => array( 'collaboration' => '1' ), 'Frequency' => array( 'collaboration' => 'weekly' ) ) );
        $category = expMailCategoryRegistry::instance()->get( 'collaboration' );
        $rows = expConsentLog::fetchForRecipient( $this->recipient( 'c' ) );
        $byAction = array();
        foreach ( $rows as $row )
            $byAction[$row->attribute( 'action' )] = $row->attribute( 'wording' );
        $this->assertSame( MailPreferencesPage::categoryWording( $category ), $byAction['on'] );
        $this->assertStringContainsString( MailPreferencesPage::frequencyNames()['weekly'], $byAction['frequency'] );
        $this->assertStringContainsString( $category->name, $byAction['on'] );
        $this->assertStringContainsString( $category->description, $byAction['on'] );
    }

    // ------------------------------------------------------------------ LAW-16 .. LAW-20: data rights, records

    public function testRightOfAccess()
    {
        $p = expMailPreferences::forRecipient( $this->recipient( 'd' ) );
        $p->set( 'content', true, $this->context() );
        $p->set( 'newsletter', true, $this->context() );   // pending, mail to the file transport
        $export = $p->export();
        foreach ( array( 'recipient', 'master', 'categories', 'consent_log' ) as $k )
            $this->assertArrayHasKey( $k, $export );
        $states = array_column( $export['categories'], 'state', 'identifier' );
        $this->assertSame( 'pending', $states['newsletter'] );
        $this->assertSame( 'on', $states['content'] );
        foreach ( expMailCategoryRegistry::instance()->all() as $id => $c )
            $this->assertArrayHasKey( $id, $states, "$id is in the export" );
        $this->assertCount( 2, $export['consent_log'] );
        $first = $export['consent_log'][0];
        foreach ( array( 'wording', 'source', 'ip' ) as $k )
            $this->assertArrayHasKey( $k, $first, "the log in the export has $k" );
        $csv = expMailPreferences::exportToCsv( $export );
        $this->assertStringContainsString( 'category,newsletter,pending', $csv );
        $this->assertStringContainsString( 'MPLAW wording', $csv );
    }

    public function testErasureKeepsTheProofOfWithdrawal()
    {
        $r = $this->recipient( 'e' );
        $p = expMailPreferences::forRecipient( $r );
        $p->set( 'marketing', true, $this->context( 'import' ) );
        expMailPreferencesService::unsubscribeAll( $r, $this->context( 'link' ) );
        $this->assertTrue( expMailSuppression::isSuppressed( self::address( 'e' ) ) );
        $p->erase( $this->context( 'admin', 'MPLAW erasure' ) );
        $this->assertCount( 0, expConsentLog::fetchForRecipient( $r ) );
        $this->assertSame( 0, expConsentLog::countList( array( 'email' => self::address( 'e' ) ) ), 'the address is gone from the log' );
        $this->assertTrue( expMailSuppression::isSuppressed( self::address( 'e' ) ), 'the address stays blocked after erasure (only its hash is kept)' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'e' ) ), 'marketing' ) ) );
        $this->assertCount( 0, $this->mailsTo( self::address( 'e' ) ), 'no mail after erasure' );
    }

    public function testAnAdministratorsChangeIsRecordedAsSuch()
    {
        $admin = eZUser::currentUser();
        $user = expMailRecipient::fromUser( self::$user );
        $prefs = expMailPreferences::forRecipient( $user );
        $this->post( $prefs, array( 'MailPreferencesForm' => 'categories', 'CategoryShown' => array( 'system' ), 'Category' => array( 'system' => '1' ) ), 'admin' );
        $rows = expConsentLog::fetchForRecipient( $user );
        $this->assertCount( 1, $rows );
        $this->assertSame( 'admin', $rows[0]->attribute( 'source' ) );
        $this->assertSame( (int)$admin->attribute( 'contentobject_id' ), (int)$rows[0]->attribute( 'actor_user_id' ) );
        $this->assertSame( self::$userObjectID, (int)$rows[0]->attribute( 'user_id' ) );
        $this->assertSame( MailPreferencesPage::sourceNames()['admin'], MailPreferencesPage::logRow( $rows[0] )['source_name'],
                           'the person\'s history says the change was made by an administrator' );
    }

    public function testSuppressionCannotBeReadBack()
    {
        expMailSuppression::add( self::address( 'f' ), 'legal', 'MPLAW legal request' );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT * FROM expmail_suppression WHERE email_hash = \'' . $db->escapeString( expMailSuppression::hash( self::address( 'f' ) ) ) . '\'' );
        $this->assertCount( 1, $rows );
        $this->assertStringNotContainsString( 'mplaw-f', json_encode( $rows[0] ) );
        $this->assertStringNotContainsString( hash( 'sha256', self::address( 'f' ) ), json_encode( $rows[0] ), 'not a bare hash of the address' );
        $this->assertTrue( expMailSuppression::isSuppressed( strtoupper( self::address( 'f' ) ) ) );
    }

    public function testABounceOrComplaintStopsEvenRequestedMail()
    {
        foreach ( array( 'bounce' => 'g', 'complaint' => 'h' ) as $reason => $k )
        {
            expMailSuppression::add( self::address( $k ), $reason );
            $this->assertSame( 'blocked', expMailPreferencesService::requestLink( self::address( $k ) ), $reason );
            $p = expMailPreferences::forRecipient( $this->recipient( $k ) );
            $p->set( 'newsletter', true, $this->context() );
            $this->assertCount( 0, $this->mailsTo( self::address( $k ) ), "$reason: no confirmation mail" );
            $this->assertFalse( $p->allows( 'newsletter' ) );
        }
        // the person's own "unsubscribe from all" still lets a requested link through
        expMailSuppression::add( self::address( 'a' ), 'unsubscribe_all' );
        $this->assertSame( 'sent', expMailPreferencesService::requestLink( self::address( 'a' ) ) );
        $this->assertCount( 1, $this->mailsTo( self::address( 'a' ) ) );
    }

    // ------------------------------------------------------------------ LAW-21 .. LAW-24: RFC 8058

    public function testListUnsubscribeHeadersAreExact()
    {
        $this->subscribe( 'b', 'content' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'b' ) ), 'content' ) ) );
        $m = $this->mailsTo( self::address( 'b' ) )[0];
        $lu = $this->headerOf( $m, 'List-Unsubscribe' );
        $this->assertMatchesRegularExpression( '#^<https://[^<>\s,]+>$#', (string)$lu, 'one https URI in angle brackets' );
        $this->assertSame( 'List-Unsubscribe=One-Click', $this->headerOf( $m, 'List-Unsubscribe-Post' ) );
        $this->assertSame( 1, preg_match_all( '/^List-Unsubscribe:/mi', $m ) );
        $this->assertSame( 1, preg_match_all( '/^List-Unsubscribe-Post:/mi', $m ) );
    }

    public function testEachRecipientsLinkIsItsOwn()
    {
        $this->subscribe( 'c', 'content' );
        $this->subscribe( 'd', 'content' );
        $mail = new eZMail();
        $mail->setSender( 'mplaw-sender@' . self::DOMAIN );
        $mail->setReceiver( self::address( 'c' ) );
        $mail->addCc( self::address( 'd' ) );
        $mail->setSubject( 'MPLAW subject' );
        $mail->setBody( "MPLAW body\n" );
        $mail->setCategory( 'content' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $this->assertCount( 2, $this->mails() );
        foreach ( array( 'c' => 'd', 'd' => 'c' ) as $k => $other )
        {
            $mails = $this->mailsTo( self::address( $k ) );
            $this->assertCount( 1, $mails );
            $this->assertStringNotContainsString( self::address( $other ), $mails[0] );
            $token = $this->unsubscribeLinkOf( self::address( $k ) );
            $this->assertSame( self::address( $k ), expMailToken::verify( $token, 'unsubscribe' )['email'] );
        }
    }

    public function testWithoutSplittingTheFooterStillLeadsToThePreferences()
    {
        $this->setIni( 'GateSettings', 'SplitRecipients', 'disabled' );
        $this->subscribe( 'e', 'content' );
        $this->subscribe( 'f', 'content' );
        $this->assertTrue( eZMailTransport::send( $this->newMail( array( self::address( 'e' ), self::address( 'f' ) ), 'content' ) ) );
        $mails = $this->mails();
        $this->assertCount( 1, $mails, 'one mail to both' );
        $this->assertNull( $this->headerOf( $mails[0], 'List-Unsubscribe' ), 'no personal one-click link in a shared mail' );
        $this->assertStringContainsString( '/mailpreferences/request', $mails[0], 'the footer links to "send me a link"' );
        $this->assertStringContainsString( 'MPLAW Street 1, 12345 Town', $mails[0] );
    }

    public function testOneClickOverHttp()
    {
        $base = expMailToken::baseURL();
        if ( stripos( $base, 'https://' ) !== 0 || getenv( 'EXP_TEST_NO_WEB' ) )
            $this->markTestSkipped( 'no web address of the installation' );
        $r = $this->recipient( 'http' );
        expMailPreferences::forRecipient( $r )->set( 'content', true, $this->context( 'import' ) );
        $token = expMailToken::create( $r, 'unsubscribe', 'content' );
        $url = $base . '/mailpreferences/unsubscribe/' . $token;
        $request = function ( $method, $body = null ) use ( $url ) {
            $opts = array( 'http' => array( 'method' => $method, 'ignore_errors' => true, 'timeout' => 20, 'follow_location' => 0,
                                            'header' => "User-Agent: MPLAW compliance test\r\nContent-Type: application/x-www-form-urlencoded\r\n" ),
                           'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false ) );
            if ( $body !== null )
                $opts['http']['content'] = $body;
            $response = @file_get_contents( $url, false, stream_context_create( $opts ) );
            $headers = isset( $http_response_header ) ? $http_response_header : array();
            return array( $response, $headers );
        };
        list( $page, $headers ) = $request( 'GET' );
        if ( $page === false || !$headers )
            $this->markTestSkipped( 'the web server of the installation cannot be reached from here' );
        if ( !preg_match( '#^HTTP/\S+ 200#', $headers[0] ) )
            $this->markTestSkipped( 'the web server does not serve mailpreferences/unsubscribe (yet): ' . $headers[0] );
        $this->assertStringContainsString( 'UnsubscribeButton', $page, 'GET shows the button' );
        expMailPreferences::forRecipient( $r )->reload();
        $this->assertTrue( expMailPreferences::forRecipient( $r )->isOn( 'content' ), 'GET changes nothing (mail scanners open links)' );
        list( $answer, $headers ) = $request( 'POST', 'List-Unsubscribe=One-Click' );
        $this->assertMatchesRegularExpression( '#^HTTP/\S+ 200#', $headers[0], (string)$answer );
        $this->assertMatchesRegularExpression( '#^Content-Type:\s*text/plain#mi', implode( "\n", $headers ) );
        $this->assertMatchesRegularExpression( '#^Cache-Control:.*no-store#mi', implode( "\n", $headers ) );
        if ( class_exists( 'eZDBQueryCache' ) )
            eZDBQueryCache::clearAll();
        $this->assertFalse( expMailPreferences::forRecipient( $r )->isOn( 'content' ), 'the POST unsubscribed' );
        $rows = expConsentLog::fetchForRecipient( $r );
        $this->assertSame( 'off', $rows[0]->attribute( 'action' ) );
        $this->assertSame( 'link', $rows[0]->attribute( 'source' ) );
        $this->assertStringContainsString( 'RFC 8058', $rows[0]->attribute( 'wording' ) );
        // a broken link answers 400
        $bad = substr( $token, 0, -4 ) . ( substr( $token, -4 ) === 'AAAA' ? 'BBBB' : 'AAAA' );
        $opts = array( 'http' => array( 'method' => 'POST', 'ignore_errors' => true, 'timeout' => 20, 'content' => 'List-Unsubscribe=One-Click',
                                        'header' => "Content-Type: application/x-www-form-urlencoded\r\n" ),
                       'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false ) );
        @file_get_contents( $base . '/mailpreferences/unsubscribe/' . $bad, false, stream_context_create( $opts ) );
        $this->assertMatchesRegularExpression( '#^HTTP/\S+ 400#', $http_response_header[0] );
    }
}
