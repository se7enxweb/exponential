<?php
/**
 * The shared work of the forum, topic, reply and comment services: they are content services over configurable
 * content classes ([Community] in expservices.ini). Lists, reads, create, edit, remove and moderation (hide)
 * follow the content policies of the current user, so "own" limitations apply as they do in the admin.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

abstract class expCommunityBase extends expServiceBase
{
    /** The datatypes whose value is set from a plain POST text. */
    protected static $textTypes = array( 'ezstring', 'eztext', 'ezboolean', 'ezinteger', 'ezfloat', 'ezemail', 'ezxmltext' );

    /** The class identifier configured for a role: forum, container, topic, reply, comment, poll, review. */
    public static function classOf( $role )
    {
        $keys = array( 'forum' => 'ForumClass', 'container' => 'ForumContainerClass', 'topic' => 'TopicClass', 'reply' => 'ReplyClass',
            'comment' => 'CommentClass', 'poll' => 'PollClass', 'review' => 'ReviewClass' );
        $defaults = array( 'forum' => 'forum', 'container' => 'forums', 'topic' => 'forum_topic', 'reply' => 'forum_reply', 'comment' => 'comment', 'poll' => 'poll', 'review' => 'review' );
        $ini = eZINI::instance( 'expservices.ini' );
        return $ini->hasVariable( 'Community', $keys[$role] ) ? $ini->variable( 'Community', $keys[$role] ) : $defaults[$role];
    }

    protected static function root()
    {
        return 1;
    }

    /** The top level node needs no read check: the tree fetches apply the read policies. */
    protected static function node( $nodeId, $function = 'read' )
    {
        if ( (int)$nodeId === 1 && $function === 'read' )
            return eZContentObjectTreeNode::fetch( 1 );
        return parent::node( $nodeId, $function );
    }

    /** The node of the id, which has to be of one of the classes; 404 otherwise. */
    protected static function item( $nodeId, array $classes, $function = 'read' )
    {
        $node = self::node( $nodeId, $function );
        if ( !in_array( $node->attribute( 'class_identifier' ), $classes, true ) )
            throw new expServiceException( "Node $nodeId is not a " . implode( ' or ', $classes ), 404 );
        return $node;
    }

    /** The editable text fields of a class: identifier => datatype. */
    protected static function fieldsOf( $classIdentifier )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class )
            throw new expServiceException( "The content class $classIdentifier does not exist: see [Community] in expservices.ini", 500 );
        $out = array();
        foreach ( $class->fetchAttributes() as $a )
            if ( in_array( $a->attribute( 'data_type_string' ), self::$textTypes, true ) )
                $out[$a->attribute( 'identifier' )] = $a->attribute( 'data_type_string' );
        return $out;
    }

    /**
     * The attribute values of the POST: "fields" as JSON, or the fields as POST fields of their own name.
     *
     * @return array identifier => string
     */
    protected static function postedFields( $classIdentifier, $require )
    {
        $allowed = self::fieldsOf( $classIdentifier );
        $given = self::post( 'fields', 'json', array() );
        if ( !is_array( $given ) )
            throw new expServiceException( 'fields is a JSON object', 400 );
        foreach ( array_keys( $allowed ) as $id )
            if ( !array_key_exists( $id, $given ) )
            {
                $v = self::post( $id, 'string', null );
                if ( $v !== null )
                    $given[$id] = $v;
            }
        $out = array();
        foreach ( $given as $id => $v )
        {
            if ( !isset( $allowed[$id] ) )
                throw new expServiceException( "'$id' is not a field of $classIdentifier (fields: " . implode( ', ', array_keys( $allowed ) ) . ')', 422 );
            if ( !is_scalar( $v ) )
                throw new expServiceException( "The value of '$id' must be text", 422 );
            $out[$id] = $allowed[$id] === 'ezboolean' ? ( $v && $v !== 'false' && $v !== '0' ? '1' : '0' ) : trim( (string)$v );
            if ( $allowed[$id] === 'ezxmltext' )
                $out[$id] = self::xmlText( $out[$id] );
            if ( mb_strlen( $out[$id] ) > 65000 )
                throw new expServiceException( "The value of '$id' is too long", 422 );
        }
        if ( $require )
        {
            $class = eZContentClass::fetchByIdentifier( $classIdentifier );
            foreach ( $class->fetchAttributes() as $a )
                if ( $a->attribute( 'is_required' ) && isset( $allowed[$a->attribute( 'identifier' )] ) && ( !isset( $out[$a->attribute( 'identifier' )] ) || $out[$a->attribute( 'identifier' )] === '' ) )
                    throw new expServiceException( "'" . $a->attribute( 'identifier' ) . "' is required", 422 );
        }
        return $out;
    }

    /** Whether the current user may create an object of the class below the node (the policy create with its class limits). */
    protected static function canCreateClass( eZContentObjectTreeNode $parent, $classIdentifier )
    {
        $class = eZContentClass::fetchByIdentifier( $classIdentifier );
        if ( !$class || !$parent->canCreate() )
            return false;
        foreach ( (array)$parent->attribute( 'object' )->attribute( 'can_create_class_list' ) as $c )
            if ( (int)$c['id'] === (int)$class->attribute( 'id' ) )
                return true;
        return false;
    }

    /** Plain text as the XML of a rich text attribute: one paragraph per line break block. */
    protected static function xmlText( $text )
    {
        $paragraphs = array();
        foreach ( preg_split( '/\R{2,}/', $text ) as $p )
            if ( trim( $p ) !== '' )
                $paragraphs[] = '<paragraph>' . htmlspecialchars( trim( $p ), ENT_XML1 | ENT_QUOTES, 'UTF-8' ) . '</paragraph>';
        return '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">'
            . implode( '', $paragraphs ) . '</section>';
    }
    /** Creates a child of $parent of class $classIdentifier from the POST fields, as the current user. */
    protected static function createChild( eZContentObjectTreeNode $parent, $classIdentifier )
    {
        if ( !self::canCreateClass( $parent, $classIdentifier ) )
            throw new expServiceException( "You may not create a $classIdentifier here", 403 );
        $fields = self::postedFields( $classIdentifier, true );
        $object = eZContentFunctions::createAndPublishObject( array( 'parent_node_id' => (int)$parent->attribute( 'node_id' ), 'class_identifier' => $classIdentifier, 'attributes' => $fields ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( "The $classIdentifier could not be created", 422 );
        return eZContentObjectTreeNode::fetch( (int)$object->attribute( 'main_node_id' ) );
    }

    /** Changes the text fields of an item the user may edit. */
    protected static function editNode( eZContentObjectTreeNode $node )
    {
        if ( !$node->canEdit() )
            throw new expServiceException( 'No edit access to this item', 403 );
        $fields = self::postedFields( $node->attribute( 'class_identifier' ), false );
        if ( !$fields )
            throw new expServiceException( 'Nothing to change: send fields', 400 );
        if ( !eZContentFunctions::updateAndPublishObject( $node->attribute( 'object' ), array( 'attributes' => $fields ) ) )
            throw new expServiceException( 'The item could not be updated', 422 );
        return eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) );
    }

    /** Removes an item the user may remove, to the trash. */
    protected static function removeNode( eZContentObjectTreeNode $node )
    {
        if ( !$node->canRemove() )
            throw new expServiceException( 'No remove access to this item', 403 );
        $id = (int)$node->attribute( 'node_id' );
        eZContentObjectTreeNode::removeSubtrees( array( $id ), true );
        return array( 'removed' => $id );
    }

    /** The children of a node of the given classes, as a paged answer. */
    protected static function children( eZContentObjectTreeNode $parent, array $classes, array $args, $limitIndex, $offsetIndex, $newestFirst = true, $depth = 1 )
    {
        list( $limit, $offset ) = self::paging( $args, $limitIndex, $offsetIndex );
        $p = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'SortBy' => array( array( 'published', !$newestFirst ), array( 'node_id', !$newestFirst ) ), 'Limit' => $limit, 'Offset' => $offset );
        if ( $depth )
        {
            $p['Depth'] = $depth;
            $p['DepthOperator'] = 'eq';
        }
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $p, $parent->attribute( 'node_id' ) );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $p, $parent->attribute( 'node_id' ) );
        $items = array();
        foreach ( (array)$nodes as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return self::page( $items, $total, $offset, $limit );
    }

    /** The items of the classes that a user owns, newest first. */
    protected static function ownedBy( array $classes, $userObjectId, array $args, $limitIndex, $offsetIndex )
    {
        list( $limit, $offset ) = self::paging( $args, $limitIndex, $offsetIndex );
        $p = array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ),
            'AttributeFilter' => array( array( 'owner', '=', (int)$userObjectId ) ), 'Limit' => $limit, 'Offset' => $offset );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $p, self::root() );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $p, self::root() );
        $items = array();
        foreach ( (array)$nodes as $n )
            $items[] = expCommerceExport::contentItem( $n );
        return self::page( $items, $total, $offset, $limit );
    }

    /** Words in the name and text fields of items of the classes below a node. */
    protected static function searchIn( array $classes, $text, $parentId, array $args, $limitIndex, $offsetIndex )
    {
        $text = mb_strtolower( trim( $text ) );
        if ( $text === '' )
            throw new expServiceException( 'The search text is empty', 400 );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'ClassFilterType' => 'include', 'ClassFilterArray' => $classes, 'SortBy' => array( array( 'published', false ), array( 'node_id', false ) ), 'Limit' => 2000 ), $parentId );
        $hits = array();
        foreach ( (array)$nodes as $n )
        {
            $item = expCommerceExport::contentItem( $n );
            $hay = mb_strtolower( $item['name'] . ' ' . implode( ' ', array_map( 'strval', $item['fields'] ) ) );
            if ( mb_strpos( $hay, $text ) !== false )
                $hits[] = $item;
        }
        return self::pageOf( $hits, $args, $limitIndex, $offsetIndex );
    }

    /** Hides or reveals a node (moderation); the policy content/hide applies. */
    protected static function setHidden( eZContentObjectTreeNode $node, $hide )
    {
        if ( !$node->canHide() )
            throw new expServiceException( 'No hide access to this item', 403 );
        if ( $hide )
            eZContentObjectTreeNode::hideSubTree( $node );
        else
            eZContentObjectTreeNode::unhideSubTree( $node );
        return expCommerceExport::contentItem( eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) ) );
    }

    /** Moves an item below another parent of an allowed class. */
    protected static function moveNode( eZContentObjectTreeNode $node, eZContentObjectTreeNode $target )
    {
        if ( !$node->canMoveFrom() || !$target->canCreate() )
            throw new expServiceException( 'No move access', 403 );
        if ( !eZContentObjectTreeNodeOperations::move( (int)$node->attribute( 'node_id' ), (int)$target->attribute( 'node_id' ) ) )
            throw new expServiceException( 'The item could not be moved there', 422 );
        return expCommerceExport::contentItem( eZContentObjectTreeNode::fetch( (int)$node->attribute( 'node_id' ) ) );
    }
}
