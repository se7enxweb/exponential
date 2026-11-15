<?php
/**
 * File containing the expImageFormatConverter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The format variants of image aliases (WebP, AVIF): which converter can write which format, and the conversion of an
 * alias file into a variant.
 *
 * Converters, in the order of image.ini [AliasSettings] PreferredConverter:
 *
 *   imagick      the PHP extension imagick (Imagick::queryFormats())
 *   imagemagick  the ImageMagick command of [ImageMagick] (ExecutablePath, Executable; only while IsEnabled=true),
 *                its formats from "<command> -list format"
 *   gd           the GD functions imagewebp() and imageavif(), as gd_info() reports them
 *
 * auto tries them in that order; a named converter is tried first and the others follow. A format no converter can
 * write is left out: the alias is answered in its own format.
 *
 * A variant lies next to its alias, named "<alias file>.<format>" (foo_large.jpg.webp). It is no alias of its own:
 * neither the attribute's XML nor ezimagefile name it, so both look exactly as in render mode. It is made again
 * whenever the alias file is newer than the variant, and removed with its alias (eZImageAliasHandler).
 *
 * The aliases of AliasList[] themselves are still made by [ImageConverterSettings] ImageConverters[], in both modes,
 * so an alias made on demand is byte for byte the alias render mode makes.
 *
 * @since 6.0.15
 * @package kernel
 */
class expImageFormatConverter
{
    /** @var string[] format => MIME type */
    const FORMATS = array( 'webp' => 'image/webp', 'avif' => 'image/avif' );

    /** @var string[] the converters, in the order of PreferredConverter=auto */
    const CONVERTERS = array( 'imagick', 'imagemagick', 'gd' );

    /** @var int seconds the formats of the ImageMagick command are kept in the cache file */
    const CACHE_TTL = 86400;

    /** @var array|null converter => formats it writes, set by tests (setCapabilities()) or found once per process */
    protected static $capabilities = null;

    /** @var bool the capabilities were set from outside (tests): not found and not cached */
    protected static $fixed = false;

    /** @var string|null the converter of the last convert() */
    protected static $lastConverter = null;

    // ------------------------------------------------------------------------------------------------ formats

    /**
     * @param string $format
     * @return string|null the MIME type of a variant format
     */
    public static function mimeType( $format )
    {
        $format = strtolower( (string)$format );
        return isset( self::FORMATS[$format] ) ? self::FORMATS[$format] : null;
    }

    /**
     * The storage path of the variant $format of an alias file.
     *
     * @param string $aliasPath
     * @param string $format
     * @return string
     */
    public static function variantPath( $aliasPath, $format )
    {
        return (string)$aliasPath . '.' . strtolower( (string)$format );
    }

    /**
     * Splits the path of a variant into the alias file and the format.
     *
     * @param string $path
     * @return array|null array( alias path, format ), null when the path names no variant
     */
    public static function splitVariant( $path )
    {
        if ( !preg_match( '#^(.+/[^/]+\.[A-Za-z0-9]{1,8})\.(webp|avif)$#', (string)$path, $match ) )
            return null;
        // foo_large.webp.webp is no variant: an alias that is a WebP file has none
        $suffix = strtolower( substr( $match[1], strrpos( $match[1], '.' ) + 1 ) );
        if ( $suffix === $match[2] )
            return null;
        return array( $match[1], $match[2] );
    }

    /**
     * Whether an alias of MIME type $aliasMime gets variants at all: not one already in that format, never an SVG
     * (no alias is made of it) nor a GIF (animated GIFs would lose their frames).
     *
     * @param string $aliasMime
     * @param string $format
     * @return bool
     */
    public static function variantApplies( $aliasMime, $format )
    {
        $mime = self::mimeType( $format );
        if ( $mime === null )
            return false;
        return !in_array( strtolower( (string)$aliasMime ), array( $mime, 'image/svg+xml', 'image/gif' ), true );
    }

    // ------------------------------------------------------------------------------------------------ converters

    /**
     * The converters in the order they are tried (image.ini [AliasSettings] PreferredConverter).
     *
     * @param string|null $preferred auto|imagick|imagemagick|gd; null: the setting
     * @return string[]
     */
    public static function order( $preferred = null )
    {
        if ( $preferred === null )
            $preferred = expImageAliasSettings::preferredConverter();
        $order = self::CONVERTERS;
        if ( in_array( $preferred, $order, true ) )
            $order = array_values( array_unique( array_merge( array( $preferred ), $order ) ) );
        return $order;
    }

