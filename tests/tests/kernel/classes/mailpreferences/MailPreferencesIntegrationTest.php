<?php
/**
 * The e-mail preferences joined to the rest of the system: the notification categories, the essential senders,
 * the bounce reader, the cjw_newsletter bridge, the suppression list export, the sender details of the footer and
 * the installer. Live style: the tests run on the installation they find (no test database), with addresses on
 * mpint.invalid and one test user (remote id mpint-user) that are removed again; where there is no installation
 * (CI) they are skipped.
 *
 * NO MAIL LEAVES THE SERVER. The mail transport is forced to "file" in the process's own ini (never written to a
 * settings file), the newsletter transport too; setUpBeforeClass() and setUp() refuse to run otherwise. Every
 * address is on the reserved .invalid domain. No mailbox is read: the bounce reader reads the fixture messages in
 * fixtures/bounces/. Settings files are written only into var/tmp.
 *
 *  INT-01  The transports are the file transports
 *  INT-02  content and collaboration read the subtree and collaboration rules and the digest settings
 *  INT-03  The notification helpers skip a person early (category off, master off, suppressed); the rules stay
 *  INT-04  Notification mail carries its category: the gate blocks it for who switched it off
 *  INT-05  The essential senders declare security, orders and admin; the gate always sends them
 *  INT-06  Bounces: hard bounces and complaints are suppressed, soft bounces, policy failures and not-spam are not
 *  INT-07  Bounces: a dry run changes nothing; a second read adds nothing; the status file and status()['bounce']
 *  INT-08  The suppression list CSV export holds hashes, no address
 *  INT-09  Sender details: the site name stands in for an empty organisation; save() keeps the secret, mode 0640
 *  INT-10  The installer: site secret and sender details written by the create sites step; the wizard and
 *          exp:install ask for them
 *  INT-11  cjw_newsletter: the blacklist and the suppression list follow each other both ways
 *  INT-12  cjw_newsletter: subscription changes feed the consent log and the category; the lists show on the page
 *  INT-13  cjw_newsletter: an edition mail goes through the gate (blocked when the category is off or the master
 *          switch, sent with footer and List-Unsubscribe when on)
 *  INT-14  Collaboration digest: a chosen daily frequency schedules the item, the digest is collaboration mail
 *          with the item; switched off, the waiting items are dropped and no digest is sent
 *  INT-15  Address change: the account keeps its address until the new one is confirmed, the old one gets a
 *          notice; Confirm=disabled changes at once; publishing the user object takes the same way
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group mailpreferences
 */

class MailPreferencesIntegrationTest extends PHPUnit\Framework\TestCase
{
    const DOMAIN = 'mpint.invalid';
    const LIST_ID = 999999901;

