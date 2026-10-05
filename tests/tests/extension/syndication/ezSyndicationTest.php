<?php
/**
 * The syndication extension: the fixed classes (removal by id, the filter factory, the safe unserialize, the
 * list helpers, the runner and its lock, the background job ids) and the runnable command and cronjob classes.
 * The live installation, no test database; the rows the tests create are named SYNTEST and removed in tearDown().
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/syndication/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class ezSyndicationTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ezpLiveInstallation::requireOrSkip();
        if ( !class_exists( 'eZSyndicationFeed' ) )
        {
            self::markTestSkipped( 'The syndication extension is not active.' );
        }
        $messages = array();
        if ( !eZSyndicationInstaller::install( null, $messages ) )
        {
            self::markTestSkipped( 'The syndication tables cannot be created: ' . implode( ' ', $messages ) );
        }
    }

    protected function tearDown(): void
    {
        $db = eZDB::instance();
        foreach ( $db->arrayQuery( "SELECT DISTINCT id FROM ezsyndication_feed WHERE name LIKE 'SYNTEST%'" ) as $row )
        {
            eZSyndicationFeed::removeFeed( (int)$row['id'] );
        }
        foreach ( $db->arrayQuery( "SELECT DISTINCT id FROM ezsyndication_import WHERE name LIKE 'SYNTEST%'" ) as $row )
        {
            eZSyndicationImport::removeImport( (int)$row['id'] );
        }
        $db->query( "DELETE FROM ezsyndication_filter WHERE type = 'SYNTEST'" );
    }

    private function newFeed( $name = 'SYNTEST feed' )
    {
        $feed = eZSyndicationFeed::create();
        $feed->setAttribute( 'name', $name );
        $feed->setAttribute( 'status', eZSyndicationFeed::STATUS_PUBLISHED );
        $feed->store();
        return $feed;
    }

    public function testInstallerKnowsAllTablesAndCreatesNothingTwice()
    {
        $this->assertCount( 11, eZSyndicationInstaller::tableNames() );
        $this->assertSame( array(), eZSyndicationInstaller::missingTables() );
        $messages = array();
        $this->assertTrue( eZSyndicationInstaller::install( null, $messages ) );
        $this->assertSame( array( 'The tables exist already.' ), $messages );
    }

    public function testRemoveFeedByIdIsStaticAndRemovesPublishedAndDraft()
    {
        $feed = $this->newFeed();
        $id = (int)$feed->attribute( 'id' );
        $draft = eZSyndicationFeed::fetchDraft( $id );
        $draft->store();
        $this->assertNotFalse( eZSyndicationFeed::fetch( $id, false ) );
        // a static call of what used to be an instance method was a fatal error on PHP 8
        $this->assertTrue( eZSyndicationFeed::removeFeed( $id ) );
        $this->assertFalse( (bool)eZSyndicationFeed::fetch( $id, false ) );
        $this->assertFalse( eZSyndicationFeed::removeFeed( $id ) );
    }

    public function testRemoveImportRemovesItsFilters()
    {
        $import = eZSyndicationImport::create();
        $import->setAttribute( 'name', 'SYNTEST import' );
        $import->setAttribute( 'status', eZSyndicationImport::STATUS_PUBLISHED );
        $import->store();
        $filter = eZSyndicationImportFilter::create( $import->attribute( 'id' ), 'Section' );
        $filter->setAttribute( 'status', eZSyndicationImport::STATUS_PUBLISHED );
        $filter->store();
        $filterID = (int)$filter->attribute( 'id' );
        $this->assertTrue( eZSyndicationImport::removeImport( (int)$import->attribute( 'id' ) ) );
        $this->assertFalse( (bool)eZSyndicationImportFilter::fetch( $filterID ) );
    }

    public function testFilterFactoryRefusesAnythingButTheConfiguredTypes()
    {
        $this->assertFalse( eZSyndicationFilter::filterClassName( 'Section(); system("id"); //' ) );
        $this->assertFalse( eZSyndicationFilter::filterClassName( 'Unknown' ) );
        $this->assertFalse( eZSyndicationFilter::filterClassName( array( 'Section' ) ) );
        $this->assertSame( 'eZFilterSection', eZSyndicationFilter::filterClassName( 'Section' ) );
        $this->assertFalse( eZSyndicationFilter::create( 'x"); exit; //' ) );
        $names = array_column( eZSyndicationFilter::filterTypeList(), 'type' );
        $this->assertContains( 'Section', $names );
        $this->assertContains( 'Attribute', $names );
    }

    public function testUnserializeArrayNeverCreatesObjects()
    {
        $this->assertSame( array( 'a' => 1 ), eZSyndication::unserializeArray( serialize( array( 'a' => 1 ) ) ) );
        $this->assertSame( array(), eZSyndication::unserializeArray( '' ) );
        $this->assertSame( array(), eZSyndication::unserializeArray( 'garbage' ) );
        $this->assertSame( array(), eZSyndication::unserializeArray( serialize( new stdClass() ) ) );
        $result = eZSyndication::unserializeArray( serialize( array( 'o' => new ArrayObject() ) ) );
        $this->assertInstanceOf( '__PHP_Incomplete_Class', $result['o'] );
        $this->assertSame( '0.1', eZSyndication::version() );
    }

    public function testListParametersAndPage()
    {
        $vp = eZSyndicationUI::listParameters( array( 'UserParameters' => array( 'sort' => 'bogus', 'order' => 'desc', 'q' => 'Foo%20Bar' ), 'Offset' => 5 ), array( 'name', 'id' ), 3 );
        $this->assertSame( 'name', $vp['sort'] );
        $this->assertSame( 'desc', $vp['order'] );
        $this->assertSame( 'Foo Bar', $vp['q'] );
        $this->assertSame( 5, $vp['offset'] );

        $items = array();
        foreach ( array( 'beta', 'Alpha', 'gamma', 'alphabet' ) as $i => $name )
        {
            $items[] = new eZSyndicationFeed( array( 'id' => $i + 1, 'name' => $name ) );
        }
        $total = 0;
        $page = eZSyndicationUI::page( $items, array( 'offset' => 0, 'limit' => 10, 'q' => 'alpha', 'sort' => 'name', 'order' => 'asc' ), array( 'name' ), $total );
        $this->assertSame( 2, $total );
        $this->assertSame( array( 'Alpha', 'alphabet' ), array_map( function ( $f ) { return $f->attribute( 'name' ); }, $page ) );
        $page = eZSyndicationUI::page( $items, array( 'offset' => 1, 'limit' => 2, 'q' => '', 'sort' => 'id', 'order' => 'desc' ), array( 'name' ), $total );
        $this->assertSame( 4, $total );
        $this->assertSame( array( 3, 2 ), array_map( function ( $f ) { return (int)$f->attribute( 'id' ); }, $page ) );
        $this->assertSame( 'syndication/list/(q)/a%20b/(sort)/name/(order)/asc', eZSyndicationUI::listURL( 'list', array( 'offset' => 0, 'q' => 'a b', 'sort' => 'name', 'order' => 'asc' ) ) );
    }

    public function testSafeServerHidesCredentials()
    {
        $import = eZSyndicationImport::create();
        $import->setAttribute( 'server', 'https://user:secret@example.com:8443/soap.php?x=1' );
        $this->assertSame( 'https://example.com:8443/soap.php?x=1', eZSyndicationUI::safeServer( $import ) );
        $import->setAttribute( 'server', 'https://example.com/soap.php' );
        $this->assertSame( 'https://example.com/soap.php', eZSyndicationUI::safeServer( $import ) );
    }

    public function testFeedWithoutSourceExportsNothingInADryRun()
    {
        $feed = $this->newFeed( 'SYNTEST dry' );
        $run = eZSyndicationRunner::exportFeed( $feed, false, 0, true );
        $this->assertSame( array( 'sources' => 0, 'checked' => 0, 'generated' => 0, 'more' => false ), $run );
        $totals = eZSyndicationRunner::runExport( false, 'test', (int)$feed->attribute( 'id' ), true );
        $this->assertTrue( $totals['ok'] );
        $this->assertSame( 1, $totals['feeds'] );
    }

    public function testTheRunLockAllowsOneRunAtATime()
    {
        $first = eZSyndicationRunner::lock( 'test_lock' );
        $this->assertNotFalse( $first );
        $this->assertFalse( eZSyndicationRunner::lock( 'test_lock' ) );
        eZSyndicationRunner::unlock( $first );
        $again = eZSyndicationRunner::lock( 'test_lock' );
        $this->assertNotFalse( $again );
        eZSyndicationRunner::unlock( $again );
    }

    public function testRecordedRunsAreReadBack()
    {
        $before = eZSyndicationRunner::lastRun( eZSyndicationRunner::LAST_EXPORT );
        eZSyndicationRunner::recordRun( eZSyndicationRunner::LAST_EXPORT, array( 'feeds' => 2 ), 'test' );
        $run = eZSyndicationRunner::lastRun( eZSyndicationRunner::LAST_EXPORT );
        $this->assertSame( 2, $run['feeds'] );
        $this->assertSame( 'test', $run['by'] );
        if ( $before )
        {
            eZSyndicationRunner::recordRun( eZSyndicationRunner::LAST_EXPORT, $before, $before['by'] );
        }
    }

    public function testJobIdsAndStatus()
    {
        $this->assertTrue( eZSyndicationJob::isID( '20261004192147-4cfaaf5d' ) );
        $this->assertFalse( eZSyndicationJob::isID( '../../etc/passwd' ) );
        $this->assertFalse( eZSyndicationJob::isID( '20261004192147-4cfaaf5dX' ) );
        $this->assertFalse( eZSyndicationJob::status( '../x' ) );
        $error = '';
        $this->assertFalse( eZSyndicationJob::start( 'rm -rf', array(), $error ) );
        $this->assertSame( 'Unknown command.', $error );
    }

    public function testRunnableClassesExistAndAreRegistered()
    {
        foreach ( array( 'Command\Extension\Syndication\Export', 'Command\Extension\Syndication\Import', 'Command\Extension\Syndication\Install',
                         'Command\Extension\Syndication\Status', 'Cronjob\Extension\Syndication\ExportFeed', 'Cronjob\Extension\Syndication\ImportFeed' ) as $class )
        {
            $this->assertTrue( class_exists( 'Exponential\\' . $class ), $class );
        }
        $this->assertInstanceOf( 'Exponential\Runnable\CronjobPart', Exponential\Cronjob\Extension\Syndication\ExportFeed::create( __FILE__ ) );
        $this->assertInstanceOf( 'Exponential\Runnable\Command', Exponential\Command\Extension\Syndication\Status::create( __FILE__ ) );
        $root = dirname( __DIR__, 4 );
        foreach ( array( 'export', 'import', 'install', 'status' ) as $name )
        {
            $this->assertFileExists( $root . '/extension/syndication/bin/php/' . $name . '.php' );
        }
        $ini = eZINI::instance( 'cronjob.ini' );
        $this->assertContains( 'syndication_export.php', $ini->variable( 'CronjobPart-export_feed', 'Scripts' ) );
        $this->assertContains( 'syndication_import.php', $ini->variable( 'CronjobPart-import_feed', 'Scripts' ) );
    }

    public function testConsoleCommandsRunWithDryRun()
    {
        $root = dirname( __DIR__, 4 );
        $php = escapeshellarg( PHP_BINARY );
        foreach ( array( 'status', 'export --dry-run', 'import --dry-run', 'install --dry-run' ) as $command )
        {
            $output = (string)shell_exec( 'cd ' . escapeshellarg( $root ) . ' && ' . $php . ' extension/syndication/bin/php/' . $command . ' --allow-root-user 2>&1' );
            $this->assertStringNotContainsString( 'Fatal error', $output, $command );
            $this->assertStringNotContainsString( 'Uncaught', $output, $command );
            $this->assertNotSame( '', trim( $output ), $command );
        }
    }

    public function testDashboardSummaryHasTheKeysTheStartPageUses()
    {
        $summary = eZSyndicationDashboard::summary();
        foreach ( array( 'tables_missing', 'feeds', 'imports', 'items', 'runs', 'problems', 'soap' ) as $key )
        {
            $this->assertArrayHasKey( $key, $summary );
        }
        $feed = $this->newFeed( 'SYNTEST problem' );
        $codes = array_column( eZSyndicationDashboard::summary()['problems'], 'code' );
        $this->assertContains( 'feed_source', $codes );
        $this->assertContains( 'feed_disabled', $codes );
    }
}
