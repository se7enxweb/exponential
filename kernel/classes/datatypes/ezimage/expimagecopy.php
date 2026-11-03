<?php
/**
 * File containing the expImageCopy class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Takes the image of an existing content object into an image field (ezimage) as a copy.
 *
 * The edit form of an image field offers, besides the upload, "Choose an existing image": the editor
 * browses the content tree or searches by name for an object that has an image, and its image file is
 * copied into the field being edited. The copy is a new file under the edited object's own storage
 * path, stored through the same code as an upload (eZImageAliasHandler::initializeFromFile()), so the
 * field afterwards behaves exactly as if the file had been uploaded: the aliases are made from it, it
 * can be removed or replaced, and nothing refers to the other object. The other object is only read.
 *
 * Checks, all made before anything is written:
 * - the selected object exists and the current user may read it (content/read), told apart from
 *   neither, so the answer does not say whether an object the user may not read exists;
 * - it has an image: the "image" field (ezimage) of the object, else its first image field with a file;
 * - the file is read through the cluster file handler, so it works on a cluster (DFS) as well;
 * - the file passes what an upload must pass: the size limit of the field being edited, the file
 *   name blacklist, an image by name and by content (eZImageType::imageFileError(), SVG only when
 *   accepted, and then sanitized on the way in).
 */
class expImageCopy
{
    /** The identifier of the image field that is preferred when an object has several. */
    const PREFERRED_IDENTIFIER = 'image';

    /** Most objects a search lists. */
    const SEARCH_LIMIT = 12;

    /** Longest search text taken. */
    const SEARCH_TEXT_MAX_LENGTH = 100;

    /**
     * The identifiers of the content classes that have an image field: the classes an editor can pick
     * an image from (browse and search).
     *
     * @return string[]
     */
    public static function imageClassIdentifiers()
    {
        $identifiers = array();
        $classAttributes = eZContentClassAttribute::fetchFilteredList( array( 'data_type_string' => eZImageType::DATA_TYPE_STRING,
                                                                              'version' => eZContentClass::VERSION_STATUS_DEFINED ) );
        foreach ( (array)$classAttributes as $classAttribute )
        {
            $class = eZContentClass::fetch( $classAttribute->attribute( 'contentclass_id' ) );
            if ( $class instanceof eZContentClass )
                $identifiers[$class->attribute( 'identifier' )] = true;
        }
        $identifiers = array_keys( $identifiers );
        sort( $identifiers );
        return $identifiers;
    }

    /**
     * The ids of the content classes that have an image field.
     *
     * @return int[]
     */
    public static function imageClassIDs()
    {
        $ids = array();
        $classAttributes = eZContentClassAttribute::fetchFilteredList( array( 'data_type_string' => eZImageType::DATA_TYPE_STRING,
                                                                              'version' => eZContentClass::VERSION_STATUS_DEFINED ) );
        foreach ( (array)$classAttributes as $classAttribute )
            $ids[(int)$classAttribute->attribute( 'contentclass_id' )] = true;
        return array_keys( $ids );
    }

    /**
     * The object id in $value when it is one: a positive whole number, as a number or as digits.
     *
     * @param mixed $value
     * @return int|false
     */
    public static function objectIDFromInput( $value )
    {
        if ( is_array( $value ) )
            $value = reset( $value );
        if ( is_int( $value ) )
            $value = (string)$value;
        if ( !is_string( $value ) )
            return false;
        $value = trim( $value );
        if ( $value === '' || !ctype_digit( $value ) || strlen( $value ) > 10 || (int)$value <= 0 )
            return false;
        return (int)$value;
    }

