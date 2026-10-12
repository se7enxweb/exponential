<?php
/**
 * The console's sources of extensions (doc/bc/6.0/audit.md, "An extension's source in the console"):
 * audit.ini [AuditSourceSettings] Sources[] and expAuditSourceRegistry.
 *
 *  AS-01 — no source registered (the shipped settings): every answer is empty, the channel filter is the channel
 *          list, an unknown object type has no link; the views look as without the registry
 *  AS-02 — a source of a channel of its own: registered, labelled, found by its channel; its event fields, links,
 *          panel and charts reach the views cleaned; expAuditConsole::linkFor() asks it for its own types
 *  AS-03 — what keeps a source out, reported in status(): a missing class, a class that does not implement the
 *          interface, a malformed identifier, a channel not in Channels[], a non-empty problem()
 *  AS-04 — only module/view paths with view parameters: a query string, an absolute URL, a leading slash and ".."
 *          are dropped
 *  AS-05 — a source that throws is left out of that call; the view goes on
 *  AS-06 — the Channel limitation of audit/read: a user who may not read the source's channel sees nothing of it
 *  AS-07 — an extension's branch routes its names into the source's channel, and the record lands in that
 *          channel's own file and chain; without the channel in Channels[] it falls back to DefaultChannel; the console shows the branch's own label
 *  AS-08 — audit/read's Channel limitation lists a channel an extension adds, named after its source
 *  AS-09 — the CSV export of a source's channel has the generic columns, then the source's (cleaned: no generic
 *          name, no malformed name or path), a missing value empty, a boolean 1/0, a list as JSON; another channel
 *          and no channel filter keep the generic columns only
 *  AS-10 — the audit menu leaves out a link into a channel the user may not read: an audit view filtered on the
 *          channel, or a link menu.ini names with AuditChannel_<key>; nothing for a user who may read every channel
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditSourceTestSource extends expAuditSourceBase
{
    /** @var bool */
    public static $throw = false;

    public function label()
    {
        return 'Test source';
    }

    public function channel()
    {
        return 'tst';
    }

    public function links( $view, array $filters )
    {
        if ( self::$throw )
            throw new RuntimeException( 'broken on purpose' );
        return array( array( 'label' => 'Test events', 'url' => self::consoleUrl( 'audit/console', array( 'channel' => 'tst' ) ) ),
                      array( 'label' => 'Own page', 'url' => 'audittest/list/(view)/all' ),
                      array( 'label' => 'Query', 'url' => 'audittest/list?view=all' ),
                      array( 'label' => 'Absolute', 'url' => 'https://example.com/x' ),
                      array( 'label' => 'Slash', 'url' => '/audittest/list' ),
                      array( 'label' => 'Up', 'url' => 'audittest/../setup/info' ),
                      array( 'label' => '', 'url' => 'audittest/list' ),
                      'not an array' );
    }

    public function dashboard( expAuditQuery $q, ?array $allowed, array $period )
    {
        return array( 'figures' => array( array( 'label' => 'votes', 'value' => 3, 'url' => 'audit/console/(channel)/tst', 'state' => 'failed' ),
                                          array( 'label' => 'bad', 'value' => 1, 'url' => 'javascript:alert(1)', 'state' => 'red' ),
                                          array( 'label' => 'no value' ) ),
                      'lists' => array( array( 'title' => 'Top', 'items' => array( array( 'label' => 'admin', 'n' => '2', 'url' => 'audit/console/(login)/admin' ) ) ) ),
                      'links' => array( array( 'label' => 'All', 'url' => 'audit/console/(channel)/tst' ) ),
                      'note' => 'A note', 'extra' => 'dropped' );
    }

    public function event( array $record )
    {
        return array( 'fields' => array( array( 'label' => 'Votes', 'value' => isset( $record['after']['votes'] ) ? $record['after']['votes'] : '' ),
                                         array( 'label' => 'Empty', 'value' => '' ),
                                         array( 'label' => 'List', 'value' => array( 1, 2 ) ) ),
                      'links' => array( array( 'label' => 'The poll', 'url' => 'audittest/poll/12' ), array( 'label' => 'Bad', 'url' => '//evil' ) ) );
    }

    public function linkFor( $type, $id )
    {
        return $type === 'audittest_poll' ? 'audittest/poll/' . (int)$id : null;
    }

    public function charts()
    {
        return array( array( 'title' => 'Votes per day', 'series' => array( array( 'label' => 'votes', 'name' => 'content.audittest_poll.vote' ),
                                                                            array( 'label' => 'bad', 'name' => 'content.audittest_poll.vote*' ) ) ),
                      array( 'title' => 'Empty' ) );
    }

    public function exportColumns()
    {
        return array( 'votes' => 'after.votes', 'poll' => 'object.id', 'flags' => 'after.flags', 'open' => 'after.open', 'missing' => 'after.none',
                      'name' => 'after.votes', 'Bad Name' => 'after.votes', 'query' => 'after.votes?x', 'up' => '../after', 7 => 'after.votes' );
    }
}

