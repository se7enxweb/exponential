<?php
/**
 * File containing the eZPackageUploadInspector class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Looks at a package archive before anything of it is written into a repository.
 *
 * eZPackage::import() extracted every entry of an uploaded archive into the repository as the archive named it:
 * an entry "../../../settings/override/x.php", an absolute path, a symbolic link pointing anywhere (and a file
 * written through it afterwards), a hard link or a device node all landed where they said (zip-slip). Nor was
 * there a limit on the archive's size, the number of its entries or what they unpack to, and a package.xml that
 * was not well formed or lacked the elements the parser reads ended the request with a fatal error.
 *
 * inspect() reads the archive's table of contents and its package.xml only, and says whether it may be imported:
 * - the upload's name ends in .ezpkg, .tar.gz or .tgz, and the file starts with the gzip signature;
 * - the archive is at most MaxArchiveSize bytes, has at most MaxEntries entries that unpack to at most
 *   MaxUnpackedSize bytes (package.ini [UploadSettings]);
 * - every entry is a plain file or a directory with a relative path without "..", a back slash, a NUL or a
 *   control character (safeEntryPath());
 * - package.xml is at the top, well formed, its root element is <package> and it has a <name> that
 *   eZPackage::isValidName() accepts, and its vendor gives a repository directory name.
 *
 * eZPackage::importUnaudited() calls entriesProblem() on every archive it extracts, so the command line (ezpm) and
 * the setup wizard's upload are covered as well as package/upload.
 */
class eZPackageUploadInspector
{
    const DEFAULT_MAX_ARCHIVE_SIZE = 268435456;    // 256 MiB
    const DEFAULT_MAX_UNPACKED_SIZE = 2147483648;  // 2 GiB
    const DEFAULT_MAX_ENTRIES = 50000;
    const MAX_DEFINITION_SIZE = 16777216;          // package.xml: 16 MiB

    /**
     * The limits from package.ini [UploadSettings], with the defaults above for what is not set.
     *
     * @return array( 'max_archive_size' => int, 'max_unpacked_size' => int, 'max_entries' => int, 'suffixes' => string[] )
     */
    static function limits()
    {
        $limits = array( 'max_archive_size' => self::DEFAULT_MAX_ARCHIVE_SIZE,
                         'max_unpacked_size' => self::DEFAULT_MAX_UNPACKED_SIZE,
                         'max_entries' => self::DEFAULT_MAX_ENTRIES,
                         'suffixes' => array( 'ezpkg', 'tar.gz', 'tgz' ) );
        if ( !class_exists( 'eZINI' ) )
            return $limits;
        $ini = eZINI::instance( 'package.ini' );
        foreach ( array( 'MaxArchiveSize' => 'max_archive_size', 'MaxUnpackedSize' => 'max_unpacked_size', 'MaxEntries' => 'max_entries' ) as $setting => $key )
        {
            if ( $ini->hasVariable( 'UploadSettings', $setting ) && (int)$ini->variable( 'UploadSettings', $setting ) > 0 )
                $limits[$key] = (int)$ini->variable( 'UploadSettings', $setting );
        }
        if ( $ini->hasVariable( 'UploadSettings', 'AllowedSuffixes' ) )
        {
            $suffixes = array();
            foreach ( (array)$ini->variable( 'UploadSettings', 'AllowedSuffixes' ) as $suffix )
            {
                $suffix = strtolower( trim( (string)$suffix, " .\t" ) );
                if ( $suffix !== '' )
                    $suffixes[] = $suffix;
            }
            if ( $suffixes )
                $limits['suffixes'] = $suffixes;
        }
        return $limits;
    }

    /**
     * Whether an archive entry's path stays inside the directory it is extracted to.
     *
     * @param mixed $path
     * @return bool
     */
    static function safeEntryPath( $path )
    {
        if ( !is_string( $path ) || $path === '' || strlen( $path ) > 4096 )
            return false;
        if ( preg_match( '/[\x00-\x1F\x7F\\\\]/', $path ) )
            return false;
        if ( $path[0] === '/' || preg_match( '/^[A-Za-z]:/', $path ) )
            return false;
        foreach ( explode( '/', $path ) as $segment )
        {
            if ( $segment === '..' )
                return false;
        }
        // "." and "./" (the directory itself, as some tar writers list it) stay inside it too
        return true;
    }

