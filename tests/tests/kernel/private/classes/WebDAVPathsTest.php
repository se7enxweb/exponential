<?php
/**
 * The paths of the WebDAV server (index_webdav.php): the siteaccess PathPrefix every operation resolves its Content paths
 * with (eZWebDAVContentBackend::pathWithPrefix()), the key of the stored properties (locks) of a resource
 * (eZWebDAVContentBackend::propertyStoragePath()) and the encoding of URIs in responses
 * (expWebDAVPathFactory::encodePathElements()). No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class WebDAVPathsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        if ( !class_exists( 'ezcWebdavAutomaticPathFactory' ) )
            $this->markTestSkipped( 'zetacomponents/webdav is not installed' );
    }

    public function testNoPrefixLeavesThePath()
    {
        $this->assertSame( 'Folder1/file1', eZWebDAVContentBackend::pathWithPrefix( 'Folder1/file1', '' ) );
        $this->assertSame( '', eZWebDAVContentBackend::pathWithPrefix( '', '' ) );
    }

    public function testPrefixIsPrepended()
    {
        // a file written to /<siteaccess>/Content/name lands below the PathPrefix node, where it is read back
        $this->assertSame( 'fit-healthy/report', eZWebDAVContentBackend::pathWithPrefix( 'report', 'fit-healthy' ) );
        $this->assertSame( 'fit-healthy/a/b', eZWebDAVContentBackend::pathWithPrefix( '/a/b/', 'fit-healthy' ) );
        // the Content folder itself is the PathPrefix node
        $this->assertSame( 'fit-healthy', eZWebDAVContentBackend::pathWithPrefix( '', 'fit-healthy' ) );
    }

    public function testPrefixIsNotAddedTwice()
    {
        $this->assertSame( 'fit-healthy/a', eZWebDAVContentBackend::pathWithPrefix( 'fit-healthy/a', 'fit-healthy' ) );
        $this->assertSame( 'Fit-Healthy', eZWebDAVContentBackend::pathWithPrefix( 'Fit-Healthy', 'fit-healthy' ) );
        // a path that only starts with the same letters still gets it
        $this->assertSame( 'fit-healthy/fit-healthy-site-info', eZWebDAVContentBackend::pathWithPrefix( 'fit-healthy-site-info', 'fit-healthy' ) );
    }

    public function testExcludedPathsKeepTheirPlace()
    {
        $exclude = array( 'media', 'users' );
        $this->assertSame( 'media/images/a', eZWebDAVContentBackend::pathWithPrefix( 'media/images/a', 'fit-healthy', $exclude ) );
        $this->assertSame( 'users', eZWebDAVContentBackend::pathWithPrefix( 'users', 'fit-healthy', $exclude ) );
        $this->assertSame( 'fit-healthy/mediathek', eZWebDAVContentBackend::pathWithPrefix( 'mediathek', 'fit-healthy', $exclude ) );
        $this->assertSame( 'fit-healthy/x', eZWebDAVContentBackend::pathWithPrefix( 'x', 'fit-healthy', array( '' ) ) );
    }

    public function testPropertyStoragePathIgnoresSlashes()
    {
        $this->assertSame( '/site/Content/a b.txt', eZWebDAVContentBackend::propertyStoragePath( '/site/Content/a b.txt' ) );
        $this->assertSame( '/site/Content/folder', eZWebDAVContentBackend::propertyStoragePath( 'site/Content/folder/' ) );
        $this->assertSame( '/', eZWebDAVContentBackend::propertyStoragePath( '/' ) );
    }

    public function testUriPathElementsAreEncoded()
    {
        $this->assertSame( '/site/Content/Annual%20report%20%26%20notes.docx',
                           expWebDAVPathFactory::encodePathElements( '/site/Content/Annual report & notes.docx' ) );
        $this->assertSame( '/site/Content/', expWebDAVPathFactory::encodePathElements( '/site/Content/' ) );
        $this->assertSame( '/site/Content/%C3%9Cbersicht', expWebDAVPathFactory::encodePathElements( '/site/Content/Übersicht' ) );
    }
}
