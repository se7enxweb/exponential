<?php
/**
 * Subitems list columns about the addresses of a node: its URL alias, its system URL, the
 * address visitors use on the public site, custom and historic aliases.
 *
 * Field= picks the column: url_alias, system_url, public_path, public_url, url_slug, alias_count,
 * custom_aliases, custom_alias_count, history_count, location_count, other_locations, main_location.
 * url_alias, system_url and url_slug read the loaded node; public_path and public_url add the
 * public siteaccess's site.ini (read once per request); the alias fields cost one query on
 * ezurlalias_ml, the location fields one on ezcontentobject_tree, per node or, prefetched, per page.
 *
 * The public siteaccess is SiteAccess= in the column block, or else [SiteSettings] DefaultAccess.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expSubitemsURLColumn extends expSubitemsFieldColumn
{
    /**
     * The alias the admin uses: the full path from the content root, no PathPrefix of the admin
     * applied.
     *
     * The kernel's url_alias is empty in two cases, and the column tells them apart from the
     * alias rows (ezurlalias_ml) instead of showing nothing:
     * - the node owns the root element (the row with parent 0 and text ''): it is the page at "/",
     *   so the column shows "/" (on alpha: the front page node, "Fit & Healthy");
     * - the node is [NodeSettings] RootNode, for which pathWithNames() always answers '': when the
     *   root element belongs to another node, the content root's own alias ("websites") is shown.
     */
    protected function fieldUrlAlias( eZContentObjectTreeNode $node )
    {
        $alias = eZURLAliasML::cleanURL( (string)$node->attribute( 'url_alias' ) );
        if ( $alias !== '' )
            return $alias;
        $root = $this->rootAlias( $node );
        return $root === '' ? '/' : $root;
    }

    /** The address that always works, whatever the aliases: content/view/full/<node id>. */
    protected function fieldSystemUrl( eZContentObjectTreeNode $node )
    {
        return 'content/view/full/' . (int)$node->attribute( 'node_id' );
    }

    /** The last element of the URL alias. */
    protected function fieldUrlSlug( eZContentObjectTreeNode $node )
    {
        $alias = $this->fieldUrlAlias( $node );
        if ( $alias === '/' )
            return null; // the site root has no last element
        $parts = explode( '/', $alias );
        return (string)end( $parts );
    }

    /** The path visitors use on the public siteaccess: the alias with that siteaccess's PathPrefix removed. */
    protected function fieldPublicPath( eZContentObjectTreeNode $node )
    {
        $path = self::publicPath( $node, $this->publicIni(), $this->ownPath( $node ) );
        return $path === '' ? '/' : $path; // '' is the public site's root page
    }

    /** The full address on the public site: scheme, [SiteSettings] SiteURL of the public siteaccess and the public path. */
    protected function fieldPublicUrl( eZContentObjectTreeNode $node )
    {
        $ini = $this->publicIni();
        if ( !$ini )
            return null;
        $siteURL = trim( (string)$ini->variable( 'SiteSettings', 'SiteURL' ), '/ ' );
        if ( $siteURL === '' )
            return null;
        if ( !preg_match( '#^https?://#i', $siteURL ) )
        {
            $scheme = strtolower( (string)$this->setting( 'Scheme', 'https' ) );
            $siteURL = ( $scheme === 'http' ? 'http' : 'https' ) . '://' . $siteURL;
        }
        $path = self::publicPath( $node, $ini, $this->ownPath( $node ) );
        return $siteURL . '/' . ( $path === null ? '' : $path );
    }

    /** Original aliases of the node in all its languages (one per translation, normally). */
    protected function fieldAliasCount( eZContentObjectTreeNode $node )
    {
        return count( $this->aliasRowsOfKind( $node, 'original' ) );
    }

    /**
     * Every original alias of the node, in all its languages, as full paths ("/" for the site root
     * element). A node that owns the root element usually has a named one as well (its children
     * hang below that one), so this shows both.
     */
    protected function fieldAllAliases( eZContentObjectTreeNode $node )
    {
        $paths = array();
        foreach ( $this->aliasRowsOfKind( $node, 'original' ) as $row )
        {
            $path = eZURLAliasML::cleanURL( (string)$row->getPath() );
            $path = $path === '' ? '/' : $path;
            $paths[$path] = $path;
        }
        return array_values( $paths );
    }

    /** The custom aliases made for the node (Manage URL aliases), as paths. */
    protected function fieldCustomAliases( eZContentObjectTreeNode $node )
    {
        $paths = array();
        foreach ( $this->aliasRowsOfKind( $node, 'custom' ) as $row )
        {
            $path = $row->getPath();
            if ( $path !== '' && $path !== null )
                $paths[$path] = $path;
        }
        return array_values( $paths );
    }

    protected function fieldCustomAliasCount( eZContentObjectTreeNode $node )
    {
        return count( $this->aliasRowsOfKind( $node, 'custom' ) );
    }

    /** Old addresses (after renames and moves) that still redirect to the node. */
    protected function fieldHistoryCount( eZContentObjectTreeNode $node )
    {
        return count( $this->aliasRowsOfKind( $node, 'history' ) );
    }

    /** How many locations (nodes) the object has. */
    protected function fieldLocationCount( eZContentObjectTreeNode $node )
    {
        return count( $this->locations( $node ) );
    }

    /** The URL aliases of the object's other locations. */
    protected function fieldOtherLocations( eZContentObjectTreeNode $node )
    {
        $paths = array();
        foreach ( $this->locations( $node ) as $location )
        {
            if ( (int)$location->attribute( 'node_id' ) !== (int)$node->attribute( 'node_id' ) )
                $paths[] = (string)$location->attribute( 'url_alias' );
        }
        return $paths;
    }

    /** The URL alias of the main location when this row is not it; null when it is. */
    protected function fieldMainLocation( eZContentObjectTreeNode $node )
    {
        if ( $node->isMain() )
            return null;
        foreach ( $this->locations( $node ) as $location )
        {
            if ( (int)$location->attribute( 'node_id' ) === (int)$node->attribute( 'main_node_id' ) )
                return (string)$location->attribute( 'url_alias' );
        }
        return null;
    }

    /**
     * The alias and location fields read their rows for every node. url_alias, url_slug,
     * public_path, public_url and main_location are left out: they read the alias rows (or the
     * locations) only for the odd node (the site root, a secondary location), so loading them
     * for the whole page costs more than it saves.
     */
    protected static function prefetchSets()
    {
        return array( 'Aliases' => array( 'alias_count', 'all_aliases', 'custom_aliases', 'custom_alias_count', 'history_count' ),
                      'Locations' => array( 'location_count', 'other_locations' ) );
    }

    /** The alias rows of every node of the page, one query (the rows fetchByAction() gives per node). */
    protected function prefetchAliases( array $nodes )
    {
        $ids = self::notMemoised( 'aliases', self::nodeIDs( $nodes ) );
        $db = self::sqlDatabase();
        if ( !$ids || !$db )
            return;
        $actions = array();
        foreach ( $ids as $id )
            $actions[] = "'" . $db->escapeString( 'eznode:' . $id ) . "'";
        $rows = $db->arrayQuery( 'SELECT * FROM ezurlalias_ml WHERE action IN ( ' . implode( ', ', $actions ) . ' )' );
        if ( !is_array( $rows ) )
            return;
        $byNode = array_fill_keys( $ids, array() );
        foreach ( $rows as $row )
            $byNode[(int)substr( $row['action'], strlen( 'eznode:' ) )][] = $row;
        foreach ( $byNode as $id => $nodeRows )
            self::remember( 'aliases', $id, $nodeRows ? eZPersistentObject::handleRows( $nodeRows, 'eZURLAliasML', true ) : array() );
    }

    /** The locations of every object of the page, one query (the nodes fetchByContentObjectID() gives per object). */
    protected function prefetchLocations( array $nodes )
    {
        $ids = self::notMemoised( 'locations', self::objectIDs( $nodes ) );
        if ( !$ids )
            return;
        $locations = eZPersistentObject::fetchObjectList( eZContentObjectTreeNode::definition(), null,
                                                          array( 'contentobject_id' => array( $ids ) ), null, null, true );
        if ( !is_array( $locations ) )
            return;
        $byObject = array_fill_keys( $ids, array() );
        foreach ( $locations as $location )
            $byObject[(int)$location->attribute( 'contentobject_id' )][] = $location;
        foreach ( $byObject as $id => $objectLocations )
            self::remember( 'locations', $id, $objectLocations );
    }

    /**
     * The node's full path (no PathPrefix taken off), '' for the site root; an empty
     * pathWithNames() is resolved from the alias rows as fieldUrlAlias() does.
     */
    protected function ownPath( eZContentObjectTreeNode $node )
    {
        $path = eZURLAliasML::cleanURL( (string)$node->pathWithNames() );
        return $path !== '' ? $path : $this->rootAlias( $node );
    }

    /**
     * The path of a node whose kernel alias is empty: '' when it owns the root element (or has no
     * alias at all), else its own first original alias (the content root's "websites").
     */
    protected function rootAlias( eZContentObjectTreeNode $node )
    {
        $own = $this->fieldAllAliases( $node );
        return !$own || in_array( '/', $own, true ) ? '' : $own[0];
    }

    /**
     * The node's alias rows of one kind: 'original' (its addresses, one per language), 'custom'
     * (made in Manage URL aliases) or 'history' (old addresses that redirect).
     */
    protected function aliasRowsOfKind( eZContentObjectTreeNode $node, $kind )
    {
        $rows = array();
        foreach ( $this->aliasRows( $node ) as $row )
        {
            $original = (int)$row->attribute( 'is_original' ) === 1;
            $alias = (int)$row->attribute( 'is_alias' ) === 1;
            if ( ( $kind === 'original' && $original && !$alias ) || ( $kind === 'custom' && $original && $alias )
                 || ( $kind === 'history' && !$original ) )
                $rows[] = $row;
        }
        return $rows;
    }

    /** All rows of ezurlalias_ml for the node (originals, custom aliases, history), one query. */
    protected function aliasRows( eZContentObjectTreeNode $node )
    {
        $nodeID = (int)$node->attribute( 'node_id' );
        return self::memo( 'aliases', $nodeID, function () use ( $nodeID )
        {
            $rows = eZURLAliasML::fetchByAction( 'eznode', $nodeID, false, false, true );
            return is_array( $rows ) ? $rows : array();
        } );
    }

    /** Every location of the node's object, one query, memoised per object. */
    protected function locations( eZContentObjectTreeNode $node )
    {
        $objectID = (int)$node->attribute( 'contentobject_id' );
        return self::memo( 'locations', $objectID, function () use ( $objectID )
        {
            $nodes = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
            return is_array( $nodes ) ? $nodes : array();
        } );
    }

    /** The site.ini of the column's public siteaccess (SiteAccess=). */
    protected function publicIni()
    {
        return self::publicSiteIni( $this->setting( 'SiteAccess', '' ) );
    }

    /**
     * The site.ini of the public siteaccess $siteAccess ('' = [SiteSettings] DefaultAccess), read
     * once per request; null when the siteaccess is unknown.
     */
    public static function publicSiteIni( $siteAccess = '' )
    {
        $siteAccess = trim( (string)$siteAccess );
        if ( $siteAccess === '' )
            $siteAccess = (string)eZINI::instance()->variable( 'SiteSettings', 'DefaultAccess' );
        if ( $siteAccess === '' || !preg_match( '/^[A-Za-z0-9_\-]+$/', $siteAccess ) )
            return null;
        return self::memo( 'siteini', $siteAccess, function () use ( $siteAccess )
        {
            $list = eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
            return is_array( $list ) && in_array( $siteAccess, $list, true ) ? eZSiteAccess::getIni( $siteAccess, 'site.ini' ) : null;
        } );
    }

    /**
     * The node's path on the siteaccess whose site.ini is $ini: the URL alias with its PathPrefix
     * taken off (unless PathPrefixExclude[] keeps it), exactly as eZContentObjectTreeNode::urlAlias()
     * does for the current siteaccess.
     */
    public static function publicPath( eZContentObjectTreeNode $node, $ini, $path = null )
    {
        $path = $path === null ? (string)$node->pathWithNames() : (string)$path;
        if ( !$ini instanceof eZINI )
            return eZURLAliasML::cleanURL( $path );

        $prefix = $ini->hasVariable( 'SiteAccessSettings', 'PathPrefix' )
            ? eZURLAliasML::cleanURL( (string)$ini->variable( 'SiteAccessSettings', 'PathPrefix' ) ) : '';
        if ( $prefix === '' )
            return eZURLAliasML::cleanURL( $path );

        $exclude = $ini->hasVariable( 'SiteAccessSettings', 'PathPrefixExclude' )
            ? (array)$ini->variable( 'SiteAccessSettings', 'PathPrefixExclude' ) : array();
        foreach ( $exclude as $item )
        {
            if ( $item !== '' && preg_match( '#^' . preg_quote( eZURLAliasML::cleanURL( $item ), '#' ) . '(/.*)?$#i', $path ) )
                return eZURLAliasML::cleanURL( $path );
        }

        if ( strcasecmp( $path, $prefix ) === 0 )
            return '';
        if ( strncasecmp( $path, $prefix . '/', strlen( $prefix ) + 1 ) === 0 )
            return eZURLAliasML::cleanURL( substr( $path, strlen( $prefix ) + 1 ) );
        return eZURLAliasML::cleanURL( $path );
    }
}
