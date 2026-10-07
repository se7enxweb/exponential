<?php
/**
 * The code of extension/ezoe/modules/ezoe/upload.php, moved into a class (#207 stage 1). The file extension/ezoe/modules/ezoe/upload.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezoe\Ezoe
{

class Upload extends \Exponential\Runnable\ModuleView
{
    /**
     * Whether the current user may upload into version $versionNumber of $object: an upload creates an object and
     * makes it a relation of that version, so it is a write and needs what content/edit needs
     * (Dialog::mayEditVersion(): a draft of the user's own, edit access in its language, not in the trash).
     *
     * @param \eZContentObject|null $object
     * @param int $versionNumber
     * @return bool
     */
    public static function mayUpload( $object, $versionNumber )
    {
        return Dialog::mayEditVersion( $object, $versionNumber );
    }

    /**
     * Makes the uploaded object an embed relation of the version being edited ($versionNumber of $object), not of
     * whatever version number the new object happens to have.
     *
     * @param \eZContentObject $object The object being edited
     * @param int $versionNumber The version being edited
     * @param int $newObjectID The uploaded object
     */
    public static function addEmbedRelation( $object, $versionNumber, $newObjectID )
    {
        $object->addContentObjectRelation( (int)$newObjectID, (int)$versionNumber, 0, \eZContentObject::RELATION_EMBED );
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $objectID        = isset( $Params['ObjectID'] ) ? (int) $Params['ObjectID'] : 0;
        $objectVersion   = isset( $Params['ObjectVersion'] ) ? (int) $Params['ObjectVersion'] : 0;
        $forcedUpload    = isset( $Params['ForcedUpload'] ) ? (int) $Params['ForcedUpload'] : 0;

        // Supported content types: image, media and file
        // Media is threated as file for now
        $contentType   = 'objects';

        if ( isset( $Params['ContentType'] ) && $Params['ContentType'] !== '' )
        {
            $contentType   = $Params['ContentType'];
        }


        if ( $objectID === 0  || $objectVersion === 0 )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid or missing parameter: %parameter', null, array( '%parameter' => 'ObjectID/ObjectVersion' ) );
           \eZExecution::cleanExit();
        }

        // The content type goes into the path of the template (design:ezoe/upload_<type>.tpl): a known one only
        if ( !Dialog::isContentType( $contentType ) )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid or missing parameter: %parameter', null, array( '%parameter' => 'ContentType' ) );
           \eZExecution::cleanExit();
        }


        $user = \eZUser::currentUser();
        if ( $user instanceOf \eZUser )
        {
            $result = $user->hasAccessTo( 'ezoe', 'relations' );
        }
        else
        {
            $result = array('accessWord' => 'no');
        }

        if ( $result['accessWord'] === 'no' )
        {
           echo \ezpI18n::tr( 'design/standard/error/kernel', 'Your current user does not have the proper privileges to access this page.' );
           \eZExecution::cleanExit();
        }



        $object    = \eZContentObject::fetch( $objectID );
        $http      = \eZHTTPTool::instance();
        $imageIni  = \eZINI::instance( 'image.ini' );
        $params    = array('dataMap' => array('image'));


        // The dialog (the form, and what the version already relates to) opens by the rule of the dialogs and edit
        // access to the object (Dialog::mayOpenForEditing()). The version being edited goes along, so an extension can
        // let further editors of the draft in (filter content/edit/access)
        if ( !Dialog::mayOpenForEditing( $object, $objectVersion ) )
        {
           echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid parameter: %parameter = %value', null, array( '%parameter' => 'ObjectId', '%value' => $objectID ) );
           \eZExecution::cleanExit();
        }


        // is this a upload?
        // forcedUpload is needed since hasPostVariable returns false if post size exceeds
        // allowed size set in max_post_size in php.ini
        if ( $http->hasPostVariable( 'uploadButton' ) || $forcedUpload )
        {
            $version   = \eZContentObjectVersion::fetchVersion( $objectVersion, $objectID );
            // An upload writes into the version being edited (the new object becomes a relation of it): only into a
            // draft of the current user's own that they may edit, as content/edit decides it (Dialog::mayEditVersion()),
            // never into someone else's draft or a published or archived version. Checked before anything is fetched
            // or created.
            if ( !$version || !self::mayUpload( $object, $objectVersion ) )
            {
                echo \ezpI18n::tr( 'design/standard/ezoe', 'Invalid parameter: %parameter = %value', null, array( '%parameter' => 'ObjectVersion', '%value' => $objectVersion ) );
                \eZExecution::cleanExit();
            }
            // The file types the embed dialog's upload accepts (ezoe.ini [EditorSettings] UploadFileExtensions) are
            // enforced here, not only in the browser: a request can skip the dialog
            $sentName = isset( $_FILES['fileName']['name'] ) ? (string) $_FILES['fileName']['name'] : '';
            // "From a URL": the server fetches the file, a chosen file from the computer wins
            $uploadUrl = $sentName === '' ? trim( (string) $http->postVariable( 'uploadUrl', '' ) ) : '';
            $fetched = false;
            if ( $uploadUrl !== '' )
            {
                try
                {
                    $fetched = \expOEUrlFetcher::fetch( $uploadUrl );
                }
                catch ( \expOEUrlException $e )
                {
                    echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
                    echo 'window.parent.document.getElementById("upload_in_progress").style.display = "none";';
                    echo '</script></head><body><div style="position:absolute; top: 0px; left: 0px;background-color: white; width: 100%;">';
                    echo '<p style="margin: 0; padding: 3px; color: red">' . htmlspecialchars( $e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</p>';
                    echo '</div></body></html>';
                    \eZExecution::cleanExit();
                }
            }
            if ( $fetched === false && $sentName !== '' && !\expOEEditor::uploadExtensionAllowed( $sentName ) )
            {
                echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
                echo 'window.parent.document.getElementById("upload_in_progress").style.display = "none";';
                echo '</script></head><body><div style="position:absolute; top: 0px; left: 0px;background-color: white; width: 100%;">';
                echo '<p style="margin: 0; padding: 3px; color: red">' . htmlspecialchars( \ezpI18n::tr( 'design/standard/ezoe', 'This file type is not accepted by the editor: %file', null, array( '%file' => basename( $sentName ) ) ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</p>';
                echo '</div></body></html>';
                \eZExecution::cleanExit();
            }
            $upload = new \eZContentUpload();

            $location = false;
            if ( $http->hasPostVariable( 'location' ) )
            {
                $location = $http->postVariable( 'location' );
                if ( $location === 'auto' || trim( $location ) === '' ) $location = false;
            }

            $objectName = '';
            if ( $http->hasPostVariable( 'objectName' ) )
            {
                $objectName = trim( $http->postVariable( 'objectName' ) );
            }

            try
            {
                if ( $fetched !== false )
                {
                    // the same creation path as a browser upload, from the checked temporary file
                    $uploadedOk = $upload->handleLocalFile(
                        $result,
                        $fetched['path'],
                        $location,
                        false,
                        $objectName,
                        $version->attribute( 'initial_language' )->attribute( 'locale' ),
                        false
                    );
                }
                else
                {
                    $uploadedOk = $upload->handleUpload(
                        $result,
                        'fileName',
                        $location,
                        false,
                        $objectName,
                        $version->attribute( 'initial_language' )->attribute( 'locale' ),
                        false
                    );
                }
                if ( !$uploadedOk )
                {
                    throw new \RuntimeException( "Upload failed" );
                }

                $uploadVersion = $uploadedOk['contentobject']->currentVersion();
                $newObjectID = (int)$uploadedOk['contentobject']->attribute( 'id' );

                foreach ( $uploadVersion->dataMap() as $key => $attr )
                {
                    //post pattern: ContentObjectAttribute_attribute-identifier
                    $base = 'ContentObjectAttribute_'. $key;
                    $postVar = trim( $http->postVariable( $base, '' ) );
                    if ( $postVar !== '' )
                    {
                        switch ( $attr->attribute( 'data_type_string' ) )
                        {
                            case 'ezstring':
                                $classAttr = $attr->attribute( 'contentclass_attribute' );
                                $dataType = $classAttr->attribute( 'data_type' );
                                if ( $dataType->validateStringHTTPInput( $postVar, $attr, $classAttr ) !== \eZInputValidator::STATE_ACCEPTED )
                                {
                                    throw new \InvalidArgumentException( $attr->validationError() );
                                }
                            case 'eztext':
                            case 'ezkeyword':
                                $attr->fromString( $postVar );
                                $attr->store();
                                break;
                            case 'ezfloat':
                                $floatValue = (float)str_replace( ',', '.', $postVar );
                                $classAttr = $attr->attribute( 'contentclass_attribute' );
                                $dataType = $classAttr->attribute( 'data_type' );
                                if ( $dataType->validateFloatHTTPInput( $floatValue, $attr, $classAttr ) !== \eZInputValidator::STATE_ACCEPTED )
                                {
                                    throw new \InvalidArgumentException( $attr->validationError() );
                                }
                                $attr->setAttribute( 'data_float', $floatValue );
                                $attr->store();
                                break;
                            case 'ezinteger':
                                $classAttr = $attr->attribute( 'contentclass_attribute' );
                                $dataType = $classAttr->attribute( 'data_type' );
                                if ( $dataType->validateIntegerHTTPInput( $postVar, $attr, $classAttr ) !== \eZInputValidator::STATE_ACCEPTED )
                                {
                                    throw new \InvalidArgumentException( $attr->validationError() );
                                }
                            case 'ezboolean':
                                $attr->setAttribute( 'data_int', (int)$postVar );
                                $attr->store();
                                break;
                            case 'ezimage':
                                // validation has been done by eZContentUpload
                                $content = $attr->attribute( 'content' );
                                $content->setAttribute( 'alternative_text', $postVar );
                                $content->store( $attr );
                                break;
                            case 'ezxmltext':
                                $parser = new \eZOEInputParser();
                                $document = $parser->process( $postVar );
                                $xmlString = \eZXMLTextType::domString( $document );
                                $attr->setAttribute( 'data_text', $xmlString );
                                $attr->store();
                                break;
                        }
                    }
                }

                $operationResult = \eZOperationHandler::execute(
                    'content', 'publish',
                    array(
                        'object_id' => $newObjectID,
                        'version' => $uploadVersion->attribute( 'version' )
                    )
                );
                $newObject = \eZContentObject::fetch( $newObjectID );
                $newObjectName = $newObject->attribute( 'name' );
                $newObjectNodeID = (int)$newObject->attribute( 'main_node_id' );

                self::addEmbedRelation( $object, $objectVersion, $newObjectID );
                if ( $fetched !== false && class_exists( 'expAudit' ) )
                {
                    \expAudit::event( 'content.ezoe.upload.url', array(
                        'object' => array( 'type' => 'content_object', 'id' => $newObjectID ),
                        'after'  => array( 'url' => $uploadUrl, 'file' => $fetched['name'], 'size' => $fetched['size'], 'type' => $fetched['type'] ),
                        'result' => 'success' ) );
                }
                echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
                echo 'window.parent.eZOEPopupUtils.selectByEmbedId( ' . $newObjectID . ', ' . $newObjectNodeID . ', ' . json_encode( $newObjectName ) . ' );';
                echo '</script></head><body></body></html>';
            }
            catch ( \InvalidArgumentException $e )
            {
                $uploadedOk['contentobject']->purge();
                echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
                echo 'window.parent.document.getElementById("upload_in_progress").style.display = "none";';
                echo '</script></head><body><div style="position:absolute; top: 0px; left: 0px;background-color: white; width: 100%;">';
                echo '<p style="margin: 0; padding: 3px; color: red">' . htmlspecialchars( $e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</p>';
                echo '</div></body></html>';
            }
            catch ( \RuntimeException $e )
            {
                echo '<html><head><title>HiddenUploadFrame</title><script type="text/javascript">';
                echo 'window.parent.document.getElementById("upload_in_progress").style.display = "none";';
                echo '</script></head><body><div style="position:absolute; top: 0px; left: 0px;background-color: white; width: 100%;">';
                foreach( $result['errors'] as $err )
                    echo '<p style="margin: 0; padding: 3px; color: red">' . htmlspecialchars( $err['description'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</p>';
                echo '</div></body></html>';
            }
            finally
            {
                // the temporary download never outlives the request
                if ( $fetched !== false )
                    \expOEUrlFetcher::cleanup( $fetched );
            }
            \eZDB::checkTransactionCounter();
            \eZExecution::cleanExit();
        }


        $siteIni       = \eZINI::instance( 'site.ini' );
        $contentIni    = \eZINI::instance( 'content.ini' );

        $groups             = $contentIni->variable( 'RelationGroupSettings', 'Groups' );
        $defaultGroup       = $contentIni->variable( 'RelationGroupSettings', 'DefaultGroup' );
        $imageDatatypeArray = $siteIni->variable( 'ImageDataTypeSettings', 'AvailableImageDataTypes' );

        $classGroupMap         = array();
        $groupClassLists       = array();
        $groupedRelatedObjects = array();
        $relatedObjects        = $object->relatedContentObjectArray( $objectVersion );
        // $hasContentTypeGroup   = false;
        // $contentTypeGroupName  = $contentType . 's';

        foreach ( $groups as $groupName )
        {
            $groupedRelatedObjects[$groupName] = array();
            $setting                     = ucfirst( $groupName ) . 'ClassList';
            $groupClassLists[$groupName] = $contentIni->variable( 'RelationGroupSettings', $setting );
            foreach ( $groupClassLists[$groupName] as $classIdentifier )
            {
                $classGroupMap[$classIdentifier] = $groupName;
                // if ( $contentTypeGroupName  === $groupName ) $hasContentTypeGroup = true;
            }
        }

        $groupedRelatedObjects[$defaultGroup] = array();

        foreach ( $relatedObjects as $relatedObjectKey => $relatedObject )
        {
            $srcString        = '';
            $imageAttribute   = false;
            $relID            = $relatedObject->attribute( 'id' );
            $classIdentifier  = $relatedObject->attribute( 'class_identifier' );
            $groupName        = isset( $classGroupMap[$classIdentifier] ) ? $classGroupMap[$classIdentifier] : $defaultGroup;

            // if ( $hasContentTypeGroup === true && $contentTypeGroupName !== $groupName ) continue;

            if ( $groupName === 'images' )
            {
                $objectAttributes = $relatedObject->contentObjectAttributes();
                foreach ( $objectAttributes as $objectAttribute )
                {
                    $classAttribute = $objectAttribute->contentClassAttribute();
                    $dataTypeString = $classAttribute->attribute( 'data_type_string' );
                    if ( in_array ( $dataTypeString, $imageDatatypeArray ) && $objectAttribute->hasContent() )
                    {
                        $content = $objectAttribute->content();
                        if ( $content == null )
                            continue;

                        if ( $content->hasAttribute( 'small' ) )
                        {
                            $srcString = $content->imageAlias( 'small' );
                            $imageAttribute = $classAttribute->attribute('identifier');
                            break;
                        }
                        else
                        {
                            \eZDebug::writeError( "Image alias does not exist: small, missing from image.ini?",
                                __METHOD__ );
                        }
                    }
                }
            }
            $item = array( 'object' => $relatedObjects[$relatedObjectKey],
                           'id' => 'eZObject_' . $relID,
                           'image_alias' => $srcString,
                           'image_attribute' => $imageAttribute,
                           'selected' => false );
            $groupedRelatedObjects[$groupName][] = $item;
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'object', $object );
        $tpl->setVariable( 'object_id', $objectID );
        $tpl->setVariable( 'object_version', $objectVersion );
        $tpl->setVariable( 'related_contentobjects', $relatedObjects );
        $tpl->setVariable( 'grouped_related_contentobjects', $groupedRelatedObjects );
        $tpl->setVariable( 'content_type', $contentType );

        $contentTypeCase = ucfirst( $contentType );
        if ( $contentIni->hasVariable( 'RelationGroupSettings', $contentTypeCase . 'ClassList' ) )
            $tpl->setVariable( 'class_filter_array', $contentIni->variable( 'RelationGroupSettings', $contentTypeCase . 'ClassList' ) );
        else
            $tpl->setVariable( 'class_filter_array', array() );

        $tpl->setVariable( 'content_type_name', rtrim( $contentTypeCase, 's' ) );

        $tpl->setVariable( 'persistent_variable', array() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:ezoe/upload_' . $contentType . '.tpl' );
        $Result['pagelayout'] = 'design:ezoe/popup_pagelayout.tpl';
        $Result['persistent_variable'] = $tpl->variable( 'persistent_variable' );

        return $this->viewResult( isset( $Result ) ? $Result : null,  $Result );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
