#!/usr/bin/env php
<?php
/**
 * Builds the package archives of tests/tests/kernel/classes/packages/fixtures/legacy/ the way the tools of each
 * generation wrote them, for expPackageArchiveLegacyFormatsTest.
 *
 * Usage (from the installation root): php tests/tests/kernel/classes/packages/fixtures/build-legacy-package-fixtures.php [--only <id>,...]
 *
 * Every fixture is a small package (a package.xml of its generation, a licence text, a settings file, a template whose
 * path is longer than 100 bytes, and for the repacked ones a hard link, symbolic links and a fifo). They are written
 * reproducibly (fixed times, owner 0, sorted), so a rebuild changes no byte unless a tool or this script changed.
 *
 * - ez3x-archive-tar-v7: the PHP Archive_Tar port eZ Publish 3.x packed with, written byte by byte: no magic,
 *   files typed "\0", directories "5" with a trailing slash, modes with the file type bits ("100666"), a long name as
 *   "L" header without magic (the ezwebin 1.0 packages are of this kind).
 * - ez4x-ezcarchive-ustar: ezcArchive's ustar writer, as the exporter of 4.x/5.x writes (prefix field for long paths).
 * - ez4x-archive-tar-ustar: the archive handler of 4.0 to 4.2, written byte by byte: the 3.x one with the magic
 *   "ustar" and an empty version ("ustar\0\0\0"), "L" long names with that magic (the ezjscore 4.2 packages).
 * - pear-archive-tar: PEAR Archive_Tar 1.4 (GNU magic), when /usr/share/pear has it; a symbolic link inside.
 * - gnutar-gnu, gnutar-posix, gnutar-ustar: GNU tar repacking an unpacked package with "tar -C dir -czf x.ezpkg ."
 *   in each format: "./" names, a hard link between two identical files, symbolic links to a file, to a directory,
 *   out of the package (relative and absolute), to nothing, a fifo; the posix one with PAX headers (a non-ASCII name).
 * - gnutar-gnu-plain: the same without links and fifo: only the "./" in front of every name.
 * - gnutar-v7: GNU tar --format=v7 (short names only and no fifo, which v7 cannot hold).
 * - bsdtar-macos-pax: the archive bsdtar on macOS writes (written byte by byte, there is no bsdtar here): "ustar\0"
 *   magic, "./" first, PAX headers "PaxHeader/<name>" with path, LIBARCHIVE.xattr.* and SCHILY.* records, an
 *   AppleDouble "._" file, a symbolic link, a hard link whose target name is only in the PAX linkpath.
 * - gnu-oddities: a GNU volume label "V", a long link name "K", a long name whose size is a multiple of 512, a dump
 *   directory "D", an old GNU sparse file "S" with an extension block.
 * - ustar-oddities: a PAX global header "g", a contiguous file "7", a file typed "\0" with the ustar magic, a character
 *   and a block device, a fifo, an unknown type "Q".
 */

$root = getcwd();
if ( !is_file( $root . '/autoload.php' ) || !is_dir( $root . '/kernel' ) )
{
    fwrite( STDERR, "FAIL: run from the installation root\n" );
    exit( 2 );
}
require $root . '/autoload.php';
$only = null;
for ( $i = 1; $i < count( $argv ); $i++ )
{
    if ( $argv[$i] === '--only' && isset( $argv[$i + 1] ) )
        $only = explode( ',', $argv[++$i] );
}

$outDir = __DIR__ . '/legacy';
$work = $root . '/var/tmp/legacy-package-fixtures-' . getmypid();
if ( !is_dir( $outDir ) )
    mkdir( $outDir, 0755, true );
mkdir( $work, 0755, true );

const FIXTURE_MTIME = 1161340236;
const LONG_DIR = 'ezextension/legacyfixture/design/standard/templates/node/view/a_directory_name_long_enough_for_the_prefix';
const LONG_FILE = 'a_template_whose_name_makes_the_path_longer_than_one_hundred_bytes.tpl';

