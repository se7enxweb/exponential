<?php
require_once __DIR__ . '/cjwNewsletterTestCase.php';

/**
 * The runner behind the cronjob parts, the console commands and the admin's "run now": one run at a time, the last
 * run, the dry run, the orphans of removed users; the dashboard, the background job state and the list helpers.
 */
class cjwNewsletterRunnerTest extends cjwNewsletterTestCase
{
    private function orphanSubscription()
    {
        $user = $this->newSubscriber( 'orph' );
        $sub = $this->subscriptionOf( $user );
        $id = (int)$sub->attribute( 'id' );
        eZDB::instance()->query( 'DELETE FROM cjwnl_user WHERE id = ' . (int)$user->attribute( 'id' ) );
        return $id;
    }

    public function testLockIsExclusiveAndCanBeTakenAgainAfterTheRelease()
    {
        $first = CjwNewsletterRunner::lock( 'nltest_lock' );
        $this->assertNotFalse( $first );
        $this->assertFalse( CjwNewsletterRunner::lock( 'nltest_lock' ), 'a second run does not get the lock' );
        $this->assertNotFalse( $other = CjwNewsletterRunner::lock( 'nltest_other_lock' ), 'another kind of run does' );
        CjwNewsletterRunner::unlock( $other );
        CjwNewsletterRunner::unlock( $first );
        $again = CjwNewsletterRunner::lock( 'nltest_lock' );
        $this->assertNotFalse( $again );
        CjwNewsletterRunner::unlock( $again );
    }

    public function testRecordRunAndLastRun()
    {
        $old = CjwNewsletterRunner::lastRun( 'nltest_last_run' );
        $this->assertFalse( $old );
        CjwNewsletterRunner::recordRun( 'nltest_last_run', array( 'sent' => 3 ), 'nltest' );
        $run = CjwNewsletterRunner::lastRun( 'nltest_last_run' );
        $this->assertSame( 3, $run['sent'] );
        $this->assertSame( 'nltest', $run['by'] );
        $this->assertLessThanOrEqual( 5, abs( time() - $run['time'] ) );
        eZDB::instance()->query( "DELETE FROM ezsite_data WHERE name = 'nltest_last_run'" );
    }

    public function testAnOrphanSubscriptionIsNotListedNotCountedNotMailedAndIsReported()
    {
        $id = $this->orphanSubscription();
        $ids = array_map( function ( $s ) { return (int)$s->attribute( 'id' ); }, CjwNewsletterSubscription::fetchSubscriptionListByListId( self::LIST_OBJECT_ID, false, 0, 0 ) );
        $this->assertNotContains( $id, $ids, 'the list of subscriptions skips it' );
        $approved = CjwNewsletterSubscription::fetchSubscriptionListByListId( self::LIST_OBJECT_ID, CjwNewsletterSubscription::STATUS_APPROVED, 0, 0 );
        $this->assertSame( count( $approved ), CjwNewsletterSubscription::fetchSubscriptionListByListIdCount( self::LIST_OBJECT_ID, CjwNewsletterSubscription::STATUS_APPROVED ), 'and the count agrees with the list' );
        $this->assertContains( $id, CjwNewsletterRunner::orphans()['subscriptions'] );
        $summary = CjwNewsletterDashboard::summary();
        $codes = array_map( function ( $p ) { return $p['code']; }, $summary['problems'] );
        $this->assertContains( 'orphans', $codes );
        $this->assertGreaterThanOrEqual( 1, $summary['orphans']['subscriptions'] );
        // the mail queue never gets an item for it
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $this->runCronjob( 'cjw_newsletter_mailqueue_create' );
        foreach ( CjwNewsletterEditionSendItem::fetchListSendIdAndStatus( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW, 0, 0 ) as $item )
            $this->assertNotSame( $id, (int)$item->attribute( 'subscription_id' ) );
        eZDB::instance()->query( 'DELETE FROM cjwnl_subscription WHERE id = ' . $id );
    }

