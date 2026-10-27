<?php
/**
 * File containing the expPackageArchive class.
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Opens a package archive (.ezpkg: a gzip compressed tar archive) for reading, after a bounded scan of its tar
 * headers.
 *
 * Every package the earlier releases wrote has to import: eZ Publish 3.x wrote V7 archives (no magic, regular files
 * typed "\0"), 4.x and 5.x wrote ustar/GNU archives through ezcArchive, and packages repacked by hand come in what
 * the tar at hand writes by default: GNU tar (gnu, with "./" in front of every name, hard links between identical
 * files, symbolic links), GNU tar --format=posix and bsdtar, the tar of macOS and the BSDs (ustar with PAX extended
 * headers for long or non-ASCII names and extended attributes).
 *
 * ezcArchive (zetacomponents/archive 1.5.1) cannot be pointed at such archives itself:
 * - its GNU header reader (tar/headers/gnu.php) loops for ever on every header whose type is not a digit, except the
 *   GNU long name "L" and long link "K": a PAX header ("x"/"g"), a GNU "D"/"M"/"N"/"S"/"V" header or a regular file
 *   typed "\0" in an archive with the "ustar" magic kept package/upload busy until max_execution_time (measured with
 *   PHP 8.5.4);
 * - its V7 reader takes a file typed "\0" for no file under PHP 8, and a directory only by a trailing slash;
 * - its ustar reader throws on the contiguous file type "7";
 * - it reads the name of an "L"/"K" header wholly into memory, as long as the header says (up to 8 GiB), and reads one
 *   block too many when that size is a multiple of 512;
 * - it applies the modes the archive gives: a set-uid file (04755), a world writable (0777) or an unreadable (0000)
 *   directory came out of the archive as they were packed.
 *
 * index() reads the archive header by header, 512 bytes at a time, and skips the data blocks by their size; it keeps
 * at most one header block, one long name of at most MAX_LONG_NAME bytes and one PAX header of at most MAX_PAX_SIZE
 * bytes in memory, and stops at MaxEntries entries and MaxUnpackedSize bytes (package.ini [UploadSettings],
 * eZPackageUploadInspector::limits()). It reads:
 * - regular files "0", "\0" and "7", directories "5" (and "D", and a "0"/"\0" entry whose name ends in "/");
 * - the long names of GNU tar ("L", "K") and of PAX extended headers ("x": path, linkpath, size, mtime), the ustar
 *   prefix field; a PAX global header ("g") and a GNU volume label ("V") name no entry and are passed over;
 * - hard links ("1") and symbolic links ("2") to a file of the package: extracted as a copy of that file;
 * - names normalised: "./" in front, "//" and "/./" inside are dropped, the entry "./" itself is no entry.
 * What a package cannot hold is passed over with a notice, and the rest of the package is imported: a symbolic link
 * that points outside the package (an absolute target, or one with ".." that leaves it), to a directory or to nothing
 * the package holds; a hard link to nothing it holds; devices and fifos ("3", "4", "6"); sparse files and the other
 * types no package tool writes. An entry whose own name leaves the package (an absolute path, "..") still refuses the
 * whole package (eZPackageUploadInspector::safeEntryPath()), as do a damaged header and the limits.
 *
 * An archive that ezcArchive reads as it is (the "ustar" magic in every header, only files "0", directories "5" and
 * long names "L", every name already normalised: what the exporter and ezcArchive write) is opened as it is.
 * Any other is first rewritten into a plain tar archive in that form under the cache directory (rewrite()), with the
 * links as copies and without the entries passed over, and that copy is opened; it is removed again as soon as it is
 * open (its file handle keeps it readable) or, where the system does not allow that, at the end of the request.
 *
 * open() opens the archive only when index() finds nothing to refuse, and with an extract callback
 * (expPackageArchiveModes) that gives every file it extracts 0644 and every directory 0755, within the installation's
 * limits.
 */
class expPackageArchive
{
    const BLOCK_SIZE = 512;
    /** the longest entry name or link target (eZPackageUploadInspector::safeEntryPath() allows no longer path) */
    const MAX_LONG_NAME = 4096;
    /** the longest file or directory name in an entry's path (what the file systems store, NAME_MAX) */
    const MAX_NAME_SEGMENT = 255;
    /** the largest PAX extended header that is read */
    const MAX_PAX_SIZE = 65536;
    /** symbolic links that point to symbolic links: at most this many are followed */
    const MAX_LINK_HOPS = 32;
    /** extension blocks of an old GNU sparse header that are passed over at most */
    const MAX_SPARSE_BLOCKS = 4096;

