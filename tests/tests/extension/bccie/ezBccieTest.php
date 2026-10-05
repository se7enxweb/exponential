<?php
/**
 * The bccie extension: the neutralising of spreadsheet formulas, the CSV and SYLK writers, the handlers, the
 * date and option validation, the runner (files, locks, scheduled export, removal) and the runnable command and
 * cronjob classes. The live installation, no test database; the object the tests create is named CIETEST phpunit
 * and removed in tearDown() with everything it collected.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/bccie/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class ezBccieTest extends TestCase
{
    private static $objectID = 0;
    private $tmpDir;
    private $savedLog;
    private $savedLast;

    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        if ( !class_exists( 'bccieRunner' ) )
        {
            self::markTestSkipped( 'The bccie extension is not active.' );
        }
    }

    protected function setUp(): void
    {
        $this->tmpDir = eZSys::varDirectory() . '/tmp/bccie-phpunit';
        if ( !is_dir( $this->tmpDir ) )
        {
            eZDir::mkdir( $this->tmpDir, false, true );
        }
        eZUser::setCurrentlyLoggedInUser( eZUser::fetch( 14 ), 14 );
        // the runs of the tests are not exports of the site: the dashboard's log is put back in tearDown()
        $this->savedLog = bccieRunner::exportLog( 100 );
        $this->savedLast = bccieRunner::lastExport();
    }

    private function storeSiteData( $name, $value )
    {
        $method = new ReflectionMethod( 'bccieRunner', 'storeSiteData' );
        $method->setAccessible( true );
        $method->invoke( null, $name, $value );
    }

    protected function tearDown(): void
    {
        foreach ( eZDB::instance()->arrayQuery( "SELECT id FROM ezcontentobject WHERE name LIKE 'CIETEST phpunit%'" ) as $row )
        {
            eZInformationCollection::removeContentObject( (int)$row['id'] );
            $object = eZContentObject::fetch( (int)$row['id'] );
            if ( $object )
            {
                $object->purge();
            }
        }
        foreach ( (array)glob( $this->tmpDir . '/*' ) as $file )
        {
            @unlink( $file );
        }
        self::$objectID = 0;
        $this->storeSiteData( bccieRunner::EXPORT_LOG, $this->savedLog );
        $this->storeSiteData( bccieRunner::LAST_EXPORT, $this->savedLast ? $this->savedLast : array() );
    }

    /**
     * A form with four collections: formulas, quotes, umlauts, euro, a newline.
     */
    private function form()
    {
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => 43, 'class_identifier' => 'feedback_form',
                                                                     'creator_id' => 14, 'attributes' => array( 'name' => 'CIETEST phpunit form', 'description' => '' ) ) );
        $this->assertInstanceOf( 'eZContentObject', $object, 'the test form cannot be created' );
        self::$objectID = (int)$object->attribute( 'id' );
        $map = $object->dataMap();
        $rows = array(
            array( 'sender_name' => '=1+1', 'subject' => '+cmd', 'message' => '-2+3', 'email' => '@SUM(1,1)' ),
            array( 'sender_name' => 'Müller; Jörg', 'subject' => 'A "quoted" word, a comma', 'message' => "L1\nL2", 'email' => 'm@example.com' ),
            array( 'sender_name' => 'Euro € Привет', 'subject' => '+4912345', 'message' => 'x', 'email' => 'x@example.org' ),
            array( 'sender_name' => 'plain', 'subject' => '0', 'message' => '', 'email' => 'p@example.org' ),
        );
        foreach ( $rows as $n => $row )
        {
            $collection = eZInformationCollection::create( self::$objectID, 'phpunit' . $n, 14 );
            $collection->setAttribute( 'created', time() - 86400 * ( 10 - $n * 3 ) );
            $collection->store();
            foreach ( $row as $identifier => $text )
            {
                $attribute = eZInformationCollectionAttribute::create( $collection->attribute( 'id' ) );
                $attribute->setAttribute( 'contentobject_id', self::$objectID );
                $attribute->setAttribute( 'contentclass_attribute_id', $map[$identifier]->attribute( 'contentclassattribute_id' ) );
                $attribute->setAttribute( 'contentobject_attribute_id', $map[$identifier]->attribute( 'id' ) );
                $attribute->setAttribute( 'data_text', $text );
                $attribute->store();
            }
        }
        return $object;
    }

    /**
     * The collections of the form counted by a query that is never the same twice, because another process (the
     * console commands) changes them and the query cache would answer with the old number.
     */
    private function freshCount()
    {
        static $n = 0;
        return (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezinfocollection WHERE contentobject_id = ' . self::$objectID . ' AND ' . ( ++$n ) . ' = ' . $n )[0]['c'];
    }

    private function options( $object, array $override = array() )
    {
        $input = bccieRunner::defaultOptions( $object ) + array( 'creation_date' => false, 'modification_date' => false );
        $options = bccieRunner::normalizeOptions( $override + $input, $object, $errors );
        $this->assertNotFalse( $options, 'the options are not valid: ' . print_r( $errors, true ) );
        return $options;
    }

    // ---- neutralising formulas -------------------------------------------------------------------------

    public function testNeutraliseQuotesEveryFormulaStart()
    {
        foreach ( array( '=1+1', '+cmd|x', '-2+3', '@SUM(A1)', "\t=x", "\r=x" ) as $cell )
        {
            $this->assertSame( "'" . $cell, bccieExportUtils::neutralise( $cell ), 'not neutralised: ' . json_encode( $cell ) );
        }
    }

    public function testNeutraliseLeavesTextAndPlainNumbers()
    {
        foreach ( array( '', 'plain', 'a=b', '12', '-5', '+4912345', '3,5', '-0.5', ' =x', "'=x" ) as $cell )
        {
            $this->assertSame( $cell, bccieExportUtils::neutralise( $cell ) );
        }
    }

    public function testSafeFileNameKeepsOnlyHarmlessCharacters()
    {
        $this->assertSame( 'cietest_form', bccieExportUtils::safeFileName( 'CIETEST form' ) );
        $this->assertSame( 'etc_passwd', bccieExportUtils::safeFileName( '../../etc/passwd' ) );
        $this->assertSame( 'export', bccieExportUtils::safeFileName( '///' ) );
        $this->assertSame( 'a_b', bccieExportUtils::safeFileName( "a\r\nb;\"" ) );
    }

    // ---- dates ------------------------------------------------------------------------------------------

    public function testDateConditionsAcceptsBothFormsAndRejectsNonsense()
    {
        $ok = bccieExportUtils::dateConditions( array( 'start_date' => '2026-10-01', 'end_date' => '2026-10-04' ) );
        $this->assertSame( '', $ok['error'] );
        $this->assertSame( mktime( 0, 0, 0, 10, 1, 2026 ), $ok['from'] );
        $this->assertSame( mktime( 23, 59, 59, 10, 4, 2026 ), $ok['to'] );

        $old = bccieExportUtils::dateConditions( array( 'start_day' => '01', 'start_month' => '10', 'start_year' => '2026' ) );
        $this->assertSame( '', $old['error'] );
        $this->assertSame( '>=', $old['conditions'][0] );

        foreach ( array( array( 'start_date' => '2026-02-30' ), array( 'start_date' => 'yesterday' ), array( 'end_day' => 'x', 'end_month' => '1', 'end_year' => '2026' ),
                         array( 'start_date' => '2026-10-04', 'end_date' => '2026-10-01' ) ) as $request )
        {
            $this->assertNotSame( '', bccieExportUtils::dateConditions( $request )['error'], json_encode( $request ) );
        }
        $this->assertNull( bccieExportUtils::dateConditions( array() )['conditions'] );
    }

    // ---- handlers and Parser ----------------------------------------------------------------------------

    public function testBaseHandlerKeepsUtf8AndFlattensAnything()
    {
        $handler = new BaseHandler();
        $this->assertSame( 'Euro € Привет Żółć', $handler->escape( "Euro € Привет Żółć" ) );
        $this->assertSame( 'a b c', $handler->escape( "a\nb\r\nc" ) );
        $this->assertSame( '', $handler->escape( null ) );
        $this->assertSame( '', $handler->escape( false ) );
        $this->assertSame( '1', $handler->escape( true ) );
        $this->assertSame( 'a, b', $handler->escape( array( 'a', 'b', array( 'x' ) ) ) );
        $this->assertSame( '', $handler->escape( new stdClass() ) );
        $this->assertSame( 'Müller', $handler->escape( mb_convert_encoding( 'Müller', 'ISO-8859-1', 'UTF-8' ) ) );
    }

    public function testParserLoadsTheHandlersOfTheExportableDatatypes()
    {
        $parser = new Parser();
        $this->assertNotEmpty( $parser->handlerMap, 'no handler was loaded (the constructor is not called)' );
        $this->assertArrayHasKey( 'ezoption', $parser->handlerMap );
        $this->assertInstanceOf( 'eZOptionHandler', $parser->handlerMap['ezoption']['handler'] );
    }

    // ---- writers ----------------------------------------------------------------------------------------

    public function testCsvLineQuotesDoublesAndHasNoTrailingSeparator()
    {
        $exporter = new bccieExporter( array( 'format' => 'csv', 'separator' => ';' ) );
        $this->assertSame( "\"a\";\"say \"\"hi\"\"\";\"'=1+1\";\"\"\n", $exporter->csvLine( array( 'a', 'say "hi"', '=1+1', '' ) ) );
        $comma = new bccieExporter( array( 'format' => 'csv', 'separator' => ',' ) );
        $this->assertSame( "\"a\",\"b\"\n", $comma->csvLine( array( 'a', 'b' ) ) );
        $bad = new bccieExporter( array( 'format' => 'csv', 'separator' => 'x' ) );
        $this->assertSame( "\"a\";\"b\"\n", $bad->csvLine( array( 'a', 'b' ) ), 'an unknown separator falls back to ;' );
    }

    public function testHeaderHasOneCellPerColumnInTheOrderOfTheFields()
    {
        $object = $this->form();
        $ids = array();
        foreach ( bccieExportUtils::collectorAttributes( $object ) as $attribute )
        {
            $ids[$attribute['identifier']] = $attribute['id'];
        }
        $exporter = new bccieExporter( array( 'fields' => array( $ids['email'], -1, -2, 'contentobjectid', $ids['sender_name'] ), 'creation_date' => true, 'modification_date' => true ) );
        $this->assertSame( array( 'Email', '', 'ID', 'Sender name', 'Created', 'Modified' ), $exporter->headerCells() );
    }

    public function testSylkExportHasHeaderRowAndEscapes()
    {
        $object = $this->form();
        $collections = eZInformationCollection::fetchCollectionsList( self::$objectID, false, false, array() );
        $options = $this->options( $object );
        $parser = new Parser();
        $text = $parser->exportInformationCollection( $collections, $options['fields'], ';', 'sylk' );
        $this->assertStringStartsWith( "ID;Pcie\n", $text );
        $this->assertStringContainsString( 'C;N;K"Sender name"', $text, 'the header names are written' );
        $this->assertStringContainsString( 'K"\'=1+1"', $text );
        $this->assertStringContainsString( 'K"A ""quoted"" word, a comma"', $text );
        $this->assertStringContainsString( 'K"M', $text );
        $this->assertSame( 5, substr_count( $text, ';Y' ), 'one Y for the header and one per data row' );
        $this->assertStringEndsWith( "E\n", $text );
    }

    public function testCsvExportThroughTheParserKeepsEveryRowAndCharacter()
    {
        $object = $this->form();
        $collections = eZInformationCollection::fetchCollectionsList( self::$objectID, false, false, array() );
        $options = $this->options( $object );
        $text = ( new Parser() )->exportInformationCollection( $collections, $options['fields'], ';', 'csv' );
        $lines = explode( "\n", trim( $text ) );
        $this->assertCount( 5, $lines );
        $this->assertSame( '"ID";"Sender name";"Subject";"Message";"Email"', $lines[0] );
        $this->assertStringContainsString( 'Euro € Привет', $text );
        $this->assertStringContainsString( '"+4912345"', $text, 'a phone number is not a formula' );
        $this->assertStringContainsString( '"\'+cmd"', $text );
        $this->assertStringContainsString( '"L1 L2"', $text, 'a newline becomes a space' );
    }

    public function testOutputHandlersConvertAndPrefix()
    {
        $this->assertSame( "\xEF\xBB\xBF" . 'x', ( new bccieExportFormatOutputHandlerUtf8Bom() )->formatOutput( 'x' ) );
        $this->assertSame( "\xFF\xFE" . "x\0", ( new bccieExportFormatOutputHandlerUtf16Le() )->formatOutput( 'x' ) );
        $this->assertSame( "M\xFCller \x80", ( new bccieExportFormatOutputHandlerCP1252() )->formatOutput( 'Müller €' ) );
        $this->assertSame( 'windows-1252', ( new bccieExportFormatOutputHandlerCP1252() )->charset() );
        $this->assertSame( 'utf-16le', ( new bccieExportFormatOutputHandlerUtf16Le() )->charset() );
        $this->assertInstanceOf( 'bccieExportFormatOutputHandlerUtf8', bccieExportFormatOutputHandler::instance( 'no-such-handler' ), 'an unknown key gives the default handler' );
    }

    // ---- options ----------------------------------------------------------------------------------------

    public function testNormalizeOptionsRefusesWhatIsNotValid()
    {
        $object = $this->form();
        $good = bccieRunner::defaultOptions( $object );
        $cases = array( 'format' => array( 'format' => 'xls' ), 'separator' => array( 'separator' => 'x' ), 'charset' => array( 'charset' => 'ebcdic' ),
                        'fields' => array( 'fields' => array( 999999 ) ), 'dates' => array( 'start_date' => 'nope' ) );
        foreach ( $cases as $key => $override )
        {
            $this->assertFalse( bccieRunner::normalizeOptions( $override + $good, $object, $errors ), $key );
            $this->assertArrayHasKey( $key, $errors );
        }
        $this->assertFalse( bccieRunner::normalizeOptions( array( 'fields' => array( -2 ) ) + $good, $object, $errors ), 'only ignored fields' );
        $this->assertFalse( bccieRunner::normalizeOptions( $good, false, $errors ), 'no object' );
        $options = bccieRunner::normalizeOptions( array( 'separator' => 'pipe' ) + $good, $object, $errors );
        $this->assertSame( '|', $options['separator'], 'a separator can be given by name' );
    }

    // ---- the runner -------------------------------------------------------------------------------------

    public function testExportToFileWritesRowsAndReportsProgress()
    {
        $object = $this->form();
        $options = $this->options( $object, array( 'charset' => 'utf8bom' ) );
        $path = $this->tmpDir . '/out.csv';
        $steps = array();
        $rows = bccieRunner::exportToFile( $options, $path, function ( $done, $total ) use ( &$steps ) { $steps[] = array( $done, $total ); } );
        $this->assertSame( 4, $rows );
        $this->assertSame( array( array( 4, 4 ) ), $steps );
        $this->assertStringStartsWith( "\xEF\xBB\xBF\"ID\";", file_get_contents( $path ) );
        $this->assertFalse( bccieRunner::exportToFile( $options, $this->tmpDir . '/missing/folder/out.csv' ), 'an unwritable path is reported' );
    }

    public function testDateRangeLimitsTheExport()
    {
        $object = $this->form();
        $all = bccieRunner::countCollections( $this->options( $object ) );
        $recent = bccieRunner::countCollections( $this->options( $object, array( 'start_date' => date( 'Y-m-d', time() - 86400 * 5 ) ) ) );
        $this->assertSame( 4, $all );
        $this->assertLessThan( $all, $recent );
        $this->assertGreaterThan( 0, $recent );
    }

    public function testBatchesCoverMoreRowsThanOneBatch()
    {
        $object = $this->form();
        $db = eZDB::instance();
        // 250 more collections, so that the 200 per batch are crossed
        for ( $n = 0; $n < 250; ++$n )
        {
            $db->query( 'INSERT INTO ezinfocollection ( contentobject_id, created, modified, user_identifier, creator_id ) VALUES ( ' . self::$objectID . ', ' . ( time() - $n ) . ', ' . time() . ", 'bulk$n', 14 )" );
        }
        $options = $this->options( $object );
        $path = $this->tmpDir . '/bulk.csv';
        $this->assertSame( 254, bccieRunner::exportToFile( $options, $path ) );
        $this->assertCount( 255, file( $path ) );
    }

    public function testLockAllowsOneRunAtATime()
    {
        $lock = bccieRunner::lock( 'phpunit' );
        $this->assertNotFalse( $lock );
        $this->assertFalse( bccieRunner::lock( 'phpunit' ), 'a second run does not get the lock' );
        bccieRunner::unlock( $lock );
        $again = bccieRunner::lock( 'phpunit' );
        $this->assertNotFalse( $again, 'the lock is free after unlock' );
        bccieRunner::unlock( $again );
    }

    public function testPurgeDryRunBeforeAndAll()
    {
        $this->form();
        $this->assertSame( 4, bccieRunner::purge( self::$objectID, false, true ) );
        $this->assertSame( 4, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ), 'a dry run removes nothing' );
        $removed = bccieRunner::purge( self::$objectID, time() - 86400 * 3 );
        $this->assertSame( 3, $removed, 'the collections of 10, 7 and 4 days ago' );
        $this->assertSame( 1, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ) );
        $this->assertSame( 1, bccieRunner::purge( self::$objectID ) );
        $this->assertSame( 0, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ) );
        $this->assertSame( 0, eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezinfocollection_attribute WHERE contentobject_id = ' . self::$objectID )[0]['c'] + 0, 'the attributes go with their collections' );
    }

    public function testScheduledExportWritesFilesDryRunWritesNothingAndRemoveExportedKeepsOthers()
    {
        $object = $this->form();
        $ini = eZINI::instance( 'cie.ini' );
        $saved = array();
        foreach ( array( 'Collection', 'Directory', 'RemoveExported', 'ExportLimitedRange', 'CsvSeparator' ) as $key )
        {
            $saved[$key] = $ini->variable( 'CieSettings', $key );
        }
        $out = new class { public $lines = array(); function output( $t = false, $e = true ) { $this->lines[] = $t; } function error( $t = false, $e = true ) { $this->lines[] = 'ERROR ' . $t; } };
        try
        {
            $ini->setVariable( 'CieSettings', 'Collection', array( self::$objectID, 99999999 ) );
            $ini->setVariable( 'CieSettings', 'Directory', $this->tmpDir );
            $ini->setVariable( 'CieSettings', 'CsvSeparator', ',' );
            $ini->setVariable( 'CieSettings', 'RemoveExported', 'disabled' );
            $ini->setVariable( 'CieSettings', 'ExportLimitedRange', 'disabled' );

            $totals = bccieRunner::runCron( 'csv', $out, true );
            $this->assertSame( 0, $totals['files'] );
            $this->assertSame( 4, $totals['rows'] );
            $this->assertCount( 1, $totals['errors'], 'the missing object is reported' );
            $this->assertEmpty( glob( $this->tmpDir . '/*.csv' ), 'a dry run writes no file' );

            $totals = bccieRunner::runCron( 'csv', $out );
            $this->assertSame( 1, $totals['files'] );
            $files = glob( $this->tmpDir . '/cietest_phpunit_form_export_*.csv' );
            $this->assertCount( 1, $files );
            $first = trim( explode( "\n", file_get_contents( $files[0] ) )[0] );
            $this->assertSame( '"ID","Sender name","Subject","Message","Email"', $first, 'the header and the separator of the settings' );
            $this->assertSame( 4, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ), 'nothing is removed unless RemoveExported is on' );

            // RemoveExported: the exported collections go, one that arrives during the run would stay (its id is higher)
            $ini->setVariable( 'CieSettings', 'RemoveExported', 'enabled' );
            $totals = bccieRunner::runCron( 'sylk', $out );
            $this->assertSame( 4, $totals['removed'] );
            $this->assertSame( 0, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ) );
            $this->assertCount( 1, glob( $this->tmpDir . '/*.slk' ) );
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                $ini->setVariable( 'CieSettings', $key, $value );
            }
        }
    }

    public function testCronExportRefusesAnUnwritableFolderAndRemovesNothing()
    {
        $this->form();
        $ini = eZINI::instance( 'cie.ini' );
        $saved = array( 'Collection' => $ini->variable( 'CieSettings', 'Collection' ), 'Directory' => $ini->variable( 'CieSettings', 'Directory' ), 'RemoveExported' => $ini->variable( 'CieSettings', 'RemoveExported' ) );
        $out = new class { function output( $t = false, $e = true ) {} function error( $t = false, $e = true ) {} };
        try
        {
            $ini->setVariable( 'CieSettings', 'Collection', array( self::$objectID ) );
            $ini->setVariable( 'CieSettings', 'Directory', '/proc/bccie-no-such-folder' );
            $ini->setVariable( 'CieSettings', 'RemoveExported', 'enabled' );
            $totals = bccieRunner::runCron( 'csv', $out );
            $this->assertFalse( $totals['ok'] );
            $this->assertSame( 4, eZInformationCollection::fetchCollectionCountForObject( self::$objectID ), 'nothing is removed when no file was written' );
        }
        finally
        {
            foreach ( $saved as $key => $value )
            {
                $ini->setVariable( 'CieSettings', $key, $value );
            }
        }
    }

    public function testExportLogKeepsTheNewestEntries()
    {
        $before = bccieRunner::exportLog( 100 );
        for ( $n = 0; $n < bccieRunner::LOG_KEEP + 3; ++$n )
        {
            bccieRunner::recordExport( array( 'object_id' => 1, 'name' => 'CIETEST phpunit log', 'format' => 'csv', 'rows' => $n, 'source' => 'admin' ) );
        }
        $log = bccieRunner::exportLog( 100 );
        $this->assertCount( bccieRunner::LOG_KEEP, $log );
        $this->assertSame( bccieRunner::LOG_KEEP + 2, $log[0]['rows'], 'newest first' );
        $this->assertSame( $log[0]['time'], bccieRunner::lastExport()['time'] );
    }

    // ---- jobs, views, commands --------------------------------------------------------------------------

    public function testJobIdsAndFilesAreStrict()
    {
        $this->assertTrue( bccieJob::isID( '20261004201130-8d37e620' ) );
        foreach ( array( '', '../etc/passwd', '20261004201130-8D37E620', '20261004201130', "20261004201130-8d37e620\n" ) as $id )
        {
            $this->assertFalse( bccieJob::isID( $id ), json_encode( $id ) );
        }
        $this->assertFalse( bccieJob::status( '../x' ) );
        $this->assertFalse( bccieJob::exportFile( '20260101000000-00000000' ) );
    }

    public function testRemovingNeedsItsOwnPolicy()
    {
        $access = eZUser::fetch( eZUser::anonymousId() )->hasAccessTo( 'bccie', 'remove' );
        $this->assertSame( 'no', $access['accessWord'], 'the anonymous user may not remove collected information' );
        $admin = eZUser::fetch( 14 )->hasAccessTo( 'bccie', 'remove' );
        $this->assertNotSame( 'no', $admin['accessWord'] );
    }

    public function testModuleDefinesTheViewsAndThePolicies()
    {
        $Module = null;
        $ViewList = array();
        $FunctionList = array();
        include 'extension/bccie/modules/bccie/module.php';
        foreach ( array( 'overview', 'export', 'doexport', 'download', 'job' ) as $view )
        {
            $this->assertArrayHasKey( $view, $ViewList );
            $this->assertFileExists( 'extension/bccie/modules/bccie/' . $ViewList[$view]['script'] );
            $this->assertSame( 'ezbccienavigationpart', $ViewList[$view]['default_navigation_part'] );
        }
        $this->assertArrayHasKey( 'remove', $FunctionList );
        $this->assertFileExists( 'extension/bccie/design/standard/templates/parts/bccie/menu.tpl', 'the left menu of the navigation part' );
    }

    public function testRunnableClassesExistAndAreRegistered()
    {
        foreach ( array( 'Command\Extension\Bccie\Export', 'Command\Extension\Bccie\Status', 'Command\Extension\Bccie\Purge',
                         'Cronjob\Extension\Bccie\Exportcsv', 'Cronjob\Extension\Bccie\Exportsylk' ) as $class )
        {
            $this->assertTrue( class_exists( 'Exponential\\' . $class ), $class );
        }
        $this->assertInstanceOf( 'Exponential\Runnable\Command', Exponential\Command\Extension\Bccie\Export::create( __FILE__ ) );
        $this->assertInstanceOf( 'Exponential\Runnable\CronjobPart', Exponential\Cronjob\Extension\Bccie\Exportcsv::create( __FILE__ ) );

        foreach ( array( 'export', 'status', 'purge' ) as $command )
        {
            $stub = file_get_contents( 'extension/bccie/bin/php/' . $command . '.php' );
            $this->assertStringContainsString( '@alias cie-' . $command, $stub );
            $this->assertStringContainsString( '--help', $stub );
            $this->assertLessThan( 30, count( explode( "\n", $stub ) ), 'a thin stub' );
        }
        foreach ( array( 'exportcsv', 'exportsylk' ) as $part )
        {
            $stub = file_get_contents( 'extension/bccie/cronjobs/' . $part . '.php' );
            $this->assertStringContainsString( '@description', $stub );
            $this->assertStringContainsString( '::main( __FILE__', $stub );
        }
        $ini = eZINI::instance( 'cronjob.ini' );
        $this->assertContains( 'exportcsv.php', $ini->variable( 'CronjobPart-exportcsv', 'Scripts' ) );
        $this->assertContains( 'exportsylk.php', $ini->variable( 'CronjobPart-exportsylk', 'Scripts' ) );
    }

    public function testConsoleCommandsRunWithDryRun()
    {
        $object = $this->form();
        $run = function ( $arguments )
        {
            $command = 'php ' . escapeshellarg( getcwd() . '/extension/bccie/bin/php/' . $arguments[0] . '.php' ) . ' --allow-root-user -q ' . implode( ' ', array_map( 'escapeshellarg', array_slice( $arguments, 1 ) ) ) . ' 2>&1';
            exec( $command, $output, $code );
            return array( $code, implode( "\n", $output ) );
        };
        list( $code, $text ) = $run( array( 'export', '--object=' . self::$objectID, '--dry-run' ) );
        $this->assertSame( 0, $code, $text );
        $this->assertSame( 4, $this->freshCount() );
        list( $code, $text ) = $run( array( 'export', '--object=' . self::$objectID, '--format=xls' ) );
        $this->assertNotSame( 0, $code, 'an invalid format is an error' );
        list( $code, $text ) = $run( array( 'purge', '--object=' . self::$objectID ) );
        $this->assertSame( 0, $code );
        $this->assertSame( 4, $this->freshCount(), 'purge without --yes removes nothing' );
        list( $code, $text ) = $run( array( 'purge', '--object=' . self::$objectID, '--yes' ) );
        $this->assertSame( 0, $code, $text );
        $this->assertSame( 0, $this->freshCount() );
        list( $code, $text ) = $run( array( 'status' ) );
        $this->assertSame( 0, $code, $text );
    }
}