    /**
     * Whether the upload's own name ends in one of the allowed suffixes.
     *
     * @param string $originalName
     * @param string[] $suffixes
     * @return bool
     */
    static function hasAllowedSuffix( $originalName, array $suffixes )
    {
        $name = strtolower( (string)$originalName );
        foreach ( $suffixes as $suffix )
        {
            if ( substr( $name, -strlen( $suffix ) - 1 ) === '.' . $suffix )
                return true;
        }
        return false;
    }

    /**
     * The first reason the entries of an open archive must not be extracted, or null when they may.
     *
     * @param ezcArchive $archive
     * @param array $limits as limits()
     * @return array|null array( 'code' => ..., 'path' => ... ) or null; the archive is rewound afterwards
     */
    static function entriesProblem( $archive, array $limits = array() )
    {
        $limits += self::limits();
        $count = 0;
        $bytes = 0;
        $archive->rewind();
        foreach ( $archive as $entry )
        {
            $count++;
            $path = $entry->getPath();
            if ( $count > $limits['max_entries'] )
                return array( 'code' => 'too_many_entries', 'path' => '' );
            if ( !self::safeEntryPath( $path ) )
                return array( 'code' => 'unsafe_path', 'path' => (string)$path );
            $type = $entry->getType();
            if ( $type !== ezcArchiveEntry::IS_FILE && $type !== ezcArchiveEntry::IS_DIRECTORY )
                return array( 'code' => 'unsafe_type', 'path' => (string)$path );
            $bytes += max( 0, (int)$entry->getSize() );
            if ( $bytes > $limits['max_unpacked_size'] )
                return array( 'code' => 'too_large_unpacked', 'path' => '' );
        }
        $archive->rewind();
        return null;
    }

