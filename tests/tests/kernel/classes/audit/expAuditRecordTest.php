<?php
/**
 * The audit record and its canonical JSON (doc/bc/6.0/audit.md, "The record format", acceptance test B1), the
 * ULID, and the taxonomy registry (names, patterns, routing, on/off at any rank, extension branches).
 *
 *  AR-01 — canonical JSON: keys sorted by bytes at every depth, lists keep their order, {} vs [], no escaping of
 *          "/" or non-ASCII, control characters as \u00xx in lower case, \n \t short, integers only
 *  AR-02 — the hash of a crafted record is sha256 of its canonical form without "hash", a known value
 *  AR-03 — a ULID is 26 Crockford characters and sorts by time
 *  AR-04 — a record has every field of the fields table that applies, and none that is null
 *  AR-05 — a refused or failed result raises the severity (notice, warning)
 *  AR-06 — names: 3 to 6 ranks in the five domains; anything else is refused (event() returns null)
 *  AR-07 — patterns: "content.*" matches the domain, "content.node.remove.*" matches itself and its details, the
 *          most specific wins, Disabled[] wins a tie
 *  AR-08 — the catalogue has the 135 names of the design; defaults: on, off, always, sampled
 *  AR-09 — routing: Route[] patterns, the exact entry wins, DefaultChannel for the rest
 *  AR-10 — Audit=disabled records nothing; an "always" name ignores Disabled[]; MinSeverity drops lower ones
 *  AR-11 — an extension branch (Branches[]) adds names; a missing class, a class not implementing the interface
 *          and a name claimed twice are problems
 *  AR-12 — parent and child events: begin/end, depth, ChildDepth and MaxChildren with children_omitted,
 *          withParent(), setJob(), setRun(); an open parent is written as failed at the end of the request
 *
 * Writes only into var/tmp/audit-tests/; no database, no live settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/fixtures/expaudittestfixtures.php';

class expAuditRecordTestBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array( 'content.audittest_poll.vote' => array( 'label' => 'Poll vote', 'severity' => 'info', 'default' => 'off' ),
                      'content.audittest_poll.close' => array( 'label' => 'Poll closed', 'severity' => 'notice', 'default' => 'on' ),
                      'content.node.move' => array( 'default' => 'on' ) );
    }
}

class expAuditRecordTestNotABranch
{
    public function events()
    {
        return array();
    }
}

class expAuditRecordTest extends PHPUnit\Framework\TestCase
{
    protected $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = expAuditTestFixtures::setUp( $this->name() );
    }

    protected function tearDown(): void
    {
        expAuditTestFixtures::tearDown();
        parent::tearDown();
    }

    /** AR-01 */
    public function testCanonicalJson()
    {
        $value = array( 'b' => 1, 'a' => array(), 'c' => new stdClass(), 'B' => true, 'd' => "é/\x01\n\t\"\\", 'e' => array( 3, 1, 2 ),
                        'f' => array( 'z' => null, 'y' => false ), 'g' => -7, '10' => 'ten', '9' => 'nine' );
        $this->assertSame( '{"10":"ten","9":"nine","B":true,"a":[],"b":1,"c":{},"d":"é/\u0001\n\t\"\\\\","e":[3,1,2],"f":{"y":false,"z":null},"g":-7}',
                           expAuditJson::encode( $value ) );
        // U+2028 is not escaped; invalid UTF-8 is replaced, never dropped silently
        $this->assertSame( "\"a\u{2028}b\"", expAuditJson::encode( "a\u{2028}b" ) );
        $this->assertSame( "\"x\u{FFFD}y\"", expAuditJson::encode( "x\xC3y" ) );
        // a float becomes a string: records hold integers only
        $this->assertSame( '"1.5"', expAuditJson::encode( 1.5 ) );
        // decode() keeps {} and [] apart, so re-encoding gives the same bytes
        $line = '{"a":{},"b":[],"c":{"d":[{"e":{}}]}}';
        $this->assertSame( $line, expAuditJson::encode( expAuditJson::decode( $line ) ) );
        $this->assertNull( expAuditJson::decode( '{"a":' ) );
    }

    /** AR-02 */
    public function testKnownHash()
    {
        $record = array( 'v' => 1, 'id' => '01J9ZK3M7Q8R2T4V6X8Z0B2D4F', 'seq' => 1, 'name' => 'content.node.move', 'channel' => 'content',
                         'time' => '2026-10-02T13:30:01.123Z', 'object' => array( 'type' => 'node', 'id' => 275, 'name' => 'Workout' ),
                         'prev' => 'sha256:' . str_repeat( '0', 64 ) );
        $canonical = '{"channel":"content","id":"01J9ZK3M7Q8R2T4V6X8Z0B2D4F","name":"content.node.move","object":{"id":275,"name":"Workout","type":"node"},"prev":"sha256:'
                   . str_repeat( '0', 64 ) . '","seq":1,"time":"2026-10-02T13:30:01.123Z","v":1}';
        $this->assertSame( $canonical, expAuditJson::encode( $record ) );
        $this->assertSame( 'sha256:' . hash( 'sha256', $canonical ), expAuditWriter::hashOf( $record ) );
        // the same value sha256sum gives for those bytes
        $this->assertSame( 'sha256:f56f0403faaa9f933cdae00f0cfee601a19701f09e74536cfd8128a30541ae6d', expAuditWriter::hashOf( $record ) );
        // the hash member itself is never part of what is hashed
        $with = $record + array( 'hash' => 'sha256:whatever' );
        $this->assertSame( expAuditWriter::hashOf( $record ), expAuditWriter::hashOf( $with ) );
    }

    /** AR-03 */
    public function testUlid()
    {
        $a = expAudit::ulid( 1790947801123 );
        $b = expAudit::ulid( 1790947801124 );
        $this->assertMatchesRegularExpression( '/^[0-9A-HJKMNP-TV-Z]{26}$/', $a );
        $this->assertSame( -1, strcmp( substr( $a, 0, 10 ), substr( $b, 0, 10 ) ) <=> 0 );
        $this->assertNotSame( expAudit::ulid( 1 ), expAudit::ulid( 1 ), 'the random part differs' );
        $this->assertSame( '2026-10-02T13:30:01.123Z', expAudit::timeString( 1790947801123 ) );
    }

    /** AR-04 */
    public function testRecordFields()
    {
        $id = expAudit::event( 'content.node.move', array(
            'object' => array( 'type' => 'node', 'id' => 275, 'object_id' => 273, 'name' => 'Workout' ),
            'target' => array( 'type' => 'node', 'id' => 89 ),
            'before' => array( 'parent' => 2 ), 'after' => array( 'parent' => 89 ) ) );
        expAudit::flushFinal();
        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $this->assertCount( 1, $events );
        $r = $events[0];
        $this->assertSame( $id, $r['id'] );
        foreach ( array( 'v', 'id', 'seq', 'name', 'channel', 'time', 'severity', 'request', 'actor', 'verb', 'object', 'target',
                         'before', 'after', 'result', 'depth', 'prev', 'hash' ) as $field )
            $this->assertArrayHasKey( $field, $r, $field );
        foreach ( array( 'reason', 'error', 'parent', 'job', 'run', 'x', 'imported', 'source' ) as $absent )
            $this->assertArrayNotHasKey( $absent, $r, "$absent is null and left out" );
        $this->assertSame( 1, $r['v'] );
        $this->assertSame( 'move', $r['verb'] );
        $this->assertSame( 'content', $r['channel'] );
        $this->assertSame( 'success', $r['result'] );
        $this->assertSame( 2, $r['seq'], 'after the file open record' );
        $this->assertMatchesRegularExpression( '/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d\.\d{3}Z$/', $r['time'] );
        $this->assertMatchesRegularExpression( '/^r-[0-9A-HJKMNP-TV-Z]{26}$/', $r['request']['id'] );
        $this->assertIsInt( $r['request']['ms'] );
        $this->assertEquals( array( 'user_id' => 14, 'login' => 'editor1', 'roles' => array( 2 ) ),
                           array_intersect_key( $r['actor'], array( 'user_id' => 1, 'login' => 1, 'roles' => 1 ) ) );
        $this->assertSame( array( 'parent' => 2 ), $r['before'] );
    }

    /** AR-05 */
    public function testSeverityRaisedByResult()
    {
        expAudit::event( 'access.permission.refused', array( 'object' => array( 'type' => 'view', 'id' => 'setup/cache' ),
                                                             'result' => 'refused', 'reason' => 'policy' ) );
        expAudit::event( 'access.session.login', array( 'result' => 'failed', 'reason' => 'error' ) );
        $events = expAuditTestFixtures::events( $this->dir, 'access' );
        $this->assertSame( array( 'notice', 'warning' ), array_column( $events, 'severity' ) );
        $this->assertSame( 'policy', $events[0]['reason'] );
    }

    /** AR-06 */
    public function testNames()
    {
        foreach ( array( 'content.node.move', 'access.session.login.failed', 'system.audit.chain.broken', 'data.export.csv',
                         'commerce.order.item.remove', 'content.a.b.c.d.e' ) as $ok )
            $this->assertTrue( expAuditTaxonomy::isValidName( $ok ), $ok );
        foreach ( array( 'content.node', 'other.node.move', 'Content.node.move', 'content..move', 'content.node.move.', 'content.a.b.c.d.e.f',
                         'content.node-x.move', 'content.1node.move', '' ) as $bad )
            $this->assertFalse( expAuditTaxonomy::isValidName( $bad ), $bad );
        $this->assertNull( expAudit::event( 'content.node' ) );
        $this->assertNull( expAudit::event( 'whatever.node.move' ) );
    }

    /** AR-07 */
    public function testPatterns()
    {
        $this->assertSame( 1, expAuditTaxonomy::match( 'content.*', 'content.node.move' ) );
        $this->assertSame( 3, expAuditTaxonomy::match( 'content.node.remove.*', 'content.node.remove' ) );
        $this->assertSame( 3, expAuditTaxonomy::match( 'content.node.remove.*', 'content.node.remove.trash' ) );
        $this->assertSame( -1, expAuditTaxonomy::match( 'content.node.remove.*', 'content.node.removed' ) );
        $this->assertSame( 4, expAuditTaxonomy::match( 'content.node.move', 'content.node.move' ) );
        $this->assertSame( -1, expAuditTaxonomy::match( 'content.node.move', 'content.node.move.x' ) );

        $dir = $this->dir;
        expAuditTestFixtures::configure( $dir, array( 'AuditEventSettings/Enabled' => array( 'content.*', 'content.node.remove.trash' ),
                                                      'AuditEventSettings/Disabled' => array( 'content.node.*', 'content.node.remove.*' ) ) );
        $this->assertTrue( expAudit::isOn( 'content.object.remove' ), 'content.* only' );
        $this->assertFalse( expAudit::isOn( 'content.node.move' ), 'content.node.* is more specific than content.*' );
        $this->assertFalse( expAudit::isOn( 'content.node.remove' ), 'content.node.remove.* matches the name itself' );
        $this->assertTrue( expAudit::isOn( 'content.node.remove.trash' ), 'the whole name beats every pattern' );
        expAuditTestFixtures::configure( $dir, array( 'AuditEventSettings/Enabled' => array( 'content.node.*' ),
                                                      'AuditEventSettings/Disabled' => array( 'content.node.*' ) ) );
        $this->assertFalse( expAudit::isOn( 'content.node.move' ), 'a tie: Disabled[] is the later line' );
    }

    /** AR-08 */
    public function testCatalogue()
    {
        $catalogue = expAuditTaxonomy::catalogue();
        $this->assertCount( 135, $catalogue );
        $defaults = array_count_values( array_column( $catalogue, 'default' ) );
        $this->assertSame( array( 'always' => 23, 'off' => 23, 'on' => 86, 'sampled' => 3 ), array( 'always' => $defaults['always'], 'off' => $defaults['off'],
                                                                                                  'on' => $defaults['on'], 'sampled' => $defaults['sampled'] ) );
        foreach ( $catalogue as $name => $def )
        {
            $this->assertTrue( expAuditTaxonomy::isValidName( $name ), $name );
            $this->assertContains( $def['severity'], expAuditTaxonomy::$severities, $name );
        }
        foreach ( array( 'access.session.login', 'access.session.login.failed', 'access.session.logout', 'system.setting.write',
                         'system.setting.undo', 'content.node.move', 'commerce.order.delete' ) as $on )
            $this->assertTrue( expAudit::isOn( $on ), "$on is on by default" );
        foreach ( array( 'content.object.publish', 'content.node.view', 'access.session.regenerate', 'commerce.order.create' ) as $off )
            $this->assertFalse( expAudit::isOn( $off ), "$off is off by default" );
    }

    /** AR-09 */
    public function testRouting()
    {
        $c = expAuditConfig::get();
        $this->assertSame( 'content', expAuditTaxonomy::decide( 'content.node.move', $c )['channel'] );
        $this->assertSame( 'read', expAuditTaxonomy::decide( 'content.node.view', $c )['channel'] );
        $this->assertSame( 'read', expAuditTaxonomy::decide( 'content.search.query', $c )['channel'] );
        $this->assertSame( 'access', expAuditTaxonomy::decide( 'access.session.login', $c )['channel'] );
        $this->assertSame( 'commerce', expAuditTaxonomy::decide( 'data.export.csv', $c )['channel'] );
        $this->assertSame( 'system', expAuditTaxonomy::decide( 'system.legacy.my_old_name', $c )['channel'] );
        $this->assertTrue( expAuditTaxonomy::decide( 'access.session.login', $c )['immediate'] );
        $this->assertTrue( expAuditTaxonomy::decide( 'system.setting.write', $c )['immediate'] );
        $this->assertFalse( expAuditTaxonomy::decide( 'content.node.move', $c )['immediate'] );
        expAuditTestFixtures::configure( $this->dir, array( 'AuditChannelSettings/Route' => array( 'content.*' => 'nosuch' ) ) );
        $this->assertSame( 'system', expAuditTaxonomy::decide( 'content.node.move', expAuditConfig::get() )['channel'], 'an unknown channel: DefaultChannel' );
    }

    /** AR-10 */
    public function testOffAlwaysAndMinSeverity()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditSettings/Audit' => 'disabled' ) );
        $this->assertNull( expAudit::event( 'access.session.login' ) );
        $this->assertFalse( expAudit::isEnabled() );
        $this->assertNull( expAudit::responseHeader() );
        $this->assertSame( array(), glob( $this->dir . 'log/*.jsonl' ) );

        expAuditTestFixtures::configure( $this->dir, array( 'AuditEventSettings/Disabled' => array( 'system.*' ),
                                                            'AuditEventSettings/MinSeverity' => 'notice' ) );
        $this->assertNotNull( expAudit::event( 'system.audit.verify', array( 'object' => array( 'type' => 'channel', 'id' => 'content' ) ) ),
                              'always: neither Disabled[] nor MinSeverity apply' );
        $this->assertNull( expAudit::event( 'access.session.login' ), 'info is below notice' );
        $this->assertNotNull( expAudit::event( 'access.session.login.failed' ), 'notice' );
        $header = expAudit::responseHeader();
        $this->assertSame( 'X-Exp-Request-Id', $header[0] );
        $this->assertSame( expAudit::requestId(), $header[1] );
    }

    /** AR-11 */
    public function testBranches()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditEventSettings/Branches' => array(
            'audittest' => 'expAuditRecordTestBranch', 'missing' => 'expAuditNoSuchBranchClass', 'wrong' => 'expAuditRecordTestNotABranch' ) ) );
        $registry = expAuditTaxonomy::registry();
        $this->assertSame( 'audittest', $registry['content.audittest_poll.close']['branch'] );
        $this->assertSame( 'Poll closed', $registry['content.audittest_poll.close']['label'] );
        $this->assertSame( 'kernel', $registry['content.node.move']['branch'], 'the first claim wins' );
        $problems = expAuditTaxonomy::problems();
        $this->assertSame( 'the class does not exist', $problems['missing'] );
        $this->assertSame( 'the class does not implement expAuditTaxonomyBranch', $problems['wrong'] );
        $this->assertStringStartsWith( 'already registered by kernel', $problems['audittest:content.node.move'] );
        $this->assertTrue( expAudit::isOn( 'content.audittest_poll.close' ), 'default on (no pattern matches content.audittest_poll)' );
        $this->assertFalse( expAudit::isOn( 'content.audittest_poll.vote' ), 'default off' );
        $this->assertNotNull( expAudit::event( 'content.audittest_poll.close', array( 'object' => array( 'type' => 'poll', 'id' => 12 ) ) ) );
    }

    /** AR-12 */
    public function testParentsAndChildren()
    {
        expAuditTestFixtures::configure( $this->dir, array( 'AuditRecordSettings/ChildDepth' => '1', 'AuditRecordSettings/MaxChildren' => '3' ) );
        expAudit::setJob( '20261002-133001-4f2a9c1e' );
        expAudit::setRun( 'r-RUN' );
        $parent = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 89 ) ) );
        $this->assertMatchesRegularExpression( '/^[0-9A-HJKMNP-TV-Z]{26}$/', $parent );
        $ids = array();
        expAudit::withParent( $parent, function () use ( &$ids ) {
            for ( $i = 0; $i < 5; $i++ )
                $ids[] = expAudit::event( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 100 + $i ) ) );
        } );
        $this->assertCount( 3, array_filter( $ids ), 'MaxChildren' );
        $grandChild = expAudit::event( 'content.node.remove', array( 'parent' => $ids[0], 'object' => array( 'type' => 'node', 'id' => 999 ) ) );
        $this->assertNotNull( $grandChild, 'a parent that is not open: written as a top-level event' );
        expAudit::end( $parent, array( 'after' => array( 'removed' => 5 ) ) );
        $open = expAudit::begin( 'content.node.remove', array( 'object' => array( 'type' => 'node', 'id' => 1 ) ) );
        expAudit::flushFinal();
        $events = expAuditTestFixtures::events( $this->dir, 'content' );
        $byId = array_column( $events, null, 'id' );
        $this->assertEquals( array( 'removed' => 5, 'children_omitted' => 2 ), $byId[$parent]['after'] );
        $this->assertSame( 0, $byId[$parent]['depth'] );
        $this->assertSame( $parent, $byId[$ids[0]]['parent'] );
        $this->assertSame( 1, $byId[$ids[0]]['depth'] );
        $this->assertSame( '20261002-133001-4f2a9c1e', $byId[$ids[0]]['job'] );
        $this->assertSame( 'r-RUN', $byId[$ids[0]]['run'] );
        $this->assertSame( 'failed', $byId[$open]['result'], 'still open at the end of the request' );
        $this->assertSame( 'error', $byId[$open]['reason'] );
        // the children were written before their parent
        $positions = array_flip( array_column( $events, 'id' ) );
        $this->assertLessThan( $positions[$parent], $positions[$ids[0]] );
    }
}
