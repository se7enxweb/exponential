<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * cjw_newsletter 4.2.0, area N6 import/export and migration: the CSV import with a column mapping (mapper, dry run,
 * import, skips, consent source), the subscriber export (filters, columns, formula cells), the eznewsletter
 * migration (ext:cjw_newsletter:import-eznewsletter) against a throwaway SQLite file under var/tmp holding the old
 * tables with synthetic rows, and the views of the area.
 *
 * Live style: the installation's database, nltest-*@example.invalid addresses only, everything removed in tearDown.
 * The old eznewsletter tables are never created in the installation's database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class cjwNewsletterImportExportTest extends cjwNewsletterTestCase
{
    private $files = array();
    private $runIds = array();
    private $sqlite = null;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'CjwNewsletterMappedImport' ) )
            $this->markTestSkipped( 'cjw_newsletter 4.2.0 import/export classes are not installed' );
        $this->setIni( 'cjw_newsletter.ini', 'NewsletterCsvImportSettings', 'ImportInBackground', 'disabled' );
    }

    public function tearDown(): void
    {
        $db = eZDB::instance();
        foreach ( $this->runIds as $runId )
            $db->query( "DELETE FROM cjwnl_migration_log WHERE run_id = '" . $db->escapeString( $runId ) . "'" );
        $this->runIds = array();
        $db->query( "DELETE FROM cjwnl_import_mapping WHERE name LIKE 'NLTEST%'" );
        // the kernel's preference rows and consent log of the test addresses
        if ( class_exists( 'expMailSuppression' ) && class_exists( 'CjwNewsletterMailPreferences' ) && CjwNewsletterMailPreferences::available() )
        {
            $emails = $this->extraEmails;
            foreach ( (array)$db->arrayQuery( "SELECT email FROM cjwnl_user WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" ) as $row )
                $emails[] = $row['email'];
            foreach ( array_unique( $emails ) as $email )
            {
                $key = $db->escapeString( 'a:' . expMailSuppression::hash( strtolower( $email ) ) );
                $db->query( "DELETE FROM expmail_preference WHERE recipient_key = '$key'" );
                $db->query( "DELETE FROM expmail_pending WHERE recipient_key = '$key'" );
                $db->query( "DELETE FROM expmail_consent_log WHERE recipient_key = '$key'" );
            }
            $db->query( "DELETE FROM expmail_consent_log WHERE email LIKE 'nltest-%@" . self::MAIL_DOMAIN . "'" );
        }
        foreach ( $this->createdImportIds as $importId )
        {
            $result = CjwNewsletterMappedImport::resultFilePath( $importId );
            if ( is_file( $result ) )
                unlink( $result );
        }
        foreach ( $this->files as $file )
            if ( is_file( $file ) )
                unlink( $file );
        $this->files = array();
        $this->sqlite = null;
        parent::tearDown();
    }

    // ------------------------------------------------------------------ helpers

    /** @return string a CSV file in the upload folder of the imports */
    private function uploadFile( $text )
    {
        $dir = CjwNewsletterCsvMapper::directory();
        eZDir::mkdir( $dir, false, true );
        $path = $dir . '/nltest-' . getmypid() . '-' . ( ++self::$counter ) . '-mapped.csv';
        file_put_contents( $path, $text );
        $this->files[] = $path;
        return $path;
    }

    /** @return CjwNewsletterImport a stored mapped import of the test list */
    private function mappedImport( $text, $mapping = null, $settings = array(), $consentSource = 'NLTEST trade fair sign-up' )
    {
        $path = $this->uploadFile( $text );
        $import = CjwNewsletterImport::create( self::LIST_OBJECT_ID, CjwNewsletterMappedImport::TYPE, 'NLTEST import' );
        $import->setAttribute( 'consent_source', $consentSource );
        $import->store();
        $this->createdImportIds[] = (int)$import->attribute( 'id' );
        $import->setAttribute( 'data_text', $path );
        $settings = array_merge( array( 'mapping' => array(), 'delimiter' => ';', 'has_header' => true, 'encoding' => 'UTF-8',
                                        'formats' => array( 0 ), 'update_existing' => true ), $settings );
        $mapper = new CjwNewsletterCsvMapper( $path, $settings['delimiter'], $settings['has_header'], $settings['encoding'] );
        $settings['mapping'] = $mapping === null ? $mapper->guessMapping() : $mapping;
        CjwNewsletterMappedImport::storeSettings( $import, $settings );
        $import->store();
        return CjwNewsletterImport::fetch( $import->attribute( 'id' ) );
    }

    private function userCount( $email )
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT COUNT(*) AS c FROM cjwnl_user WHERE email = '" . eZDB::instance()->escapeString( $email ) . "'" );
        return (int)$rows[0]['c'];
    }

    private function consentRows( $email, $source = 'import' )
    {
        if ( !CjwNewsletterMailPreferences::available() )
            return null;
        $db = eZDB::instance();
        $key = $db->escapeString( 'a:' . expMailSuppression::hash( strtolower( $email ) ) );
        return (array)$db->arrayQuery( "SELECT action, source, wording FROM expmail_consent_log WHERE recipient_key = '$key' AND source = '" . $db->escapeString( $source ) . "'" );
    }

    /** @return string a throwaway SQLite file with the eznewsletter 1.6 tables */
    private function oldDatabase()
    {
        $dir = eZSys::rootDir() . '/var/tmp';
        $file = $dir . '/nltest-eznewsletter-' . getmypid() . '-' . ( ++self::$counter ) . '.sqlite';
        $this->files[] = $file;
        $pdo = new PDO( 'sqlite:' . $file, null, null, array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
        foreach ( array_filter( array_map( 'trim', explode( ';', preg_replace( '/^--.*$/m', '', file_get_contents( __DIR__ . '/fixtures/cjwNewsletterImportExport-eznewsletter-1.6-sqlite.sql' ) ) ) ) ) as $statement )
            $pdo->exec( $statement );
        $this->sqlite = $pdo;
        return $file;
    }

    private function insert( $table, $row )
    {
        $statement = $this->sqlite->prepare( 'INSERT INTO ' . $table . ' (' . implode( ', ', array_keys( $row ) ) . ') VALUES (' . implode( ', ', array_fill( 0, count( $row ), '?' ) ) . ')' );
        $statement->execute( array_values( $row ) );
    }

    private function migrate( $file, $options = array() )
    {
        $migration = new CjwNewsletterEznewsletterMigration( CjwNewsletterEznewsletterSource::sqliteFile( $file ), $options );
        $this->runIds[] = $migration->runId();
        return $migration->run();
    }

    // ------------------------------------------------------------------ the mapper

    public function testMapperGuessesTheMappingFromEnglishAndGermanColumnNames()
    {
        $path = $this->uploadFile( "Vorname;E-Mail;Nachname;Firma;Telefon;Sprache;Lieblingsfarbe\nAnn;a@example.invalid;Tester;ACME;+49 170 1;ger-DE;blau\n" );
        $mapper = new CjwNewsletterCsvMapper( $path, ';', true );
        $this->assertSame( array( 'first_name', 'email', 'last_name', 'organisation', 'phone_number', 'language', 'ignore' ), $mapper->guessMapping() );
        $this->assertSame( 1, $mapper->rowCount() );
        $path = $this->uploadFile( "First name,Email address,Surname\nBob,b@example.invalid,Test\n" );
        $this->assertSame( array( 'first_name', 'email', 'last_name' ), ( new CjwNewsletterCsvMapper( $path, 'comma', true ) )->guessMapping() );
    }

    public function testMapperFindsTheAddressColumnWithoutAHeaderAndReadsOtherEncodings()
    {
        $path = $this->uploadFile( "\xEF\xBB\xBFAnn;a@example.invalid\r\nBob;b@example.invalid\r\n" );
        $mapper = new CjwNewsletterCsvMapper( $path, ';', false );
        $this->assertSame( array( 'ignore', 'email' ), $mapper->guessMapping() );
        $this->assertCount( 2, $mapper->rows() );
        $this->assertSame( 'Ann', $mapper->rows()[0][0], 'the byte order mark is dropped' );
        $latin = $this->uploadFile( "email;first_name\na@example.invalid;J\xF6rg\n" );
        $rows = ( new CjwNewsletterCsvMapper( $latin, ';', true, 'ISO-8859-1' ) )->rows();
        $this->assertSame( 'Jörg', $rows[0][1] );
        $old = $this->uploadFile( "email;first_name\ra@example.invalid;Ann\rb@example.invalid;Bob\r" );
        $this->assertSame( 2, ( new CjwNewsletterCsvMapper( $old, ';', true ) )->rowCount(), 'old Mac line endings' );
    }

    public function testCleanMappingDropsUnknownAndRepeatedFields()
    {
        $clean = CjwNewsletterCsvMapper::cleanMapping( array( 0 => 'email', 1 => 'email', 2 => 'password', 3 => 'first_name' ), 5 );
        $this->assertSame( array( 0 => 'email', 1 => 'ignore', 2 => 'ignore', 3 => 'first_name', 4 => 'ignore' ), $clean );
        $this->assertSame( array( 0 => 'last_name', 1 => 'email' ),
            CjwNewsletterCsvMapper::mappingForHeader( array( 'Mail' => 'email', 'Name' => 'last_name' ), array( 'Name', 'Mail' ) ) );
    }

    public function testValuesAreCleaned()
    {
        $notes = array();
        $values = CjwNewsletterMappedImport::cleanValues( array( 'email' => 'x', 'salutation' => 'Frau', 'language' => 'ger_de', 'phone_number' => '0049 (170) 123-4567',
                                                                 'first_name' => ' Ann ', 'organisation' => '' ), $notes );
        $this->assertSame( array( 'salutation' => 2, 'language' => 'ger-DE', 'phone_number' => '+491701234567', 'first_name' => 'Ann' ), $values );
        $this->assertSame( array(), $notes );
        CjwNewsletterMappedImport::cleanValues( array( 'salutation' => 'Dr. Prof.', 'language' => 'German', 'phone_number' => 'call me' ), $notes );
        $this->assertSame( array( 'salutation', 'language', 'phone_number' ), $notes );
        $this->assertSame( 1, CjwNewsletterMappedImport::salutation( 'Mr.' ) );
    }

    // ------------------------------------------------------------------ the import

    public function testDryRunCountsAndWritesNothing()
    {
        $new = $this->newEmail( 'n6dry' );
        $import = $this->mappedImport( "email;first_name\n$new;Dry\nnot-an-address;X\n" );
        $result = CjwNewsletterMappedImport::run( $import, true );
        $this->assertSame( '', $result['error'] );
        $this->assertSame( 1, $result['totals']['users_created'] );
        $this->assertSame( 1, $result['totals']['subscriptions_created'] );
        $this->assertSame( 1, $result['totals']['reasons']['invalid'] );
        $this->assertSame( 0, $this->userCount( $new ), 'a dry run creates no user' );
        $import = CjwNewsletterImport::fetch( $import->attribute( 'id' ) );
        $this->assertSame( 1, (int)$import->attribute( 'is_dry_run' ) );
        $this->assertSame( CjwNewsletterMappedImport::STATUS_PREVIEWED, (int)$import->attribute( 'status' ) );
        $this->assertSame( 0, (int)$import->attribute( 'imported' ) );
        $this->assertSame( 1, (int)$import->attribute( 'skipped_count' ) );
        $this->assertIsArray( CjwNewsletterMappedImport::readResult( $import->attribute( 'id' ) ) );
    }

    public function testImportCreatesUpdatesAndSkipsWithReasons()
    {
        $existing = $this->newSubscriber( 'n6existing', null, CjwNewsletterSubscription::STATUS_PENDING );
        $existingEmail = $existing->attribute( 'email' );
        $new = $this->newEmail( 'n6new' );
        $unsubscribed = $this->newSubscriber( 'n6unsub', null, CjwNewsletterSubscription::STATUS_REMOVED_SELF );
        $blacklisted = $this->newEmail( 'n6black' );
        $item = CjwNewsletterBlacklistItem::create( $blacklisted, 'NLTEST' );
        $item->store();
        $text = "Mail;Vorname;Nachname;Anrede;Sprache;Telefon;Notiz\n"
              . "$new;Nina;Neu;Frau;ger-DE;+49 170 0000001;x\n"
              . strtoupper( $existingEmail ) . ";Erik;Existing;Herr;;;y\n"
              . "$new;Again;Twice;;;;\n"
              . "not-an-address;Bad;Row;;;;\n"
              . ";No;Mail;;;;\n"
              . $unsubscribed->attribute( 'email' ) . ";Un;Sub;;;;\n"
              . "$blacklisted;Black;Listed;;;;\n";
        $import = $this->mappedImport( $text );
        $settings = CjwNewsletterMappedImport::settings( $import );
        $this->assertSame( array( 'email', 'first_name', 'last_name', 'salutation', 'language', 'phone_number', 'ignore' ), $settings['mapping'] );

        $result = CjwNewsletterMappedImport::run( $import, false );
        $t = $result['totals'];
        $this->assertSame( '', $result['error'] );
        $this->assertSame( 7, $t['rows'] );
        $this->assertSame( 1, $t['users_created'] );
        $this->assertSame( 1, $t['users_updated'] );
        $this->assertSame( 1, $t['subscriptions_created'] );
        $this->assertSame( 1, $t['subscriptions_updated'], 'the pending subscription of the existing user is approved' );
        $this->assertSame( 5, $t['skipped'] );
        foreach ( array( 'duplicate', 'invalid', 'missing_email', 'removed_self' ) as $reason )
            $this->assertSame( 1, $t['reasons'][$reason], $reason );
        // the blacklist is mirrored into the kernel suppression list where that exists
        $this->assertSame( 1, $t['reasons']['blacklisted'] + $t['reasons']['suppressed'], 'blacklisted' );

        $user = CjwNewsletterUser::fetchByEmail( $new );
        $this->assertSame( 'Nina', $user->attribute( 'first_name' ) );
        $this->assertSame( 2, (int)$user->attribute( 'salutation' ) );
        $this->assertSame( 'ger-DE', $user->attribute( 'language' ) );
        $this->assertSame( '+491700000001', $user->attribute( 'phone_number' ) );
        $this->assertSame( (int)$import->attribute( 'id' ), (int)$user->attribute( 'import_id' ) );
        $sub = $this->subscriptionOf( $user );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$sub->attribute( 'status' ) );

        $this->assertSame( 1, $this->userCount( $existingEmail ), 'existing subscribers are updated, never duplicated' );
        $existing = CjwNewsletterUser::fetchByEmail( $existingEmail );
        $this->assertSame( 'Erik', $existing->attribute( 'first_name' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$this->subscriptionOf( $existing )->attribute( 'status' ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_REMOVED_SELF, (int)$this->subscriptionOf( $unsubscribed )->attribute( 'status' ), 'an unsubscription is respected' );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $blacklisted ) );

        $import = CjwNewsletterImport::fetch( $import->attribute( 'id' ) );
        $this->assertSame( CjwNewsletterMappedImport::STATUS_DONE, (int)$import->attribute( 'status' ) );
        $this->assertSame( 0, (int)$import->attribute( 'is_dry_run' ) );
        $this->assertGreaterThan( 0, (int)$import->attribute( 'imported' ) );
        $this->assertSame( 5, (int)$import->attribute( 'skipped_count' ) );
        $this->assertSame( 'done', CjwNewsletterMappedImport::run( $import, false )['error'], 'an import runs once' );

        $rows = $this->consentRows( $new );
        if ( $rows !== null )
        {
            $this->assertNotEmpty( $rows, 'the consent is in the kernel consent log with the source import' );
            $this->assertStringContainsString( 'NLTEST trade fair sign-up', $rows[0]['wording'] );
            $this->assertSame( array(), $this->consentRows( $new, 'bridge' ), 'the bridge did not record it a second time' );
        }
    }

    public function testSuppressedAndOptedOutAddressesAreSkipped()
    {
        if ( !CjwNewsletterMailPreferences::available() )
            $this->markTestSkipped( 'the e-mail preferences are not installed' );
        $suppressed = $this->newEmail( 'n6supp' );
        expMailSuppression::add( $suppressed, 'admin', 'NLTEST' );
        $optedOut = $this->newEmail( 'n6optout' );
        $recipient = expMailRecipient::fromAddress( $optedOut );
        expMailPreferences::forRecipient( $recipient )->setMaster( false, expConsentContext::system( 'NLTEST' ) );
        $this->assertSame( 'suppressed', CjwNewsletterImportConsent::blockReason( $suppressed ) );
        $this->assertSame( 'opted_out', CjwNewsletterImportConsent::blockReason( $optedOut ) );
        $import = $this->mappedImport( "email\n$suppressed\n$optedOut\n" );
        $result = CjwNewsletterMappedImport::run( $import, false );
        $this->assertSame( 1, $result['totals']['reasons']['suppressed'] );
        $this->assertSame( 1, $result['totals']['reasons']['opted_out'] );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $suppressed ) );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $optedOut ) );
    }

    public function testImportWithoutAnAddressColumnIsRefused()
    {
        $import = $this->mappedImport( "a;b\nx;y\n", array( 'first_name', 'last_name' ) );
        $this->assertSame( 'no_email', CjwNewsletterMappedImport::run( $import, true )['error'] );
    }

    public function testImportFileOutsideTheUploadFolderIsRefused()
    {
        $import = $this->mappedImport( "email\nx@example.invalid\n" );
        $import->setAttribute( 'data_text', eZSys::rootDir() . '/settings/site.ini' );
        $this->assertNull( CjwNewsletterMappedImport::mapper( $import ) );
        $this->assertSame( 'file', CjwNewsletterMappedImport::run( $import, true )['error'] );
    }

    // ------------------------------------------------------------------ the export

    public function testExportFiltersByStatusAndDefusesFormulaCells()
    {
        $user = $this->newSubscriber( 'n6export' );
        $user->setAttribute( 'first_name', '=HYPERLINK("http://x.invalid")' );
        $user->setAttribute( 'last_name', '+1-2' );
        $user->store();
        $pending = $this->newSubscriber( 'n6exportpending', null, CjwNewsletterSubscription::STATUS_PENDING );
        $filters = CjwNewsletterSubscriberExport::cleanFilters( array( 'statuses' => array( CjwNewsletterSubscription::STATUS_APPROVED ),
                                                                       'columns' => array( 'email', 'first_name', 'last_name', 'subscription_status', 'bogus' ) ) );
        $this->assertSame( array( 'email', 'first_name', 'last_name', 'subscription_status' ), $filters['columns'] );
        $csv = CjwNewsletterSubscriberExport::csv( self::LIST_OBJECT_ID, $filters );
        $this->assertStringStartsWith( "\xEF\xBB\xBF" . 'email;first_name;last_name;subscription_status', $csv );
        $this->assertStringContainsString( $user->attribute( 'email' ), $csv );
        $this->assertStringNotContainsString( $pending->attribute( 'email' ), $csv, 'the status filter' );
        $this->assertStringContainsString( "\"'=HYPERLINK(\"\"http://x.invalid\"\")\"", $csv, 'a formula cell is defused' );
        $this->assertStringContainsString( "'+1-2", $csv );
        $this->assertSame( '-5', CjwNewsletterSubscriberExport::defuse( '-5' ), 'a number stays a number' );
        $this->assertStringContainsString( ';approved', $csv );
        $all = CjwNewsletterSubscriberExport::cleanFilters( array() );
        $this->assertGreaterThanOrEqual( 2, CjwNewsletterSubscriberExport::count( self::LIST_OBJECT_ID, $all ) );
    }

    public function testExportFiltersByDate()
    {
        $user = $this->newSubscriber( 'n6exportdate' );
        $sub = $this->subscriptionOf( $user );
        eZDB::instance()->query( 'UPDATE cjwnl_subscription SET created = ' . mktime( 12, 0, 0, 3, 15, 2001 ) . ' WHERE id = ' . (int)$sub->attribute( 'id' ) );
        $in = CjwNewsletterSubscriberExport::cleanFilters( array( 'date_field' => 'subscribed', 'date_from' => '2001-03-15', 'date_to' => '2001-03-15', 'columns' => array( 'email', 'subscribed' ) ) );
        $rows = CjwNewsletterSubscriberExport::fetchRows( self::LIST_OBJECT_ID, $in );
        $this->assertSame( array( $user->attribute( 'email' ) ), array_column( $rows, 'email' ) );
        $this->assertStringStartsWith( '2001-03-15T', $rows[0]['subscribed'] );
        $out = CjwNewsletterSubscriberExport::cleanFilters( array( 'date_from' => '2001-03-16', 'date_to' => '2001-03-20' ) );
        $this->assertNotContains( $user->attribute( 'email' ), array_column( CjwNewsletterSubscriberExport::fetchRows( self::LIST_OBJECT_ID, $out ), 'email' ) );
        $this->assertSame( 0, CjwNewsletterSubscriberExport::day( '2001-02-30', false ), 'not a date' );
    }

    public function testExportAndImportUseKnownAuditEvents()
    {
        if ( !class_exists( 'expAuditTaxonomy' ) )
            $this->markTestSkipped( 'no audit trail' );
        foreach ( array( 'data.export.csv', 'data.import.csv', 'system.cjw_newsletter.import' ) as $name )
            $this->assertTrue( expAuditTaxonomy::decide( $name, expAuditConfig::get() )['valid'], $name );
    }

    // ------------------------------------------------------------------ the eznewsletter migration

    /** @return array the addresses of the synthetic old rows, by label */
    private function oldRows()
    {
        $e = array();
        foreach ( array( 'ann', 'confirmed', 'pending', 'removed', 'robinson', 'bounced', 'existing', 'otherlist' ) as $label )
            $e[$label] = $this->newEmail( 'n6old' . $label );
        $e['invalid'] = 'not-an-address';
        $this->insert( 'ezsubscription_list', array( 'id' => 1, 'name' => 'NLTEST old list A', 'description' => '', 'status' => 1 ) );
        $this->insert( 'ezsubscription_list', array( 'id' => 1, 'name' => 'NLTEST old list A (draft)', 'description' => '', 'status' => 0 ) );
        $this->insert( 'ezsubscription_list', array( 'id' => 2, 'name' => 'NLTEST old list B', 'description' => '', 'status' => 1 ) );
        $this->insert( 'ezsubscriptionuserdata', array( 'id' => 11, 'email' => $e['ann'], 'firstname' => 'Ann', 'name' => 'Old', 'password' => 'x', 'hash' => 'h', 'mobile' => '0049 170 1234567' ) );
        $this->insert( 'ezsubscriptionuserdata', array( 'id' => 12, 'email' => $e['existing'], 'firstname' => 'Ernie', 'name' => 'Old', 'password' => '', 'hash' => '', 'mobile' => '' ) );
        $d = function ( $y, $m, $day ) { return gmmktime( 10, 0, 0, $m, $day, $y ); };
        $sub = function ( $id, $email, $status, $list = 1, $extra = array() ) use ( $d ) {
            return array_merge( array( 'id' => $id, 'version_status' => 1, 'subscriptionlist_id' => $list, 'email' => $email, 'hash' => 'h' . $id,
                                       'status' => $status, 'output_format' => '0,1', 'created' => $d( 2019, 1, 1 ), 'confirmed' => 0, 'approved' => 0,
                                       'removed' => 0, 'bounce_count' => 0 ), $extra );
        };
        $this->insert( 'ezsubscription', $sub( 101, $e['ann'], 2, 1, array( 'confirmed' => $d( 2019, 1, 2 ), 'approved' => $d( 2019, 1, 3 ) ) ) );
        $this->insert( 'ezsubscription', $sub( 101, $e['ann'], 0, 1, array( 'version_status' => 0 ) ) );
        $this->insert( 'ezsubscription', $sub( 102, $e['confirmed'], 1, 1, array( 'confirmed' => $d( 2019, 2, 2 ), 'output_format' => '0' ) ) );
        $this->insert( 'ezsubscription', $sub( 103, $e['pending'], 0 ) );
        $this->insert( 'ezsubscription', $sub( 104, $e['removed'], 3, 1, array( 'confirmed' => $d( 2019, 3, 3 ), 'removed' => $d( 2020, 5, 5 ) ) ) );
        $this->insert( 'ezsubscription', $sub( 105, $e['robinson'], 2, 1, array( 'approved' => $d( 2019, 1, 3 ) ) ) );
        $this->insert( 'ezsubscription', $sub( 106, $e['bounced'], 2, 1, array( 'approved' => $d( 2019, 1, 3 ), 'bounce_count' => 3 ) ) );
        $this->insert( 'ezsubscription', $sub( 107, $e['invalid'], 2, 1, array( 'approved' => $d( 2019, 1, 3 ) ) ) );
        $this->insert( 'ezsubscription', $sub( 108, $e['existing'], 2, 1, array( 'approved' => $d( 2019, 1, 3 ) ) ) );
        $this->insert( 'ezsubscription', $sub( 109, $e['otherlist'], 2, 2, array( 'approved' => $d( 2019, 1, 3 ) ) ) );
        $this->insert( 'ezrobinsonlist', array( 'id' => 1, 'value' => $e['robinson'], 'type' => 0, 'global' => 1 ) );
        $this->insert( 'eznewsletter', array( 'id' => 7, 'name' => 'NLTEST old edition', 'send_date' => $d( 2020, 1, 1 ), 'send_status' => 3, 'status' => 1,
                                              'pretext' => '', 'posttext' => '' ) );
        $this->insert( 'ezsendnewsletteritem', array( 'id' => 1, 'newsletter_id' => 7, 'subscription_id' => 101, 'send_status' => 1 ) );
        $this->insert( 'ezsendnewsletteritem', array( 'id' => 2, 'newsletter_id' => 7, 'subscription_id' => 106, 'send_status' => 2 ) );
        return $e;
    }

    public function testMigrationDryRunWritesOnlyTheLog()
    {
        $file = $this->oldDatabase();
        $e = $this->oldRows();
        $before = md5_file( $file );
        $totals = $this->migrate( $file, array( 'dry_run' => true, 'list_map' => array( 1 => self::LIST_OBJECT_ID ) ) );
        $this->assertSame( '', $totals['error'] );
        $this->assertTrue( $totals['dry_run'] );
        $this->assertSame( 4, $totals['subscriptions']['created'], 'ann, confirmed, removed, existing' );
        $this->assertSame( 1, $totals['lists']['merged'] );
        $this->assertSame( 1, $totals['lists']['skipped'] );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $e['ann'] ), 'a dry run creates no user' );
        $rows = CjwNewsletterMigrationLog::fetchListByRunId( $totals['run_id'] );
        $this->assertNotEmpty( $rows );
        foreach ( $rows as $row )
            $this->assertSame( 1, (int)$row->attribute( 'is_dry_run' ) );
        $this->assertSame( $before, md5_file( $file ), 'the old tables are never changed' );
    }

    public function testMigrationTakesListsSubscribersAndOldDatesOver()
    {
        $file = $this->oldDatabase();
        $e = $this->oldRows();
        $existing = CjwNewsletterUser::create( $e['existing'], 0, 'Kept', '', false, CjwNewsletterUser::STATUS_PENDING, 'default', '', '', '', '' );
        $existing->setAttribute( 'status', CjwNewsletterUser::STATUS_CONFIRMED );
        $existing->store();
        $existingSub = CjwNewsletterSubscription::create( self::LIST_OBJECT_ID, $existing->attribute( 'id' ), array( 0 ), CjwNewsletterSubscription::STATUS_APPROVED );
        $existingSub->store();
        $before = md5_file( $file );

        $totals = $this->migrate( $file, array( 'list_map' => array( 1 => self::LIST_OBJECT_ID ) ) );
        $this->assertSame( '', $totals['error'] );
        $this->assertSame( 3, $totals['subscriptions']['created'] );
        $this->assertSame( 1, $totals['subscriptions']['merged'] );
        $this->assertSame( 1, $totals['reasons']['robinson'] );
        $this->assertSame( 1, $totals['reasons']['never_confirmed'] );
        $this->assertSame( 1, $totals['reasons']['bounced'] );
        $this->assertSame( 1, $totals['reasons']['invalid'] );
        $this->assertSame( 1, $totals['reasons']['no_list'] );
        $this->assertSame( 1, $totals['sends'] );

        $ann = CjwNewsletterUser::fetchByEmail( $e['ann'] );
        $this->assertIsObject( $ann );
        $this->assertSame( 'Ann', $ann->attribute( 'first_name' ) );
        $this->assertSame( '+491701234567', $ann->attribute( 'phone_number' ) );
        $sub = $this->subscriptionOf( $ann );
        $this->assertSame( CjwNewsletterSubscription::STATUS_APPROVED, (int)$sub->attribute( 'status' ) );
        $this->assertSame( gmmktime( 10, 0, 0, 1, 1, 2019 ), (int)$sub->attribute( 'created' ) );
        $this->assertSame( gmmktime( 10, 0, 0, 1, 2, 2019 ), (int)$sub->attribute( 'confirmed' ) );
        $this->assertSame( gmmktime( 10, 0, 0, 1, 3, 2019 ), (int)$sub->attribute( 'approved' ) );
        $this->assertSame( 'eznewsletter:101', $sub->attribute( 'remote_id' ) );
        $this->assertSame( ';0;1;', $sub->attribute( 'output_format_array_string' ) );

        $removed = $this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $e['removed'] ) );
        $this->assertSame( CjwNewsletterSubscription::STATUS_REMOVED_SELF, (int)$removed->attribute( 'status' ), 'an old unsubscription stays one' );
        $this->assertSame( gmmktime( 10, 0, 0, 5, 5, 2020 ), (int)$removed->attribute( 'removed' ) );
        $confirmed = $this->subscriptionOf( CjwNewsletterUser::fetchByEmail( $e['confirmed'] ) );
        $this->assertContains( (int)$confirmed->attribute( 'status' ), array( CjwNewsletterSubscription::STATUS_CONFIRMED, CjwNewsletterSubscription::STATUS_APPROVED ) );
        $this->assertSame( ';1;', $confirmed->attribute( 'output_format_array_string' ), 'old text only is cjw text' );
        foreach ( array( 'pending', 'robinson', 'bounced', 'otherlist' ) as $label )
            $this->assertFalse( CjwNewsletterUser::fetchByEmail( $e[$label] ), $label );

        $this->assertSame( 1, $this->userCount( $e['existing'] ), 'an existing newsletter user is used, not duplicated' );
        $this->assertSame( 'Kept', CjwNewsletterUser::fetchByEmail( $e['existing'] )->attribute( 'first_name' ) );

        $rows = $this->consentRows( $e['ann'] );
        if ( $rows !== null )
        {
            $this->assertNotEmpty( $rows );
            $this->assertStringContainsString( '2019-01-02', $rows[0]['wording'], 'the original opt-in date' );
        }
        $this->assertSame( $before, md5_file( $file ), 'the old tables are never changed' );

        // idempotent: a second run takes nothing over twice
        $again = $this->migrate( $file, array( 'list_map' => array( 1 => self::LIST_OBJECT_ID ) ) );
        $this->assertSame( 0, $again['subscriptions']['created'] );
        $this->assertSame( 4, $again['reasons']['already_migrated'] );
        $this->assertSame( 0, $again['sends'], 'the sends are recorded once' );
        $this->assertSame( 1, $this->userCount( $e['ann'] ) );
        $this->assertCount( 1, CjwNewsletterSubscription::fetchSubscriptionListByNewsletterUserId( $ann->attribute( 'id' ) ) );
    }

    public function testMigrationCreatesListsAndPutsRobinsonEntriesOnTheSuppressionList()
    {
        if ( !CjwNewsletterMailPreferences::available() )
            $this->markTestSkipped( 'the e-mail preferences are not installed' );
        $file = $this->oldDatabase();
        $e = $this->oldRows();
        $system = eZContentObjectTreeNode::fetch( self::LIST_NODE_ID )->attribute( 'parent' );
        $dry = $this->migrate( $file, array( 'dry_run' => true, 'create_lists' => true, 'system_node_id' => $system->attribute( 'node_id' ), 'robinson_to_suppression' => true ) );
        $this->assertSame( 2, $dry['lists']['created'] );
        $this->assertSame( 1, $dry['robinson']['suppressed'] );
        $this->assertFalse( expMailSuppression::isSuppressed( $e['robinson'] ), 'a dry run suppresses nothing' );

        $totals = $this->migrate( $file, array( 'create_lists' => true, 'system_node_id' => $system->attribute( 'node_id' ), 'robinson_to_suppression' => true ) );
        foreach ( CjwNewsletterMigrationLog::fetchListByRunId( $totals['run_id'] ) as $row )
            if ( $row->attribute( 'source_table' ) === 'ezsubscription_list' && (int)$row->attribute( 'target_id' ) > 0 )
                $this->createdObjectIds[] = (int)$row->attribute( 'target_id' );
        $this->assertSame( 2, $totals['lists']['created'] );
        $this->assertCount( 2, $this->createdObjectIds );
        foreach ( $this->createdObjectIds as $id )
            $this->assertTrue( CjwNewsletterEznewsletterMigration::isListObject( $id ) );
        $this->assertTrue( expMailSuppression::isSuppressed( $e['robinson'] ) );
        $this->assertSame( 'legal', expMailSuppression::reason( $e['robinson'] ) );
        $this->assertIsObject( CjwNewsletterUser::fetchByEmail( $e['otherlist'] ), 'the second list was created, so its subscriber came over' );
    }

    public function testSourceRunsOnlySelectStatementsAndOpensSqliteReadOnly()
    {
        $file = $this->oldDatabase();
        $source = CjwNewsletterEznewsletterSource::sqliteFile( $file );
        $this->assertSame( array(), $source->missingTables() );
        foreach ( array( 'DELETE FROM ezsubscription', 'SELECT 1; DELETE FROM ezsubscription', 'SELECT * FROM ezsubscription WHERE 1 = 1 UNION SELECT 1 FROM x; DROP TABLE y' ) as $sql )
        {
            try
            {
                $source->select( $sql );
                $this->fail( 'not refused: ' . $sql );
            }
            catch ( InvalidArgumentException $e )
            {
                $this->assertStringContainsString( 'SELECT', $e->getMessage() );
            }
        }
        $this->assertSame( 0, $source->count( 'ezsubscription' ) );
        $this->assertTrue( CjwNewsletterEznewsletterSource::sameDatabase()->hasTable( 'ezcontentobject' ) );
        $this->assertFalse( CjwNewsletterEznewsletterSource::sameDatabase()->hasTable( 'nltest_no_such_table' ) );

        // a database without the old tables: the run stops before it reads anything
        $empty = eZSys::rootDir() . '/var/tmp/nltest-eznewsletter-empty-' . getmypid() . '.sqlite';
        $this->files[] = $empty;
        ( new PDO( 'sqlite:' . $empty ) )->exec( 'CREATE TABLE other ( id INTEGER )' );
        $totals = $this->migrate( $empty );
        $this->assertStringContainsString( 'ezsubscription_list', $totals['error'] );
        $this->assertSame( 0, CjwNewsletterMigrationLog::fetchListCount( array( 'run_id' => $totals['run_id'] ) ) );
    }

    // ------------------------------------------------------------------ views, command, dashboard

    public function testUploadFormAndMappingViewRender()
    {
        $r = $this->runView( 'import_mapping', array( 0 ), array(), array( 'list' => self::LIST_NODE_ID ) );
        $this->assertViewOk( $r, 'upload form' );
        $this->assertStringContainsString( 'name="UploadCsvFile"', $r['content'] );
        $this->assertStringContainsString( 'name="ConsentSource"', $r['content'] );

        $new = $this->newEmail( 'n6view' );
        $import = $this->mappedImport( "E-Mail;Vorname\n$new;Vera\nbroken;Row\n" );
        $id = (int)$import->attribute( 'id' );
        $r = $this->runView( 'import_mapping', array( $id ) );
        $this->assertViewOk( $r, 'mapping' );
        $this->assertStringContainsString( 'name="Mapping[0]"', $r['content'] );
        $this->assertMatchesRegularExpression( '#<option value="email" selected="selected">#', $r['content'] );
        $this->assertStringContainsString( 'Vera', $r['content'], 'the preview' );

        $post = array( 'Mapping' => array( 'email', 'first_name' ), 'CsvDelimiter' => 'semicolon', 'Encoding' => 'UTF-8', 'HasHeader' => '1',
                       'UpdateExisting' => '1', 'Formats' => array( '0' ), 'ConsentSource' => 'NLTEST view sign-up' );
        $r = $this->runView( 'import_mapping', array( $id ), $post + array( 'DryRunButton' => '1' ) );
        $this->assertViewOk( $r, 'dry run' );
        $this->assertStringContainsString( 'nl-ie-report', $r['content'] );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $new ) );

        $r = $this->runView( 'import_mapping', array( $id ), $post + array( 'SaveMappingButton' => '1', 'MappingName' => 'NLTEST mapping' ) );
        $this->assertViewOk( $r, 'save mapping' );
        $saved = CjwNewsletterImportMapping::fetchListByListContentobjectId( self::LIST_OBJECT_ID );
        $this->assertSame( 'NLTEST mapping', $saved[0]->attribute( 'name' ) );
        $this->assertSame( array( 'E-Mail' => 'email', 'Vorname' => 'first_name' ), json_decode( $saved[0]->attribute( 'mapping' ), true ) );

        $r = $this->runView( 'import_mapping', array( $id ), $post + array( 'ImportButton' => '1' ) );
        $this->assertViewOk( $r, 'import' );
        $this->assertIsObject( CjwNewsletterUser::fetchByEmail( $new ) );
        $this->assertSame( 'NLTEST view sign-up', CjwNewsletterImport::fetch( $id )->attribute( 'consent_source' ) );

        $r = $this->runView( 'import_mapping', array( 999999999 ) );
        $this->assertViewClean( $r, 'unknown import' );
    }

    public function testImportWithoutConsentSourceIsRefusedInTheView()
    {
        $new = $this->newEmail( 'n6noconsent' );
        $import = $this->mappedImport( "email\n$new\n", null, array(), '' );
        $r = $this->runView( 'import_mapping', array( (int)$import->attribute( 'id' ) ),
                             array( 'Mapping' => array( 'email' ), 'HasHeader' => '1', 'ConsentSource' => '', 'ImportButton' => '1' ) );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'message-error', $r['content'] );
        $this->assertFalse( CjwNewsletterUser::fetchByEmail( $new ) );
    }

    public function testExportAndMigrationLogViewsRender()
    {
        $this->newSubscriber( 'n6exportview' );
        $r = $this->runView( 'subscriber_export', array( self::LIST_OBJECT_ID ) );
        $this->assertViewOk( $r, 'export form' );
        $this->assertStringContainsString( 'name="Columns[]"', $r['content'] );
        // the preview holds the first ten rows of the list, which other test data may fill: check the table and the count
        $this->assertStringContainsString( 'nl-ie-preview', $r['content'], 'the preview' );
        $this->assertMatchesRegularExpression( '#<span class="nl-pill is-info">[1-9][0-9]* rows</span>#', $r['content'], 'the row count' );
        $this->assertViewClean( $this->runView( 'subscriber_export', array( 1 ) ), 'not a list' );

        $file = $this->oldDatabase();
        $this->oldRows();
        $totals = $this->migrate( $file, array( 'dry_run' => true, 'list_map' => array( 1 => self::LIST_OBJECT_ID ) ) );
        $r = $this->runView( 'migration_log' );
        $this->assertViewOk( $r, 'runs' );
        $this->assertStringContainsString( $totals['run_id'], $r['content'] );
        $r = $this->runView( 'migration_log', array( $totals['run_id'] ), array(), array( 'action' => 'skipped' ) );
        $this->assertViewOk( $r, 'one run' );
        $this->assertStringContainsString( 'ezsubscription', $r['content'] );
        $this->assertViewClean( $this->runView( 'migration_log', array( 'no-such-run' ) ), 'unknown run' );

        $summary = CjwNewsletterImportExportHooks::dashboardSummary( array() );
        $this->assertSame( $totals['run_id'], $summary['runs'][0]['run_id'] );
        $this->assertGreaterThan( 0, $summary['run_count'] );
    }

    public function testCommandIsNamedImportEznewsletterAndRunsADryRun()
    {
        $this->assertFileExists( 'extension/cjw_newsletter/bin/php/import-eznewsletter.php', 'the console names it ext:cjw_newsletter:import-eznewsletter' );
        $file = $this->oldDatabase();
        $this->oldRows();
        $line = array( PHP_BINARY, 'extension/cjw_newsletter/bin/php/import-eznewsletter.php', '-s', 'admin', '--allow-root-user',
                       '--dry-run', '--source-sqlite=' . $file, '--list-map=1:' . self::LIST_OBJECT_ID );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, eZSys::rootDir(),
                              array( 'PATH' => getenv( 'PATH' ) ?: '/usr/bin:/bin' ) );
        $out = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        if ( preg_match( '/Dry run (ezn-[0-9a-f-]+)/', $out, $m ) )
            $this->runIds[] = $m[1];
        $this->assertSame( 0, $code, $out );
        $this->assertStringContainsString( 'nothing was written', $out );
        $this->assertStringContainsString( 'Subscriptions: 4 created', $out );
    }
}
