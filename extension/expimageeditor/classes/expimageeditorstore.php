<?php
/**
 * File containing the expImageEditorStore class
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expimageeditor
 */

/**
 * What the save view of the image editor does on the server: the editor works in the browser and posts the finished
 * image; this class checks who posts it and what, and stores it as the image of the draft's attribute.
 *
 * - editableAttribute(): the attribute must be an image (ezimage) of a draft, internal draft or repeat version that
 *   the current user created, and the user must be allowed to edit the object in the attribute's language (as
 *   content/edit requires). The anonymous user is refused: all anonymous visitors share one user ID.
 * - checkUpload(): the posted file must be an image the datatype stores (eZImageType::imageFileError(): type by
 *   content, AllowedMIMETypes[], size in pixels, SVG through the sanitizer), of the same kind as the stored one (an
 *   SVG stays an SVG, a raster image stays a raster image; an attribute without an image takes a new drawing, which
 *   must be an SVG and is stored as drawing.svg), of a type on expimageeditor.ini [EditorSettings] OutputMIMETypes[],
 *   and within the class attribute's maximum file size.
 * - store(): stores it through eZImageAliasHandler::initializeFromFile(), which writes an SVG only as the output of
 *   expSvgSanitizer, and keeps the alternative text. The previous version keeps its own image. The new original must
 *   exist afterwards, else nothing is recorded; files of the draft that initializeFromFile() has already replaced are
 *   not restored (the draft keeps the previous image only in the database), so the form shows the error.
 *
 * Every refusal is an expImageEditorException with the HTTP status to answer with.
 *
 * @package expimageeditor
 */
class expImageEditorStore
{
    /** The field of the posted image */
    const FILE_FIELD = 'image';

