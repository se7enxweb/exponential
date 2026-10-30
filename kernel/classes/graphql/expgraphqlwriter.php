<?php
/**
 * How the mutations of the GraphQL API change content: through the kernel's own operations (content/publish,
 * content/delete, content/move, content/hide, content/addlocation), so the workflows triggered on them run as they
 * do in the administration, and with the rights of the user of the request, checked before anything is written:
 *
 *   createContent     content/create below the parent, for the class and the language
 *   updateContent     content/edit of the object in the language (a new version, published or left a draft)
 *   translateContent  content/edit in the new language (a new version copied from the main language)
 *   publishDraft      a draft of the user (or one the user may edit)
 *   deleteContent     content/remove of every location removed (to the trash by default)
 *   moveContent       content/edit and content/remove of the location, content/create below the new parent
 *   hideContent       content/hide (and reveal)
 *   addLocation       content/edit of the object, content/create below the new parent
 *
 * What the user may not read is answered NOT_FOUND, as the queries answer it as not there; what the user may read
 * but not change is FORBIDDEN. A class the schema does not show (the user accounts, ExcludedClassList[]) is never
 * written. A top-level location (depth 1) is never removed or moved.
 *
 * A publish that a workflow stops (an approval: the event waits for an editor) answers PENDING, one it cancels
 * CANCELLED; the version stays as the workflow left it. Every method answers an array: status, contentId, nodeId,
 * version, content (expGraphQLContent read after the change, with the user's rights).
 *
 * Made for one request, never kept. A test extends the class to answer without a database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLWriter
{
    /** The status of a mutation's answer (the enum MutationStatus). */
    const PUBLISHED = 'PUBLISHED';
    const DRAFT = 'DRAFT';
    const PENDING = 'PENDING';
    const CANCELLED = 'CANCELLED';
    const DONE = 'DONE';
    const TRASHED = 'TRASHED';
    const DELETED = 'DELETED';

    /** @var expGraphQLRepository */
    protected $repository;

    /** @var expGraphQLClassCatalogue */
    protected $catalogue;

    /** @var eZUser|null */
    protected $user;

    /**
     * @param expGraphQLRepository $repository
     * @param expGraphQLClassCatalogue $catalogue the classes of the schema
     * @param eZUser|null $user the user of the request (the kernel's current user)
     */
    public function __construct( expGraphQLRepository $repository, expGraphQLClassCatalogue $catalogue, $user )
    {
        $this->repository = $repository;
        $this->catalogue = $catalogue;
        $this->user = $user;
    }

    /** @return int|false the content object id of the user */
    protected function userId()
    {
        return $this->user instanceof eZUser ? (int)$this->user->attribute( 'contentobject_id' ) : false;
    }

    // ------------------------------------------------------------------------------------------------ the mutations

    /**
     * @param array $args parentId, classIdentifier, fields, language, remoteId, publish
     * @return array
     */
    public function create( array $args )
    {
        $parent = $this->readableNode( $args['parentId'] );
        $class = $this->exposedClass( (string)$args['classIdentifier'] );
        $classObject = eZContentClass::fetchByIdentifier( $class['identifier'] );
        if ( !$classObject instanceof eZContentClass )
            throw self::notFound();
        $language = $this->languageOf( isset( $args['language'] ) ? $args['language'] : null, null );
        if ( $parent->checkAccess( 'create', (int)$classObject->attribute( 'id' ), (int)$parent->attribute( 'contentclass_id' ), false, $language, $this->userId() ) != 1 )
            throw self::forbidden();
        $fields = expGraphQLFieldInput::prepare( $class, isset( $args['fields'] ) ? (array)$args['fields'] : array(), true );
        $remoteId = isset( $args['remoteId'] ) ? trim( (string)$args['remoteId'] ) : '';
        if ( $remoteId !== '' && eZContentObject::fetchByRemoteID( $remoteId, false ) )
            throw new expGraphQLError( expGraphQLMessage::tr( 'The remote id %remoteId is taken.', array( '%remoteId' => $remoteId ) ), expGraphQLError::BAD_USER_INPUT );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $object = $classObject->instantiate( $this->userId(), 0, false, $language );
            if ( $remoteId !== '' )
                $object->setAttribute( 'remote_id', $remoteId );
            $object->store();
            eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
                                             'contentobject_version' => $object->attribute( 'current_version' ),
                                             'parent_node' => (int)$parent->attribute( 'node_id' ),
                                             'is_main' => 1,
                                             'sort_field' => $classObject->attribute( 'sort_field' ),
                                             'sort_order' => $classObject->attribute( 'sort_order' ) ) )->store();
            $version = $object->version( 1 );
            $version->setAttribute( 'modified', time() );
            $version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
            $version->store();
            $this->writeFields( $version->contentObjectAttributes( $language ), $fields, (int)$object->attribute( 'id' ) );
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            throw $e;
        }
        return $this->finish( $object, 1, $language, !isset( $args['publish'] ) || $args['publish'] );
    }

    /**
     * @param array $args id, fields, language, publish
     * @param bool $translate a new translation: copied from the main language first
     * @return array
     */
    public function update( array $args, $translate = false )
    {
        $object = $this->readableObject( $args['id'] );
        $class = $this->exposedClass( (string)$object->attribute( 'class_identifier' ) );
        $language = $this->languageOf( isset( $args['language'] ) ? $args['language'] : null, $object );
        // every translation of the object, not only those of the siteaccess's languages (availableLanguages())
        $existing = false;
        foreach ( (array)$object->allLanguages() as $objectLanguage )
        {
            if ( $objectLanguage instanceof eZContentLanguage && (string)$objectLanguage->attribute( 'locale' ) === $language )
                $existing = true;
        }
        if ( $translate && $existing )
            throw new expGraphQLError( expGraphQLMessage::tr( 'The content has a translation in %language already: use updateContent.', array( '%language' => $language ) ),
                                       expGraphQLError::BAD_USER_INPUT );
        if ( !$object->canEdit( false, false, false, $language ) )
            throw self::forbidden();
        $fields = expGraphQLFieldInput::prepare( $class, isset( $args['fields'] ) ? (array)$args['fields'] : array(), false );
        if ( !$fields && !$translate )
            throw new expGraphQLError( expGraphQLMessage::tr( 'Give at least one field.' ), expGraphQLError::BAD_USER_INPUT );

        $db = eZDB::instance();
        $db->begin();
        try
        {
            $version = $existing ? $object->createNewVersionIn( $language )
                                 : $object->createNewVersionIn( $language, (string)$object->attribute( 'initial_language_code' ) );
            if ( !$version instanceof eZContentObjectVersion )
                throw new expGraphQLError( expGraphQLMessage::tr( 'No new version could be made (the content has too many drafts).' ), expGraphQLError::BAD_USER_INPUT );
            $version->setAttribute( 'modified', time() );
            $version->store();
            $this->writeFields( $version->contentObjectAttributes( $language ), $fields, (int)$object->attribute( 'id' ) );
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            throw $e;
        }
        return $this->finish( $object, (int)$version->attribute( 'version' ), $language, !isset( $args['publish'] ) || $args['publish'] );
    }

    /**
     * @param int $id
     * @param int $versionNumber
     * @return array
     */
    public function publishDraft( $id, $versionNumber )
    {
        $object = eZContentObject::fetch( (int)$id );
        $version = $object instanceof eZContentObject ? $object->version( (int)$versionNumber ) : null;
        if ( !$version instanceof eZContentObjectVersion || !$this->catalogue->classByIdentifier( (string)$object->attribute( 'class_identifier' ) ) )
            throw self::notFound();
        $own = (int)$version->attribute( 'creator_id' ) === (int)$this->userId();
        $language = (string)$version->initialLanguageCode();
        if ( !$own && !$version->canVersionRead() )
            throw self::notFound();
        if ( !in_array( (int)$version->attribute( 'status' ), array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true ) )
            throw new expGraphQLError( expGraphQLMessage::tr( 'Version %version is not a draft.', array( '%version' => (int)$versionNumber ) ), expGraphQLError::BAD_USER_INPUT );
        if ( !$object->canEdit( false, false, false, $language ) )
            throw self::forbidden();
        return $this->finish( $object, (int)$versionNumber, $language, true );
    }

    /**
     * @param array $args id or nodeId, trash
     * @return array
     */
    public function remove( array $args )
    {
        $given = ( isset( $args['id'] ) ? 1 : 0 ) + ( isset( $args['nodeId'] ) ? 1 : 0 );
        if ( $given !== 1 )
            throw new expGraphQLError( expGraphQLMessage::tr( 'deleteContent needs exactly one of id and nodeId.' ), expGraphQLError::BAD_USER_INPUT );
        if ( isset( $args['nodeId'] ) )
            $nodes = array( $this->readableNode( $args['nodeId'] ) );
        else
        {
            $object = $this->readableObject( $args['id'] );
            $nodes = array_values( array_filter( (array)$object->assignedNodes( true ), function ( $node ) { return $node instanceof eZContentObjectTreeNode; } ) );
            if ( !$nodes )
                throw self::notFound();
        }
        $ids = array();
        foreach ( $nodes as $node )
        {
            $this->exposedClass( (string)$node->attribute( 'class_identifier' ) );
            if ( !$node->canRead() )
                throw self::notFound();
            if ( (int)$node->attribute( 'depth' ) <= 1 || !$node->canRemove() )
                throw self::forbidden();
            $ids[] = (int)$node->attribute( 'node_id' );
        }
        $first = $nodes[0];
        $objectId = (int)$first->attribute( 'contentobject_id' );
        $trash = !isset( $args['trash'] ) || $args['trash'];
        if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
        {
            $result = eZOperationHandler::execute( 'content', 'delete', array( 'node_id_list' => $ids, 'move_to_trash' => $trash ? 1 : 0 ), null, true );
            $status = self::workflowStatus( $result, self::DONE );
            if ( $status !== self::DONE )
                return array( 'status' => $status, 'contentId' => $objectId, 'nodeId' => $ids[0], 'version' => null, 'content' => null );
        }
        else
            eZContentOperationCollection::deleteObject( $ids, $trash );
        eZContentObject::clearCache( array( $objectId ) );
        $left = $this->repository->contentById( $objectId );
        return array( 'status' => $left !== null ? self::DONE : ( $trash ? self::TRASHED : self::DELETED ), 'contentId' => $objectId, 'nodeId' => $ids[0],
                      'version' => null, 'content' => $left );
    }

    /**
     * @param int $nodeId
     * @param int $newParentId
     * @return array
     */
    public function move( $nodeId, $newParentId )
    {
        $node = $this->readableNode( $nodeId );
        $this->exposedClass( (string)$node->attribute( 'class_identifier' ) );
        $parent = $this->readableNode( $newParentId );
        if ( strpos( (string)$parent->attribute( 'path_string' ), (string)$node->attribute( 'path_string' ) ) === 0 )
            throw new expGraphQLError( expGraphQLMessage::tr( 'A location cannot be moved below itself.' ), expGraphQLError::BAD_USER_INPUT );
        if ( (int)$node->attribute( 'depth' ) <= 1 || !$node->canMoveFrom()
             || $parent->checkAccess( 'create', (int)$node->attribute( 'contentclass_id' ), (int)$parent->attribute( 'contentclass_id' ), false, false, $this->userId() ) != 1 )
            throw self::forbidden();
        $objectId = (int)$node->attribute( 'contentobject_id' );
        if ( (int)$node->attribute( 'parent_node_id' ) !== (int)$parent->attribute( 'node_id' ) )
        {
            if ( eZOperationHandler::operationIsAvailable( 'content_move' ) )
            {
                $result = eZOperationHandler::execute( 'content', 'move', array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => $objectId,
                                                                               'new_parent_node_id' => (int)$parent->attribute( 'node_id' ) ), null, true );
                $status = self::workflowStatus( $result, self::DONE );
                if ( $status !== self::DONE )
                    return array( 'status' => $status, 'contentId' => $objectId, 'nodeId' => (int)$node->attribute( 'node_id' ), 'version' => null, 'content' => null );
            }
            else
                eZContentOperationCollection::moveNode( (int)$node->attribute( 'node_id' ), $objectId, (int)$parent->attribute( 'node_id' ) );
        }
        eZContentObject::clearCache( array( $objectId ) );
        return array( 'status' => self::DONE, 'contentId' => $objectId, 'nodeId' => (int)$node->attribute( 'node_id' ), 'version' => null,
                      'content' => $this->repository->contentByNodeId( (int)$node->attribute( 'node_id' ) ) );
    }

    /**
     * @param int $nodeId
     * @param bool $hide true: hide, false: reveal
     * @return array
     */
    public function visibility( $nodeId, $hide )
    {
        $node = $this->readableNode( $nodeId );
        $this->exposedClass( (string)$node->attribute( 'class_identifier' ) );
        if ( !$node->canHide() )
            throw self::forbidden();
        $objectId = (int)$node->attribute( 'contentobject_id' );
        if ( (bool)$node->attribute( 'is_hidden' ) !== (bool)$hide )
        {
            if ( eZOperationHandler::operationIsAvailable( 'content_hide' ) )
            {
                $result = eZOperationHandler::execute( 'content', 'hide', array( 'node_id' => (int)$nodeId ), null, true );
                $status = self::workflowStatus( $result, self::DONE );
                if ( $status !== self::DONE )
                    return array( 'status' => $status, 'contentId' => $objectId, 'nodeId' => (int)$nodeId, 'version' => null, 'content' => null );
            }
            else
                eZContentOperationCollection::changeHideStatus( (int)$nodeId );
        }
        eZContentObject::clearCache( array( $objectId ) );
        return array( 'status' => self::DONE, 'contentId' => $objectId, 'nodeId' => (int)$nodeId, 'version' => null,
                      'content' => $this->repository->contentByNodeId( (int)$nodeId ) );
    }

    /**
     * @param int $id
     * @param int $parentId
     * @return array
     */
    public function addLocation( $id, $parentId )
    {
        $object = $this->readableObject( $id );
        $this->exposedClass( (string)$object->attribute( 'class_identifier' ) );
        $parent = $this->readableNode( $parentId );
        if ( !$object->canEdit()
             || $parent->checkAccess( 'create', (int)$object->attribute( 'contentclass_id' ), (int)$parent->attribute( 'contentclass_id' ), false, false, $this->userId() ) != 1 )
            throw self::forbidden();
        foreach ( (array)$object->assignedNodes( false ) as $row )
        {
            if ( (int)$row['parent_node_id'] === (int)$parentId )
                throw new expGraphQLError( expGraphQLMessage::tr( 'The content has a location below %parentId already.', array( '%parentId' => (int)$parentId ) ),
                                           expGraphQLError::BAD_USER_INPUT );
        }
        $mainNodeId = (int)$object->attribute( 'main_node_id' );
        if ( eZOperationHandler::operationIsAvailable( 'content_addlocation' ) )
        {
            $result = eZOperationHandler::execute( 'content', 'addlocation', array( 'node_id' => $mainNodeId, 'object_id' => (int)$id,
                                                                                  'select_node_id_array' => array( (int)$parentId ) ), null, true );
            $status = self::workflowStatus( $result, self::DONE );
            if ( $status !== self::DONE )
                return array( 'status' => $status, 'contentId' => (int)$id, 'nodeId' => null, 'version' => null, 'content' => null );
        }
        else
            eZContentOperationCollection::addAssignment( $mainNodeId, (int)$id, array( (int)$parentId ) );
        eZContentObject::clearCache( array( (int)$id ) );
        $nodeId = null;
        foreach ( (array)eZContentObject::fetch( (int)$id )->assignedNodes( false ) as $row )
        {
            if ( (int)$row['parent_node_id'] === (int)$parentId )
                $nodeId = (int)$row['node_id'];
        }
        return array( 'status' => self::DONE, 'contentId' => (int)$id, 'nodeId' => $nodeId, 'version' => null,
                      'content' => $nodeId !== null ? $this->repository->contentByNodeId( $nodeId ) : null );
    }

    // --------------------------------------------------------------------------------------------- drafts, preview

    /**
     * A version of an object, for a user who may read it: content/versionread, or a draft of the user's own.
     *
     * @param int $id
     * @param int $versionNumber
     * @param string|false $language
     * @return expGraphQLDraftContent|null null when it is not there or the user may not read it
     */
    public function version( $id, $versionNumber, $language = false )
    {
        $object = (int)$id > 0 ? eZContentObject::fetch( (int)$id ) : null;
        $version = $object instanceof eZContentObject ? $object->version( (int)$versionNumber ) : null;
        if ( !$version instanceof eZContentObjectVersion || !$this->catalogue->classByIdentifier( (string)$object->attribute( 'class_identifier' ) ) )
            return null;
        if ( (int)$version->attribute( 'creator_id' ) !== (int)$this->userId() && !$version->canVersionRead() )
            return null;
        return new expGraphQLDraftContent( $object, (int)$versionNumber, $language );
    }

    /**
     * The version a preview token names, read without the rights of the request (the token is the permission).
     *
     * @param array $token of expGraphQLPreviewToken::verify()
     * @return expGraphQLDraftContent|null null when the version, its object or the user who made the token is gone
     */
    public function preview( array $token )
    {
        $object = eZContentObject::fetch( (int)$token['objectId'] );
        if ( !$object instanceof eZContentObject || !$object->version( (int)$token['version'] ) instanceof eZContentObjectVersion
             || !$this->catalogue->classByIdentifier( (string)$object->attribute( 'class_identifier' ) )
             || !eZUser::fetch( (int)$token['userId'] ) instanceof eZUser )
            return null;
        return new expGraphQLDraftContent( $object, (int)$token['version'], $token['language'] !== '' ? $token['language'] : false );
    }

    /**
     * What a preview token may name: a version the user may read.
     *
     * @param int $id
     * @param int|null $versionNumber null: the newest draft of the user, else the current version
     * @param string|null $language
     * @return array objectId, version, language
     */
    public function previewTarget( $id, $versionNumber, $language )
    {
        $object = (int)$id > 0 ? eZContentObject::fetch( (int)$id ) : null;
        if ( !$object instanceof eZContentObject )
            throw self::notFound();
        if ( $versionNumber === null )
        {
            $versionNumber = (int)$object->attribute( 'current_version' );
            foreach ( (array)$object->versions( true, array( 'conditions' => array( 'status' => eZContentObjectVersion::STATUS_DRAFT,
                                                                                    'creator_id' => (int)$this->userId() ) ) ) as $draft )
                $versionNumber = max( $versionNumber, (int)$draft->attribute( 'version' ) );
        }
        $content = $this->version( $id, $versionNumber, $language !== null ? (string)$language : false );
        if ( $content === null )
            throw self::notFound();
        return array( 'objectId' => (int)$id, 'version' => (int)$versionNumber, 'language' => $content->language() );
    }

    // ---------------------------------------------------------------------------------------------------- helpers

    /**
     * Publishes a version through the operation content/publish (its workflows run) and answers the new state.
     *
     * @param eZContentObject $object
     * @param int $versionNumber
     * @param string $language
     * @param bool $publish false: the version stays a draft
     * @return array
     */
    protected function finish( eZContentObject $object, $versionNumber, $language, $publish )
    {
        $id = (int)$object->attribute( 'id' );
        if ( !$publish )
        {
            eZContentObject::clearCache( array( $id ) );
            $fresh = eZContentObject::fetch( $id );
            return array( 'status' => self::DRAFT, 'contentId' => $id, 'nodeId' => null, 'version' => (int)$versionNumber,
                          'content' => $fresh instanceof eZContentObject ? new expGraphQLDraftContent( $fresh, (int)$versionNumber, $language ) : null );
        }
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => (int)$versionNumber ) );
        $status = self::workflowStatus( $result, self::PUBLISHED );
        eZContentObject::clearCache( array( $id ) );
        $content = $status === self::PUBLISHED ? $this->repository->contentById( $id, $language ) : null;
        $location = $content !== null ? $content->mainLocation() : null;
        // a translation the siteaccess does not list (SiteLanguageList): the answer still shows what was written
        if ( $content !== null && !in_array( $language, $content->languages(), true ) )
            $content = new expGraphQLDraftContent( $content->object(), (int)$versionNumber, $language );
        return array( 'status' => $status, 'contentId' => $id, 'nodeId' => $location !== null ? $location->id() : null,
                      'version' => (int)$versionNumber, 'content' => $content );
    }

    /**
     * The status of a mutation from the result of a kernel operation.
     *
     * @param array|null $result of eZOperationHandler::execute()
     * @param string $done the status when the operation ran to its end
     * @return string
     */
    public static function workflowStatus( $result, $done )
    {
        if ( !is_array( $result ) || !isset( $result['status'] ) )
            return $done;
        switch ( (int)$result['status'] )
        {
            case eZModuleOperationInfo::STATUS_CANCELLED:
                return self::CANCELLED;
            case eZModuleOperationInfo::STATUS_HALTED:
            case eZModuleOperationInfo::STATUS_REPEAT:
            case eZModuleOperationInfo::STATUS_QUEUED:
                return self::PENDING;
        }
        return $done;
    }

    /**
     * Writes the prepared fields into the attributes of a version; a file is copied out of the upload first.
     *
     * @param eZContentObjectAttribute[] $attributes
     * @param array $fields of expGraphQLFieldInput::prepare()
     * @param int $objectId
     */
    protected function writeFields( $attributes, array $fields, $objectId )
    {
        $dirs = array();
        try
        {
            foreach ( (array)$attributes as $attribute )
            {
                $identifier = (string)$attribute->attribute( 'contentclass_attribute_identifier' );
                if ( !isset( $fields[$identifier] ) )
                    continue;
                $field = $fields[$identifier];
                $path = null;
                if ( $field['upload'] instanceof expGraphQLUpload )
                {
                    $problem = $field['upload']->problem( !$field['upload']->fetched );
                    if ( $problem !== null )
                        throw new expGraphQLError( $problem, expGraphQLError::BAD_USER_INPUT );
                    $dir = eZSys::cacheDirectory() . '/graphql/uploads/' . bin2hex( random_bytes( 8 ) );
                    if ( !expGraphQLResponseCache::makeDirectory( $dir ) )
                        throw new expGraphQLError( expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL );
                    $dirs[] = $dir;
                    $path = $dir . '/' . $field['upload']->safeName();
                    if ( !@copy( $field['upload']->tmpName, $path ) )
                        throw new expGraphQLError( expGraphQLMessage::tr( 'Internal server error' ), expGraphQLError::INTERNAL );
                    @chmod( $path, expGraphQLResponseCache::fileMode( 0666 ) );
                }
                if ( $attribute->fromString( expGraphQLFieldInput::stringOf( $field, $path, $objectId ) ) === false )
                    throw new expGraphQLError( expGraphQLMessage::tr( 'The value of the field %field could not be stored.', array( '%field' => $identifier ) ),
                                               expGraphQLError::BAD_USER_INPUT );
                $attribute->store();
            }
        }
        finally
        {
            foreach ( $dirs as $dir )
            {
                foreach ( (array)glob( $dir . '/*' ) as $file )
                    @unlink( $file );
                @rmdir( $dir );
            }
        }
    }

    /**
     * @param string|null $language asked for
     * @param eZContentObject|null $object
     * @return string the language: asked for, else the object's main language, else the first of the siteaccess
     */
    protected function languageOf( $language, $object )
    {
        if ( $language !== null && $language !== '' )
        {
            if ( !eZContentLanguage::fetchByLocale( (string)$language ) instanceof eZContentLanguage )
                throw new expGraphQLError( expGraphQLMessage::tr( 'There is no content language %language.', array( '%language' => (string)$language ) ),
                                           expGraphQLError::BAD_USER_INPUT );
            return (string)$language;
        }
        if ( $object instanceof eZContentObject )
            return (string)$object->attribute( 'initial_language_code' );
        $top = eZContentLanguage::topPriorityLanguage();
        return $top instanceof eZContentLanguage ? (string)$top->attribute( 'locale' ) : 'eng-GB';
    }

    /**
     * @param int $nodeId
     * @return eZContentObjectTreeNode a location the user may read
     * @throws expGraphQLError NOT_FOUND
     */
    protected function readableNode( $nodeId )
    {
        $node = (int)$nodeId > 0 ? eZContentObjectTreeNode::fetch( (int)$nodeId ) : null;
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            throw self::notFound();
        return $node;
    }

    /**
     * @param int $id
     * @return eZContentObject a published object the user may read
     * @throws expGraphQLError NOT_FOUND
     */
    protected function readableObject( $id )
    {
        $object = (int)$id > 0 ? eZContentObject::fetch( (int)$id ) : null;
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED || !$object->canRead() )
            throw self::notFound();
        return $object;
    }

    /**
     * @param string $identifier
     * @return array the class as the schema shows it
     * @throws expGraphQLError NOT_FOUND for a class the schema does not show
     */
    protected function exposedClass( $identifier )
    {
        $class = $this->catalogue->classByIdentifier( (string)$identifier );
        if ( $class === null )
            throw new expGraphQLError( expGraphQLMessage::tr( 'There is no content class %class in the schema.', array( '%class' => (string)$identifier ) ),
                                       expGraphQLError::NOT_FOUND );
        return $class;
    }

    /** @return expGraphQLError */
    public static function notFound()
    {
        return new expGraphQLError( expGraphQLMessage::tr( 'The content is not there, or you may not read it.' ), expGraphQLError::NOT_FOUND );
    }

    /** @return expGraphQLError */
    public static function forbidden()
    {
        return new expGraphQLError( expGraphQLMessage::tr( 'You may not do this to this content.' ), expGraphQLError::FORBIDDEN );
    }
}

?>
