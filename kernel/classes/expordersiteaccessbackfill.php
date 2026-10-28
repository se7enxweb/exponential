<?php
/**
 * File containing the expOrderSiteaccessBackfill class.
 *
 * Infers the siteaccess of an order placed before ezorder.siteaccess was recorded, from the content tree each
 * siteaccess serves (bin/php/expbackfillordersiteaccess.php, doc/eng-us/features/6.0/order-siteaccess.md).
 *
 * A siteaccess serves the subtree of its content root: the node its [SiteAccessSettings] PathPrefix (site.ini)
 * names when it has one, else its [NodeSettings] RootNode (content.ini), as the kernel resolves addresses for it.
 * An order whose products (the main location of each item's object) all lie in the tree of exactly one siteaccess
 * gets that siteaccess. An order matching none or several stays without one (Unknown), and so does an order with a
 * product that is no longer in the tree. With "prefer the deepest root", of several siteaccesses that serve every
 * product the one whose root is deepest wins when it is the only one that deep: a shop's own siteaccess beats an
 * administration siteaccess that serves the whole tree.
 *
 * The inference (infer()) works on path strings only, so it needs no database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expOrderSiteaccessBackfill
{
    /**
     * A node path string as the kernel writes it, "/1/2/61/", from "/1/2/61", "1/2/61/" and the like; '' when it
     * is none.
     *
     * @param mixed $path
     * @return string
     */
    public static function normalizePath( $path )
    {
        if ( !is_scalar( $path ) )
            return '';
        $parts = array_values( array_filter( explode( '/', (string)$path ), function ( $part ) {
            return $part !== '';
        } ) );
        foreach ( $parts as $part )
        {
            if ( !ctype_digit( $part ) )
                return '';
        }
        return $parts ? '/' . implode( '/', $parts ) . '/' : '';
    }

    /**
     * The siteaccesses whose tree holds a path, with the depth of their root.
     *
     * @param string $path a node path string
     * @param array $roots siteaccess => root path string
     * @return array siteaccess => depth of its root (number of nodes in the root's path)
     */
    public static function siteaccessesServing( $path, array $roots )
    {
        $path = self::normalizePath( $path );
        $serving = array();
        if ( $path === '' )
            return $serving;
        foreach ( $roots as $siteaccess => $root )
        {
            $root = self::normalizePath( $root );
            if ( $root !== '' && strncmp( $path, $root, strlen( $root ) ) === 0 )
                $serving[(string)$siteaccess] = substr_count( $root, '/' ) - 1;
        }
        return $serving;
    }

    /**
     * The siteaccess of an order, from the paths of its products.
     *
     * @param array $itemPaths the main location's path string of each product item; null (or '') for a product
     *                         that is no longer in the content tree
     * @param array $roots siteaccess => root path string
     * @param bool $preferDeepestRoot of several siteaccesses that serve every product, take the one with the deepest
     *                                root when it is the only one that deep
     * @return array( 'siteaccess' => name or '', 'reason' => why )
     */
    public static function infer( array $itemPaths, array $roots, $preferDeepestRoot = false )
    {
        if ( !$itemPaths )
            return array( 'siteaccess' => '', 'reason' => 'the order has no product items' );

        $candidates = null;
        foreach ( $itemPaths as $path )
        {
            if ( self::normalizePath( $path ) === '' )
                return array( 'siteaccess' => '', 'reason' => 'a product is no longer in the content tree' );
            $serving = self::siteaccessesServing( $path, $roots );
            if ( !$serving )
                return array( 'siteaccess' => '', 'reason' => 'the product at ' . self::normalizePath( $path ) . ' lies in no siteaccess\'s tree' );
            $candidates = $candidates === null ? $serving : array_intersect_key( $candidates, $serving );
        }

        if ( !$candidates )
            return array( 'siteaccess' => '', 'reason' => 'no siteaccess serves every product of the order' );
        if ( count( $candidates ) === 1 )
            return array( 'siteaccess' => (string)key( $candidates ), 'reason' => 'the only siteaccess that serves its products' );

        ksort( $candidates );
        if ( $preferDeepestRoot )
        {
            $deepest = max( $candidates );
            $top = array_keys( $candidates, $deepest, true );
            if ( count( $top ) === 1 )
                return array( 'siteaccess' => (string)$top[0], 'reason' => 'the siteaccess with the deepest root that serves its products' );
            return array( 'siteaccess' => '', 'reason' => 'several siteaccesses with the same root serve its products: ' . implode( ', ', $top ) );
        }
        return array( 'siteaccess' => '', 'reason' => 'several siteaccesses serve its products: ' . implode( ', ', array_keys( $candidates ) ) );
    }

    /**
     * The content root of every siteaccess of AvailableSiteAccessList, as a path string: the node of its PathPrefix
     * when it has one, else its RootNode. A siteaccess whose root cannot be found is left out and named in
     * $problems.
     *
     * @param array $problems filled with siteaccess => why it was left out
     * @return array siteaccess => root path string
     */
    public static function siteaccessRoots( &$problems = array() )
    {
        $problems = array();
        $roots = array();
        foreach ( (array)eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $siteaccess )
        {
            $siteaccess = expOrderSiteaccess::normalize( $siteaccess );
            if ( $siteaccess === '' || isset( $roots[$siteaccess] ) )
                continue;
            $siteIni = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
            $contentIni = eZSiteAccess::getIni( $siteaccess, 'content.ini' );
            $prefix = $siteIni->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) ? trim( (string)$siteIni->variable( 'SiteAccessSettings', 'PathPrefix' ), '/' ) : '';
            $nodeID = 0;
            if ( $prefix !== '' )
            {
                $nodeID = (int)eZURLAliasML::fetchNodeIDByPath( $prefix );
                if ( !$nodeID )
                {
                    $problems[$siteaccess] = "its PathPrefix '$prefix' names no node";
                    continue;
                }
            }
            else
            {
                $nodeID = $contentIni->hasVariable( 'NodeSettings', 'RootNode' ) ? (int)$contentIni->variable( 'NodeSettings', 'RootNode' ) : 2;
            }
            $node = $nodeID ? eZContentObjectTreeNode::fetch( $nodeID ) : null;
            if ( !$node instanceof eZContentObjectTreeNode )
            {
                $problems[$siteaccess] = "its root node $nodeID does not exist";
                continue;
            }
            $roots[$siteaccess] = self::normalizePath( $node->attribute( 'path_string' ) );
        }
        return $roots;
    }

    /**
     * The main location's path string of each product item of an order; null for a product whose object or main
     * location is gone.
     *
     * @param eZOrder $order
     * @return array
     */
    public static function orderItemPaths( eZOrder $order )
    {
        $paths = array();
        $collection = $order->productCollection();
        if ( !$collection )
            return $paths;
        foreach ( (array)eZProductCollectionItem::fetchList( array( 'productcollection_id' => $collection->attribute( 'id' ) ) ) as $item )
        {
            $object = eZContentObject::fetch( (int)$item->attribute( 'contentobject_id' ) );
            $node = $object ? $object->mainNode() : null;
            $paths[] = $node ? (string)$node->attribute( 'path_string' ) : null;
        }
        return $paths;
    }
}