    /**
     * The first reason the archive at $archivePath must not be imported, or null when it may.
     *
     * @param string $archivePath a gzip compressed (or plain) tar archive
     * @param array $limits as eZPackageUploadInspector::limits() (max_entries, max_unpacked_size)
     * @return array|null array( 'code' => ..., 'path' => ..., 'type' => ... ) or null
     */
    static function scan( $archivePath, array $limits = array() )
    {
        return self::index( $archivePath, $limits )['problem'];
    }

    /**
     * What the archive at $archivePath holds, read from its tar headers only.
     *
     * @param string $archivePath a gzip compressed (or plain) tar archive
     * @param array $limits as eZPackageUploadInspector::limits()
     * @return array( 'problem' => array|null (as scan()), 'notices' => array[] (see noticeText()), 'rewrite' => bool
     *                (whether ezcArchive needs the archive rewritten), 'entries' => int, 'bytes' => int )
     */
    static function index( $archivePath, array $limits = array() )
    {
        $limits += eZPackageUploadInspector::limits();
        $handle = @fopen( "compress.zlib://$archivePath", 'rb' );
        if ( !$handle )
            return array( 'problem' => self::problem( 'unreadable' ), 'notices' => array(), 'rewrite' => false, 'entries' => 0, 'bytes' => 0 );
        try
        {
            $walk = self::walk( $handle, $limits, null );
        }
        finally
        {
            fclose( $handle );
        }
        $result = array( 'problem' => $walk['problem'], 'notices' => array(), 'rewrite' => $walk['rewrite'],
                         'entries' => $walk['count'], 'bytes' => $walk['bytes'] );
        if ( $walk['problem'] !== null )
            return $result;
        $links = self::resolveLinks( $walk['entries'] );
        $result['notices'] = array_merge( $walk['notices'], $links['notices'] );
        foreach ( $links['copies'] as $copy )
            $result['bytes'] += $copy['size'];
        if ( $result['bytes'] > $limits['max_unpacked_size'] )
            $result['problem'] = self::problem( 'too_large_unpacked' );
        return $result;
    }

    /**
     * The archive at $archivePath opened for reading, with expPackageArchiveModes as extract callback; null when
     * index() refuses it, with the reason in $problem.
     *
     * @param string $archivePath
     * @param array|null $problem set to the reason it is refused (as scan()), or null
     * @param array $limits as eZPackageUploadInspector::limits()
     * @param array|null $notices set to what was passed over (see noticeText())
     * @return ezcArchive|null
     * @throws ezcArchiveException when ezcArchive cannot read the archive after all
     */
    static function open( $archivePath, &$problem = null, array $limits = array(), &$notices = null )
    {
        $limits += eZPackageUploadInspector::limits();
        $index = self::index( $archivePath, $limits );
        $problem = $index['problem'];
        $notices = $index['notices'];
        if ( $problem !== null )
            return null;
        $options = new ezcArchiveOptions( array( 'readOnly' => true, 'extractCallback' => new expPackageArchiveModes() ) );
        if ( !$index['rewrite'] )
            return ezcArchive::open( "compress.zlib://$archivePath", eZPackage::archiveTarType( $archivePath ), $options );

        $copy = self::rewrite( $archivePath, $limits, $problem );
        if ( $copy === null )
            return null;
        try
        {
            $archive = ezcArchive::open( $copy, ezcArchive::TAR_GNU, $options );
        }
        catch ( Exception $e )
        {
            @unlink( $copy );
            throw $e;
        }
        // the open handle keeps the copy readable on POSIX systems; elsewhere it goes at the end of the request
        if ( !@unlink( $copy ) )
            register_shutdown_function( function () use ( $copy ) { @unlink( $copy ); } );
        return $archive;
    }

