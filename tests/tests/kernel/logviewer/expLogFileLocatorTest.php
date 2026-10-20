<?php
/**
 * Which files the log viewer may open (doc/eng-us/features/6.0/log-viewer.md, "Which files"), on a temporary root.
 *
 *  LV-30  the shape of an id: .., ., //, a leading /, a backslash, NUL and control characters are refused
 *  LV-31  traversal and absolute paths never resolve; a url-encoded .. is not decoded
 *  LV-32  a symlinked file pointing outside the allowed directories is refused (and not listed); one pointing to an
 *         allowed file resolves
 *  LV-33  a directory of Directories[] (also through *) that resolves outside the root is never allowed
 *  LV-34  FilePatterns[], ExcludePatterns[], subdirectories are neither listed nor opened
 *  LV-35  groups kernel, site, extensions, webserver (empty by default), archives; rotations and .gz; the file level
 *  LV-36  rotationsOf() in number order; defaultID()
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expLogViewerTestFixtures.php';

class expLogFileLocatorTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    protected $root;

    /** @var string a directory outside the root */
    protected $outside;

    protected function setUp(): void
    {
        $this->root = expLogViewerTestFixtures::root( 'lvloc' );
        $this->outside = expLogViewerTestFixtures::root( 'lvout' );
        $log = $this->root . '/var/log/';
        foreach ( array( 'error.log', 'warning.log', 'expmcp.log', 'error.log.1', 'error.log.2.gz', 'error.log.10', 'old.log.gz',
                         'notes.txt', 'error.log.bak', 'secret-keys.log' ) as $name )
            file_put_contents( $log . $name, "[ Oct 17 2026 01:00:00 ] [192.0.2.1] x\n" );
        file_put_contents( $this->root . '/var/site/log/storage.log', "x\n" );
        mkdir( $log . 'audit' );
        file_put_contents( $log . 'audit/inside.log', "x\n" );
        file_put_contents( $this->outside . '/var/log/stolen.log', "secret\n" );
        symlink( $this->outside . '/var/log/stolen.log', $log . 'link-out.log' );
        symlink( $log . 'warning.log', $log . 'link-in.log' );
        // a site whose log directory is a link to elsewhere
        mkdir( $this->root . '/var/evil' );
        symlink( $this->outside . '/var/log', $this->root . '/var/evil/log' );
    }

    protected function tearDown(): void
    {
        expLogViewerTestFixtures::remove( $this->root );
        expLogViewerTestFixtures::remove( $this->outside );
    }

    protected function locator( array $values = array() )
    {
        return new expLogFileLocator( expLogViewerTestFixtures::config( $this->root, $values ), 'var/log' );
    }

    /** LV-30 */
    public function testIdShape()
    {
        $this->assertTrue( expLogFileLocator::validId( 'var/log/error.log' ) );
        foreach ( array( '', '../error.log', 'var/log/../error.log', './var/log/error.log', 'var/./log/error.log', 'var//log/error.log',
                         '/var/log/error.log', 'var\\log\\error.log', "var/log/error.log\0.txt", "var/log/err\nor.log", 'var/log/',
                         str_repeat( 'a/', 300 ) . 'x.log' ) as $bad )
            $this->assertFalse( expLogFileLocator::validId( $bad ), json_encode( $bad ) );
        $this->assertFalse( expLogFileLocator::validId( array( 'var/log/error.log' ) ) );
    }

    /** LV-31 */
    public function testTraversalAndAbsolute()
    {
        $l = $this->locator();
        $this->assertNotNull( $l->resolve( 'var/log/error.log' ) );
        $this->assertNull( $l->resolve( 'var/log/../../' . basename( $this->outside ) . '/var/log/stolen.log' ) );
        $this->assertNull( $l->resolve( $this->root . '/var/log/error.log' ) );
        $this->assertNull( $l->resolve( 'var/log/%2e%2e/var/log/error.log' ) );
        $this->assertNull( $l->resolve( 'var/log/missing.log' ) );
        $this->assertTrue( expLogFileLocator::inside( $this->root . '/var', $this->root ) );
        $this->assertTrue( expLogFileLocator::inside( $this->root, $this->root ) );
        $this->assertFalse( expLogFileLocator::inside( $this->root . 'x/var', $this->root ) );
        $this->assertFalse( expLogFileLocator::inside( '/etc', '' ) );
    }

    /** LV-32 */
    public function testSymlinkedFiles()
    {
        $l = $this->locator();
        $this->assertNull( $l->resolve( 'var/log/link-out.log' ) );
        $in = $l->resolve( 'var/log/link-in.log' );
        $this->assertNotNull( $in );
        $ids = $this->ids( $l->listFiles() );
        $this->assertNotContains( 'var/log/link-out.log', $ids );
        $this->assertContains( 'var/log/link-in.log', $ids );
    }

    /** LV-33 */
    public function testDirectoriesOutsideTheRoot()
    {
        $l = $this->locator();
        $dirs = $l->directories();
        $this->assertContains( 'var/log', $dirs );
        $this->assertContains( 'var/site/log', $dirs );
        $this->assertNotContains( 'var/evil/log', $dirs );
        foreach ( array_keys( $dirs ) as $real )
            $this->assertTrue( expLogFileLocator::inside( $real, $this->root ) );
        $this->assertNull( $l->resolve( 'var/evil/log/stolen.log' ) );

        $l = $this->locator( array( 'LogViewerSettings' => array( 'Directories' => array( $this->outside . '/var/log', '../' . basename( $this->outside ) . '/var/log', 'var/evil/log' ) ) ) );
        $this->assertSame( array(), $l->directories() );
        $this->assertSame( array(), $this->ids( $l->listFiles() ) );
    }

    /** LV-34 */
    public function testPatternsAndSubdirectories()
    {
        $l = $this->locator( array( 'LogViewerSettings' => array( 'ExcludePatterns' => array( 'secret*.log' ) ) ) );
        $ids = $this->ids( $l->listFiles() );
        foreach ( array( 'var/log/notes.txt', 'var/log/error.log.bak', 'var/log/secret-keys.log', 'var/log/audit/inside.log', 'var/log/audit' ) as $id )
        {
            $this->assertNotContains( $id, $ids );
            $this->assertNull( $l->resolve( $id ), $id );
        }
        $this->assertFalse( $l->nameAllowed( '.hidden.log' ) );
        $this->assertNotNull( $this->locator()->resolve( 'var/log/secret-keys.log' ), 'without the exclusion it is a log file' );
    }

    /** LV-35 */
    public function testGroups()
    {
        $groups = $this->locator()->listFiles();
        $this->assertSame( array( 'kernel', 'site', 'extensions', 'webserver', 'archives' ), array_keys( $groups ) );
        $this->assertSame( array(), $groups['webserver'], 'the web server logs are opt-in: none without LogPath[]' );
        $this->assertSame( array( 'var/log/error.log', 'var/log/warning.log' ), array_column( $groups['kernel'], 'id' ) );
        $this->assertSame( array( 'var/site/log/storage.log' ), array_column( $groups['site'], 'id' ) );
        $this->assertContains( 'var/log/expmcp.log', array_column( $groups['extensions'], 'id' ) );
        $archives = array_column( $groups['archives'], 'rotation', 'id' );
        $this->assertSame( 1, $archives['var/log/error.log.1'] );
        $this->assertSame( 2, $archives['var/log/error.log.2.gz'] );
        $this->assertSame( 10, $archives['var/log/error.log.10'] );
        $this->assertSame( -1, $archives['var/log/old.log.gz'] );

        $l = $this->locator();
        $f = $l->resolve( 'var/log/error.log.2.gz' );
        $this->assertTrue( $f['gz'] );
        $this->assertTrue( $f['archive'] );
        $this->assertSame( 'error.log', $f['base'] );
        $this->assertSame( 'var/log/error.log', $f['base_id'] );
        $this->assertSame( 'error', $f['level'] );
        $this->assertSame( 'warning', $l->fileLevel( 'warning.log' ) );
        $this->assertSame( '', $l->fileLevel( 'expmcp.log' ) );
        $this->assertSame( realpath( $this->root . '/var/log/error.log.2.gz' ), $f['path'] );
    }

    /** LV-36 */
    public function testRotationsAndDefault()
    {
        $l = $this->locator();
        $rot = $l->rotationsOf( $l->resolve( 'var/log/error.log' ) );
        $this->assertSame( array( 'var/log/error.log.1', 'var/log/error.log.2.gz', 'var/log/error.log.10' ), array_column( $rot, 'id' ) );
        $this->assertSame( 'var/log/error.log', $l->defaultID() );
        $l = $this->locator( array( 'LogViewerSettings' => array( 'DefaultFile' => 'var/log/none.log' ) ) );
        $this->assertSame( 'var/log/error.log', $l->defaultID(), 'the first kernel log' );
        $l = $this->locator( array( 'LogViewerSettings' => array( 'DefaultFile' => '../etc/passwd' ) ) );
        $this->assertSame( 'var/log/error.log', $l->defaultID() );
    }

    protected function ids( array $groups )
    {
        $ids = array();
        foreach ( $groups as $list )
            foreach ( $list as $info )
                $ids[] = $info['id'];
        return $ids;
    }
}