    /** File name extension per stored type */
    const EXTENSIONS = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    );

    /**
     * The image attribute the current user may edit with the editor.
     *
     * @param int $attributeId
     * @param int $version
     * @return eZContentObjectAttribute
     * @throws expImageEditorException
     */
    public static function editableAttribute( $attributeId, $version )
    {
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser || $user->isAnonymous() )
            throw new expImageEditorException( 403, 'Log in to edit images.' );

        $attribute = eZContentObjectAttribute::fetch( (int)$attributeId, (int)$version );
        if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'data_type_string' ) !== 'ezimage' )
            throw new expImageEditorException( 404, 'The image does not exist.' );

        $object = eZContentObject::fetch( $attribute->attribute( 'contentobject_id' ) );
        $objectVersion = eZContentObjectVersion::fetchVersion( $attribute->attribute( 'version' ), $attribute->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || !$objectVersion instanceof eZContentObjectVersion )
            throw new expImageEditorException( 404, 'The image does not exist.' );

        self::checkEditable( $object, $objectVersion, $attribute->attribute( 'language_code' ), (int)$user->attribute( 'contentobject_id' ) );
        return $attribute;
    }

    /**
     * Whether the user may change the attribute of this version: the object may be edited in the language, and the
     * version is a draft of the user's own.
     *
     * @param eZContentObject $object
     * @param eZContentObjectVersion $objectVersion
     * @param string $languageCode
     * @param int $userId
     * @throws expImageEditorException
     */
    public static function checkEditable( $object, $objectVersion, $languageCode, $userId )
    {
        if ( !$object->canEdit( false, false, false, $languageCode ) )
            throw new expImageEditorException( 403, 'You are not allowed to edit this image.' );

        $editableStates = array( eZContentObjectVersion::STATUS_DRAFT,
                                 eZContentObjectVersion::STATUS_INTERNAL_DRAFT,
                                 eZContentObjectVersion::STATUS_REPEAT );
        if ( !in_array( (int)$objectVersion->attribute( 'status' ), $editableStates, true ) ||
             (int)$objectVersion->attribute( 'creator_id' ) !== (int)$userId )
            throw new expImageEditorException( 403, 'Only an image in your own draft can be edited.' );
    }

    /**
     * Checks the posted image against the stored one and returns the path to store and the name to store it under.
     *
     * @param array|null $file the entry of $_FILES
     * @param eZContentObjectAttribute $attribute
     * @param bool $mustBeUploaded false only in tests: accept a file that is not an HTTP upload
     * @return array( 'path' => string, 'name' => string, 'mime' => string )
     * @throws expImageEditorException
     */
    public static function checkUpload( $file, $attribute, $mustBeUploaded = true )
    {
        if ( !is_array( $file ) || !isset( $file['tmp_name'], $file['error'], $file['size'] ) || is_array( $file['tmp_name'] ) )
            throw new expImageEditorException( 400, 'No image was sent.' );
        if ( (int)$file['error'] === UPLOAD_ERR_INI_SIZE || (int)$file['error'] === UPLOAD_ERR_FORM_SIZE )
            throw new expImageEditorException( 413, 'The edited image is larger than the server accepts.' );
        if ( (int)$file['error'] !== UPLOAD_ERR_OK )
            throw new expImageEditorException( 400, 'The image could not be received.' );

        $path = (string)$file['tmp_name'];
        if ( $mustBeUploaded ? !is_uploaded_file( $path ) : !is_file( $path ) )
            throw new expImageEditorException( 400, 'No image was sent.' );

        $maxBytes = self::maxBytes( $attribute );
        clearstatcache( true, $path );
        if ( $maxBytes > 0 && filesize( $path ) > $maxBytes )
            throw new expImageEditorException( 413, 'The edited image is larger than this image field accepts.' );
        $editorMaxBytes = self::editorMaxBytes();
        if ( $editorMaxBytes > 0 && filesize( $path ) > $editorMaxBytes )
            throw new expImageEditorException( 413, 'The edited image is larger than the editor stores.' );

        $content = $attribute->attribute( 'content' );
        if ( !$content instanceof eZImageAliasHandler )
            throw new expImageEditorException( 404, 'The image does not exist.' );
        $original = $content->attribute( 'original' );
        // an attribute without an image takes a new drawing, which is an SVG
        $isNew = !is_array( $original ) || empty( $original['is_valid'] );

        $isSVG = expSvgSanitizer::isSVGFile( $path );
        if ( $isNew && !$isSVG )
            throw new expImageEditorException( 415, 'A new drawing must be an SVG image.' );
        $wasSVG = $isNew || ( isset( $original['mime_type'] ) && $original['mime_type'] === 'image/svg+xml' );
        if ( $isSVG !== $wasSVG )
            throw new expImageEditorException( 415, $wasSVG ? 'An SVG image can only be replaced by an SVG image here.'
                                                            : 'A raster image can only be replaced by a raster image here.' );

        $error = eZImageType::imageFileError( $path, isset( $file['name'] ) ? (string)$file['name'] : null );
        if ( $error !== false )
            throw new expImageEditorException( 415, $error );

        $mime = $isSVG ? 'image/svg+xml' : self::rasterType( $path );
        if ( !in_array( $mime, self::outputMIMETypes(), true ) || !isset( self::EXTENSIONS[$mime] ) )
            throw new expImageEditorException( 415, 'The editor may not store images of this type.' );

        $originalName = !$isNew && isset( $original['original_filename'] ) ? (string)$original['original_filename'] : 'drawing';
        $upload = array( 'path' => $path, 'name' => self::targetName( $originalName, $mime ), 'mime' => $mime, 'temporary' => false );
        if ( !$isSVG && self::reencodeEnabled() )
        {
            // what is stored is what GD decoded and wrote anew, never the bytes the browser sent: no appended payload,
            // no metadata, no second format hidden in the file
            $upload['path'] = self::reencodeRaster( $path, $mime );
            $upload['temporary'] = true;
        }
        return $upload;
    }

    /**
     * Removes the temporary file checkUpload() made (the re-encoded image), once it is stored or refused.
     *
     * @param array|null $upload what checkUpload() returned
     */
    public static function discard( $upload )
    {
        if ( is_array( $upload ) && !empty( $upload['temporary'] ) && isset( $upload['path'] ) &&
             strpos( (string)$upload['path'], self::temporaryDirectory() . '/' ) === 0 )
        {
            @unlink( $upload['path'] );
        }
    }

    /**
     * Whether a raster image is re-encoded on the server before it is stored: expimageeditor.ini [EditorSettings]
     * ReEncodeRaster (enabled unless disabled).
     *
     * @return bool
     */
    public static function reencodeEnabled()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        return !$ini->hasVariable( 'EditorSettings', 'ReEncodeRaster' ) || $ini->variable( 'EditorSettings', 'ReEncodeRaster' ) !== 'disabled';
    }

    /**
     * The raster image at $path decoded by GD and written anew, as a file in temporaryDirectory() with a random name,
     * in the same type, with transparency kept (PNG, WebP) and expimageeditor.ini Quality (JPEG, WebP). Refused above
     * expimageeditor.ini MaxPixels, before it is decoded.
     *
     * @param string $path
     * @param string $mime image/jpeg, image/png, image/webp or image/gif
     * @return string the path of the new file (the caller removes it with discard())
     * @throws expImageEditorException
     */
    public static function reencodeRaster( $path, $mime )
    {
        $codecs = array( 'image/jpeg' => array( 'imagecreatefromjpeg', 'imagejpeg' ),
                         'image/png' => array( 'imagecreatefrompng', 'imagepng' ),
                         'image/webp' => array( 'imagecreatefromwebp', 'imagewebp' ),
                         'image/gif' => array( 'imagecreatefromgif', 'imagegif' ) );
        if ( !isset( $codecs[$mime] ) || !function_exists( $codecs[$mime][0] ) || !function_exists( $codecs[$mime][1] ) )
            throw new expImageEditorException( 415, 'The server cannot re-encode images of this type.' );

        $info = @getimagesize( $path );
        if ( !is_array( $info ) || empty( $info[0] ) || empty( $info[1] ) )
            throw new expImageEditorException( 415, 'The edited image could not be read.' );
        $maxPixels = self::maxPixels();
        if ( $maxPixels > 0 && (float)$info[0] * (float)$info[1] > $maxPixels )
            throw new expImageEditorException( 413, 'The edited image has more pixels than the editor stores.' );

        $image = @call_user_func( $codecs[$mime][0], $path );
        if ( !$image )
            throw new expImageEditorException( 415, 'The edited image could not be read.' );
        if ( $mime === 'image/png' || $mime === 'image/webp' )
        {
            imagealphablending( $image, false );
            imagesavealpha( $image, true );
        }

        $dir = self::temporaryDirectory();
        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) && !is_dir( $dir ) )
            throw new expImageEditorException( 500, 'The edited image could not be stored.' );
        self::removeStaleTemporaryFiles( $dir );
        $target = $dir . '/' . bin2hex( random_bytes( 16 ) ) . '.' . self::EXTENSIONS[$mime];

        $quality = self::quality();
        switch ( $mime )
        {
            case 'image/jpeg': $written = imagejpeg( $image, $target, (int)round( $quality * 100 ) ); break;
            case 'image/webp': $written = imagewebp( $image, $target, (int)round( $quality * 100 ) ); break;
            case 'image/png': $written = imagepng( $image, $target, 6 ); break;
            default: $written = imagegif( $image, $target );
        }
        if ( PHP_VERSION_ID < 80000 )
            imagedestroy( $image );
        unset( $image );
        clearstatcache( true, $target );
        if ( !$written || !is_file( $target ) || !filesize( $target ) )
        {
            @unlink( $target );
            throw new expImageEditorException( 500, 'The edited image could not be stored.' );
        }
        return $target;
    }

    /**
     * Where the re-encoded images wait until they are stored: <cache directory>/expimageeditor.
     *
     * @return string
     */
    public static function temporaryDirectory()
    {
        return rtrim( eZSys::cacheDirectory(), '/' ) . '/expimageeditor';
    }

    /**
     * Removes the files an interrupted request left in $dir (older than an hour). Under Velocity a worker remembers
     * file answers within a request, so the stat cache is cleared first.
     *
     * @param string $dir
     */
    public static function removeStaleTemporaryFiles( $dir )
    {
        clearstatcache();
        $limit = time() - 3600;
        // no GLOB_BRACE: it is missing on some C libraries (musl)
        foreach ( (array)glob( $dir . '/*', GLOB_NOSORT ) as $file )
        {
            if ( is_string( $file ) && preg_match( '#/[0-9a-f]{32}\.(?:jpg|png|webp|gif)$#', $file ) && @filemtime( $file ) < $limit )
                @unlink( $file );
        }
    }

    /**
     * The largest image the editor stores, in pixels: expimageeditor.ini [EditorSettings] MaxPixels (0: no limit of its
     * own; image.ini MaxImagePixels still applies).
     *
     * @return int
     */
    public static function maxPixels()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        return $ini->hasVariable( 'EditorSettings', 'MaxPixels' ) ? max( 0, (int)$ini->variable( 'EditorSettings', 'MaxPixels' ) ) : 25000000;
    }

    /**
     * The largest file the editor stores, in bytes: expimageeditor.ini [EditorSettings] MaxFileSize in megabytes (0: no
     * limit of its own; the class attribute's maximum file size and PHP's upload limit still apply).
     *
     * @return int
     */
    public static function editorMaxBytes()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        $megabytes = $ini->hasVariable( 'EditorSettings', 'MaxFileSize' ) ? (float)$ini->variable( 'EditorSettings', 'MaxFileSize' ) : 20.0;
        return $megabytes > 0 ? (int)( $megabytes * 1024 * 1024 ) : 0;
    }

    /**
     * expimageeditor.ini [EditorSettings] Quality, 0.1 to 1 (0.92 when it is not a number in that range).
     *
     * @return float
     */
    public static function quality()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        $quality = $ini->hasVariable( 'EditorSettings', 'Quality' ) ? (float)$ini->variable( 'EditorSettings', 'Quality' ) : 0.92;
        return $quality >= 0.1 && $quality <= 1 ? $quality : 0.92;
    }

    /**
     * Stores the checked image as the image of the attribute (its draft), keeping the alternative text.
     *
     * @param eZContentObjectAttribute $attribute
     * @param array $upload what checkUpload() returned
     * @throws expImageEditorException
     */
    public static function store( $attribute, array $upload )
    {
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            throw new expImageEditorException( 404, 'The attribute has no image to edit.' );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $stored = $handler->initializeFromFile( $upload['path'], $handler->attribute( 'alternative_text' ), $upload['name'] );
            // initializeFromFile() does not report a failed copy: the new original must be there before it is recorded
            $original = $stored === false ? null : $handler->attribute( 'original' );
            // the file was just written: under Velocity a worker remembers an earlier "not there" within the request
            if ( is_array( $original ) && !empty( $original['url'] ) )
                clearstatcache( true, $original['url'] );
            if ( !is_array( $original ) || empty( $original['is_valid'] ) || empty( $original['url'] ) ||
                 !eZClusterFileHandler::instance( $original['url'] )->exists() && !is_file( $original['url'] ) )
            {
                throw new expImageEditorException( 500, 'The edited image could not be stored.' );
            }
            $handler->store( $attribute );
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            eZDebug::writeError( 'expimageeditor: storing attribute ' . $attribute->attribute( 'id' ) . ' failed: ' . $e->getMessage(), __METHOD__ );
            throw $e instanceof expImageEditorException ? $e : new expImageEditorException( 500, 'The edited image could not be stored.' );
        }

        eZContentCacheManager::clearObjectViewCacheIfNeeded( $attribute->attribute( 'contentobject_id' ) );
    }

    /**
     * The name the edited image is stored under: the stored image's name with the extension of the new type.
     *
     * @param string $originalName
     * @param string $mime
     * @return string
     */
    public static function targetName( $originalName, $mime )
    {
        $base = pathinfo( basename( str_replace( '\\', '/', (string)$originalName ) ), PATHINFO_FILENAME );
        $base = trim( preg_replace( '/[\x00-\x1f\x7f\/:*?"<>|]+/', '', $base ) );
        if ( $base === '' )
            $base = 'image';
        return $base . '.' . self::EXTENSIONS[$mime];
    }

    /**
     * The types the editor may store: expimageeditor.ini [EditorSettings] OutputMIMETypes[].
     *
     * @return string[]
     */
    public static function outputMIMETypes()
    {
        $ini = eZINI::instance( 'expimageeditor.ini' );
        $types = $ini->hasVariable( 'EditorSettings', 'OutputMIMETypes' ) ? $ini->variable( 'EditorSettings', 'OutputMIMETypes' ) : array();
        return array_values( array_filter( array_map( 'strtolower', array_map( 'trim', (array)$types ) ) ) );
    }

    /**
     * The largest file the attribute takes, in bytes: the class attribute's maximum file size (0: no limit).
     *
     * @param eZContentObjectAttribute $attribute
     * @return int
     */
    public static function maxBytes( $attribute )
    {
        $classAttribute = $attribute->attribute( 'contentclass_attribute' );
        $megabytes = $classAttribute ? (int)$classAttribute->attribute( 'data_int1' ) : 0;
        return $megabytes > 0 ? $megabytes * 1024 * 1024 : 0;
    }

    /**
     * The type of a raster image by its content.
     *
     * @param string $path
     * @return string
     */
    private static function rasterType( $path )
    {
        $info = @getimagesize( $path );
        return is_array( $info ) && isset( $info['mime'] ) ? strtolower( $info['mime'] ) : '';
    }
}
