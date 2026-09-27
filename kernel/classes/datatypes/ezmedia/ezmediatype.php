<?php
/**
 * File containing the eZMediaType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMediaType ezmediatype.php
  \ingroup eZDatatype
  \brief The class eZMediaType handles storage and playback of media files.

*/

class eZMediaType extends eZDataType
{
    const DATA_TYPE_STRING = "ezmedia";
    const MAX_FILESIZE_FIELD = 'data_int1';
    const MAX_FILESIZE_VARIABLE = '_ezmedia_max_filesize_';
    const TYPE_FIELD = "data_text1";
    const TYPE_VARIABLE = "_ezmedia_type_";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Media", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
        $this->FileExtensionBlackListValidator = new eZFileExtensionBlackListValidator();
    }

    /*!
     Sets value according to current version
    */
    function postInitializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $contentObjectAttributeID = $originalContentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $oldfile = eZMedia::fetch( $contentObjectAttributeID, $currentVersion );
            if( $oldfile != null )
            {
                $oldfile->setAttribute( 'contentobject_attribute_id', $contentObjectAttribute->attribute( 'id' ) );
                $oldfile->setAttribute( "version",  $version );
                $oldfile->store();
            }
        }
        else
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
            $version = $contentObjectAttribute->attribute( 'version' );

            $media = eZMedia::create( $contentObjectAttributeID, $version );

            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $pluginPage = eZMediaType::pluginPage( $contentClassAttribute->attribute( 'data_text1' ) );

            $media->setAttribute( 'quality', 'high' );
            $media->setAttribute( 'pluginspage', $pluginPage );
            $media->store();
        }
    }

    /*!
     The object is being moved to trash, do any necessary changes to the attribute.
     Rename file and update db row with new name, so that access to the file using old links no longer works.
    */
    function trashStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
        $sys = eZSys::instance();
        $storage_dir = $sys->storageDirectory();

        if ( $version == null )
            $mediaFiles = eZMedia::fetch( $contentObjectAttributeID, null );
        else
            $mediaFiles = array( eZMedia::fetch( $contentObjectAttributeID, $version ) );

        foreach ( (array)$mediaFiles as $mediaFile )
        {
            if ( $mediaFile == null )
                continue;
            $mimeType =  $mediaFile->attribute( "mime_type" );
            $orig_dir = $storage_dir . '/original/' . eZMedia::mimeGroup( $mimeType );
            $fileName = $mediaFile->attribute( "filename" );
            // No file yet, or a stored name with a directory part: nothing of this
            // datatype to move (the latter would rename a file outside the storage)
            if ( !eZMedia::isSafeFileName( $fileName ) )
                continue;

            // Check if there are any other records in ezmedia that point to that fileName.
            $mediaObjectsWithSameFileName = eZMedia::fetchByFileName( $fileName );

            $filePath = $orig_dir . "/" . $fileName;
            $file = eZClusterFileHandler::instance( $filePath );

            if ( $file->exists() and count( (array)$mediaObjectsWithSameFileName ) <= 1 )
            {
                // create dest filename in the same manner as eZHTTPFile::store()
                // grab file's suffix
                $fileSuffix = eZFile::suffix( $fileName );
                // prepend dot
                if ( $fileSuffix )
                    $fileSuffix = '.' . $fileSuffix;
                // grab filename without suffix
                $fileBaseName = basename( $fileName, $fileSuffix );
                // create dest filename
                $newFileName = md5( $fileBaseName . microtime() . mt_rand() ) . $fileSuffix;
                $newFilePath = $orig_dir . "/" . $newFileName;

                // rename the file, and update the database data
                $file->move( $newFilePath );
                $mediaFile->setAttribute( 'filename', $newFileName );
                $mediaFile->store();
            }
        }
    }

    /*!
     Delete stored attribute
    */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
        $sys = eZSys::instance();
        $storage_dir = $sys->storageDirectory();
        if ( $version == null )
        {
            $mediaFiles = eZMedia::fetch( $contentObjectAttributeID, null );
            foreach ( (array)$mediaFiles as $mediaFile )
            {
                $mimeType =  $mediaFile->attribute( "mime_type" );
                // An empty mime type (a media row without a file yet) used to raise
                // an undefined offset warning here
                $orig_dir = $storage_dir . '/original/' . eZMedia::mimeGroup( $mimeType );
                $fileName = $mediaFile->attribute( "filename" );

                // No file, or a stored name with a directory part ("../../x"): not
                // a file this datatype stored, never deleted
                if ( !eZMedia::isSafeFileName( $fileName ) )
                    continue;

                $file = eZClusterFileHandler::instance( $orig_dir . "/" . $fileName );
                if ( $file->exists() )
                    $file->delete();
            }
        }
        else
        {
            $mediaFiles = eZMedia::fetchByContentObjectID( $contentObjectAttribute->attribute( 'contentobject_id' ) );
            $count = 0;
            $currentBinaryFile = eZMedia::fetch( $contentObjectAttributeID, $version );
            if ( $currentBinaryFile != null )
            {
                $mimeType =  $currentBinaryFile->attribute( "mime_type" );
                $currentFileName = $currentBinaryFile->attribute( "filename" );
                $orig_dir = $storage_dir . '/original/' . eZMedia::mimeGroup( $mimeType );
                foreach ( $mediaFiles as $mediaFile )
                {
                    $fileName = $mediaFile->attribute( "filename" );
                    if( $currentFileName == $fileName )
                        $count += 1;
                }
                // A stored name with a directory part is never deleted
                if ( $count == 1 && eZMedia::isSafeFileName( $currentFileName ) )
                {
                    $file = eZClusterFileHandler::instance( $orig_dir . "/" . $currentFileName );
                    if ( $file->exists() )
                        $file->delete();
                }
            }
        }
        eZMedia::removeByID( $contentObjectAttributeID, $version );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $httpFileName = $base . "_data_mediafilename_" . $contentObjectAttribute->attribute( "id" );
        // The limit is a number of megabytes, 0 for none; an empty or broken class
        // value is no limit instead of a non-numeric warning
        $maxSize = 1024 * 1024 * max( 0, (int)$classAttribute->attribute( self::MAX_FILESIZE_FIELD ) );
        $mustUpload = false;

        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $version = $contentObjectAttribute->attribute( 'version' );
        $media = eZMedia::fetch( $contentObjectAttributeID, $version );
        // A form that posts the file field as an array (name[]) is not an upload
        // this datatype can take; it must not reach the validators as an array
        if ( isset( $_FILES[$httpFileName] ) &&
             ( !is_array( $_FILES[$httpFileName] ) ||
               !is_string( $_FILES[$httpFileName]['tmp_name'] ?? '' ) || !is_string( $_FILES[$httpFileName]['name'] ?? '' ) ||
               !is_scalar( $_FILES[$httpFileName]['error'] ?? null ) ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'A valid media file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        $extensionsBlackList = implode(', ', $this->FileExtensionBlackListValidator->extensionsBlackList() );
        if ( $media === null || !$media->attribute( 'filename' ) )
        {
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $mustUpload = true;
            }
        }
        else
        {
            $state = $this->FileExtensionBlackListValidator->validate( $media->attribute( 'filename' ) );
            if ( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                    "A valid file is required. The following file extensions are blacklisted: $extensionsBlackList" ) );
                return eZInputValidator::STATE_INVALID;
            }
        }

        if ( isset( $_FILES[$httpFileName] ) && ( $_FILES[$httpFileName]['tmp_name'] ?? '' ) !== '' )
        {
            $state = $this->FileExtensionBlackListValidator->validate( $_FILES[$httpFileName]['name'] ?? '' );
            if ( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                    "A valid file is required. The following file extensions are blacklisted: $extensionsBlackList" ) );
                return eZInputValidator::STATE_INVALID;
            }
        }

        $canFetchResult = eZHTTPFile::canFetch( $httpFileName, $maxSize );
        if ( $mustUpload && $canFetchResult === eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'A valid media file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult === eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The size of the uploaded file exceeds the limit set by upload_max_filesize directive in php.ini. Please contact the site administrator.') );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult === eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The size of the uploaded file exceeds site maximum: %1 bytes.' ), $maxSize );
            return eZInputValidator::STATE_INVALID;
        }
        // A partial upload, a missing temporary directory, a failed disk write or an
        // upload stopped by a PHP extension used to be accepted here and then dropped
        // without a word by fetchObjectAttributeHTTPInput(): the draft was saved
        // without the file and the editor was not told
        if ( $canFetchResult === eZHTTPFile::UPLOADEDFILE_MISSING_TMP_DIR ||
             $canFetchResult === eZHTTPFile::UPLOADEDFILE_CANT_WRITE ||
             $canFetchResult === eZHTTPFile::UPLOADEDFILE_UNKNOWN_ERROR )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The file could not be uploaded. Please try again or contact the site administrator.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Checks if file uploads are enabled, if not it gives a warning.
    */
    function checkFileUploads()
    {
        $isFileUploadsEnabled = ini_get( 'file_uploads' ) != 0;
        if ( !$isFileUploadsEnabled )
        {
            if ( empty( $GLOBALS['eZMediaTypeWarningAdded'] ) )
            {
                $text = ezpI18n::tr( 'kernel/classes/datatypes',
                                     'File uploading is not enabled. Please contact the site administrator to enable it.' );
                // eZAppendWarningItem() is only loaded by the web front controller;
                // a CLI import reaching this was a fatal "undefined function"
                if ( function_exists( 'eZAppendWarningItem' ) )
                    eZAppendWarningItem( array( 'error' => array( 'type' => 'kernel',
                                                                  'number' => eZError::KERNEL_NOT_AVAILABLE ),
                                                'text' => $text ) );
                else
                    eZDebug::writeWarning( $text, __METHOD__ );
                $GLOBALS['eZMediaTypeWarningAdded'] = true;
            }
        }
    }

    /*!
     \static
     Returns plugin page by media type

    */
    function pluginPage( $mediaType )
    {
        $pluginPage = '';
        switch( $mediaType )
        {
            case 'flash':
                $pluginPage = "http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash";
            break;
            case 'quick_time':
                $pluginPage = "http://quicktime.apple.com";
            break;
            case 'real_player' :
                $pluginPage = "http://www.real.com/";
            break;
            case 'silverlight':
                $pluginPage = "http://go.microsoft.com/fwlink/?LinkID=108182";
            break;
            case 'windows_media_player' :
                $pluginPage = "http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112" ;
            break;
            default:
                $pluginPage = "";
            break;
        }

        return $pluginPage;
    }

    /*!
     Fetches input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {

        eZMediaType::checkFileUploads();

        if ( $this->validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) !== eZInputValidator::STATE_ACCEPTED )
        {
            return false;
        }

        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $player = $classAttribute->attribute( "data_text1" );
        $pluginPage = eZMediaType::pluginPage( $player );

        $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
        $version = $contentObjectAttribute->attribute( "version" );
        // Width and height are integer columns and end up in width="" / height=""
        // of the player markup: only a whole number of pixels is taken, anything
        // else (missing, an array, "100px", "5 onload=...") is 0, "player decides".
        // Quality and controls are free text columns: an array from a crafted form
        // is dropped instead of being stored as "Array" or raising a TypeError.
        $width = self::postedDimension( $http, $base . "_data_media_width_" . $contentObjectAttribute->attribute( "id" ) );
        $height = self::postedDimension( $http, $base . "_data_media_height_" . $contentObjectAttribute->attribute( "id" ) );
        $quality = self::postedText( $http, $base . "_data_media_quality_" . $contentObjectAttribute->attribute( "id" ) );
        $controls = self::postedText( $http, $base . "_data_media_controls_" . $contentObjectAttribute->attribute( "id" ) );

        $media = eZMedia::fetch( $contentObjectAttributeID, $version );
        if ( $media == null )
        {
           $media = eZMedia::create( $contentObjectAttributeID, $version );
        }

        $media->setAttribute( "contentobject_attribute_id", $contentObjectAttributeID );
        $media->setAttribute( "version", $version );
        $media->setAttribute( "width", $width );
        $media->setAttribute( "height", $height );
        $media->setAttribute( "quality", $quality );
        $media->setAttribute( "controls", $controls );
        $media->setAttribute( "pluginspage", $pluginPage );
        if ( $http->hasPostVariable( $base . "_data_media_is_autoplay_" . $contentObjectAttribute->attribute( "id" ) ) )
            $media->setAttribute( "is_autoplay", true );
        else
            $media->setAttribute( "is_autoplay", false );
        if ( $http->hasPostVariable( $base . "_data_media_has_controller_" . $contentObjectAttribute->attribute( "id" ) ) )
            $media->setAttribute( "has_controller", true );
        else
            $media->setAttribute( "has_controller", false );
        if ( $http->hasPostVariable( $base . "_data_media_is_loop_" . $contentObjectAttribute->attribute( "id" ) ) )
            $media->setAttribute( "is_loop", true );
        else
            $media->setAttribute( "is_loop", false );

        $mediaFilePostVarName = $base . "_data_mediafilename_" . $contentObjectAttribute->attribute( "id" );
        if ( eZHTTPFile::canFetch( $mediaFilePostVarName ) )
            $mediaFile = eZHTTPFile::fetch( $mediaFilePostVarName );
        else
            $mediaFile = null;
        if ( $mediaFile instanceof eZHTTPFile )
        {
            $mimeData = eZMimeType::findByFileContents( $mediaFile->attribute( "original_filename" ) );
            $mime = $mimeData['name'];

            if ( $mime == '' )
            {
                $mime = $mediaFile->attribute( "mime_type" );
            }
            $extension = eZFile::suffix( $mediaFile->attribute( "original_filename" ) );
            $mediaFile->setMimeType( $mime );
            if ( !$mediaFile->store( "original", $extension ) )
            {
                eZDebug::writeError( "Failed to store http-file: " . $mediaFile->attribute( "original_filename" ),
                                     "eZMediaType" );
                return false;
            }

            $orig_dir = $mediaFile->storageDir( "original" );
            eZDebug::writeNotice( "dir=$orig_dir" );
            $media->setAttribute( "filename", basename( $mediaFile->attribute( "filename" ) ) );
            $media->setAttribute( "original_filename", $mediaFile->attribute( "original_filename" ) );
            $media->setAttribute( "mime_type", $mime );

            $filePath = $mediaFile->attribute( 'filename' );
            $fileHandler = eZClusterFileHandler::instance();
            $fileHandler->fileStore( $filePath, 'media', true, $mime );
        }
        else if ( $media->attribute( 'filename' ) == '' )
        {
            $media->remove();
            return false;
        }

        $media->store();
        $contentObjectAttribute->setContent( $media );
        return true;
    }

    /*!
     \static
     \return the posted width or height \a $name as a whole number of pixels >= 0,
     0 when it is missing, empty or not a number.
    */
    static function postedDimension( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return 0;
        $value = $http->postVariable( $name );
        if ( !is_scalar( $value ) )
            return 0;
        $value = trim( (string)$value );
        if ( !preg_match( '/^\d{1,9}$/', $value ) )
            return 0;
        return (int)$value;
    }

    /*!
     \static
     \return the posted text \a $name, null when it is missing or not a string.
    */
    static function postedText( $http, $name )
    {
        if ( !$http->hasPostVariable( $name ) )
            return null;
        $value = $http->postVariable( $name );
        return is_scalar( $value ) ? (string)$value : null;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        if ( $action == "delete_media" )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $this->deleteStoredObjectAttribute( $contentObjectAttribute, $version );
            $media = eZMedia::create( $contentObjectAttributeID, $version );
            $contentObjectAttribute->setContent( $media );
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
     Inserts the file using the eZMedia class.
    */
    function insertHTTPFile( $object, $objectVersion, $objectLanguage,
                             $objectAttribute, $httpFile, $mimeData,
                             &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );
        $attributeID = $objectAttribute->attribute( 'id' );

        $media = eZMedia::fetch( $attributeID, $objectVersion );
        if ( $media === null )
            $media = eZMedia::create( $attributeID, $objectVersion );

        $httpFile->setMimeType( $mimeData['name'] );
        if ( !$httpFile->store( "original", false, false ) )
        {
            $result['errors'][] = array( 'description' => ezpI18n::tr( 'kernel/classes/datatypes/ezmedia',
                                                        'Failed to store media file %filename. Please contact the site administrator.', null,
                                                        array( '%filename' => $httpFile->attribute( "original_filename" ) ) ) );
            return false;
        }

        $classAttribute = $objectAttribute->contentClassAttribute();
        $player = $classAttribute->attribute( "data_text1" );
        $pluginPage = eZMediaType::pluginPage( $player );

        $media->setAttribute( "contentobject_attribute_id", $attributeID );
        $media->setAttribute( "version", $objectVersion );
        $media->setAttribute( "filename", basename( $httpFile->attribute( "filename" ) ) );
        $media->setAttribute( "original_filename", $httpFile->attribute( "original_filename" ) );
        $media->setAttribute( "mime_type", $mimeData['name'] );

        // Setting width and height to zero means that the browser/player must find the size itself.
        // In the future we will probably analyze the media file and find this information
        $width = $height = 0;
        // Quality is not known, so we don't set any
        $quality = false;
        // Not sure what this is for, set to false
        $controls = false;
        // We want to show controllers by default
        $hasController = true;
        // Don't play automatically
        $isAutoplay = false;
        // Don't loop movie
        $isLoop = false;

        $media->setAttribute( "width", $width );
        $media->setAttribute( "height", $height );
        $media->setAttribute( "quality", $quality );
        $media->setAttribute( "controls", $controls );
        $media->setAttribute( "pluginspage", $pluginPage );
        $media->setAttribute( "is_autoplay", $isAutoplay );
        $media->setAttribute( "has_controller", $hasController );
        $media->setAttribute( "is_loop", $isLoop );

        $filePath = $httpFile->attribute( 'filename' );
        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $filePath, 'mediafile', true, $mimeData['name'] );


        $media->store();

        $objectAttribute->setContent( $media );
        return true;
    }

    /*!
     Inserts the file using the eZMedia class.
    */
    function insertRegularFile( $object, $objectVersion, $objectLanguage,
                                $objectAttribute, $filePath,
                                &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );
        $attributeID = $objectAttribute->attribute( 'id' );

        $media = eZMedia::fetch( $attributeID, $objectVersion );
        if ( $media === null )
            $media = eZMedia::create( $attributeID, $objectVersion );

        $fileName = basename( $filePath );
        // An import names a file that may not be there (a stale CSV, a typo): that
        // is a failed import, not a copy() warning and an empty stored file
        if ( !is_string( $filePath ) || $filePath === '' || !is_file( $filePath ) || !is_readable( $filePath ) )
        {
            eZDebug::writeError( "The file '$filePath' does not exist, cannot initialize media attribute with it", __METHOD__ );
            return false;
        }
        $mimeData = eZMimeType::findByFileContents( $filePath );
        $storageDir = eZSys::storageDirectory();
        $group = eZMedia::mimeGroup( $mimeData['name'] ?? '' );
        $destination = $storageDir . '/original/' . $group;

        if ( !file_exists( $destination ) )
        {
            if ( !eZDir::mkdir( $destination, false, true ) )
            {
                return false;
            }
        }

        // create dest filename in the same manner as eZHTTPFile::store()
        // grab file's suffix
        $fileSuffix = eZFile::suffix( $fileName );
        // prepend dot
        if( $fileSuffix )
            $fileSuffix = '.' . $fileSuffix;
        // grab filename without suffix
        $fileBaseName = basename( $fileName, $fileSuffix );
        // create dest filename
        $destFileName = md5( $fileBaseName . microtime() . mt_rand() ) . $fileSuffix;
        $destination = $destination . '/' . $destFileName;

        if ( !copy( $filePath, $destination ) )
        {
            eZDebug::writeError( "Failed to copy '$filePath' to '$destination'", __METHOD__ );
            return false;
        }

        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $destination, 'mediafile', true, $mimeData['name'] );

        $classAttribute = $objectAttribute->contentClassAttribute();
        $player = $classAttribute->attribute( "data_text1" );
        $pluginPage = eZMediaType::pluginPage( $player );

        $media->setAttribute( "contentobject_attribute_id", $attributeID );
        $media->setAttribute( "version", $objectVersion );
        $media->setAttribute( "filename", $destFileName );
        $media->setAttribute( "original_filename", $fileName );
        $media->setAttribute( "mime_type", $mimeData['name'] );

        // Setting width and height to zero means that the browser/player must find the size itself.
        // In the future we will probably analyze the media file and find this information
        $width = $height = 0;
        // Quality is not known, so we don't set any
        $quality = false;
        // Not sure what this is for, set to false
        $controls = false;
        // We want to show controllers by default
        $hasController = true;
        // Don't play automatically
        $isAutoplay = false;
        // Don't loop movie
        $isLoop = false;

        $media->setAttribute( "width", $width );
        $media->setAttribute( "height", $height );
        $media->setAttribute( "quality", $quality );
        $media->setAttribute( "controls", $controls );
        $media->setAttribute( "pluginspage", $pluginPage );
        $media->setAttribute( "is_autoplay", $isAutoplay );
        $media->setAttribute( "has_controller", $hasController );
        $media->setAttribute( "is_loop", $isLoop );

        $media->store();

        $objectAttribute->setContent( $media );
        return true;
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
      Extracts file information for the media entry.
    */
    function storedFileInformation( $object, $objectVersion, $objectLanguage,
                                    $objectAttribute )
    {
        $mediaFile = eZMedia::fetch( $objectAttribute->attribute( "id" ),
                                      $objectAttribute->attribute( "version" ) );
        if ( $mediaFile )
        {
            return $mediaFile->storedFileInfo();
        }
        return false;
    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function storeDefinedClassAttribute( $attribute )
    {
    }

    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $filesizeName = $base . self::MAX_FILESIZE_VARIABLE . $classAttribute->attribute( 'id' );
        $typeName = $base . self::TYPE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $filesizeName ) )
        {
            $filesizeValue = $http->postVariable( $filesizeName );
            // The field is an integer number of megabytes: an array, text or a
            // negative number from a crafted form is stored as 0 (no limit set)
            $filesizeValue = is_scalar( $filesizeValue ) && is_numeric( trim( (string)$filesizeValue ) )
                             ? max( 0, (int)trim( (string)$filesizeValue ) ) : 0;
            $classAttribute->setAttribute( self::MAX_FILESIZE_FIELD, $filesizeValue );
        }
        if ( $http->hasPostVariable( $typeName ) )
        {
            $typeValue = $http->postVariable( $typeName );
            // The player type is a text column; an array is not a player
            if ( is_scalar( $typeValue ) )
                $classAttribute->setAttribute( self::TYPE_FIELD, (string)$typeValue );
        }
    }

    /*!
     Returns the object title.
    */
    function title( $contentObjectAttribute,  $name = "original_filename" )
    {
        $mediaFile = eZMedia::fetch( $contentObjectAttribute->attribute( "id" ),
                                      $contentObjectAttribute->attribute( "version" ) );

        if ( $mediaFile != null )
            $value = $mediaFile->attribute( $name );
        else
            $value = "";
        return $value;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $mediaFile = eZMedia::fetch( $contentObjectAttribute->attribute( "id" ),
                                      $contentObjectAttribute->attribute( "version" ) );
        if ( !$mediaFile )
            return false;
        if( $mediaFile->attribute( "filename" ) == "" )
            return false;
       return true;
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $mediaFile = eZMedia::fetch( $contentObjectAttribute->attribute( "id" ),
                                      $contentObjectAttribute->attribute( "version" ) );
        if ( !$mediaFile )
        {
            $retValue = false;
            return $retValue;
        }
        return $mediaFile;
    }

    function metaData( $contentObjectAttribute )
    {
        return "";
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $objectAttribute )
    {
        $mediaFile = $objectAttribute->content();

        if ( is_object( $mediaFile ) )
        {
            return implode( '|', array( $mediaFile->attribute( 'filepath' ), $mediaFile->attribute( 'original_filename' ) ) );
        }
        else
            return '';
    }



    function fromString( $objectAttribute, $string )
    {
        if( !$string )
            return true;

        // toString() writes "filepath|original_filename"; the whole string used to be
        // taken as the path, so an exported value could never be imported again.
        // A path without "|" is read as before.
        $parts = explode( '|', (string)$string, 2 );
        $filePath = $parts[0];
        $originalFileName = isset( $parts[1] ) ? $parts[1] : '';

        $result = array();
        $stored = $this->insertRegularFile( $objectAttribute->attribute( 'object' ),
                                            $objectAttribute->attribute( 'version' ),
                                            $objectAttribute->attribute( 'language_code' ),
                                            $objectAttribute,
                                            $filePath,
                                            $result );
        if ( $stored && $originalFileName !== '' )
        {
            $media = $objectAttribute->content();
            if ( $media instanceof eZMedia )
            {
                $media->setAttribute( 'original_filename', $originalFileName );
                $media->store();
            }
        }
        return $stored;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $maxSize = $classAttribute->attribute( self::MAX_FILESIZE_FIELD );
        $type = $classAttribute->attribute( self::TYPE_FIELD );

        $dom = $attributeParametersNode->ownerDocument;

        $maxSizeNode = $dom->createElement( 'max-size' );
        $maxSizeNode->appendChild( $dom->createTextNode( $maxSize ) );
        $maxSizeNode->setAttribute( 'unit-size', 'mega' );
        $attributeParametersNode->appendChild( $maxSizeNode );

        $typeNode = $dom->createElement( 'type' );
        $typeNode->appendChild( $dom->createTextNode( $type ) );
        $attributeParametersNode->appendChild( $typeNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package written without one of the elements (or by hand) keeps the
        // default for it rather than failing on a null node
        if ( !$attributeParametersNode )
            return;
        $sizeNode = $attributeParametersNode->getElementsByTagName( 'max-size' )->item( 0 );
        if ( $sizeNode )
        {
            $maxSize = trim( $sizeNode->textContent );
            $classAttribute->setAttribute( self::MAX_FILESIZE_FIELD, is_numeric( $maxSize ) ? max( 0, (int)$maxSize ) : 0 );
        }
        $typeNode = $attributeParametersNode->getElementsByTagName( 'type' )->item( 0 );
        if ( $typeNode )
            $classAttribute->setAttribute( self::TYPE_FIELD, $typeNode->textContent );
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {

        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $mediaFile = $objectAttribute->attribute( 'content' );
        if ( !$mediaFile )
        {
            // Media type content could not be found.
            return $node;
        }

        $fileKey = md5( mt_rand() );

        $fileInfo = $mediaFile->storedFileInfo();
        $package->appendSimpleFile( $fileKey, $fileInfo['filepath'] );

        $dom = $node->ownerDocument;

        $mediaNode = $dom->createElement( 'media-file' );
        $mediaNode->setAttribute( 'filesize', $mediaFile->attribute( 'filesize' ) );
        $mediaNode->setAttribute( 'filename', $mediaFile->attribute( 'filename' ) );
        $mediaNode->setAttribute( 'original-filename', $mediaFile->attribute( 'original_filename' ) );
        $mediaNode->setAttribute( 'mime-type', $mediaFile->attribute( 'mime_type' ) );
        $mediaNode->setAttribute( 'filekey', $fileKey );

        // A row made by create() has NULL has_controller/is_autoplay (it sets other
        // keys), which DOM refuses with a deprecation on PHP 8.1+; (string) gives
        // the same text DOM made of a bool or a number before
        $mediaNode->setAttribute( 'width', (string)$mediaFile->attribute( 'width' ) );
        $mediaNode->setAttribute( 'height', (string)$mediaFile->attribute( 'height' ) );
        $mediaNode->setAttribute( 'has-controller', (string)$mediaFile->attribute( 'has_controller' ) );
        $mediaNode->setAttribute( 'controls', (string)$mediaFile->attribute( 'controls' ) );
        $mediaNode->setAttribute( 'is-autoplay', (string)$mediaFile->attribute( 'is_autoplay' ) );
        $mediaNode->setAttribute( 'plugins-page', (string)$mediaFile->attribute( 'pluginspage' ) );
        $mediaNode->setAttribute( 'quality', (string)$mediaFile->attribute( 'quality' ) );
        $mediaNode->setAttribute( 'is-loop', (string)$mediaFile->attribute( 'is_loop' ) );
        $node->appendChild( $mediaNode );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $mediaNode = $attributeNode->getElementsByTagName( 'media-file' )->item( 0 );
        if ( !$mediaNode )
        {
            // No media type data found.
            return;
        }

        $mediaFile = eZMedia::create( $objectAttribute->attribute( 'id' ), $objectAttribute->attribute( 'version' ) );

        $sourcePath = $package->simpleFilePath( $mediaNode->getAttribute( 'filekey' ) );
        // A package without the file (or a wrong key) is reported, as the binary
        // file datatype does, instead of a copy warning and a row without a file
        if ( !is_string( $sourcePath ) || !file_exists( $sourcePath ) )
        {
            eZDebug::writeError( "The file '$sourcePath' does not exist, cannot initialize media attribute with it", __METHOD__ );
            return false;
        }

        $ini = eZINI::instance();
        $mimeType = $mediaNode->getAttribute( 'mime-type' );
        // The package decides the directory through the mime type: "../x" must not
        // make the copy land outside var/storage/original
        $mimeTypeCategory = eZMedia::mimeGroup( $mimeType );
        $destinationPath = eZSys::storageDirectory() . '/original/' . $mimeTypeCategory . '/';
        if ( !file_exists( $destinationPath ) )
        {
            if ( !eZDir::mkdir( $destinationPath, false, true ) )
            {
                return false;
            }
        }

        // basename() alone keeps a backslash path or "..": the stored name must be one
        // that the delete code will later accept
        $basename = eZMedia::safeFileName( $mediaNode->getAttribute( 'filename' ) );
        while ( $basename === '' || file_exists( $destinationPath . $basename ) )
        {
            $basename = substr( md5( mt_rand() ), 0, 8 ) . '.' . eZFile::suffix( eZMedia::safeFileName( $mediaNode->getAttribute( 'filename' ) ) );
        }

        eZFileHandler::copy( $sourcePath, $destinationPath . $basename );
        eZDebug::writeNotice( 'Copied: ' . $sourcePath . ' to: ' . $destinationPath . $basename, __METHOD__ );

        $mediaFile->setAttribute( 'contentobject_attribute_id', $objectAttribute->attribute( 'id' ) );
        $mediaFile->setAttribute( 'filename', $basename );
        $mediaFile->setAttribute( 'original_filename', $mediaNode->getAttribute( 'original-filename' ) );
        $mediaFile->setAttribute( 'mime_type', $mediaNode->getAttribute( 'mime-type' ) );

        // Integer columns that end up in player markup: numbers only
        $mediaFile->setAttribute( 'width', max( 0, (int)$mediaNode->getAttribute( 'width' ) ) );
        $mediaFile->setAttribute( 'height', max( 0, (int)$mediaNode->getAttribute( 'height' ) ) );
        $mediaFile->setAttribute( 'has_controller', $mediaNode->getAttribute( 'has-controller' ) );
        $mediaFile->setAttribute( 'controls', $mediaNode->getAttribute( 'controls' ) );
        $mediaFile->setAttribute( 'is_autoplay', $mediaNode->getAttribute( 'is-autoplay' ) );
        $mediaFile->setAttribute( 'pluginspage', $mediaNode->getAttribute( 'plugins-page' ) );
        $mediaFile->setAttribute( 'quality', $mediaNode->getAttribute( 'quality' ) );
        $mediaFile->setAttribute( 'is_loop', $mediaNode->getAttribute( 'is-loop' ) );

        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $destinationPath . $basename, 'mediafile', true );

        $mediaFile->store();
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /// \privatesection
    /// The file extension blacklist validator
    private $FileExtensionBlackListValidator;
}

eZDataType::register( eZMediaType::DATA_TYPE_STRING, "eZMediaType" );

?>