    /**
     * What each converter can write.
     *
     * @param bool $refresh find out again (the ImageMagick command is asked again too)
     * @return array converter => string[] formats (webp, avif)
     */
    public static function capabilities( $refresh = false )
    {
        if ( self::$fixed )
            return self::$capabilities;
        if ( self::$capabilities !== null && !$refresh )
            return self::$capabilities;
        self::$capabilities = array( 'imagick' => self::imagickFormats(), 'imagemagick' => self::imageMagickFormats( $refresh ),
                                     'gd' => self::gdFormats() );
        return self::$capabilities;
    }

    /**
     * Sets what the converters can write (tests); null finds it out again.
     *
     * @param array|null $capabilities
     */
    public static function setCapabilities( $capabilities )
    {
        self::$capabilities = $capabilities;
        self::$fixed = $capabilities !== null;
    }

    /**
     * The converters that can write $format, in the order they are tried.
     *
     * @param string $format
     * @param string|null $preferred
     * @return string[]
     */
    public static function convertersFor( $format, $preferred = null )
    {
        $format = strtolower( (string)$format );
        $capabilities = self::capabilities();
        $out = array();
        foreach ( self::order( $preferred ) as $converter )
        {
            if ( isset( $capabilities[$converter] ) && in_array( $format, (array)$capabilities[$converter], true ) )
                $out[] = $converter;
        }
        return $out;
    }

    /**
     * @param string $format
     * @return bool some converter can write $format
     */
    public static function canWrite( $format )
    {
        return self::mimeType( $format ) !== null && self::convertersFor( $format ) !== array();
    }

    /**
     * @return string|null the converter that wrote the last variant
     */
    public static function lastConverter()
    {
        return self::$lastConverter;
    }

    /**
     * The formats the imagick extension can write.
     *
     * @return string[]
     */
    public static function imagickFormats()
    {
        if ( !extension_loaded( 'imagick' ) || !class_exists( 'Imagick' ) )
            return array();
        $out = array();
        foreach ( array_keys( self::FORMATS ) as $format )
        {
            try
            {
                if ( Imagick::queryFormats( strtoupper( $format ) ) )
                    $out[] = $format;
            }
            catch ( Throwable $e )
            {
            }
        }
        return $out;
    }

    /**
     * The formats GD can write.
     *
     * @return string[]
     */
    public static function gdFormats()
    {
        if ( !function_exists( 'gd_info' ) )
            return array();
        $info = gd_info();
        $out = array();
        if ( function_exists( 'imagewebp' ) && !empty( $info['WebP Support'] ) )
            $out[] = 'webp';
        if ( function_exists( 'imageavif' ) && !empty( $info['AVIF Support'] ) )
            $out[] = 'avif';
        return $out;
    }

    /**
     * The ImageMagick command of image.ini [ImageMagick], as an absolute path where it can be found; null when
     * ImageMagick is not enabled or not there.
     *
     * @return string|null
     */
    public static function imageMagickCommand()
    {
        if ( !class_exists( 'eZINI' ) )
            return null;
        $ini = eZINI::instance( 'image.ini' );
        if ( !$ini->hasGroup( 'ImageMagick' ) || $ini->variable( 'ImageMagick', 'IsEnabled' ) !== 'true'
             || !$ini->hasVariable( 'ImageMagick', 'Executable' ) )
            return null;
        $executable = trim( (string)$ini->variable( 'ImageMagick', 'Executable' ) );
        $path = $ini->hasVariable( 'ImageMagick', 'ExecutablePath' ) ? rtrim( trim( (string)$ini->variable( 'ImageMagick', 'ExecutablePath' ) ), '/' ) : '';
        if ( $executable === '' || strpbrk( $executable, "/\\ \0;&|`$<>" ) !== false )
            return null;
        $directories = $path !== '' ? array( $path ) : array_merge( explode( PATH_SEPARATOR, (string)getenv( 'PATH' ) ),
                                                                   array( '/usr/bin', '/usr/local/bin', '/opt/local/bin' ) );
        foreach ( $directories as $directory )
        {
            if ( $directory !== '' && @is_file( $directory . '/' . $executable ) && @is_executable( $directory . '/' . $executable ) )
                return $directory . '/' . $executable;
        }
        return null;
    }

