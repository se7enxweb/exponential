<?php
/**
 * ezjscore/call/exporder::<method>: the orders. A customer reads their own orders; the shop administrators
 * (policy shop/administrate) list, view, change the status of, archive and remove every order. Orders are
 * created by the checkout of the basket, never here.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expOrderServices extends expServiceBase
{
    public static $services = array(
        'mine' => array( 'summary' => 'The orders of the logged-in customer, newest first', 'access' => 'user', 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of orders' ),
        'myOrder' => array( 'summary' => 'One order of the logged-in customer with lines, extra order items and status history', 'access' => 'user', 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'order' ),
        'myCount' => array( 'summary' => 'How many orders the logged-in customer has', 'access' => 'user', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'myReceipt' => array( 'summary' => 'The receipt link and totals of one of the customer\'s orders', 'access' => 'user', 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'receipt url, totals, status' ),
        'statuses' => array( 'summary' => 'The order statuses', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'all' => 'bool' ), 'returns' => 'list of status id, name, active, internal' ),
        'list' => array( 'summary' => 'Orders of all customers, filterable by status, archived and sorted', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'show' => 'string normal|archived|all', 'sort' => 'string created|id|total|user_name', 'order' => 'string asc|desc', 'status_id' => 'int' ),
            'returns' => 'paged list of orders' ),
        'count' => array( 'summary' => 'Number of orders', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'show' => 'string normal|archived|all' ), 'returns' => 'count' ),
        'view' => array( 'summary' => 'One order by id with lines, order items and status history', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'order' ),
        'byNumber' => array( 'summary' => 'One order by its order number', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_nr' => 'int' ), 'returns' => 'order' ),
        'items' => array( 'summary' => 'The product lines of an order', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'list of lines' ),
        'orderItems' => array( 'summary' => 'The extra items of an order: shipping, discounts', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'list of order items' ),
        'history' => array( 'summary' => 'The status history of an order', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'list of status changes' ),
        'account' => array( 'summary' => 'The customer account information of an order', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'name, email, information' ),
        'statusOptions' => array( 'summary' => 'The statuses the current user may set an order to (policy FromStatus/ToStatus)', 'access' => array( 'shop', 'setstatus' ), 'write' => false,
            'args' => array( 'order_id' => 'int' ), 'returns' => 'list of status id, name' ),
        'setStatus' => array( 'summary' => 'Changes the status of an order', 'access' => array( 'shop', 'setstatus' ), 'write' => true,
            'args' => array( 'order_id' => 'int POST', 'status_id' => 'int POST' ), 'returns' => 'the order' ),
        'archive' => array( 'summary' => 'Moves an order to the archive', 'access' => array( 'shop', 'administrate' ), 'write' => true, 'args' => array( 'order_id' => 'int POST' ), 'returns' => 'the order' ),
        'unarchive' => array( 'summary' => 'Brings an archived order back', 'access' => array( 'shop', 'administrate' ), 'write' => true, 'args' => array( 'order_id' => 'int POST' ), 'returns' => 'the order' ),
        'remove' => array( 'summary' => 'Deletes an order with its lines and history (audited)', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'order_id' => 'int POST' ), 'returns' => 'removed' ),
        'search' => array( 'summary' => 'Orders by customer email or account name', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'text' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of orders' ),
        'statistics' => array( 'summary' => 'Products sold in a year or month with totals', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'year' => 'int', 'month' => 'int' ), 'returns' => 'products with count and totals' ),
        'customers' => array( 'summary' => 'The customers (distinct emails) with their order count and sum', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of customers' ),
        'customerOrders' => array( 'summary' => 'The orders of one customer by user id', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'user_id' => 'int', 'email' => 'string' ), 'returns' => 'list of orders' ),
        'customerProducts' => array( 'summary' => 'The products one customer bought', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'user_id' => 'int', 'email' => 'string' ), 'returns' => 'list of products with count' ),
        'dashboard' => array( 'summary' => 'Order counts per status and the latest orders', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'by_status, latest, totals' ),
    );

    protected static function mineOrder( $id )
    {
        $order = eZOrder::fetch( $id );
        $me = (int)eZUser::currentUser()->attribute( 'contentobject_id' );
        if ( !$order instanceof eZOrder || (int)$order->attribute( 'user_id' ) !== $me || $order->attribute( 'is_temporary' ) )
            throw new expServiceException( "No order $id of yours", 404 );
        return $order;
    }

    protected static function anyOrder( $id )
    {
        $order = eZOrder::fetch( (int)$id );
        if ( !$order instanceof eZOrder )
            throw new expServiceException( "No order $id", 404 );
        return $order;
    }

    protected static function show( array $args, $i )
    {
        $s = self::arg( $args, $i, 'string', 'normal' );
        $map = array( 'normal' => eZOrder::SHOW_NORMAL, 'archived' => eZOrder::SHOW_ARCHIVED, 'all' => eZOrder::SHOW_ALL );
        if ( !isset( $map[$s] ) )
            throw new expServiceException( 'show is normal, archived or all', 400 );
        return $map[$s];
    }

    public static function mine( array $args )
    {
        self::guard( 'mine' );
        $orders = eZOrder::activeByUserID( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) );
        $items = array();
        foreach ( $orders as $o )
            $items[] = expCommerceExport::order( $o );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function myOrder( array $args )
    {
        self::guard( 'myOrder' );
        return self::ok( expCommerceExport::order( self::mineOrder( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function myCount( array $args )
    {
        self::guard( 'myCount' );
        return self::ok( array( 'count' => count( eZOrder::activeByUserID( (int)eZUser::currentUser()->attribute( 'contentobject_id' ) ) ) ) );
    }

    public static function myReceipt( array $args )
    {
        self::guard( 'myReceipt' );
        $o = self::mineOrder( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'order_id' => (int)$o->attribute( 'id' ), 'receipt_url' => eZShopReceipt::linkURLForID( $o->attribute( 'id' ) ),
            'total_inc_vat' => expCommerceExport::num( $o->totalIncVAT() ), 'currency' => $o->currencyCode(), 'status' => $o->attribute( 'status_name' ) ) );
    }

    public static function statuses( array $args )
    {
        self::guard( 'statuses' );
        $out = array();
        foreach ( eZOrderStatus::fetchList( true, self::arg( $args, 0, 'bool', false ) ) as $s )
            $out[] = array( 'status_id' => (int)$s->attribute( 'status_id' ), 'name' => $s->attribute( 'name' ), 'active' => (bool)$s->attribute( 'is_active' ),
                'internal' => (bool)$s->isInternal() );
        return self::ok( $out );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $show = self::show( $args, 2 );
        $sort = self::arg( $args, 3, 'string', 'created' );
        $order = strtolower( self::arg( $args, 4, 'string', 'desc' ) ) === 'asc' ? 'asc' : 'desc';
        $status = self::arg( $args, 5, 'int', 0 );
        $columns = array( 'created' => 'created', 'id' => 'id', 'total' => 'id', 'user_name' => 'user_name' );
        if ( !isset( $columns[$sort] ) )
            throw new expServiceException( 'sort is created, id, total or user_name', 400 );
        $all = eZOrder::active( true, 0, 100000, $columns[$sort], $order, $show );
        $items = array();
        foreach ( (array)$all as $o )
        {
            if ( $status && (int)$o->attribute( 'status_id' ) !== $status )
                continue;
            $items[] = expCommerceExport::order( $o );
        }
        if ( $sort === 'total' )
            usort( $items, function ( $a, $b ) use ( $order ) { return $order === 'asc' ? $a['total_inc_vat'] <=> $b['total_inc_vat'] : $b['total_inc_vat'] <=> $a['total_inc_vat']; } );
        return self::page( array_slice( $items, $offset, $limit ), count( $items ), $offset, $limit );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => (int)eZOrder::activeCount( self::show( $args, 0 ) ) ) );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( expCommerceExport::order( self::anyOrder( self::arg( $args, 0, 'int' ) ), true ) );
    }

    public static function byNumber( array $args )
    {
        self::guard( 'byNumber' );
        $nr = self::arg( $args, 0, 'int' );
        $rows = eZPersistentObject::fetchObjectList( eZOrder::definition(), null, array( 'order_nr' => $nr ), null, array( 'limit' => 1 ) );
        if ( !$rows )
            throw new expServiceException( "No order number $nr", 404 );
        return self::ok( expCommerceExport::order( $rows[0], true ) );
    }

    public static function items( array $args )
    {
        self::guard( 'items' );
        $o = self::anyOrder( self::arg( $args, 0, 'int' ) );
        $c = $o->productCollection();
        return self::ok( $c ? expCommerceExport::items( $c ) : array() );
    }

    public static function orderItems( array $args )
    {
        self::guard( 'orderItems' );
        return self::ok( expCommerceExport::order( self::anyOrder( self::arg( $args, 0, 'int' ) ), true )['order_items'] );
    }

    public static function history( array $args )
    {
        self::guard( 'history' );
        return self::ok( expCommerceExport::order( self::anyOrder( self::arg( $args, 0, 'int' ) ), true )['history'] );
    }

    public static function account( array $args )
    {
        self::guard( 'account' );
        $o = self::anyOrder( self::arg( $args, 0, 'int' ) );
        $info = array();
        foreach ( (array)$o->accountInformation() as $k => $v )
            $info[$k] = is_scalar( $v ) || $v === null ? $v : null;
        return self::ok( array( 'name' => $o->accountName(), 'email' => $o->accountEmail(), 'information' => $info ) );
    }

    public static function statusOptions( array $args )
    {
        self::guard( 'statusOptions' );
        $o = self::anyOrder( self::arg( $args, 0, 'int' ) );
        $out = array();
        foreach ( (array)$o->statusModificationList() as $s )
            $out[] = array( 'status_id' => (int)$s->attribute( 'status_id' ), 'name' => $s->attribute( 'name' ) );
        return self::ok( $out );
    }

    public static function setStatus( array $args )
    {
        self::guard( 'setStatus' );
        $o = self::anyOrder( self::post( 'order_id', 'int' ) );
        $status = self::post( 'status_id', 'int' );
        if ( !eZOrderStatus::fetchByStatus( $status ) instanceof eZOrderStatus )
            throw new expServiceException( "No order status $status", 422 );
        if ( !$o->canModifyStatus( $status ) )
            throw new expServiceException( 'Your policy does not allow this status change', 403 );
        if ( (int)$o->attribute( 'status_id' ) !== $status )
            $o->modifyStatus( $status );
        return self::ok( expCommerceExport::order( eZOrder::fetch( $o->attribute( 'id' ) ), true ) );
    }

    public static function archive( array $args )
    {
        self::guard( 'archive' );
        $o = self::anyOrder( self::post( 'order_id', 'int' ) );
        eZOrder::archiveOrder( $o->attribute( 'id' ) );
        return self::ok( expCommerceExport::order( eZOrder::fetch( $o->attribute( 'id' ) ) ) );
    }

    public static function unarchive( array $args )
    {
        self::guard( 'unarchive' );
        $o = self::anyOrder( self::post( 'order_id', 'int' ) );
        eZOrder::unArchiveOrder( $o->attribute( 'id' ) );
        return self::ok( expCommerceExport::order( eZOrder::fetch( $o->attribute( 'id' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        $o = self::anyOrder( self::post( 'order_id', 'int' ) );
        eZOrder::cleanupOrder( $o->attribute( 'id' ) );
        return self::ok( array( 'removed' => (int)$o->attribute( 'id' ) ) );
    }

    public static function search( array $args )
    {
        self::guard( 'search' );
        $text = mb_strtolower( trim( self::arg( $args, 0, 'string' ) ) );
        if ( $text === '' )
            throw new expServiceException( 'The search text is empty', 400 );
        $items = array();
        foreach ( (array)eZOrder::active( true, 0, 100000, 'created', 'desc', eZOrder::SHOW_ALL ) as $o )
            if ( mb_strpos( mb_strtolower( $o->attribute( 'email' ) . ' ' . $o->accountName() ), $text ) !== false )
                $items[] = expCommerceExport::order( $o );
        return self::pageOf( $items, $args, 1, 2 );
    }

    public static function statistics( array $args )
    {
        self::guard( 'statistics' );
        $year = self::arg( $args, 0, 'int', 0 );
        $month = self::arg( $args, 1, 'int', 0 );
        if ( $month && !$year )
            throw new expServiceException( 'A month needs a year', 400 );
        $s = eZOrder::orderStatistics( $year ?: false, $month ?: false );
        $products = array();
        $total = array();
        if ( isset( $s[0] ) )
        {
            foreach ( (array)$s[0]['product_list'] as $p )
            {
                $o = isset( $p['object'] ) ? $p['object'] : null;
                $products[] = array( 'object_id' => $o ? (int)$o->attribute( 'id' ) : null, 'name' => $o ? $o->attribute( 'name' ) : ( isset( $p['name'] ) ? $p['name'] : null ),
                    'count' => isset( $p['sum_count'] ) ? (int)$p['sum_count'] : null );
            }
            foreach ( (array)$s[0]['total_sum_info'] as $cur => $t )
                $total[] = array( 'currency' => $cur, 'sum_count' => isset( $t['sum_count'] ) ? $t['sum_count'] : null, 'sum_ex_vat' => isset( $t['sum_ex_vat'] ) ? expCommerceExport::num( $t['sum_ex_vat'] ) : null,
                    'sum_inc_vat' => isset( $t['sum_inc_vat'] ) ? expCommerceExport::num( $t['sum_inc_vat'] ) : null );
        }
        return self::ok( array( 'year' => $year ?: null, 'month' => $month ?: null, 'products' => $products, 'totals' => $total ) );
    }

    public static function customers( array $args )
    {
        self::guard( 'customers' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT email, MAX(user_id) AS user_id, COUNT(*) AS orders, MAX(created) AS last_order FROM ezorder WHERE is_temporary='0' GROUP BY email ORDER BY email",
            array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( $rows as $r )
            $items[] = array( 'email' => $r['email'], 'user_id' => (int)$r['user_id'], 'orders' => (int)$r['orders'], 'last_order' => $r['last_order'] ? gmdate( 'c', (int)$r['last_order'] ) : null );
        return self::page( $items, (int)eZOrder::customerCount(), $offset, $limit );
    }

    public static function customerOrders( array $args )
    {
        self::guard( 'customerOrders' );
        $items = array();
        foreach ( eZOrder::orderList( self::arg( $args, 0, 'int' ), self::arg( $args, 1, 'string', '' ) ) as $o )
            $items[] = expCommerceExport::order( $o );
        return self::ok( $items );
    }

    public static function customerProducts( array $args )
    {
        self::guard( 'customerProducts' );
        $r = eZOrder::productList( self::arg( $args, 0, 'int' ), self::arg( $args, 1, 'string', '' ) );
        $items = array();
        foreach ( isset( $r[0]['product_list'] ) ? $r[0]['product_list'] : array() as $p )
        {
            $o = isset( $p['object'] ) ? $p['object'] : null;
            $items[] = array( 'object_id' => $o ? (int)$o->attribute( 'id' ) : null, 'name' => $o ? $o->attribute( 'name' ) : null,
                'count' => isset( $p['sum_count'] ) ? (int)$p['sum_count'] : null );
        }
        return self::ok( $items );
    }

    public static function dashboard( array $args )
    {
        self::guard( 'dashboard' );
        $db = eZDB::instance();
        $by = array();
        foreach ( $db->arrayQuery( "SELECT status_id, COUNT(*) AS c FROM ezorder WHERE is_temporary='0' GROUP BY status_id" ) as $r )
        {
            $s = eZOrderStatus::fetchByStatus( (int)$r['status_id'] );
            $by[] = array( 'status_id' => (int)$r['status_id'], 'name' => $s ? $s->attribute( 'name' ) : null, 'orders' => (int)$r['c'] );
        }
        $latest = array();
        foreach ( (array)eZOrder::active( true, 0, 10, 'created', 'desc' ) as $o )
            $latest[] = expCommerceExport::order( $o );
        return self::ok( array( 'by_status' => $by, 'latest' => $latest, 'orders' => (int)eZOrder::activeCount( eZOrder::SHOW_ALL ), 'customers' => (int)eZOrder::customerCount() ) );
    }
}