    /**
     * The image field of $object that has a file: the field with the identifier "image", else the
     * first image field with a file, in the order of the class. Null when there is none.
     *
     * @param eZContentObject $object
     * @return eZContentObjectAttribute|null
     */
    public static function sourceAttribute( $object )
    {
        if ( !$object instanceof eZContentObject )
            return null;
        $first = null;
        foreach ( (array)$object->contentObjectAttributes() as $attribute )
        {
            if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'data_type_string' ) !== eZImageType::DATA_TYPE_STRING )
                continue;
            if ( !self::attributeHasImageFile( $attribute ) )
                continue;
            if ( $attribute->attribute( 'contentclass_attribute_identifier' ) === self::PREFERRED_IDENTIFIER )
                return $attribute;
            if ( $first === null )
                $first = $attribute;
        }
        return $first;
    }

    /**
     * Whether the image field $attribute holds an image file.
     *
     * @param eZContentObjectAttribute $attribute
     * @return bool
     */
    public static function attributeHasImageFile( $attribute )
    {
        if ( !$attribute instanceof eZContentObjectAttribute || !$attribute->attribute( 'has_content' ) )
            return false;
        $handler = $attribute->attribute( 'content' );
        if ( !$handler instanceof eZImageAliasHandler )
            return false;
        $original = $handler->imageAlias( 'original' );
        return is_array( $original ) && !empty( $original['is_valid'] ) && !empty( $original['url'] );
    }

    /**
     * Copies the image of the object $sourceObjectID into the image field $target (a field of a draft
     * being edited) and stores the field. The alternative text of $target is kept; when it is empty,
     * the source image's alternative text is taken.
     *
     * @param eZContentObjectAttribute $target
     * @param mixed $sourceObjectID the selected object, as posted
     * @return true|string true when the image was copied, else the reason, translated, to show the editor
     */
    public static function copyFromObject( $target, $sourceObjectID )
    {
        if ( !$target instanceof eZContentObjectAttribute || $target->attribute( 'data_type_string' ) !== eZImageType::DATA_TYPE_STRING )
            return ezpI18n::tr( 'kernel/classes/datatypes', 'The image could not be copied. Please try again or contact the site administrator.' );

        $notReadable = ezpI18n::tr( 'kernel/classes/datatypes', 'The selected object does not exist or you may not read it.' );
        $objectID = self::objectIDFromInput( $sourceObjectID );
        if ( $objectID === false )
            return $notReadable;
        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
            return $notReadable;

        $sourceAttribute = self::sourceAttribute( $object );
        if ( $sourceAttribute === null )
            return ezpI18n::tr( 'kernel/classes/datatypes', 'The selected object has no image.' );

        $sourceHandler = $sourceAttribute->attribute( 'content' );
        $original = $sourceHandler->imageAlias( 'original' );
        $sourcePath = (string)$original['url'];
        $originalFilename = isset( $original['original_filename'] ) && is_string( $original['original_filename'] ) && $original['original_filename'] !== '' ?
                            basename( $original['original_filename'] ) : basename( $sourcePath );

        $unreadable = ezpI18n::tr( 'kernel/classes/datatypes', 'The image file of the selected object could not be read.' );
        $sourceFile = eZClusterFileHandler::instance( $sourcePath );
        if ( !$sourceFile->exists() )
        {
            eZDebug::writeError( "The image file '$sourcePath' of object $objectID does not exist", __METHOD__ );
            return $unreadable;
        }

        // The checks of an upload, against the field being edited
        $classAttribute = $target->contentClassAttribute();
        $maxSize = $classAttribute ? 1024 * 1024 * (int)$classAttribute->attribute( eZImageType::FILESIZE_FIELD ) : 0;
        $size = (int)$sourceFile->size();
        if ( $maxSize > 0 && $size > $maxSize )
        {
            return ezpI18n::tr( 'kernel/classes/datatypes', 'The image of the selected object is larger than this field allows: %1 bytes.', null,
                                array( '%1' => $maxSize ) );
        }
        $blackList = new eZFileExtensionBlackListValidator();
        $state = $blackList->validate( $originalFilename );
        if ( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE ||
             !eZImageType::isAcceptedImageFileName( $originalFilename ) )
        {
            return ezpI18n::tr( 'kernel/classes/datatypes', 'A valid image file is required.' );
        }

        // A private copy of the file to work on: the source's storage is only read, also when the field
        // being edited removes its own old files (which, for an object's own image, can be the same file)
        $workDir = eZSys::cacheDirectory() . '/expimagecopy';
        if ( !is_dir( $workDir ) && !eZDir::mkdir( $workDir, false, true ) && !is_dir( $workDir ) )
        {
            eZDebug::writeError( "The working directory '$workDir' could not be created", __METHOD__ );
            return $unreadable;
        }
        $suffix = strtolower( (string)pathinfo( $originalFilename, PATHINFO_EXTENSION ) );
        $workPath = $workDir . '/' . bin2hex( random_bytes( 12 ) ) . ( $suffix !== '' && ctype_alnum( $suffix ) ? '.' . $suffix : '' );

        $localPath = $sourceFile->fetchUnique();
        $copied = is_string( $localPath ) && is_file( $localPath ) && @copy( $localPath, $workPath );
        if ( is_string( $localPath ) && $localPath !== $sourcePath )
            $sourceFile->fileDeleteLocal( $localPath );
        if ( !$copied )
        {
            eZDebug::writeError( "The image file '$sourcePath' of object $objectID could not be read", __METHOD__ );
            self::removeWorkFile( $workPath );
            return $unreadable;
        }

        try
        {
            $imageError = eZImageType::imageFileError( $workPath, $originalFilename, eZImageType::svgAccepted() );
            if ( $imageError !== false )
                return $imageError;

            /** @var eZImageAliasHandler $handler */
            $handler = $target->attribute( 'content' );
            if ( !$handler instanceof eZImageAliasHandler )
                return ezpI18n::tr( 'kernel/classes/datatypes', 'The image could not be copied. Please try again or contact the site administrator.' );

            $altText = $handler->attribute( 'alternative_text' );
            if ( !is_string( $altText ) || trim( $altText ) === '' )
            {
                $sourceAltText = $sourceHandler->attribute( 'alternative_text' );
                $altText = is_string( $sourceAltText ) ? $sourceAltText : '';
            }

            $db = eZDB::instance();
            $db->begin();
            try
            {
                if ( !$handler->initializeFromFile( $workPath, $altText, $originalFilename ) )
                {
                    $db->commit();
                    return ezpI18n::tr( 'kernel/classes/datatypes', 'The image could not be copied. Please try again or contact the site administrator.' );
                }
                $handler->store( $target );
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            $target->setContent( $handler );
        }
        finally
        {
            self::removeWorkFile( $workPath );
        }

        eZDebug::writeNotice( "The image of object $objectID (field " . $sourceAttribute->attribute( 'id' ) . ') was copied into field ' .
                              $target->attribute( 'id' ) . ' of object ' . $target->attribute( 'contentobject_id' ) . ', version ' . $target->attribute( 'version' ), __METHOD__ );
        return true;
    }

    /**
     * The objects with an image whose name contains $text, that the current user may read: at most
     * SEARCH_LIMIT, the most recently changed first. An empty text lists the most recent images.
     * Every entry has: object_id, node_id, name, class_name and attribute (the image field to show).
     *
     * @param mixed $text
     * @return array[]
     */
    public static function search( $text )
    {
        $text = is_scalar( $text ) ? (string)$text : '';
        $text = trim( preg_replace( '/[\x00-\x1F\x7F]/', '', $text ) );
        if ( function_exists( 'mb_substr' ) )
            $text = mb_substr( $text, 0, self::SEARCH_TEXT_MAX_LENGTH, 'UTF-8' );
        else
            $text = substr( $text, 0, self::SEARCH_TEXT_MAX_LENGTH );

        $classIDs = self::imageClassIDs();
        if ( empty( $classIDs ) )
            return array();

        $params = array( 'ClassFilterType' => 'include',
                         'ClassFilterArray' => $classIDs,
                         'MainNodeOnly' => true,
                         'SortBy' => array( 'modified', false ),
                         'Limit' => self::SEARCH_LIMIT * 3,
                         'LoadDataMap' => false,
                         'AsObject' => true );
        if ( $text !== '' )
        {
            // The text is found as written: the wildcard and its escape are taken literally
            $literal = str_replace( array( '\\', '*' ), array( '\\\\', '\\*' ), $text );
            $params['AttributeFilter'] = array( array( 'name', 'like', '*' . $literal . '*' ) );
        }

        // The tree fetch applies the user's content/read policies
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, 1 );
        $results = array();
        foreach ( (array)$nodes as $node )
        {
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;
            $object = $node->object();
            if ( !$object instanceof eZContentObject || !$object->canRead() )
                continue;
            $attribute = self::sourceAttribute( $object );
            if ( $attribute === null )
                continue;
            $results[] = array( 'object_id' => (int)$object->attribute( 'id' ),
                                'node_id' => (int)$node->attribute( 'node_id' ),
                                'name' => (string)$object->attribute( 'name' ),
                                'class_name' => (string)$object->attribute( 'class_name' ),
                                'attribute' => $attribute );
            if ( count( $results ) >= self::SEARCH_LIMIT )
                break;
        }
        return $results;
    }

    /**
     * Removes the working copy $path, written by copyFromObject().
     *
     * @param string $path
     */
    protected static function removeWorkFile( $path )
    {
        if ( is_string( $path ) && $path !== '' && is_file( $path ) )
            @unlink( $path );
    }
}
