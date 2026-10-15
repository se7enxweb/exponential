<?php
/**
 * File containing the expImageUploadValidator class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit c8e45fced7), Copyright (C) Mugo Web, GPL v2
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 04b3b6985a), Copyright (C) Mugo Web, GPL v2
 */

/**
 * Decides whether an uploaded file is an image the site accepts, by what the file contains rather
 * than by its name.
 *
 * The type is read with PHP's fileinfo extension (finfo) and compared with the allow-list
 * image.ini [ImageUploadSettings] AllowedMIMETypes[] (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP and SVG
 * as shipped; an SVG, recognised by its root element, also when gzip-compressed, is stored only
 * after expSvgSanitizer has made it safe, see eZImageType::svgAccepted()). ValidateMIMEType=disabled
 * turns the allow-list off and leaves the older check: the
 * file must be an image by getimagesize(), or by its name where getimagesize() is not there.
 *
 * Without fileinfo the same older check gives the type, which is still compared with the list,
 * and one warning per process says so.
 *
 * Used for every image that enters the site: the image datatype (edit form, upload module,
 * eZContentUpload, the rich text editor's upload dialog, the AJAX uploader, WebDAV and imports)
 * and the package thumbnails of the package wizard.
 *
 * Holds no request data; the only static is the "fileinfo is missing" warning flag.
 *
 * @package kernel
 */
