<?php
/**
 * File containing the ezjscServerFunctionsAjaxUploader class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezjscore
 * @subpackage ajaxuploader
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit c8e45fced7), Copyright (C) Mugo Web, GPL v2
 */

/**
 * This class handles AJAX calls for the ajax uploader component
 * @package ezjscore
 * @subpackage ajaxuploader
 */
class ezjscServerFunctionsAjaxUploader extends ezjscServerFunctions
{
    /** The session variable that holds the files stored by upload() until preview() takes them */
    const PENDING_SESSION_KEY = 'ezjscAjaxUploaderPending';

    /** At most this many stored files wait for their location per session; the oldest is forgotten first */
    const PENDING_MAX = 20;

    /**
     * What a stored file is bound to: the user, the upload handler and its data (the attribute and version).
     *
     * @param array $args the arguments of the ezjscore ajax function
     * @param int $userID
     * @return string
     */
    public static function pendingBinding( array $args, $userID )
    {
        $http = eZHTTPTool::instance();
        $handlerData = $http->hasPostVariable( 'AjaxUploadHandlerData' ) ? $http->postVariable( 'AjaxUploadHandlerData' ) : array();
        if ( !is_array( $handlerData ) )
            $handlerData = array();
        ksort( $handlerData );
        return hash( 'sha256', json_encode( array( (int)$userID, isset( $args[0] ) ? (string)$args[0] : '', array_map( 'strval', array_filter( $handlerData, 'is_scalar' ) ) ) ) );
    }

    /** A stored file waits this many seconds for its location; then its token is refused and forgotten */
    const PENDING_LIFETIME = 3600;

    /**
     * $store with the stored $file under a new random token, without the expired entries and at most PENDING_MAX
     * entries (the oldest forgotten first). The files of the entries it drops are returned, for the caller to
     * delete: nobody can take them any more.
     *
     * @param array $store token => entry
     * @param string $file the path upload() stored the file at
     * @param string $originalFilename
     * @param string $binding pendingBinding()
     * @param int|null $now the time (tests); null for time()
     * @return array array( new store, token, files of the dropped entries )
     */
    public static function rememberPending( array $store, $file, $originalFilename, $binding, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        $dropped = array();
        foreach ( $store as $key => $entry )
        {
            if ( !self::isLive( $entry, $now ) )
            {
                if ( is_array( $entry ) && isset( $entry['file'] ) )
                    $dropped[] = (string)$entry['file'];
                unset( $store[$key] );
            }
        }
        $token = bin2hex( random_bytes( 16 ) );
        $store[$token] = array( 'file' => (string)$file, 'original_filename' => (string)$originalFilename,
                                'binding' => (string)$binding, 'time' => $now );
        while ( count( $store ) > self::PENDING_MAX )
        {
            $evicted = array_shift( $store );
            if ( is_array( $evicted ) && isset( $evicted['file'] ) )
                $dropped[] = (string)$evicted['file'];
        }
        return array( $store, $token, $dropped );
    }

    /**
     * The entry of $token in $store when it was made for $binding and has not expired (PENDING_LIFETIME), else null.
     *
     * @param array $store
     * @param mixed $token
     * @param string $binding
     * @param int|null $now the time (tests); null for time()
     * @return array|null
     */
    public static function pendingEntry( array $store, $token, $binding, $now = null )
    {
        if ( !is_string( $token ) || !preg_match( '/^[0-9a-f]{32}$/', $token ) || !isset( $store[$token] ) )
            return null;
        $entry = $store[$token];
        if ( !is_array( $entry ) || !isset( $entry['binding'], $entry['file'] ) || !hash_equals( (string)$entry['binding'], (string)$binding ) )
            return null;
        if ( !self::isLive( $entry, $now === null ? time() : (int)$now ) )
            return null;
        return $entry;
    }