    private static $installation;
    private static $tmp;
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
        self::$tmp = 'var/tmp/mailpreferences-int/run-' . getmypid() . '-' . time();
        @mkdir( self::$installation . '/' . self::$tmp, 0775, true );
        self::$mailDir = self::$tmp . '/mail';
        self::$logFile = self::$installation . '/' . self::$tmp . '/gate.jsonl';
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
        expMailBounceReader::setStatusFileForTest( null );
        expMailSenderDetails::setDirForTest( null );
        self::removeTree( self::$installation . '/' . self::$tmp );
    }

    protected function setUp(): void
    {
        chdir( self::$installation );
        self::forceFileTransport();
        expMailGate::setLogFileForTest( self::$logFile );
        expMailBounceReader::setStatusFileForTest( self::$installation . '/' . self::$tmp . '/bounces.json' );
        expMailCategoryRegistry::reset();
        self::clean();
        foreach ( glob( self::$mailDir . '/*' ) ?: array() as $f )
            @unlink( $f );
        @unlink( self::$logFile );
    }

    protected function tearDown(): void
    {
        foreach ( self::$iniBackup as $key => $value )
        {
            list( $file, $block, $var ) = explode( '/', $key );
            eZINI::instance( $file )->setVariable( $block, $var, $value );
        }
        self::$iniBackup = array();
        expMailCategoryRegistry::reset();
        expMailSecret::setForTest( null, null );
        expMailSecret::reset();
        expMailSenderDetails::setDirForTest( null );
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
        $nl = eZINI::instance( 'cjw_newsletter.ini' );
        foreach ( array( 'TransportMethodCronjob', 'TransportMethodDirectly', 'TransportMethodPreview' ) as $v )
            $nl->setVariable( 'NewsletterMailSettings', $v, 'file' );
        $nl->setVariable( 'NewsletterMailSettings', 'FileTransportMailDir', self::$mailDir );
    }

    private function setIni( $file, $block, $var, $value )
    {
        $ini = eZINI::instance( $file );
        $key = "$file/$block/$var";
        if ( !array_key_exists( $key, self::$iniBackup ) )
            self::$iniBackup[$key] = $ini->hasVariable( $block, $var ) ? $ini->variable( $block, $var ) : '';
        $ini->setVariable( $block, $var, $value );
    }

    private static function address( $key )
    {
        return 'mpint-' . $key . '@' . self::DOMAIN;
    }

    /** The addresses the tests and the bounce fixtures use. */
    private static function addresses()
    {
        $out = array();
        foreach ( array( 'a', 'b', 'c', 'nl', 'nl2', 'bl', 'sup', 'user', 'user-new', 'user-pub' ) as $k )
            $out[] = self::address( $k );
        foreach ( array( 'mpbounce-hard', 'mpbounce-soft', 'mpbounce-policy', 'mpbounce-ok', 'mpbounce-gone', 'mpbounce-nodomain',
                         'mpcomplaint', 'mpcomplaint-headers', 'mpcomplaint-notspam', 'mpbounce-holiday', 'mpbounce-notreal' ) as $k )
            $out[] = $k . '@mptest.invalid';
        return $out;
    }

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
        $db->query( 'DELETE FROM expmail_consent_log WHERE recipient_key IN (' . $in( array_merge( $keys, $anon ) ) . ")"
                    . " OR email LIKE '%@" . self::DOMAIN . "' OR email LIKE '%@mptest.invalid'" );
        $hashes = array();
        foreach ( self::addresses() as $email )
            $hashes[] = expMailSuppression::hash( $email );
        $db->query( 'DELETE FROM expmail_suppression WHERE email_hash IN (' . $in( $hashes ) . ')' );
        if ( self::cjwAvailable() )
        {
            $db->query( "DELETE FROM cjwnl_blacklist_item WHERE email LIKE '%@" . self::DOMAIN . "' OR email LIKE '%@mptest.invalid'" );
            $ids = $db->arrayQuery( "SELECT id FROM cjwnl_user WHERE email LIKE '%@" . self::DOMAIN . "'" );
            foreach ( (array)$ids as $row )
                $db->query( 'DELETE FROM cjwnl_subscription WHERE newsletter_user_id = ' . (int)$row['id'] );
            $db->query( "DELETE FROM cjwnl_user WHERE email LIKE '%@" . self::DOMAIN . "'" );
        }
        if ( self::$userObjectID )
        {
            $db->query( 'DELETE FROM ezsubtree_notification_rule WHERE user_id = ' . (int)self::$userObjectID );
            $db->query( 'DELETE FROM ezcollab_notification_rule WHERE user_id = ' . (int)self::$userObjectID );
            $db->query( 'DELETE FROM ezgeneral_digest_user_settings WHERE user_id = ' . (int)self::$userObjectID );
        }
    }

    private static function makeUser()
    {
        $login = 'mpint-user';
        $password = bin2hex( random_bytes( 16 ) );
        $type = eZUser::hashType();
        $hash = eZUser::createHash( $login, $password, eZUser::site(), $type );
        $account = $login . '|' . self::address( 'user' ) . '|' . $hash . '|' . eZUser::passwordHashTypeName( $type ) . '|1';
        $admin = eZUser::fetchByName( 'admin' );
        $object = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => 5, 'class_identifier' => 'user', 'creator_id' => $admin->attribute( 'contentobject_id' ),
            'remote_id' => 'mpint-user', 'attributes' => array( 'first_name' => 'MPINT', 'last_name' => 'User', 'user_account' => $account ) ) );
        if ( !$object )
            throw new RuntimeException( 'MPINT: the test user could not be created' );
        self::$userObjectID = (int)$object->attribute( 'id' );
        eZUser::cleanupCache();
        self::$user = eZUser::fetch( self::$userObjectID );
    }

    private static function removeUser()
    {
        $object = eZContentObject::fetchByRemoteID( 'mpint-user' );
        if ( $object )
        {
            $id = (int)$object->attribute( 'id' );
            $db = eZDB::instance();
            $db->query( "DELETE FROM expmail_preference WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_pending WHERE recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM expmail_consent_log WHERE user_id = $id OR recipient_key = 'u:$id'" );
            $db->query( "DELETE FROM ezsubtree_notification_rule WHERE user_id = $id" );
            $db->query( "DELETE FROM ezcollab_notification_rule WHERE user_id = $id" );
            $db->query( "DELETE FROM ezgeneral_digest_user_settings WHERE user_id = $id" );
            $object->purge();
        }
        self::$user = null;
    }

    private static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( scandir( $dir ) as $f )
        {
            if ( $f === '.' || $f === '..' )
                continue;
            $p = $dir . '/' . $f;
            if ( is_dir( $p ) )
                self::removeTree( $p );
            else
                @unlink( $p );
        }
        @rmdir( $dir );
    }

    private static function cjwAvailable()
    {
        return class_exists( 'CjwNewsletterMailPreferences' ) && class_exists( 'CjwNewsletterUser' )
               && expMailPreferencesService::tableExists( 'cjwnl_user' ) && expMailPreferencesService::tableExists( 'cjwnl_blacklist_item' );
    }

    private function context( $source = 'page', $wording = 'MPINT wording' )
    {
        return new expConsentContext( $source, $wording, '192.0.2.20', 0, 'mpint' );
    }

    private function userRecipient()
    {
        return expMailRecipient::fromUser( self::$user );
    }

    /** @return string[] file contents of the mail written so far */
    private function mails()
    {
        $out = array();
        $files = glob( self::$mailDir . '/*' ) ?: array();
        sort( $files );
        foreach ( $files as $f )
            if ( is_file( $f ) )
                $out[] = (string)file_get_contents( $f );
        return $out;
    }

    private function logLines()
    {
        if ( !is_file( self::$logFile ) )
            return array();
        return array_map( function ( $l ) { return json_decode( $l, true ); }, file( self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );
    }

    private function fixtures()
    {
        return __DIR__ . '/fixtures/bounces';
    }

    // ------------------------------------------------------------------ INT-01

    public function testTheTransportsAreFileTransports()
    {
        $this->assertSame( 'file', eZINI::instance()->variable( 'MailSettings', 'Transport' ) );
        $this->assertSame( 'file', eZINI::instance( 'cjw_newsletter.ini' )->variable( 'NewsletterMailSettings', 'TransportMethodCronjob' ) );
        foreach ( self::addresses() as $a )
            $this->assertStringEndsWith( '.invalid', $a );
    }

    // ------------------------------------------------------------------ INT-02

    public function testNotificationCategoriesReadTheRules()
    {
        $registry = expMailCategoryRegistry::instance();
        $this->assertSame( 'expNotificationMailCategoryHandler', $registry->get( 'content' )->handlerClass );
        $this->assertSame( 'expNotificationMailCategoryHandler', $registry->get( 'collaboration' )->handlerClass );
        $prefs = expMailPreferences::forRecipient( $this->userRecipient() );
        $this->assertSame( 'off', $prefs->state( 'content' ), 'no rule: opt-in default' );
        $this->assertSame( 'off', $prefs->state( 'collaboration' ) );

        eZSubtreeNotificationRule::create( 2, self::$userObjectID )->store();
        eZCollaborationNotificationRule::create( 'ezapprove', self::$userObjectID )->store();
        $prefs->reload();
        $this->assertSame( 'on', $prefs->state( 'content' ), 'a subtree rule reads as on' );
        $this->assertSame( 'on', $prefs->state( 'collaboration' ), 'a collaboration rule reads as on' );
        $this->assertSame( 'immediate', $prefs->frequency( 'content' ) );

        $settings = eZGeneralDigestUserSettings::create( self::$userObjectID, 1, eZGeneralDigestUserSettings::TYPE_DAILY, '', '08:00' );
        $settings->store();
        $this->assertSame( 'daily', $prefs->frequency( 'content' ) );
        $settings->setAttribute( 'digest_type', eZGeneralDigestUserSettings::TYPE_WEEKLY );
        $settings->store();
        $this->assertSame( 'weekly', $prefs->frequency( 'content' ) );

        // a choice on the page wins, and the rules stay
        $prefs->set( 'content', false, $this->context() );
        $this->assertSame( 'off', $prefs->state( 'content' ) );
        $this->assertSame( 1, (int)eZSubtreeNotificationRule::fetchListCount( self::$userObjectID ), 'the rule is kept' );
        $prefs->setFrequency( 'content', 'daily', $this->context() );
        $this->assertSame( 'daily', expNotificationMailCategoryHandler::storedFrequency( self::$userObjectID, 'content' ) );
        $this->assertSame( '', expNotificationMailCategoryHandler::storedFrequency( self::$userObjectID, 'collaboration' ) );
    }

    // ------------------------------------------------------------------ INT-03

    public function testNotificationHelpersSkipEarly()
    {
        eZSubtreeNotificationRule::create( 2, self::$userObjectID )->store();
        $this->assertTrue( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'content' ), 'a rule without a choice: sent as before' );
        $this->assertTrue( expNotificationMailCategoryHandler::allowsAddress( self::address( 'user' ), 'content' ) );

        $prefs = expMailPreferences::forRecipient( $this->userRecipient() );
        $prefs->setMaster( false, $this->context() );
        $this->assertFalse( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'content' ), 'master switch off' );
        $this->assertSame( 1, (int)eZSubtreeNotificationRule::fetchListCount( self::$userObjectID ), 'the rule is dormant, not removed' );
        $prefs->setMaster( true, $this->context() );
        $this->assertTrue( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'content' ), 'back on with the master switch' );

        $this->assertFalse( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'collaboration' ), 'no collaboration rule, no choice' );
        expMailSuppression::add( self::address( 'user' ), 'legal', 'MPINT' );
        $this->assertFalse( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'content' ), 'suppressed' );
        $this->setIni( 'mailpreferences.ini', 'GateSettings', 'Gate', 'disabled' );
        $this->assertFalse( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'content' ), 'suppressed also without the gate' );
        expMailSuppression::lift( self::address( 'user' ) );
        $this->assertTrue( expNotificationMailCategoryHandler::allowsUser( self::$userObjectID, 'collaboration' ), 'gate off: as before' );

        $this->assertSame( 'content', expNotificationMailCategoryHandler::categoryForHandler( 'ezsubtree' ) );
        $this->assertSame( 'content', expNotificationMailCategoryHandler::categoryForHandler( 'ezgeneraldigest' ) );
        $this->assertSame( 'collaboration', expNotificationMailCategoryHandler::categoryForHandler( 'ezcollaboration' ) );
        $this->assertNull( expNotificationMailCategoryHandler::categoryForHandler( 'other' ) );
    }

    // ------------------------------------------------------------------ INT-04

    public function testNotificationMailCarriesItsCategory()
    {
        expMailPreferences::forRecipient( expMailRecipient::fromAddress( self::address( 'a' ) ) )->set( 'content', true, $this->context() );
        $transport = new eZMailNotificationTransport();
        $ok = $transport->send( array( self::address( 'a' ), self::address( 'b' ) ), 'MPINT notification', "MPINT body\n", null,
                                array( 'mail_category' => 'content' ) );
        $this->assertTrue( (bool)$ok );
        $mails = $this->mails();
        $this->assertCount( 1, $mails, 'only the person who switched content on' );
        $this->assertStringContainsString( self::address( 'a' ), $mails[0] );
        $this->assertStringNotContainsString( self::address( 'b' ), $mails[0] );
        $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category:\s*content/mi', $mails[0] );
        $this->assertMatchesRegularExpression( '/^List-Unsubscribe:/mi', $mails[0] );
        $blocked = array_filter( $this->logLines(), function ( $l ) { return $l['d'] === 'blocked' && $l['c'] === 'content'; } );
        $this->assertCount( 1, $blocked );
    }

    // ------------------------------------------------------------------ INT-05

    public function testEssentialSendersDeclareTheirCategory()
    {
        $expect = array( 'kernel/private/classes/views/user/forgotpassword.php' => 'security',
                         'kernel/user/ezuseroperationcollection.php' => 'security',
                         'kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php' => 'orders',
                         'kernel/classes/audit/sinks/expauditmailsink.php' => 'admin',
                         'kernel/private/classes/views/user/register.php' => 'admin',
                         'kernel/setup/steps/ezstep_registration.php' => 'admin',
                         'kernel/classes/mailpreferences/expmailpreferencesservice.php' => 'security' );
        foreach ( $expect as $file => $category )
        {
            $code = (string)file_get_contents( self::$installation . '/' . $file );
            $sends = preg_match_all( '/MailTransport::send\(\s*\$mail\s*\)/', $code );
            $tags = preg_match_all( "/->setCategory\(\s*'" . $category . "'\s*\)/", $code );
            $this->assertGreaterThan( 0, $sends, $file );
            $this->assertSame( $sends, $tags, "$file: every send declares '$category'" );
            $this->assertTrue( expMailCategoryRegistry::instance()->get( $category )->essential, $category );
        }
        // an essential mail goes even with everything off
        $r = expMailRecipient::fromAddress( self::address( 'c' ) );
        expMailPreferences::forRecipient( $r )->setMaster( false, $this->context() );
        $mail = new eZMail();
        $mail->setSender( 'mpint-sender@' . self::DOMAIN );
        $mail->setReceiver( self::address( 'c' ) );
        $mail->setSubject( 'MPINT order' );
        $mail->setBody( "MPINT order\n" );
        $mail->setCategory( 'orders' );
        $this->assertTrue( (bool)eZMailTransport::send( $mail ) );
        $this->assertCount( 1, $this->mails() );
        $this->assertStringNotContainsString( 'List-Unsubscribe', $this->mails()[0], 'no unsubscribe on essential mail' );
    }

    // ------------------------------------------------------------------ INT-06

    public function testBounceClassification()
    {
        $kinds = array();
        foreach ( glob( $this->fixtures() . '/*.eml' ) as $file )
            $kinds[basename( $file )] = expMailBounceReader::classify( (string)file_get_contents( $file ) );
        $this->assertSame( 'hard', $kinds['dsn-hard-user-unknown.eml']['kind'] );
        $this->assertSame( array( 'mpbounce-hard@mptest.invalid' ), $kinds['dsn-hard-user-unknown.eml']['addresses'] );
        $this->assertSame( '5.1.1', $kinds['dsn-hard-user-unknown.eml']['detail'] );
        $this->assertSame( 'hard', $kinds['dsn-mixed-recipients.eml']['kind'] );
        $this->assertSame( array( 'mpbounce-gone@mptest.invalid', 'mpbounce-nodomain@mptest.invalid' ), $kinds['dsn-mixed-recipients.eml']['addresses'],
                           'the delivered recipient is not a bounce; addresses in lower case' );
        $this->assertSame( 'soft', $kinds['dsn-soft-delayed.eml']['kind'] );
        $this->assertSame( 'soft', $kinds['dsn-failed-policy.eml']['kind'], '5.7.1 is not in HardStatusCodes' );
        $this->assertSame( array(), $kinds['dsn-failed-policy.eml']['addresses'] );
        $this->assertSame( 'complaint', $kinds['arf-abuse.eml']['kind'] );
        $this->assertSame( array( 'mpcomplaint@mptest.invalid' ), $kinds['arf-abuse.eml']['addresses'] );
        $this->assertSame( 'complaint', $kinds['arf-headers-only.eml']['kind'] );
        $this->assertSame( array( 'mpcomplaint-headers@mptest.invalid' ), $kinds['arf-headers-only.eml']['addresses'], 'the To of the reported headers' );
        $this->assertSame( 'none', $kinds['arf-not-spam.eml']['kind'] );
        $this->assertSame( 'none', $kinds['plain-auto-reply.eml']['kind'], 'text that looks like a report is not one' );
        $this->assertTrue( expMailBounceReader::isHardStatus( '5.1.10' ) );
        $this->assertFalse( expMailBounceReader::isHardStatus( '4.1.1' ) );
        $this->assertFalse( expMailBounceReader::isHardStatus( '5.2.2' ) );
    }

    // ------------------------------------------------------------------ INT-07

    public function testBounceReaderSuppressesAndReports()
    {
        $dry = expMailBounceReader::runFiles( array( $this->fixtures() ), true );
        $this->assertTrue( $dry['ok'] );
        $this->assertSame( 8, $dry['counts']['messages'] );
        $this->assertSame( 0, $dry['counts']['suppressed'] );
        $this->assertFalse( expMailSuppression::isSuppressed( 'mpbounce-hard@mptest.invalid' ), 'a dry run changes nothing' );

        $run = expMailBounceReader::runFiles( array( $this->fixtures() ), false );
        $this->assertSame( array( 'messages' => 8, 'hard' => 2, 'soft' => 2, 'complaints' => 2, 'other' => 2, 'suppressed' => 5, 'errors' => 0 ), $run['counts'] );
        $this->assertSame( 'bounce', expMailSuppression::reason( 'mpbounce-hard@mptest.invalid' ) );
        $this->assertSame( 'bounce', expMailSuppression::reason( 'mpbounce-gone@mptest.invalid' ) );
        $this->assertSame( 'complaint', expMailSuppression::reason( 'mpcomplaint@mptest.invalid' ) );
        foreach ( array( 'mpbounce-soft', 'mpbounce-policy', 'mpbounce-ok', 'mpcomplaint-notspam', 'mpbounce-notreal' ) as $k )
            $this->assertFalse( expMailSuppression::isSuppressed( $k . '@mptest.invalid' ), $k );
        foreach ( $run['items'] as $item )
            foreach ( $item['addresses'] as $shown )
                $this->assertStringNotContainsString( 'mpbounce', $shown, 'addresses are masked' );
        $log = expConsentLog::fetchForRecipient( expMailRecipient::fromAddress( 'mpbounce-hard@mptest.invalid' ) );
        $this->assertSame( 'suppress', $log[0]->attribute( 'action' ) );
        $this->assertSame( 'system', $log[0]->attribute( 'source' ) );

        $again = expMailBounceReader::runFiles( array( $this->fixtures() ), false );
        $this->assertSame( 0, $again['counts']['suppressed'], 'nothing is added twice' );

        // without a mailbox the reader does nothing and says why
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Reader', 'enabled' );
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Server', '' );
        $this->assertSame( 'not_configured', expMailBounceReader::run()['skipped'] );
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Reader', 'disabled' );
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Server', 'imap.mpint.invalid' );
        $this->assertSame( 'disabled', expMailBounceReader::run()['skipped'] );
        // a server that cannot be reached: an error in the status, the status page warns
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Reader', 'enabled' );
        $this->setIni( 'mailpreferences.ini', 'BounceSettings', 'Port', '9' );
        $failed = expMailBounceReader::run();
        $this->assertFalse( $failed['ok'] );
        $status = expMailPreferencesService::status();
        $this->assertArrayHasKey( 'bounce', $status );
        $this->assertTrue( $status['bounce']['enabled'] );
        $this->assertNotSame( '', $status['bounce']['last_error'] );
        $this->assertSame( 0, $status['bounce']['last_read'] );
        $this->assertContains( 'bounce_error', array_map( function ( $p ) { return $p[1]; }, $status['problems'] ) );
        $this->assertStringNotContainsString( 'Password', json_encode( $status['bounce'] ) );

        list( $code, $out ) = $this->command( 'mailbounces', array( '--dry-run', '--file=' . $this->fixtures() ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( '8 messages: 2 hard bounces', $out );
        $this->assertStringContainsString( 'PASS', $out );
        $this->assertStringNotContainsString( 'mpbounce-hard@', $out );
    }

    /** @return array( int code, string output ) */
    private function command( $script, array $args )
    {
        $command = array_merge( array( PHP_BINARY, 'bin/php/' . $script . '.php' ), $args, $script === 'install' ? array() : array( '--allow-root-user', '--no-colors' ) );
        $process = proc_open( $command, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, self::$installation );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        return array( proc_close( $process ), $out );
    }

    // ------------------------------------------------------------------ INT-08

    public function testSuppressionCsvExport()
    {
        expMailSuppression::add( self::address( 'sup' ), 'legal', '=HYPERLINK("x") for ' . self::address( 'sup' ) );
        $csv = expMailSuppression::exportCsv();
        $this->assertStringStartsWith( 'email_hash,reason,created,created_by,note', $csv );
        $this->assertStringContainsString( expMailSuppression::hash( self::address( 'sup' ) ), $csv );
        $this->assertStringNotContainsString( self::address( 'sup' ), $csv, 'no address in the export' );
        $this->assertStringContainsString( "'=HYPERLINK", $csv, 'a formula is defused' );
        $this->assertStringContainsString( 'export_uri', (string)file_get_contents( self::$installation . '/design/standard/templates/mailpreferences/admin/suppression.tpl' ) );
    }

    // ------------------------------------------------------------------ INT-09

    public function testSenderDetails()
    {
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationName', '' );
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationAddress', '' );
        $site = expMailSenderDetails::siteName();
        $this->assertNotSame( '', $site, 'the installation has a site name' );
        $details = expMailSenderDetails::get();
        $this->assertSame( $site, $details['name'] );
        $this->assertSame( 'site', $details['name_source'] );
        $this->assertSame( $site, expMailGate::organisation()['name'] );
        $status = expMailPreferencesService::status();
        $this->assertContains( 'footer_missing', array_map( function ( $p ) { return $p[1]; }, $status['problems'] ), 'the address is missing' );

        // the footer of optional mail names the site, and the mail is sent although the address is empty
        expMailPreferences::forRecipient( expMailRecipient::fromAddress( self::address( 'a' ) ) )->set( 'content', true, $this->context() );
        $mail = new eZMail();
        $mail->setSender( 'mpint-sender@' . self::DOMAIN );
        $mail->setReceiver( self::address( 'a' ) );
        $mail->setSubject( 'MPINT footer' );
        $mail->setBody( "MPINT body\n" );
        $mail->setCategory( 'content' );
        $this->assertTrue( (bool)eZMailTransport::send( $mail ) );
        $this->assertCount( 1, $this->mails() );
        $this->assertStringContainsString( $site, quoted_printable_decode( $this->mails()[0] ) );

        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationName', 'MPINT Org' );
        $this->setIni( 'mailpreferences.ini', 'FooterSettings', 'OrganisationAddress', 'Street 1\n12345 Town' );
        $details = expMailSenderDetails::get();
        $this->assertSame( 'MPINT Org', $details['name'] );
        $this->assertSame( "Street 1\n12345 Town", $details['address'] );

        // save() into a settings directory of its own: the secret stays, the file is 0640
        $dir = self::$installation . '/' . self::$tmp . '/override';
        @mkdir( $dir, 0775, true );
        file_put_contents( $dir . '/mailpreferences.ini.append.php', "<?php /* #?ini charset=\"utf-8\"?\n\n[SecretSettings]\nTokenSecret=MPINTSECRET\n*/ ?>\n" );
        $this->assertTrue( expMailSenderDetails::save( 'MPINT Org', "Street 1\r\n\r\n12345 Town", $dir ) );
        $content = (string)file_get_contents( $dir . '/mailpreferences.ini.append.php' );
        $this->assertStringContainsString( 'TokenSecret=MPINTSECRET', $content );
        $this->assertStringContainsString( 'OrganisationName=MPINT Org', $content );
        $this->assertStringContainsString( 'OrganisationAddress=Street 1\n12345 Town', $content );
        $this->assertSame( '0640', substr( sprintf( '%o', fileperms( $dir . '/mailpreferences.ini.append.php' ) ), -4 ) );
        $this->expectException( InvalidArgumentException::class );
        expMailSenderDetails::save( 'Bad ## name', '', $dir );
    }

    // ------------------------------------------------------------------ INT-10

    public function testInstallerWritesSecretAndSenderDetails()
    {
        $dir = self::$installation . '/' . self::$tmp . '/install-override';
        @mkdir( $dir, 0775, true );
        expMailSecret::setForTest( $dir, null );
        expMailSenderDetails::setDirForTest( $dir );
        $tpl = eZTemplate::factory();
        $persistence = array();
        $step = new eZStepCreateSites( $tpl, eZHTTPTool::instance(), eZINI::instance(), $persistence );
        $step->setUpMailPreferences( array( 'title' => 'MPINT Site', 'organisation_name' => 'MPINT Site', 'organisation_address' => "Road 2\n99999 City" ) );
        $content = (string)file_get_contents( $dir . '/mailpreferences.ini.append.php' );
        $this->assertMatchesRegularExpression( '/^TokenSecret=\S{40,}$/m', $content, 'the site secret is generated at install' );
        $this->assertStringContainsString( 'OrganisationName=', $content );
        $this->assertStringNotContainsString( 'OrganisationName=MPINT Site', $content, 'the prefilled site name keeps following the site name' );
        $this->assertStringContainsString( 'OrganisationAddress=Road 2\n99999 City', $content );

        $empty = self::$installation . '/' . self::$tmp . '/install-empty';
        @mkdir( $empty, 0775, true );
        expMailSecret::setForTest( $empty, null );
        expMailSenderDetails::setDirForTest( $empty );
        $step->setUpMailPreferences( array( 'title' => 'MPINT Site', 'organisation_name' => '', 'organisation_address' => '' ) );
        $content = (string)file_get_contents( $empty . '/mailpreferences.ini.append.php' );
        $this->assertMatchesRegularExpression( '/^TokenSecret=\S{40,}$/m', $content );
        $this->assertStringNotContainsString( 'OrganisationAddress', $content, 'nothing entered: the footer settings stay empty' );

        $wizard = (string)file_get_contents( self::$installation . '/design/standard/templates/setup/init/site_details.tpl' );
        $this->assertStringContainsString( 'eZSetup_site_templates_organisation_name', $wizard );
        $this->assertStringContainsString( 'eZSetup_site_templates_organisation_address', $wizard );
        list( $code, $out ) = $this->command( 'install', array( '--help' ) );
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( '--organisation-address', $out );
        $this->assertStringContainsString( 'OrganisationAddress', (string)file_get_contents( self::$installation . '/kickstart.ini-dist' ) );
        foreach ( array( 'expmail_category', 'expmail_preference', 'expmail_consent_log', 'expmail_suppression', 'expmail_pending' ) as $table )
            $this->assertMatchesRegularExpression( "/^  '$table' => $/m", (string)file_get_contents( self::$installation . '/share/db_schema.dba' ), $table );
    }

    // ------------------------------------------------------------------ INT-11

    public function testNewsletterBlacklistAndSuppressionFollowEachOther()
    {
        if ( !self::cjwAvailable() )
            $this->markTestSkipped( 'cjw_newsletter is not active' );
        $this->assertContains( 'CjwNewsletterMailPreferences', (array)eZINI::instance( 'mailpreferences.ini' )->variable( 'SuppressionSettings', 'Listeners' ) );
        $email = self::address( 'bl' );
        $item = CjwNewsletterBlacklistItem::create( $email, 'MPINT' );
        $item->store();
        $this->assertSame( 'bridge', expMailSuppression::reason( $email ), 'blacklisted: suppressed' );
        $item->remove();
        $this->assertFalse( expMailSuppression::isSuppressed( $email ), 'off the blacklist: lifted' );

        $email = self::address( 'sup' );
        expMailSuppression::add( $email, 'complaint', 'MPINT' );
        $this->assertIsObject( CjwNewsletterBlacklistItem::fetchByEmail( $email ), 'suppressed: on the blacklist' );
        $this->assertSame( 'complaint', expMailSuppression::reason( $email ), 'the reason stays' );
        expMailSuppression::lift( expMailSuppression::hash( $email ) );
        $this->assertFalse( CjwNewsletterBlacklistItem::fetchByEmail( $email ), 'lifted by hash: off the blacklist' );
    }

    // ------------------------------------------------------------------ INT-12

    private function newsletterUser( $key )
    {
        $user = CjwNewsletterUser::create( self::address( $key ), '', 'MPINT', 'Reader', 0, CjwNewsletterUser::STATUS_CONFIRMED, 'mpint', '', '', '', '' );
        $user->store();
        return $user;
    }

    public function testNewsletterSubscriptionsFeedTheConsentLog()
    {
        if ( !self::cjwAvailable() )
            $this->markTestSkipped( 'cjw_newsletter is not active' );
        $this->assertSame( 'CjwNewsletterMailCategoryHandler', expMailCategoryRegistry::instance()->get( 'newsletter' )->handlerClass );
        $nlUser = $this->newsletterUser( 'nl' );
        $recipient = expMailRecipient::fromAddress( self::address( 'nl' ) );
        $prefs = expMailPreferences::forRecipient( $recipient );
        $this->assertSame( 'off', $prefs->state( 'newsletter' ) );

        $sub = CjwNewsletterSubscription::create( self::LIST_ID, $nlUser->attribute( 'id' ), array( 0 ), CjwNewsletterSubscription::STATUS_PENDING, 'mpint' );
        $sub->store();
        $log = expConsentLog::fetchForRecipient( $recipient );
        $this->assertSame( 'pending', $log[0]->attribute( 'action' ) );
        $this->assertSame( 'bridge', $log[0]->attribute( 'source' ) );
        $this->assertStringContainsString( '#' . self::LIST_ID, $log[0]->attribute( 'wording' ) );
        $this->assertSame( 'off', $prefs->state( 'newsletter' ), 'pending is not consent yet' );

        $sub->setAttribute( 'status', CjwNewsletterSubscription::STATUS_CONFIRMED );
        $sub->store();
        $prefs->reload();
        $this->assertSame( 'on', $prefs->state( 'newsletter' ), 'confirmed: the category is on, without a second confirmation' );
        $this->assertTrue( $prefs->allows( 'newsletter' ) );
        $rows = $this->overviewSubscriptions( $prefs );
        $this->assertCount( 1, $rows, 'the list shows on the preference page' );
        $this->assertTrue( $rows[0]['active'] );
        $this->assertStringContainsString( 'newsletter/configure/', $rows[0]['url'] );

        $sub->unsubscribe();
        $prefs->reload();
        $this->assertSame( 'off', $prefs->state( 'newsletter' ), 'the last list left: the category is off' );
        $actions = array_map( function ( $r ) { return $r->attribute( 'action' ) . ':' . $r->attribute( 'source' ); }, expConsentLog::fetchForRecipient( $recipient ) );
        $this->assertContains( 'on:bridge', $actions );
        $this->assertContains( 'off:bridge', $actions );

        // the handler reads a subscription without a stored choice as on
        $nl2 = $this->newsletterUser( 'nl2' );
        $sub2 = CjwNewsletterSubscription::create( self::LIST_ID, $nl2->attribute( 'id' ), array( 0 ), CjwNewsletterSubscription::STATUS_APPROVED, 'mpint' );
        $sub2->store();
        eZDB::instance()->query( "DELETE FROM expmail_preference WHERE recipient_key = '" . eZDB::instance()->escapeString( expMailRecipient::fromAddress( self::address( 'nl2' ) )->key() ) . "'" );
        $this->assertSame( 'on', expMailPreferences::forRecipient( expMailRecipient::fromAddress( self::address( 'nl2' ) ) )->state( 'newsletter' ) );
    }

    private function overviewSubscriptions( expMailPreferences $prefs )
    {
        $vars = Exponential\Service\MailPreferencesPage::templateVariables( $prefs, 'token', 'mailpreferences/manage/x', false );
        foreach ( $vars['categories'] as $c )
            if ( $c['identifier'] === 'newsletter' )
                return $c['subscriptions'];
        return array();
    }

    // ------------------------------------------------------------------ INT-13

    public function testNewsletterEditionGoesThroughTheGate()
    {
        if ( !self::cjwAvailable() )
            $this->markTestSkipped( 'cjw_newsletter is not active' );
        $cjwMail = new CjwNewsletterMail();
        $cjwMail->setTransportMethodCronjobFromIni();
        $cjwMail->setMailCategory( 'newsletter' );
        $send = function () use ( $cjwMail ) {
            return $cjwMail->sendEmail( 'mpint-news@' . self::DOMAIN, 'MPINT News', self::address( 'nl' ), 'Reader', 'MPINT edition',
                                        array( 'html' => '<html><body><p>MPINT edition</p></body></html>', 'text' => "MPINT edition\n" ) );
        };
        $r = $send();
        $this->assertTrue( !empty( $r['blocked'] ), 'no subscription, no choice: not sent' );
        $this->assertCount( 0, $this->mails() );

        $prefs = expMailPreferences::forRecipient( expMailRecipient::fromAddress( self::address( 'nl' ) ) );
        $prefs->set( 'newsletter', true, $this->context( 'bridge' ) );
        $r = $send();
        $this->assertTrue( $r['send_result'] === true, json_encode( $r ) );
        $mails = $this->mails();
        $this->assertCount( 1, $mails );
        $this->assertMatchesRegularExpression( '/^List-Unsubscribe-Post:\s*List-Unsubscribe=One-Click/mi', $mails[0] );
        $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category:\s*newsletter/mi', $mails[0] );
        $this->assertStringContainsString( 'mailpreferences/unsubscribe/', quoted_printable_decode( str_replace( "=\r\n", '', $mails[0] ) ) );

        $prefs->setMaster( false, $this->context() );
        $r = $send();
        $this->assertTrue( !empty( $r['blocked'] ), 'master switch off' );
        $this->assertSame( array( 'master_off' ), $r['blocked_reasons'] );
        $prefs->setMaster( true, $this->context() );
        expMailSuppression::add( self::address( 'nl' ), 'bounce', 'MPINT' );
        $r = $send();
        $this->assertSame( array( 'suppressed' ), $r['blocked_reasons'] );
        $this->assertCount( 1, $this->mails() );

        // a test mail (preview) is never gated
        $preview = $cjwMail->sendEmail( 'mpint-news@' . self::DOMAIN, 'MPINT News', self::address( 'nl' ), 'Reader', 'MPINT preview',
                                        array( 'text' => "MPINT preview\n" ), true );
        $this->assertTrue( $preview['send_result'] === true );
    }

    // ------------------------------------------------------------------ INT-14

    /** A collaboration notification for the test user: an event (without a real collaboration item) and one item. */
    private function collaborationItem( array &$made )
    {
        $event = new eZNotificationEvent( array( 'id' => null, 'event_type_string' => 'ezcollaboration', 'status' => eZNotificationEvent::STATUS_HANDLED,
                                                 'data_int1' => 0, 'data_int2' => 0, 'data_int3' => 0, 'data_int4' => 0,
                                                 'data_text1' => 'ezapprove', 'data_text2' => '', 'data_text3' => '', 'data_text4' => '' ) );
        $event->store();
        $collection = eZNotificationCollection::create( $event->attribute( 'id' ), 'ezcollaboration', 'ezmail' );
        $collection->setAttribute( 'data_subject', 'MPINT collaboration' );
        $collection->setAttribute( 'data_text', "MPINT collaboration\n" );
        $collection->store();
        $item = $collection->addItem( self::address( 'user' ) );
        $made[] = array( (int)$event->attribute( 'id' ), (int)$collection->attribute( 'id' ) );
        return array( $event, $collection, $item );
    }

    private function removeNotifications( array $made )
    {
        $db = eZDB::instance();
        foreach ( $made as $pair )
        {
            $db->query( 'DELETE FROM eznotificationcollection_item WHERE collection_id = ' . (int)$pair[1] );
            $db->query( 'DELETE FROM eznotificationcollection WHERE id = ' . (int)$pair[1] );
            $db->query( 'DELETE FROM eznotificationevent WHERE id = ' . (int)$pair[0] );
        }
    }

    /** The digest run, for the test user's address only (the live installation's own digests are not touched). */
    private function runDigestForTestUser()
    {
        $address = self::address( 'user' );
        $digest = new class( $address ) extends eZGeneralDigestHandler {
            private $only;
            public function __construct( $only ) { parent::__construct(); $this->only = $only; }
            function fetchUsersForDigest( $timestamp ) { return array( array( 'address' => $this->only ) ); }
        };
        $tick = eZNotificationEvent::create( 'ezcurrenttime', array( 'time' => time() + 8 * 86400 ) );
        return $digest->handle( $tick );
    }

    public function testCollaborationDigest()
    {
        $made = array();
        try
        {
            $prefs = expMailPreferences::forRecipient( $this->userRecipient() );
            $prefs->set( 'collaboration', true, $this->context() );
            $prefs->setFrequency( 'collaboration', 'daily', $this->context() );

            list( $event, $collection, $item ) = $this->collaborationItem( $made );
            eZCollaborationItemHandler::scheduleForPreference( $item, self::$userObjectID );
            $stored = eZPersistentObject::fetchObject( eZNotificationCollectionItem::definition(), null, array( 'id' => $item->attribute( 'id' ) ) );
            $this->assertGreaterThan( time(), (int)$stored->attribute( 'send_date' ), 'daily: the item waits for the digest' );

            // the immediate send has nothing to do and does not count a failure
            $handler = new eZCollaborationNotificationHandler();
            $handler->sendMessage( $event, array() );
            $this->assertCount( 0, $this->mails() );

            $this->runDigestForTestUser();
            $mails = $this->mails();
            $this->assertCount( 1, $mails, 'one digest' );
            $this->assertStringContainsString( self::address( 'user' ), $mails[0] );
            $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category:\s*collaboration/mi', $mails[0], 'a digest of collaboration items alone is collaboration mail' );
            $this->assertStringContainsString( "Collaboration and approvals:\n", str_replace( "\r\n", "\n", quoted_printable_decode( $mails[0] ) ), 'the digest lists the collaboration part' );
            $this->assertNull( eZPersistentObject::fetchObject( eZNotificationCollectionItem::definition(), null, array( 'id' => $item->attribute( 'id' ) ) ),
                               'the item is sent and removed' );

            // immediately: no schedule
            $prefs->setFrequency( 'collaboration', 'immediate', $this->context() );
            list( , , $item2 ) = $this->collaborationItem( $made );
            eZCollaborationItemHandler::scheduleForPreference( $item2, self::$userObjectID );
            $stored = eZPersistentObject::fetchObject( eZNotificationCollectionItem::definition(), null, array( 'id' => $item2->attribute( 'id' ) ) );
            $this->assertSame( 0, (int)$stored->attribute( 'send_date' ) );

            // switched off: the waiting item is dropped, no digest
            $prefs->setFrequency( 'collaboration', 'weekly', $this->context() );
            list( , , $item3 ) = $this->collaborationItem( $made );
            eZCollaborationItemHandler::scheduleForPreference( $item3, self::$userObjectID );
            $prefs->set( 'collaboration', false, $this->context() );
            foreach ( glob( self::$mailDir . '/*' ) ?: array() as $f )
                @unlink( $f );
            $this->runDigestForTestUser();
            $this->assertCount( 0, $this->mails() );
            $this->assertNull( eZPersistentObject::fetchObject( eZNotificationCollectionItem::definition(), null, array( 'id' => $item3->attribute( 'id' ) ) ) );
            $this->assertStringContainsString( 'digest_items', (string)file_get_contents( self::$installation . '/design/standard/templates/notification/handler/ezcollaboration/view/digest_plain.tpl' ) );
        }
        finally
        {
            $this->removeNotifications( $made );
        }
    }

    // ------------------------------------------------------------------ INT-15

    private function resetUserAddress()
    {
        eZDB::instance()->query( "UPDATE ezuser SET email = '" . self::address( 'user' ) . "' WHERE contentobject_id = " . (int)self::$userObjectID );
        eZUser::purgeUserCacheByUserId( self::$userObjectID );
        self::$user = eZUser::fetch( self::$userObjectID );
    }

    private function storedAddress()
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT email FROM ezuser WHERE contentobject_id = ' . (int)self::$userObjectID );
        return isset( $rows[0]['email'] ) ? (string)$rows[0]['email'] : '';
    }

    public function testAddressChangeNeedsConfirmation()
    {
        try
        {
            $this->resetUserAddress();
            $this->setIni( 'mailpreferences.ini', 'EmailChangeSettings', 'Confirm', 'enabled' );
            $this->assertTrue( expMailAddressChange::confirmationRequired() );
            $this->assertSame( 'unchanged', expMailAddressChange::request( self::$user, strtoupper( self::address( 'user' ) ) ) );

            $result = expMailAddressChange::request( self::$user, self::address( 'user-new' ), $this->context() );
            $this->assertSame( 'pending_confirmation', $result );
            $this->assertSame( self::address( 'user' ), $this->storedAddress(), 'the account keeps its address' );
            $mails = $this->mails();
            $this->assertCount( 2, $mails, 'the confirmation to the new address, the notice to the old one' );
            $toNew = array_values( array_filter( $mails, function ( $m ) { return preg_match( '/^To:.*' . preg_quote( self::address( 'user-new' ), '/' ) . '/mi', $m ); } ) );
            $toOld = array_values( array_filter( $mails, function ( $m ) { return preg_match( '/^To:.*' . preg_quote( self::address( 'user' ), '/' ) . '/mi', $m ); } ) );
            $this->assertCount( 1, $toNew );
            $this->assertCount( 1, $toOld );
            foreach ( $mails as $m )
                $this->assertMatchesRegularExpression( '/^X-Exp-Mail-Category:\s*security/mi', $m );
            $this->assertStringNotContainsString( self::address( 'user-new' ), $toOld[0], 'the notice shows the new address masked' );

            // the page shows the waiting change
            $vars = Exponential\Service\MailPreferencesPage::templateVariables( expMailPreferences::forRecipient( $this->userRecipient() ), 'account', 'mailpreferences/settings', false );
            $texts = array_map( function ( $n ) { return $n['text']; }, array_merge( $vars['notice'] ? array( $vars['notice'] ) : array(), $vars['notices'] ) );
            $this->assertNotEmpty( array_filter( $texts, function ( $t ) { return strpos( $t, 'waits for its confirmation' ) !== false; } ) );

            // the link of the mail confirms it
            $this->assertMatchesRegularExpression( '#mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', quoted_printable_decode( str_replace( "=\n", '', $toNew[0] ) ) );
            preg_match( '#mailpreferences/confirm/(m1[A-Za-z0-9_-]+)#', quoted_printable_decode( str_replace( "=\n", '', $toNew[0] ) ), $m );
            $confirmed = expMailPreferencesService::confirm( $m[1], new expConsentContext( 'confirm', 'MPINT', '192.0.2.20', 0, 'mpint' ) );
            $this->assertSame( 'confirmed', $confirmed['result'] );
            $this->assertSame( 'email_change', $confirmed['kind'] );
            $this->assertSame( self::address( 'user-new' ), $this->storedAddress(), 'confirmed: the new address' );

            // the old behaviour, by setting
            $this->resetUserAddress();
            $this->setIni( 'mailpreferences.ini', 'EmailChangeSettings', 'Confirm', 'disabled' );
            $this->assertSame( 'changed', expMailAddressChange::request( self::$user, self::address( 'user-new' ) ) );
        }
        finally
        {
            $this->resetUserAddress();
        }
    }

    public function testPublishingAUserObjectAsksForConfirmation()
    {
        try
        {
            $this->resetUserAddress();
            $this->setIni( 'mailpreferences.ini', 'EmailChangeSettings', 'Confirm', 'enabled' );
            // as user/edit and the admin's edit do it: a new version, the account's new address stored as the draft,
            // then the publish
            $object = eZContentObject::fetch( self::$userObjectID );
            $version = $object->createNewVersion();
            $dataMap = $version->dataMap();
            $attribute = $dataMap['user_account'];
            $draftUser = $attribute->content();
            $this->assertInstanceOf( eZUser::class, $draftUser );
            $draftUser->setAttribute( 'email', self::address( 'user-pub' ) );
            $attribute->setContent( $draftUser );
            $attribute->store();
            $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => self::$userObjectID, 'version' => $version->attribute( 'version' ) ) );
            $this->assertSame( eZModuleOperationInfo::STATUS_CONTINUE, $result['status'] );
            $this->assertSame( self::address( 'user' ), $this->storedAddress(), 'published: the account keeps its address' );
            $last = expMailAddressChange::lastResult();
            $this->assertSame( 'pending_confirmation', $last['result'] );
            $pending = expMailPendingRow::fetchForKey( 'u:' . self::$userObjectID, 'email_change' );
            $this->assertCount( 1, $pending );
            $this->assertSame( self::address( 'user-pub' ), $pending[0]->dataArray()['email'] );

            // the account services take the same way
            $code = (string)file_get_contents( self::$installation . '/extension/expservices/classes/users/expaccountservices.php' )
                  . (string)file_get_contents( self::$installation . '/extension/expservices/classes/users/expuserservices.php' );
            $this->assertSame( 2, substr_count( $code, 'expMailAddressChange::request(' ) );
        }
        finally
        {
            $this->resetUserAddress();
        }
    }
}
