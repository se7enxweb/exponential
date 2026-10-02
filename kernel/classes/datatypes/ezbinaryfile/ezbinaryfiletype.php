<?php
/**
 * File containing the eZBinaryFileType class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZBinaryFileType ezbinaryfiletype.php
  \ingroup eZDatatype
  \brief The class eZBinaryFileType handles files and association with content objects

*/

class eZBinaryFileType extends eZDataType
{
    const MAX_FILESIZE_FIELD = 'data_int1';

    const MAX_FILESIZE_VARIABLE = '_ezbinaryfile_max_filesize_';

    const DATA_TYPE_STRING = "ezbinaryfile";

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "File", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
        $this->FileExtensionBlackListValidator = new eZFileExtensionBlackListValidator();
    }

    /*!
     \return the binary file handler.
    */
    function fileHandler()
    {
        return eZBinaryFileHandler::instance();
    }

    /*!
     \return the template name which the handler decides upon.
    */
    function viewTemplate( $contentobjectAttribute )
    {
        $handler = $this->fileHandler();
        $handlerTemplate = $handler->viewTemplate( $contentobjectAttribute );
        $template = $this->DataTypeString;
        if ( $handlerTemplate !== false )
            $template .= '_' . $handlerTemplate;
        return $template;
    }

    /*!
     \return the template name to use for editing the attribute.
     \note Default is to return the datatype string which is OK
           for most datatypes, if you want dynamic templates
           reimplement this function and return a template name.
     \note The returned template name does not include the .tpl extension.
     \sa viewTemplate, informationTemplate
    */
    function editTemplate( $contentobjectAttribute )
    {
        $handler = $this->fileHandler();
        $handlerTemplate = $handler->editTemplate( $contentobjectAttribute );
        $template = $this->DataTypeString;
        if ( $handlerTemplate !== false )
            $template .= '_' . $handlerTemplate;
        return $template;
    }

    /*!
     \return the template name to use for information collection for the attribute.
     \note Default is to return the datatype string which is OK
           for most datatypes, if you want dynamic templates
           reimplement this function and return a template name.
     \note The returned template name does not include the .tpl extension.
     \sa viewTemplate, editTemplate
    */
    function informationTemplate( $contentobjectAttribute )
    {
        $handler = $this->fileHandler();
        $handlerTemplate = $handler->informationTemplate( $contentobjectAttribute );
        $template = $this->DataTypeString;
        if ( $handlerTemplate !== false )
            $template .= '_' . $handlerTemplate;
        return $template;
    }

    /*!
     Sets value according to current version
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $contentObjectAttributeID = $originalContentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $oldfile = eZBinaryFile::fetch( $contentObjectAttributeID, $currentVersion );
            if ( $oldfile != null )
            {
                $oldfile->setAttribute( 'contentobject_attribute_id', $contentObjectAttribute->attribute( 'id' ) );
                $oldfile->setAttribute( "version",  $version );
                $oldfile->store();
            }
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
            $binaryFiles = eZBinaryFile::fetch( $contentObjectAttributeID );
        else
            $binaryFiles = array( eZBinaryFile::fetch( $contentObjectAttributeID, $version ) );

        foreach ( (array)$binaryFiles as $binaryFile )
        {
            if ( $binaryFile == null )
                continue;
            $mimeType =  $binaryFile->attribute( "mime_type" );
            $orig_dir = $storage_dir . '/original/' . eZBinaryFile::mimeGroup( $mimeType );
            $fileName = $binaryFile->attribute( "filename" );
            // A stored name with a directory part is never moved: it would rename a
            // file outside the storage directory
            if ( !eZBinaryFile::isSafeFileName( $fileName ) )
                continue;

            // Check if there are any other records in ezbinaryfile that point to that fileName.
            $binaryObjectsWithSameFileName = eZBinaryFile::fetchByFileName( $fileName );

            $filePath = $orig_dir . "/" . $fileName;
            $file = eZClusterFileHandler::instance( $filePath );

            if ( $file->exists() and count( (array)$binaryObjectsWithSameFileName ) <= 1 )
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
                $binaryFile->setAttribute( 'filename', $newFileName );
                $binaryFile->store();
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
            $binaryFiles = eZBinaryFile::fetch( $contentObjectAttributeID );
            eZBinaryFile::removeByID( $contentObjectAttributeID, null );

            foreach ( (array)$binaryFiles as  $binaryFile )
            {
                $mimeType =  $binaryFile->attribute( "mime_type" );
                $orig_dir = $storage_dir . '/original/' . eZBinaryFile::mimeGroup( $mimeType );
                $fileName = $binaryFile->attribute( "filename" );
                // A stored name with a directory part ("../../settings/x") is never
                // deleted: it is not a file this datatype stored
                if ( !eZBinaryFile::isSafeFileName( $fileName ) )
                    continue;

                // Check if there are any other records in ezbinaryfile that point to that fileName.
                $binaryObjectsWithSameFileName = eZBinaryFile::fetchByFileName( $fileName );

                $filePath = $orig_dir . "/" . $fileName;
                $file = eZClusterFileHandler::instance( $filePath );

                if ( $file->exists() and count( (array)$binaryObjectsWithSameFileName ) < 1 )
                    $file->delete();
            }
        }
        else
        {
            $count = 0;
            $binaryFile = eZBinaryFile::fetch( $contentObjectAttributeID, $version );
            if ( $binaryFile != null )
            {
                $mimeType =  $binaryFile->attribute( "mime_type" );
                $orig_dir = $storage_dir . "/original/" . eZBinaryFile::mimeGroup( $mimeType );
                $fileName = $binaryFile->attribute( "filename" );

                eZBinaryFile::removeByID( $contentObjectAttributeID, $version );

                // The row goes, but a stored name with a directory part is never
                // deleted: it is not a file this datatype stored
                if ( eZBinaryFile::isSafeFileName( $fileName ) )
                {
                    // Check if there are any other records in ezbinaryfile that point to that fileName.
                    $binaryObjectsWithSameFileName = eZBinaryFile::fetchByFileName( $fileName );

                    $filePath = $orig_dir . "/" . $fileName;
                    $file = eZClusterFileHandler::instance( $filePath );

                    if ( $file->exists() and count( (array)$binaryObjectsWithSameFileName ) < 1 )
                        $file->delete();
                }
            }
        }
    }

    /*!
     Checks if file uploads are enabled, if not it gives a warning.
    */
    function checkFileUploads()
    {
        $isFileUploadsEnabled = ini_get( 'file_uploads' ) != 0;
        if ( !$isFileUploadsEnabled )
        {
            // The flag is unset until the first warning, reading it raised a warning
            if ( empty( $GLOBALS['eZBinaryFileTypeWarningAdded'] ) )
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
                $GLOBALS['eZBinaryFileTypeWarningAdded'] = true;
            }
        }
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        eZBinaryFileType::checkFileUploads();
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $mustUpload = false;
        $httpFileName = $base . "_data_binaryfilename_" . $contentObjectAttribute->attribute( "id" );
        // The limit is a number of megabytes, 0 for none; an empty or broken class
        // value is no limit instead of a non-numeric warning
        $maxSize = 1024 * 1024 * max( 0, (int)$classAttribute->attribute( self::MAX_FILESIZE_FIELD ) );

        $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
        $version = $contentObjectAttribute->attribute( 'version' );
        $binary = eZBinaryFile::fetch( $contentObjectAttributeID, $version );
        // A form that posts the file field as an array (name[]) is not an upload
        // this datatype can take; it must not reach the validators as an array
        if ( isset( $_FILES[$httpFileName] ) &&
             ( !is_array( $_FILES[$httpFileName] ) ||
               !is_string( $_FILES[$httpFileName]['tmp_name'] ?? '' ) || !is_string( $_FILES[$httpFileName]['name'] ?? '' ) ||
               !is_scalar( $_FILES[$httpFileName]['error'] ?? null ) ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'A valid file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        $extensionsBlackList = implode(', ', $this->FileExtensionBlackListValidator->extensionsBlackList() );
        if ( $binary === null )
        {
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $mustUpload = true;
            }
        }
        else
        {
            $state = $this->FileExtensionBlackListValidator->validate( $binary->attribute( 'filename' ) );
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
                                                                 'A valid file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult === eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                'The size of the uploaded file exceeds the limit set by the upload_max_filesize directive in php.ini.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult === eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'The size of the uploaded file exceeds the maximum upload size: %1 bytes.' ), $maxSize );
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
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        eZBinaryFileType::checkFileUploads();
        if ( $this->isDeletingFile( $http, $contentObjectAttribute ) )
        {
            return false;
        }

        if ( $this->validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) !== eZInputValidator::STATE_ACCEPTED )
        {
            return false;
        }

        if ( !eZHTTPFile::canFetch( $base . "_data_binaryfilename_" . $contentObjectAttribute->attribute( "id" ) ) )
            return false;

        $httpBinaryFile = eZHTTPFile::fetch( $base . "_data_binaryfilename_" . $contentObjectAttribute->attribute( "id" ) );

        $contentObjectAttribute->setContent( $httpBinaryFile );

        if ( $httpBinaryFile instanceof eZHTTPFile )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );

            /*
            $mimeObj = new  eZMimeType();
            $mimeData = $mimeObj->findByURL( $httpBinaryFile->attribute( "original_filename" ), true );
            $mime = $mimeData['name'];
            */

            $mimeData = eZMimeType::findByFileContents( $httpBinaryFile->attribute( "original_filename" ) );
            $mime = $mimeData['name'];

            if ( $mime == '' )
            {
                $mime = $httpBinaryFile->attribute( "mime_type" );
            }
            $extension = eZFile::suffix( $httpBinaryFile->attribute( "original_filename" ) );
            $httpBinaryFile->setMimeType( $mime );
            if ( !$httpBinaryFile->store( "original", $extension ) )
            {
                eZDebug::writeError( "Failed to store http-file: " . $httpBinaryFile->attribute( "original_filename" ),
                                     "eZBinaryFileType" );
                return false;
            }

            $binary = eZBinaryFile::fetch( $contentObjectAttributeID, $version );
            if ( $binary === null )
            {
                $binary = eZBinaryFile::create( $contentObjectAttributeID, $version );
            }
            else
            {
                // if storing a different file for the same version, see if the existing file can be removed.
                $newFileName = basename( $httpBinaryFile->attribute( "filename" ) );
                if ( $newFileName != $binary->Filename )
                {
                    $this->deleteStoredObjectAttribute( $contentObjectAttribute, $version );
                }
            }

            $orig_dir = $httpBinaryFile->storageDir( "original" );

            $binary->setAttribute( "contentobject_attribute_id", $contentObjectAttributeID );
            $binary->setAttribute( "version", $version );
            $binary->setAttribute( "filename", basename( $httpBinaryFile->attribute( "filename" ) ) );
            $binary->setAttribute( "original_filename", $httpBinaryFile->attribute( "original_filename" ) );
            $binary->setAttribute( "mime_type", $mime );

            $binary->store();

            $filePath = $httpBinaryFile->attribute( 'filename' );
            $fileHandler = eZClusterFileHandler::instance();
            $fileHandler->fileStore( $filePath, 'binaryfile', true, $mime );

            $contentObjectAttribute->setContent( $binary );
        }
        return true;
    }

    /*!
     Does nothing, since the file has been stored. See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        eZBinaryFileType::checkFileUploads();
        if( $action == "delete_binary" )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $this->deleteStoredObjectAttribute( $contentObjectAttribute, $version );
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
     HTTP file insertion is supported.
    */
    function isRegularFileInsertionSupported()
    {
        return true;
    }

    /*!
     Inserts the file using the eZBinaryFile class.
    */
    function insertHTTPFile( $object, $objectVersion, $objectLanguage,
                             $objectAttribute, $httpFile, $mimeData,
                             &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );
        $attributeID = $objectAttribute->attribute( 'id' );

        $binary = eZBinaryFile::fetch( $attributeID, $objectVersion );
        if ( $binary === null )
            $binary = eZBinaryFile::create( $attributeID, $objectVersion );

        $httpFile->setMimeType( $mimeData['name'] );

        $suffix = false;
        if ( isset( $mimeData['suffix'] ) )
            $suffix = $mimeData['suffix'];

        $db = eZDB::instance();
        $db->begin();

        if ( !$httpFile->store( "original", $suffix, false ) )
        {
            $result['errors'][] = array( 'description' => ezpI18n::tr( 'kernel/classes/datatypes/ezbinaryfile',
                                                        'Failed to store file %filename. Please contact the site administrator.', null,
                                                        array( '%filename' => $httpFile->attribute( "original_filename" ) ) ) );
            return false;
        }


        $filePath = $binary->attribute( 'filename' );

        $binary->setAttribute( "contentobject_attribute_id", $attributeID );
        $binary->setAttribute( "version", $objectVersion );
        $binary->setAttribute( "filename", basename( $httpFile->attribute( "filename" ) ) );
        $binary->setAttribute( "original_filename", $httpFile->attribute( "original_filename" ) );
        $binary->setAttribute( "mime_type", $mimeData['name'] );

        $binary->store();

        $filePath = $httpFile->attribute( 'filename' );
        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $filePath, 'binaryfile', true, $mimeData['name'] );
        $objectAttribute->setContent( $binary );

        $db->commit();

        $objectAttribute->setContent( $binary );

        return true;
    }

    /*!
     Inserts the file using the eZBinaryFile class.
    */
    function insertRegularFile( $object, $objectVersion, $objectLanguage,
                                $objectAttribute, $filePath,
                                &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => false );
        $attributeID = $objectAttribute->attribute( 'id' );

        $binary = eZBinaryFile::fetch( $attributeID, $objectVersion );
        if ( $binary === null )
            $binary = eZBinaryFile::create( $attributeID, $objectVersion );

        $fileName = basename( $filePath );
        // An import names a file that may not be there (a stale CSV, a typo): that
        // is a failed import, not a copy() warning and an empty stored file
        if ( !is_string( $filePath ) || $filePath === '' || !is_file( $filePath ) || !is_readable( $filePath ) )
        {
            eZDebug::writeError( "The file '$filePath' does not exist, cannot initialize file attribute with it", __METHOD__ );
            return false;
        }
        $mimeData = eZMimeType::findByFileContents( $filePath );
        $storageDir = eZSys::storageDirectory();
        $group = eZBinaryFile::mimeGroup( $mimeData['name'] ?? '' );
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
        $fileHandler->fileStore( $destination, 'binaryfile', true, $mimeData['name'] );


        $binary->setAttribute( "contentobject_attribute_id", $attributeID );
        $binary->setAttribute( "version", $objectVersion );
        $binary->setAttribute( "filename", $destFileName );
        $binary->setAttribute( "original_filename", $fileName );
        $binary->setAttribute( "mime_type", $mimeData['name'] );

        $binary->store();

        $objectAttribute->setContent( $binary );
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
      Extracts file information for the binaryfile entry.
    */
    function storedFileInformation( $object, $objectVersion, $objectLanguage,
                                    $objectAttribute )
    {
        $binaryFile = eZBinaryFile::fetch( $objectAttribute->attribute( "id" ),
                                            $objectAttribute->attribute( "version" ) );
        if ( $binaryFile )
        {
            return $binaryFile->storedFileInfo();
        }
        return false;
    }
    /*!
      Updates download count for binary file.
    */
    function handleDownload( $object, $objectVersion, $objectLanguage,
                             $objectAttribute )
    {
        $binaryFile = eZBinaryFile::fetch( $objectAttribute->attribute( "id" ),
                                            $objectAttribute->attribute( "version" ) );

        // Both go into SQL unquoted: numbers only
        $contentObjectAttributeID = (int)$objectAttribute->attribute( 'id' );
        $version =  (int)$objectAttribute->attribute( "version" );

        if ( $binaryFile )
        {
            $db = eZDB::instance();
            if ( $db->databaseName() === 'mongo' )
            {
                $db->mongoUpdateOne( 'ezbinaryfile',
                    [ 'contentobject_attribute_id' => (int)$contentObjectAttributeID,
                      'version'                    => (int)$version ],
                    [ '$inc' => [ 'download_count' => 1 ] ] );
            }
            else
            {
                $db->query( "UPDATE ezbinaryfile SET download_count=(download_count+1)
                             WHERE
                             contentobject_attribute_id=$contentObjectAttributeID AND version=$version" );
            }
            return true;
        }
        return false;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $filesizeName = $base . self::MAX_FILESIZE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $filesizeName ) )
        {
            $filesizeValue = $http->postVariable( $filesizeName );
            // The field is an integer number of megabytes: an array, text or a
            // negative number from a crafted form is stored as 0 (no limit set)
            $filesizeValue = is_scalar( $filesizeValue ) && is_numeric( trim( (string)$filesizeValue ) )
                             ? max( 0, (int)trim( (string)$filesizeValue ) ) : 0;
            $classAttribute->setAttribute( self::MAX_FILESIZE_FIELD, $filesizeValue );
        }
    }
    /*!
     Returns the object title.
    */
    function title( $contentObjectAttribute,  $name = "original_filename" )
    {
        $value = false;
        $binaryFile = eZBinaryFile::fetch( $contentObjectAttribute->attribute( 'id' ),
                                           $contentObjectAttribute->attribute( 'version' ) );
        if ( is_object( $binaryFile ) )
            $value = $binaryFile->attribute( $name );

        return $value;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $binaryFile = eZBinaryFile::fetch( $contentObjectAttribute->attribute( "id" ),
                                            $contentObjectAttribute->attribute( "version" ) );
        if ( !$binaryFile )
            return false;
        return true;
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $binaryFile = eZBinaryFile::fetch( $contentObjectAttribute->attribute( "id" ),
                                            $contentObjectAttribute->attribute( "version" ) );
        if ( !$binaryFile )
        {
            $attrValue = false;
            return $attrValue;
        }
        return $binaryFile;
    }

    function isIndexable()
    {
        return true;
    }

    function metaData( $contentObjectAttribute )
    {
        $binaryFile = $contentObjectAttribute->content();

        $metaData = "";
        if ( $binaryFile instanceof eZBinaryFile )
        {
            $metaData = $binaryFile->metaData();
        }
        return $metaData;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $dom = $attributeParametersNode->ownerDocument;
        $maxSize = $classAttribute->attribute( self::MAX_FILESIZE_FIELD );
        $maxSizeNode = $dom->createElement( 'max-size' );
        $maxSizeNode->appendChild( $dom->createTextNode( $maxSize ) );
        $maxSizeNode->setAttribute( 'unit-size', 'mega' );
        $attributeParametersNode->appendChild( $maxSizeNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        // A package written without the element (or by hand) has no limit rather
        // than a fatal error on a null node
        $sizeNode = $attributeParametersNode ? $attributeParametersNode->getElementsByTagName( 'max-size' )->item( 0 ) : null;
        if ( !$sizeNode )
            return;
        $maxSize = trim( $sizeNode->textContent );
        $classAttribute->setAttribute( self::MAX_FILESIZE_FIELD, is_numeric( $maxSize ) ? max( 0, (int)$maxSize ) : 0 );
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $objectAttribute )
    {
        $binaryFile = $objectAttribute->content();

        if ( is_object( $binaryFile ) )
        {
            return implode( '|', array( $binaryFile->attribute( 'filepath' ), $binaryFile->attribute( 'original_filename' ) ) );
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
            $binary = $objectAttribute->content();
            if ( $binary instanceof eZBinaryFile )
            {
                $binary->setAttribute( 'original_filename', $originalFileName );
                $binary->store();
            }
        }
        return $stored;
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $binaryFile = $objectAttribute->attribute( 'content' );
        if ( is_object( $binaryFile ) )
        {
            $fileKey = md5( mt_rand() );
            $package->appendSimpleFile( $fileKey, $binaryFile->attribute( 'filepath' ) );

            $dom = $node->ownerDocument;
            $fileNode = $dom->createElement( 'binary-file' );
            $fileNode->setAttribute( 'filesize', $binaryFile->attribute( 'filesize' ) );
            $fileNode->setAttribute( 'filename', $binaryFile->attribute( 'filename' ) );
            $fileNode->setAttribute( 'original-filename', $binaryFile->attribute( 'original_filename' ) );
            $fileNode->setAttribute( 'mime-type', $binaryFile->attribute( 'mime_type' ) );
            $fileNode->setAttribute( 'filekey', $fileKey );
            $node->appendChild( $fileNode );
        }

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $fileNode = $attributeNode->getElementsByTagName( 'binary-file' )->item( 0 );
        if ( !is_object( $fileNode ) or !$fileNode->hasAttributes() )
        {
            return;
        }

        $binaryFile = eZBinaryFile::create( $objectAttribute->attribute( 'id' ), $objectAttribute->attribute( 'version' ) );

        $sourcePath = $package->simpleFilePath( $fileNode->getAttribute( 'filekey' ) );

        if ( !file_exists( $sourcePath ) )
        {
            eZDebug::writeError( "The file '$sourcePath' does not exist, cannot initialize file attribute with it", __METHOD__ );
            return false;
        }

        $ini = eZINI::instance();
        $mimeType = $fileNode->getAttribute( 'mime-type' );
        // The package decides the directory through the mime type: "../x" must not
        // make the copy land outside var/storage/original
        $mimeTypeCategory = eZBinaryFile::mimeGroup( $mimeType );
        $destinationPath = eZSys::storageDirectory() . '/original/' . $mimeTypeCategory . '/';
        if ( !file_exists( $destinationPath ) )
        {
            $oldumask = umask( 0 );
            if ( !eZDir::mkdir( $destinationPath, false, true ) )
            {
                umask( $oldumask );
                return false;
            }
            umask( $oldumask );
        }

        // basename() alone keeps a backslash path or "..": the stored name must be one
        // that the delete code will later accept
        $basename = eZBinaryFile::safeFileName( $fileNode->getAttribute( 'filename' ) );
        while ( $basename === '' || file_exists( $destinationPath . $basename ) )
        {
            $basename = substr( md5( mt_rand() ), 0, 8 ) . '.' . eZFile::suffix( eZBinaryFile::safeFileName( $fileNode->getAttribute( 'filename' ) ) );
        }

        eZFileHandler::copy( $sourcePath, $destinationPath . $basename );
        eZDebug::writeNotice( 'Copied: ' . $sourcePath . ' to: ' . $destinationPath . $basename, __METHOD__ );

        $binaryFile->setAttribute( 'contentobject_attribute_id', $objectAttribute->attribute( 'id' ) );
        $binaryFile->setAttribute( 'filename', $basename );
        $binaryFile->setAttribute( 'original_filename', $fileNode->getAttribute( 'original-filename' ) );
        $binaryFile->setAttribute( 'mime_type', $fileNode->getAttribute( 'mime-type' ) );

        $binaryFile->store();

        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $destinationPath . $basename, 'binaryfile', true );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }

    /**
     * Checks if current HTTP request is asking for current binary file deletion
     * @param eZHTTPTool $http
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     */
    private function isDeletingFile( eZHTTPTool $http, eZContentObjectAttribute $contentObjectAttribute )
    {
        $isDeletingFile = false;
        if ( $http->hasPostVariable( 'CustomActionButton' ) )
        {
            $customActionArray = $http->postVariable( 'CustomActionButton' );
            $attributeID = $contentObjectAttribute->attribute( 'id' );
            if ( isset( $customActionArray[$attributeID . '_delete_binary'] ) )
            {
                $isDeletingFile = true;
            }
        }

        return $isDeletingFile;
    }

    /// \privatesection
    /// The file extension blacklist validator
    private $FileExtensionBlackListValidator;
}

eZDataType::register( eZBinaryFileType::DATA_TYPE_STRING, "eZBinaryFileType" );

?>