    /** Whether a stored entry is well formed and younger than PENDING_LIFETIME at $now */
    private static function isLive( $entry, $now )
    {
        return is_array( $entry ) && isset( $entry['time'] ) && is_int( $entry['time'] )
            && $entry['time'] <= $now && $now - $entry['time'] < self::PENDING_LIFETIME;
    }

    /** Deletes the stored files of dropped entries, on the cluster and locally, when they are upload paths */
    private static function deleteDropped( array $files )
    {
        if ( count( $files ) === 0 )
            return;
        $fileHandler = eZClusterFileHandler::instance();
        $storageDir = eZSys::storageDirectory();
        foreach ( $files as $file )
        {
            if ( !self::isStoredUploadPath( $file, $storageDir ) )
                continue;
            $fileHandler->fileDelete( $file );
            $fileHandler->fileDeleteLocal( $file );
        }
    }

    /**
     * Whether $file is a path upload() can have stored: a file below $storageDir, without "." or ".." parts.
     * With $mustExist, the real path is checked too (after the file was fetched from the cluster).
     *
     * @param string $file
     * @param string $storageDir eZSys::storageDirectory()
     * @param bool $mustExist
     * @return bool
     */
    public static function isStoredUploadPath( $file, $storageDir, $mustExist = false )
    {
        $file = (string)$file;
        $storageDir = rtrim( (string)$storageDir, '/' );
        if ( $file === '' || $storageDir === '' || strpos( $file, "\0" ) !== false || strpos( $file, '\\' ) !== false )
            return false;
        if ( strpos( $file, $storageDir . '/' ) !== 0 )
            return false;
        foreach ( explode( '/', substr( $file, strlen( $storageDir ) + 1 ) ) as $part )
        {
            if ( $part === '' || $part === '.' || $part === '..' )
                return false;
        }
        if ( !$mustExist )
            return true;
        $realFile = realpath( $file );
        $realDir = realpath( $storageDir );
        return $realFile !== false && $realDir !== false && is_file( $realFile ) && strpos( $realFile, rtrim( $realDir, '/' ) . '/' ) === 0;
    }

    /** @return int the user a stored file is bound to */
    protected static function currentUserID()
    {
        return (int)eZUser::currentUserID();
    }

    /** @return array the stored files of this session waiting for their location */
    private static function pendingStore()
    {
        $http = eZHTTPTool::instance();
        $store = $http->hasSessionVariable( self::PENDING_SESSION_KEY ) ? $http->sessionVariable( self::PENDING_SESSION_KEY ) : array();
        return is_array( $store ) ? $store : array();
    }

    /** A file name for the temporary copy: no directory, never empty, "." or ".." */
    private static function safeFilename( $name )
    {
        $name = str_replace( array( '/', '\\', "\0" ), '_', (string)$name );
        $name = trim( $name );
        if ( $name === '' || $name === '.' || $name === '..' )
            $name = 'upload';
        return $name;
    }