    /**
     * The archive at $archivePath rewritten into a plain tar archive that ezcArchive reads as it is (GNU headers,
     * files "0", directories "5", long names "L"), links as copies of their targets, without the entries index()
     * passes over. The path of the copy (under the cache directory), or null with the reason in $problem.
     *
     * @param string $archivePath
     * @param array $limits as eZPackageUploadInspector::limits()
     * @param array|null $problem
     * @return string|null
     */
    static function rewrite( $archivePath, array $limits = array(), &$problem = null )
    {
        $limits += eZPackageUploadInspector::limits();
        $dir = eZDir::path( array( eZSys::cacheDirectory(), 'packages' ) );
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        $copy = $dir . '/normalized-' . bin2hex( random_bytes( 8 ) ) . '.tar';
        $in = @fopen( "compress.zlib://$archivePath", 'rb' );
        $out = @fopen( $copy, 'x+b' );
        if ( !$in || !$out )
        {
            if ( $in )
                fclose( $in );
            if ( $out )
                fclose( $out );
            @unlink( $copy );
            $problem = self::problem( 'unreadable' );
            return null;
        }
        $ok = false;
        try
        {
            $walk = self::walk( $in, $limits, $out );
            $problem = $walk['problem'];
            if ( $problem === null )
            {
                $links = self::resolveLinks( $walk['entries'] );
                foreach ( $links['copies'] as $link )
                {
                    if ( !self::appendCopy( $out, $link ) )
                    {
                        $problem = self::problem( 'unreadable', $link['path'] );
                        break;
                    }
                }
                if ( $problem === null )
                    $ok = fwrite( $out, str_repeat( "\0", 2 * self::BLOCK_SIZE ) ) === 2 * self::BLOCK_SIZE;
                if ( $problem === null && !$ok )
                    $problem = self::problem( 'unreadable' );
            }
        }
        finally
        {
            fclose( $in );
            fclose( $out );
            if ( !$ok )
                @unlink( $copy );
        }
        return $ok ? $copy : null;
    }

    /**
     * A text for the log: what index() refused.
     *
     * @param array $problem
     * @return string
     */
    static function problemText( array $problem )
    {
        $text = $problem['code'];
        if ( $problem['type'] !== '' )
            $text .= ' ' . self::typeText( $problem['type'] );
        if ( $problem['path'] !== '' )
            $text .= ': ' . $problem['path'];
        return $text;
    }

    /**
     * A text for the log: an entry index() passed over.
     *
     * Codes: link_outside (a link whose target leaves the package), link_missing (its target is not in the package,
     * or the links go round in a circle), link_directory (a symbolic link to a directory), special_file (a device or
     * a fifo), unsupported_entry (a sparse file or another type no package tool writes).
     *
     * @param array $notice array( 'code' => ..., 'path' => ..., 'target' => ..., 'type' => ... )
     * @return string
     */
    static function noticeText( array $notice )
    {
        $text = $notice['code'] . ' ' . self::typeText( $notice['type'] ) . ': ' . $notice['path'];
        if ( $notice['target'] !== '' )
            $text .= ' -> ' . $notice['target'];
        return $text . ' (not imported)';
    }

    /**
     * The message for a notice, for the pages that show what an import passed over.
     *
     * @param array $notice
     * @return string
     */
    static function noticeMessage( array $notice )
    {
        $params = array( '%path' => $notice['path'], '%target' => $notice['target'], '%type' => self::typeText( $notice['type'] ) );
        switch ( $notice['code'] )
        {
            case 'link_outside': return ezpI18n::tr( 'kernel/package', 'The link "%path" points outside the package (%target) and was not imported.', false, $params );
            case 'link_missing': return ezpI18n::tr( 'kernel/package', 'The link "%path" points to "%target", which the package does not hold, and was not imported.', false, $params );
            case 'link_directory': return ezpI18n::tr( 'kernel/package', 'The link "%path" points to the directory "%target" and was not imported.', false, $params );
            case 'special_file': return ezpI18n::tr( 'kernel/package', 'The entry "%path" is a device or a named pipe (tar type "%type") and was not imported.', false, $params );
        }
        return ezpI18n::tr( 'kernel/package', 'The entry "%path" has the tar type "%type", which a package does not use, and was not imported.', false, $params );
    }

    /**
     * A tar type flag for a message: the character itself, or its code ("\x00") when it is not printable.
     *
     * @param string $type
     * @return string
     */
    static function typeText( $type )
    {
        $type = (string)$type;
        return ( $type !== '' && ctype_graph( $type ) ) ? $type : sprintf( '\\x%02X', $type === '' ? 0 : ord( $type ) );
    }

