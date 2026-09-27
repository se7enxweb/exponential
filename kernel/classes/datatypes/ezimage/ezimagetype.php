<?php
/**
 * File containing the eZImageType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZImageType ezimagetype.php
  \ingroup eZDatatype
  \brief The class eZImageType handles image accounts and association with content objects

  \note The method initializeObjectAttribute was removed in 3.8, the new
        storage technique removes the need to have it.
*/

class eZImageType extends eZDataType
{
    const FILESIZE_FIELD = 'data_int1';
    const FILESIZE_VARIABLE = '_ezimage_max_filesize_';
    const DATA_TYPE_STRING = "ezimage";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Image", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
        $this->FileExtensionBlackListValidator = new eZFileExtensionBlackListValidator();
    }

    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /*!
     The object is being moved to trash, do any necessary changes to the attribute.
     Rename file and update db row with new name, so that access to the file using old links no longer works.
    */
    function trashStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $imageHandler = $contentObjectAttribute->attribute( "content" );
        $originalAlias = $imageHandler->imageAlias( "original" );

        // check if there is an actual image, 'is_valid' says if there is an image or not
        // (no original at all: XML without an <ezimage> element)
        if ( !is_array( $originalAlias ) || ( $originalAlias['is_valid'] != '1' && empty( $originalAlias['filename'] ) ) )
        {
            return;
        }

        $basenameHashed = md5( $originalAlias["basename"] );
        $trashedFolder = "{$originalAlias["dirpath"]}/trashed";
        $imageHandler->updateAliasPath( $trashedFolder, $basenameHashed );
        if ( $imageHandler->isStorageRequired() )
        {
            $imageHandler->store( $contentObjectAttribute );
            $contentObjectAttribute->store();
        }

        // Now clean all other aliases, not cleanly registered within the attribute content
        // First get all remaining aliases full path to then safely move them to the trashed folder
        ezpEvent::getInstance()->notify( 'image/trashAliases', array( $originalAlias['url'] ) );
        $aliasNames = array_keys( $imageHandler->aliasList() );
        $aliasesPath = array();
        foreach ( $aliasNames as $aliasName )
        {
            if ( $aliasName === "original" )
            {
                continue;
            }

            $aliasesPath[] = "{$originalAlias["dirpath"]}/{$originalAlias["basename"]}_{$aliasName}.{$originalAlias["suffix"]}";
        }

        if( empty( $aliasesPath ) )
        {
            return;
        }
        $conds = array(
            "contentobject_attribute_id" => $contentObjectAttribute->attribute( "id" ),
            "filepath"                   => array( $aliasesPath )
        );
        $remainingAliases = eZPersistentObject::fetchObjectList(
            eZImageFile::definition(), null,
            $conds
        );
        unset( $conds, $remainingAliasesPath );

        if ( !empty( $remainingAliases ) )
        {
            foreach ( $remainingAliases as $remainingAlias )
            {
                $filename = basename( $remainingAlias->attribute( "filepath" ) );
                $newFilePath = $trashedFolder . "/" . $basenameHashed . substr( $filename, strrpos( $filename, '_' ) );
                eZClusterFileHandler::instance( $remainingAlias->attribute( "filepath" ) )->move( $newFilePath );

                // $newFilePath might have already been processed in eZImageFile
                // If so, $remainingAlias is a duplicate. We can then remove it safely
                $imageFile = eZImageFile::fetchByFilepath( false, $newFilePath, false );
                if ( empty( $imageFile ) )
                {
                    $remainingAlias->setAttribute( "filepath", $newFilePath );
                    $remainingAlias->store();
                }
                else
                {
                    $remainingAlias->remove();
                }
            }
        }
    }

    public function restoreTrashedObjectAttribute( $contentObjectAttribute )
    {
        $imageHandler = $contentObjectAttribute->attribute( "content" );
        $originalAlias = $imageHandler->imageAlias( "original" );
        if ( !is_array( $originalAlias ) )
            return;
        $originalPath = str_replace( "/trashed", "", $originalAlias["dirpath"]);
        $originalName = $imageHandler->imageName( $contentObjectAttribute, $contentObjectAttribute->objectVersion() );
        $imageHandler->updateAliasPath( $originalPath, $originalName );

        if ( $imageHandler->isStorageRequired() )
        {
            $imageHandler->store( $contentObjectAttribute );
            $contentObjectAttribute->store();
        }

        // Now clean all other aliases, not cleanly registered within the attribute content
        // First get all remaining aliases full path to then safely remove them
        $aliasNames = array_keys( $imageHandler->aliasList() );
        $aliasesPath = array();
        foreach ( $aliasNames as $aliasName )
        {
            if ( $aliasName === "original" )
            {
                continue;
            }

            $aliasesPath[] = "{$originalAlias["dirpath"]}/{$originalAlias["basename"]}_{$aliasName}.{$originalAlias["suffix"]}";
        }

        if( empty( $aliasesPath ) )
        {
            return;
        }
        $conds = array(
        	"contentobject_attribute_id" => $contentObjectAttribute->attribute( "id" ),
            "filepath"                   => array( $aliasesPath )
        );
        $remainingAliases = eZPersistentObject::fetchObjectList(
            eZImageFile::definition(), null,
            $conds
        );
        unset( $conds, $remainingAliasesPath );

        if ( !empty( $remainingAliases ) )
        {
            foreach ( $remainingAliases as $remainingAlias )
            {
                $filename = basename( $remainingAlias->attribute( "filepath" ) );
                $newFilePath = $originalPath . "/" . $originalName . substr( $filename, strrpos( $filename, '_' ) );
                eZClusterFileHandler::instance( $remainingAlias->attribute( "filepath" ) )->move( $newFilePath );

                // $newFilePath might have already been processed in eZImageFile
                // If so, $remainingAlias is a duplicate. We can then remove it safely
                $imageFile = eZImageFile::fetchByFilepath( false, $newFilePath, false );
                if ( empty( $imageFile ) )
                {
                    $remainingAlias->setAttribute( "filepath", $newFilePath );
                    $remainingAlias->store();
                }
                else
                {
                    $remainingAlias->remove();
                }
            }
        }
    }

    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        /** @var eZImageAliasHandler $imageHandler */
        $imageHandler = $contentObjectAttribute->attribute( 'content' );
        if ( $imageHandler )
        {
            $imageHandler->setAttribute( 'alternative_text', false );
            $imageHandler->removeAliases();
            $imageHandler->store( $contentObjectAttribute );
            $contentObjectAttribute->setContent( null );
        }
    }

    /**
     * Validate the object attribute input in http. If there is validation failure, there failure message will be put into $contentObjectAttribute->ValidationError
     * @param $http: http object
     * @param $base:
     * @param $contentObjectAttribute: content object attribute being validated
     * @return validation result- eZInputValidator::STATE_INVALID or eZInputValidator::STATE_ACCEPTED
     *
     * @see kernel/classes/eZDataType#validateObjectAttributeHTTPInput($http, $base, $objectAttribute)
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $httpFileName = $base . "_data_imagename_" . $contentObjectAttribute->attribute( "id" );
        $maxSize = 1024 * 1024 * $classAttribute->attribute( self::FILESIZE_FIELD );
        $mustUpload = false;

        $tmpImgObj = $contentObjectAttribute->attribute( 'content' );
        $original = $tmpImgObj->attribute( 'original' );
        if( $contentObjectAttribute->validateIsRequired() )
        {
            if ( !$original['is_valid'] )
            {
                $mustUpload = true;
            }
        }

        $extensionsBlackList = implode(', ', $this->FileExtensionBlackListValidator->extensionsBlackList() );
        $state = $this->FileExtensionBlackListValidator->validate( $original['filename'] );
        if ( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                "A valid file is required. The following file extensions are blacklisted: $extensionsBlackList" ) );
            return eZInputValidator::STATE_INVALID;
        }

        // A file field posted as name[] (or a forged request) gives arrays in
        // $_FILES: that is no image upload, and every check below expects strings
        if ( isset( $_FILES[$httpFileName] ) &&
             ( !is_array( $_FILES[$httpFileName] ) || !isset( $_FILES[$httpFileName]['tmp_name'], $_FILES[$httpFileName]['name'] ) ||
               !is_string( $_FILES[$httpFileName]['tmp_name'] ) || !is_string( $_FILES[$httpFileName]['name'] ) ||
               ( isset( $_FILES[$httpFileName]['error'] ) && !is_scalar( $_FILES[$httpFileName]['error'] ) ) ||
               ( isset( $_FILES[$httpFileName]['size'] ) && !is_scalar( $_FILES[$httpFileName]['size'] ) ) ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'A valid image file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        $canFetchResult = eZHTTPFile::canFetch( $httpFileName, $maxSize );
        // A partial upload, a missing temporary directory or a failed write is
        // an upload that did not work, not "no file"
        if ( in_array( $canFetchResult, array( eZHTTPFile::UPLOADEDFILE_UNKNOWN_ERROR, eZHTTPFile::UPLOADEDFILE_MISSING_TMP_DIR, eZHTTPFile::UPLOADEDFILE_CANT_WRITE ), true ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The image could not be uploaded. Please try again or contact the site administrator.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( isset( $_FILES[$httpFileName] ) and  $_FILES[$httpFileName]["tmp_name"] != "" )
        {
             $imagefile = $_FILES[$httpFileName]['tmp_name'];
             if ( !$_FILES[$httpFileName]["size"] )
             {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                     'The image file must have non-zero size.' ) );
                return eZInputValidator::STATE_INVALID;
             }

             $state = $this->FileExtensionBlackListValidator->validate( $_FILES[$httpFileName]['name'] );
             if ( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
             {
                 $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                     "A valid file is required. The following file extensions are on the blacklist: $extensionsBlackList" ) );
                 return eZInputValidator::STATE_INVALID;
             }

             if ( !self::validateImageFileExtension( $_FILES[$httpFileName]['name'] ) )
             {
                 $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                           'A valid image file is required.' ) );
                 return eZInputValidator::STATE_INVALID;
             }

             // The name says image; the content must say so as well
             $imageError = self::imageFileError( $imagefile );
             if ( $imageError !== false )
             {
                 $contentObjectAttribute->setValidationError( $imageError );
                 return eZInputValidator::STATE_INVALID;
             }
        }
        if ( $mustUpload && $canFetchResult == eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'A valid image file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult == eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The size of the uploaded image exceeds limit set by upload_max_filesize directive in php.ini. Please contact the site administrator.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult == eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The size of the uploaded file exceeds the limit set for this site: %1 bytes.' ), $maxSize );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    private static function validateImageFileExtension($filename)
    {
        if ( !is_string( $filename ) || $filename === '' )
            return false;
        $mimeType = eZMimeType::findByURL( $filename );
        $nameMimeType = isset( $mimeType['name'] ) ? (string)$mimeType['name'] : '';
        $nameMimeTypes = explode('/', $nameMimeType);

        // SVG is an image by name, but a document a browser runs script from,
        // served from the storage directory under the site's own origin
        return $nameMimeTypes[0] === 'image' && $nameMimeType !== 'image/svg+xml';
    }

    /**
     * Why the file at $filePath is not an image this datatype stores, as a
     * translated validation message, or false when it is one.
     *
     * The content decides, not the name: it must be a raster image PHP can read
     * the size of (so no SVG, HTML, script or archive named .jpg), must not
     * start with markup a browser could sniff as HTML, and must not have more
     * pixels than [ImageSettings] MaxImagePixels in image.ini (default 100
     * million), which the image converters would need all at once in memory.
     *
     * @param string $filePath
     * @return string|false
     */
    static function imageFileError( $filePath )
    {
        $invalid = ezpI18n::tr( 'kernel/classes/datatypes', 'A valid image file is required.' );
        if ( !is_string( $filePath ) || $filePath === '' || strpos( $filePath, "\0" ) !== false || !is_file( $filePath ) )
            return $invalid;

        $info = @getimagesize( $filePath );
        if ( !is_array( $info ) || empty( $info[0] ) || empty( $info[1] ) || !isset( $info['mime'] ) ||
             strpos( $info['mime'], 'image/' ) !== 0 || $info['mime'] === 'image/svg+xml' )
            return $invalid;

        $head = (string)@file_get_contents( $filePath, false, null, 0, 256 );
        if ( preg_match( '/<(?:!doctype|html|head|body|script|svg|iframe|\?php)/i', $head ) )
            return $invalid;

        $maxPixels = 100000000;
        $ini = eZINI::instance( 'image.ini' );
        if ( $ini->hasVariable( 'ImageSettings', 'MaxImagePixels' ) && (int)$ini->variable( 'ImageSettings', 'MaxImagePixels' ) > 0 )
            $maxPixels = (int)$ini->variable( 'ImageSettings', 'MaxImagePixels' );
        if ( (float)$info[0] * (float)$info[1] > $maxPixels )
        {
            return ezpI18n::tr( 'kernel/classes/datatypes', 'The image is too large: %1 x %2 pixels.', null,
                                array( '%1' => $info[0], '%2' => $info[1] ) );
        }
        return false;
    }

    /**
     * True if $filePath, a path given to fromString() (package or CSV import),
     * is a file inside the installation, its var directory or the temporary
     * directory. A path through "..", an absolute path elsewhere, a URL or a
     * stream wrapper is not read.
     */
    static function isImportablePath( $filePath )
    {
        if ( !is_string( $filePath ) || $filePath === '' || strpos( $filePath, "\0" ) !== false || preg_match( '#^[a-z][a-z0-9+.-]*://#i', $filePath ) )
            return false;
        $real = realpath( $filePath );
        if ( $real === false || !is_file( $real ) )
            return false;
        foreach ( array( eZSys::rootDir(), eZSys::varDirectory(), sys_get_temp_dir() ) as $dir )
        {
            $dir = $dir ? realpath( $dir ) : false;
            if ( $dir && strpos( $real, rtrim( $dir, '/' ) . '/' ) === 0 )
                return true;
        }
        return false;
    }

    /**
     * $text as alternative text the XML storage can hold: a string, without
     * the control characters XML 1.0 does not allow (they were written as
     * character references, and the stored image XML then no longer parsed).
     */
    static function cleanAlternativeText( $text )
    {
        if ( $text === false || $text === null )
            return $text;
        if ( !is_scalar( $text ) )
            return '';
        return preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string)$text );
    }

    /**
     * Fetch object attribute http input, override the ezDataType method
     * This method is triggered when submiting a http form which includes Image class
     * Image is stored into file system every time there is a file input and validation result is valid.
     * @param $http http object
     * @param $base
     * @param $contentObjectAttribute : the content object attribute being handled
     * @return true if content object is not null, false if content object is null
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $result = false;
        $imageAltText = false;
        $hasImageAltText = false;
        if ( $http->hasPostVariable( $base . "_data_imagealttext_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            // Text only: an array became "Array" or a TypeError in the XML
            $imageAltText = self::cleanAlternativeText( $http->postVariable( $base . "_data_imagealttext_" . $contentObjectAttribute->attribute( "id" ) ) );
            $hasImageAltText = true;
        }

        $content = $contentObjectAttribute->attribute( 'content' );
        $httpFileName = $base . "_data_imagename_" . $contentObjectAttribute->attribute( "id" );

        if ( eZHTTPFile::canFetch( $httpFileName ) )
        {
            $httpFile = eZHTTPFile::fetch( $httpFileName );
            if ( $httpFile )
            {
                if ( $content )
                {
                    $content->setHTTPFile( $httpFile );
                    $result = true;
                }
            }

        }

        if ( $content )
        {
            if ( $hasImageAltText )
                $content->setAttribute( 'alternative_text', $imageAltText );
            $result = true;
        }

        return $result;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $imageHandler = $contentObjectAttribute->attribute( 'content' );
        if ( $imageHandler )
        {
            $httpFile = $imageHandler->httpFile( true );
            // Validation checked the upload; checked again here for callers that
            // store without validating, since this is where the file is kept
            if ( $httpFile && self::validateImageFileExtension( $httpFile->attribute( 'original_filename' ) ) &&
                 self::imageFileError( $httpFile->attribute( 'filename' ) ) === false )
            {
                $imageAltText = $imageHandler->attribute( 'alternative_text' );

                $imageHandler->initializeFromHTTPFile( $httpFile, $imageAltText );
            }
            if ( $imageHandler->isStorageRequired() )
            {
                $imageHandler->store( $contentObjectAttribute );
            }
        }
    }

    /*!
     HTTP file insertion is supported.
    */
    function isHTTPFileInsertionSupported()
    {
        return true;
    }

    /*!
     Regular file insertion is supported.
    */
    function isRegularFileInsertionSupported()
    {
        return true;
    }

    /*!
     Inserts the file using the Image Handler eZImageAliasHandler.
    */
    function insertHTTPFile( $object, $objectVersion, $objectLanguage,
                             $objectAttribute, $httpFile, $mimeData,
                             &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );

        $handler = $objectAttribute->content();
        if ( !$handler )
        {
            $result['errors'][] = array( 'description' => ezpI18n::tr( 'kernel/classes/datatypes/ezimage',
                                                                  'Failed to fetch Image Handler. Please contact the site administrator.' ) );
            return false;
        }

        // The upload module and the WebDAV/REST inserts skip the edit form's
        // validation: the same content check applies here
        $imageError = self::validateImageFileExtension( $httpFile->attribute( 'original_filename' ) ) ?
                      self::imageFileError( $httpFile->attribute( 'filename' ) ) :
                      ezpI18n::tr( 'kernel/classes/datatypes', 'A valid image file is required.' );
        if ( $imageError !== false )
        {
            $result['errors'][] = array( 'description' => $imageError );
            return false;
        }

        $status = $handler->initializeFromHTTPFile( $httpFile );
        $result['require_storage'] = $handler->isStorageRequired();
        return $status;
    }

    /*!
     Inserts the file using the Image Handler eZImageAliasHandler.
    */
    function insertRegularFile( $object, $objectVersion, $objectLanguage,
                                $objectAttribute, $filePath,
                                &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );

        $handler = $objectAttribute->content();
        if ( !$handler )
        {
            $result['errors'][] = array( 'description' => ezpI18n::tr( 'kernel/classes/datatypes/ezimage',
                                                                  'Failed to fetch Image Handler. Please contact the site administrator.' ) );
            return false;
        }

        $imageError = self::imageFileError( $filePath );
        if ( $imageError !== false )
        {
            $result['errors'][] = array( 'description' => $imageError );
            return false;
        }

        $status = $handler->initializeFromFile( $filePath, false, basename( $filePath ) );
        $result['require_storage'] = $handler->isStorageRequired();
        return $status;
    }

    /*!
      We support file information
    */
    function hasStoredFileInformation( $object, $objectVersion, $objectLanguage,
                                       $objectAttribute )
    {
        return true;
    }

    /*!
      Extracts file information for the image entry.
    */
    function storedFileInformation( $object, $objectVersion, $objectLanguage,
                                    $objectAttribute )
    {
        $content = $objectAttribute->content();
        if ( $content )
        {
            $original = $content->attribute( 'original' );
            $fileName = $original['filename'];
            $filePath = $original['full_path'];
            $mimeType = $original['mime_type'];
            $originalFileName = $original['original_filename'];

            return array( 'filename' => $fileName,
                          'original_filename' => $originalFileName,
                          'filepath' => $filePath,
                          'mime_type' => $mimeType );
        }
        return false;
    }

    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
        $hasContent = $contentObjectAttribute->hasContent();
        if ( $hasContent )
        {
            /** @var eZImageAliasHandler $imageHandler */
            $imageHandler = $contentObjectAttribute->attribute( 'content' );
            $mainNode = false;
            foreach ( array_keys( $publishedNodes ) as $publishedNodeKey )
            {
                $publishedNode = $publishedNodes[$publishedNodeKey];
                if ( $publishedNode->attribute( 'is_main' ) )
                {
                    $mainNode = $publishedNode;
                    break;
                }
            }
            if ( $mainNode )
            {
                $dirpath = $imageHandler->imagePathByNode( $contentObjectAttribute, $mainNode );
                $oldDirpath = $imageHandler->directoryPath();
                if ( $oldDirpath != $dirpath )
                {
                    $name = $imageHandler->imageNameByNode( $contentObjectAttribute, $mainNode );
                    $imageHandler->updateAliasPath( $dirpath, $name );
                }
            }
            if ( $imageHandler->isStorageRequired() )
            {
                $imageHandler->store( $contentObjectAttribute );
                $contentObjectAttribute->store();
            }
        }
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $filesizeName = $base . self::FILESIZE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $filesizeName ) )
        {
            // Megabytes, a whole number that is not negative (0 is no limit)
            $filesizeValue = $http->postVariable( $filesizeName );
            $filesizeValue = is_scalar( $filesizeValue ) && is_numeric( $filesizeValue ) ? max( 0, (int)$filesizeValue ) : 0;
            $classAttribute->setAttribute( self::FILESIZE_FIELD, $filesizeValue );
            return true;
        }
        return false;
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        if( $action == "delete_image" )
        {
            $this->deleteStoredObjectAttribute( $contentObjectAttribute );
        }
    }

    /*!
     Will return one of the following items from the original alias.
     - alternative_text - If it's not empty
     - Default paramater in \a $name if it exists
     - original_filename, this is the default fallback.
    */
    function title( $contentObjectAttribute, $name = 'original_filename' )
    {
        $content = $contentObjectAttribute->content();
        // XML without an <ezimage> element has no original alias
        $original = $content ? $content->attribute( 'original' ) : null;
        if ( !is_array( $original ) )
            return '';
        $value = $original['alternative_text'];
        if ( trim( (string)$value ) == '' )
        {
            if ( array_key_exists( $name, $original ) )
                $value = $original[$name];
            else
                $value = $original['original_filename'];
        }

        return $value;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $handler = $contentObjectAttribute->content();
        if ( !$handler )
            return false;
        return $handler->attribute( 'is_valid' );
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $imageHandler = new eZImageAliasHandler( $contentObjectAttribute );

        return $imageHandler;
    }

    function metaData( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $original = $content ? $content->attribute( 'original' ) : null;
        $value = is_array( $original ) ? $original['alternative_text'] : '';
        return $value;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $maxSize = $classAttribute->attribute( self::FILESIZE_FIELD );
        $dom = $attributeParametersNode->ownerDocument;

        $maxSizeNode = $dom->createElement( 'max-size' );
        $maxSizeNode->appendChild( $dom->createTextNode( $maxSize ) );
        $maxSizeNode->setAttribute( 'unit-size', 'mega' );
        $attributeParametersNode->appendChild( $maxSizeNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package without <max-size> gets no limit (0), as a new class attribute has
        $sizeNode = $attributeParametersNode->getElementsByTagName( 'max-size' )->item( 0 );
        $maxSize = $sizeNode ? $sizeNode->textContent : 0;
        $classAttribute->setAttribute( self::FILESIZE_FIELD, $maxSize );
    }


    /*!
     \return a DOM representation of the content object attribute
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $content = $objectAttribute->content();
        $original = $content ? $content->attribute( 'original' ) : null;
        if ( !is_array( $original ) )
            $original = array( 'url' => false, 'alternative_text' => '' );

        if ( $original['url'] )
        {
            $imageKey = md5( mt_rand() );

            $package->appendSimpleFile( $imageKey, $original['url'] );
            $node->setAttribute( 'image-file-key', $imageKey );
        }

        $node->setAttribute( 'alternative-text', $original['alternative_text'] );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        // Remove all existing image data for the case this is a translated attribute,
        // so initial language's image alias will not be removed in 'initializeFromFile'
        $objectAttribute->setAttribute( 'data_text', '' );

        $alternativeText = $attributeNode->getAttribute( 'alternative-text' );
        // Backwards compatibility with older node name
        if ( $alternativeText === false )
            $alternativeText = $attributeNode->getAttribute( 'alternativ-text' );
        $content = $objectAttribute->attribute( 'content' );
        $imageFileKey = $attributeNode->getAttribute( 'image-file-key' );
        if ( $imageFileKey )
        {
            $content->initializeFromFile( $package->simpleFilePath( $imageFileKey ), $alternativeText );
        }
        else
        {
            $content->setAttribute( 'alternative_text', $alternativeText );
        }
        $content->store( $objectAttribute );
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $objectAttribute )
    {
        $content = $objectAttribute->content();
        $original = $content ? $content->attribute( 'original' ) : null;
        $alternativeText = $content ? $content->attribute( 'alternative_text' ) : '';
        return ( is_array( $original ) ? $original['url'] : '' ) . '|' . $alternativeText;
    }

    /**
     * "path|alternative text" or just "path", as toString() gives it. The path
     * must be a file inside the installation, its var directory or the
     * temporary directory (no "..", other absolute paths, URLs or stream
     * wrappers) and an image by its content; otherwise nothing changes and
     * false is returned. An empty path keeps the image and sets the text only.
     */
    function fromString( $objectAttribute, $string )
    {
        $string = is_scalar( $string ) ? (string)$string : '';
        $delimiterPos = strpos( $string, '|' );
        $path = $delimiterPos === false ? $string : substr( $string, 0, $delimiterPos );
        $alternativeText = $delimiterPos === false ? null : substr( $string, $delimiterPos + 1 );

        if ( $path !== '' )
        {
            if ( !self::isImportablePath( $path ) )
            {
                eZDebug::writeError( "The image file is not a file inside the installation, its var or temporary directory: $path", __METHOD__ );
                return false;
            }
            $imageError = self::imageFileError( $path );
            if ( $imageError !== false )
            {
                eZDebug::writeError( "The file is not an image that can be stored: $path", __METHOD__ );
                return false;
            }
        }

        /** @var eZImageAliasHandler $content */
        $content = $objectAttribute->attribute( 'content' );
        if ( $path !== '' )
        {
            $content->initializeFromFile( $path, '' );
        }
        if ( $alternativeText !== null )
        {
            $content->setAttribute( 'alternative_text', $alternativeText );
        }
        $content->store( $objectAttribute );
        return true;
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /**
     * Iterates over images referenced in data_text, and adds eZImageFile references
     * @param eZContentObjectAttribute $objectAttribute
     */
    function postStore( $objectAttribute )
    {
        $objectAttributeId = $objectAttribute->attribute( "id" );

        // Empty or broken XML references no files, and is no warning
        $dataText = $objectAttribute->attribute( "data_text" );
        if ( !is_string( $dataText ) || trim( $dataText ) === '' )
            return;
        $useErrors = libxml_use_internal_errors( true );
        $doc = simplexml_load_string( $dataText );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( $doc === false )
            return;

        // Creates ezimagefile entries
        foreach ( $doc->xpath( "//*/@url" ) as $url )
        {
            $url = (string)$url;

            if ( $url === "" )
                continue;

            eZImageFile::appendFilepath( $objectAttributeId, $url, true );
        }
    }

    /// \privatesection
    /// The file extension blacklist validator
    private $FileExtensionBlackListValidator;
}

eZDataType::register( eZImageType::DATA_TYPE_STRING, "eZImageType" );

?>