/**
 * The package.xml of a generation ('3.8', '4.7', '5.4' or '6.0').
 */
function fixtureDefinition( $name, $generation )
{
    $named = $generation;
    return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
         . '<package version="' . ( $generation === '3.8' ? '3.5.2' : '3.5.4' ) . '" development="false" install_type="install">' . "\n"
         . "  <name>$name</name>\n"
         . "  <summary>Legacy archive fixture ($generation)</summary>\n"
         . "  <type value=\"extension\" />\n"
         . "  <ezpublish>\n    <version>$generation.0</version>\n    <named-version>$named</named-version>\n  </ezpublish>\n"
         . "  <maintainers>\n    <maintainer>\n      <name>Fixture Maintainer</name>\n      <email>fixture@example.invalid</email>\n      <role>lead</role>\n    </maintainer>\n  </maintainers>\n"
         . "  <packaging>\n    <timestamp>" . FIXTURE_MTIME . "</timestamp>\n    <host>fixture.example.invalid</host>\n  </packaging>\n"
         . "  <documents>\n    <document mime-type=\"text/plain\" name=\"LICENCE\" />\n  </documents>\n"
         . "  <simple-files />\n"
         . "  <version>\n    <number>1.0</number>\n    <release>1</release>\n  </version>\n"
         . "  <licence>GPL</licence>\n  <state>stable</state>\n"
         . "  <dependencies>\n    <provides />\n    <requires />\n    <obsoletes />\n    <conflicts />\n  </dependencies>\n"
         . "</package>\n";
}

/**
 * The files every fixture holds (path => content).
 */
function fixtureFiles( $name, $generation, $long = true )
{
    $files = array(
        'package.xml' => fixtureDefinition( $name, $generation ),
        'documents/LICENCE' => "GNU General Public License v2.0 (or any later version)\n",
        'ezextension/legacyfixture/settings/site.ini.append.php' => "<?php /* #?ini charset=\"utf-8\"?\n[DesignSettings]\nDesignExtensions[]=legacyfixture\n*/ ?>\n",
        'ezextension/legacyfixture/design/standard/images/logo.gif' => "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;",
    );
    if ( $long )
        $files[LONG_DIR . '/' . LONG_FILE] = "{* a template with a long path *}\n<p>{\$node.name|wash}</p>\n";
    return $files;
}

/**
 * Writes a package tree: the files, and with $links the links and the fifo of a repacked package.
 */
function writeTree( $dir, array $files, $links, $fifo = true )
{
    foreach ( $files as $path => $content )
    {
        if ( !is_dir( dirname( "$dir/$path" ) ) )
            mkdir( dirname( "$dir/$path" ), 0755, true );
        file_put_contents( "$dir/$path", $content );
        chmod( "$dir/$path", 0644 );
    }
    if ( $links )
    {
        $ext = "$dir/ezextension/legacyfixture";
        link( "$ext/design/standard/images/logo.gif", "$ext/design/standard/images/logo-copy.gif" );
        symlink( 'site.ini.append.php', "$ext/settings/site.ini.append" );
        symlink( 'standard', "$ext/design/current" );
        symlink( '../../../../etc/passwd', "$ext/passwd" );
        symlink( '/etc/hosts', "$ext/hosts" );
        symlink( 'missing.txt', "$ext/dangling" );
        if ( $fifo )
            posix_mkfifo( "$ext/pipe", 0644 );
        file_put_contents( "$dir/documents/Lizenz-\xC3\xBCbersicht.txt", "Lizenz\n" );
    }
    // every directory and file the same time, so the archives are reproducible
    $items = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
    foreach ( $items as $item )
    {
        if ( !$item->isLink() )
            touch( $item->getPathname(), FIXTURE_MTIME );
    }
    touch( $dir, FIXTURE_MTIME );
}