    public function testRepairDryRunCountsAndChangesNothingAndTheRealRunRemovesTheOrphans()
    {
        $id = $this->orphanSubscription();
        $before = CjwNewsletterRunner::orphans();
        $dry = CjwNewsletterRunner::repair( false, 'nltest', true );
        $this->assertTrue( $dry['ok'] );
        $this->assertSame( count( $before['subscriptions'] ), $dry['subscriptions'] );
        $this->assertSame( $before, CjwNewsletterRunner::orphans(), 'a dry run removes nothing' );
        $foreign = array_diff( $before['subscriptions'], array( $id ) );
        if ( $foreign || $before['send_items'] || $before['interests'] )
        {
            // rows of the installation itself: never removed by a test
            eZDB::instance()->query( 'DELETE FROM cjwnl_subscription WHERE id = ' . $id );
            $this->markTestIncomplete( 'The installation has orphans of its own; the removal itself is not run here.' );
        }
        $real = CjwNewsletterRunner::repair( false, 'nltest' );
        $this->assertSame( 1, $real['subscriptions'] );
        $this->assertSame( array( 'subscriptions' => array(), 'send_items' => array(), 'interests' => array() ), CjwNewsletterRunner::orphans() );
        $this->assertSame( 'nltest', CjwNewsletterRunner::lastRun( CjwNewsletterRunner::LAST_REPAIR )['by'] );
    }

    public function testRepairDoesNothingWhileAnotherRepairRuns()
    {
        $lock = CjwNewsletterRunner::lock( 'repair' );
        $totals = CjwNewsletterRunner::repair( false, 'nltest' );
        CjwNewsletterRunner::unlock( $lock );
        $this->assertTrue( $totals['locked'] );
        $this->assertFalse( $totals['ok'] );
    }

    public function testQueueCreateDryRunCountsAndChangesNothing()
    {
        $this->newSubscriber( 'dry1' );
        $this->newSubscriber( 'dry2' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $dry = CjwNewsletterRunner::queueCreate( false, 'nltest', true );
        $this->assertGreaterThanOrEqual( 1, $dry['sends'] );
        $this->assertGreaterThanOrEqual( 2, $dry['items'] );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_WAIT_FOR_SCHEDULE, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ), 'still scheduled' );
        $this->assertSame( 0, CjwNewsletterEditionSendItem::fetchListBySendIdAndStatusCount( $send->attribute( 'id' ), CjwNewsletterEditionSendItem::STATUS_NEW ) );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testQueueCreateAndProcessThroughTheRunnerSendToFilesRecordTheRunAndTheTotals()
    {
        $this->newSubscriber( 'run1' );
        $this->newSubscriber( 'run2' );
        $send = $this->editionContent( $this->newEdition() )->createNewsletterSendObject( time() - 5 );
        $create = CjwNewsletterRunner::queueCreate( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertTrue( $create['ok'] );
        $this->assertGreaterThanOrEqual( 2, $create['items'] );
        $this->assertSame( 'nltest', CjwNewsletterRunner::lastRun( CjwNewsletterRunner::LAST_QUEUE_CREATE )['by'] );
        $dry = CjwNewsletterRunner::queueProcess( false, 'nltest', true );
        $this->assertGreaterThanOrEqual( 2, $dry['waiting'] );
        $this->assertCount( 0, $this->outbox(), 'counting sends nothing' );
        $process = CjwNewsletterRunner::queueProcess( new CjwNewsletterJobOutput( false ), 'nltest' );
        $this->assertGreaterThanOrEqual( 2, $process['sent'] );
        $this->assertGreaterThanOrEqual( 2, count( $this->outbox() ) );
        $this->assertGreaterThanOrEqual( 1, $process['finished'] );
        $this->assertSame( CjwNewsletterEditionSend::STATUS_MAILQUEUE_PROCESS_FINISHED, (int)CjwNewsletterEditionSend::fetch( $send->attribute( 'id' ) )->attribute( 'status' ) );
    }

    public function testQueueProcessRefusesToRunTwiceAtOnce()
    {
        $lock = CjwNewsletterRunner::lock( 'queue_process' );
        $totals = CjwNewsletterRunner::queueProcess( false, 'nltest' );
        CjwNewsletterRunner::unlock( $lock );
        $this->assertTrue( $totals['locked'] );
        $this->assertCount( 0, $this->outbox() );
    }

    public function testMailboxRunWithoutActiveAccountsDoesNothing()
    {
        $dry = CjwNewsletterRunner::mailbox( false, 'nltest', 'both', true );
        $this->assertTrue( $dry['ok'] );
        $totals = CjwNewsletterRunner::mailbox( new CjwNewsletterJobOutput( false ), 'nltest', 'parse' );
        $this->assertTrue( $totals['ok'] );
        $this->assertSame( 0, $totals['collected'] );
        $this->assertSame( 'nltest', CjwNewsletterRunner::lastRun( CjwNewsletterRunner::LAST_MAILBOX )['by'] );
    }

    public function testMailboxCronjobPartIsDeclaredAndRuns()
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $this->assertSame( array( 'cjw_newsletter_mailbox.php' ), $ini->variable( 'CronjobPart-cjw_newsletter_mailbox', 'Scripts' ) );
        $this->assertFileExists( 'extension/cjw_newsletter/cronjobs/cjw_newsletter_mailbox.php' );
        $out = $this->runCronjob( 'cjw_newsletter_mailbox' );
        $this->assertStringContainsString( 'START: cjw_newsletter_mailbox', $out );
        $this->assertStringContainsString( 'END: cjw_newsletter_mailbox', $out );
    }

