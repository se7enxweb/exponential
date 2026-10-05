<?php
/**
 * The e-mail preferences after the review of 2026-10-05: the answers that end a request on every web server, the
 * history of an address change, the public site name in mail sent from the console, and the gaps of the law
 * checklist (doc/specifications/6.0/mail-preferences-compliance.md, "Gaps") that were closed.
 *
 * Live style, like MailPreferencesCoreTest: the tests run on the installation they find (no test database), with
 * addresses on mph.invalid and one test user (remote id mph-user) that are removed again; without an installation
 * (CI) they are skipped.
 *
 * NO MAIL LEAVES THE SERVER. The mail transport is forced to "file" in the process's own ini (never written to a
 * settings file); setUpBeforeClass() and setUp() refuse to run otherwise. Every address is on the reserved .invalid
 * domain. MPH-09 talks to the web servers of the installation (the site's address and, when it is set up, the HTTPS
 * port of Velocity): it posts an unsubscribe and downloads a person's data, which sends nothing.
 *
 *  MPH-01  discardOutput() / eZExecution::discardOutputBuffers() end at a buffer that cannot be removed (a persistent
 *          worker's): they empty it and return; no view of the kernel ends its output with an unbounded loop
 *  MPH-02  The history shows a pending e-mail address change as an address change, not as "All optional e-mail"
 *  MPH-03  Mail sent from the console names the public site, never an administration siteaccess; OrganisationName wins
 *  MPH-04  DefaultOn=true of an optional category is refused and shown on the status page, unless AllowDefaultOn=enabled
 *  MPH-05  Removing an account erases its preferences and pending confirmations and anonymises its consent log;
 *          the erase is kept as proof, the suppression list stays
 *  MPH-06  Optional mail with an empty From gets the site's sender (logged); without one it is not sent
 *  MPH-07  The privacy notice: [FooterSettings] PrivacyURL in the text and HTML footer and on the preference page
 *  MPH-08  Unsubscribe and manage links never live shorter than 60 days; the status page warns about the setting
 *  MPH-09  Over HTTP, on the site's web server and on Velocity: the one-click POST answers 200, a broken link 400,
 *          and the JSON and CSV downloads arrive complete
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

use Exponential\Service\MailPreferencesPage;

class MailPreferencesHardeningTest extends PHPUnit\Framework\TestCase
{
    const DOMAIN = 'mph.invalid';

    private static $installation;
    private static $mailDir;
    private static $logFile;
    private static $iniBackup = array();
    private static $accessBackup = null;

    public static function setUpBeforeClass(): void
    {
        self::$installation = dirname( __DIR__, 5 );
        chdir( self::$installation );
        ezpLiveInstallation::requireOrSkip();
        if ( !expMailPreferencesService::tableExists( 'expmail_preference' ) )
            self::markTestSkipped( 'needs the expmail_* tables (database update)' );
        self::$mailDir = 'var/tmp/mailpreferences-mail/mph-' . getmypid() . '-' . time();
        self::$logFile = self::$installation . '/var/tmp/mailpreferences-mail/mph-' . getmypid() . '-gate.jsonl';
        self::forceFileTransport();
        $admin = eZUser::fetchByName( 'admin' );
        if ( !$admin )
            self::markTestSkipped( 'needs the admin user' );
        eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
        self::removeUser();
        self::clean();
    }

    public static function tearDownAfterClass(): void
    {
        if ( self::$installation === null )
            return;
        chdir( self::$installation );
        self::forceFileTransport();
        self::removeUser();
        self::clean();
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
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationName', '' );
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationAddress', 'MPH Street 1, 12345 Town' );
    }

    protected function tearDown(): void
    {
        foreach ( self::$iniBackup as $key => $value )
        {
            list( $file, $block, $var ) = explode( '|', $key );
            eZINI::instance( $file )->setVariable( $block, $var, $value );
        }
        self::$iniBackup = array();
        if ( self::$accessBackup !== null )
        {
            $GLOBALS['eZCurrentAccess'] = self::$accessBackup;
            self::$accessBackup = null;
        }
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

    private function setIni( $file, $block, $var, $value )
    {
        $ini = eZINI::instance( $file );
        $key = "$file|$block|$var";
        if ( !array_key_exists( $key, self::$iniBackup ) )
            self::$iniBackup[$key] = $ini->hasVariable( $block, $var ) ? $ini->variable( $block, $var ) : '';
        $ini->setVariable( $block, $var, $value );
    }

    private static function address( $key )
    {
        return 'mph-' . $key . '@' . self::DOMAIN;
    }

    private static function addresses()
    {
        $out = array();
        foreach ( array( 'a', 'b', 'c', 'from', 'http', 'user' ) as $k )
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
        $anon = array();
        foreach ( $keys as $k )
            $anon[] = 'x:' . substr( hash( 'sha256', $k . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $in = function ( array $values ) use ( $db ) {
            return "'" . implode( "','", array_map( array( $db, 'escapeString' ), $values ) ) . "'";
        };
        $db->query( 'DELETE FROM expmail_preference WHERE recipient_key IN (' . $in( $keys ) . ')' );
        $db->query( 'DELETE FROM expmail_pending WHERE recipient_key IN (' . $in( $keys ) . ')' );
        $db->query( 'DELETE FROM expmail_consent_log WHERE recipient_key IN (' . $in( array_merge( $keys, $anon ) ) . ") OR email LIKE '%@" . self::DOMAIN . "' OR wording LIKE 'MPH %'" );
        $db->query( 'DELETE FROM expmail_suppression WHERE email_hash IN (' . $in( $hashes ) . ')' );
    }

    private static function makeUser()
    {
        $login = 'mph-user';
        $password = bin2hex( random_bytes( 16 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . self::address( 'user' ) . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        $admin = eZUser::fetchByName( 'admin' );
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => $admin->attribute( 'contentobject_id' ),
            'remote_id' => 'mph-user', 'attributes' => array( 'first_name' => 'MPH', 'last_name' => 'User', 'user_account' => $account ) ) );
        if ( !$object )
            throw new RuntimeException( 'MPH: the test user could not be created' );
        eZUser::cleanupCache();
        return eZUser::fetch( (int)$object->attribute( 'id' ) );
    }

    /** removes the test user and every row of it, the anonymised ones too */
    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( 'mph-user' );
        if ( !$object )
            return;
        $id = (int)$object->attribute( 'id' );
        $anon = 'x:' . substr( hash( 'sha256', 'u:' . $id . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $object->purge();
        $db = eZDB::instance();
        $db->query( "DELETE FROM expmail_preference WHERE recipient_key = 'u:$id'" );
        $db->query( "DELETE FROM expmail_pending WHERE recipient_key = 'u:$id'" );
        $db->query( "DELETE FROM expmail_consent_log WHERE user_id = $id OR recipient_key IN ( 'u:$id', '" . $db->escapeString( $anon ) . "' ) OR actor_user_id = $id" );
    }

    private function recipient( $key )
    {
        $r = expMailRecipient::fromAddress( self::address( $key ) );
        $this->assertNotNull( $r );
        return $r;
    }

    private function context( $source = 'page', $wording = 'MPH wording' )
    {
        return new expConsentContext( $source, $wording, '192.0.2.30', 0, 'mph' );
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

    /** @return array[] the entries of the gate log of this test */
    private function gateLog()
    {
        $out = array();
        foreach ( is_file( self::$logFile ) ? file( self::$logFile ) : array() as $line )
            if ( is_array( $e = json_decode( $line, true ) ) )
                $out[] = $e;
        return $out;
    }

    private function problemCodes()
    {
        return array_map( function ( $p ) { return $p[1]; }, expMailPreferencesService::status()['problems'] );
    }

    // ------------------------------------------------------------------ MPH-01 the end of a request

    public function testDiscardOutputStopsAtABufferThatCannotBeRemoved()
    {
        // in a process of its own: a buffer that cannot be removed stays until the process ends
        $fixture = __DIR__ . '/fixtures/discard_output_worker_buffer.php';
        $process = proc_open( array( PHP_BINARY, '-n', '-d', 'display_errors=stderr', $fixture ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        stream_set_blocking( $pipes[1], false );
        $out = '';
        $deadline = microtime( true ) + 10;
        while ( microtime( true ) < $deadline )
        {
            $out .= (string)stream_get_contents( $pipes[1] );
            $status = proc_get_status( $process );
            if ( !$status['running'] )
                break;
            usleep( 20000 );
        }
        $out .= (string)stream_get_contents( $pipes[1] );
        $status = proc_get_status( $process );
        if ( $status['running'] )
            proc_terminate( $process, 9 );
        $errors = (string)stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        $this->assertFalse( $status['running'], 'discardOutput() returned (before, the loop never ended): ' . $errors );
        $result = json_decode( $out, true );
        $this->assertIsArray( $result, $out . $errors );
        $this->assertLessThan( 1.0, $result['seconds'] );
        $this->assertTrue( $result['clean'], 'nothing printed before is left in front of the answer' );
        $this->assertSame( $result['worker_level'], $result['level'], 'the page\'s buffer is gone, the worker\'s is left' );
        $this->assertSame( 0, $result['length'], 'and emptied' );
        $this->assertSame( 'the answer', $result['captured'], 'the worker gets the answer alone' );

        // every answer of the module that ends the request goes through sendResponse()
        foreach ( glob( self::$installation . '/kernel/private/classes/views/mailpreferences/*.php' ) as $file )
        {
            $code = (string)file_get_contents( $file );
            $this->assertDoesNotMatchRegularExpression( '/while\s*\(\s*ob_get_level\(\)\s*>\s*0\s*\)/', $code, basename( $file ) );
            if ( strpos( $code, 'cleanExit' ) !== false )
                $this->fail( basename( $file ) . ' ends the request itself; it must use MailPreferencesPage::sendResponse()' );
        }
        // nor does any other view of the kernel that answers with its own body
        foreach ( glob( self::$installation . '/kernel/private/classes/views/*/*.php' ) as $file )
            $this->assertDoesNotMatchRegularExpression( '/while\s*\(\s*ob_get_level\(\)\s*>\s*0\s*\)\s*\n\s*ob_end_clean/', (string)file_get_contents( $file ),
                                                        basename( dirname( $file ) ) . '/' . basename( $file ) . ': use eZExecution::discardOutputBuffers()' );
        $page = (string)file_get_contents( self::$installation . '/kernel/private/classes/services/mailpreferencespage.php' );
        $this->assertSame( 1, substr_count( $page, '\eZExecution::cleanExit()' ), 'one place ends the request' );
    }

    // ------------------------------------------------------------------ MPH-02 the history

    public function testThePendingAddressChangeIsShownAsAnAddressChange()
    {
        $r = $this->recipient( 'a' );
        expConsentLog::record( $r, '', 'pending', '', 'email_change', $this->context( 'page', 'MPH new address' ) );
        expConsentLog::record( $r, '', 'email_change', '', '', $this->context( 'confirm', 'MPH confirmed address' ) );
        expConsentLog::record( $r, '', 'master_off', 'on', 'off', $this->context( 'page', 'MPH master off' ) );
        $rows = array();
        foreach ( expConsentLog::fetchForRecipient( $r ) as $row )
            $rows[$row->attribute( 'action' )] = MailPreferencesPage::logRow( $row );
        $this->assertSame( MailPreferencesPage::tr( 'E-mail address' ), $rows['pending']['category'] );
        $this->assertSame( MailPreferencesPage::tr( 'New address asked for, waiting for confirmation' ), $rows['pending']['change'] );
        $this->assertSame( MailPreferencesPage::tr( 'E-mail address' ), $rows['email_change']['category'] );
        $this->assertSame( MailPreferencesPage::actionNames()['email_change'], $rows['email_change']['change'] );
        $this->assertSame( MailPreferencesPage::tr( 'All optional e-mail' ), $rows['master_off']['category'], 'the main switch is still the main switch' );
        // a category waiting for its confirmation keeps its own name
        expConsentLog::record( $r, 'newsletter', 'pending', 'off', 'pending', $this->context() );
        foreach ( expConsentLog::fetchForRecipient( $r ) as $row )
            if ( $row->attribute( 'category' ) === 'newsletter' )
                $this->assertSame( MailPreferencesPage::actionNames()['pending'], MailPreferencesPage::logRow( $row )['change'] );
    }

    // ------------------------------------------------------------------ MPH-03 the site name

    public function testMailFromTheConsoleNamesThePublicSite()
    {
        $site = eZINI::instance();
        $default = trim( (string)$site->variable( 'SiteSettings', 'DefaultAccess' ) );
        $adminNames = array();
        $admins = array();
        foreach ( (array)$site->variable( 'SiteSettings', 'SiteList' ) as $access )
        {
            $ini = eZSiteAccess::getIni( $access, 'site.ini' );
            if ( $ini->hasVariable( 'SiteAccessSettings', 'RequireUserLogin' ) && $ini->variable( 'SiteAccessSettings', 'RequireUserLogin' ) === 'true' )
            {
                $admins[] = $access;
                $adminNames[] = trim( (string)$ini->variable( 'SiteSettings', 'SiteName' ) );
            }
        }
        $publicName = trim( (string)eZSiteAccess::getIni( $default, 'site.ini' )->variable( 'SiteSettings', 'SiteName' ) );
        if ( !$admins || $publicName === '' || in_array( $publicName, $adminNames, true ) )
            $this->markTestSkipped( 'needs an administration siteaccess with a name of its own besides the public one' );

        self::$accessBackup = isset( $GLOBALS['eZCurrentAccess'] ) ? $GLOBALS['eZCurrentAccess'] : array();
        $GLOBALS['eZCurrentAccess'] = array_merge( (array)self::$accessBackup, array( 'name' => $admins[0] ) );
        $this->assertSame( $publicName, expMailSenderDetails::siteName(), 'a command run with an administration siteaccess' );
        $this->assertSame( $publicName, expMailSenderDetails::siteName( $admins[0] ), 'an administration siteaccess as the one the mail is about' );
        $this->assertSame( $publicName, expMailSenderDetails::get()['name'] );

        $category = expMailCategoryRegistry::instance()->get( 'content' );
        $links = array( 'manage' => 'https://' . self::DOMAIN . '/m', 'unsubscribe' => 'https://' . self::DOMAIN . '/u' );
        foreach ( array( false, true ) as $html )
        {
            $footer = expMailGate::footer( $category, $links, $html );
            $this->assertStringContainsString( $html ? htmlspecialchars( $publicName ) : $publicName, $footer );
            foreach ( $adminNames as $name )
                $this->assertStringNotContainsString( ' ' . $name . '.', $footer, 'never the administration siteaccess' );
        }
        // the confirmation and "send me a link" mails render with the same name
        $rendered = expMailPreferencesService::renderTemplate( 'design:mailpreferences/mail/link.tpl',
                                                               array( 'email' => self::address( 'a' ), 'manage_url' => $links['manage'], 'expires' => time() + 3600 ) );
        if ( $rendered !== null )
            $this->assertStringContainsString( $publicName, (string)$rendered['subject'] );

        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationName', 'MPH Organisation' );
        $this->assertSame( 'MPH Organisation', expMailSenderDetails::get()['name'], 'OrganisationName is used when set' );
        $this->assertStringContainsString( 'MPH Organisation', expMailGate::footer( $category, $links, false ) );
    }

    // ------------------------------------------------------------------ MPH-04 DefaultOn

    public function testDefaultOnIsRefusedUnlessAllowed()
    {
        $this->setIni( 'mailpreferences.ini', 'CategorySettings', 'AllowDefaultOn', 'disabled' );
        $registry = expMailCategoryRegistry::instance();
        $registry->register( new expMailCategory( 'mphdefault', array( 'name' => 'MPH default on', 'defaultOn' => true, 'source' => 'extension' ) ) );
        $this->assertFalse( $registry->get( 'mphdefault' )->defaultOn, 'treated as off' );
        $this->assertArrayHasKey( 'mphdefault', $registry->refusedDefaultOn() );
        $prefs = expMailPreferences::forRecipient( $this->recipient( 'b' ) );
        $this->assertSame( expMailPreferences::OFF, $prefs->state( 'mphdefault' ), 'a new person has it off' );
        $this->assertContains( 'default_on_refused', $this->problemCodes() );
        $problem = null;
        foreach ( expMailPreferencesService::status()['problems'] as $p )
            if ( $p[1] === 'default_on_refused' )
                $problem = $p;
        $this->assertSame( 'warning', $problem[0] );
        $this->assertStringContainsString( 'mphdefault', expMailPreferencesService::problemText( $problem ) );

        // where the law allows opt-out, the site can say so
        $this->setIni( 'mailpreferences.ini', 'CategorySettings', 'AllowDefaultOn', 'enabled' );
        expMailCategoryRegistry::reset();
        $this->assertTrue( $registry->get( 'mphdefault' )->defaultOn );
        $this->assertSame( array(), $registry->refusedDefaultOn() );
        $this->assertSame( expMailPreferences::ON, expMailPreferences::forRecipient( $this->recipient( 'b' ) )->state( 'mphdefault' ) );
        $codes = $this->problemCodes();
        $this->assertNotContains( 'default_on_refused', $codes );
        $this->assertContains( 'default_on_allowed', $codes );
        // essential categories are untouched either way
        $this->assertFalse( $registry->get( 'security' )->defaultOn );
    }

    // ------------------------------------------------------------------ MPH-05 account removal

    public function testRemovingAnAccountErasesItsPreferences()
    {
        $user = self::makeUser();
        $this->assertInstanceOf( 'eZUser', $user );
        $id = (int)$user->attribute( 'contentobject_id' );
        $r = expMailRecipient::fromUser( $user );
        $prefs = expMailPreferences::forRecipient( $r );
        $prefs->set( 'content', true, $this->context( 'page', 'MPH content on' ) );
        $prefs->set( 'newsletter', true, $this->context( 'page', 'MPH newsletter on' ) );   // pending, mail to the file transport
        $prefs->set( 'content', false, $this->context( 'link', 'MPH content off' ) );
        expMailSuppression::add( self::address( 'user' ), 'complaint' );
        $db = eZDB::instance();
        $count = function ( $sql ) use ( $db ) { $r = $db->arrayQuery( $sql ); return (int)$r[0]['c']; };
        $this->assertGreaterThan( 0, $count( "SELECT COUNT(*) AS c FROM expmail_preference WHERE recipient_key = 'u:$id'" ) );
        $this->assertGreaterThan( 0, $count( "SELECT COUNT(*) AS c FROM expmail_pending WHERE recipient_key = 'u:$id'" ) );
        $logged = $count( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE user_id = $id" );
        $this->assertGreaterThanOrEqual( 3, $logged );

        eZContentObject::fetch( $id )->purge();
        eZUser::cleanupCache();

        $this->assertSame( 0, $count( "SELECT COUNT(*) AS c FROM expmail_preference WHERE recipient_key = 'u:$id' OR user_id = $id" ), 'preferences erased' );
        $this->assertSame( 0, $count( "SELECT COUNT(*) AS c FROM expmail_pending WHERE recipient_key = 'u:$id'" ), 'confirmations erased' );
        $this->assertSame( 0, $count( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE user_id = $id OR recipient_key = 'u:$id'" ), 'no record names the account' );
        $this->assertSame( 0, expConsentLog::countList( array( 'email' => self::address( 'user' ) ) ), 'no record holds the address' );
        $anon = 'x:' . substr( hash( 'sha256', 'u:' . $id . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        $rows = $db->arrayQuery( "SELECT action, wording, anonymised, ip, email FROM expmail_consent_log WHERE recipient_key = '" . $db->escapeString( $anon ) . "' ORDER BY id" );
        $this->assertCount( $logged + 1, $rows, 'every record is kept, anonymised, and the erase is recorded' );
        $actions = array_column( $rows, 'action' );
        $this->assertContains( 'off', $actions, 'the withdrawal is kept as proof' );
        $this->assertSame( 'erase', end( $actions ) );
        foreach ( $rows as $row )
        {
            $this->assertSame( 1, (int)$row['anonymised'] );
            $this->assertSame( '', (string)$row['ip'] );
            $this->assertSame( '', (string)$row['email'] );
        }
        $this->assertTrue( expMailSuppression::isSuppressed( self::address( 'user' ) ), 'the suppression list keeps blocking the address' );
        $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '" . $db->escapeString( $anon ) . "'" );
    }

    public function testRemovingAnAccountWithoutPreferencesWritesNothing()
    {
        $user = self::makeUser();
        $id = (int)$user->attribute( 'contentobject_id' );
        $anon = 'x:' . substr( hash( 'sha256', 'u:' . $id . expMailSecret::derive( 'anonymise' ) ), 0, 32 );
        eZContentObject::fetch( $id )->purge();
        $db = eZDB::instance();
        $r = $db->arrayQuery( "SELECT COUNT(*) AS c FROM expmail_consent_log WHERE recipient_key IN ( 'u:$id', '" . $db->escapeString( $anon ) . "' )" );
        $this->assertSame( 0, (int)$r[0]['c'], 'nothing to erase, nothing recorded' );
    }

    // ------------------------------------------------------------------ MPH-06 the sender

    public function testOptionalMailWithoutASenderGetsTheSitesSender()
    {
        $this->setIni( 'site.ini', 'MailSettings', 'EmailSender', 'mph-site-sender@' . self::DOMAIN );
        expMailPreferences::forRecipient( $this->recipient( 'from' ) )->set( 'content', true, $this->context( 'import' ) );
        $mail = new eZMail();
        $mail->addReceiver( self::address( 'from' ) );
        $mail->setSubject( 'MPH no sender' );
        $mail->setBody( "MPH body\n" );
        $mail->setCategory( 'content' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $mails = $this->mailsTo( self::address( 'from' ) );
        $this->assertCount( 1, $mails );
        $this->assertMatchesRegularExpression( '/^From:.*mph-site-sender@mph\.invalid/mi', $mails[0] );
        $decisions = array_column( $this->gateLog(), 'd' );
        $this->assertContains( 'from_fallback', $decisions, 'the gate log notes it' );
        $this->assertSame( 1, expMailGate::stats( time() - 60 )['from_fallback'] );
        $this->assertContains( 'from_fallback', $this->problemCodes() );

        // a mail that names its sender is left as it is
        @unlink( self::$logFile );
        $mail = new eZMail();
        $mail->setSender( 'mph-own@' . self::DOMAIN );
        $mail->addReceiver( self::address( 'from' ) );
        $mail->setSubject( 'MPH own sender' );
        $mail->setBody( "MPH body\n" );
        $mail->setCategory( 'content' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $this->assertNotContains( 'from_fallback', array_column( $this->gateLog(), 'd' ) );

        // no sender and none of the site: not sent
        $this->setIni( 'site.ini', 'MailSettings', 'EmailSender', '' );
        $this->setIni( 'site.ini', 'MailSettings', 'AdminEmail', '' );
        foreach ( glob( self::$mailDir . '/*.mail' ) ?: array() as $f )
            @unlink( $f );
        $mail = new eZMail();
        $mail->addReceiver( self::address( 'from' ) );
        $mail->setSubject( 'MPH nobody sends' );
        $mail->setBody( "MPH body\n" );
        $mail->setCategory( 'content' );
        $this->assertFalse( eZMailTransport::send( $mail ) );
        $this->assertCount( 0, $this->mails() );
        $this->assertContains( 'no_sender', expMailGate::lastResult()['blocked'] );
    }

    // ------------------------------------------------------------------ MPH-07 the privacy notice

    public function testThePrivacyNoticeIsLinked()
    {
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'PrivacyURL', 'https://privacy.' . self::DOMAIN . '/notice' );
        $category = expMailCategoryRegistry::instance()->get( 'content' );
        $links = array( 'manage' => 'https://' . self::DOMAIN . '/m', 'unsubscribe' => 'https://' . self::DOMAIN . '/u' );
        $text = expMailGate::footer( $category, $links, false );
        $this->assertMatchesRegularExpression( '#https://privacy\.mph\.invalid/notice\r?\n#', $text, 'the link ends its line' );
        $this->assertStringContainsString( 'href="https://privacy.mph.invalid/notice"', expMailGate::footer( $category, $links, true ) );
        $vars = MailPreferencesPage::templateVariables( expMailPreferences::forRecipient( $this->recipient( 'c' ) ), 'token', 'x', false );
        $this->assertSame( 'https://privacy.mph.invalid/notice', $vars['privacy_url'] );
        $this->assertNotContains( 'privacy_missing', $this->problemCodes() );

        // a path of the site
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'PrivacyURL', '/privacy' );
        $this->assertSame( expMailToken::baseURL() . '/privacy', expMailSenderDetails::privacyURL() );

        // empty: the privacy node of the public siteaccess, else nothing (and a notice)
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'PrivacyURL', '' );
        $url = expMailSenderDetails::privacyURL();
        if ( $url === '' )
        {
            $this->assertContains( 'privacy_missing', $this->problemCodes() );
            $this->assertStringNotContainsString( 'privacy', strtolower( expMailGate::footer( $category, $links, false ) ) );
        }
        else
        {
            $this->assertStringStartsWith( expMailToken::baseURL() . '/', $url );
            $this->assertStringContainsString( $url, expMailGate::footer( $category, $links, false ) );
        }
    }

    // ------------------------------------------------------------------ MPH-08 the lifetime of links

    public function testLinksNeverLiveShorterThanSixtyDays()
    {
        $this->setIni( 'mailpreferences.ini', 'TokenSettings', 'TTL', array( 'unsubscribe' => '86400', 'manage' => '0', 'confirm' => '3600' ) );
        $this->assertSame( expMailToken::MIN_LINK_TTL, expMailToken::defaultTTL( 'unsubscribe' ), 'raised to 60 days' );
        $this->assertSame( 0, expMailToken::defaultTTL( 'manage' ), '0 stays "never expires"' );
        $this->assertSame( 3600, expMailToken::defaultTTL( 'confirm' ), 'a confirmation link is not a link of a mail to stop mail' );
        $this->assertSame( array( 'unsubscribe' => 86400 ), expMailToken::belowMinimumTTL() );
        $this->assertContains( 'ttl_below_minimum', $this->problemCodes() );

        // the link in a mail works for 60 days
        expMailPreferences::forRecipient( $this->recipient( 'c' ) )->set( 'content', true, $this->context( 'import' ) );
        $mail = new eZMail();
        $mail->setSender( 'mph-sender@' . self::DOMAIN );
        $mail->addReceiver( self::address( 'c' ) );
        $mail->setSubject( 'MPH ttl' );
        $mail->setBody( "MPH body\n" );
        $mail->setCategory( 'content' );
        $this->assertTrue( eZMailTransport::send( $mail ) );
        $m = $this->mailsTo( self::address( 'c' ) );
        $this->assertCount( 1, $m );
        $this->assertSame( 1, preg_match( '#^List-Unsubscribe:\s*<https://[^>]+/mailpreferences/unsubscribe/(m1[A-Za-z0-9_-]+)>#mi', $m[0], $match ) );
        $payload = expMailToken::verify( $match[1], 'unsubscribe' );
        $this->assertGreaterThanOrEqual( time() + expMailToken::MIN_LINK_TTL - 60, (int)$payload['expires'] );
        $this->assertNotNull( expMailToken::decode( $match[1], time() + 59 * 86400 ), 'still works after 59 days' );

        $this->setIni( 'mailpreferences.ini', 'TokenSettings', 'TTL', array( 'unsubscribe' => '0', 'manage' => '0', 'confirm' => '604800' ) );
        $this->assertSame( array(), expMailToken::belowMinimumTTL() );
        $this->assertNotContains( 'ttl_below_minimum', $this->problemCodes() );
    }

    // ------------------------------------------------------------------ MPH-09 over HTTP, on both servers

    /** @return string[] name => base address: the site's, and Velocity's HTTPS port when it is set up */
    private static function webServers()
    {
        $base = expMailToken::baseURL();
        $out = array();
        if ( stripos( $base, 'https://' ) !== 0 || getenv( 'EXP_TEST_NO_WEB' ) )
            return $out;
        $out['site'] = $base;
        $velocity = eZINI::exists( 'velocity.ini' ) ? eZINI::instance( 'velocity.ini' ) : null;
        if ( $velocity && $velocity->hasVariable( 'ServerSettings', 'HTTPSPort' ) && ctype_digit( trim( (string)$velocity->variable( 'ServerSettings', 'HTTPSPort' ) ) ) )
        {
            $parts = parse_url( $base );
            $out['velocity'] = 'https://' . $parts['host'] . ':' . trim( (string)$velocity->variable( 'ServerSettings', 'HTTPSPort' ) )
                             . ( isset( $parts['path'] ) ? rtrim( $parts['path'], '/' ) : '' );
        }
        return $out;
    }

    /** @return array status (int, 0 when there was no answer), headers (string), body, seconds */
    private static function request( $url, $method = 'GET', $body = null )
    {
        $opts = array( 'http' => array( 'method' => $method, 'ignore_errors' => true, 'timeout' => 20, 'follow_location' => 0,
                                        'header' => "User-Agent: MPH test\r\nContent-Type: application/x-www-form-urlencoded\r\nConnection: close\r\n" ),
                       'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false ) );
        if ( $body !== null )
            $opts['http']['content'] = $body;
        $started = microtime( true );
        $answer = @file_get_contents( $url, false, stream_context_create( $opts ) );
        $headers = isset( $http_response_header ) ? $http_response_header : array();
        $status = $headers && preg_match( '#^HTTP/\S+ (\d{3})#', $headers[0], $m ) ? (int)$m[1] : 0;
        return array( 'status' => $status, 'headers' => implode( "\n", $headers ), 'body' => $answer === false ? '' : $answer,
                      'seconds' => microtime( true ) - $started );
    }

    public function testOneClickAndDownloadsAnswerOnEveryWebServer()
    {
        $servers = self::webServers();
        if ( !$servers )
            $this->markTestSkipped( 'no web address of the installation' );
        $r = $this->recipient( 'http' );
        $tested = 0;
        foreach ( $servers as $name => $base )
        {
            $probe = self::request( $base . '/mailpreferences/request' );
            if ( $probe['status'] === 0 && $name !== 'site' )
                continue;   // Velocity is not running here
            if ( $probe['status'] !== 200 )
                $this->markTestSkipped( "$name: the web server does not serve mailpreferences/request: " . $probe['status'] );
            $tested++;
            expMailPreferences::forRecipient( $r )->set( 'content', true, $this->context( 'import', 'MPH import' ) );
            $token = expMailToken::create( $r, 'unsubscribe', 'content' );

            $answer = self::request( $base . '/mailpreferences/unsubscribe/' . $token, 'POST', 'List-Unsubscribe=One-Click' );
            $this->assertSame( 200, $answer['status'], "$name: one-click answers 200 (" . round( $answer['seconds'], 1 ) . ' s)' );
            $this->assertMatchesRegularExpression( '#^Content-Type:\s*text/plain#mi', $answer['headers'], $name );
            $this->assertMatchesRegularExpression( '#^Cache-Control:.*no-store#mi', $answer['headers'], $name );
            $this->assertSame( MailPreferencesPage::tr( 'You are unsubscribed.' ) . "\n", $answer['body'], "$name: the answer alone, no page around it" );
            if ( class_exists( 'eZDBQueryCache' ) )
                eZDBQueryCache::clearAll();
            $this->assertFalse( expMailPreferences::forRecipient( $r )->isOn( 'content' ), "$name: unsubscribed" );

            $bad = self::request( $base . '/mailpreferences/unsubscribe/m1broken' . bin2hex( random_bytes( 4 ) ), 'POST', 'List-Unsubscribe=One-Click' );
            $this->assertSame( 400, $bad['status'], "$name: a broken link answers 400 (" . round( $bad['seconds'], 1 ) . ' s)' );
            $this->assertSame( MailPreferencesPage::tr( 'This link does not work any more' ) . "\n", $bad['body'], $name );

            $manage = expMailToken::create( $r, 'manage' );
            $json = self::request( $base . '/mailpreferences/export/json/(token)/' . $manage );
            $this->assertSame( 200, $json['status'], "$name: the JSON download (" . round( $json['seconds'], 1 ) . ' s)' );
            $this->assertMatchesRegularExpression( '#^Content-Type:\s*application/json#mi', $json['headers'], $name );
            $this->assertMatchesRegularExpression( '#^Content-Disposition:\s*attachment; filename="email-data-\d{4}-\d{2}-\d{2}\.json"#mi', $json['headers'], $name );
            if ( preg_match( '#^Content-Length:\s*(\d+)#mi', $json['headers'], $m ) )
                $this->assertSame( (int)$m[1], strlen( $json['body'] ), "$name: Content-Length is the body" );
            $data = json_decode( $json['body'], true );
            $this->assertIsArray( $data, "$name: the JSON is complete" );
            $this->assertArrayHasKey( 'consent_log', $data );
            $this->assertStringNotContainsString( '<html', $json['body'], "$name: no page after the download" );

            $csv = self::request( $base . '/mailpreferences/export/csv/(token)/' . $manage );
            $this->assertSame( 200, $csv['status'], "$name: the CSV download" );
            $this->assertMatchesRegularExpression( '#^Content-Type:\s*text/csv#mi', $csv['headers'], $name );
            $this->assertStringStartsWith( "\xEF\xBB\xBF", $csv['body'] );
            $this->assertStringContainsString( 'category,content,off', $csv['body'], "$name: the CSV is complete" );
            $this->assertStringNotContainsString( '<html', $csv['body'], "$name: no page after the download" );
        }
        $this->assertGreaterThan( 0, $tested );
    }
}
