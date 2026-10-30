<?php
/**
 * Where the layouts schema extension (expGraphQLLayoutsExtension) reads the page builder from: explayouts, its
 * published layouts, zones, blocks, block parameters and collections, as plain arrays. A test extends it to answer
 * without a database.
 *
 *   layout     id, identifier, name, type, shared, modified
 *   zone       id, identifier, layoutId, linkedLayoutId, linkedZone, sourceZoneId (the zone whose blocks it shows)
 *   block      id, zoneId, layoutId, parentId, position, definition, viewType, itemViewType, name, placeholder
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLLayoutsSource
{
    /** The status of a published layout, zone or block. */
    const PUBLISHED = 2;

    /**
     * @return bool explayouts is active here
     */
    public function isAvailable()
    {
        return class_exists( 'expLayoutsLayout' ) && class_exists( 'expLayoutsResolver' ) && class_exists( 'expLayoutsBlock' );
    }

    /**
     * @param expLayoutsLayout|false|null $layout
     * @return array|null a published layout of the site (not one of the administration)
     */
    protected function layout( $layout )
    {
        if ( !$layout instanceof expLayoutsLayout || (int)$layout->attribute( 'status' ) !== self::PUBLISHED )
            return null;
        $type = (string)$layout->attribute( 'layout_type' );
        if ( class_exists( 'expLayoutsLayoutType' ) && expLayoutsLayoutType::isAdminType( $type ) )
            return null;
        return array( 'id' => (int)$layout->attribute( 'id' ), 'identifier' => (string)$layout->attribute( 'identifier' ),
                      'name' => (string)$layout->attribute( 'name' ), 'type' => $type, 'shared' => (bool)$layout->attribute( 'shared' ),
                      'modified' => (int)$layout->attribute( 'modified' ) );
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function layoutById( $id )
    {
        return $this->layout( (int)$id > 0 ? expLayoutsLayout::fetch( (int)$id ) : null );
    }

    /**
     * @param string $identifier
     * @return array|null
     */
    public function layoutByIdentifier( $identifier )
    {
        return $this->layout( expLayoutsLayout::fetchByIdentifier( (string)$identifier, self::PUBLISHED ) );
    }

    /**
     * The layout the rules of explayouts give a page of the site (the resolver, as the page itself resolves it).
     *
     * @param string $urlAlias the URL alias of the page ('' for the front page)
     * @return array|null
     */
    public function layoutForPath( $urlAlias )
    {
        $path = trim( (string)$urlAlias, '/' );
        return $this->layout( expLayoutsResolver::resolve( $path === '' ? 'home' : $path ) );
    }

    /**
     * @param int $layoutId
     * @return array[] its published zones
     */
    public function zones( $layoutId )
    {
        $zones = array();
        foreach ( (array)expLayoutsZone::fetchByLayout( (int)$layoutId, self::PUBLISHED ) as $zone )
        {
            if ( !$zone instanceof expLayoutsZone )
                continue;
            $source = expLayoutsZone::resolveSource( $zone );
            $zones[] = array( 'id' => (int)$zone->attribute( 'id' ), 'identifier' => (string)$zone->attribute( 'identifier' ),
                              'layoutId' => (int)$zone->attribute( 'layout_id' ),
                              'linkedLayoutId' => $zone->attribute( 'linked_layout_id' ) ? (int)$zone->attribute( 'linked_layout_id' ) : null,
                              'linkedZone' => $zone->attribute( 'linked_zone_identifier' ) ? (string)$zone->attribute( 'linked_zone_identifier' ) : null,
                              'sourceZoneId' => $source ? (int)$source->attribute( 'id' ) : (int)$zone->attribute( 'id' ) );
        }
        return $zones;
    }

    /**
     * @param expLayoutsBlock $block
     * @return array
     */
    protected function block( $block )
    {
        return array( 'id' => (int)$block->attribute( 'id' ), 'zoneId' => (int)$block->attribute( 'zone_id' ), 'layoutId' => (int)$block->attribute( 'layout_id' ),
                      'parentId' => (int)$block->attribute( 'parent_id' ), 'position' => (int)$block->attribute( 'position' ),
                      'definition' => (string)$block->attribute( 'definition_identifier' ), 'viewType' => (string)$block->attribute( 'view_type' ),
                      'itemViewType' => (string)$block->attribute( 'item_view_type' ), 'name' => (string)$block->attribute( 'name' ),
                      'placeholder' => (string)$block->attribute( 'placeholder' ) );
    }

    /**
     * @param int $zoneId
     * @return array[] the published blocks at the top of a zone, in their order
     */
    public function blocks( $zoneId )
    {
        $blocks = array();
        foreach ( (array)expLayoutsBlock::fetchByZone( (int)$zoneId, self::PUBLISHED ) as $block )
        {
            if ( $block instanceof expLayoutsBlock && !(int)$block->attribute( 'parent_id' ) )
                $blocks[] = $this->block( $block );
        }
        return $blocks;
    }

    /**
     * @param int $blockId
     * @return array[] the published blocks in a block (a container's)
     */
    public function children( $blockId )
    {
        $blocks = array();
        foreach ( (array)expLayoutsBlock::fetchChildren( (int)$blockId, self::PUBLISHED ) as $block )
        {
            if ( $block instanceof expLayoutsBlock )
                $blocks[] = $this->block( $block );
        }
        return $blocks;
    }

    /**
     * The parameters of several blocks at once (one query).
     *
     * @param int[] $blockIds
     * @return array block id => array( name => value )
     */
    public function parametersOf( array $blockIds )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $blockIds ) ) ) );
        $answer = array_fill_keys( $ids, array() );
        if ( !$ids )
            return $answer;
        foreach ( (array)eZPersistentObject::fetchObjectList( expLayoutsBlockParameter::definition(), null, array( 'block_id' => array( $ids ) ),
                                                              array( 'id' => 'asc' ), null, false ) as $row )
            $answer[(int)$row['block_id']][(string)$row['name']] = (string)$row['value'];
        return $answer;
    }

    /**
     * The collections of several blocks at once, with their items (two queries).
     *
     * @param int[] $blockIds
     * @return array block id => array( type, offset, limit, nodeIds ) or null (no collection)
     */
    public function collectionsOf( array $blockIds )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $blockIds ) ) ) );
        $answer = array_fill_keys( $ids, null );
        if ( !$ids )
            return $answer;
        $byId = array();
        foreach ( (array)eZPersistentObject::fetchObjectList( expLayoutsCollection::definition(), null, array( 'block_id' => array( $ids ) ),
                                                              array( 'id' => 'asc' ), null, false ) as $row )
        {
            if ( isset( $answer[(int)$row['block_id']] ) )
                continue; // a block has one collection; the first
            $answer[(int)$row['block_id']] = array( 'type' => (string)$row['collection_type'], 'offset' => (int)$row['offset_value'],
                                                    'limit' => (int)$row['limit_value'], 'nodeIds' => array() );
            $byId[(int)$row['id']] = (int)$row['block_id'];
        }
        if ( $byId )
        {
            foreach ( (array)eZPersistentObject::fetchObjectList( expLayoutsCollectionItem::definition(), null,
                                                                  array( 'collection_id' => array( array_keys( $byId ) ) ),
                                                                  array( 'position' => 'asc', 'id' => 'asc' ), null, false ) as $item )
            {
                $blockId = $byId[(int)$item['collection_id']];
                if ( (int)$item['value_id'] > 0 )
                    $answer[$blockId]['nodeIds'][] = (int)$item['value_id'];
            }
        }
        return $answer;
    }
}

?>