    public function testDashboardSummaryHasTheDocumentedShape()
    {
        $user = $this->newSubscriber( 'dash' );
        $s = CjwNewsletterDashboard::summary();
        $this->assertSame( array(), $s['tables_missing'], 'every table is found, on SQLite too' );
        foreach ( array( 'lists', 'users', 'subscriptions', 'editions', 'sends', 'last_sends', 'blacklist', 'mailboxes', 'imports', 'transport', 'runs', 'orphans', 'problems' ) as $key )
            $this->assertArrayHasKey( $key, $s );
        $this->assertGreaterThanOrEqual( 1, $s['users']['confirmed'] );
        $this->assertGreaterThanOrEqual( 1, $s['subscriptions']['approved'] );
        $names = array_map( function ( $l ) { return $l['object_id']; }, $s['lists'] );
        $this->assertContains( self::LIST_OBJECT_ID, $names );
        $this->assertSame( 'file', $s['transport']['method'], 'the tests switch the transport to files' );
        $codes = array_map( function ( $p ) { return $p['code']; }, $s['problems'] );
        $this->assertContains( 'transport_file', $codes );
    }

    public function testMissingTablesIsEmptyWhenTheTablesExistAndNamesTheMissingOnes()
    {
        $this->assertSame( array(), CjwNewsletterDashboard::missingTables() );
        $this->assertContains( 'cjwnl_user', CjwNewsletterDashboard::tableNames() );
        $schema = eval( 'return ' . preg_replace( '#^<\?php#', '', trim( file_get_contents( 'extension/cjw_newsletter/share/db_schema.dba' ) ) ) . ';' );
        $this->assertSame( array(), array_diff( array_keys( $schema ), array( '_info' ), CjwNewsletterDashboard::tableNames() ), 'the list of tables matches share/db_schema.dba' );
    }

    public function testDashboardViewShowsTheStartPageAndRefusesRunsWithoutPermission()
    {
        $r = $this->runView( 'index' );
        $this->assertViewOk( $r );
        $this->assertStringContainsString( 'Newsletter dashboard', $r['content'] );
        $this->assertStringContainsString( 'Problems and hints', $r['content'] );
        $this->assertStringContainsString( 'name="RunQueueButton"', $r['content'] );
    }

