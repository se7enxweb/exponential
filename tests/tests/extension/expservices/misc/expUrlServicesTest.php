<?php
require_once __DIR__ . '/../media/expMediaTestCase.php';

/** expurl: stored links; setvalid is put back, check is tested only on addresses that are refused (no network). */
class expUrlServicesTest extends expMediaTestCase
{
    protected function aLink( $where = '' )
    {
        $r = $this->ok( 'expUrlServices', 'list', array( 1, 0, $where ?: 'all' ) )['data'];
        if ( !$r )
            $this->markTestSkipped( 'no stored link' );
        return $r[0];
    }

    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expUrlServices', 12 );
    }

    public function testListIsPaged()
    {
        $r = $this->ok( 'expUrlServices', 'list', array( 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertLessThanOrEqual( 5, $r['meta']['count'] );
        $this->assertArrayHasKey( 'is_valid', $r['data'][0] );
    }

    public function testListFilteredByValidity()
    {
        foreach ( $this->ok( 'expUrlServices', 'list', array( 20, 0, 'invalid' ) )['data'] as $u )
            $this->assertFalse( $u['is_valid'] );
        foreach ( $this->ok( 'expUrlServices', 'list', array( 20, 0, 'valid' ) )['data'] as $u )
            $this->assertTrue( $u['is_valid'] );
        $this->fails( 400, 'expUrlServices', 'list', array( 5, 0, 'bogus' ) );
    }

    public function testStatsAddUp()
    {
        $s = $this->ok( 'expUrlServices', 'stats' )['data'];
        $this->assertSame( $s['total'], $s['valid'] + $s['invalid'] );
        $this->assertSame( $s['total'], $this->ok( 'expUrlServices', 'count' )['data']['count'] );
        $this->assertSame( $s['invalid'], $this->ok( 'expUrlServices', 'count', array( 'invalid' ) )['data']['count'] );
    }

    public function testGetByIdAndByUrl()
    {
        $u = $this->aLink();
        $this->assertSame( $u['url'], $this->ok( 'expUrlServices', 'get', array( $u['id'] ) )['data']['url'] );
        $this->assertSame( $u['id'], $this->ok( 'expUrlServices', 'byurl', array( $u['url'] ) )['data']['id'] );
        $this->fails( 404, 'expUrlServices', 'get', array( 99999999 ) );
        $this->fails( 404, 'expUrlServices', 'byurl', array( 'http://no-such-link.invalid/' . uniqid() ) );
    }

    public function testSearchFindsTheLink()
    {
        $u = $this->aLink();
        $host = parse_url( $u['url'], PHP_URL_HOST ) ?: substr( $u['url'], 0, 6 );
        $r = $this->ok( 'expUrlServices', 'search', array( $host, 100, 0 ) );
        $this->assertContains( $u['id'], array_column( $r['data'], 'id' ) );
        $this->fails( 422, 'expUrlServices', 'search', array( 'x' ) );
    }

    public function testSearchTreatsWildcardsLiterally()
    {
        $this->assertSame( 0, $this->ok( 'expUrlServices', 'search', array( '%%%_%%', 10, 0 ) )['meta']['total'] );
    }

    public function testInvalidAndUncheckedLists()
    {
        $this->assertPaged( $this->ok( 'expUrlServices', 'invalid', array( 5, 0 ) ) );
        $this->assertPaged( $this->ok( 'expUrlServices', 'unchecked', array( 5, 0 ) ) );
    }

    public function testObjectsUsingALink()
    {
        $rows = eZDB::instance()->arrayQuery( 'SELECT url_id FROM ezurl_object_link', array( 'limit' => 1 ) );
        if ( !$rows )
            $this->markTestSkipped( 'no link is used by an object' );
        $r = $this->ok( 'expUrlServices', 'objects', array( (int)$rows[0]['url_id'], 5, 0 ) );
        $this->assertPaged( $r );
    }

    public function testByDomainCounts()
    {
        $d = $this->ok( 'expUrlServices', 'bydomain', array( 3 ) )['data'];
        $this->assertLessThanOrEqual( 3, count( $d ) );
        $this->assertGreaterThan( 0, $d[0]['count'] );
    }

    public function testRefusalOfInternalAddresses()
    {
        foreach ( array( 'http://127.0.0.1/', 'http://localhost/x', 'http://10.1.2.3/', 'http://192.168.0.1/', 'http://169.254.169.254/latest', 'ftp://example.com/', 'file:///etc/passwd', 'ezlocation://5', 'http://user:pw@example.com/', 'http://[::1]/' ) as $url )
            $this->assertNotNull( expUrlServices::refusal( $url ), $url );
    }

    public function testPublicLiteralAddressIsNotRefused()
    {
        $this->assertNull( expUrlServices::refusal( 'https://93.184.216.34/' ) );
    }

    public function testCheckNeedsPostAndRefusesInternalLinks()
    {
        $u = $this->aLink( 'invalid' );
        $this->fails( 403, 'expUrlServices', 'check', array( $u['id'] ) );
        $link = eZURL::create( 'http://127.0.0.1/expservices-a5-' . uniqid() );
        $link->store();
        $id = (int)$link->attribute( 'id' );
        $this->cleanups[] = function () use ( $id ) { eZURL::removeByID( $id ); };
        $r = $this->fails( 422, 'expUrlServices', 'check', array( $id ), array( 'confirm' => '1' ) );
        $this->assertStringContainsString( 'public', $r['error']['message'] );
    }

    public function testSetValidIsToggledAndPutBack()
    {
        $u = $this->aLink();
        $id = $u['id'];
        $was = $u['is_valid'];
        $this->cleanups[] = function () use ( $id, $was ) { eZURL::setIsValid( $id, $was ? 1 : 0 ); };
        $r = $this->ok( 'expUrlServices', 'setvalid', array( $id ), array( 'valid' => $was ? '0' : '1' ) )['data'];
        $this->assertSame( !$was, $r['is_valid'] );
        $this->fails( 400, 'expUrlServices', 'setvalid', array( $id ), array( 'valid' => 'maybe' ) );
    }

    public function testNeedsTheUrlPolicy()
    {
        $this->loginAnonymous();
        $r = $this->call( 'expUrlServices', 'list', array( 5, 0 ) );
        $this->assertFalse( $r['ok'] );
        $this->assertContains( $r['error']['code'], array( 401, 403 ) );
    }
}
