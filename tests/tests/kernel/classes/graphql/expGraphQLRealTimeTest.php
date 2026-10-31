<?php
/**
 * The real time of the GraphQL API without a database (doc/eng-us/features/6.0/graphql.md, "Real time: webhooks and
 * subscriptions"): the event journal (append, read from a position, a half-written line, a new generation, a reader
 * behind the files kept), the filters of webhooks and subscriptions, the webhooks (their settings, the signature and
 * its check, the body, the outbox with retries, back-off and giving up, the delivery log, the cursor), and the
 * subscriptions (the limits, the SSE messages, the stream loop on a fake clock, which opens no database connection
 * while it waits, the policies against a removed object's record, and with the library the type Subscription and the
 * checking of a subscription document).
 *
 * Files under var/tmp/graphql-realtime-tests/, removed after each test.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGraphQLRealTimeTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $dir;

    /** @var callable|null the exception handler before the test */
    protected $handler;

    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function setUp(): void
    {
        $this->handler = self::exceptionHandler();
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/graphql-realtime-tests/' . $this->name() . '-' . getmypid();
        if ( !is_dir( $this->dir ) )
            mkdir( $this->dir, 0770, true );
        expGraphQLEventListener::reset();
    }

    protected function tearDown(): void
    {
        self::removeTree( $this->dir );
        @rmdir( dirname( $this->dir ) ); // when it is empty
        expGraphQLEventListener::$journal = null;
        expGraphQLEventListener::$forceActive = null;
        expGraphQLEventListener::reset();
        // eZExecution registers its handlers when the listener asks for its cleanup handler
        while ( self::exceptionHandler() !== $this->handler && self::exceptionHandler() !== null )
            restore_exception_handler();
    }

    protected static function removeTree( $dir )
    {
        if ( !is_dir( $dir ) )
            return;
        foreach ( (array)scandir( $dir ) as $name )
        {
            if ( $name === '.' || $name === '..' )
                continue;
            $path = $dir . '/' . $name;
            is_dir( $path ) && !is_link( $path ) ? self::removeTree( $path ) : unlink( $path );
        }
        rmdir( $dir );
    }

    protected function journal( $maxBytes = 1048576, $keep = 3 )
    {
        return new expGraphQLEvents( $this->dir . '/events', $maxBytes, $keep );
    }

    protected static function event( $event, $objectId, array $more = array() )
    {
        return $more + array( 'event' => $event, 'objectId' => $objectId, 'classIdentifier' => 'article', 'classId' => 2, 'sectionId' => 1, 'ownerId' => 14,
                      'nodeIds' => array( 100 + $objectId ), 'parentNodeIds' => array( 2 ), 'pathStrings' => array( '/1/2/' . ( 100 + $objectId ) . '/' ),
                      'language' => 'eng-GB', 'languages' => array( 'eng-GB' ), 'name' => 'Article ' . $objectId );
    }

    // ---- the journal ------------------------------------------------------------------------------------------------

    public function testTheJournalIsReadFromAPosition()
    {
        $journal = $this->journal();
        $start = $journal->end();
        $this->assertSame( '1.0', $start, 'an empty journal starts at generation 1' );
        $after = $journal->append( array( self::event( 'publish', 1 ), self::event( 'update', 2 ) ) );
        $this->assertTrue( expGraphQLEvents::isPosition( $after ) );
        $this->assertSame( $after, $journal->end() );

        $read = $journal->read( $start );
        $this->assertSame( array( 'publish', 'update' ), array_column( $read['events'], 'event' ) );
        $this->assertSame( $after, $read['position'] );
        $this->assertSame( $read['events'][1]['_position'], $after, 'each event says where the read after it starts' );
        $this->assertFalse( $read['lost'] );
        $this->assertSame( 1, $read['events'][0]['v'] );
        $this->assertMatchesRegularExpression( '/^\d{16}-[0-9a-f]{8}$/', $read['events'][0]['id'] );

        // from the first event's position only the second is read; from the end nothing
        $this->assertSame( array( 'update' ), array_column( $journal->read( $read['events'][0]['_position'] )['events'], 'event' ) );
        $this->assertSame( array(), $journal->read( $after )['events'] );
        $this->assertSame( array(), $journal->read( null )['events'], 'no position: from the end' );
        $this->assertSame( array( 'publish' ), array_column( $journal->read( $start, 1 )['events'], 'event' ), 'at most the limit' );

        // a line still being written is left for the next read
        file_put_contents( $journal->file( 1 ), '{"v":1,"event":"mo', FILE_APPEND );
        $this->assertSame( array(), $journal->read( $after )['events'] );
        $this->assertSame( $after, $journal->read( $after )['position'] );
    }

    public function testTheJournalStartsANewFileAndKeepsTheNewest()
    {
        $journal = $this->journal( 1024, 2 );
        $start = $journal->end();
        for ( $i = 1; $i <= 30; $i++ )
            $journal->append( array( self::event( 'publish', $i ) ) );
        $generations = $journal->generations();
        $this->assertGreaterThan( 2, $journal->currentGeneration() );
        $this->assertCount( 2, $generations, 'only the newest JournalKeep files stay' );

        // a reader behind the files kept goes on from the oldest one, and says so
        $read = $journal->read( $start, 1000 );
        $this->assertTrue( $read['lost'] );
        $this->assertNotEmpty( $read['events'] );
        $this->assertSame( 30, (int)end( $read['events'] )['objectId'], 'and reads on across the files to the newest event' );
        $this->assertSame( $journal->end(), $read['position'] );

        // a reader within the files kept loses nothing across a new file
        $position = $journal->end();
        for ( $i = 31; $i <= 33; $i++ )
            $journal->append( array( self::event( 'update', $i ) ) );
        $read = $journal->read( $position, 1000 );
        $this->assertFalse( $read['lost'] );
        $this->assertSame( range( 31, 33 ), array_map( 'intval', array_column( $read['events'], 'objectId' ) ) );
    }

    public function testARecordHasTheTypesOfItsFields()
    {
        $record = expGraphQLEvents::record( array( 'event' => 'move', 'objectId' => '7', 'nodeIds' => array( '9', 9 ), 'languages' => 'eng-GB',
                                                   'name' => 'A', 'unknown' => 'x', 'time' => 1767225600.12345 ) );
        $this->assertSame( 7, $record['objectId'] );
        $this->assertSame( array( 9 ), $record['nodeIds'] );
        $this->assertSame( array( 'eng-GB' ), $record['languages'] );
        $this->assertArrayNotHasKey( 'unknown', $record );
        $this->assertSame( 1767225600.123, $record['time'] );
        $public = expGraphQLEvents::publicFields( $record );
        $this->assertSame( '2026-01-01T00:00:00.123Z', $public['time'] );
        $this->assertSame( array(), $public['parentNodeIds'] );
        $this->assertArrayNotHasKey( 'classId', $public, 'the internal fields stay out' );
        $this->assertArrayNotHasKey( 'pathStrings', $public );
    }

    public function testTheFiltersOfWebhooksAndSubscriptions()
    {
        $event = self::event( 'publish', 5 );
        $this->assertTrue( expGraphQLEvents::matches( $event, array() ) );
        $this->assertTrue( expGraphQLEvents::matches( $event, array( 'events' => array( 'publish', 'update' ), 'classes' => array( 'article' ) ) ) );
        $this->assertFalse( expGraphQLEvents::matches( $event, array( 'events' => array( 'trash' ) ) ) );
        $this->assertFalse( expGraphQLEvents::matches( $event, array( 'classes' => array( 'folder' ) ) ) );
        $this->assertTrue( expGraphQLEvents::matches( $event, array( 'parentId' => 2 ) ) );
        $this->assertFalse( expGraphQLEvents::matches( $event, array( 'parentId' => 1 ) ), 'parentId: directly below only' );
        $this->assertTrue( expGraphQLEvents::matches( $event, array( 'subtreeId' => 1 ) ), 'subtreeId: anywhere below' );
        $this->assertTrue( expGraphQLEvents::matches( $event, array( 'subtreeId' => 105 ) ), 'subtreeId: the node itself' );
        $this->assertFalse( expGraphQLEvents::matches( $event, array( 'subtreeId' => 43 ) ) );
        $this->assertTrue( expGraphQLEvents::matches( $event, array( 'rootNodeIds' => array( 2 ), 'languages' => array( 'eng-GB', 'ger-DE' ) ) ) );
        $this->assertFalse( expGraphQLEvents::matches( $event, array( 'languages' => array( 'ger-DE' ) ) ) );
        $move = array( 'event' => 'move', 'objectId' => 5, 'nodeIds' => array( 105 ), 'newParentNodeId' => 60, 'parentNodeIds' => array( 60 ) );
        $this->assertTrue( expGraphQLEvents::matches( $move, array( 'languages' => array( 'ger-DE' ) ) ), 'an event without languages concerns every language' );
        $this->assertTrue( expGraphQLEvents::matches( $move, array( 'parentId' => 60 ) ) );
    }

    public function testTheListenerWritesItsNotesAtTheEndOfTheRequestOnly()
    {
        expGraphQLEventListener::$journal = $this->journal();
        expGraphQLEventListener::$forceActive = false;
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 5 ) );
        $this->assertSame( array(), expGraphQLEventListener::pending(), 'nobody listens: nothing is noted' );

        expGraphQLEventListener::$forceActive = true;
        $start = expGraphQLEventListener::$journal->end();
        expGraphQLEventListener::mutation( 'createContent', array( 'contentId' => 5, 'nodeId' => 105, 'version' => 1 ) );
        $pending = expGraphQLEventListener::pending();
        $this->assertCount( 1, $pending );
        $this->assertSame( array( 'event' => 'mutation', 'mutation' => 'createContent', 'objectId' => 5, 'nodeIds' => array( 105 ), 'version' => 1 ),
                           array_intersect_key( $pending[0], array_flip( array( 'event', 'mutation', 'objectId', 'nodeIds', 'version' ) ) ) );
        $this->assertSame( array(), expGraphQLEventListener::$journal->read( $start )['events'], 'nothing written before the end of the request' );
    }

    // ---- the webhooks -----------------------------------------------------------------------------------------------

    protected function webhooks( array $endpoints, array $settings = array() )
    {
        $list = array();
        foreach ( $endpoints as $name => $endpoint )
            $list[$name] = expGraphQLWebhooks::endpoint( $name, $endpoint );
        $webhooks = new expGraphQLWebhooks( $this->dir . '/webhooks', $this->journal(), $list, $settings + array( 'RetryBackoff' => 30, 'MaxAttempts' => 3 ) );
        $webhooks->secretReader = function ( $name ) { return $name === 'graphql.webhook.missing' ? null : 'secret-of-' . $name; };
        $webhooks->now = 1767225600;
        return $webhooks;
    }

    public function testAnEndPointIsCheckedWhenItIsRead()
    {
        $ok = expGraphQLWebhooks::endpoint( 'shop', array( 'URL' => 'https://shop.example.com/hook', 'Events' => array( 'publish', '', 'publish' ) ) );
        $this->assertSame( '', $ok['problem'] );
        $this->assertSame( array( 'publish' ), $ok['events'] );
        $this->assertSame( 'graphql.webhook.shop', $ok['secret'], 'the default name of the signing secret' );
        $this->assertSame( 'event', $ok['payload'] );
        $problem = function ( array $settings, $name = 'shop' ) { return expGraphQLWebhooks::endpoint( $name, $settings )['problem']; };
        $this->assertStringContainsString( 'URL', $problem( array( 'URL' => 'ftp://x' ) ) );
        $this->assertStringContainsString( 'password', $problem( array( 'URL' => 'https://u:p@x.example.com/' ) ) );
        $this->assertStringContainsString( 'unknown event', $problem( array( 'URL' => 'https://x.example.com/', 'Events' => array( 'created' ) ) ) );
        $this->assertStringContainsString( 'Query', $problem( array( 'URL' => 'https://x.example.com/', 'Payload' => 'query' ) ) );
        $this->assertStringContainsString( 'name', $problem( array( 'URL' => 'https://x.example.com/' ), 'Shop-1' ) );
        $this->assertSame( '', $problem( array( 'URL' => 'http://localhost:8099/hook', 'Secret' => 'env:SHOP_SECRET' ) ) );
    }

    public function testTheSignatureAndItsCheck()
    {
        $signature = expGraphQLWebhooks::signature( 'k', 1767225600, '{"a":1}' );
        $this->assertSame( 'sha256=' . hash_hmac( 'sha256', '1767225600.{"a":1}', 'k' ), $signature );
        $this->assertSame( '', expGraphQLWebhooks::verify( 'k', '1767225600', '{"a":1}', $signature, 300, 1767225700 ) );
        $this->assertSame( 'bad_signature', expGraphQLWebhooks::verify( 'k', '1767225600', '{"a":2}', $signature, 300, 1767225700 ) );
        $this->assertSame( 'bad_signature', expGraphQLWebhooks::verify( 'other', '1767225600', '{"a":1}', $signature, 300, 1767225700 ) );
        $this->assertSame( 'stale_timestamp', expGraphQLWebhooks::verify( 'k', '1767225600', '{"a":1}', $signature, 300, 1767226000 ), 'a replay after 300 s' );
        $this->assertSame( 'stale_timestamp', expGraphQLWebhooks::verify( 'k', 'x', '{"a":1}', $signature ) );
        $this->assertSame( 64, strlen( expGraphQLWebhooks::newSecret() ) );
    }

    public function testTheRequestOfADelivery()
    {
        $webhooks = $this->webhooks( array( 'shop' => array( 'URL' => 'https://shop.example.com/hook' ) ) );
        $event = expGraphQLEvents::record( self::event( 'publish', 5, array( 'id' => '1767225600000000-0a0b0c0d', 'time' => 1767225600 ) ) );
        list( $body, $headers ) = $webhooks->request( $webhooks->all()['shop'], $event, 'd-1', 2, 'k', 1767225601 );
        $payload = json_decode( $body, true );
        $this->assertSame( 'publish', $payload['data']['contentEvent']['event'] );
        $this->assertSame( 5, $payload['data']['contentEvent']['objectId'] );
        $this->assertSame( '2026-01-01T00:00:00.000Z', $payload['data']['contentEvent']['time'] );
        $this->assertSame( array( 'schema' => 'exp-graphql-webhook/1', 'webhook' => 'shop', 'delivery' => 'd-1', 'attempt' => 2 ), $payload['extensions'] );
        $this->assertContains( 'X-Exponential-Event: publish', $headers );
        $this->assertContains( 'X-Exponential-Event-Id: 1767225600000000-0a0b0c0d', $headers );
        $this->assertContains( 'X-Exponential-Delivery: d-1', $headers );
        $this->assertContains( 'X-Exponential-Timestamp: 1767225601', $headers );
        $this->assertContains( 'X-Exponential-Signature: ' . expGraphQLWebhooks::signature( 'k', 1767225601, $body ), $headers );
        $this->assertStringNotContainsString( 'secret', $body );
        $this->assertContains( 'X-Exponential-Signature: sha256=…', expGraphQLWebhooks::maskSignature( $headers ) );

        // Payload=query: the answer of the persisted query, the event in the extensions
        $search = $this->webhooks( array( 'search' => array( 'URL' => 'https://search.example.com/', 'Payload' => 'query', 'Query' => 'article_for_index' ) ) );
        $search->queryRunner = function ( array $endpoint, array $event ) { return array( 'data' => array( 'content' => array( 'name' => $event['name'] ) ) ); };
        list( $body ) = $search->request( $search->all()['search'], $event, 'd-2', 1, 'k' );
        $payload = json_decode( $body, true );
        $this->assertSame( array( 'content' => array( 'name' => 'Article 5' ) ), $payload['data'] );
        $this->assertSame( 5, $payload['extensions']['event']['objectId'] );
    }

    public function testDeliveriesAreRetriedWithBackOffAndThenGivenUp()
    {
        $webhooks = $this->webhooks( array( 'shop' => array( 'URL' => 'https://shop.example.com/hook', 'Events' => array( 'publish' ) ),
                                            'all' => array( 'URL' => 'https://all.example.com/hook' ),
                                            'missing' => array( 'URL' => 'https://missing.example.com/hook' ) ) );
        $journal = $this->journal();
        $journal->append( array( self::event( 'publish', 1 ) ) );

        // the first run starts at the end of the journal: what happened before the webhook is not sent
        $first = $webhooks->dispatch();
        $this->assertSame( array( 0, 0 ), array( $first['events'], $first['queued'] ) );
        $this->assertSame( $journal->end(), $webhooks->cursor() );

        $journal->append( array( self::event( 'publish', 2 ), self::event( 'trash', 3 ) ) );
        $dispatched = $webhooks->dispatch();
        $this->assertSame( 2, $dispatched['events'] );
        $this->assertSame( 5, $dispatched['queued'], 'shop wants the publish, all and missing want both' );
        $this->assertSame( 0, $webhooks->dispatch()['events'], 'the cursor moved on' );

        $sent = array();
        $answer = array( 'https://shop.example.com/hook' => array( 200, null ), 'https://all.example.com/hook' => array( 503, null ),
                         'https://missing.example.com/hook' => array( 200, null ) );
        $webhooks->transport = function ( $url, $body, array $headers, $timeout ) use ( &$sent, &$answer ) {
            $sent[] = array( $url, json_decode( $body, true )['data']['contentEvent']['event'] );
            return $answer[$url];
        };
        $report = $webhooks->deliver();
        $this->assertSame( 1, $report['sent'] );
        $this->assertSame( 4, $report['failed'], 'all answered 503 twice; missing has no secret twice' );
        $this->assertSame( array( array( 'https://shop.example.com/hook', 'publish' ), array( 'https://all.example.com/hook', 'publish' ),
                                  array( 'https://all.example.com/hook', 'trash' ) ), $sent, 'nothing is sent without a secret' );

        // not due yet: nothing is sent
        $sent = array();
        $this->assertSame( 0, $webhooks->deliver()['sent'] );
        $this->assertSame( array(), $sent );
        $this->assertSame( 30, $webhooks->backoff( 1 ) );
        $this->assertSame( 60, $webhooks->backoff( 2 ) );
        $this->assertSame( 3600, $webhooks->backoff( 20 ), 'at most MaxBackoff' );

        // after the back-off: all answers now, missing still has no secret
        $webhooks->now += 31;
        $answer['https://all.example.com/hook'] = array( 204, null );
        $report = $webhooks->deliver();
        $this->assertSame( 2, $report['sent'] );
        $this->assertSame( 2, $report['failed'] );

        // the third failed try of missing gives up: kept in failed/
        $webhooks->now += 61;
        $report = $webhooks->deliver();
        $this->assertSame( 2, $report['gaveUp'] );
        $this->assertSame( array(), $webhooks->outbox() );
        $stats = $webhooks->stats();
        $this->assertSame( 2, $stats['missing']['failed'] );
        $this->assertSame( 'gave-up', $stats['missing']['last']['result'] );
        $this->assertSame( 'sent', $stats['shop']['last']['result'] );
        $log = $webhooks->recent( 100 );
        $this->assertCount( 11, $log, 'every try is a line of the log: 5, then 4, then 2' );
        foreach ( $log as $line )
            $this->assertStringNotContainsString( 'secret-of', json_encode( $line ), 'never a secret in the log' );
        $missing = array_values( array_filter( $log, function ( $line ) { return $line['endpoint'] === 'missing'; } ) );
        $this->assertCount( 6, $missing );
        $this->assertStringContainsString( 'not set', $missing[0]['error'] );
    }

    public function testAnEndPointOfSiteaccessesGetsTheirContentOnly()
    {
        $webhooks = $this->webhooks( array( 'ger' => array( 'URL' => 'https://ger.example.com/', 'SiteAccessList' => array( 'ger' ) ) ) );
        $webhooks->siteAccessScope = function ( $siteAccess ) { return array( 'rootNodeIds' => array( 60 ), 'languages' => array( 'ger-DE' ) ); };
        $endpoint = $webhooks->all()['ger'];
        $this->assertFalse( $webhooks->wants( $endpoint, self::event( 'publish', 5 ) ), 'below node 2, in English' );
        $this->assertTrue( $webhooks->wants( $endpoint, self::event( 'publish', 5, array( 'pathStrings' => array( '/1/2/60/105/' ), 'languages' => array( 'ger-DE' ), 'language' => 'ger-DE' ) ) ) );
        $disabled = expGraphQLWebhooks::endpoint( 'off', array( 'URL' => 'https://off.example.com/', 'Enabled' => 'disabled' ) );
        $this->assertFalse( $webhooks->wants( $disabled, self::event( 'publish', 5 ) ) );
    }

    public function testATestDeliveryAndTheChangesOfTheSettings()
    {
        $webhooks = $this->webhooks( array( 'shop' => array( 'URL' => 'https://shop.example.com/hook' ) ) );
        $webhooks->transport = function () { return array( 0, 'Could not resolve host' ); };
        $result = $webhooks->sendTest( 'shop' );
        $this->assertSame( 'Could not resolve host', $result['error'] );
        $this->assertSame( 'ping', json_decode( $result['body'], true )['data']['contentEvent']['event'] );
        $this->assertContains( 'X-Exponential-Signature: sha256=…', $result['headers'] );
        $this->assertNotNull( $webhooks->queueTest( 'shop' ) );
        $this->assertCount( 1, $webhooks->outbox() );

        $changes = expGraphQLWebhooks::addChanges( 'shop', array( 'URL' => 'https://shop.example.com/hook', 'Events' => array( 'publish', 'update' ),
                                                                  'ClassList' => array( 'article' ) ) );
        $rows = array_map( function ( $c ) { return array( $c['op'], $c['block'], $c['variable'], $c['value'] ); }, $changes );
        $this->assertContains( array( 'add', 'GraphQLWebhooks', 'EndpointList', 'shop' ), $rows );
        $this->assertContains( array( 'set', 'GraphQLWebhook_shop', 'URL', 'https://shop.example.com/hook' ), $rows );
        $this->assertContains( array( 'add', 'GraphQLWebhook_shop', 'Events', 'update' ), $rows );
        $this->assertContains( array( 'set', 'GraphQLWebhook_shop', 'Secret', 'graphql.webhook.shop' ), $rows );
        foreach ( $changes as $change )
            $this->assertSame( 'global', $change['scope'], 'settings/override' );
        try
        {
            expGraphQLWebhooks::addChanges( 'shop', array( 'URL' => 'mailto:x@example.com' ) );
            $this->fail( 'a URL that is not one' );
        }
        catch ( expGraphQLError $e )
        {
            $this->assertSame( expGraphQLError::BAD_USER_INPUT, $e->errorCode() );
        }
        $this->assertSame( array( array( 'remove', 'GraphQLWebhooks', 'EndpointList', 'shop' ), array( 'removeBlock', 'GraphQLWebhook_shop', '', '' ) ),
                           array_map( function ( $c ) { return array( $c['op'], $c['block'], $c['variable'], $c['value'] ); }, expGraphQLWebhooks::removeChanges( 'shop' ) ) );
    }

    // ---- the subscriptions ------------------------------------------------------------------------------------------

    public function testTheLimitsOfOpenSubscriptions()
    {
        $now = 1767225600;
        $one = new expGraphQLSubscriptions( $this->dir . '/subscriptions', array( 'max' => 4, 'perUser' => 2, 'perAddress' => 3, 'heartbeat' => 15 ) );
        $this->assertNull( $one->acquire( 'user:14', '10.0.0.1', $now ) );
        $two = new expGraphQLSubscriptions( $this->dir . '/subscriptions', $one->settings() );
        $this->assertNull( $two->acquire( 'user:14', '10.0.0.2', $now ) );
        $three = new expGraphQLSubscriptions( $this->dir . '/subscriptions', $one->settings() );
        $this->assertStringContainsString( '2', (string)$three->acquire( 'user:14', '10.0.0.3', $now ), 'per user' );
        $this->assertNull( $three->acquire( '', '10.0.0.1', $now ), 'anonymous: by address only' );
        $four = new expGraphQLSubscriptions( $this->dir . '/subscriptions', $one->settings() );
        $this->assertNull( $four->acquire( '', '10.0.0.1', $now ) );
        $five = new expGraphQLSubscriptions( $this->dir . '/subscriptions', $one->settings() );
        $this->assertNotNull( $five->acquire( '', '10.0.0.9', $now ), 'at most max on the installation' );
        $this->assertCount( 4, $one->open( $now ) );
        $one->release();
        $this->assertCount( 3, $one->open( $now ) );
        $this->assertNull( $five->acquire( '', '10.0.0.9', $now ) );
        $this->assertNotNull( ( new expGraphQLSubscriptions( $this->dir . '/subscriptions', $one->settings() ) )->acquire( '', '10.0.0.1', $now ), 'per address' );

        // a process that stopped beating is not counted (and its file goes)
        $two->beat( $now + 100 );
        $this->assertCount( 1, $one->open( $now + 100 ), 'only the one that beat' );
    }

    public function testTheMessagesOfTheStream()
    {
        $this->assertSame( "event: next\nid: 1.20\ndata: {\"data\":1}\n\n", expGraphQLSubscriptions::sseMessage( 'next', '{"data":1}', '1.20' ) );
        $this->assertSame( "event: complete\ndata:\n\n", expGraphQLSubscriptions::sseMessage( 'complete' ) );
        $this->assertSame( ": heartbeat\nid: 2.0\n\n", expGraphQLSubscriptions::sseMessage( null, null, '2.0', 'heartbeat' ) );
        $this->assertSame( "event: next\ndata: a\ndata: b\n\n", expGraphQLSubscriptions::sseMessage( 'next', "a\r\nb" ) );
    }

    public function testTheStreamSendsWhatMatchesAndHoldsNoDatabaseWhileItWaits()
    {
        $journal = $this->journal();
        $subscriptions = new expGraphQLSubscriptions( $this->dir . '/subscriptions', array( 'heartbeat' => 15, 'maxDuration' => 60, 'poll' => 500 ) );
        $prepared = array( 'filter' => array( 'events' => array( 'publish', 'update' ), 'classes' => array( 'article' ), 'parentId' => 2, 'subtreeId' => null ) );
        $time = 1000.0;
        $written = '';
        $database = array();
        $delivered = array();
        $start = $journal->end();
        $steps = 0;
        $hooks = array(
            'clock' => function () use ( &$time ) { return $time; },
            'sleep' => function ( $ms ) use ( &$time, &$steps, $journal ) {
                $time += $ms / 1000;
                $steps++;
                // the events arrive while the stream waits
                if ( $steps === 2 )
                    $journal->append( array( self::event( 'publish', 1 ), self::event( 'trash', 2 ), self::event( 'update', 3, array( 'parentNodeIds' => array( 60 ) ) ),
                                             self::event( 'update', 4 ), self::event( 'publish', 5 ) ) );
            },
            'database' => function ( $open ) use ( &$database ) { $database[] = $open ? 'open' : 'close'; },
        );
        $deliver = function ( array $event ) use ( &$delivered ) {
            $delivered[] = $event['objectId'];
            // object 4 may not be read by this subscriber
            return $event['objectId'] === 4 ? null : json_encode( array( 'data' => array( 'contentPublished' => array( 'objectId' => $event['objectId'] ) ) ) );
        };
        $write = function ( $text ) use ( &$written ) { $written .= $text; return true; };
        $result = $subscriptions->stream( $journal, $prepared, $start, $deliver, $write, $hooks );

        $this->assertSame( 'duration', $result['reason'], 'ends after SubscriptionMaxDuration' );
        $this->assertSame( array( 1, 4, 5 ), $delivered, 'only what the filter matches is delivered: not the trash, not the update below 60' );
        $this->assertSame( 2, $result['events'] );
        $this->assertSame( array( 'open', 'close', 'open', 'close', 'open', 'close' ), $database, 'a connection per event delivered, closed again' );
        $this->assertStringStartsWith( ": subscribed\n\n", $written );
        $this->assertSame( 2, substr_count( $written, "event: next\n" ) );
        $this->assertStringContainsString( '"objectId":5', $written );
        $this->assertStringContainsString( ': heartbeat', $written );
        $this->assertStringEndsWith( "id: " . $journal->end() . "\n\n", $written, 'the client resumes at the end' );
        $this->assertSame( $journal->end(), $result['position'] );
    }

    public function testTheStreamEndsWhenTheClientLeavesOrItIsIdle()
    {
        $journal = $this->journal();
        $time = 0.0;
        $hooks = array( 'clock' => function () use ( &$time ) { return $time; }, 'sleep' => function ( $ms ) use ( &$time ) { $time += $ms / 1000; } );
        $prepared = array( 'filter' => array() );
        $never = function () { return 'x'; };
        $idle = new expGraphQLSubscriptions( $this->dir . '/s', array( 'heartbeat' => 15, 'maxDuration' => 280, 'idleTimeout' => 20, 'poll' => 1000 ) );
        $result = $idle->stream( $journal, $prepared, null, $never, function () { return true; }, $hooks );
        $this->assertSame( 'idle', $result['reason'] );
        $this->assertEqualsWithDelta( 20.0, $time, 1.0 );

        $time = 0.0;
        $calls = 0;
        $gone = function () use ( &$calls ) { return ++$calls < 2; };
        $result = $idle->stream( $journal, $prepared, null, $never, $gone, $hooks + array() );
        $this->assertSame( 'gone', $result['reason'], 'the heartbeat finds the client gone' );

        $time = 0.0;
        $result = $idle->stream( $journal, $prepared, null, $never, function () { return true; }, $hooks + array( 'aborted' => function () { return true; } ) );
        $this->assertSame( 'gone', $result['reason'] );
    }

    public function testARemovedObjectIsSentToWhoCouldReadIt()
    {
        $event = self::event( 'trash', 5, array( 'stateIds' => array( 1 ) ) );
        $this->assertTrue( expGraphQLSubscriptions::snapshotReadable( array( 'accessWord' => 'yes' ), $event, 10 ) );
        $this->assertFalse( expGraphQLSubscriptions::snapshotReadable( array( 'accessWord' => 'no' ), $event, 10 ) );
        $limited = function ( array $policies ) { return array( 'accessWord' => 'limited', 'policies' => $policies ); };
        $this->assertTrue( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Class' => array( 2 ), 'Section' => array( 1 ) ) ) ), $event, 10 ) );
        $this->assertFalse( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Class' => array( 3 ) ) ) ), $event, 10 ) );
        $this->assertTrue( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Class' => array( 3 ) ), 'p2' => array( 'Subtree' => array( '/1/2/' ) ) ) ), $event, 10 ),
                           'any policy that holds' );
        $this->assertFalse( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Subtree' => array( '/1/43/' ) ) ) ), $event, 10 ) );
        $this->assertTrue( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Owner' => array( 1 ) ) ) ), $event, 14 ) );
        $this->assertFalse( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Owner' => array( 1 ) ) ) ), $event, 10 ) );
        $this->assertTrue( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'Node' => array( 105 ), 'StateGroup_lock' => array( 1 ) ) ) ), $event, 10 ) );
        $this->assertFalse( expGraphQLSubscriptions::snapshotReadable( $limited( array( 'p1' => array( 'SiteAccess' => array( 1 ) ) ) ), $event, 10 ),
                            'a limitation it cannot judge refuses' );
    }

    public function testTheSubscriptionTypeAndTheCheckOfADocument()
    {
        if ( !expGraphQL::loadLibrary() )
        {
            if ( getenv( 'CI' ) )
                $this->fail( 'webonyx/graphql-php is not installed (composer.json requires it)' );
            $this->markTestSkipped( 'webonyx/graphql-php is not installed; set GRAPHQL_LIBRARY_PATH' );
        }
        $attribute = array( 'id' => 1, 'identifier' => 'title', 'datatype' => 'ezstring', 'name' => 'Title', 'description' => '', 'required' => false, 'options' => array() );
        $catalogue = expGraphQLClassCatalogue::filtered( array( array( 'id' => 2, 'identifier' => 'article', 'name' => 'Article', 'description' => '',
                                                                       'attributes' => array( $attribute ) ) ),
                                                         array( 'ClassList' => array(), 'ExcludedClassList' => array(), 'HiddenFieldList' => array(), 'HiddenDatatypeList' => array() ) );
        $builder = new expGraphQLSchemaBuilder( $catalogue, new expGraphQLNaming( 'identifier' ) );
        $schema = $builder->schema();
        $this->assertNotNull( $schema->getSubscriptionType() );
        $this->assertSame( array_keys( expGraphQLSubscriptions::FIELDS ), array_keys( $schema->getSubscriptionType()->getFields() ) );
        $sdl = $builder->sdl();
        $this->assertStringContainsString( 'type Subscription {', $sdl );
        $this->assertMatchesRegularExpression( '/contentPublished\(.*?classes: \[String!\].*?parentId: Int.*?subtreeId: Int.*?\): ContentEvent\n/s', $sdl );
        $this->assertStringContainsString( 'enum ContentEventKind {', $sdl );

        $prepared = expGraphQLSubscriptions::prepare( $schema, 'subscription($c: [String!]) { contentPublished(classes: $c, parentId: 2) { event objectId name } }',
                                                      array( 'c' => array( 'article' ) ), null, true );
        $this->assertArrayNotHasKey( 'error', $prepared );
        $this->assertSame( 'contentPublished', $prepared['field'] );
        $this->assertSame( array( 'events' => array( 'publish', 'update' ), 'classes' => array( 'article' ), 'parentId' => 2, 'subtreeId' => null ), $prepared['filter'] );

        $events = expGraphQLSubscriptions::prepare( $schema, 'subscription { contentEvents(events: [move, hide]) { event } }', null, null, true );
        $this->assertSame( array( 'move', 'hide' ), $events['filter']['events'] );

        $two = expGraphQLSubscriptions::prepare( $schema, 'subscription { contentMoved { event } contentHidden { event } }', null, null, true );
        $this->assertSame( expGraphQLError::VALIDATION, $two['error'][1], 'one root field' );
        $unknown = expGraphQLSubscriptions::prepare( $schema, 'subscription { contentCreated { event } }', null, null, true );
        $this->assertSame( expGraphQLError::VALIDATION, $unknown['error'][1] );
        $this->assertSame( expGraphQLError::PARSE, expGraphQLSubscriptions::prepare( $schema, 'subscription {', null, null, true )['error'][1] );
        $this->assertSame( 'query', expGraphQLSubscriptions::prepare( $schema, '{ translations { code } }', null, null, true )['operation'] );

        // the selection runs with the event as its root (content needs a database: not asked for here)
        $context = new expGraphQLContext( null, expGraphQLContext::AUTH_CONSOLE, new expGraphQLRepository( $catalogue ) );
        $event = expGraphQLEvents::record( self::event( 'update', 5, array( 'version' => 3 ) ) );
        $result = \GraphQL\GraphQL::executeQuery( $schema, $prepared['document'], $event, $context, $prepared['variables'], null, null, array() )->toArray();
        $this->assertSame( array( 'contentPublished' => array( 'event' => 'update', 'objectId' => 5, 'name' => 'Article 5' ) ), $result['data'] );

        // and /api/graphql refuses a subscription: it is a stream
        $answer = ( new expGraphQLServer( $builder ) )->execute( 'subscription { contentMoved { event } }', null, null, $context );
        $this->assertSame( expGraphQLError::BAD_USER_INPUT, $answer['errors'][0]['extensions']['code'] );
        $this->assertStringContainsString( '/api/graphql/subscribe', $answer['errors'][0]['message'] );
    }
}

?>