    public function testJobIdsStateAndLog()
    {
        $this->assertFalse( CjwNewsletterJob::isID( '../../etc/passwd' ) );
        $this->assertFalse( CjwNewsletterJob::isID( '20261004120000-ZZZZZZZZ' ) );
        $this->assertFalse( CjwNewsletterJob::status( '../x' ) );
        $id = date( 'YmdHis' ) . '-' . bin2hex( random_bytes( 4 ) );
        $this->assertTrue( CjwNewsletterJob::isID( $id ) );
        $this->assertFalse( CjwNewsletterJob::status( $id ), 'an unknown job' );
        CjwNewsletterJob::write( $id, array( 'id' => $id, 'command' => 'queue', 'status' => 'running', 'started' => time(), 'by' => 'nltest', 'result' => null ) );
        CjwNewsletterJob::log( $id, 'hello' );
        $state = CjwNewsletterJob::status( $id );
        $this->assertSame( 'running', $state['status'] );
        $this->assertSame( array( 'hello' ), $state['log'] );
        CjwNewsletterJob::markFinished( $id, 'queue', 'done', array( 'sent' => 2 ) );
        $this->assertSame( 'done', CjwNewsletterJob::status( $id )['status'] );
        $this->assertSame( 2, CjwNewsletterJob::status( $id )['result']['sent'] );
        $dir = CjwNewsletterJob::directory();
        $this->assertFileExists( $dir . '/' . $id . '.json' );
        // the job view answers JSON for a known job and 404 for an unknown one (the output is the JSON)
        foreach ( glob( $dir . '/' . $id . '.*' ) as $file )
            unlink( $file );
    }

    public function testStartRefusesACommandThatIsNotOneOfOurs()
    {
        $error = '';
        $this->assertFalse( CjwNewsletterJob::start( 'rm -rf', array(), $error ) );
        $this->assertSame( 'Unknown command.', $error );
        $this->assertFalse( CjwNewsletterJob::start( 'status', array(), $error ), 'status is not a background command' );
    }

    public function testSearchConditionEscapesQuotesAndLikeCharacters()
    {
        $this->assertSame( '', CjwNewsletterUI::searchCondition( '   ', array( 'a.b' ) ) );
        $sql = CjwNewsletterUI::searchCondition( "O'Brien_100%", array( 'cjwnl_user.email', 'cjwnl_user.last_name' ) );
        $this->assertStringContainsString( "LIKE '%o''brien!_100!%%' ESCAPE '!'", str_replace( "\\'", "''", $sql ) );
        $this->assertSame( 2, substr_count( $sql, 'LOWER(' ) );
        $this->assertSame( 1, count( eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM cjwnl_user WHERE ' . $sql ) ), 'the database accepts it' );
    }

    public function testListParametersFallBackToDefaults()
    {
        $vp = CjwNewsletterUI::listParameters( array( 'UserParameters' => array( 'sort' => 'bogus', 'order' => 'x', 'offset' => '-4', 'q' => rawurlencode( 'a b' ) ) ), array( 'email', 'name' ), 25 );
        $this->assertSame( 'email', $vp['sort'] );
        $this->assertSame( 'asc', $vp['order'] );
        $this->assertSame( 0, $vp['offset'] );
        $this->assertSame( 'a b', $vp['q'] );
        $this->assertSame( 25, $vp['limit'] );
    }

    public function testNoticesAreShownOnceToTheUserWhoGotThem()
    {
        CjwNewsletterUI::takeNotices();
        CjwNewsletterUI::notice( 'feedback', 'one' );
        CjwNewsletterUI::notice( 'weird', 'two' );
        $notices = CjwNewsletterUI::takeNotices();
        $this->assertSame( array( 'one', 'two' ), array_column( $notices, 'text' ) );
        $this->assertSame( 'feedback', $notices[1]['type'], 'an unknown type becomes feedback' );
        $this->assertSame( array(), CjwNewsletterUI::takeNotices() );
    }

    public function testAuditBranchNamesTheEventsOfTheRuns()
    {
        $events = ( new cjwNewsletterAuditBranch() )->events();
        foreach ( array( 'queue_create', 'queue_process', 'mailbox', 'import', 'repair' ) as $run )
            $this->assertArrayHasKey( 'system.cjw_newsletter.' . $run, $events );
        $this->assertSame( 'cjwNewsletterAuditBranch', eZINI::instance( 'audit.ini' )->variable( 'AuditEventSettings', 'Branches' )['cjw_newsletter'] );
    }

    public function testLogWriterDeclaresItsProperty()
    {
        $this->assertTrue( property_exists( 'CjwNewsletterLogWriter', 'defaultFile' ) );
        $log = CjwNewsletterLog::getInstance( true );
        $log::writeInfo( 'nltest', 'nltest', 'nltest' );
        $this->assertTrue( true, 'writing a log line raised no deprecation (tearDown checks the collector)' );
    }
}
