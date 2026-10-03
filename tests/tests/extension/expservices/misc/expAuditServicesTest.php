<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expaudit: the audit read services (audit/read, narrowed to the Channel limitation); nothing is changed. */
class expAuditServicesTest extends expMediaTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'expAuditConsole' ) || !expAuditConsole::available() )
            $this->markTestSkipped( 'The audit classes are not loaded' );
    }

    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expAuditServices', 19 );
        foreach ( expAuditServices::$services as $m => $d )
            $this->assertFalse( $d['write'], "$m must not write" );
    }

    public function testAvailable()
    {
        $r = $this->ok( 'expAuditServices', 'available' )['data'];
        $this->assertTrue( $r['available'] );
        $this->assertArrayHasKey( 'index', $r );
    }

    public function testChannelsListsTheReadableChannels()
    {
        $names = array_column( $this->ok( 'expAuditServices', 'channels' )['data'], 'channel' );
        $this->assertContains( 'access', $names );
    }

    public function testRecentIsPagedNewestFirst()
    {
        $r = $this->ok( 'expAuditServices', 'recent', array( '', 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 5, $r['meta']['count'] );
        $times = array_column( $r['data'], 'time' );
        $sorted = $times;
        rsort( $sorted );
        $this->assertSame( $sorted, $times );
    }

    public function testRecentOfOneChannel()
    {
        $r = $this->ok( 'expAuditServices', 'recent', array( 'access', 5, 0 ) );
        foreach ( $r['data'] as $e )
            $this->assertSame( 'access', $e['channel'] );
    }

    public function testInvalidChannelNameIsIgnoredNotAnInjection()
    {
        $r = $this->call( 'expAuditServices', 'recent', array( "x'; DROP TABLE y;--", 3, 0 ) );
        $this->assertTrue( $r['ok'] );
    }

    public function testSearchByNamePatternAndResult()
    {
        $r = $this->ok( 'expAuditServices', 'search', array( '', 'access.*', '', '', '', '', 5, 0 ) );
        foreach ( $r['data'] as $e )
            $this->assertStringStartsWith( 'access.', $e['name'] );
    }

    public function testSearchWithABadTimeIsRefused()
    {
        $this->fails( 400, 'expAuditServices', 'search', array( '', '', '', '', 'not-a-time', '', 5, 0 ) );
    }

    public function testEventAndRelated()
    {
        $e = $this->ok( 'expAuditServices', 'recent', array( '', 1, 0 ) )['data'];
        if ( !$e )
            $this->markTestSkipped( 'no audit events' );
        $one = $this->ok( 'expAuditServices', 'event', array( $e[0]['id'] ) )['data'];
        $this->assertSame( $e[0]['id'], $one['id'] );
        $this->assertIsArray( $one['record'] );
        $this->assertIsArray( $this->ok( 'expAuditServices', 'related', array( $e[0]['id'], 10 ) )['data'] );
    }

    public function testEventIdIsValidated()
    {
        $this->fails( 400, 'expAuditServices', 'event', array( 'short' ) );
        $this->fails( 404, 'expAuditServices', 'event', array( '00000000000000000000000000' ) );
    }

    public function testRefusedAndFailedFilterByResult()
    {
        foreach ( $this->ok( 'expAuditServices', 'refused', array( 5, 0 ) )['data'] as $e )
            $this->assertSame( 'refused', $e['result'] );
        foreach ( $this->ok( 'expAuditServices', 'failed', array( 5, 0 ) )['data'] as $e )
            $this->assertSame( 'failed', $e['result'] );
    }

    public function testByLoginObjectAndRequest()
    {
        foreach ( $this->ok( 'expAuditServices', 'bylogin', array( 'admin', 5, 0 ) )['data'] as $e )
            $this->assertSame( 'admin', $e['login'] );
        $this->assertPaged( $this->ok( 'expAuditServices', 'byobject', array( 'node:2', 5, 0 ) ) );
        $this->assertPaged( $this->ok( 'expAuditServices', 'byrequest', array( 'does-not-exist', 5, 0 ) ) );
    }

    public function testNamesAreSorted()
    {
        $names = $this->ok( 'expAuditServices', 'names' )['data'];
        $this->assertNotEmpty( $names );
        $sorted = $names;
        sort( $sorted );
        $this->assertSame( $sorted, $names );
    }

    public function testFiguresOfTheDashboard()
    {
        $this->assertNotEmpty( $this->ok( 'expAuditServices', 'volume' )['data'] );
        $r = $this->ok( 'expAuditServices', 'results' )['data'];
        $this->assertSame( $r['today']['total'], $r['today']['success'] + $r['today']['refused'] + $r['today']['failed'] );
        $this->assertCount( 7, $this->ok( 'expAuditServices', 'perday', array( 7 ) )['data'] );
        $this->assertCount( 30, $this->ok( 'expAuditServices', 'perday', array( 500 ) )['data'] );
        $this->assertIsArray( $this->ok( 'expAuditServices', 'topactors', array( 3 ) )['data'] );
        $this->assertArrayHasKey( 'total', $this->ok( 'expAuditServices', 'failedlogins' )['data'] );
    }

    public function testChainsAndSummary()
    {
        $this->assertNotEmpty( $this->ok( 'expAuditServices', 'chains' )['data'] );
        $s = $this->ok( 'expAuditServices', 'summary' )['data'];
        foreach ( array( 'volume', 'results', 'failed_logins', 'chains' ) as $k )
            $this->assertArrayHasKey( $k, $s );
    }

    public function testEveryUseIsRecorded()
    {
        $before = ( new expAuditQuery() )->count( array( 'name' => 'system.audit.read' ), null );
        $this->ok( 'expAuditServices', 'recent', array( '', 1, 0 ) );
        expAudit::flush( true );
        $after = ( new expAuditQuery() )->count( array( 'name' => 'system.audit.read' ), null );
        $this->assertGreaterThanOrEqual( $before, $after );
    }

    public function testAnonymousHasNoAccess()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expAuditServices', 'recent', array( '', 3, 0 ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
    }
}
