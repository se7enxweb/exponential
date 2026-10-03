<?php
/**
 * Shared helpers of the media services (images, files, media): finding a node's attribute of one datatype,
 * absolute URLs, and the paged listing of the nodes of some classes below a parent.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

abstract class expAttrServiceBase extends expServiceBase
{
    /**
     * The attribute of a datatype on a node's object (current version, current language).
     *
     * @param eZContentObjectTreeNode $node
     * @param string $datatype ezimage, ezbinaryfile, ezmedia, eztags
     * @param string|null $identifier attribute identifier; null: the first of that datatype with content, else the first
     * @return eZContentObjectAttribute
     * @throws expServiceException 404 when the node has no such attribute
     */
    protected static function attributeOf( eZContentObjectTreeNode $node, $datatype, $identifier = null )
    {
        $map = $node->dataMap();
        if ( $identifier !== null && $identifier !== '' )
        {
            if ( !isset( $map[$identifier] ) || $map[$identifier]->attribute( 'data_type_string' ) !== $datatype )
                throw new expServiceException( "Node " . $node->attribute( 'node_id' ) . " has no $datatype attribute '$identifier'", 404 );
            return $map[$identifier];
        }
        $first = null;
        foreach ( $map as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) !== $datatype )
                continue;
            if ( $first === null )
                $first = $attribute;
            if ( $attribute->hasContent() )
                return $attribute;
        }
        if ( $first === null )
            throw new expServiceException( "Node " . $node->attribute( 'node_id' ) . " has no $datatype attribute", 404 );
        return $first;
    }

    /** The attributes of a datatype on a node. @return eZContentObjectAttribute[] */
    protected static function attributesOf( eZContentObjectTreeNode $node, $datatype )
    {
        $list = array();
        foreach ( $node->dataMap() as $identifier => $attribute )
            if ( $attribute->attribute( 'data_type_string' ) === $datatype )
                $list[$identifier] = $attribute;
        return $list;
    }

    /** An absolute URL for a site path (/var/... or a module URL). */
    protected static function absolute( $path )
    {
        if ( preg_match( '#^https?://#i', (string)$path ) )
            return $path;
        $host = eZSys::serverURL();
        if ( !$host )
        {
            $site = eZINI::instance( 'site.ini' )->variable( 'SiteSettings', 'SiteURL' );
            $host = 'https://' . $site;
        }
        return rtrim( $host, '/' ) . '/' . ltrim( $path, '/' );
    }

    /** A module URL through the site's URL rules (index.php and siteaccess prefix). */
    protected static function moduleUrl( $uri )
    {
        $url = '/' . ltrim( $uri, '/' );
        eZURI::transformURI( $url, false, 'full' );
        return $url;
    }

    /**
     * The nodes of some classes below a parent the user may read, paged.
     *
     * @param int $parentId
     * @param string[] $classes class identifiers, empty: any
     * @param int $limit
     * @param int $offset
     * @param array $extra more eZContentObjectTreeNode::subTreeByNodeID parameters
     * @return array array( nodes, total )
     */
    protected static function nodesBelow( $parentId, array $classes, $limit, $offset, array $extra = array() )
    {
        self::node( $parentId, 'read' );
        $params = array( 'Offset' => $offset, 'Limit' => $limit, 'SortBy' => array( 'modified', false ), 'LoadDataMap' => false );
        if ( $classes )
        {
            $params['ClassFilterType'] = 'include';
            $params['ClassFilterArray'] = $classes;
        }
        $params = array_merge( $params, $extra );
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $params, (int)$parentId );
        $count = $params;
        unset( $count['Offset'], $count['Limit'], $count['SortBy'] );
        $total = eZContentObjectTreeNode::subTreeCountByNodeID( $count, (int)$parentId );
        return array( is_array( $nodes ) ? $nodes : array(), (int)$total );
    }

    /** The small description of a node every media service starts its answer with. */
    protected static function exportNode( eZContentObjectTreeNode $node )
    {
        $object = $node->object();
        return array( 'node_id' => (int)$node->attribute( 'node_id' ), 'object_id' => (int)$node->attribute( 'contentobject_id' ),
                      'name' => $node->attribute( 'name' ), 'class' => $object ? $object->attribute( 'class_identifier' ) : '',
                      'url_alias' => $node->attribute( 'url_alias' ), 'modified' => self::iso( $object ? $object->attribute( 'modified' ) : 0 ) );
    }

    /** Runs a query on the installation's database and returns the rows. */
    protected static function rows( $sql, array $params = array() )
    {
        $rows = eZDB::instance()->arrayQuery( $sql, $params );
        return is_array( $rows ) ? $rows : array();
    }

    /** One number from a query. */
    protected static function scalar( $sql, $column = 'n' )
    {
        $rows = self::rows( $sql );
        return $rows ? (int)$rows[0][$column] : 0;
    }

    /** A class identifier list from the services' ini or a default. */
    protected static function classList( $ini, $group, $setting, array $default )
    {
        $i = eZINI::instance( $ini );
        if ( $i->hasVariable( $group, $setting ) )
        {
            $v = (array)$i->variable( $group, $setting );
            if ( $v )
                return $v;
        }
        return $default;
    }
}