class expImageUploadValidator
{
    /** The allow-list when image.ini does not set one */
    const DEFAULT_ALLOWED_MIME_TYPES = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                                              'image/avif', 'image/tiff', 'image/bmp', 'image/svg+xml' );

    /** Other names libmagic and browsers give the same formats */
    const MIME_ALIASES = array( 'image/pjpeg' => 'image/jpeg',
                                'image/jpg' => 'image/jpeg',
                                'image/x-png' => 'image/png',
                                'image/x-ms-bmp' => 'image/bmp',
                                'image/x-bmp' => 'image/bmp',
                                'image/x-windows-bmp' => 'image/bmp',
                                'image/x-tiff' => 'image/tiff',
                                'image/tiff-fx' => 'image/tiff',
                                'image/svg' => 'image/svg+xml' );

    /** @var int How often the "fileinfo is missing" warning was written in this process (once at most) */
    protected static $finfoMissingWarnings = 0;

    /**
     * Whether the file at $path is an image the site accepts, and its type.
     *
     * @param string $path the file on disk (an upload's temporary file is fine)
     * @param string|null $originalName the name the file was uploaded with, used for the type only
     *        when neither fileinfo nor getimagesize() is available
     * @return array{valid: bool, mime: string} mime is the type found ('' when none was)
     */
    public static function validate( $path, $originalName = null )
    {
        return static::validateAgainst( $path, static::allowedMIMETypes(), static::isEnabled(), $originalName );
    }

    /**
     * validate() with the settings given instead of read from image.ini.
     *
     * @param string $path
     * @param string[] $allowedMIMETypes
     * @param bool $checkAllowList false: any image type by the older check, as ValidateMIMEType=disabled
     * @param string|null $originalName
     * @return array{valid: bool, mime: string}
     */
    public static function validateAgainst( $path, array $allowedMIMETypes, $checkAllowList = true, $originalName = null )
    {
        if ( !is_string( $path ) || $path === '' || strpos( $path, "\0" ) !== false || !is_file( $path ) ||
             !is_readable( $path ) || !filesize( $path ) )
        {
            return array( 'valid' => false, 'mime' => '' );
        }

        if ( !$checkAllowList )
        {
            $mime = static::fallbackMIMEType( $path, $originalName );
            return array( 'valid' => strpos( $mime, 'image/' ) === 0, 'mime' => $mime );
        }

        if ( static::hasFileInfo() )
        {
            $mime = static::fileInfoMIMEType( $path );
        }
        else
        {
            static::warnFileInfoMissing();
            $mime = static::fallbackMIMEType( $path, $originalName );
        }
        // An SVG by its root element: libmagic says text/xml, text/plain or application/gzip (.svgz) for many
        if ( $mime !== 'image/svg+xml' && strpos( $mime, 'image/' ) !== 0 && expSvgSanitizer::isSVGFile( $path ) )
            $mime = 'image/svg+xml';

        $allowed = array();
        foreach ( $allowedMIMETypes as $type )
        {
            $type = static::normalize( $type );
            if ( $type !== '' )
                $allowed[$type] = true;
        }
        return array( 'valid' => $mime !== '' && isset( $allowed[$mime] ), 'mime' => $mime );
    }

    /**
     * The allow-list, image.ini [ImageUploadSettings] AllowedMIMETypes[], normalized; the shipped
     * list when the setting is not there.
     *
     * @return string[]
     */
    public static function allowedMIMETypes()
    {
        $ini = eZINI::instance( 'image.ini' );
        if ( !$ini->hasVariable( 'ImageUploadSettings', 'AllowedMIMETypes' ) )
            return self::DEFAULT_ALLOWED_MIME_TYPES;
        $list = array();
        foreach ( (array)$ini->variable( 'ImageUploadSettings', 'AllowedMIMETypes' ) as $type )
        {
            $type = static::normalize( $type );
            if ( $type !== '' && !in_array( $type, $list, true ) )
                $list[] = $type;
        }
        return $list;
    }

    /**
     * True unless image.ini [ImageUploadSettings] ValidateMIMEType=disabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        $ini = eZINI::instance( 'image.ini' );
        return !$ini->hasVariable( 'ImageUploadSettings', 'ValidateMIMEType' ) ||
               $ini->variable( 'ImageUploadSettings', 'ValidateMIMEType' ) !== 'disabled';
    }

    /**
     * $type in lower case, without parameters, under its usual name.
     *
     * @param mixed $type
     * @return string
     */
    public static function normalize( $type )
    {
        if ( !is_string( $type ) )
            return '';
        $type = strtolower( trim( $type ) );
        $semicolon = strpos( $type, ';' );
        if ( $semicolon !== false )
            $type = rtrim( substr( $type, 0, $semicolon ) );
        return isset( self::MIME_ALIASES[$type] ) ? self::MIME_ALIASES[$type] : $type;
    }

    /**
     * How often the "fileinfo is missing" warning was written in this process: 0 or 1.
     *
     * @return int
     */
    public static function fileInfoMissingWarnings()
    {
        return static::$finfoMissingWarnings;
    }

    /** @return bool whether PHP's fileinfo extension is there */
    protected static function hasFileInfo()
    {
        return function_exists( 'finfo_open' ) && function_exists( 'finfo_file' );
    }

    /**
     * The type fileinfo reads from the content, normalized; '' when it reads none.
     *
     * @param string $path
     * @return string
     */
    protected static function fileInfoMIMEType( $path )
    {
        $info = @finfo_open( FILEINFO_MIME_TYPE );
        if ( !$info )
            return static::fallbackMIMEType( $path, null );
        $mime = @finfo_file( $info, $path );
        return is_string( $mime ) ? static::normalize( $mime ) : '';
    }

    /**
     * The type by the check from before fileinfo: getimagesize(), or the file name where
     * getimagesize() is not there.
     *
     * @param string $path
     * @param string|null $originalName
     * @return string
     */
    protected static function fallbackMIMEType( $path, $originalName )
    {
        if ( function_exists( 'getimagesize' ) )
        {
            $info = @getimagesize( $path );
            return is_array( $info ) && isset( $info['mime'] ) && is_string( $info['mime'] ) ? static::normalize( $info['mime'] ) : '';
        }
        $name = is_string( $originalName ) && $originalName !== '' ? $originalName : $path;
        $mimeData = eZMimeType::findByURL( $name );
        return isset( $mimeData['name'] ) ? static::normalize( $mimeData['name'] ) : '';
    }

    /** Writes, once per process, that the type is checked without fileinfo */
    protected static function warnFileInfoMissing()
    {
        if ( static::$finfoMissingWarnings > 0 )
            return;
        static::$finfoMissingWarnings = 1;
        eZDebug::writeWarning( 'PHP\'s fileinfo extension is not available: uploaded images are checked with getimagesize() ' .
                               'or by their file name instead. Install or enable fileinfo for the check by content.',
                               __METHOD__ );
    }
}