/**
 * One tar header block. $o: mode, uid, gid, mtime, link, magic ('v7', 'gnu', 'posix'), uname, gname, prefix,
 * modeFormat ('ustar' "0000644\0", 'archivetar' "%6s " of the full mode), extra (offset => bytes).
 */
function tarHeader( $name, $size, $type, array $o = array() )
{
    $o += array( 'mode' => 0644, 'uid' => 0, 'gid' => 0, 'mtime' => FIXTURE_MTIME, 'link' => '', 'magic' => 'posix',
                 'uname' => '', 'gname' => '', 'prefix' => '', 'modeFormat' => 'ustar', 'extra' => array(), 'sizeField' => null );
    if ( $o['modeFormat'] === 'archivetar' )
    {
        $mode = sprintf( '%6s ', decoct( $o['mode'] ) );
        $uid = sprintf( '%6s ', decoct( $o['uid'] ) );
        $gid = sprintf( '%6s ', decoct( $o['gid'] ) );
        $sizeField = sprintf( '%11s ', decoct( $size ) );
        $mtime = sprintf( '%11s', decoct( $o['mtime'] ) );
    }
    else
    {
        $mode = sprintf( '%07o', $o['mode'] & 07777 );
        $uid = sprintf( '%07o', $o['uid'] );
        $gid = sprintf( '%07o', $o['gid'] );
        $sizeField = sprintf( '%011o', $size );
        $mtime = sprintf( '%011o', $o['mtime'] );
    }
    if ( $o['sizeField'] !== null )
        $sizeField = $o['sizeField'];
    $magics = array( 'v7' => str_repeat( "\0", 8 ), 'gnu' => "ustar  \0", 'posix' => "ustar\x0000", 'ustar-nul' => "ustar\0\0\0" );
    $header = pack( 'a100a8a8a8a12a12', $name, $mode, $uid, $gid, $sizeField, $mtime )
            . '        '
            . pack( 'a1a100', $type, $o['link'] )
            . $magics[$o['magic']]
            . pack( 'a32a32a8a8a155a12', $o['uname'], $o['gname'], '', '', $o['prefix'], '' );
    foreach ( $o['extra'] as $offset => $bytes )
        $header = substr_replace( $header, $bytes, $offset, strlen( $bytes ) );
    $sum = 0;
    for ( $i = 0; $i < 512; $i++ )
        $sum += ord( $header[$i] );
    $checksum = $o['modeFormat'] === 'archivetar' ? pack( 'a8', sprintf( '%6s ', decoct( $sum ) ) ) : sprintf( '%06o', $sum ) . "\0 ";
    return substr( $header, 0, 148 ) . $checksum . substr( $header, 156 );
}

function tarData( $content )
{
    return $content . str_repeat( "\0", ( 512 - strlen( $content ) % 512 ) % 512 );
}

function tarEnd()
{
    return str_repeat( "\0", 1024 );
}

/**
 * A PAX record "<length> <key>=<value>\n".
 */
function paxRecord( $key, $value )
{
    $body = " $key=$value\n";
    $length = strlen( $body ) + 1;
    while ( strlen( (string)$length ) + strlen( $body ) !== $length )
        $length = strlen( (string)$length ) + strlen( $body );
    return $length . $body;
}

function writeFixture( $outDir, $id, $tar )
{
    $path = "$outDir/$id.ezpkg";
    file_put_contents( $path, gzencode( $tar, 9 ) );
    printf( "%-24s %6d bytes\n", $id, filesize( $path ) );
}

