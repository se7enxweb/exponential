<?php
/**
 * ezjscore/call/expwishlist::<method>: the wish list of the logged-in user, and for shop administrators the wish
 * lists of all users.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expWishlistServices extends expServiceBase
{
    public static $services = array(
        'view' => array( 'summary' => 'The wish list of the logged-in user with its lines', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'wish list id, items, totals' ),
        'items' => array( 'summary' => 'The lines of the wish list', 'access' => 'user', 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of lines' ),
        'count' => array( 'summary' => 'How many lines the wish list has', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'contains' => array( 'summary' => 'Whether a product is on the wish list', 'access' => 'user', 'write' => false, 'args' => array( 'object_id' => 'int' ), 'returns' => 'contains, item_id' ),
        'add' => array( 'summary' => 'Puts a product on the wish list (options as JSON attribute id => choice id)', 'access' => 'user', 'write' => true,
            'args' => array( 'object_id' => 'int POST', 'options' => 'json POST' ), 'returns' => 'the wish list' ),
        'remove' => array( 'summary' => 'Removes one line', 'access' => 'user', 'write' => true, 'args' => array( 'item_id' => 'int POST' ), 'returns' => 'the wish list' ),
        'empty' => array( 'summary' => 'Removes every line', 'access' => 'user', 'write' => true, 'args' => array(), 'returns' => 'the empty wish list' ),
        'moveToBasket' => array( 'summary' => 'Puts a wish list line in the basket and removes it from the list', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array( 'item_id' => 'int POST', 'quantity' => 'int POST' ), 'returns' => 'the wish list' ),
        'adminList' => array( 'summary' => 'The wish lists of all users with their size', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of wish lists' ),
        'adminView' => array( 'summary' => 'The wish list of one user by object id', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'user_id' => 'int' ), 'returns' => 'wish list' ),
        'popular' => array( 'summary' => 'The products on most wish lists', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'limit' => 'int' ), 'returns' => 'list of product and count' ),
    );

    protected static function mine()
    {
        return eZWishList::currentWishList();
    }

    protected static function encode( eZWishList $w )
    {
        $collection = eZProductCollection::fetch( $w->attribute( 'productcollection_id' ) );
        $items = $collection ? expCommerceExport::items( $collection ) : array();
        return array( 'wishlist_id' => (int)$w->attribute( 'id' ), 'user_id' => (int)$w->attribute( 'user_id' ), 'items' => $items, 'totals' => expCommerceExport::totals( $items ) );
    }

    protected static function line( eZWishList $w, $id )
    {
        $i = eZProductCollectionItem::fetch( (int)$id );
        if ( !$i instanceof eZProductCollectionItem || (int)$i->attribute( 'productcollection_id' ) !== (int)$w->attribute( 'productcollection_id' ) )
            throw new expServiceException( "No line $id on your wish list", 404 );
        return $i;
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( self::encode( self::mine() ) );
    }

    public static function items( array $args )
    {
        self::guard( 'items' );
        return self::pageOf( self::encode( self::mine() )['items'], $args, 0, 1 );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => count( self::encode( self::mine() )['items'] ) ) );
    }

    public static function contains( array $args )
    {
        self::guard( 'contains' );
        $oid = self::arg( $args, 0, 'int' );
        foreach ( self::encode( self::mine() )['items'] as $i )
            if ( $i['object_id'] === $oid )
                return self::ok( array( 'contains' => true, 'item_id' => $i['id'] ) );
        return self::ok( array( 'contains' => false, 'item_id' => null ) );
    }

    public static function add( array $args )
    {
        self::guard( 'add' );
        $oid = self::post( 'object_id', 'int' );
        $options = self::post( 'options', 'json', array() );
        $object = eZContentObject::fetch( $oid );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'No such object', 404 );
        if ( !$object->canRead() )
            throw new expServiceException( 'No read access to the product', 403 );
        if ( !eZShopFunctions::isProductObject( $object ) )
            throw new expServiceException( 'The object is not a product', 422 );
        $w = self::mine();
        $enc = self::encode( $w );
        foreach ( $enc['items'] as $i )
            if ( $i['object_id'] === $oid && !$options && !$i['options'] )
                return self::ok( $enc );
        $db = eZDB::instance();
        $db->begin();
        $item = eZProductCollectionItem::create( $w->attribute( 'productcollection_id' ) );
        $item->setAttribute( 'name', $object->attribute( 'name' ) );
        $item->setAttribute( 'contentobject_id', $oid );
        $item->setAttribute( 'item_count', 1 );
        $item->store();
        foreach ( is_array( $options ) ? $options : array() as $attributeId => $choices )
            foreach ( (array)$choices as $choice )
            {
                $attribute = eZContentObjectAttribute::fetch( (int)$attributeId, $object->attribute( 'current_version' ) );
                if ( !$attribute )
                    continue;
                $data = $attribute->dataType()->productOptionInformation( $attribute, $choice, $item );
                if ( $data )
                    eZProductCollectionItemOption::create( $item->attribute( 'id' ), $data['id'], $data['name'], $data['value'], 0, (int)$attributeId )->store();
            }
        $db->commit();
        return self::ok( self::encode( $w ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        $w = self::mine();
        self::line( $w, self::post( 'item_id', 'int' ) )->remove();
        return self::ok( self::encode( $w ) );
    }

    public static function empty( array $args )
    {
        self::guard( 'empty' );
        $w = self::mine();
        $c = eZProductCollection::fetch( $w->attribute( 'productcollection_id' ) );
        if ( $c )
            foreach ( $c->itemList() as $i )
                $i->remove();
        return self::ok( self::encode( $w ) );
    }

    public static function moveToBasket( array $args )
    {
        self::guard( 'moveToBasket' );
        $w = self::mine();
        $item = self::line( $w, self::post( 'item_id', 'int' ) );
        $oid = (int)$item->attribute( 'contentobject_id' );
        $options = array();
        foreach ( $item->attribute( 'option_list' ) as $o )
            $options[(int)$o->attribute( 'object_attribute_id' )] = $o->attribute( 'option_item_id' );
        $q = self::post( 'quantity', 'int', max( 1, (int)$item->attribute( 'item_count' ) ) );
        expServiceBase::$postData = array( 'object_id' => $oid, 'quantity' => $q, 'options' => $options );
        $r = expBasketServices::invoke( 'expBasketServices', 'add' );
        if ( !$r['ok'] )
            throw new expServiceException( $r['error']['message'], $r['error']['code'] );
        $item->remove();
        return self::ok( array( 'wishlist' => self::encode( $w ), 'basket' => $r['data'] ) );
    }

    public static function adminList( array $args )
    {
        self::guard( 'adminList' );
        $out = array();
        foreach ( (array)eZPersistentObject::fetchObjectList( eZWishList::definition(), null, null, array( 'id' => 'desc' ) ) as $w )
        {
            $e = self::encode( $w );
            $u = eZContentObject::fetch( (int)$w->attribute( 'user_id' ) );
            $out[] = array( 'wishlist_id' => $e['wishlist_id'], 'user_id' => $e['user_id'], 'user_name' => $u ? $u->attribute( 'name' ) : null, 'lines' => count( $e['items'] ) );
        }
        return self::pageOf( $out, $args, 0, 1 );
    }

    public static function adminView( array $args )
    {
        self::guard( 'adminView' );
        $uid = self::arg( $args, 0, 'int' );
        $list = eZPersistentObject::fetchObjectList( eZWishList::definition(), null, array( 'user_id' => $uid ), null, array( 'limit' => 1 ) );
        if ( !$list )
            throw new expServiceException( 'That user has no wish list', 404 );
        return self::ok( self::encode( $list[0] ) );
    }

    public static function popular( array $args )
    {
        self::guard( 'popular' );
        $limit = min( self::arg( $args, 0, 'int', 10 ), 100 );
        $rows = eZDB::instance()->arrayQuery( 'SELECT i.contentobject_id AS oid, COUNT(*) AS c FROM ezwishlist w, ezproductcollection_item i WHERE i.productcollection_id=w.productcollection_id GROUP BY i.contentobject_id ORDER BY c DESC', array( 'limit' => $limit ) );
        $out = array();
        foreach ( $rows as $r )
        {
            $o = eZContentObject::fetch( (int)$r['oid'] );
            $out[] = array( 'object_id' => (int)$r['oid'], 'name' => $o ? $o->attribute( 'name' ) : null, 'wishlists' => (int)$r['c'] );
        }
        return self::ok( $out );
    }
}