    /**
     * Whether $block is a tar header (512 bytes with a checksum that matches): what a plain, uncompressed package
     * archive starts with.
     *
     * @param string $block
     * @return bool
     */
    static function isTarHeader( $block )
    {
        return is_string( $block ) && strlen( $block ) === self::BLOCK_SIZE && trim( $block, "\0" ) !== '' && self::checksumMatches( $block );
    }

    /**
     * An entry name as it is extracted: "./" in front, "//" and "/./" inside and a trailing "/" dropped; '' for the
     * directory itself ("." or "./"). A name with ".." or an absolute one is returned as it is, for safeEntryPath()
     * to refuse.
     *
     * @param string $name
     * @return string
     */
    static function normalizeName( $name )
    {
        $name = (string)$name;
        if ( $name === '' || $name[0] === '/' )
            return $name;
        $segments = array();
        foreach ( explode( '/', $name ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            $segments[] = $segment;
        }
        return implode( '/', $segments );
    }

    private static function problem( $code, $path = '', $type = '' )
    {
        return array( 'code' => $code, 'path' => (string)$path, 'type' => (string)$type );
    }

    private static function notice( $code, $path, $type, $target = '' )
    {
        return array( 'code' => $code, 'path' => (string)$path, 'type' => (string)$type, 'target' => (string)$target );
    }

    /**
     * Reads the archive from $handle header by header. With $out, every file and directory that is kept is written
     * to it in the form ezcArchive reads (see rewrite()).
     *
     * @return array( 'problem' => array|null, 'notices' => array[], 'rewrite' => bool, 'count' => int, 'bytes' => int,
     *                'entries' => array[] (path, kind file|dir|hardlink|symlink, target, size, mtime, offset, type) )
     */
    private static function walk( $handle, array $limits, $out )
    {
        $state = array( 'problem' => null, 'notices' => array(), 'rewrite' => false, 'count' => 0, 'bytes' => 0, 'entries' => array() );
        $headers = 0;
        $longName = null;
        $longLink = null;
        $pax = null;
        $fail = function ( $problem ) use ( &$state )
        {
            $state['problem'] = $problem;
            return $state;
        };
        while ( true )
        {
            $header = self::read( $handle, self::BLOCK_SIZE );
            if ( $header === '' || trim( $header, "\0" ) === '' )
            {
                // the end of the stream at a header boundary is the end of the archive (ezcArchive takes it so too);
                // an archive without a single header is no archive, and a long name or PAX header has to name an entry
                if ( $headers === 0 || $longName !== null || $longLink !== null || $pax !== null )
                    return $fail( self::problem( 'unreadable' ) );
                return $state;
            }
            if ( strlen( $header ) < self::BLOCK_SIZE || !self::checksumMatches( $header ) )
                return $fail( self::problem( 'unreadable' ) );
            $headers++;

            $magic = substr( $header, 257, 8 );
            $posix = substr( $magic, 0, 6 ) === "ustar\0";
            $gnu = $magic === "ustar  \0";
            if ( !$posix && !$gnu )
                $state['rewrite'] = true;
            // the GNU format keeps access times and the like where ustar keeps the prefix, and ezcArchive reads that as a prefix
            if ( $gnu && trim( substr( $header, 345, 155 ), "\0" ) !== '' )
                $state['rewrite'] = true;

            $type = $header[156];
            $rawName = self::field( $header, 0, 100 );
            if ( $posix )
            {
                $prefix = self::field( $header, 345, 155 );
                if ( $prefix !== '' )
                    $rawName = $prefix . '/' . $rawName;
            }
            $size = self::size( substr( $header, 124, 12 ) );
            if ( $pax !== null && isset( $pax['size'] ) )
                $size = $pax['size'];
            if ( $size === null )
                return $fail( self::problem( 'unreadable', $rawName ) );

            // headers that name no entry of their own
            if ( $type === 'L' || $type === 'K' )
            {
                if ( ( $type === 'L' ? $longName : $longLink ) !== null )
                    return $fail( self::problem( 'unreadable', '', $type ) );
                if ( $size > self::MAX_LONG_NAME )
                    return $fail( self::problem( 'tar_long_name', '', $type ) );
                // ezcArchiveGnuHeader::readExtension() reads one block too many when the size is a multiple of 512
                if ( $type === 'K' || !$gnu && !$posix || $size % self::BLOCK_SIZE === 0 )
                    $state['rewrite'] = true;
                $data = self::read( $handle, self::padded( $size ) );
                if ( strlen( $data ) < self::padded( $size ) )
                    return $fail( self::problem( 'unreadable' ) );
                $value = self::cString( substr( $data, 0, $size ) );
                // an empty long name names nothing: the entry's own header does
                if ( $value === '' )
                    continue;
                if ( $type === 'L' )
                    $longName = $value;
                else
                    $longLink = $value;
                continue;
            }
            if ( $type === 'x' || $type === 'g' )
            {
                $state['rewrite'] = true;
                if ( $size > self::MAX_PAX_SIZE )
                    return $fail( self::problem( 'tar_long_name', $rawName, $type ) );
                $data = self::read( $handle, self::padded( $size ) );
                if ( strlen( $data ) < self::padded( $size ) )
                    return $fail( self::problem( 'unreadable', $rawName ) );
                $records = self::paxRecords( substr( $data, 0, $size ) );
                if ( $records === null )
                    return $fail( self::problem( 'unreadable', $rawName, $type ) );
                // a global header's values would apply to every entry after it; none of those it may set (a path,
                // a size for every entry) makes sense in a package, so it is passed over
                if ( $type === 'g' )
                    continue;
                if ( $pax !== null )
                    return $fail( self::problem( 'unreadable', $rawName, $type ) );
                $pax = array();
                foreach ( array( 'path', 'linkpath' ) as $key )
                {
                    if ( isset( $records[$key] ) )
                    {
                        if ( strlen( $records[$key] ) > self::MAX_LONG_NAME )
                            return $fail( self::problem( 'tar_long_name', '', $type ) );
                        $pax[$key] = $records[$key];
                    }
                }
                if ( isset( $records['size'] ) )
                {
                    if ( !preg_match( '/^[0-9]{1,18}$/', $records['size'] ) )
                        return $fail( self::problem( 'unreadable', $rawName, $type ) );
                    $pax['size'] = (int)$records['size'];
                }
                if ( isset( $records['mtime'] ) && preg_match( '/^[0-9]{1,11}(\.[0-9]*)?$/', $records['mtime'] ) )
                    $pax['mtime'] = (int)$records['mtime'];
                foreach ( $records as $key => $value )
                {
                    if ( strpos( $key, 'GNU.sparse.' ) === 0 )
                        $pax['sparse'] = true;
                }
                continue;
            }
            if ( $type === 'V' )
            {
                // a GNU volume label names the archive, not an entry
                $state['rewrite'] = true;
                if ( !self::skip( $handle, self::padded( $size ) ) )
                    return $fail( self::problem( 'unreadable', $rawName ) );
                continue;
            }

            $name = $pax !== null && isset( $pax['path'] ) ? $pax['path'] : ( $longName !== null ? $longName : $rawName );
            $link = $pax !== null && isset( $pax['linkpath'] ) ? $pax['linkpath'] : ( $longLink !== null ? $longLink : self::field( $header, 157, 100 ) );
            $mtime = $pax !== null && isset( $pax['mtime'] ) ? $pax['mtime'] : (int)self::size( substr( $header, 136, 12 ) );
            $sparse = $pax !== null && !empty( $pax['sparse'] );
            $longName = $longLink = $pax = null;

            $state['count']++;
            if ( $state['count'] > $limits['max_entries'] )
                return $fail( self::problem( 'too_many_entries' ) );
            // whatever data the header announces is read past; it counts towards what the archive unpacks to
            $state['bytes'] += $size;
            if ( $state['bytes'] > $limits['max_unpacked_size'] )
                return $fail( self::problem( 'too_large_unpacked' ) );

            if ( $type === 'S' && $header[482] === "\1" )
            {
                // an old GNU sparse file: extension headers follow while each says another one does
                for ( $i = 0; ; $i++ )
                {
                    $extension = self::read( $handle, self::BLOCK_SIZE );
                    if ( strlen( $extension ) < self::BLOCK_SIZE || $i >= self::MAX_SPARSE_BLOCKS )
                        return $fail( self::problem( 'unreadable', $name, $type ) );
                    if ( $extension[504] !== "\1" )
                        break;
                }
            }

            $path = self::normalizeName( $name );
            if ( $path !== rtrim( $name, '/' ) )
                $state['rewrite'] = true;
            $directory = $type === '5' || $type === 'D' || ( ( $type === '0' || $type === "\0" || $type === '7' ) && substr( $name, -1 ) === '/' );
            $kind = null;
            if ( $sparse || $type === 'S' )
                $kind = 'unsupported';
            else if ( $directory )
                $kind = 'dir';
            else if ( $type === '0' || $type === "\0" || $type === '7' )
                $kind = 'file';
            else if ( $type === '1' )
                $kind = 'hardlink';
            else if ( $type === '2' )
                $kind = 'symlink';
            else if ( $type === '3' || $type === '4' || $type === '6' )
                $kind = 'special';
            else
                $kind = 'unsupported';
            if ( $type !== '0' && $type !== '5' )
                $state['rewrite'] = true;
            if ( $directory && $type !== '5' )
                $state['rewrite'] = true;

            if ( $path === '' )
            {
                // "./": the directory the package is extracted to
                $state['rewrite'] = true;
                if ( !self::skip( $handle, self::padded( $size ) ) )
                    return $fail( self::problem( 'unreadable', $name ) );
                continue;
            }
            if ( !eZPackageUploadInspector::safeEntryPath( $path ) )
                return $fail( self::problem( 'unsafe_path', $name, $type ) );
            // ezcArchive ends the request with a fatal error when it cannot create a file (fwrite() of false)
            foreach ( explode( '/', $path ) as $segment )
            {
                if ( strlen( $segment ) > self::MAX_NAME_SEGMENT )
                    return $fail( self::problem( 'tar_name_segment', $path, $type ) );
            }

            $entry = array( 'path' => $path, 'kind' => $kind, 'target' => $link, 'size' => $kind === 'file' ? $size : 0,
                            'mtime' => $mtime, 'offset' => null, 'type' => $type );
            if ( $kind === 'special' )
                $state['notices'][] = self::notice( 'special_file', $path, $type );
            else if ( $kind === 'unsupported' )
                $state['notices'][] = self::notice( 'unsupported_entry', $path, $type );

            if ( $out !== null && ( $kind === 'file' || $kind === 'dir' ) )
            {
                if ( !self::writeHeader( $out, $path, $kind, $kind === 'file' ? $size : 0, $mtime ) )
                    return $fail( self::problem( 'unreadable', $path ) );
                if ( $kind === 'file' )
                {
                    $entry['offset'] = ftell( $out );
                    if ( !self::copyStream( $handle, $out, $size ) )
                        return $fail( self::problem( 'unreadable', $path ) );
                    if ( !self::skip( $handle, self::padded( $size ) - $size ) || !self::pad( $out, $size ) )
                        return $fail( self::problem( 'unreadable', $path ) );
                }
                else if ( !self::skip( $handle, self::padded( $size ) ) )
                    return $fail( self::problem( 'unreadable', $path ) );
            }
            else if ( !self::skip( $handle, self::padded( $size ) ) )
                return $fail( self::problem( 'unreadable', $name ) );
            $state['entries'][] = $entry;
        }
    }

    /**
     * The links of the archive that are extracted as copies of a file it holds, and notices for those that are not.
     *
     * @param array[] $entries of walk()
     * @return array( 'copies' => array[] (path, size, mtime, offset of the target's data), 'notices' => array[] )
     */
    private static function resolveLinks( array $entries )
    {
        // what each path is at the end of the archive (a later entry of the same name replaces an earlier one)
        $byPath = array();
        foreach ( $entries as $entry )
            $byPath[$entry['path']] = $entry;
        $copies = array();
        $notices = array();
        foreach ( $entries as $entry )
        {
            if ( $entry['kind'] !== 'hardlink' && $entry['kind'] !== 'symlink' )
                continue;
            $current = $entry;
            $resolved = null;
            $code = null;
            $target = $entry['target'];
            for ( $hop = 0; $hop <= self::MAX_LINK_HOPS; $hop++ )
            {
                $path = self::linkTarget( $current );
                if ( $path === null )
                {
                    $code = 'link_outside';
                    break;
                }
                if ( !isset( $byPath[$path] ) )
                {
                    $code = 'link_missing';
                    break;
                }
                $next = $byPath[$path];
                if ( $next['kind'] === 'file' )
                {
                    $resolved = $next;
                    break;
                }
                if ( $next['kind'] === 'dir' )
                {
                    $code = 'link_directory';
                    break;
                }
                if ( $next['kind'] !== 'hardlink' && $next['kind'] !== 'symlink' )
                {
                    $code = 'link_missing';
                    break;
                }
                $current = $next;
            }
            if ( $resolved === null )
            {
                $notices[] = self::notice( $code !== null ? $code : 'link_missing', $entry['path'], $entry['type'], $target );
                continue;
            }
            $copies[] = array( 'path' => $entry['path'], 'size' => $resolved['size'], 'mtime' => $resolved['mtime'], 'offset' => $resolved['offset'] );
        }
        return array( 'copies' => $copies, 'notices' => $notices );
    }

    /**
     * The archive path a link entry points to, or null when that is outside the package. A hard link names an
     * archive path, a symbolic link a path relative to the directory it is in.
     */
    private static function linkTarget( array $entry )
    {
        $target = (string)$entry['target'];
        if ( $target === '' || $target[0] === '/' || preg_match( '/^[A-Za-z]:|[\x00-\x1F\x7F\\\\]/', $target ) )
            return null;
        $base = $entry['kind'] === 'symlink' ? explode( '/', $entry['path'] ) : array( '' );
        array_pop( $base );
        $segments = $base;
        foreach ( explode( '/', $target ) as $segment )
        {
            if ( $segment === '' || $segment === '.' )
                continue;
            if ( $segment === '..' )
            {
                if ( !$segments )
                    return null;
                array_pop( $segments );
                continue;
            }
            $segments[] = $segment;
        }
        return $segments ? implode( '/', $segments ) : null;
    }

    /**
     * Writes one entry header in the form ezcArchive's GNU reader reads: an "L" long name before it when the name
     * does not fit into the 100 bytes of the header (one NUL more when the name and its NUL fill whole blocks, as
     * readExtension() reads one block too many then).
     */
    private static function writeHeader( $out, $path, $kind, $size, $mtime )
    {
        $name = $kind === 'dir' ? $path . '/' : $path;
        $blocks = '';
        if ( strlen( $name ) > 99 )
        {
            $data = $name . "\0";
            if ( strlen( $data ) % self::BLOCK_SIZE === 0 )
                $data .= "\0";
            $blocks .= self::headerBlock( '././@LongLink', strlen( $data ), 'L', 0 ) . $data . str_repeat( "\0", self::padded( strlen( $data ) ) - strlen( $data ) );
        }
        $blocks .= self::headerBlock( substr( $name, 0, 99 ), $size, $kind === 'dir' ? '5' : '0', $mtime );
        return fwrite( $out, $blocks ) === strlen( $blocks );
    }

    private static function headerBlock( $name, $size, $type, $mtime )
    {
        $mtime = max( 0, min( (int)$mtime, 077777777777 ) );
        $header = str_pad( $name, 100, "\0" )
                . ( $type === '5' ? "0000755\0" : "0000644\0" )
                . "0000000\0" . "0000000\0"
                . sprintf( '%011o', $size ) . "\0"
                . sprintf( '%011o', $mtime ) . "\0"
                . '        '
                . $type
                . str_repeat( "\0", 100 )
                . "ustar  \0"
                . str_repeat( "\0", 32 ) . str_repeat( "\0", 32 )
                . str_repeat( "\0", 8 ) . str_repeat( "\0", 8 )
                . str_repeat( "\0", 167 );
        $sum = 0;
        for ( $i = 0; $i < self::BLOCK_SIZE; $i++ )
            $sum += ord( $header[$i] );
        return substr( $header, 0, 148 ) . sprintf( '%06o', $sum ) . "\0 " . substr( $header, 156 );
    }

    /**
     * Appends a link as a copy of the file it points to, read back from the rewritten archive itself.
     */
    private static function appendCopy( $out, array $link )
    {
        if ( fseek( $out, 0, SEEK_END ) !== 0 || !self::writeHeader( $out, $link['path'], 'file', $link['size'], $link['mtime'] ) )
            return false;
        $done = 0;
        while ( $done < $link['size'] )
        {
            $length = min( 1048576, $link['size'] - $done );
            if ( fseek( $out, $link['offset'] + $done ) !== 0 )
                return false;
            $chunk = self::read( $out, $length );
            if ( strlen( $chunk ) !== $length || fseek( $out, 0, SEEK_END ) !== 0 || fwrite( $out, $chunk ) !== $length )
                return false;
            $done += $length;
        }
        return self::pad( $out, $link['size'] );
    }

    private static function copyStream( $in, $out, $length )
    {
        while ( $length > 0 )
        {
            $chunk = @fread( $in, min( 1048576, $length ) );
            if ( !is_string( $chunk ) || $chunk === '' || fwrite( $out, $chunk ) !== strlen( $chunk ) )
                return false;
            $length -= strlen( $chunk );
        }
        return true;
    }

    private static function pad( $out, $size )
    {
        $padding = self::padded( $size ) - $size;
        return $padding === 0 || fwrite( $out, str_repeat( "\0", $padding ) ) === $padding;
    }

    /**
     * The records of a PAX extended header ("<length> <key>=<value>\n" each), or null when it is not well formed.
     */
    private static function paxRecords( $data )
    {
        $records = array();
        $offset = 0;
        $length = strlen( $data );
        while ( $offset < $length )
        {
            if ( $data[$offset] === "\0" )
                break;
            $space = strpos( $data, ' ', $offset );
            if ( $space === false || $space - $offset > 18 )
                return null;
            $digits = substr( $data, $offset, $space - $offset );
            if ( !preg_match( '/^[0-9]+$/', $digits ) )
                return null;
            $recordLength = (int)$digits;
            if ( $recordLength <= $space - $offset + 2 || $offset + $recordLength > $length || $data[$offset + $recordLength - 1] !== "\n" )
                return null;
            $record = substr( $data, $space + 1, $offset + $recordLength - $space - 2 );
            $equals = strpos( $record, '=' );
            if ( $equals === false || $equals === 0 )
                return null;
            $records[substr( $record, 0, $equals )] = substr( $record, $equals + 1 );
            $offset += $recordLength;
        }
        return $records;
    }

    /**
     * A NUL terminated text field of a header (what follows the first NUL is not part of it).
     */
    private static function field( $header, $offset, $length )
    {
        return self::cString( substr( $header, $offset, $length ) );
    }

    private static function cString( $data )
    {
        $nul = strpos( $data, "\0" );
        return $nul === false ? $data : substr( $data, 0, $nul );
    }

    /**
     * Up to $length bytes from the stream ('' at its end).
     */
    private static function read( $handle, $length )
    {
        $data = '';
        while ( strlen( $data ) < $length && !feof( $handle ) )
        {
            $chunk = @fread( $handle, min( 65536, $length - strlen( $data ) ) );
            if ( !is_string( $chunk ) || $chunk === '' )
                break;
            $data .= $chunk;
        }
        return $data;
    }

    /**
     * Reads past $length bytes of the stream without keeping them; false when the stream ends first.
     */
    private static function skip( $handle, $length )
    {
        while ( $length > 0 )
        {
            $chunk = @fread( $handle, min( 1048576, $length ) );
            if ( !is_string( $chunk ) || $chunk === '' )
                return false;
            $length -= strlen( $chunk );
        }
        return true;
    }

    private static function padded( $size )
    {
        return (int)( ceil( $size / self::BLOCK_SIZE ) * self::BLOCK_SIZE );
    }

    /**
     * The size field (12 bytes, octal); null for the base-256 form (GNU, from 8 GiB on) or anything not octal.
     * Old writers fill all 12 bytes with digits and no terminator; ezcArchive reads that form too.
     */
    private static function size( $field )
    {
        if ( $field !== '' && ( ord( $field[0] ) & 0x80 ) )
            return null;
        $digits = trim( $field, " \0" );
        if ( $digits === '' )
            return 0;
        if ( !preg_match( '/^[0-7]{1,12}$/', $digits ) )
            return null;
        return octdec( $digits );
    }

    /**
     * Whether the header's checksum is right (the unsigned sum, or the signed one some old writers made).
     */
    private static function checksumMatches( $header )
    {
        $digits = trim( substr( $header, 148, 8 ), " \0" );
        if ( !preg_match( '/^[0-7]{1,7}$/', $digits ) )
            return false;
        $stored = octdec( $digits );
        $block = substr( $header, 0, 148 ) . '        ' . substr( $header, 156 );
        $unsigned = 0;
        $signed = 0;
        for ( $i = 0; $i < self::BLOCK_SIZE; $i++ )
        {
            $byte = ord( $block[$i] );
            $unsigned += $byte;
            $signed += $byte > 127 ? $byte - 256 : $byte;
        }
        return $stored === $unsigned || $stored === $signed;
    }
}

?>