function runTar( array $args, $cwd )
{
    $process = proc_open( array_merge( array( 'tar' ), $args ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $cwd );
    $out = stream_get_contents( $pipes[1] );
    $err = stream_get_contents( $pipes[2] );
    fclose( $pipes[1] );
    fclose( $pipes[2] );
    $code = proc_close( $process );
    if ( $code > 1 )
        throw new RuntimeException( "tar " . implode( ' ', $args ) . " failed ($code): $err" );
    return $out;
}

$builders = array();

$builders['ez3x-archive-tar-v7'] = function ( $id ) use ( $outDir )
{
    $o = array( 'magic' => 'v7', 'modeFormat' => 'archivetar', 'uid' => 48, 'gid' => 48 );
    $tar = '';
    $dirs = array();
    foreach ( fixtureFiles( 'legacy_ez3x_archive_tar_v7', '3.8' ) as $path => $content )
    {
        // Archive_Tar listed each directory before its files
        $parts = explode( '/', $path );
        array_pop( $parts );
        $dir = '';
        foreach ( $parts as $part )
        {
            $dir .= $part . '/';
            if ( isset( $dirs[$dir] ) )
                continue;
            $dirs[$dir] = true;
            $tar .= tarHeader( $dir, 0, '5', array( 'mode' => 040777 ) + $o );
        }
        if ( strlen( $path ) > 99 )
        {
            // _writeLongHeader(): "././@LongLink", type "L", no magic
            $tar .= tarHeader( '././@LongLink', strlen( $path ), 'L', array( 'mode' => 0 ) + $o ) . tarData( $path );
        }
        $tar .= tarHeader( substr( $path, 0, 99 ), strlen( $content ), '', array( 'mode' => 0100666 ) + $o ) . tarData( $content );
    }
    writeFixture( $outDir, $id, $tar . tarEnd() );
};

$ezcBuilder = function ( $type, $name ) use ( $work, $outDir )
{
    return function ( $id ) use ( $type, $name, $work, $outDir )
    {
        $source = "$work/$id";
        writeTree( $source, fixtureFiles( $name, '4.7' ), false );
        $tmp = "$work/$id.tar";
        $archive = ezcArchive::open( $tmp, $type );
        $archive->truncate();
        $fileList = array();
        eZDir::recursiveList( $source, $source, $fileList );
        usort( $fileList, function ( $a, $b ) { return strcmp( $a['path'] . '/' . $a['name'], $b['path'] . '/' . $b['name'] ); } );
        foreach ( $fileList as $fileInfo )
        {
            $path = $fileInfo['type'] === 'dir' ? $fileInfo['path'] . '/' . $fileInfo['name'] . '/' : $fileInfo['path'] . '/' . $fileInfo['name'];
            $archive->append( array( $path ), $source . '/' );
        }
        $archive->close();
        writeFixture( $outDir, $id, file_get_contents( $tmp ) );
    };
};
$builders['ez4x-ezcarchive-ustar'] = $ezcBuilder( ezcArchive::TAR_USTAR, 'legacy_ez4x_ezcarchive_ustar' );

$builders['ez4x-archive-tar-ustar'] = function ( $id ) use ( $outDir )
{
    // the archive handler of 4.0 to 4.2: the Archive_Tar port of 3.x with the magic "ustar" and an empty version
    $o = array( 'magic' => 'ustar-nul', 'modeFormat' => 'archivetar' );
    $tar = '';
    $dirs = array();
    foreach ( fixtureFiles( 'legacy_ez4x_archive_tar_ustar', '4.2' ) as $path => $content )
    {
        $parts = explode( '/', $path );
        array_pop( $parts );
        $dir = '';
        foreach ( $parts as $part )
        {
            $dir .= $part . '/';
            if ( isset( $dirs[$dir] ) )
                continue;
            $dirs[$dir] = true;
            $tar .= tarHeader( $dir, 0, '5', array( 'mode' => 040755 ) + $o );
        }
        if ( strlen( $path ) > 99 )
            $tar .= tarHeader( '././@LongLink', strlen( $path ) + 1, 'L', array( 'mode' => 0100644 ) + $o ) . tarData( $path . "\0" );
        $tar .= tarHeader( substr( $path, 0, 100 ), strlen( $content ), '0', array( 'mode' => 0100644 ) + $o ) . tarData( $content );
    }
    writeFixture( $outDir, $id, $tar . tarEnd() );
};

$builders['pear-archive-tar'] = function ( $id ) use ( $work, $outDir )
{
    if ( !is_file( '/usr/share/pear/Archive/Tar.php' ) )
    {
        echo "$id: skipped, no PEAR Archive_Tar in /usr/share/pear (the committed fixture is kept)\n";
        return;
    }
    set_include_path( '/usr/share/pear' . PATH_SEPARATOR . get_include_path() );
    require_once 'Archive/Tar.php';
    $source = "$work/$id";
    writeTree( $source, fixtureFiles( 'legacy_pear_archive_tar', '5.4' ), false );
    symlink( 'site.ini.append.php', "$source/ezextension/legacyfixture/settings/site.ini.append" );
    $tmp = "$work/$id.tar";
    $previous = getcwd();
    chdir( $source );
    $tar = new Archive_Tar( $tmp );
    $list = array( 'package.xml', 'documents', 'ezextension' );
    $tar->create( $list );
    chdir( $previous );
    // Archive_Tar writes the file times and the owner of the files as they are; make them the fixture's
    $data = file_get_contents( $tmp );
    $fixed = '';
    for ( $pos = 0; $pos + 512 <= strlen( $data ); )
    {
        $header = substr( $data, $pos, 512 );
        if ( trim( $header, "\0" ) === '' )
            break;
        $size = octdec( trim( substr( $header, 124, 12 ), " \0" ) );
        $header = substr_replace( $header, sprintf( '%011o', FIXTURE_MTIME ) . ' ', 136, 12 );
        $header = substr_replace( $header, sprintf( '%07o', 0 ) . "\0" . sprintf( '%07o', 0 ) . "\0", 108, 16 );
        $header = substr_replace( $header, str_repeat( "\0", 64 ), 265, 64 );
        $header = substr_replace( $header, '        ', 148, 8 );
        $sum = 0;
        for ( $i = 0; $i < 512; $i++ )
            $sum += ord( $header[$i] );
        $header = substr_replace( $header, sprintf( '%06o', $sum ) . "\0 ", 148, 8 );
        $padded = (int)ceil( $size / 512 ) * 512;
        $fixed .= $header . substr( $data, $pos + 512, $padded );
        $pos += 512 + $padded;
    }
    writeFixture( $outDir, $id, $fixed . tarEnd() );
};

$gnuTarBuilder = function ( $format, $name, $long = true, $links = true ) use ( $work, $outDir )
{
    return function ( $id ) use ( $format, $name, $long, $links, $work, $outDir )
    {
        $source = "$work/$id";
        // GNU tar cannot put a fifo into a v7 archive
        writeTree( $source, fixtureFiles( $name, '6.0', $long ), $links, $format !== 'v7' );
        $args = array( '--format=' . $format, '--sort=name', '--owner=0', '--group=0', '--numeric-owner',
                       '--mtime=@' . FIXTURE_MTIME, '-C', $source, '-cf', '-', '.' );
        if ( $format === 'posix' )
            array_unshift( $args, '--pax-option=delete=atime,delete=ctime,exthdr.name=%d/PaxHeaders/%f' );
        writeFixture( $outDir, $id, runTar( $args, $source ) );
    };
};
$builders['gnutar-gnu'] = $gnuTarBuilder( 'gnu', 'legacy_gnutar_gnu' );
$builders['gnutar-gnu-plain'] = $gnuTarBuilder( 'gnu', 'legacy_gnutar_gnu_plain', true, false );
$builders['gnutar-posix'] = $gnuTarBuilder( 'posix', 'legacy_gnutar_posix' );
$builders['gnutar-ustar'] = $gnuTarBuilder( 'ustar', 'legacy_gnutar_ustar' );
$builders['gnutar-v7'] = $gnuTarBuilder( 'v7', 'legacy_gnutar_v7', false );

$builders['bsdtar-macos-pax'] = function ( $id ) use ( $outDir )
{
    $o = array( 'magic' => 'posix', 'uname' => 'staff', 'gname' => 'staff', 'uid' => 501, 'gid' => 20 );
    $quarantine = paxRecord( 'LIBARCHIVE.xattr.com.apple.quarantine', 'MDA4MTs2NTAwMDAwMDtTYWZhcmk7' )
                . paxRecord( 'SCHILY.xattr.com.apple.quarantine', '0081;65000000;Safari;' );
    $tar = tarHeader( './', 0, '5', array( 'mode' => 0755 ) + $o );
    $files = fixtureFiles( 'legacy_bsdtar_macos_pax', '6.0' );
    $files['documents/Lizenz-' . "\xC3\xBC" . 'bersicht.txt'] = "Lizenz\n";
    ksort( $files );
    $dirs = array();
    foreach ( $files as $path => $content )
    {
        $parts = explode( '/', $path );
        array_pop( $parts );
        $dir = '.';
        foreach ( $parts as $part )
        {
            $dir .= '/' . $part;
            if ( isset( $dirs[$dir] ) )
                continue;
            $dirs[$dir] = true;
            $tar .= tarHeader( $dir . '/', 0, '5', array( 'mode' => 0755 ) + $o );
        }
        $name = './' . $path;
        $records = '';
        // bsdtar writes a PAX header for a name that does not fit or is not ASCII, and for extended attributes
        if ( strlen( $name ) > 100 || preg_match( '/[\x80-\xFF]/', $name ) )
            $records .= paxRecord( 'path', $name );
        if ( $path === 'package.xml' )
            $records .= $quarantine;
        if ( $records !== '' )
        {
            $paxName = substr( dirname( $name ) . '/PaxHeader/' . basename( $name ), 0, 99 );
            $tar .= tarHeader( $paxName, strlen( $records ), 'x', $o ) . tarData( $records );
        }
        if ( $path === 'package.xml' )
        {
            // the AppleDouble file macOS adds for the extended attributes
            $double = "\x00\x05\x16\x07\x00\x02\x00\x00Mac OS X        \x00\x01\x00\x00\x00\x09\x00\x00\x00\x32\x00\x00\x00\x00";
            $tar .= tarHeader( './._package.xml', strlen( $double ), '0', $o ) . tarData( $double );
        }
        $tar .= tarHeader( substr( $name, 0, 100 ), strlen( $content ), '0', $o ) . tarData( $content );
    }
    // a symbolic link inside, and a hard link whose target is only in the PAX linkpath
    $tar .= tarHeader( './ezextension/legacyfixture/settings/site.ini.append', 0, '2', array( 'link' => 'site.ini.append.php', 'mode' => 0755 ) + $o );
    $target = './' . LONG_DIR . '/' . LONG_FILE;
    $records = paxRecord( 'linkpath', $target );
    $tar .= tarHeader( './ezextension/legacyfixture/PaxHeader/same.tpl', strlen( $records ), 'x', $o ) . tarData( $records );
    $tar .= tarHeader( './ezextension/legacyfixture/same.tpl', 0, '1', array( 'link' => substr( $target, 0, 100 ) ) + $o );
    writeFixture( $outDir, $id, $tar . tarEnd() );
};

$builders['gnu-oddities'] = function ( $id ) use ( $outDir )
{
    $o = array( 'magic' => 'gnu', 'uname' => 'root', 'gname' => 'root' );
    $files = fixtureFiles( 'legacy_gnu_oddities', '6.0', false );
    $tar = tarHeader( 'Legacy fixture volume', 0, 'V', $o );
    foreach ( $files as $path => $content )
        $tar .= tarHeader( $path, strlen( $content ), '0', $o ) . tarData( $content );
    // a long name of 511 bytes and its NUL: 512, a multiple of the block size
    $long = 'ezextension/legacyfixture/' . str_repeat( 'd', 200 ) . '/' . str_repeat( 'e', 200 ) . '/';
    $long .= str_repeat( 'n', 511 - strlen( $long ) - 4 ) . '.txt';
    $tar .= tarHeader( '././@LongLink', 512, 'L', $o ) . tarData( $long . "\0" );
    $tar .= tarHeader( substr( $long, 0, 100 ), 12, '0', $o ) . tarData( 'block length' );
    // a symbolic link whose target needs a GNU long link name
    $target = str_repeat( 'k', 110 ) . '.txt';
    $targetPath = 'ezextension/legacyfixture/' . $target;
    $tar .= tarHeader( '././@LongLink', strlen( $targetPath ) + 1, 'L', $o ) . tarData( $targetPath . "\0" );
    $tar .= tarHeader( substr( $targetPath, 0, 100 ), 4, '0', $o ) . tarData( 'kkkk' );
    $tar .= tarHeader( '././@LongLink', strlen( $target ) + 1, 'K', $o ) . tarData( $target . "\0" );
    $tar .= tarHeader( 'ezextension/legacyfixture/to-long-target.txt', 0, '2', array( 'link' => substr( $target, 0, 100 ) ) + $o );
    // a dump directory of an incremental backup: a directory with the list of its names as data
    $listing = "Ydumped.txt\0\0";
    $tar .= tarHeader( 'ezextension/legacyfixture/dumped/', strlen( $listing ), 'D', $o ) . tarData( $listing );
    // an old GNU sparse file with one extension block
    $extra = array( 482 => "\1" );
    $tar .= tarHeader( 'ezextension/legacyfixture/sparse.bin', 5, 'S', array( 'extra' => $extra ) + $o );
    $tar .= str_repeat( "\0", 512 ) . tarData( 'spars' );
    writeFixture( $outDir, $id, $tar . tarEnd() );
};

$builders['ustar-oddities'] = function ( $id ) use ( $outDir )
{
    $o = array( 'magic' => 'posix', 'uname' => 'root', 'gname' => 'root' );
    $global = paxRecord( 'comment', 'written by a fixture builder' ) . paxRecord( 'mtime', (string)FIXTURE_MTIME );
    $tar = tarHeader( 'pax_global_header', strlen( $global ), 'g', $o ) . tarData( $global );
    $files = fixtureFiles( 'legacy_ustar_oddities', '6.0', false );
    $types = array( 'package.xml' => '0', 'documents/LICENCE' => '7', 'ezextension/legacyfixture/settings/site.ini.append.php' => "\0" );
    foreach ( $files as $path => $content )
        $tar .= tarHeader( $path, strlen( $content ), isset( $types[$path] ) ? $types[$path] : '0', $o ) . tarData( $content );
    $tar .= tarHeader( 'ezextension/legacyfixture/tty', 0, '3', array( 'extra' => array( 329 => '0000004', 337 => '0000001' ) ) + $o );
    $tar .= tarHeader( 'ezextension/legacyfixture/disk', 0, '4', array( 'extra' => array( 329 => '0000010', 337 => '0000000' ) ) + $o );
    $tar .= tarHeader( 'ezextension/legacyfixture/pipe', 0, '6', $o );
    $tar .= tarHeader( 'ezextension/legacyfixture/unknown.bin', 3, 'Q', $o ) . tarData( 'abc' );
    writeFixture( $outDir, $id, $tar . tarEnd() );
};

$failed = 0;
foreach ( $builders as $id => $builder )
{
    if ( $only !== null && !in_array( $id, $only, true ) )
        continue;
    try
    {
        $builder( $id );
    }
    catch ( Throwable $e )
    {
        $failed++;
        echo "FAIL $id: " . $e->getMessage() . "\n";
    }
}
eZDir::recursiveDelete( $work );
echo $failed ? "FAIL: $failed fixture(s) not built\n" : "PASS: fixtures written to $outDir\n";
exit( $failed ? 1 : 0 );
