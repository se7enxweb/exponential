<?php
//
// Definition of EnhancedeZBinaryFileType class
//
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.4.0
// BUILD VERSION: 21995
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 Contactivity.com
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

/*!
  \class EnhancedeZBinaryFileType enhancedezbinaryfiletype.php
  \ingroup eZDatatype
  \brief The class EnhancedeZBinaryFileType handles files and association with content objects with the collectedinformation function

*/

class EnhancedeZBinaryFileType extends eZDataType
{
    const MAX_FILESIZE_FIELD = 'data_int1';
    const MAX_FILESIZE_VARIABLE = '_enhancedezbinaryfile_max_filesize_';
    const DATA_TYPE_STRING = "enhancedezbinaryfile";

    /*!
     Construction of the class, note that the second parameter in eZDataType
     is the actual name showed in the datatype dropdown list.
    */
    function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, ezpI18n::tr( 'extension/enhancedezbinaryfile/datatype', 'Enhanced File', 'Datatype name' ), array( 'serialize_supported' => true, 'object_serialize_map' => array( 'data_text' => 'filename' )));
    }

    function EnhancedeZBinaryFileType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Enhanced File", 'Datatype name' ),
                           array( 'serialize_supported' => true,'object_serialize_map' => array( 'data_text' => 'filename' )
                            ));
    }

    /*!
     \return the binary file handler.
    */
    function fileHandler()
    {
        return eZBinaryFileHandler::instance();
    }

    /*!
     \reimp
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
     Delete stored attribute
    */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
    /* This is called when you delete the datatype from the class*/
        $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
        $sys = eZSys::instance();
        $storage_dir = $sys->storageDirectory();

        require_once( 'kernel/classes/ezclusterfilehandler.php' );
        if ( $version == null )
        {
            $binaryFiles = eZBinaryFile::fetch( $contentObjectAttributeID );
            eZBinaryFile::removeByID( $contentObjectAttributeID, null );

            foreach ( $binaryFiles as  $binaryFile )
            {
                $mimeType =  $binaryFile->attribute( "mime_type" );
                list( $prefix, $suffix ) = split ('[/]', $mimeType );
                $orig_dir = $storage_dir . '/'.$downloadPath.'/' . $prefix;
                $fileName = $binaryFile->attribute( "filename" );

                // Check if there are any other records in ezbinaryfile that point to that fileName.
                $binaryObjectsWithSameFileName = eZBinaryFile::fetchByFileName( $fileName );

                $filePath = $orig_dir . "/" . $fileName;
                $file = eZClusterFileHandler::instance( $filePath );

                if ( $file->exists() and count( $binaryObjectsWithSameFileName ) < 1 )
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
                list( $prefix, $suffix ) = split ('[/]', $mimeType );
                $orig_dir = $storage_dir . "/original/" . $prefix;
                $fileName = $binaryFile->attribute( "filename" );

                eZBinaryFile::removeByID( $contentObjectAttributeID, $version );

                // Check if there are any other records in ezbinaryfile that point to that fileName.
                $binaryObjectsWithSameFileName = eZBinaryFile::fetchByFileName( $fileName );

                $filePath = $orig_dir . "/" . $fileName;
                $file = eZClusterFileHandler::instance( $filePath );

                if ( $file->exists() and count( $binaryObjectsWithSameFileName ) < 1 )
                    $file->delete();
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
            $isFileWarningAdded = $GLOBALS['eZBinaryFileTypeWarningAdded'];
            if ( !isset( $isFileWarningAdded ) or
                 !$isFileWarningAdded )
            {
                eZAppendWarningItem( array( 'error' => array( 'type' => 'kernel',
                                                              'number' => eZError::KERNEL_NOT_AVAILABLE ),
                                            'text' => ezpI18n::tr( 'kernel/classes/datatypes',
                                                              'File uploading is not enabled. Please contact the site administrator to enable it.' ) ) );
                $GLOBALS['eZBinaryFileTypeWarningAdded'] = true;
            }
        }
    }


    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
    /*gets hit the first time through*/
        EnhancedeZBinaryFileType::checkFileUploads();
        if ( !EnhancedeZBinaryFileType::canFetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) ) )
            return false;

        $binaryFile = eZHTTPFile::fetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) );

        $contentObjectAttribute->setContent( $binaryFile );

        if ( $binaryFile instanceof eZHTTPFile )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );

            /*
            $mimeObj = new  eZMimeType();
            $mimeData = $mimeObj->findByURL( $binaryFile->attribute( "original_filename" ), true );
            $mime = $mimeData['name'];
            */

            $mimeData = eZMimeType::findByFileContents( $binaryFile->attribute( "original_filename" ) );
            $mime = $mimeData['name'];

            if ( $mime == '' )
            {
                $mime = $binaryFile->attribute( "mime_type" );
            }

            $extension = eZFile::suffix( $binaryFile->attribute( "original_filename" ) );
            $binaryFile->setMimeType( $mime );
            if ( !$binaryFile->store( "original", $extension ) )
            {
                eZDebug::writeError( "Failed to store http-file: " . $binaryFile->attribute( "original_filename" ),
                                     "EnhancedeZBinaryFileType" );
                return false;
            }

            $binary = eZBinaryFile::fetch( $contentObjectAttributeID, $version );
            if ( $binary === null )
                $binary = eZBinaryFile::create( $contentObjectAttributeID, $version );

            $orig_dir = $binaryFile->storageDir( "original" );

            $binary->setAttribute( "contentobject_attribute_id", $contentObjectAttributeID );
            $binary->setAttribute( "version", $version );
            $binary->setAttribute( "filename", basename( $binaryFile->attribute( "filename" ) ) );
            $binary->setAttribute( "original_filename", $binaryFile->attribute( "original_filename" ) );
            $binary->setAttribute( "mime_type", $mime );

            $binary->store();

            // VS-DBFILE

            require_once( 'kernel/classes/ezclusterfilehandler.php' );
            $filePath = $binaryFile->attribute( 'filename' );
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
        EnhancedeZBinaryFileType::checkFileUploads();
        if( $action == "delete_binary" )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $this->deleteStoredObjectAttribute( $contentObjectAttribute, $version );
        }
    }

    /*!
     \reimp
     HTTP file insertion is supported.
    */
    function isHTTPFileInsertionSupported()
    {
        return true;
    }

    /*!
     \reimp
     HTTP file insertion is supported.
    */
    function isRegularFileInsertionSupported()
    {
        return true;
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $filesizeName = $base . self::MAX_FILESIZE_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $filesizeName ) )
        {
            $filesizeValue = $http->postVariable( $filesizeName );
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
    /* Gets hit the first time through */
        $binaryFile = eZBinaryFile::fetch( $contentObjectAttribute->attribute( "id" ),
                                            $contentObjectAttribute->attribute( "version" ) );
        if ( !$binaryFile )
            return false;
        return true;
    }

    function objectAttributeContent( $contentObjectAttribute )
    {
        $binaryFile = eZBinaryFile::fetch( $contentObjectAttribute->attribute( "id" ), $contentObjectAttribute->attribute( "version" ) );
        if ( !$binaryFile )
        {
            $attrValue = false;
            return $attrValue;
        }
        return $binaryFile;
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return true;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return true;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        EnhancedeZBinaryFileType::checkFileUploads();
        if ( eZHTTPFile::canFetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) ) != eZHTTPFile::UPLOADEDFILE_OK ) {
            return false;
	}
	//Check allowed file type - must do it here,again - otherwise an illegal
	//file will still be created in the storage directory
        $binaryFile = eZHTTPFile::fetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) );
	if (!$binaryFile) return eZInputValidator::STATE_INVALID;

        $moduleINI = eZINI::instance( 'module.ini.append.php', 'settings');
	$allowed = $moduleINI->variable('AllowedFileTypes', 'AllowedFileTypeList');

	// $binaryFile->attribute( 'mime_type_part' ) not always the extension
	$extension = preg_replace('/.*\.(.+?)$/', '\\1', $binaryFile->attribute( "original_filename" ) );
	if (!in_array(strtolower($extension),$allowed))
	{
		$contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes','Failed to store file. Only the following file types are allowed: %1.' ), implode(", ",$allowed) );
		return eZInputValidator::STATE_INVALID;
	}

        //$contentObjectAttribute->setContent( $binaryFile );

        if ( $binaryFile instanceof eZHTTPFile )
        {
		//clean up older files.
		$moduleINI = eZINI::instance( 'module.ini.append.php', 'settings');
		$maxFiles=$moduleINI->variable('RemoveFiles', 'MaxFiles');
		$downloadPath=$moduleINI->variable('RemoveFiles', 'DownloadPath');
		$downloadPath=trim($downloadPath,"/");
		if (!$downloadPath) $downloadPath='original/collected';

		if ( $maxFiles>0 )
		{
			$Files = array();
			$storageDir=eZSys::storageDirectory();
			$fileCollection=eZDir::recursiveFindRelative( $storageDir, $downloadPath, '.*' );
			if ( count($fileCollection)>=$maxFiles )
			{
				foreach ( $fileCollection as $fileItem )
                		{
					$lastModified = filemtime( $storageDir .'/'. $fileItem);
					$Files[$fileItem] = filemtime($storageDir .'/'. $fileItem);
                		}
            			asort($Files, SORT_NUMERIC);
				while (count($Files)>=$maxFiles){
					$removeFile=key($Files);
					if ( file_exists( $storageDir .'/'. $removeFile ) )
					{
						if (!unlink( $storageDir .'/'. $removeFile ) )
						{
							eZDebug::writeError( "Failed to delete file: " . $storageDir .'/'. $removeFile, "EnhancedeZBinaryFileType" );
							return false;
						}
					}
					array_shift($Files);
				}
			}
		}
		//end cleanup

           // $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            //$version = $contentObjectAttribute->attribute( "version" );

            $mimeData = eZMimeType::findByFileContents( $binaryFile->attribute( "original_filename" ) );  //Nice name but it still uses the extension to set the mimetype and therefore can be bogus
            $mime = $mimeData['name'];

            if ( $mime == '' )
            {
                $mime = $binaryFile->attribute( "mime_type" );
            }
            $extension = eZFile::suffix( $binaryFile->attribute( "original_filename" ) );
            $binaryFile->setMimeType( $mime );
            if ( !$binaryFile->store( $downloadPath, $extension ) )
            {
                eZDebug::writeError( "Failed to store http-file: " . $binaryFile->attribute( "original_filename" ),
                                     "EnhancedeZBinaryFileType" );
                return false;
            }

	//Adds xmltext to collection attribute with file info to data_text attribute
	$doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'binaryfile-info' );
	$binaryFileList = $doc->createElement( 'binaryfile-attributes' );
	foreach ( $binaryFile as $key => $binaryFileItem )
	{
		$binaryFileElement = $doc->createElement(  $key, $binaryFileItem );
		$binaryFileList->appendChild( $binaryFileElement );
	}
	$root->appendChild( $binaryFileList );
	$doc->appendChild( $root );
	$docText = EnhancedeZBinaryFileType::domString( $doc );
	$collectionAttribute->setAttribute( 'data_text', $docText );
        }
        return true;
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        //validateCollection goes first, then the fetchCollection
	/*WITH THE ENCTYPE OF MULTIPART THE $_POST FOR A FILE IS EMPTY so can't use haspostvariable */

	$binaryFile = eZHTTPFile::fetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) );
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
	//This is only checking if it is required or not
	if ( !$binaryFile )
	{
		if ( $contentObjectAttribute->validateIsRequired() )
		{
			$contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes','Input required.' ) );
			return eZInputValidator::STATE_INVALID;
		}
		else
			return eZInputValidator::STATE_ACCEPTED;
	}
	else {
		return $this->validateFileHTTPInput( $base, $contentObjectAttribute, $classAttribute );
	}
    }

    /*
     Private method, only for using inside this class.
    */

    function validateFileHTTPInput( $base, $contentObjectAttribute, $classAttribute )
    {
	//Check allowed file type
	//Have to do it here again, otherwise no error message
        $binaryFile = eZHTTPFile::fetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ) );
	if (!$binaryFile) return eZInputValidator::STATE_INVALID;

        $moduleINI = eZINI::instance( 'module.ini.append.php', 'settings');
	$allowed = $moduleINI->variable('AllowedFileTypes', 'AllowedFileTypeList');

	// $binaryFile->attribute( 'mime_type_part' ) not always the extension
	$extension = preg_replace('/.*\.(.+?)$/', '\\1', $binaryFile->attribute( "original_filename" ) );
	if (!in_array(strtolower($extension),$allowed))
	{
		$contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes','Failed to store file. Only the following file types are allowed: %1.' ), implode(", ",$allowed) );
		return eZInputValidator::STATE_INVALID;
	}

	//Check size
	$mustUpload = false;
        $maxSize = 1024 * 1024 * $classAttribute->attribute( self::MAX_FILESIZE_FIELD );

