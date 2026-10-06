<?php
/**
 * File containing the expRestContentPermission class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The current user's rights on the content a REST call reads or writes, checked the way the content module checks
 * them (doc/guides/api-keys.md, "Permissions of the REST interface").
 *
 * One place for every caller: the ezprestapi content controller asks it before each read and write, whatever the
 * request was authenticated with (OAuth token, personal API key, HTTP basic authentication, the anonymous user when
 * authentication is off), and the personal API keys ask it for their write guard ([ApiKeySettings] RouteGuards[]),
 * so both answer the same.
 *
 *  read    content/read of the node (nodeId) or the object (objectId): canRead()
 *  create  content/create of the class (classIdentifier) below the parent node (parentNodeID), in the language
 *          (languageLocale, optional): the parent object's checkAccess( 'create', class, parent class, language ),
 *          as eZContentObject::createWithNodeAssignment() does for content/action NewButton. The Class, ParentClass,
 *          Section, Node, Subtree and Language limitations all apply.
 *  edit    content/edit of the node (nodeId), with user/selfedit for one's own user object (canEdit()), and in the
 *          language when one is given.
 *  remove  content/remove of every location of the node's object and of everything below them (the REST call
 *          removes the object): canRemove() of each location and can_remove_all of
 *          eZContentObjectTreeNode::subtreeRemovalInformation(), as content/removeobject asks before it removes.
 *
 * check() returns null when the call may go on, else array( 'status' => 400|403|404, 'reason' => ..., 'message' => ... ).
 * The content module itself has no content/publish check (the policy exists for the Platform kernel), and neither
 * has this: a create publishes when content/create allows it, and the publish workflow (content/publish triggers)
 * runs as it does for the content module.
 */
class expRestContentPermission
{
    const READ = 'read';
    const CREATE = 'create';
    const EDIT = 'edit';
    const REMOVE = 'remove';

    /**
     * Whether the current user may do $action with the content named in $params.
     *
     * @param string $action read, create, edit or remove
     * @param array $params nodeId, objectId, parentNodeID, classIdentifier, languageLocale (as the REST call names them)
     * @return array|null null when allowed, else array( 'status' => int, 'reason' => string, 'message' => string )
     */
    public static function check( $action, array $params )
    {
        $language = isset( $params['languageLocale'] ) && is_scalar( $params['languageLocale'] ) && (string)$params['languageLocale'] !== ''
                    ? (string)$params['languageLocale'] : false;
        if ( $language !== false && !static::languageExists( $language ) )
            return self::refusal( 400, 'invalid_request', "The language '$language' does not exist." );

        switch ( $action )
        {
            case self::CREATE:
                $parentID = self::id( $params, 'parentNodeID' );
                $identifier = isset( $params['classIdentifier'] ) && is_scalar( $params['classIdentifier'] ) ? trim( (string)$params['classIdentifier'] ) : '';
                if ( !$parentID )
                    return self::refusal( 400, 'invalid_request', 'The parameter parentNodeID (the node to create below) is missing or not a node id.' );
                if ( $identifier === '' )
                    return self::refusal( 400, 'invalid_request', 'The parameter classIdentifier (the content class to create) is missing.' );
                $parent = static::fetchNode( $parentID );
                if ( !$parent instanceof eZContentObjectTreeNode )
                    return self::refusal( 404, 'not_found', "The parent node $parentID does not exist." );
                $class = static::fetchClass( $identifier );
                if ( !$class instanceof eZContentClass )
                    return self::refusal( 400, 'invalid_request', "The content class '$identifier' does not exist." );
                if ( !static::createAllowed( $parent, $class, $language ) )
                    return self::refusal( 403, 'access_denied', "You may not create '$identifier' below node $parentID"
                                          . ( $language !== false ? " in $language" : '' ) . ' (content/create).' );
                return null;

            case self::READ:
            case self::EDIT:
            case self::REMOVE:
                $nodeID = self::id( $params, 'nodeId' );
                $objectID = self::id( $params, 'objectId' );
                if ( $nodeID )
                {
                    $node = static::fetchNode( $nodeID );
                    if ( !$node instanceof eZContentObjectTreeNode )
                        return self::refusal( 404, 'not_found', "The node $nodeID does not exist." );
                    $subject = "node $nodeID";
                }
                else if ( $objectID && $action === self::READ )
                {
                    $object = static::fetchObject( $objectID );
                    if ( !$object instanceof eZContentObject )
                        return self::refusal( 404, 'not_found', "The object $objectID does not exist." );
                    return $object->canRead() ? null
                           : self::refusal( 403, 'access_denied', "You may not read object $objectID (content/read)." );
                }
                else
                    return self::refusal( 400, 'invalid_request', 'The node id is missing or not a number.' );

                if ( $action === self::READ )
                    return static::readAllowed( $node ) ? null
                           : self::refusal( 403, 'access_denied', "You may not read $subject (content/read)." );
                if ( $action === self::EDIT )
                    return static::editAllowed( $node, $language ) ? null
                           : self::refusal( 403, 'access_denied', "You may not edit $subject" . ( $language !== false ? " in $language" : '' ) . ' (content/edit).' );
                return static::removeAllowed( $node ) ? null
                       : self::refusal( 403, 'access_denied', "You may not remove $subject, one of its locations or something below them (content/remove)." );
        }

        // an action nobody knows allows nothing
        return self::refusal( 403, 'access_denied', "Unknown content action '" . (string)$action . "'." );
    }

