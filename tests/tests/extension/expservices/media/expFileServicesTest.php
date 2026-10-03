<?php
require_once __DIR__ . '/expMediaTestCase.php';

/** expfile: binary file information, download and signed URLs, statistics. */
class expFileServicesTest extends expMediaTestCase
{
    public function testEveryServiceIsDeclared()
    {
        $this->assertDeclared( 'expFileServices', 16 );
    }

    public function testStatsHaveGroups()
    {
        $s = $this->ok( 'expFileServices', 'stats' )['data'];
        $this->assertGreaterThan( 0, $s['files'] );
        $this->assertIsArray( $s['groups'] );
    }

    public function testByMimeListsTypes()
    {
        $this->assertNotEmpty( $this->ok( 'expFileServices', 'bymime' )['data'] );
    }

    public function testTopIsLimited()
    {
        $this->assertLessThanOrEqual( 3, count( $this->ok( 'expFileServices', 'top', array( 3 ) )['data'] ) );
    }

    public function testMimeGroup()
    {
        $this->assertSame( 'image', $this->ok( 'expFileServices', 'mimegroup', array( 'image/png' ) )['data']['group'] );
    }

    public function testSafeName()
    {
        $r = $this->ok( 'expFileServices', 'safename', array( 'report.pdf' ) )['data'];
        $this->assertArrayHasKey( 'safe_name', $r );
    }

    public function testInfoOfAFileNode()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezbinaryfile' );
        $i = $this->ok( 'expFileServices', 'info', array( $node->attribute( 'node_id' ), $attr->contentClassAttributeIdentifier() ) )['data'];
        $this->assertTrue( $i['has_content'] );
        $this->assertNotSame( '', $i['file']['original_filename'] );
    }

    public function testAttributesOfAFileNode()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $this->assertNotEmpty( $this->ok( 'expFileServices', 'attributes', array( $node->attribute( 'node_id' ) ) )['data'] );
    }

    public function testDownloadUrlPointsToContentDownload()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $u = $this->ok( 'expFileServices', 'downloadurl', array( $node->attribute( 'node_id' ) ) )['data'];
        $this->assertStringContainsString( 'content/download/', $u['url'] );
    }

    public function testSignedUrlVerifies()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $s = $this->ok( 'expFileServices', 'signedurl', array( $node->attribute( 'node_id' ), '', 60 ) )['data'];
        $v = $this->ok( 'expFileServices', 'verify', array( $s['node'], $s['attribute_id'], $s['expires'], $s['signature'] ) )['data'];
        $this->assertTrue( $v['valid'] );
    }

    public function testTamperedSignatureIsRejected()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $s = $this->ok( 'expFileServices', 'signedurl', array( $node->attribute( 'node_id' ), '', 60 ) )['data'];
        $v = $this->ok( 'expFileServices', 'verify', array( $s['node'], $s['attribute_id'], $s['expires'] + 1000, $s['signature'] ) )['data'];
        $this->assertFalse( $v['valid'] );
        $this->assertSame( 'signature', $v['reason'] );
    }

    public function testExpiredLinkIsRejected()
    {
        $expires = time() - 10;
        $sig = hash_hmac( 'sha256', '1|2|' . $expires, 'x' );
        $v = $this->ok( 'expFileServices', 'verify', array( 1, 2, $expires, $sig ) )['data'];
        $this->assertFalse( $v['valid'] );
    }

    public function testSignedUrlLifetimeIsBounded()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $this->fails( 400, 'expFileServices', 'signedurl', array( $node->attribute( 'node_id' ), '', 5 ) );
        $this->fails( 400, 'expFileServices', 'signedurl', array( $node->attribute( 'node_id' ), '', 999999 ) );
    }

    public function testVerifyIsPublic()
    {
        $this->loginAnonymous();
        $this->assertTrue( $this->call( 'expFileServices', 'verify', array( 1, 1, time() + 100, 'abc' ) )['ok'] );
    }

    public function testExistsAndDownloads()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $this->assertArrayHasKey( 'exists', $this->ok( 'expFileServices', 'exists', array( $node->attribute( 'node_id' ) ) )['data'] );
        $this->assertArrayHasKey( 'download_count', $this->ok( 'expFileServices', 'downloads', array( $node->attribute( 'node_id' ) ) )['data'] );
    }

    public function testListIsPaged()
    {
        $this->assertPaged( $this->ok( 'expFileServices', 'list', array( 2, 5, 0 ) ) );
    }

    public function testByNameFindsTheFile()
    {
        list( $node, $attr ) = $this->nodeWith( 'ezbinaryfile' );
        $name = $attr->content()->attribute( 'original_filename' );
        $r = $this->ok( 'expFileServices', 'byname', array( $name, 5, 0 ) );
        $this->assertPaged( $r );
        $this->assertGreaterThan( 0, $r['meta']['total'] );
    }

    public function testNodeWithoutFileIs404()
    {
        $this->fails( 404, 'expFileServices', 'info', array( 2 ) );
    }

    public function testResetDownloadsNeedsPost()
    {
        list( $node ) = $this->nodeWith( 'ezbinaryfile' );
        $this->fails( 403, 'expFileServices', 'resetdownloads', array( $node->attribute( 'node_id' ) ) );
    }
}
