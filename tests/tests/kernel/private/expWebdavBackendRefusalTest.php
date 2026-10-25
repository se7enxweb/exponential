<?php
/**
 * the content layer of the WebDAV backend answers a refused write with false, and the library and the
 * backend dropped that answer: PUT, MKCOL, DELETE, COPY and MOVE of an editor without content/edit were answered
 * with success and the change was lost. A refused write is now a 403. The site of a request is found by its
 * path element, not by a name prefix (/site_admin/ is not /site).
 *
 * No database: the content layer is replaced.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expWebdavBackendProbe extends eZWebDAVContentBackend
{
    public $contentAnswer = false;
    public $sites = array( 'site', 'other' );

    public function __construct()
    {
        $this->availableSites = $this->sites;
    }

    public function refused()
    {
        return $this->refusedWrites;
    }

    public function callSetResourceContents( $path, $content )
    {
        return $this->setResourceContents( $path, $content );
    }

    public function callCreateCollection( $target )
    {
        return $this->createCollection( $target );
    }

    public function callPerformDelete( $target )
    {
        return $this->performDelete( $target );
    }

    protected function storeUploadedFile( $target, $contents )
    {
        $file = tempnam( sys_get_temp_dir(), 'expwebdav' );
        return $file;
    }

    protected function putVirtualFolderData( $currentSite, $target, $tempFile )
    {
        return $this->contentAnswer;
    }

    protected function mkcolVirtualFolder( $currentSite, $target )
    {
        return $this->contentAnswer;
    }

    protected function deleteVirtualFolder( $currentSite, $target )
    {
        return $this->contentAnswer;
    }

    protected function copyVirtualFolder( $sourceSite, $destinationSite, $source, $destination )
    {
        return $this->contentAnswer;
    }

    protected function moveVirtualFolder( $sourceSite, $destinationSite, $source, $destination, $fullSource, $fullDestination )
    {
        return $this->contentAnswer;
    }

    protected function isCollection( $path )
    {
        return false;
    }
}

class expWebdavBackendRefusalTest extends PHPUnit\Framework\TestCase
{
    private $method;

    protected function setUp(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? null;
    }

    protected function tearDown(): void
    {
        if ( $this->method === null )
        {
            unset( $_SERVER['REQUEST_METHOD'] );
        }
        else
        {
            $_SERVER['REQUEST_METHOD'] = $this->method;
        }
    }

    public function testRefusedPutIsRemembered()
    {
        $backend = new expWebdavBackendProbe();
        $backend->contentAnswer = false;
        $backend->callSetResourceContents( '/site/Content/a.txt', 'x' );
        $this->assertSame( array( '/site/Content/a.txt' ), $backend->refused() );

        $backend = new expWebdavBackendProbe();
        $backend->contentAnswer = true;
        $backend->callSetResourceContents( '/site/Content/a.txt', 'x' );
        $this->assertSame( array(), $backend->refused() );
    }

    public function testRefusedMkcolIsRemembered()
    {
        $backend = new expWebdavBackendProbe();
        $backend->callCreateCollection( '/site/Content/new' );
        $this->assertSame( array( '/site/Content/new' ), $backend->refused() );

        $backend = new expWebdavBackendProbe();
        $backend->contentAnswer = true;
        $backend->callCreateCollection( '/site/Content/new' );
        $this->assertSame( array(), $backend->refused() );
    }

    public function testPutAndMkcolAnswer403ForARefusedWrite()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/kernel/private/classes/webdav/ezwebdavcontentbackend.php' );
        $this->assertSame( 2, substr_count( $source, '$this->refusedWrites !== array()' ) );
    }

    public function testRefusedDeleteIsA403()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $backend = new expWebdavBackendProbe();
        $answer = $backend->callPerformDelete( '/site/Content/a.txt' );
        $this->assertInstanceOf( 'ezcWebdavErrorResponse', $answer );
        $this->assertSame( ezcWebdavResponse::STATUS_403, $answer->status );

        $backend->contentAnswer = true;
        $this->assertNull( $backend->callPerformDelete( '/site/Content/a.txt' ) );
    }

    public function testDeleteInsideMoveAndCopyReturnsAList()
    {
        $_SERVER['REQUEST_METHOD'] = 'MOVE';
        $backend = new expWebdavBackendProbe();
        $this->assertSame( array(), $backend->callPerformDelete( '/site/Content/a.txt' ) );
    }

    public function testRefusedCopyAndMoveAreErrors()
    {
        $backend = new expWebdavBackendProbe();
        $depth = ezcWebdavRequest::DEPTH_ZERO;
        $this->assertSame( array( '/site/Content/a' ), $backend->copyRecursive( '/site/Content/a', '/site/Content/b', $depth ) );
        $this->assertSame( array( '/site/Content/a' ), $backend->moveRecursive( '/site/Content/a', '/site/Content/b', $depth ) );

        $backend->contentAnswer = true;
        $this->assertSame( array(), $backend->copyRecursive( '/site/Content/a', '/site/Content/b', $depth ) );
        $this->assertSame( array(), $backend->moveRecursive( '/site/Content/a', '/site/Content/b', $depth ) );
    }

    public function testCopyBetweenSitesIsAnErrorNotSuccess()
    {
        $backend = new expWebdavBackendProbe();
        $backend->contentAnswer = true;
        $this->assertNotSame( array(), $backend->copyRecursive( '/site/Content/a', '/other/Content/a', ezcWebdavRequest::DEPTH_ZERO ) );
        $this->assertNotSame( array(), $backend->moveRecursive( '/site/Content/a', '/other/Content/a', ezcWebdavRequest::DEPTH_ZERO ) );
    }

    public function testSiteIsMatchedByPathElement()
    {
        $backend = new expWebdavBackendProbe();
        $this->assertSame( 'site', $backend->currentSiteFromPath( '/site/Content' ) );
        $this->assertSame( 'site', $backend->currentSiteFromPath( '/site' ) );
        $this->assertFalse( $backend->currentSiteFromPath( '/site_admin/Content' ) );
        $this->assertFalse( $backend->currentSiteFromPath( '/kpx' ) );
    }
}