    /**
     * check() with the parameters of a REST request: the route's variables (nodeId, objectId) and the POST fields
     * (parentNodeID, classIdentifier, languageLocale).
     *
     * @param string $action
     * @param ezcMvcRequest $request
     * @return array|null
     */
    public static function forRequest( $action, ezcMvcRequest $request )
    {
        $post = is_array( $request->post ) ? $request->post : array();
        $variables = is_array( $request->variables ) ? $request->variables : array();
        $params = array();
        foreach ( array( 'parentNodeID', 'classIdentifier', 'languageLocale' ) as $name )
            if ( isset( $post[$name] ) )
                $params[$name] = $post[$name];
        foreach ( array( 'nodeId', 'objectId' ) as $name )
            if ( isset( $variables[$name] ) )
                $params[$name] = $variables[$name];
        return static::check( $action, $params );
    }

    /**
     * content/create of $class below $parent (in $language), as the content module asks it.
     *
     * @param eZContentObjectTreeNode $parent
     * @param eZContentClass $class
     * @param string|bool $language a locale, or false for any
     * @return bool
     */
    public static function createAllowed( eZContentObjectTreeNode $parent, eZContentClass $class, $language = false )
    {
        $object = $parent->attribute( 'object' );
        return $object instanceof eZContentObject
               && $object->checkAccess( 'create', $class->attribute( 'id' ), $parent->attribute( 'contentclass_id' ), false, $language ) == 1;
    }

    /**
     * content/read of the node.
     *
     * @param eZContentObjectTreeNode $node
     * @return bool
     */
    public static function readAllowed( eZContentObjectTreeNode $node )
    {
        return (bool)$node->canRead();
    }

    /**
     * content/edit of the node (user/selfedit counts for one's own user object), in $language when given.
     *
     * @param eZContentObjectTreeNode $node
     * @param string|bool $language
     * @return bool
     */
    public static function editAllowed( eZContentObjectTreeNode $node, $language = false )
    {
        if ( !$node->canEdit() )
            return false;
        if ( $language === false )
            return true;
        $object = $node->attribute( 'object' );
        return $object instanceof eZContentObject && (bool)$object->canEdit( false, false, false, $language );
    }

    /**
     * content/remove of every location of the node's object and everything below them.
     *
     * @param eZContentObjectTreeNode $node
     * @return bool
     */
    public static function removeAllowed( eZContentObjectTreeNode $node )
    {
        $nodes = static::objectNodes( $node );
        if ( !$nodes )
            return false;
        $ids = array();
        foreach ( $nodes as $one )
        {
            if ( !$one->canRemove() )
                return false;
            $ids[] = (int)$one->attribute( 'node_id' );
        }
        return static::subtreeRemovable( $ids );
    }

    /**
     * Every location of the node's object (the node itself when the object has none to give).
     *
     * @param eZContentObjectTreeNode $node
     * @return eZContentObjectTreeNode[]
     */
    protected static function objectNodes( eZContentObjectTreeNode $node )
    {
        $object = $node->attribute( 'object' );
        $nodes = $object instanceof eZContentObject ? $object->assignedNodes() : array();
        return is_array( $nodes ) && $nodes ? $nodes : array( $node );
    }

    /**
     * Whether everything below the nodes may be removed too (content/remove of each node of the subtrees).
     *
     * @param int[] $nodeIDs
     * @return bool
     */
    protected static function subtreeRemovable( array $nodeIDs )
    {
        $info = eZContentObjectTreeNode::subtreeRemovalInformation( $nodeIDs );
        return is_array( $info ) && !empty( $info['can_remove_all'] );
    }

    /**
     * @param int $nodeID
     * @return eZContentObjectTreeNode|null
     */
    protected static function fetchNode( $nodeID )
    {
        return eZContentObjectTreeNode::fetch( (int)$nodeID );
    }

    /**
     * @param int $objectID
     * @return eZContentObject|null
     */
    protected static function fetchObject( $objectID )
    {
        return eZContentObject::fetch( (int)$objectID );
    }

    /**
     * @param string $identifier
     * @return eZContentClass|null
     */
    protected static function fetchClass( $identifier )
    {
        return eZContentClass::fetchByIdentifier( $identifier );
    }

    /**
     * @param string $locale
     * @return bool
     */
    protected static function languageExists( $locale )
    {
        return eZContentLanguage::fetchByLocale( $locale ) instanceof eZContentLanguage;
    }

    /**
     * A positive integer id from $params[$name], or 0.
     */
    private static function id( array $params, $name )
    {
        if ( !isset( $params[$name] ) || !is_scalar( $params[$name] ) || !preg_match( '/^\s*[1-9][0-9]{0,18}\s*$/', (string)$params[$name] ) )
            return 0;
        return (int)$params[$name];
    }

    private static function refusal( $status, $reason, $message )
    {
        return array( 'status' => (int)$status, 'reason' => $reason, 'message' => $message );
    }
}
?>