/* Since it is not an ezbinary file this can never be true
   unfortunately, this is where the check would be to not upload the file
   multiple times in the event the form fails somewhere.  Unfortunately it
   can't be a binary file since it is a collection object and not a content
   object.
	if ( $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $version = $contentObjectAttribute->attribute( "version" );
            $binary = eZBinaryFile::fetch( $contentObjectAttributeID, $version );
            if ( $binary === null )
            {
                $mustUpload = true;
            }
        }
*/
        $canFetchResult = EnhancedeZBinaryFileType::canFetch( $base . "_data_enhancedbinaryfilename_" . $contentObjectAttribute->attribute( "id" ), $maxSize );
	//$binaryfile doesn't have an attribute(http_name)
        if ( $mustUpload && $canFetchResult === eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'A valid file is required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult == eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'The size of the uploaded file exceeds the limit set by the upload_max_filesize directive in php.ini.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        if ( $canFetchResult == eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'The size of the uploaded file exceeds the maximum upload size: %1 bytes.' ), $maxSize );
            return eZInputValidator::STATE_INVALID;
        }
	//Dropped all the way through with no error
        return eZInputValidator::STATE_ACCEPTED;
    }
    /*!
     \return true if the HTTP file $http_name can be fetched. If $maxSize is given,
     the function returns
        0 (eZHTTPFile::UPLOADEDFILE_OK) if the file can be fetched,
       -1 (eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST) if there has been no file uploaded,
       -2 (eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT) if the file was uploaded but size
          exceeds the upload_max_size limit (set in the PHP configuration),
       -3 (eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE) if the file was uploaded but size
          exceeds $maxSize or MAX_FILE_SIZE variable in the form.
    */
    function canFetch( $http_name, $maxSize = false )
    {
       if ( isset( $GLOBALS["eZHTTPFile-$http_name"] ) AND
           $GLOBALS["eZHTTPFile-$http_name"] instanceof eZHTTPFile  )
        {
        //No idea why it's the opposite from ezHTTPFile::canfetch
            if ( $maxSize === false )
            {
                return isset( $_FILES[$http_name] ) and $_FILES[$http_name]['name'] != "" and $_FILES[$http_name]['error'] == 0;
            }

            if ( isset( $_FILES[$http_name] ) and $_FILES[$http_name]['name'] != "" )
            {
                switch ( $_FILES[$http_name]['error'] )
                {
                    case ( UPLOAD_ERR_NO_FILE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST;
                    }break;

                    case ( UPLOAD_ERR_INI_SIZE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT;
                    }break;

                    case ( UPLOAD_ERR_FORM_SIZE ):
                    {
                        return eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE;
                    }break;

                    default:
                    {
                        return ( $maxSize == 0 || $_FILES[$http_name]['size'] <= $maxSize )? eZHTTPFile::UPLOADEDFILE_OK:
                        eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE;
                    }
                }
            }
            else
            {
                return eZHTTPFile::UPLOADEDFILE_DOES_NOT_EXIST;
            }
        }
        if ( $maxSize === false )
            return eZHTTPFile::UPLOADEDFILE_OK;
        else
            return true;
    }

    /*!
     \return the XML structure in \a $domDocument as text.
             It will take of care of the necessary charset conversions
             for content storage.
    */
    function domString( $domDocument )
    {
        $ini = eZINI::instance();
        $xmlCharset = $ini->variable( 'RegionalSettings', 'ContentXMLCharset' );
        if ( $xmlCharset == 'enabled' )
        {
            $charset = eZTextCodec::internalCharset();
        }
        else if ( $xmlCharset == 'disabled' )
            $charset = true;
        else
            $charset = $xmlCharset;
        if ( $charset !== true )
        {
            $charset = eZCharsetInfo::realCharsetCode( $charset );
        }
        $domString = $domDocument->saveXML();
        return $domString;
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

    /*!
     \reimp
    */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $dom = $attributeParametersNode->ownerDocument;
        $maxSize = $classAttribute->attribute( self::MAX_FILESIZE_FIELD );
        $maxSizeNode = $dom->createElement( 'max-size', $maxSize );
        $maxSizeNode->setAttribute( 'unit-size', 'mega' );
        $attributeParametersNode->appendChild( $maxSizeNode );
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $sizeNode = $attributeParametersNode->getElementsByTagName( 'max-size' )->item( 0 );
        $maxSize = $sizeNode->textContent;
        $unitSize = $sizeNode->getAttribute( 'unit-size' );
        $classAttribute->setAttribute( self::MAX_FILESIZE_FIELD, $maxSize );
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

        $result = array();
        return $this->insertRegularFile( $objectAttribute->attribute( 'object' ),
                                         $objectAttribute->attribute( 'version' ),
                                         $objectAttribute->attribute( 'language_code' ),
                                         $objectAttribute,
                                         $string,
                                         $result );
    }

    /*!
     \param package
     \param content attribute

     \return a DOM representation of the content object attribute
    */
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

    /*!
     \reimp
     \param package
     \param contentobject attribute object
     \param domnode object
    */
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
            eZDebug::writeError( "The file '$sourcePath' does not exist, cannot initialize file attribute with it",
                                 'EnhancedeZBinaryFileType::unserializeContentObjectAttribute' );
            return false;
        }

        //include_once( 'lib/ezfile/classes/ezdir.php' );
        $ini = eZINI::instance();
        $mimeType = $fileNode->getAttribute( 'mime-type' );
        list( $mimeTypeCategory, $mimeTypeName ) = explode( '/', $mimeType );
        $destinationPath = eZSys::storageDirectory() . '/original/' . $mimeTypeCategory . '/';
        if ( !file_exists( $destinationPath ) )
        {
            $oldumask = umask( 0 );
            if ( !eZDir::mkdir( $destinationPath, eZDir::directoryPermission(), true ) )
            {
                umask( $oldumask );
                return false;
            }
            umask( $oldumask );
        }

        $basename = basename( $fileNode->getAttribute( 'filename' ) );
        while ( file_exists( $destinationPath . $basename ) )
        {
            $basename = substr( md5( mt_rand() ), 0, 8 ) . '.' . eZFile::suffix( $fileNode->getAttribute( 'filename' ) );
        }

        //include_once( 'lib/ezfile/classes/ezfilehandler.php' );
        eZFileHandler::copy( $sourcePath, $destinationPath . $basename );
        eZDebug::writeNotice( 'Copied: ' . $sourcePath . ' to: ' . $destinationPath . $basename,
                              'EnhancedeZBinaryFileType::unserializeContentObjectAttribute()' );

        $binaryFile->setAttribute( 'contentobject_attribute_id', $objectAttribute->attribute( 'id' ) );
        $binaryFile->setAttribute( 'filename', $basename );
        $binaryFile->setAttribute( 'original_filename', $fileNode->getAttribute( 'original-filename' ) );
        $binaryFile->setAttribute( 'mime_type', $fileNode->getAttribute( 'mime-type' ) );

        $binaryFile->store();

        // VS-DBFILE + SP DBFile fix

        require_once( 'kernel/classes/ezclusterfilehandler.php' );
        $fileHandler = eZClusterFileHandler::instance();
        $fileHandler->fileStore( $destinationPath . $basename, 'binaryfile', true );
    }

}

eZDataType::register( EnhancedeZBinaryFileType::DATA_TYPE_STRING, "enhancedezbinaryfiletype" );

?>
