<?php
/**
 * ezjscore/call/expbasket::<method>: the basket of the current session and the checkout steps. The basket belongs
 * to the session (cookie) of the caller, exactly as in the shop; a remote app keeps its session cookie.
 * Orders are never completed here without the explicit placeOrder service.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expBasketServices extends expServiceBase
{
    public static $services = array(
        'view' => array( 'summary' => 'The basket of this session: lines, options, totals per VAT rate, currency', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'basket id, items, totals, vat groups' ),
        'items' => array( 'summary' => 'The lines of the basket', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'list of lines' ),
        'item' => array( 'summary' => 'One line of the basket', 'access' => 'public', 'write' => false, 'args' => array( 'item_id' => 'int' ), 'returns' => 'line' ),
        'count' => array( 'summary' => 'Number of lines and units in the basket', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'lines, quantity' ),
        'totals' => array( 'summary' => 'Totals with and without VAT, VAT per rate, shipping', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'totals' ),
        'isEmpty' => array( 'summary' => 'Whether the basket has no lines', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'empty' ),
        'currency' => array( 'summary' => 'The currency of the basket and the preferred one of the user', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'basket, preferred' ),
        'canAdd' => array( 'summary' => 'Whether a product can be put in this basket (type and currency compatible)', 'access' => 'public', 'write' => false,
            'args' => array( 'object_id' => 'int' ), 'returns' => 'can, reason' ),
        'add' => array( 'summary' => 'Puts a product in the basket (POST fields object_id, quantity, options as JSON attribute id => choice id)', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array( 'object_id' => 'int POST', 'quantity' => 'int POST', 'options' => 'json POST' ), 'returns' => 'the basket' ),
        'addNode' => array( 'summary' => 'Puts a product in the basket by its node id', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array( 'node_id' => 'int POST', 'quantity' => 'int POST', 'options' => 'json POST' ), 'returns' => 'the basket' ),
        'update' => array( 'summary' => 'Sets the quantity of one line (0 removes it)', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array( 'item_id' => 'int POST', 'quantity' => 'int POST' ), 'returns' => 'the basket' ),
        'updateMany' => array( 'summary' => 'Sets the quantities of several lines: JSON object item id => quantity', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array( 'quantities' => 'json POST' ), 'returns' => 'the basket' ),
        'remove' => array( 'summary' => 'Removes one line', 'access' => array( 'shop', 'buy' ), 'write' => true, 'args' => array( 'item_id' => 'int POST' ), 'returns' => 'the basket' ),
        'empty' => array( 'summary' => 'Removes every line', 'access' => array( 'shop', 'buy' ), 'write' => true, 'args' => array(), 'returns' => 'the empty basket' ),
        'refreshPrices' => array( 'summary' => 'Brings the prices of the lines up to date with the products', 'access' => array( 'shop', 'buy' ), 'write' => true, 'args' => array(), 'returns' => 'the basket' ),
        'setCurrency' => array( 'summary' => 'Sets the preferred currency of the user for new lines', 'access' => 'user', 'write' => true,
            'args' => array( 'currency' => 'string POST' ), 'returns' => 'preferred currency' ),
        'checkoutStatus' => array( 'summary' => 'What is needed before the checkout: lines, login, country, account handler, VAT known', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'steps with status and blockers' ),
        'accountInfo' => array( 'summary' => 'The account information of the current checkout (the temporary order)', 'access' => array( 'shop', 'buy' ), 'write' => false,
            'args' => array(), 'returns' => 'account information fields' ),
        'startCheckout' => array( 'summary' => 'Creates the temporary order of the basket (step 1); nothing is charged', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array(), 'returns' => 'temporary order' ),
        'review' => array( 'summary' => 'The temporary order with shipping and VAT as the confirm step shows it', 'access' => array( 'shop', 'buy' ), 'write' => false,
            'args' => array(), 'returns' => 'temporary order with lines and order items' ),
        'cancelCheckout' => array( 'summary' => 'Cancels the temporary order and returns to the basket', 'access' => array( 'shop', 'buy' ), 'write' => true, 'args' => array(), 'returns' => 'the basket' ),
        'placeOrder' => array( 'summary' => 'Runs the checkout operation of the temporary order: activates it or hands over to the payment gateway', 'access' => array( 'shop', 'buy' ), 'write' => true,
            'args' => array(), 'returns' => 'status, redirect URL when a gateway takes over' ),
        'adminList' => array( 'summary' => 'Baskets of all sessions with their size and age', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of baskets' ),
        'adminView' => array( 'summary' => 'One basket of any session', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'basket_id' => 'int' ), 'returns' => 'basket' ),
        'adminCleanup' => array( 'summary' => 'Removes the baskets of sessions that are gone and older than the given days', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'days' => 'int POST' ), 'returns' => 'removed baskets' ),
    );

    /** @var string|null the session key of the basket, overrides the HTTP session (tests) */
    public static $sessionKey = null;

    /** The basket of this session, false when there is none and $create is false. */
    protected static function basket( $create = false )
    {
        $key = self::$sessionKey !== null ? self::$sessionKey : (string)eZHTTPTool::instance()->sessionID();
        if ( $key === '' )
        {
            if ( $create )
                throw new expServiceException( 'This request has no session: send the session cookie', 401 );
            return false;
        }
        if ( $create )
            return eZBasket::currentBasket();
        $list = eZPersistentObject::fetchObjectList( eZBasket::definition(), null, array( 'session_id' => $key ), array( 'id' => 'desc' ), array( 'limit' => 1 ) );
        return $list ? $list[0] : false;
    }

    /** The encoded basket. */
    protected static function encode( $basket )
    {
        if ( !$basket instanceof eZBasket )
            return array( 'basket_id' => null, 'currency' => null, 'items' => array(), 'totals' => expCommerceExport::totals( array() ), 'vat' => array(), 'order_id' => 0 );
        $collection = $basket->productCollection();
        $items = $collection ? expCommerceExport::items( $collection ) : array();
        $currency = $collection ? (string)$collection->attribute( 'currency_code' ) : '';
        $groups = array();
        foreach ( $items as $i )
        {
            $k = (string)$i['vat_percent'];
            if ( !isset( $groups[$k] ) )
                $groups[$k] = array( 'vat_percent' => $i['vat_percent'], 'total_ex_vat' => 0.0, 'total_inc_vat' => 0.0 );
            $groups[$k]['total_ex_vat'] += $i['total_ex_vat'];
            $groups[$k]['total_inc_vat'] += $i['total_inc_vat'];
        }
        $vat = array();
        foreach ( $groups as $g )
            $vat[] = array( 'vat_percent' => $g['vat_percent'], 'total_ex_vat' => round( $g['total_ex_vat'], 4 ), 'total_inc_vat' => round( $g['total_inc_vat'], 4 ),
                'vat' => round( $g['total_inc_vat'] - $g['total_ex_vat'], 4 ) );
        $totals = expCommerceExport::totals( $items, $currency );
        $info = $basket->itemsInfo();
        $ship = isset( $info['additional_info']['shipping_total'] ) ? $info['additional_info']['shipping_total'] : null;
        if ( is_array( $ship ) && (float)$ship['total_price_inc_vat'] > 0 )
        {
            $totals['shipping_inc_vat'] = expCommerceExport::num( $ship['total_price_inc_vat'] );
            $totals['shipping_ex_vat'] = expCommerceExport::num( $ship['total_price_ex_vat'] );
            $totals['total_inc_vat'] = round( $totals['total_inc_vat'] + (float)$ship['total_price_inc_vat'], 4 );
            $totals['total_ex_vat'] = round( $totals['total_ex_vat'] + (float)$ship['total_price_ex_vat'], 4 );
            $totals['vat'] = round( $totals['total_inc_vat'] - $totals['total_ex_vat'], 4 );
        }
        return array( 'basket_id' => (int)$basket->attribute( 'id' ), 'currency' => $currency, 'items' => $items, 'totals' => $totals, 'vat' => $vat,
            'order_id' => (int)$basket->attribute( 'order_id' ), 'vat_known' => (bool)$basket->isVATKnown() );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( self::encode( self::basket() ) );
    }

    public static function items( array $args )
    {
        self::guard( 'items' );
        $e = self::encode( self::basket() );
        return self::ok( $e['items'], array( 'count' => count( $e['items'] ) ) );
    }

    public static function item( array $args )
    {
        self::guard( 'item' );
        $id = self::arg( $args, 0, 'int' );
        $e = self::encode( self::basket() );
        foreach ( $e['items'] as $i )
            if ( $i['id'] === $id )
                return self::ok( $i );
        throw new expServiceException( "No line $id in the basket", 404 );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        $t = self::encode( self::basket() )['totals'];
        return self::ok( array( 'lines' => $t['lines'], 'quantity' => $t['quantity'] ) );
    }

    public static function totals( array $args )
    {
        self::guard( 'totals' );
        $e = self::encode( self::basket() );
        return self::ok( array( 'totals' => $e['totals'], 'vat' => $e['vat'] ) );
    }

    public static function isEmpty( array $args )
    {
        self::guard( 'isEmpty' );
        $b = self::basket();
        return self::ok( array( 'empty' => !$b || $b->isEmpty() ) );
    }

    public static function currency( array $args )
    {
        self::guard( 'currency' );
        $b = self::basket();
        $c = $b && $b->productCollection() ? (string)$b->productCollection()->attribute( 'currency_code' ) : null;
        return self::ok( array( 'basket' => $c ?: null, 'preferred' => eZShopFunctions::preferredCurrencyCode(), 'preferred_valid' => eZShopFunctions::isPreferredCurrencyValid() === eZError::SHOP_OK ) );
    }

    public static function canAdd( array $args )
    {
        self::guard( 'canAdd' );
        $object = eZContentObject::fetch( self::arg( $args, 0, 'int' ) );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'No such object', 404 );
        if ( !$object->canRead() )
            throw new expServiceException( 'No read access to the product', 403 );
        $b = self::basket( false );
        $error = $b ? $b->canAddProduct( $object ) : ( eZShopFunctions::productTypeByObject( $object ) === false ? eZError::SHOP_NOT_A_PRODUCT : eZError::SHOP_OK );
        $reasons = array( eZError::SHOP_OK => 'ok', eZError::SHOP_NOT_A_PRODUCT => 'not a product', eZError::SHOP_BASKET_INCOMPATIBLE_PRODUCT_TYPE => 'incompatible with the products already in the basket' );
        return self::ok( array( 'can' => $error === eZError::SHOP_OK, 'reason' => isset( $reasons[$error] ) ? $reasons[$error] : 'preferred currency invalid' ) );
    }

    public static function add( array $args )
    {
        self::guard( 'add' );
        return self::doAdd( self::post( 'object_id', 'int' ) );
    }

    public static function addNode( array $args )
    {
        self::guard( 'addNode' );
        $node = self::node( self::post( 'node_id', 'int' ), 'read' );
        return self::doAdd( (int)$node->attribute( 'contentobject_id' ) );
    }

    protected static function doAdd( $objectId )
    {
        $quantity = self::post( 'quantity', 'int', 1 );
        if ( $quantity < 1 || $quantity > 10000 )
            throw new expServiceException( 'The quantity must be between 1 and 10000', 422 );
        $options = self::post( 'options', 'json', array() );
        $object = eZContentObject::fetch( $objectId );
        if ( !$object instanceof eZContentObject )
            throw new expServiceException( 'No such object', 404 );
        if ( !$object->canRead() )
            throw new expServiceException( 'No read access to the product', 403 );
        $basket = self::basket( true );
        $error = $basket->canAddProduct( $object );
        if ( $error !== eZError::SHOP_OK )
            throw new expServiceException( $error === eZError::SHOP_NOT_A_PRODUCT ? 'The object is not a product' : 'The product does not fit this basket', 422 );
        $result = eZOperationHandler::execute( 'shop', 'addtobasket', array( 'basket_id' => $basket->attribute( 'id' ), 'object_id' => $objectId,
            'quantity' => $quantity, 'option_list' => is_array( $options ) ? $options : array() ) );
        if ( $result['status'] === eZModuleOperationInfo::STATUS_CANCELLED )
            throw new expServiceException( isset( $result['reason'] ) && $result['reason'] === 'validation'
                ? 'The options are not valid: ' . json_encode( isset( $result['error_data'] ) ? $result['error_data'] : array() ) : 'The product could not be added', 422 );
        return self::ok( self::encode( self::basket() ) );
    }

    /** The line of this basket, 404 when it is not in it. */
    protected static function line( $basket, $itemId )
    {
        $item = eZProductCollectionItem::fetch( $itemId );
        if ( !$basket instanceof eZBasket || !$item instanceof eZProductCollectionItem || (int)$item->attribute( 'productcollection_id' ) !== (int)$basket->attribute( 'productcollection_id' ) )
            throw new expServiceException( "No line $itemId in the basket", 404 );
        return $item;
    }

    public static function update( array $args )
    {
        self::guard( 'update' );
        $basket = self::basket();
        $item = self::line( $basket, self::post( 'item_id', 'int' ) );
        $q = self::post( 'quantity', 'int' );
        if ( $q < 0 || $q > 10000 )
            throw new expServiceException( 'The quantity must be between 0 and 10000', 422 );
        if ( $q === 0 )
            $item->remove();
        else
        {
            $item->setAttribute( 'item_count', $q );
            $item->store();
        }
        return self::ok( self::encode( $basket ) );
    }

    public static function updateMany( array $args )
    {
        self::guard( 'updateMany' );
        $basket = self::basket();
        $map = self::post( 'quantities', 'json' );
        if ( !is_array( $map ) )
            throw new expServiceException( 'quantities is a JSON object: line id => quantity', 400 );
        foreach ( $map as $id => $q )
        {
            if ( !is_numeric( $id ) || !is_numeric( $q ) || $q < 0 || $q > 10000 )
                throw new expServiceException( 'Every quantity is a number between 0 and 10000', 422 );
            self::line( $basket, (int)$id );
        }
        foreach ( $map as $id => $q )
        {
            $item = eZProductCollectionItem::fetch( (int)$id );
            if ( (int)$q === 0 )
                $item->remove();
            else
            {
                $item->setAttribute( 'item_count', (int)$q );
                $item->store();
            }
        }
        return self::ok( self::encode( $basket ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        $basket = self::basket();
        $item = self::line( $basket, self::post( 'item_id', 'int' ) );
        $item->remove();
        return self::ok( self::encode( $basket ) );
    }

    public static function empty( array $args )
    {
        self::guard( 'empty' );
        $basket = self::basket();
        if ( $basket instanceof eZBasket )
            foreach ( $basket->productCollection()->itemList() as $i )
                $i->remove();
        return self::ok( self::encode( $basket ) );
    }

    public static function refreshPrices( array $args )
    {
        self::guard( 'refreshPrices' );
        $basket = self::basket();
        if ( $basket instanceof eZBasket )
            $basket->updatePrices();
        return self::ok( self::encode( $basket ) );
    }

    public static function setCurrency( array $args )
    {
        self::guard( 'setCurrency' );
        $code = strtoupper( self::post( 'currency', 'string' ) );
        $error = eZShopFunctions::setPreferredCurrencyCode( $code );
        if ( $error !== eZError::SHOP_OK )
            throw new expServiceException( "The currency $code is not valid or not active", 422 );
        return self::ok( array( 'preferred' => $code ) );
    }

    public static function checkoutStatus( array $args )
    {
        self::guard( 'checkoutStatus' );
        $basket = self::basket();
        $e = self::encode( $basket );
        $user = eZUser::currentUser();
        $logged = $user->isRegistered();
        $blockers = array();
        if ( !$e['items'] )
            $blockers[] = 'the basket is empty';
        if ( !$logged && !self::guestCheckout() )
            $blockers[] = 'login is required';
        if ( $basket && !$basket->isVATKnown() )
            $blockers[] = 'the VAT is not known: a country is needed';
        $handler = eZShopAccountHandler::instance();
        $order = $basket && $basket->attribute( 'order_id' ) ? eZOrder::fetch( (int)$basket->attribute( 'order_id' ) ) : null;
        $steps = array(
            array( 'step' => 'basket', 'done' => (bool)$e['items'] ),
            array( 'step' => 'account', 'done' => $logged || self::guestCheckout() ),
            array( 'step' => 'order', 'done' => $order instanceof eZOrder ),
            array( 'step' => 'payment', 'done' => false ) );
        return self::ok( array( 'can_checkout' => !$blockers, 'blockers' => $blockers, 'steps' => $steps, 'logged_in' => $logged,
            'country_required' => (bool)eZVATManager::isUserCountryRequired(), 'account_handler' => get_class( $handler ),
            'temporary_order_id' => $order instanceof eZOrder ? (int)$order->attribute( 'id' ) : null ) );
    }

    protected static function guestCheckout()
    {
        $ini = eZINI::instance( 'shop.ini' );
        return $ini->hasVariable( 'ShopSettings', 'AllowGuestCheckout' ) && $ini->variable( 'ShopSettings', 'AllowGuestCheckout' ) === 'true';
    }

    /** The temporary order of the basket, 404 when the checkout has not been started. */
    protected static function tempOrder()
    {
        $basket = self::basket();
        $id = $basket ? (int)$basket->attribute( 'order_id' ) : 0;
        $order = $id ? eZOrder::fetch( $id ) : null;
        if ( !$order instanceof eZOrder || !$order->attribute( 'is_temporary' ) )
            throw new expServiceException( 'The checkout has not been started', 404 );
        return $order;
    }

    public static function accountInfo( array $args )
    {
        self::guard( 'accountInfo' );
        $order = self::tempOrder();
        $info = $order->accountInformation();
        $safe = array();
        foreach ( (array)$info as $k => $v )
            $safe[$k] = is_scalar( $v ) || $v === null ? $v : null;
        return self::ok( array( 'name' => $order->accountName(), 'email' => $order->accountEmail(), 'information' => $safe ) );
    }

    public static function startCheckout( array $args )
    {
        self::guard( 'startCheckout' );
        $basket = self::basket( true );
        if ( $basket->isEmpty() )
            throw new expServiceException( 'The basket is empty', 409 );
        if ( !eZUser::currentUser()->isRegistered() && !self::guestCheckout() )
            throw new expServiceException( 'You need to log in to check out', 401 );
        $id = (int)$basket->attribute( 'order_id' );
        $order = $id ? eZOrder::fetch( $id ) : null;
        if ( !$order instanceof eZOrder || !$order->attribute( 'is_temporary' ) )
            $order = $basket->createOrder();
        eZHTTPTool::instance()->setSessionVariable( 'MyTemporaryOrderID', $order->attribute( 'id' ) );
        return self::ok( expCommerceExport::order( $order, true ) );
    }

    public static function review( array $args )
    {
        self::guard( 'review' );
        $order = self::tempOrder();
        $result = eZOperationHandler::execute( 'shop', 'confirmorder', array( 'order_id' => $order->attribute( 'id' ) ) );
        return self::ok( expCommerceExport::order( eZOrder::fetch( $order->attribute( 'id' ) ), true ), array( 'operation_status' => (int)$result['status'] ) );
    }

    public static function cancelCheckout( array $args )
    {
        self::guard( 'cancelCheckout' );
        $basket = self::basket();
        $id = $basket ? (int)$basket->attribute( 'order_id' ) : 0;
        $order = $id ? eZOrder::fetch( $id ) : null;
        if ( $order instanceof eZOrder && $order->attribute( 'is_temporary' ) )
            $order->purge( false );
        if ( $basket )
        {
            $basket->setAttribute( 'order_id', 0 );
            $basket->store();
        }
        return self::ok( self::encode( $basket ) );
    }

    public static function placeOrder( array $args )
    {
        self::guard( 'placeOrder' );
        $order = self::tempOrder();
        $order->setAttribute( 'email', $order->accountEmail() );
        $order->store();
        $r = eZOperationHandler::execute( 'shop', 'checkout', array( 'order_id' => $order->attribute( 'id' ) ) );
        switch ( $r['status'] )
        {
            case eZModuleOperationInfo::STATUS_CANCELLED:
                throw new expServiceException( 'The checkout was cancelled: ' . ( isset( $r['reason'] ) ? $r['reason'] : 'see the shop log' ), 409 );
            case eZModuleOperationInfo::STATUS_HALTED:
            case eZModuleOperationInfo::STATUS_REPEAT:
                return self::ok( array( 'order_id' => (int)$order->attribute( 'id' ), 'status' => 'pending', 'redirect_url' => isset( $r['redirect_url'] ) ? $r['redirect_url'] : null ) );
        }
        return self::ok( array( 'order_id' => (int)$order->attribute( 'id' ), 'status' => 'placed', 'receipt_url' => eZShopReceipt::linkURLForID( $order->attribute( 'id' ) ) ) );
    }

    public static function adminList( array $args )
    {
        self::guard( 'adminList' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $db = eZDB::instance();
        $total = (int)$db->arrayQuery( 'SELECT COUNT(*) AS c FROM ezbasket' )[0]['c'];
        $rows = $db->arrayQuery( 'SELECT b.id, b.productcollection_id, b.order_id, c.created, c.currency_code, (SELECT COUNT(*) FROM ezproductcollection_item i WHERE i.productcollection_id=b.productcollection_id) AS lines
                                  FROM ezbasket b LEFT JOIN ezproductcollection c ON c.id=b.productcollection_id ORDER BY b.id DESC', array( 'limit' => $limit, 'offset' => $offset ) );
        $items = array();
        foreach ( $rows as $r )
            $items[] = array( 'basket_id' => (int)$r['id'], 'order_id' => (int)$r['order_id'], 'lines' => (int)$r['lines'], 'currency' => $r['currency_code'],
                'created' => $r['created'] ? gmdate( 'c', (int)$r['created'] ) : null );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function adminView( array $args )
    {
        self::guard( 'adminView' );
        $basket = eZPersistentObject::fetchObject( eZBasket::definition(), null, array( 'id' => self::arg( $args, 0, 'int' ) ) );
        if ( !$basket instanceof eZBasket )
            throw new expServiceException( 'No such basket', 404 );
        return self::ok( self::encode( $basket ) );
    }

    public static function adminCleanup( array $args )
    {
        self::guard( 'adminCleanup' );
        $days = self::post( 'days', 'int' );
        if ( $days < 1 )
            throw new expServiceException( 'days must be 1 or more', 422 );
        $before = (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezbasket' )[0]['c'];
        eZBasket::cleanupExpired( time() - $days * 86400 );
        $after = (int)eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS c FROM ezbasket' )[0]['c'];
        return self::ok( array( 'removed' => $before - $after ) );
    }
}