    /**
     * What the package definition says, from the bytes of a package.xml: name, vendor directory, version,
     * summary, or the reason it cannot be read.
     *
     * @param string $xml
     * @return array( 'ok' => bool, 'code' => string|null, 'name' => string, 'vendor_dir' => string, 'summary' => string, 'version' => string )
     */
    static function definition( $xml )
    {
        $result = array( 'ok' => false, 'code' => null, 'name' => '', 'vendor_dir' => '', 'summary' => '', 'version' => '' );
        if ( !is_string( $xml ) || trim( $xml ) === '' )
        {
            $result['code'] = 'definition_malformed';
            return $result;
        }
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $previous = libxml_use_internal_errors( true );
        $loaded = $dom->loadXML( $xml, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        if ( !$loaded || !$dom->documentElement || $dom->documentElement->nodeName !== 'package' || $dom->doctype !== null )
        {
            $result['code'] = 'definition_malformed';
            return $result;
        }
        $root = $dom->documentElement;
        $text = function ( $tag ) use ( $root )
        {
            $node = $root->getElementsByTagName( $tag )->item( 0 );
            return $node ? trim( (string)$node->textContent ) : '';
        };
        $result['name'] = $text( 'name' );
        $result['summary'] = $text( 'summary' );
        // the package's own <version>, a child of the root (<ezpublish> has one too)
        $versionNode = ( new DOMXPath( $dom ) )->query( 'version', $root )->item( 0 );
        if ( $versionNode )
        {
            $number = $versionNode->getElementsByTagName( 'number' )->item( 0 );
            $release = $versionNode->getElementsByTagName( 'release' )->item( 0 );
            $result['version'] = trim( ( $number ? $number->textContent : '' ) . ( $release ? '-' . $release->textContent : '' ) );
        }
        if ( $result['name'] === '' )
        {
            $result['code'] = 'definition_no_name';
            return $result;
        }
        if ( !eZPackage::isValidName( $result['name'] ) || !eZPackageRequestGuard::isSafeName( $result['name'] ) )
        {
            $result['code'] = 'invalid_name';
            return $result;
        }
        foreach ( array( 'ezpublish', 'packaging' ) as $required )
        {
            if ( !$root->getElementsByTagName( $required )->item( 0 ) )
            {
                $result['code'] = 'definition_incomplete';
                return $result;
            }
        }
        $vendor = $text( 'vendor' );
        if ( $vendor === '' )
            $result['vendor_dir'] = 'local';
        else
            $result['vendor_dir'] = (string)eZCharTransform::instance()->transformByGroup( $vendor, 'urlalias' );
        if ( !eZPackageRequestGuard::isSafeName( $result['vendor_dir'] ) )
        {
            $result['code'] = 'invalid_vendor';
            return $result;
        }
        $result['ok'] = true;
        return $result;
    }

    /**
     * Whether the archive at $archivePath may be imported.
     *
     * @param string $archivePath the uploaded file
     * @param string $originalName the name the browser sent (only its suffix is used)
     * @param array $limits overrides of limits()
     * @return array( 'ok' => bool, 'code' => string|null, 'path' => string (the entry concerned), 'entries' => int,
     *                'bytes' => int, 'name' => string, 'vendor_dir' => string, 'summary' => string, 'version' => string )
     */
    static function inspect( $archivePath, $originalName, array $limits = array() )
    {
        $limits += self::limits();
        $result = array( 'ok' => false, 'code' => null, 'path' => '', 'entries' => 0, 'bytes' => 0,
                         'name' => '', 'vendor_dir' => '', 'summary' => '', 'version' => '' );
        if ( !is_string( $archivePath ) || !is_file( $archivePath ) )
        {
            $result['code'] = 'no_file';
            return $result;
        }
        if ( !self::hasAllowedSuffix( $originalName, $limits['suffixes'] ) )
        {
            $result['code'] = 'suffix';
            return $result;
        }
        $size = (int)filesize( $archivePath );
        if ( $size <= 0 )
        {
            $result['code'] = 'no_file';
            return $result;
        }
        if ( $size > $limits['max_archive_size'] )
        {
            $result['code'] = 'too_large';
            return $result;
        }
        $handle = fopen( $archivePath, 'rb' );
        $magic = $handle ? fread( $handle, 2 ) : '';
        if ( $handle )
            fclose( $handle );
        if ( $magic !== "\x1f\x8b" )
        {
            $result['code'] = 'not_gzip';
            return $result;
        }

        $definitionXML = null;
        try
        {
            $archive = ezcArchive::open( "compress.zlib://$archivePath", eZPackage::archiveTarType( $archivePath ),
                                         new ezcArchiveOptions( array( 'readOnly' => true ) ) );
            $problem = self::entriesProblem( $archive, $limits );
            if ( $problem !== null )
            {
                $result['code'] = $problem['code'];
                $result['path'] = $problem['path'];
                return $result;
            }
            foreach ( $archive as $entry )
            {
                $result['entries']++;
                $result['bytes'] += max( 0, (int)$entry->getSize() );
                if ( $definitionXML === null && $entry->getPath() === eZPackage::definitionFilename() && $entry->getType() === ezcArchiveEntry::IS_FILE )
                {
                    if ( (int)$entry->getSize() > self::MAX_DEFINITION_SIZE )
                    {
                        $result['code'] = 'definition_malformed';
                        return $result;
                    }
                    $definitionXML = self::currentEntryBytes( $archive );
                }
            }
        }
        catch ( Exception $e )
        {
            $result['code'] = 'unreadable';
            return $result;
        }
        if ( $definitionXML === null )
        {
            $result['code'] = 'no_definition';
            return $result;
        }
        $definition = self::definition( $definitionXML );
        foreach ( array( 'name', 'vendor_dir', 'summary', 'version' ) as $key )
            $result[$key] = $definition[$key];
        if ( !$definition['ok'] )
        {
            $result['code'] = $definition['code'];
            return $result;
        }
        $result['ok'] = true;
        return $result;
    }

    /**
     * The bytes of the archive's current entry, through a private temporary directory that is removed again.
     */
    private static function currentEntryBytes( $archive )
    {
        $dir = eZDir::path( array( eZSys::cacheDirectory(), 'packages', 'inspect-' . bin2hex( random_bytes( 8 ) ) ) );
        eZDir::mkdir( $dir, false, true );
        $bytes = '';
        try
        {
            if ( $archive->extractCurrent( $dir ) )
            {
                $file = $dir . '/' . eZPackage::definitionFilename();
                $bytes = is_file( $file ) ? (string)file_get_contents( $file ) : '';
            }
        }
        finally
        {
            eZDir::recursiveDelete( $dir );
        }
        return $bytes;
    }

    /**
     * The message for a code of inspect(), for the upload page.
     *
     * @param array $result of inspect()
     * @param array $limits as limits()
     * @return string
     */
    static function message( array $result, array $limits = array() )
    {
        $limits += self::limits();
        $path = array( '%path' => $result['path'] );
        switch ( $result['code'] )
        {
            case 'no_file': return ezpI18n::tr( 'kernel/package', 'No file was uploaded, or the file is empty.' );
            case 'suffix': return ezpI18n::tr( 'kernel/package', 'The file is not a package: its name must end in %suffixes.', false, array( '%suffixes' => '.' . implode( ', .', $limits['suffixes'] ) ) );
            case 'too_large': return ezpI18n::tr( 'kernel/package', 'The file is larger than the %size a package may have.', false, array( '%size' => self::bytesText( $limits['max_archive_size'] ) ) );
            case 'not_gzip': return ezpI18n::tr( 'kernel/package', 'The file is not a package: a package is a gzip compressed tar archive.' );
            case 'unreadable': return ezpI18n::tr( 'kernel/package', 'The archive cannot be read; it may be damaged.' );
            case 'too_many_entries': return ezpI18n::tr( 'kernel/package', 'The archive has more than %count entries.', false, array( '%count' => $limits['max_entries'] ) );
            case 'too_large_unpacked': return ezpI18n::tr( 'kernel/package', 'The archive unpacks to more than %size.', false, array( '%size' => self::bytesText( $limits['max_unpacked_size'] ) ) );
            case 'unsafe_path': return ezpI18n::tr( 'kernel/package', 'The archive was refused: the entry "%path" points outside the package.', false, $path );
            case 'unsafe_type': return ezpI18n::tr( 'kernel/package', 'The archive was refused: the entry "%path" is a link or a device, and a package holds only files and directories.', false, $path );
            case 'no_definition': return ezpI18n::tr( 'kernel/package', 'The archive has no package.xml at its top, so it is not a package.' );
            case 'definition_malformed': return ezpI18n::tr( 'kernel/package', 'The package.xml of the archive is not a well formed package definition.' );
            case 'definition_no_name': return ezpI18n::tr( 'kernel/package', 'The package.xml of the archive names no package.' );
            case 'definition_incomplete': return ezpI18n::tr( 'kernel/package', 'The package.xml of the archive lacks its version or packaging information.' );
            case 'invalid_name': return ezpI18n::tr( 'kernel/package', 'The package name %packagename is invalid, cannot import the package', false, array( '%packagename' => $result['name'] ) );
            case 'invalid_vendor': return ezpI18n::tr( 'kernel/package', 'The vendor of the package gives no usable repository name.' );
        }
        return ezpI18n::tr( 'kernel/package', 'The package could not be imported.' );
    }

    /**
     * A byte count as "12.3 MB" (decimal units, as the templates' si operator).
     */
    static function bytesText( $bytes )
    {
        $bytes = (float)$bytes;
        foreach ( array( 'B', 'kB', 'MB', 'GB', 'TB' ) as $i => $unit )
        {
            if ( $bytes < 1000 || $unit === 'TB' )
                return ( $i === 0 ? (string)(int)$bytes : number_format( $bytes, 1, '.', '' ) ) . ' ' . $unit;
            $bytes /= 1000;
        }
        return '';
    }
}

?>
