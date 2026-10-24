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
 * ezcArchive (zetacomponents/archive 1.5.1) is not safe to point at an archive nobody has looked at:
 * - its GNU header reader (tar/headers/gnu.php) loops for ever on every header whose type is not a digit, except the
 *   GNU long name "L" and long link "K": only those two move the file pointer on. Measured with PHP 8.5.4: a PAX
 *   archive (type "x"/"g", what Python's tarfile writes by default since 3.8 and macOS bsdtar for names that are not
 *   ASCII), a GNU "D"/"M"/"N"/"S"/"V" header, or a regular file typed "\0" in an archive with the "ustar" magic kept
 *   package/upload busy until max_execution_time, instead of being refused. eZPackage::archiveTarType() reads every
 *   archive with that magic as GNU (issue #15891);
 * - it reads the name of an "L"/"K" header wholly into memory, as long as the header says (up to 8 GiB), before
 *   eZPackageUploadInspector::entriesProblem() sees a single entry;
 * - it applies the modes the archive gives (archive.php: chmod( octdec( permissions ) )): a set-uid file (04755), a
 *   world writable (0777) or an unreadable (0000) directory came out of the archive as they were packed.
 *
 * scan() reads the archive header by header, 512 bytes at a time, and skips the data blocks by their size; it reads
 * at most one header block and one long name of at most MAX_LONG_NAME bytes into memory, and stops at
 * MaxEntries entries and MaxUnpackedSize bytes (package.ini [UploadSettings], eZPackageUploadInspector::limits()).
 * It allows what the reader ezcArchive uses for the archive gets through:
 * - read as GNU (the first header has the "ustar" magic): regular files "0", directories "5", a long name "L" right
 *   before the entry it names;
 * - read as V7 (no magic, the format of the 3.x packages): regular files "0" or "\0", directories "5";
 * - in either, hard and symbolic links, devices and fifos ("1", "2", "3", "4", "6"): they do not trip the reader, and
 *   entriesProblem() refuses them afterwards with its own code (unsafe_type), once the package is named.
 * Everything else is refused, PAX archives with a message of their own: they are not read, a package has to be packed
 * in the GNU format (GNU tar: tar --format=gnu; bsdtar, the tar of macOS and the BSDs: tar --format=gnutar).
 *
 * open() opens the archive only when scan() finds nothing, and with an extract callback (expPackageArchiveModes)
 * that gives every file it extracts 0644 and every directory 0755, within the installation's limits.
 */
class expPackageArchive
{
    const BLOCK_SIZE = 512;
    /** the longest name an "L" header may carry (eZPackageUploadInspector::safeEntryPath() allows no longer path) */
    const MAX_LONG_NAME = 4096;

    /**
     * The first reason the tar headers of the archive at $archivePath must not be given to ezcArchive, or null when
     * they may.
     *
     * @param string $archivePath a gzip compressed (or plain) tar archive
     * @param array $limits as eZPackageUploadInspector::limits() (max_entries, max_unpacked_size)
     * @return array|null array( 'code' => ..., 'path' => ..., 'type' => ... ) or null
     */
    static function scan( $archivePath, array $limits = array() )
    {
        $limits += eZPackageUploadInspector::limits();
        $handle = @fopen( "compress.zlib://$archivePath", 'rb' );
        if ( !$handle )
            return self::problem( 'unreadable' );
        try
        {
            return self::scanStream( $handle, $limits );
        }
        finally
        {
            fclose( $handle );
        }
    }

    /**
     * The archive at $archivePath opened for reading, with expPackageArchiveModes as extract callback; null when
     * scan() refuses it, with the reason in $problem.
     *
     * @param string $archivePath
     * @param array|null $problem set to the result of scan()
     * @param array $limits as eZPackageUploadInspector::limits()
     * @return ezcArchive|null
     * @throws ezcArchiveException when ezcArchive cannot read the archive after all
     */
    static function open( $archivePath, &$problem = null, array $limits = array() )
    {
        $problem = self::scan( $archivePath, $limits );
        if ( $problem !== null )
            return null;
        $options = new ezcArchiveOptions( array( 'readOnly' => true, 'extractCallback' => new expPackageArchiveModes() ) );
        return ezcArchive::open( "compress.zlib://$archivePath", eZPackage::archiveTarType( $archivePath ), $options );
    }

    /**
     * A text for the log: what scan() refused.
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

    private static function problem( $code, $path = '', $type = '' )
    {
        return array( 'code' => $code, 'path' => (string)$path, 'type' => (string)$type );
    }

    private static function scanStream( $handle, array $limits )
    {
        $gnu = null;
        $entries = 0;
        $bytes = 0;
        $longName = null;
        while ( true )
        {
            $header = self::read( $handle, self::BLOCK_SIZE );
            if ( $header === '' )
            {
                // the end of the stream at a header boundary is the end of the archive (ezcArchive takes it so too);
                // an archive without a single header is no archive, and a long name has to name an entry
                if ( $gnu === null || $longName !== null )
                    return self::problem( 'unreadable' );
                return null;
            }
            if ( strlen( $header ) < self::BLOCK_SIZE )
                return self::problem( 'unreadable' );
            if ( trim( $header, "\0" ) === '' )
            {
                if ( $gnu === null || $longName !== null )
                    return self::problem( 'unreadable' );
                return null;
            }
            if ( !self::checksumMatches( $header ) )
                return self::problem( 'unreadable' );
            // eZPackage::archiveTarType(): the first header decides the reader
            if ( $gnu === null )
                $gnu = substr( $header, 257, 5 ) === 'ustar';

            $type = $header[156];
            $name = $longName !== null ? $longName : self::headerName( $header );
            $size = self::size( substr( $header, 124, 12 ) );
            if ( $size === null )
                return self::problem( 'unreadable', $name );

            if ( $type === 'x' || $type === 'g' )
                return self::problem( 'tar_pax', $name, $type );
            if ( $type === 'L' && $gnu )
            {
                if ( $longName !== null )
                    return self::problem( 'unreadable', $longName, $type );
                if ( $size > self::MAX_LONG_NAME )
                    return self::problem( 'tar_long_name', '', $type );
                // ezcArchiveGnuHeader::readExtension() reads one block too many when the size is a
                // multiple of 512 (also 0): it skips the real entry header and parses the entry's
                // data as the next header, so a forged PAX header there loops forever again. GNU tar
                // writes the name plus a NUL, so a name of 511, 1023, ... or 4095 bytes cannot be imported.
                if ( $size % self::BLOCK_SIZE === 0 )
                    return self::problem( 'tar_long_name', '', $type );
                $data = self::read( $handle, self::padded( $size ) );
                if ( strlen( $data ) < self::padded( $size ) )
                    return self::problem( 'unreadable' );
                $longName = rtrim( substr( $data, 0, $size ), "\0" );
                continue;
            }
            $allowed = array( '0', '5', '1', '2', '3', '4', '6' );
            if ( !$gnu )
                $allowed[] = "\0";
            if ( !in_array( $type, $allowed, true ) )
                return self::problem( 'tar_entry_type', $name, $type );

            $longName = null;
            $entries++;
            if ( $entries > $limits['max_entries'] )
                return self::problem( 'too_many_entries' );
            // only regular files carry data in a tar archive; the reader skips what the header announces either way
            $bytes += $size;
            if ( $bytes > $limits['max_unpacked_size'] )
                return self::problem( 'too_large_unpacked' );
            if ( !self::skip( $handle, self::padded( $size ) ) )
                return self::problem( 'unreadable', $name );
        }
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

    /**
     * The entry name of a header: the ustar prefix and name, for the messages only.
     */
    private static function headerName( $header )
    {
        $name = rtrim( substr( $header, 0, 100 ), "\0" );
        if ( substr( $header, 257, 5 ) === 'ustar' )
        {
            $prefix = rtrim( substr( $header, 345, 155 ), "\0" );
            if ( $prefix !== '' )
                $name = $prefix . '/' . $name;
        }
        return $name;
    }
}

?>