class expAuditSourceTestBrokenSource extends expAuditSourceTestSource
{
    public function problem()
    {
        return 'its tables are missing';
    }
}

class expAuditSourceTestOtherChannel extends expAuditSourceTestSource
{
    public function channel()
    {
        return 'nowhere';
    }
}

class expAuditSourceTestNotASource
{
}

class expAuditSourceTestBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array( 'content.audittest_poll.vote' => array( 'label' => 'Poll vote', 'severity' => 'info', 'default' => 'on', 'channel' => 'tst' ) );
    }
}

class expAuditSourceTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
        expAuditSourceRegistry::reset();
        expAuditSourceTestSource::$throw = false;
    }

    protected function tearDown(): void
    {
        expAuditSourceRegistry::reset();
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    protected function withSource( array $sources = array( 'audittest' => 'expAuditSourceTestSource' ), $channel = true )
    {
        $channels = array( 'content', 'access', 'system', 'commerce', 'read' );
        if ( $channel )
            $channels[] = 'tst';
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSourceSettings/Sources' => $sources, 'AuditChannelSettings/Channels' => $channels,
                                                            'AuditEventSettings/Branches' => array( 'audittest' => 'expAuditSourceTestBranch' ) ) );
        expAuditSourceRegistry::reset();
    }

    /** AS-01 */
    public function testNoSource()
    {
        $this->assertSame( array(), expAuditSourceRegistry::status() );
        $this->assertSame( array(), expAuditSourceRegistry::sources() );
        $this->assertSame( array(), expAuditSourceRegistry::links( 'audit/console', array(), null ) );
        $this->assertSame( array(), expAuditSourceRegistry::charts( null ) );
        $this->assertSame( array(), expAuditSourceRegistry::channelLabels() );
        $this->assertNull( expAuditSourceRegistry::event( array( 'channel' => 'content', 'name' => 'content.node.move' ) ) );
        $this->assertNull( expAuditConsole::linkFor( 'audittest_poll', '12' ) );
        $this->assertSame( array(), expAuditConsole::sourceLinks( 'audit/console', array(), null ) );
        $options = expAuditConsole::channelOptions( null );
        $this->assertSame( expAuditConsole::channelNames( null ), array_column( $options, 'channel' ) );
        $this->assertSame( array_column( $options, 'channel' ), array_column( $options, 'label' ), 'the option text is the channel, as before' );
    }

    /** AS-02 */
    public function testSourceOfItsOwnChannel()
    {
        $this->withSource();
        $status = expAuditSourceRegistry::status();
        $this->assertSame( array( 'class' => 'expAuditSourceTestSource', 'label' => 'Test source', 'channel' => 'tst', 'problem' => '' ), $status['audittest'] );
        $this->assertSame( array( 'tst' => 'Test source' ), expAuditSourceRegistry::channelLabels() );
        $this->assertSame( 'tst (Test source)', array_column( expAuditConsole::channelOptions( null ), 'label', 'channel' )['tst'] );
        $owner = expAuditSourceRegistry::forChannel( 'tst' );
        $this->assertSame( 'audittest', $owner[0] );
        $this->assertNull( expAuditSourceRegistry::forChannel( 'content' ) );

        $event = expAuditSourceRegistry::event( array( 'channel' => 'tst', 'name' => 'content.audittest_poll.vote', 'after' => array( 'votes' => 7 ) ) );
        $this->assertSame( 'Test source', $event['label'] );
        $this->assertSame( array( array( 'label' => 'Votes', 'value' => '7' ) ), $event['fields'], 'empty and non-scalar values are left out' );
        $this->assertSame( array( array( 'label' => 'The poll', 'url' => 'audittest/poll/12' ) ), $event['links'] );

        $this->assertSame( 'audittest/poll/12', expAuditConsole::linkFor( 'audittest_poll', '12' ) );
        $this->assertSame( 'content/view/full/2', expAuditConsole::linkFor( 'node', '2' ), 'the kernel types are unchanged' );
        $this->assertNull( expAuditConsole::linkFor( 'unknown', '1' ) );

        $charts = expAuditSourceRegistry::charts( null );
        $this->assertCount( 1, $charts, 'a chart without series is left out' );
        $this->assertSame( 'Votes per day', $charts[0]['title'] );
        $this->assertSame( array( array( 'label' => 'votes', 'name' => 'content.audittest_poll.vote' ) ), $charts[0]['series'], 'a malformed pattern is left out' );

        // the test source's dashboard does not query, so a query object without a database will do: the test then
        // also runs where no database is configured
        $q = ( new ReflectionClass( 'expAuditQuery' ) )->newInstanceWithoutConstructor();
        $panels = expAuditSourceRegistry::panels( $q, null, array( 'today_ms' => 0, 'week_ms' => 0 ) );
        $this->assertCount( 1, $panels );
        $p = $panels[0];
        $this->assertSame( array( 'identifier', 'label', 'channel', 'figures', 'lists', 'links', 'note' ), array_keys( $p ) );
        $this->assertSame( array( 'label' => 'votes', 'value' => '3', 'url' => 'audit/console/(channel)/tst', 'state' => 'failed' ), $p['figures'][0] );
        $this->assertSame( array( 'label' => 'bad', 'value' => '1', 'url' => '', 'state' => '' ), $p['figures'][1], 'no javascript: address, no unknown state' );
        $this->assertCount( 2, $p['figures'], 'a figure without a value is left out' );
        $this->assertSame( 2, $p['lists'][0]['items'][0]['n'] );
        $this->assertSame( 'A note', $p['note'] );
    }

    /** AS-03 */
    public function testProblems()
    {
        $this->withSource( array( 'missing' => 'expAuditNoSuchSourceClass', 'wrong' => 'expAuditSourceTestNotASource',
                                  'Bad-Id' => 'expAuditSourceTestSource', 'broken' => 'expAuditSourceTestBrokenSource',
                                  'other' => 'expAuditSourceTestOtherChannel', 'audittest' => 'expAuditSourceTestSource' ) );
        $status = expAuditSourceRegistry::status();
        $this->assertSame( 'the class does not exist', $status['missing']['problem'] );
        $this->assertSame( 'the class does not implement expAuditSource', $status['wrong']['problem'] );
        $this->assertSame( 'not a valid identifier', $status['Bad-Id']['problem'] );
        $this->assertSame( 'its tables are missing', $status['broken']['problem'] );
        $this->assertSame( "the channel 'nowhere' is not in [AuditChannelSettings] Channels[]", $status['other']['problem'] );
        $this->assertSame( array( 'audittest' ), array_keys( expAuditSourceRegistry::sources() ), 'only the working source is used' );
    }

    /** AS-04 */
    public function testOnlyViewPaths()
    {
        $this->withSource();
        $links = expAuditSourceRegistry::links( 'audit/console', array(), null );
        $this->assertCount( 1, $links );
        $this->assertSame( array( 'audit/console/(channel)/tst', 'audittest/list/(view)/all' ), array_column( $links[0]['links'], 'url' ) );
        foreach ( array( 'expai/log/(request)/12' => true, 'audit/console/(name)/system.expai.request.%2A' => true, 'expai/log' => true,
                         'expai/log?request=12' => false, 'http://example.com' => false, '/expai/log' => false, 'expai/../setup' => false,
                         'expai/log#x' => false, '' => false, 'expai log' => false ) as $path => $ok )
            $this->assertSame( $ok, expAuditSourceRegistry::isPath( $path ), $path );
    }

    /** AS-05 */
    public function testThrowingSourceIsLeftOut()
    {
        $this->withSource();
        expAuditSourceTestSource::$throw = true;
        $this->assertSame( array(), expAuditSourceRegistry::links( 'audit/console', array(), null ) );
        $this->assertCount( 1, expAuditSourceRegistry::charts( null ), 'its other answers still count' );
    }

    /** AS-06 */
    public function testChannelLimitation()
    {
        $this->withSource();
        $this->assertSame( array( 'audittest' ), array_keys( expAuditSourceRegistry::readable( null ) ) );
        $this->assertSame( array( 'audittest' ), array_keys( expAuditSourceRegistry::readable( array( 'tst' ) ) ) );
        $this->assertSame( array(), expAuditSourceRegistry::readable( array( 'content' ) ) );
        $this->assertSame( array(), expAuditSourceRegistry::readable( array() ) );
        $this->assertSame( array(), expAuditSourceRegistry::links( 'audit/console', array(), array( 'content' ) ) );
        $this->assertSame( array(), expAuditSourceRegistry::charts( array( 'access' ) ) );
    }

    /** AS-07 */
    public function testBranchRoutesIntoTheSourceChannel()
    {
        $this->withSource();
        $id = expAudit::event( 'content.audittest_poll.vote', array( 'object' => array( 'type' => 'audittest_poll', 'id' => 12 ), 'after' => array( 'votes' => 7 ) ) );
        $this->assertNotNull( $id );
        expAudit::flushFinal();
        $events = expAuditTestFixtures::events( $this->dir, 'tst' );
        $this->assertCount( 1, $events );
        $this->assertSame( 'tst', $events[0]['channel'] );
        $this->assertSame( $id, $events[0]['id'] );
        $this->assertSame( array(), expAuditTestFixtures::events( $this->dir, 'content' ), 'not in the content channel' );
        $this->assertSame( 'intact', expAuditTestFixtures::verifier( $this->dir )->verifyChannel( 'tst' )['result'], 'its own intact chain' );
        $this->assertSame( 'Poll vote', expAuditConsole::label( 'content.audittest_poll.vote' ), 'the console shows the label of the branch' );
        $this->assertSame( 'Node move', expAuditConsole::label( 'content.node.move' ), 'a kernel name keeps its label' );

        // the source's channel not configured: DefaultChannel (system), as for any branch channel not in Channels[]
        $this->withSource( array(), false );
        $this->assertSame( 'system', expAuditTaxonomy::decide( 'content.audittest_poll.vote' )['channel'] );
    }

    /** AS-08 */
    public function testPolicyLimitationListsTheChannel()
    {
        $this->withSource();
        $Module = null;
        include dirname( __DIR__, 5 ) . '/kernel/audit/module.php';
        $values = array_column( $FunctionList['read']['Channel']['values'], 'Name', 'value' );
        $this->assertSame( 'Test source', $values['tst'] );
        $this->assertSame( 'Content', $values['content'] );
        $this->assertCount( 6, $values );
    }

    /** AS-09 */
    public function testExportColumnsOfTheSourceChannel()
    {
        $this->withSource();
        $generic = array( 'time', 'id', 'channel', 'name', 'severity', 'result', 'reason', 'user_id', 'login', 'ip', 'object_type',
                          'object_id', 'object_name', 'target_type', 'target_id', 'request', 'job', 'imported' );
        $columns = expAuditSourceRegistry::exportColumns( 'tst', $generic );
        $this->assertSame( array( 'votes' => 'after.votes', 'poll' => 'object.id', 'flags' => 'after.flags', 'open' => 'after.open', 'missing' => 'after.none' ), $columns,
                           'a generic name, a malformed name or path are left out' );
        $this->assertSame( array(), expAuditSourceRegistry::exportColumns( 'content', $generic ) );
        $this->assertSame( array(), expAuditSourceRegistry::exportColumns( null, $generic ) );
        $record = array( 'id' => 'r1', 'name' => 'content.audittest_poll.vote', 'channel' => 'tst', 'object' => array( 'type' => 'audittest_poll', 'id' => 12 ),
                         'after' => array( 'votes' => 7, 'flags' => array( 'a', 'b' ), 'open' => false ) );
        $this->assertSame( array( 'votes' => '7', 'poll' => '12', 'flags' => '["a","b"]', 'open' => '0', 'missing' => '' ),
                           expAuditSourceRegistry::exportValues( $columns, $record ) );

        $file = $this->dir . 'export.csv';
        ( new expAuditExporter() )->export( array( $record + array( 'time' => '2026-10-12T00:00:00.000Z' ) ), 'csv', $file, array( 'channel' => 'tst' ) );
        $lines = array_values( array_filter( explode( "\n", file_get_contents( $file ) ), 'strlen' ) );
        $this->assertSame( array_merge( $generic, array( 'votes', 'poll', 'flags', 'open', 'missing' ) ), str_getcsv( $lines[0], ',', '"', '\\' ) );
        $this->assertSame( array( '7', '12', '["a","b"]', '0', '' ), array_slice( str_getcsv( $lines[1], ',', '"', '\\' ), -5 ) );
        foreach ( array( array( 'channel' => 'content' ), array() ) as $filters )
        {
            ( new expAuditExporter() )->export( array( $record ), 'csv', $file, $filters );
            $this->assertSame( $generic, str_getcsv( strtok( file_get_contents( $file ), "\n" ), ',', '"', '\\' ) );
        }
    }

    /** AS-10 */
    public function testMenuLinksIntoAChannelTheUserMayNotRead()
    {
        $this->withSource();
        $this->assertSame( 'tst', expAuditSourceRegistry::menuLinkChannel( 'audit/console/(channel)/tst' ) );
        $this->assertSame( 'tst', expAuditSourceRegistry::menuLinkChannel( 'audit/recent/(name)/content.*/(channel)/tst/(limit)/20' ) );
        $this->assertSame( '', expAuditSourceRegistry::menuLinkChannel( 'audit/console' ) );
        $this->assertSame( '', expAuditSourceRegistry::menuLinkChannel( 'audittest/list/(channel)/tst' ), 'only an audit view' );

        $ini = eZINI::instance( 'menu.ini' );
        $saved = array();
        foreach ( array( 'Links', 'AuditChannel_own' ) as $v )
            $saved[$v] = $ini->hasVariable( 'Leftmenu_audittest', $v ) ? $ini->variable( 'Leftmenu_audittest', $v ) : null;
        $ini->setVariable( 'Leftmenu_audittest', 'Links', array( 'console' => 'audit/console', 'events' => 'audit/console/(channel)/tst',
                                                                 'content' => 'audit/console/(channel)/content', 'own' => 'audittest/list' ) );
        $ini->setVariable( 'Leftmenu_audittest', 'AuditChannel_own', 'tst' );
        try
        {
            $this->assertSame( array( 'events', 'own' ), expAuditSourceRegistry::hiddenMenuLinks( 'Leftmenu_audittest', array( 'content' ) ) );
            $this->assertSame( array( 'content' ), expAuditSourceRegistry::hiddenMenuLinks( 'Leftmenu_audittest', array( 'tst' ) ) );
            $this->assertSame( array(), expAuditSourceRegistry::hiddenMenuLinks( 'Leftmenu_audittest', null ), 'every channel: nothing hidden' );
            $this->assertSame( array(), expAuditSourceRegistry::hiddenMenuLinks( 'Leftmenu_nosuchmenu', array( 'content' ) ) );
        }
        finally
        {
            foreach ( $saved as $v => $value )
                if ( $value === null )
                    $ini->removeSetting( 'Leftmenu_audittest', $v );
                else
                    $ini->setVariable( 'Leftmenu_audittest', $v, $value );
        }
    }
}