    /**
     * The formats the ImageMagick command can write ("-list format": a w in the mode column), kept for a day in the
     * cache directory, per command and its modification time.
     *
     * @param bool $refresh
     * @return string[]
     */
    public static function imageMagickFormats( $refresh = false )
    {
        $command = self::imageMagickCommand();
        if ( $command === null || !self::canRun() )
            return array();
        $cacheFile = class_exists( 'eZSys' ) ? eZSys::cacheDirectory() . '/imagealias-formats.php' : null;
        $key = $command . '|' . (int)@filemtime( $command );
        if ( !$refresh && $cacheFile !== null && is_file( $cacheFile ) )
        {
            $cached = @include $cacheFile;
            if ( is_array( $cached ) && isset( $cached['key'], $cached['time'], $cached['formats'] ) && $cached['key'] === $key
                 && $cached['time'] > time() - self::CACHE_TTL )
                return (array)$cached['formats'];
        }
        $output = array();
        $status = 1;
        @exec( escapeshellarg( $command ) . ' -list format 2>/dev/null', $output, $status );
        $formats = $status === 0 ? self::parseFormatList( $output ) : array();
        if ( $cacheFile !== null && $status === 0 )
        {
            $text = '<?php return ' . var_export( array( 'key' => $key, 'time' => time(), 'formats' => $formats ), true ) . ";\n";
            if ( class_exists( 'eZFile' ) )
                eZFile::create( basename( $cacheFile ), dirname( $cacheFile ), $text, true );
        }
        return $formats;
    }

    /**
     * Reads the output of "convert -list format" (ImageMagick 6 and 7): the variant formats with a w in the mode.
     *
     * @param string[] $lines
     * @return string[]
     */
    public static function parseFormatList( array $lines )
    {
        $out = array();
        foreach ( $lines as $line )
        {
            //      WEBP* WEBP      rw+   WebP Image Format (libwebp 1.2.0 [020F])
            if ( !preg_match( '/^\s*([A-Za-z0-9-]+)\*?\s+(?:[A-Za-z0-9-]+\s+)?([r-][w-][+-])\s/', (string)$line, $match ) )
                continue;
            $format = strtolower( $match[1] );
            if ( isset( self::FORMATS[$format] ) && $match[2][1] === 'w' && !in_array( $format, $out, true ) )
                $out[] = $format;
        }
        return $out;
    }

