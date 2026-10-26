<?php
/**
 * Tests of expPackageArchive: the tar headers of a package archive are scanned before
 * ezcArchive reads them, and what is extracted gets modes 0644/0755.
 *
 * - a GNU archive with a long name ("L") is read and extracted as before, a V7 archive passes the scan;
 * - a PAX archive ("x", "g") is refused with a message of its own, and quickly: ezcArchive's GNU reader looped on it
 *   until max_execution_time (checked in a child process with a deadline, so a regression fails instead of hanging
 *   the suite);
 * - a header type ezcArchive cannot read ("V", "K", "\0" in an archive with the ustar magic) is refused;
 * - a long name header that says it is larger than 4096 bytes is refused before anything of it is read;
 * - a size field of 12 octal digits without terminator is read, a base-256 or not octal size is refused;
 * - a file packed as 04755 comes out 0644, directories packed as 0777 and 0000 come out 0755.
 *
 * Archives are written byte by byte under var/tmp and removed in tearDown(). No database.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/eZPackageTestFixtures.php';

class expPackageArchiveTest extends PHPUnit\Framework\TestCase
{
    const GNU_MAGIC = "ustar  \0";
    const USTAR_MAGIC = "ustar\x0000";

    private $dir;
    private $limits = array( 'max_archive_size' => 1000000, 'max_unpacked_size' => 2000000, 'max_entries' => 50,
                             'suffixes' => array( 'ezpkg', 'tar.gz', 'tgz' ) );

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = eZPackageTestFixtures::tempDir( 'packagearchive' );
    }

    protected function tearDown(): void
    {
        if ( $this->dir && is_dir( $this->dir ) )
        {
            // a directory left at 0000 by a failing test could not be removed
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                     RecursiveIteratorIterator::SELF_FIRST ) as $item )
            {
                if ( $item->isDir() )
                    @chmod( $item->getPathname(), 0755 );
            }
            eZDir::recursiveDelete( $this->dir );
        }
    }

    /**
     * One tar header block.
     */
    private static function header( $name, $size, $type, $mode = '0000644', $magic = self::USTAR_MAGIC, $sizeField = null )
    {
        $header = str_pad( $name, 100, "\0" )
                . $mode . "\0"
                . "0000000\0" . "0000000\0"
                . ( $sizeField !== null ? $sizeField : str_pad( decoct( $size ), 11, '0', STR_PAD_LEFT ) . "\0" )
                . str_pad( decoct( 1700000000 ), 11, '0', STR_PAD_LEFT ) . "\0"
                . '        '
                . $type
                . str_repeat( "\0", 100 )
                . $magic
                . str_pad( 'root', 32, "\0" ) . str_pad( 'root', 32, "\0" )
                . "0000000\0" . "0000000\0"
                . str_repeat( "\0", 155 ) . str_repeat( "\0", 12 );
        $sum = 0;
        for ( $i = 0; $i < 512; $i++ )
            $sum += ord( $header[$i] );
        return substr( $header, 0, 148 ) . str_pad( decoct( $sum ), 6, '0', STR_PAD_LEFT ) . "\0 " . substr( $header, 156 );
    }

    private static function data( $content )
    {
        return $content . str_repeat( "\0", ( 512 - strlen( $content ) % 512 ) % 512 );
    }

    private static function file( $name, $content, $mode = '0000644', $magic = self::USTAR_MAGIC, $type = '0' )
    {
        return self::header( $name, strlen( $content ), $type, $mode, $magic ) . self::data( $content );
    }

    private static function dir( $name, $mode = '0000755', $magic = self::USTAR_MAGIC )
    {
        return self::header( $name, 0, '5', $mode, $magic );
    }

    private static function definition( $magic = self::USTAR_MAGIC )
    {
        return self::file( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => 'k1_archive' ) ), '0000644', $magic );
    }

    private function write( $tar, $name = 'k1_archive.ezpkg' )
    {
        $path = $this->dir . '/' . $name;
        file_put_contents( $path, gzencode( $tar . str_repeat( "\0", 1024 ) ) );
        return $path;
    }

    private function inspect( $path )
    {
        $result = eZPackageUploadInspector::inspect( $path, 'k1_archive.ezpkg', $this->limits );
        return $result['ok'] ? 'ok' : $result['code'];
    }

    public function testGnuLongNameIsReadAndExtracted()
    {
        $long = 'ezextension/k1_archive/' . str_repeat( 'd', 90 ) . '/' . str_repeat( 'f', 80 ) . '.tpl';
        $tar = self::definition( self::GNU_MAGIC )
             . self::header( '././@LongLink', strlen( $long ) + 1, 'L', '0000644', self::GNU_MAGIC ) . self::data( $long . "\0" )
             . self::file( substr( $long, 0, 99 ), 'long content', '0000644', self::GNU_MAGIC );
        $path = $this->write( $tar );
        $this->assertNull( expPackageArchive::scan( $path, $this->limits ) );
        $this->assertSame( 'ok', $this->inspect( $path ) );

        $target = $this->dir . '/out';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error ), (string)$error );
        $this->assertSame( 'long content', file_get_contents( $target . '/' . $long ) );
    }

    /**
     * An archive written the way eZPackage::exportToArchive() writes one (ezcArchive's ustar writer, a path longer
     * than 100 bytes split into the prefix field, directories with a trailing slash, then gzip) passes the scan and is
     * imported: every package the kernel exports has to be importable again.
     */
    public function testArchiveWrittenAsTheExporterWritesItPassesTheScan()
    {
        $source = $this->dir . '/src';
        $deep = 'ezextension/k1_archive/design/standard/templates/' . str_repeat( 'n', 60 ) . '/' . str_repeat( 'm', 40 );
        eZDir::mkdir( $source . '/' . $deep, false, true );
        file_put_contents( $source . '/package.xml', eZPackageTestFixtures::definition( array( 'name' => 'k1_archive' ) ) );
        file_put_contents( $source . '/' . $deep . '/full.tpl', 'deep template' );

        $tmp = $this->dir . '/export.tmp';
        $archive = ezcArchive::open( $tmp, ezcArchive::TAR_USTAR );
        $archive->truncate();
        $fileList = array();
        eZDir::recursiveList( $source, $source, $fileList );
        foreach ( $fileList as $fileInfo )
        {
            $path = $fileInfo['type'] === 'dir' ? $fileInfo['path'] . '/' . $fileInfo['name'] . '/' : $fileInfo['path'] . '/' . $fileInfo['name'];
            $archive->append( array( $path ), $source . '/' );
        }
        $archive->close();
        $path = $this->dir . '/k1_archive.ezpkg';
        copy( $tmp, "compress.zlib://$path" );

        $this->assertGreaterThan( 100, strlen( $deep . '/full.tpl' ), 'the path needs the ustar prefix field' );
        $this->assertNull( expPackageArchive::scan( $path, $this->limits ) );
        $this->assertSame( 'ok', $this->inspect( $path ) );
        $target = $this->dir . '/out';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error ), (string)$error );
        $this->assertSame( 'deep template', file_get_contents( $target . '/' . $deep . '/full.tpl' ) );
        $this->assertSame( sprintf( '%04o', eZFile::fileMode( 0644 ) ), sprintf( '%04o', fileperms( $target . '/' . $deep . '/full.tpl' ) & 07777 ) );
    }

    public function testV7ArchivePassesTheScan()
    {
        $v7 = str_repeat( "\0", 8 );
        $tar = self::file( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => 'k1_archive' ) ), '0000644', $v7 )
             . self::file( 'a.txt', 'a', '0000644', $v7 );
        $path = $this->write( $tar );
        $this->assertSame( ezcArchive::TAR_V7, eZPackage::archiveTarType( $path ) );
        $this->assertNull( expPackageArchive::scan( $path, $this->limits ) );
        $this->assertSame( 'ok', $this->inspect( $path ) );
        // regular files typed "\0" pass the scan too (the V7 reader does not loop on them); that ezcArchive's V7
        // reader takes them for no file under PHP 8 (unpack() gives "", and "" == 0 is false) is not the scan's
        // business: entriesProblem() refuses them as before
        $nul = self::file( 'package.xml', eZPackageTestFixtures::definition( array( 'name' => 'k1_archive' ) ), '0000644', $v7, "\0" );
        $this->assertNull( expPackageArchive::scan( $this->write( $nul, 'nul.ezpkg' ), $this->limits ) );
    }

    public static function paxTypes()
    {
        return array( 'extended header' => array( 'x' ), 'global header' => array( 'g' ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'paxTypes' )]
    public function testPaxArchiveIsRefused( $type )
    {
        $record = "30 path=k1_archive/n\xC3\xA4me.txt\n";
        $tar = self::definition()
             . self::header( 'PaxHeaders.0/name.txt', strlen( $record ), $type ) . self::data( $record )
             . self::file( 'name.txt', 'x' );
        $path = $this->write( $tar );
        $start = microtime( true );
        $result = eZPackageUploadInspector::inspect( $path, 'k1_archive.ezpkg', $this->limits );
        $this->assertLessThan( 2.0, microtime( true ) - $start );
        $this->assertFalse( $result['ok'] );
        $this->assertSame( 'tar_pax', $result['code'] );
        // GNU tar spells the format "gnu", bsdtar (macOS, the BSDs) answers that with "No such format" and wants "gnutar"
        $message = eZPackageUploadInspector::message( $result, $this->limits );
        $this->assertStringContainsString( 'tar --format=gnu,', $message );
        $this->assertStringContainsString( 'tar --format=gnutar', $message );

        $this->assertFalse( expPackageJson::extractArchive( $path, $this->dir . '/out', $error ) );
        $this->assertStringContainsString( 'tar_pax', $error );
        $this->assertFalse( is_dir( $this->dir . '/out' ) );
        $name = '';
        $this->assertFalse( @eZPackage::importUnaudited( $path, $name, false ) );
    }

    /**
     * Before the scan, ezcArchive's GNU reader looped on this archive until max_execution_time. A child process with
     * a deadline makes a regression a failure instead of a suite that never ends.
     */
    public function testPaxArchiveDoesNotHang()
    {
        $record = "30 path=k1_archive/n\xC3\xA4me.txt\n";
        $path = $this->write( self::definition() . self::header( 'PaxHeaders.0/name.txt', strlen( $record ), 'x' ) . self::data( $record )
                            . self::file( 'name.txt', 'x' ) );
        $root = dirname( __DIR__, 5 );
        $code = 'chdir( ' . var_export( $root, true ) . ' ); require "autoload.php";'
              . '$r = eZPackageUploadInspector::inspect( ' . var_export( realpath( $path ), true ) . ', "k1.ezpkg" );'
              . 'echo $r["code"];';
        $process = proc_open( array( PHP_BINARY, '-d', 'max_execution_time=0', '-r', $code ),
                              array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        $this->assertIsResource( $process );
        stream_set_blocking( $pipes[1], false );
        $output = '';
        $deadline = microtime( true ) + 15;
        do
        {
            $output .= (string)stream_get_contents( $pipes[1] );
            $status = proc_get_status( $process );
            if ( !$status['running'] )
                break;
            usleep( 20000 );
        } while ( microtime( true ) < $deadline );
        $running = proc_get_status( $process )['running'];
        if ( $running )
            proc_terminate( $process, 9 );
        $output .= (string)stream_get_contents( $pipes[1] );
        $stderr = (string)stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        proc_close( $process );
        $this->assertFalse( $running, 'inspect() of a PAX archive did not end within 15 seconds' );
        $this->assertSame( 'tar_pax', trim( $output ), $stderr );
    }

    public static function unreadableTypes()
    {
        return array(
            'GNU multivolume' => array( 'V', self::GNU_MAGIC, 'tar_entry_type' ),
            'GNU dump dir' => array( 'D', self::GNU_MAGIC, 'tar_entry_type' ),
            'GNU long link' => array( 'K', self::GNU_MAGIC, 'tar_entry_type' ),
            'contiguous file' => array( '7', self::USTAR_MAGIC, 'tar_entry_type' ),
            'NUL typed file with ustar magic' => array( "\0", self::USTAR_MAGIC, 'tar_entry_type' ),
            'unknown letter' => array( 'Q', self::USTAR_MAGIC, 'tar_entry_type' ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'unreadableTypes' )]
    public function testUnreadableHeaderTypeIsRefused( $type, $magic, $code )
    {
        $tar = self::definition( $magic ) . self::header( 'odd', 3, $type, '0000644', $magic ) . self::data( 'abc' )
             . self::file( 'after.txt', 'x', '0000644', $magic );
        $path = $this->write( $tar );
        $start = microtime( true );
        $result = eZPackageUploadInspector::inspect( $path, 'k1_archive.ezpkg', $this->limits );
        $this->assertLessThan( 2.0, microtime( true ) - $start );
        $this->assertSame( $code, $result['code'] );
        $this->assertSame( 'odd', $result['path'] );
        $this->assertSame( $type, $result['type'] );
        $message = eZPackageUploadInspector::message( $result, $this->limits );
        $this->assertStringContainsString( '"odd"', $message );
        $this->assertStringContainsString( expPackageArchive::typeText( $type ), $message );
    }

    public function testHugeLongNameIsRefusedBeforeItIsRead()
    {
        // 8 GiB - 1 announced, nothing behind it: ezcArchive read whatever the header announced into memory
        $tar = self::definition( self::GNU_MAGIC ) . self::header( '././@LongLink', 0, 'L', '0000644', self::GNU_MAGIC, "77777777777\0" );
        $path = $this->write( $tar );
        $memory = memory_get_usage();
        $this->assertSame( 'tar_long_name', expPackageArchive::scan( $path, $this->limits )['code'] );
        $this->assertLessThan( 1048576, memory_get_usage() - $memory );
        $this->assertSame( 'tar_long_name', $this->inspect( $path ) );

        // the limit itself: 4095 bytes pass (4096 is a multiple of 512, see the next test), 4097 do not
        $name = str_repeat( 'n', 4094 ) . "\0";
        $ok = self::definition( self::GNU_MAGIC ) . self::header( '././@LongLink', 4095, 'L', '0000644', self::GNU_MAGIC ) . self::data( $name )
            . self::file( 'short', 'x', '0000644', self::GNU_MAGIC );
        $this->assertNull( expPackageArchive::scan( $this->write( $ok, 'ok.ezpkg' ), $this->limits ) );
        $over = self::definition( self::GNU_MAGIC ) . self::header( '././@LongLink', 4097, 'L', '0000644', self::GNU_MAGIC ) . self::data( $name . 'nn' )
              . self::file( 'short', 'x', '0000644', self::GNU_MAGIC );
        $this->assertSame( 'tar_long_name', expPackageArchive::scan( $this->write( $over, 'over.ezpkg' ), $this->limits )['code'] );
    }

    /**
     * ezcArchiveGnuHeader::readExtension() reads one block too many when the long name size is a multiple of 512: it
     * skips the real entry header and parses the entry's data as the next header. A forged PAX header in that data
     * passed the scan and looped ezcArchive again (found in review, measured: hung until killed).
     */
    public function testLongNameOfABlockMultipleIsRefused()
    {
        $forged = self::header( 'PaxHeaders.0/x', 10, 'x', '0000644', self::GNU_MAGIC );
        $tar = self::definition( self::GNU_MAGIC )
             . self::header( '././@LongLink', 512, 'L', '0000644', self::GNU_MAGIC ) . self::data( str_repeat( 'n', 511 ) . "\0" )
             . self::header( 'k1_archive/carrier', 512, '0', '0000644', self::GNU_MAGIC ) . $forged;
        $path = $this->write( $tar );
        $this->assertSame( 'tar_long_name', expPackageArchive::scan( $path, $this->limits )['code'] );
        $this->assertSame( 'tar_long_name', $this->inspect( $path ) );

        $empty = self::definition( self::GNU_MAGIC ) . self::header( '././@LongLink', 0, 'L', '0000644', self::GNU_MAGIC )
               . self::file( 'short', 'x', '0000644', self::GNU_MAGIC );
        $this->assertSame( 'tar_long_name', expPackageArchive::scan( $this->write( $empty, 'empty.ezpkg' ), $this->limits )['code'] );
    }

    /**
     * Old writers fill the size field with 12 octal digits and no terminator; ezcArchive reads that, so the scan does
     * too. The base-256 form (GNU, from 8 GiB on) and anything not octal stay unreadable.
     */
    public function testSizeFieldOfTwelveDigitsIsRead()
    {
        $content = 'twelve digits';
        $field = str_pad( decoct( strlen( $content ) ), 12, '0', STR_PAD_LEFT );
        $tar = self::definition()
             . self::header( 'k1_archive/twelve.txt', strlen( $content ), '0', '0000644', self::USTAR_MAGIC, $field )
             . self::data( $content );
        $path = $this->write( $tar );
        $this->assertNull( expPackageArchive::scan( $path, $this->limits ) );
        $this->assertSame( 'ok', $this->inspect( $path ) );
        $target = $this->dir . '/out';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error ), (string)$error );
        $this->assertSame( $content, file_get_contents( $target . '/k1_archive/twelve.txt' ) );

        $fields = array( 'base-256' => "\x80" . str_repeat( "\0", 10 ) . chr( strlen( $content ) ),
                         'not octal' => str_pad( '9', 11, '0', STR_PAD_LEFT ) . "\0",
                         'twelve digits, one not octal' => str_pad( '8', 12, '0', STR_PAD_LEFT ) );
        foreach ( $fields as $label => $field )
        {
            $bad = self::definition()
                 . self::header( 'k1_archive/bad.txt', strlen( $content ), '0', '0000644', self::USTAR_MAGIC, $field )
                 . self::data( $content );
            $problem = expPackageArchive::scan( $this->write( $bad, 'bad.ezpkg' ), $this->limits );
            $this->assertSame( 'unreadable', $problem['code'] ?? null, $label );
        }
    }

    public function testDamagedHeadersAreRefused()
    {
        // a checksum that does not match
        $tar = self::definition();
        $tar[148] = '7';
        $this->assertSame( 'unreadable', expPackageArchive::scan( $this->write( $tar, 'sum.ezpkg' ), $this->limits )['code'] );
        // the data ends inside an entry
        $cut = substr( self::definition() . self::file( 'big.bin', str_repeat( 'b', 5000 ) ), 0, -2048 );
        file_put_contents( $this->dir . '/cut.ezpkg', gzencode( $cut ) );
        $this->assertSame( 'unreadable', expPackageArchive::scan( $this->dir . '/cut.ezpkg', $this->limits )['code'] );
        // a long name that names no entry
        $dangling = self::definition( self::GNU_MAGIC ) . self::header( '././@LongLink', 6, 'L', '0000644', self::GNU_MAGIC ) . self::data( "a.txt\0" );
        $this->assertSame( 'unreadable', expPackageArchive::scan( $this->write( $dangling, 'dangling.ezpkg' ), $this->limits )['code'] );
    }

    public function testLimitsAreKeptByTheScan()
    {
        $many = self::definition();
        for ( $i = 0; $i < 60; $i++ )
            $many .= self::file( "f$i.txt", 'x' );
        $this->assertSame( 'too_many_entries', expPackageArchive::scan( $this->write( $many, 'many.ezpkg' ), $this->limits )['code'] );
        $big = self::definition() . self::file( 'big.bin', str_repeat( 'a', 300000 ) );
        $this->assertSame( 'too_large_unpacked', expPackageArchive::scan( $this->write( $big, 'big.ezpkg' ),
                                                                         array( 'max_unpacked_size' => 100000 ) + $this->limits )['code'] );
    }

    public function testExtractedModesAreMasked()
    {
        $tar = self::definition()
             . self::dir( 'open/', '0000777' )
             . self::dir( 'closed/', '0000000' )
             . self::file( 'closed/inside.txt', 'inside', '0000600' )
             . self::file( 'setuid.sh', "#!/bin/sh\n", '0004755' )
             . self::file( 'open/world.txt', 'w', '0000666' );
        $path = $this->write( $tar );
        $target = $this->dir . '/out';
        $this->assertTrue( expPackageJson::extractArchive( $path, $target, $error ), (string)$error );
        clearstatcache();
        $fileMode = eZFile::fileMode( 0644 );
        $dirMode = eZDir::dirMode( 0755 );
        foreach ( array( 'setuid.sh', 'open/world.txt', 'closed/inside.txt', 'package.xml' ) as $file )
            $this->assertSame( sprintf( '%04o', $fileMode ), sprintf( '%04o', fileperms( "$target/$file" ) & 07777 ), $file );
        foreach ( array( 'open', 'closed' ) as $dir )
            $this->assertSame( sprintf( '%04o', $dirMode ), sprintf( '%04o', fileperms( "$target/$dir" ) & 07777 ), $dir );
        $this->assertSame( 'inside', file_get_contents( "$target/closed/inside.txt" ) );

        // the same for a single entry of the archive expPackageArchive::open() hands out (extractCurrent(), as
        // import() and inspect() use it for package.xml)
        $archive = expPackageArchive::open( $path, $problem, $this->limits );
        $this->assertInstanceOf( 'ezcArchive', $archive, $problem ? expPackageArchive::problemText( $problem ) : '' );
        $single = $this->dir . '/single';
        foreach ( $archive as $entry )
        {
            if ( $entry->getPath() === 'setuid.sh' )
                $this->assertTrue( $archive->extractCurrent( $single ) );
        }
        clearstatcache();
        $this->assertSame( sprintf( '%04o', $fileMode ), sprintf( '%04o', fileperms( "$single/setuid.sh" ) & 07777 ) );
    }
}