    /**
     * Returns an ajaxuploader handler instance from the ezjscore function
     * arguments.
     *
     * @param array $args the arguments of the ezjscore ajax function
     * @return ezpAjaxUploaderHandlerInterface
     *
     * @throws InvalidArgumentException if the handler cannot be instanciated
     */
    private static function getHandler( array $args )
    {
        if ( !isset( $args[0] ) )
        {
            throw new InvalidArgumentException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader',
                    'Unable to find the identifier of ajax upload handler'
                )
            );
        }

        $http = eZHTTPTool::instance();
        $handlerData = $http->postVariable( 'AjaxUploadHandlerData', array() );

        $handlerOptions = new ezpExtensionOptions();
        $handlerOptions->iniFile = 'ezjscore.ini';
        $handlerOptions->iniSection = 'AjaxUploader';
        $handlerOptions->iniVariable = 'AjaxUploadHandler';
        $handlerOptions->handlerIndex = $args[0];
        $handlerOptions->handlerParams = $handlerData;

        $handler = eZExtension::getHandlerClass( $handlerOptions );

        if ( !$handler instanceof ezpAjaxUploaderHandlerInterface )
        {
            throw new InvalidArgumentException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader',
                    'Unable to load the ajax upload handler'
                )
            );
        }
        return $handler;
    }

    /**
     * Returns the upload form
     *
     * @param array $args ezjscore function arguments, the first element is the AJAX
     *                      upload handler identifier ({@link
     *                      ezjscServerFunctionsAjaxUploader::getHandler})
     * @return array( 'meta_data' => false, 'html' => string)
     *
     * @throw RuntimeException if the user is not allowed to upload a file
     */
    static function uploadForm( $args )
    {
        $handler = self::getHandler( $args );
        if ( !$handler->canUpload() )
        {
            throw new RuntimeException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader',
                    'You are not allowed to upload a file.'
                )
            );
        }
        $tpl = eZTemplate::factory();
        return array(
            'meta_data' => false,
            'html' => $tpl->fetch( 'design:ajaxuploader/upload.tpl' )
        );
    }

    /**
     * Why the uploaded $file cannot become an object of $class, or false when it can: an image
     * attribute takes only a file whose type by content is on image.ini [ImageUploadSettings]
     * AllowedMIMETypes[] (see eZContentUpload::imageUploadError()).
     *
     * @param eZContentClass $class
     * @param eZHTTPFile $file
     * @return string|false
     */
    private static function imageUploadError( eZContentClass $class, eZHTTPFile $file )
    {
        $uploadINI = eZINI::instance( 'upload.ini' );
        $group = $class->attribute( 'identifier' ) . '_ClassSettings';
        $fileAttribute = $uploadINI->hasVariable( $group, 'FileAttribute' ) ? $uploadINI->variable( $group, 'FileAttribute' ) : false;
        $dataMap = $class->dataMap();
        $upload = new eZContentUpload();
        $fileAttribute = $upload->findHTTPFileAttribute( $dataMap, $fileAttribute );
        if ( !$fileAttribute )
            return false;
        return eZContentUpload::imageUploadError( $dataMap, $fileAttribute,
                                                  $file->attribute( 'filename' ), $file->attribute( 'original_filename' ) );
    }

    /**
     * Stores the uploaded file and returns the location form. The result of
     * this method is always json encoded.
     *
     * @param array $args
     * @return string a json encoded array
     */
    static function upload( $args )
    {
        try
        {
            $handler = self::getHandler( $args );
            if ( !$handler->canUpload() )
            {
                throw new RuntimeException(
                    ezpI18n::tr(
                        'extension/ezjscore/ajaxuploader',
                        'You are not allowed to upload a file.'
                    )
                );
            }
            $fileInfo = $handler->getFileinfo();
            $mimeData = $fileInfo['mime'];
            $file = $fileInfo['file'];
            $class = $handler->getContentClass( $mimeData );

            // An image is checked by its content before the file is stored
            $imageError = self::imageUploadError( $class, $file );
            if ( $imageError !== false )
            {
                throw new RuntimeException( $imageError );
            }

            if ( $file->store( false, $mimeData['suffix'] ) === false )
            {
                throw new RuntimeException(
                    ezpI18n::tr(
                        'extension/ezjscore/ajaxuploader',
                        'Unable to store the uploaded file.'
                    )
                );
            }
            else
            {
                $fileHandler = eZClusterFileHandler::instance();
                $fileHandler->fileStore( $file->attribute( 'filename' ), 'tmp', true, $file->attribute( 'mime_type' ) );
            }

            $start = $handler->getDefaultParentNodeId( $class );
            $defaultParentNode = eZContentObjectTreeNode::fetch( $start );
            if ( !$defaultParentNode instanceof eZContentObjectTreeNode )
            {
                throw new RuntimeException(
                    ezpI18n::tr(
                        "extension/ezjscore/ajaxuploader",
                        "The default parent location for uploads cannot be retrieved! Check user permissions and correctness of settings."
                    )
                );
            }
        }
        catch ( Exception $e )
        {
            // manually catch exception to force json encode
            // because most browsers cannot upload
            // wit a json HTTP Accept header...
            // see JavaScript code in eZAjaxUploader::delegateWindowEvents();
            return json_encode(
                array(
                    'error_text' => $e->getMessage(),
                    'content' => ''
                )
            );
        }

        $browseItems = self::getBrowseItems( $defaultParentNode->attribute( 'parent' ), $class );

        $http = eZHTTPTool::instance();
        // the stored file's path stays on the server: the location form carries a token for it, bound to this
        // user and this upload target, taken once by preview()
        list( $store, $uploadToken, $dropped ) = self::rememberPending( self::pendingStore(), $file->attribute( 'filename' ),
                                                                        $file->attribute( 'original_filename' ),
                                                                        self::pendingBinding( $args, static::currentUserID() ) );
        $http->setSessionVariable( self::PENDING_SESSION_KEY, $store );
        // the files of expired or evicted entries can no longer be taken: they leave the storage
        self::deleteDropped( $dropped );

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'file', $file );
        $tpl->setVariable( 'upload_token', $uploadToken );
        $tpl->setVariable( 'name', $http->postVariable( 'UploadName', $file->attribute( 'original_filename' ) ) );
        $tpl->setVariable( 'mime_data', $mimeData );
        $tpl->setVariable( 'class', $class );
        $tpl->setVariable( 'browse_start', $defaultParentNode->attribute( 'parent' ) );
        $tpl->setVariable( 'default_parent_node', $defaultParentNode );
        $tpl->setVariable( 'browse', $browseItems );
        // An alternative text field for a class with an image attribute, marked when the class requires the text
        $altTextRequired = eZImageType::classAltTextRequired( $class );
        $tpl->setVariable( 'alt_text_field', $altTextRequired !== null );
        $tpl->setVariable( 'alt_text_required', $altTextRequired === true );
        // json_encode and url encode the HTML to be able to get it in the
        // JavaScript code. see eZAjaxUploader::delegateWindowEvents()
        return json_encode(
            array(
                'meta_data' => false,
                'html' => rawurlencode(
                    $tpl->fetch( 'design:ajaxuploader/location.tpl' )
                )
            )
        );
    }

    /**
     * Returns a struct containing the following values:
     *      - limit the number of element by page
     *      - offset the current offset
     *      - items array, each element contains
     *          - has_child boolean
     *          - can_create boolean
     *          - node eZContentObjectTreeNode
     *      - has_prev boolean, true if there's a previous page
     *      - has_next boolean, true if there's a next page
     *
     * @param eZContentObjectTreeNode $start the node where the browse will start
     * @param eZContentClass $class class of the object to be created
     * @param int $offset
     * @return array
     */
    private static function getBrowseItems( eZContentObjectTreeNode $start, eZContentClass $class, $offset = 0 )
    {
        $result = array(
            'limit' => 10,
            'offset' => $offset,
            'items' => array(),
            'has_prev' => ( $offset != 0 ),
            'has_next' => false
        );
        $containerClasses = eZPersistentObject::fetchObjectList(
            eZContentClass::definition(), null,
            array(
                'version' => eZContentClass::VERSION_STATUS_DEFINED,
                'is_container' => 1
            )
        );
        $classFilterArray = array();
        foreach ( $containerClasses as $c )
        {
            $classFilterArray[] = $c->attribute( 'identifier' );
        }
        $children = $start->subTree(
            array(
                'ClassFilterArray' => $classFilterArray,
                'ClassFilterType' => 'include',
                'Depth' => 1,
                'Limit' => $result['limit'],
                'Offset' => $offset
            )
        );
        $count = $start->subTreeCount(
            array(
                'ClassFilterArray' => $classFilterArray,
                'ClassFilterType' => 'include',
                'Depth' => 1
            )
        );
        if ( $count > ( $offset + $result['limit'] ) )
        {
            $result['has_next'] = true;
        }
        foreach( $children as $node )
        {
            $elt = array();
            $elt['node'] = $node;
            $canCreateClassist = $node->canCreateClassList();
            foreach( $canCreateClassist as $c )
            {
                if ( $c['id'] == $class->attribute( 'id' ) )
                {
                    $elt['can_create'] = true;
                    break;
                }
            }
            if ( !isset( $elt['can_create'] ) )
            {
                $elt['can_create'] = false;
            }
            $childrenContainerCount = $node->subTreeCount(
                array(
                    'ClassFilterArray' => $classFilterArray,
                    'ClassFilterType' => 'include',
                    'Depth' => 1
                )
            );
            $elt['has_child'] = ( $childrenContainerCount > 0 );
            $result['items'][] = $elt;
        }
        return $result;
    }

    /**
     * Browse AJAX action
     *
     * @param array $args containing the following values:
     *          - 0 the parent node id of the elements to display
     *          - 1 the class id of the element that is going to be created
     *          - 2 [optional] the offset
     * @return array
     * @throws InvalidArgumentException if the node or the class cannot be loaded
     */
    static function browse( $args )
    {
        if ( count( $args ) < 2 )
        {
            throw new InvalidArgumentException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader', 'Arguments error.'
                )
            );
        }
        list( $nodeID, $classId ) = $args;
        $offset = 0;
        if ( isset( $args[2] ) )
        {
            $offset = (int) $args[2];
        }
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $class = eZContentClass::fetch( $classId );
        if ( !$node instanceof eZContentObjectTreeNode
                || !$class instanceof eZContentClass )
        {
            throw new InvalidArgumentException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader', 'Arguments error.'
                )
            );
        }
        else if ( !$node->canRead() )
        {
            throw new InvalidArgumentException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader', 'Arguments error.'
                )
            );
        }
        $browseItems = self::getBrowseItems( $node, $class, $offset );

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'browse', $browseItems );
        $tpl->setVariable( 'browse_start', $node );
        $tpl->setVariable( 'default_parent_node', array( 'node_id' => 0 ) );
        $tpl->setVariable( 'class', $class );

        return array(
            'meta_data' => false,
            'html' => $tpl->fetch( 'design:ajaxuploader/browse.tpl' )
        );
    }


    /**
     * Refuses the upload when the class the file becomes requires an alternative text for its image and the
     * location form gave none (the class attribute option "Alternative text is required"). Called before the
     * uploaded file is used, so the form can be sent again with the text. $altText is set to the posted text.
     *
     * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit a28863d9c0), Copyright (C) Mugo Web, GPL v2
     *
     * @param ezpAjaxUploaderHandlerInterface $handler
     * @param string $file the uploaded file, fetched to the local file system
     * @param string $altText
     * @throw RuntimeException when an alternative text is required and none was given
     */
    private static function checkAlternativeText( $handler, $file, &$altText )
    {
        $http = eZHTTPTool::instance();
        $altText = $http->hasPostVariable( 'UploadAlternativeText' ) ? $http->postVariable( 'UploadAlternativeText' ) : '';
        $altText = is_scalar( $altText ) ? trim( (string)$altText ) : '';
        try
        {
            $class = $handler->getContentClass( eZMimeType::findByFileContents( $file ) );
        }
        catch ( Exception $e )
        {
            // createObject() reports a file that cannot become an object
            return;
        }
        if ( $class instanceof eZContentClass && eZImageType::classAltTextRequired( $class ) &&
             !eZImageType::validateImageAltText( $altText ) )
        {
            throw new RuntimeException( ezpI18n::tr( 'kernel/classes/datatypes', 'An alternative text is required for this image.' ) );
        }
    }

    /**
     * Gives the image attributes of the new $contentObject the alternative text of the location form.
     *
     * @param eZContentObject $contentObject
     * @param string $altText
     */
    private static function storeAlternativeText( $contentObject, $altText )
    {
        if ( !$contentObject instanceof eZContentObject || $altText === '' )
            return;
        $stored = false;
        foreach ( $contentObject->dataMap() as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) !== eZImageType::DATA_TYPE_STRING || !$attribute->hasContent() )
                continue;
            $content = $attribute->attribute( 'content' );
            $content->setAttribute( 'alternative_text', $altText );
            $content->store( $attribute );
            $stored = true;
        }
        if ( $stored )
        {
            // the object was published before the text was set: index and view caches see it now
            eZSearch::addObject( $contentObject, true );
            eZContentCacheManager::clearContentCacheIfNeeded( $contentObject->attribute( 'id' ) );
        }
    }

    /**
     * Creates the object from the uploaded file and displays the preview of it
     *
     * @param array $args
     * @return array
     * @throw RuntimeException if the previously uploaded file cannot be fetched
     */
    static function preview( $args )
    {
        $http = eZHTTPTool::instance();
        $handler = self::getHandler( $args );
        if ( !$handler->canUpload() )
        {
            throw new RuntimeException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader',
                    'You are not allowed to upload a file.'
                )
            );
        }

        $binding = self::pendingBinding( $args, static::currentUserID() );
        $store = self::pendingStore();
        $token = $http->hasPostVariable( 'UploadToken' ) ? $http->postVariable( 'UploadToken' ) : '';
        $entry = self::pendingEntry( $store, $token, $binding );
        $storageDir = eZSys::storageDirectory();
        $file = $entry !== null ? $entry['file'] : false;
        $fileHandler = eZClusterFileHandler::instance();
        if ( $file === false
                || !self::isStoredUploadPath( $file, $storageDir )
                || !$fileHandler->fileExists( $file )
                || !$fileHandler->fileFetch( $file )
                || !self::isStoredUploadPath( $file, $storageDir, true ) )
        {
            throw new RuntimeException(
                ezpI18n::tr(
                    'extension/ezjscore/ajaxuploader', 'Unable to retrieve the uploaded file.'
                )
            );
        }

        // refused for a missing alternative text: the token stays, so the form can be sent again with the text
        self::checkAlternativeText( $handler, $file, $altText );

        // The object is made from a copy in a directory of this token's own, named after the original file (the
        // object takes its file name from it), so two uploads of "image.jpg" never share a path. The stored file
        // and the token stay until the object exists: a refused location can be chosen again.
        $tmpDir = eZSys::cacheDirectory() . '/' . eZINI::instance()->variable( 'FileSettings', 'TemporaryDir' ) . '/ajaxuploader-' . $token;
        $tmpFile = $tmpDir . '/' . self::safeFilename( $entry['original_filename'] );
        try
        {
            if ( !eZDir::mkdir( $tmpDir, false, true ) || !copy( $file, $tmpFile ) )
            {
                throw new RuntimeException(
                    ezpI18n::tr(
                        'extension/ezjscore/ajaxuploader', 'Unable to retrieve the uploaded file.'
                    )
                );
            }
            $contentObject = $handler->createObject(
                $tmpFile,
                $http->postVariable( 'UploadLocation', false ),
                $http->postVariable( 'UploadName', '' )
            );
            if ( !$contentObject instanceof eZContentObject )
            {
                throw new RuntimeException(
                    ezpI18n::tr(
                        'extension/ezjscore/ajaxuploader', 'Unable to retrieve the uploaded file.'
                    )
                );
            }

            // the token is used once: the object exists, the stored file leaves the storage
            $store = self::pendingStore();
            unset( $store[$token] );
            $http->setSessionVariable( self::PENDING_SESSION_KEY, $store );
            $fileHandler->fileDelete( $file );
            $fileHandler->fileDeleteLocal( $file );

            self::storeAlternativeText( $contentObject, $altText );
        }
        finally
        {
            if ( is_file( $tmpFile ) )
                @unlink( $tmpFile );
            if ( is_dir( $tmpDir ) )
                @rmdir( $tmpDir );
        }

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'object', $contentObject );
        return array(
            'meta_data' => $handler->serializeObject( $contentObject ),
            'html' => $tpl->fetch( 'design:ajaxuploader/preview.tpl' )
        );
    }
}
?>