    /**
     * @return bool exec() can run (not in disable_functions)
     */
    protected static function canRun()
    {
        if ( !function_exists( 'exec' ) )
            return false;
        $disabled = array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) );
        return !in_array( 'exec', $disabled, true );
    }

    // ------------------------------------------------------------------------------------------------ converting

    /**
     * Writes the variant $format of the image file $source to $destination with the first converter that can (the
     * next one when it fails). The file appears whole or not at all (a temporary file in the same directory, then
     * renamed).
     *
     * @param string $source a local image file (JPEG, PNG or WebP)
     * @param string $destination
     * @param string $format webp|avif
     * @param int $quality 1..100
     * @param string|null $preferred the converter tried first (default: the setting)
     * @return bool
     */
    public static function convert( $source, $destination, $format, $quality, $preferred = null )
    {
        self::$lastConverter = null;
        $format = strtolower( (string)$format );
        if ( self::mimeType( $format ) === null || !is_file( $source ) )
            return false;
        $quality = max( 1, min( 100, (int)$quality ) );
        $directory = dirname( $destination );
        if ( !is_dir( $directory ) )
            eZDir::mkdir( $directory, false, true );
        foreach ( self::convertersFor( $format, $preferred ) as $converter )
        {
            $temporary = $directory . '/.' . basename( $destination ) . '.' . getmypid() . '.' . mt_rand() . '.tmp';
            $done = false;
            try
            {
                switch ( $converter )
                {
                    case 'imagick':
                        $done = self::convertImagick( $source, $temporary, $format, $quality );
                        break;
                    case 'imagemagick':
                        $done = self::convertCommand( $source, $temporary, $format, $quality );
                        break;
                    case 'gd':
                        $done = self::convertGD( $source, $temporary, $format, $quality );
                        break;
                }
            }
            catch ( Throwable $e )
            {
                $done = false;
                if ( class_exists( 'eZDebug' ) )
                    eZDebug::writeWarning( "The converter $converter could not write $format of $source: " . $e->getMessage(), __METHOD__ );
            }
            clearstatcache( true, $temporary );
            if ( $done && is_file( $temporary ) && self::hasSignature( $temporary, $format ) )
            {
                // The mode of every image variation (image.ini ImagePermissions), set before the rename
                eZImageHandler::changeFilePermissions( $temporary );
                if ( @rename( $temporary, $destination ) )
                {
                    self::$lastConverter = $converter;
                    return true;
                }
            }
            if ( is_file( $temporary ) )
                @unlink( $temporary );
        }
        return false;
    }

    /**
     * Whether a file starts as a file of $format does (RIFF....WEBP, ....ftypavif/avis).
     *
     * @param string $path
     * @param string $format
     * @return bool
     */
    public static function hasSignature( $path, $format )
    {
        return self::headHasSignature( (string)@file_get_contents( $path, false, null, 0, 16 ), $format );
    }

    /**
     * Whether the first bytes of a file are the signature of $format: RIFF....WEBP, or an ISO box ftyp with the brand
     * avif or avis. Decides an AVIF where PHP reads no size (getimagesize() before PHP 8.2: 0 x 0 on 8.1, nothing on 8.0).
     *
     * @param string $head at least the first 12 bytes
     * @param string $format webp or avif
     * @return bool
     */
    public static function headHasSignature( $head, $format )
    {
        $head = (string)$head;
        if ( $format === 'webp' )
            return strlen( $head ) >= 12 && substr( $head, 0, 4 ) === 'RIFF' && substr( $head, 8, 4 ) === 'WEBP';
        if ( $format === 'avif' )
            return strlen( $head ) >= 12 && substr( $head, 4, 4 ) === 'ftyp' && in_array( substr( $head, 8, 4 ), array( 'avif', 'avis' ), true );
        return false;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param string $format
     * @param int $quality
     * @return bool
     */
    protected static function convertImagick( $source, $destination, $format, $quality )
    {
        $image = new Imagick();
        $image->readImage( $source . '[0]' );
        $image->setImageFormat( strtoupper( $format ) );
        $image->setImageCompressionQuality( $quality );
        $done = $image->writeImage( $format . ':' . $destination );
        $image->clear();
        return (bool)$done;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param string $format
     * @param int $quality
     * @return bool
     */
    protected static function convertCommand( $source, $destination, $format, $quality )
    {
        $command = self::imageMagickCommand();
        if ( $command === null || !self::canRun() )
            return false;
        $output = array();
        $status = 1;
        exec( escapeshellarg( $command ) . ' ' . escapeshellarg( $source . '[0]' ) . ' -quality ' . (int)$quality . ' '
              . escapeshellarg( $format . ':' . $destination ) . ' 2>&1', $output, $status );
        return $status === 0;
    }

    /**
     * @param string $source
     * @param string $destination
     * @param string $format
     * @param int $quality
     * @return bool
     */
    protected static function convertGD( $source, $destination, $format, $quality )
    {
        $info = @getimagesize( $source );
        if ( !$info )
            return false;
        switch ( $info[2] )
        {
            case IMAGETYPE_JPEG:
                $image = @imagecreatefromjpeg( $source );
                break;
            case IMAGETYPE_PNG:
                $image = @imagecreatefrompng( $source );
                break;
            case IMAGETYPE_WEBP:
                $image = function_exists( 'imagecreatefromwebp' ) ? @imagecreatefromwebp( $source ) : false;
                break;
            default:
                return false;
        }
        if ( !$image )
            return false;
        if ( !imageistruecolor( $image ) )
            imagepalettetotruecolor( $image );
        imagealphablending( $image, false );
        imagesavealpha( $image, true );
        $done = $format === 'webp' ? @imagewebp( $image, $destination, $quality ) : @imageavif( $image, $destination, $quality );
        if ( PHP_VERSION_ID < 80000 )
            imagedestroy( $image );
        return (bool)$done;
    }
}
