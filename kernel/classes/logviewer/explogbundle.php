<?php
/**
 * "Download all logs" (Setup > Manage logs; doc/en/us/features/6.0/log-viewer.md, "Downloading every log"): one archive of
 * every log the viewer lists, optionally with their rotations, masked as the viewer masks unless the user who may see
 * them unmasked asks for that, with a MANIFEST.txt (path, size, last write, sha256 of what is stored).
 *
 * The archive is a .zip when ZipArchive is available, else a .tar.gz written here as a stream (a ustar writer over
 * gzopen), so memory stays flat: every file is read and written in pieces. It is built in its own directory under
 * var/tmp/logbundle and removed by cleanup() after it is sent. The sum of the files read is capped by
 * [ActionSettings] MaxBundleBytes before anything is written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogBundle
{
    /** The default of [ActionSettings] MaxBundleBytes: 256 MB */
    const DEFAULT_MAX_BYTES = 268435456;

    /** The bytes read or written at a time */
    const CHUNK = 1048576;

    /**
     * The files of a bundle: every log the locator lists (the current files), and with $archives their rotations.
     *
     * @param expLogFileLocator $locator
     * @param bool $archives
     * @return array list of resolved files (expLogFileLocator::resolve()), each with entry (its path in the archive)
     */
    public static function files( expLogFileLocator $locator, $archives = true )
    {
        $configured = $locator->configuredLogs();
        $out = array();
        foreach ( $locator->listFiles() as $list )
        {
            foreach ( $list as $info )
            {
                if ( !empty( $info['archive'] ) && !$archives )
                    continue;
                if ( isset( $info['readable'] ) && !$info['readable'] )
                    continue;
                $file = $locator->resolve( $info['id'] );
                if ( $file === null )
                    continue;
                $file['entry'] = self::entryName( $file, $configured );
                $out[] = $file;
            }
        }
        return $out;
    }

    /**
     * The path of a file in the archive: its path relative to the root ("var/log/error.log"), or for a log
     * configured by key (the web server's) "<group>/<file name>" ("webserver/access_ssl_log"). Pure.
     *
     * @param array $file
     * @param array $configured expLogFileLocator::configuredLogs()
     * @return string
     */
    public static function entryName( array $file, array $configured = array() )
    {
        $dir = (string)$file['dir'];
        if ( $dir !== '' && $dir[0] === '@' )
        {
            $key = substr( $dir, 1 );
            $group = isset( $configured[$key]['group'] ) ? $configured[$key]['group'] : expLogFileLocator::GROUP_WEBSERVER;
            return preg_replace( '/[^A-Za-z0-9._\-]+/', '-', $group ) . '/' . $file['name'];
        }
        return ( $dir !== '' ? $dir . '/' : '' ) . $file['name'];
    }

    /** @return string zip | tar.gz */
    public static function format()
    {
        return class_exists( 'ZipArchive' ) ? 'zip' : 'tar.gz';
    }

    /**
     * The host name in the archive's name: the request's, else site.ini [SiteSettings] SiteURL, else "site". Pure but
     * for the reads of $_SERVER and the settings.
     *
     * @return string
     */
    public static function host()
    {
        $host = isset( $_SERVER['SERVER_NAME'] ) ? (string)$_SERVER['SERVER_NAME'] : '';
        if ( $host === '' && class_exists( 'eZINI' ) )
        {
            $url = (string)eZINI::instance()->variable( 'SiteSettings', 'SiteURL' );
            $parsed = parse_url( strpos( $url, '://' ) === false ? 'http://' . $url : $url, PHP_URL_HOST );
            $host = is_string( $parsed ) ? $parsed : '';
        }
        $host = trim( preg_replace( '/[^A-Za-z0-9.\-]+/', '-', $host ), '.-' );
        return $host !== '' ? $host : 'site';
    }

    /**
     * The archive's file name: logs-<host>-<YmdHi>.<zip|tar.gz>. Pure.
     *
     * @param string $host
     * @param int $time
     * @param string $format
     * @return string
     */
    public static function fileName( $host, $time, $format )
    {
        return 'logs-' . $host . '-' . date( 'Ymd-Hi', (int)$time ) . '.' . ( $format === 'zip' ? 'zip' : 'tar.gz' );
    }

    /**
     * A new, private directory for one bundle under $base (var/tmp/logbundle by default).
     *
     * @param string|null $base
     * @return string|null
     */
    public static function workDirectory( $base = null )
    {
        if ( $base === null )
            $base = ( class_exists( 'eZSys' ) ? eZSys::rootDir() : getcwd() ) . '/var/tmp/logbundle';
        // a bundle left behind by a request that ended before its cleanup (a worker stopped mid-way) goes after an hour
        foreach ( (array)glob( rtrim( $base, '/' ) . '/[0-9]*-*', GLOB_ONLYDIR ) as $old )
            if ( @filemtime( $old ) !== false && time() - (int)@filemtime( $old ) > 3600 )
                self::cleanup( $old );
        $dir = rtrim( $base, '/' ) . '/' . date( 'YmdHis' ) . '-' . bin2hex( random_bytes( 8 ) );
        if ( !@mkdir( $dir, eZDir::dirMode( 0700 ), true ) && !is_dir( $dir ) )
            return null;
        return $dir;
    }

    /**
     * Builds the archive.
     *
     * @param array $files from files()
     * @param expLogRedactor|null $redactor null: stored as written (unredacted)
     * @param string $dir the bundle's own directory (workDirectory())
     * @param string $name the archive's file name
     * @param int $cap the most bytes of input
     * @param string|null $format zip | tar.gz; null: format()
     * @param int|null $now the time in the manifest
     * @return array ok, error ('' | too_large | write | empty), path, name, format, files, bytes (read), size (of the
     *               archive), manifest (list of path, size, mtime, sha256), total and cap (for too_large)
     */
    public static function build( array $files, $redactor, $dir, $name, $cap, $format = null, $now = null )
    {
        $format = $format === null ? self::format() : $format;
        if ( $format === 'zip' && !class_exists( 'ZipArchive' ) )
            $format = 'tar.gz';
        $result = array( 'ok' => false, 'error' => '', 'path' => '', 'name' => $name, 'format' => $format, 'files' => 0,
                         'bytes' => 0, 'size' => 0, 'manifest' => array(), 'total' => 0, 'cap' => (int)$cap );
        $total = 0;
        foreach ( $files as $f )
            $total += (int)$f['size'];
        $result['total'] = $total;
        if ( !$files )
        {
            $result['error'] = 'empty';
            return $result;
        }
        if ( $total > (int)$cap )
        {
            $result['error'] = 'too_large';
            return $result;
        }
        $stage = $dir . '/stage';
        if ( !is_dir( $stage ) && !@mkdir( $stage, eZDir::dirMode( 0700 ), true ) && !is_dir( $stage ) )
        {
            $result['error'] = 'write';
            return $result;
        }
        // every file as stored: masked line by line (a compressed rotation unpacked first), or the bytes as they are
        $entries = array();
        $n = 0;
        foreach ( $files as $f )
        {
            $entry = isset( $f['entry'] ) ? $f['entry'] : $f['id'];
            $tmp = $stage . '/' . ( $n++ ) . '.part';
            if ( $redactor !== null )
            {
                if ( !empty( $f['gz'] ) )
                    $entry = preg_replace( '/\.gz$/', '', $entry );
                $read = self::redactCopy( $f['path'], !empty( $f['gz'] ), $tmp, $redactor );
            }
            else
                $read = @copy( $f['path'], $tmp ) ? (int)@filesize( $tmp ) : false;
            if ( $read === false )
            {
                $result['error'] = 'write';
                return $result;
            }
            @chmod( $tmp, eZFile::fileMode( 0600 ) );
            $result['bytes'] += (int)$read;
            $entries[] = array( 'entry' => $entry, 'tmp' => $tmp, 'mtime' => (int)$f['mtime'] );
            $result['manifest'][] = array( 'path' => $entry, 'size' => (int)filesize( $tmp ), 'mtime' => gmdate( 'Y-m-d\TH:i:s\Z', (int)$f['mtime'] ),
                                           'sha256' => hash_file( 'sha256', $tmp ) );
        }
        $manifest = $stage . '/MANIFEST.txt';
        $lines = array( '# ' . $name . ', ' . gmdate( 'Y-m-d\TH:i:s\Z', $now === null ? time() : (int)$now ) . ', '
                        . ( $redactor !== null ? 'masked as in the log viewer' : 'unredacted' ),
                        "# path\tsize\tlast write (UTC)\tsha256" );
        foreach ( $result['manifest'] as $m )
            $lines[] = $m['path'] . "\t" . $m['size'] . "\t" . $m['mtime'] . "\t" . $m['sha256'];
        file_put_contents( $manifest, implode( "\n", $lines ) . "\n" );
        $entries[] = array( 'entry' => 'MANIFEST.txt', 'tmp' => $manifest, 'mtime' => $now === null ? time() : (int)$now );

        $path = $dir . '/' . $name;
        $ok = $format === 'zip' ? self::writeZip( $path, $entries ) : self::writeTarGz( $path, $entries );
        foreach ( $entries as $e )
            @unlink( $e['tmp'] );
        @rmdir( $stage );
        if ( !$ok )
        {
            @unlink( $path );
            $result['error'] = 'write';
            return $result;
        }
        @chmod( $path, eZFile::fileMode( 0600 ) );
        $result['ok'] = true;
        $result['path'] = $path;
        $result['files'] = count( $files );
        $result['size'] = (int)filesize( $path );
        return $result;
    }

    /**
     * A file masked line by line into $target; a compressed one unpacked as it is read.
     *
     * @return int|false the bytes read
     */
    protected static function redactCopy( $source, $gz, $target, expLogRedactor $redactor )
    {
        $in = $gz ? @gzopen( $source, 'rb' ) : @fopen( $source, 'rb' );
        $out = @fopen( $target, 'wb' );
        if ( $in === false || $out === false )
        {
            if ( $in !== false )
                $gz ? gzclose( $in ) : fclose( $in );
            if ( $out !== false )
                fclose( $out );
            return false;
        }
        // masked in blocks of whole lines (no mask crosses a line end), not line by line: the same result with a
        // fraction of the pattern calls, which is what the time of a bundle is spent on
        $read = 0;
        $carry = '';
        while ( true )
        {
            $block = $gz ? gzread( $in, self::CHUNK ) : fread( $in, self::CHUNK );
            $eof = $block === false || $block === '';
            if ( !$eof )
            {
                $read += strlen( $block );
                $block = $carry . $block;
                $nl = strrpos( $block, "\n" );
                if ( $nl === false )
                {
                    $carry = $block;
                    continue;
                }
                $carry = (string)substr( $block, $nl + 1 );
                $block = substr( $block, 0, $nl + 1 );
            }
            else
            {
                $block = $carry;
                $carry = '';
            }
            if ( $block !== '' )
                fwrite( $out, self::maskBlock( $block, $redactor ) );
            if ( $eof )
                break;
        }
        $gz ? gzclose( $in ) : fclose( $in );
        fclose( $out );
        return $read;
    }

    /**
     * Whole lines masked as the viewer masks one line: every line is masked on its own text (masks never cross a
     * line end), in one call per block. Pure.
     *
     * @param string $block whole lines
     * @param expLogRedactor $redactor
     * @return string
     */
    public static function maskBlock( $block, expLogRedactor $redactor )
    {
        return $redactor->text( $block );
    }

    /** A zip of the entries (ZipArchive reads each file when it closes, in pieces) */
    protected static function writeZip( $path, array $entries )
    {
        $zip = new ZipArchive();
        if ( $zip->open( $path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            return false;
        foreach ( $entries as $e )
        {
            if ( !$zip->addFile( $e['tmp'], $e['entry'] ) )
            {
                $zip->close();
                return false;
            }
            $zip->setMtimeName( $e['entry'], (int)$e['mtime'] );
        }
        return $zip->close();
    }

    /** A tar.gz of the entries, written as a stream */
    protected static function writeTarGz( $path, array $entries )
    {
        $out = @gzopen( $path, 'wb6' );
        if ( $out === false )
            return false;
        foreach ( $entries as $e )
        {
            $size = (int)filesize( $e['tmp'] );
            gzwrite( $out, self::tarHeader( $e['entry'], $size, (int)$e['mtime'] ) );
            $in = @fopen( $e['tmp'], 'rb' );
            if ( $in === false )
            {
                gzclose( $out );
                return false;
            }
            while ( !feof( $in ) )
            {
                $chunk = fread( $in, self::CHUNK );
                if ( $chunk === false || $chunk === '' )
                    break;
                gzwrite( $out, $chunk );
            }
            fclose( $in );
            if ( $size % 512 )
                gzwrite( $out, str_repeat( "\0", 512 - $size % 512 ) );
        }
        gzwrite( $out, str_repeat( "\0", 1024 ) );
        return gzclose( $out );
    }

    /**
     * A ustar header block of a regular file (mode 0644; a name over 100 bytes split into prefix and name). Pure.
     *
     * @param string $name
     * @param int $size
     * @param int $mtime
     * @return string 512 bytes
     */
    public static function tarHeader( $name, $size, $mtime )
    {
        $prefix = '';
        if ( strlen( $name ) > 100 )
        {
            $cut = strrpos( substr( $name, 0, 156 ), '/' );
            if ( $cut !== false && strlen( $name ) - $cut - 1 <= 100 )
            {
                $prefix = substr( $name, 0, $cut );
                $name = substr( $name, $cut + 1 );
            }
            else
                $name = substr( $name, -100 );
        }
        $header = str_pad( $name, 100, "\0" )
                . str_pad( '0000644', 7, '0', STR_PAD_LEFT ) . "\0"
                . str_pad( '0000000', 7, '0', STR_PAD_LEFT ) . "\0"
                . str_pad( '0000000', 7, '0', STR_PAD_LEFT ) . "\0"
                . str_pad( decoct( $size ), 11, '0', STR_PAD_LEFT ) . "\0"
                . str_pad( decoct( $mtime ), 11, '0', STR_PAD_LEFT ) . "\0"
                . '        '
                . '0'
                . str_repeat( "\0", 100 )
                . "ustar\0" . '00'
                . str_pad( '', 32, "\0" ) . str_pad( '', 32, "\0" )
                . str_pad( '', 8, "\0" ) . str_pad( '', 8, "\0" )
                . str_pad( $prefix, 155, "\0" )
                . str_repeat( "\0", 12 );
        $sum = 0;
        for ( $i = 0; $i < 512; $i++ )
            $sum += ord( $header[$i] );
        return substr_replace( $header, str_pad( decoct( $sum ), 6, '0', STR_PAD_LEFT ) . "\0 ", 148, 8 );
    }

    /**
     * Removes a bundle's directory and everything in it (only a directory under a "logbundle" directory).
     *
     * @param string $dir
     * @return void
     */
    public static function cleanup( $dir )
    {
        if ( !is_string( $dir ) || $dir === '' || basename( dirname( $dir ) ) !== 'logbundle' || !is_dir( $dir ) || is_link( $dir ) )
            return;
        foreach ( array( $dir . '/stage', $dir ) as $d )
        {
            if ( !is_dir( $d ) )
                continue;
            foreach ( (array)@scandir( $d ) as $f )
                if ( $f !== '.' && $f !== '..' && is_file( $d . '/' . $f ) )
                    @unlink( $d . '/' . $f );
        }
        @rmdir( $dir . '/stage' );
        @rmdir( $dir );
    }

    /**
     * The cap of a configuration ([ActionSettings] MaxBundleBytes, read from logviewer.ini when the configuration
     * has no value of its own).
     *
     * @param expLogViewerConfig $config
     * @return int
     */
    public static function cap( expLogViewerConfig $config )
    {
        $value = $config->get( 'ActionSettings', 'MaxBundleBytes' );
        if ( $value === null && class_exists( 'eZINI' ) && class_exists( 'eZSys', false ) && eZINI::exists( 'logviewer.ini' ) )
        {
            $ini = eZINI::instance( 'logviewer.ini' );
            if ( $ini->hasVariable( 'ActionSettings', 'MaxBundleBytes' ) )
                $value = $ini->variable( 'ActionSettings', 'MaxBundleBytes' );
        }
        return is_numeric( $value ) && (int)$value > 0 ? (int)$value : self::DEFAULT_MAX_BYTES;
    }
}
