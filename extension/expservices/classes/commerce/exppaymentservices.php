<?php
/**
 * ezjscore/call/exppayment::<method>: the payments of the shop: the payment objects the gateways create, their
 * status by order, the gateways and payment workflow events that are configured. Never returns card or account
 * data (the kernel does not store any). The approve write is for shop administrators and is audited by the kernel
 * as commerce.payment.approve.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPaymentServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The payment objects, newest first, optionally only pending or approved', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'status' => 'string all|pending|approved' ), 'returns' => 'paged list of payments' ),
        'view' => array( 'summary' => 'One payment object by id', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'payment' ),
        'byOrder' => array( 'summary' => 'The payment of an order', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'order_id' => 'int' ), 'returns' => 'payment or null' ),
        'myStatus' => array( 'summary' => 'The payment status of one of the logged-in customer\'s orders', 'access' => 'user', 'write' => false, 'args' => array( 'order_id' => 'int' ), 'returns' => 'status, approved' ),
        'counts' => array( 'summary' => 'How many payments are pending and approved', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'pending, approved, total' ),
        'gateways' => array( 'summary' => 'The payment gateways available and the directories searched', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'list of gateways' ),
        'workflowEvents' => array( 'summary' => 'The workflow events of the payment gateway type, with their gateway', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array(), 'returns' => 'list of events' ),
        'handlers' => array( 'summary' => 'The account handler and confirm order handler of the checkout', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'account, confirm' ),
        'methods' => array( 'summary' => 'What a client can offer as payment: enabled gateways, or the manual method', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'list of methods' ),
        'approve' => array( 'summary' => 'Approves a payment and continues its workflow (audited as commerce.payment.approve)', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST' ), 'returns' => 'the payment' ),
    );

    protected static function encode( eZPaymentObject $p )
    {
        return array( 'id' => (int)$p->attribute( 'id' ), 'order_id' => (int)$p->attribute( 'order_id' ), 'type' => $p->attribute( 'payment_string' ),
            'workflowprocess_id' => (int)$p->attribute( 'workflowprocess_id' ), 'approved' => (bool)$p->approved(), 'status' => $p->approved() ? 'approved' : 'pending' );
    }

    protected static function gatewayList()
    {
        $ini = eZINI::instance( 'paymentgateways.ini' );
        $names = $ini->hasVariable( 'GatewaysSettings', 'AvailableGateways' ) ? array_filter( $ini->variable( 'GatewaysSettings', 'AvailableGateways' ) ) : array();
        $dirs = $ini->hasVariable( 'GatewaysSettings', 'GatewaysDirectories' ) ? array_filter( $ini->variable( 'GatewaysSettings', 'GatewaysDirectories' ) ) : array();
        $files = array();
        foreach ( $dirs as $d )
            foreach ( (array)glob( $d . '/*gateway.php' ) as $f )
                $files[] = basename( $f, 'gateway.php' );
        return array( 'available' => array_values( $names ), 'directories' => array_values( $dirs ), 'files' => $files );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $status = self::arg( $args, 2, 'string', 'all' );
        $cond = null;
        if ( $status === 'pending' )
            $cond = array( 'status' => 0 );
        else if ( $status === 'approved' )
            $cond = array( 'status' => eZPaymentObject::STATUS_APPROVED );
        else if ( $status !== 'all' )
            throw new expServiceException( 'status is all, pending or approved', 400 );
        $total = count( (array)eZPersistentObject::fetchObjectList( eZPaymentObject::definition(), array( 'id' ), $cond, null, null, false ) );
        $rows = eZPersistentObject::fetchObjectList( eZPaymentObject::definition(), null, $cond, array( 'id' => 'desc' ), array( 'offset' => $offset, 'length' => $limit ) );
        $items = array();
        foreach ( (array)$rows as $p )
            $items[] = self::encode( $p );
        return self::page( $items, $total, $offset, $limit );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        $p = eZPaymentObject::fetchByID( self::arg( $args, 0, 'int' ) );
        if ( !$p instanceof eZPaymentObject )
            throw new expServiceException( 'No such payment', 404 );
        return self::ok( self::encode( $p ) );
    }

    public static function byOrder( array $args )
    {
        self::guard( 'byOrder' );
        $p = eZPaymentObject::fetchByOrderID( self::arg( $args, 0, 'int' ) );
        return self::ok( $p instanceof eZPaymentObject ? self::encode( $p ) : null );
    }

    public static function myStatus( array $args )
    {
        self::guard( 'myStatus' );
        $id = self::arg( $args, 0, 'int' );
        $o = eZOrder::fetch( $id );
        if ( !$o instanceof eZOrder || (int)$o->attribute( 'user_id' ) !== (int)eZUser::currentUser()->attribute( 'contentobject_id' ) )
            throw new expServiceException( "No order $id of yours", 404 );
        $p = eZPaymentObject::fetchByOrderID( $id );
        return self::ok( array( 'order_id' => $id, 'has_payment' => $p instanceof eZPaymentObject, 'approved' => $p instanceof eZPaymentObject && $p->approved(),
            'order_is_temporary' => (bool)$o->attribute( 'is_temporary' ), 'order_status' => $o->attribute( 'status_name' ) ) );
    }

    public static function counts( array $args )
    {
        self::guard( 'counts' );
        $rows = eZDB::instance()->arrayQuery( 'SELECT status, COUNT(*) AS c FROM ezpaymentobject GROUP BY status' );
        $out = array( 'pending' => 0, 'approved' => 0 );
        foreach ( $rows as $r )
            $out[(int)$r['status'] === (int)eZPaymentObject::STATUS_APPROVED ? 'approved' : 'pending'] += (int)$r['c'];
        $out['total'] = $out['pending'] + $out['approved'];
        return self::ok( $out );
    }

    public static function gateways( array $args )
    {
        self::guard( 'gateways' );
        return self::ok( self::gatewayList() );
    }

    public static function workflowEvents( array $args )
    {
        self::guard( 'workflowEvents' );
        $out = array();
        foreach ( (array)eZWorkflowEvent::fetchFilteredList( array( 'workflow_type_string' => 'event_ezpaymentgateway' ) ) as $e )
            $out[] = array( 'id' => (int)$e->attribute( 'id' ), 'workflow_id' => (int)$e->attribute( 'workflow_id' ), 'description' => $e->attribute( 'description' ), 'version' => (int)$e->attribute( 'version' ) );
        return self::ok( $out );
    }

    public static function handlers( array $args )
    {
        self::guard( 'handlers' );
        $ini = eZINI::instance( 'shopaccount.ini' );
        return self::ok( array( 'account' => $ini->hasVariable( 'AccountSettings', 'Handler' ) ? $ini->variable( 'AccountSettings', 'Handler' ) : null,
            'confirm' => $ini->hasVariable( 'ConfirmOrderSettings', 'Handler' ) ? $ini->variable( 'ConfirmOrderSettings', 'Handler' ) : null ) );
    }

    public static function methods( array $args )
    {
        self::guard( 'methods' );
        $g = self::gatewayList();
        $out = array();
        foreach ( $g['available'] as $name )
            $out[] = array( 'method' => $name, 'kind' => 'gateway' );
        if ( !$out )
            $out[] = array( 'method' => 'manual', 'kind' => 'order is placed, payment is settled outside the shop' );
        return self::ok( $out );
    }

    public static function approve( array $args )
    {
        self::guard( 'approve' );
        $p = eZPaymentObject::fetchByID( self::post( 'id', 'int' ) );
        if ( !$p instanceof eZPaymentObject )
            throw new expServiceException( 'No such payment', 404 );
        if ( $p->approved() )
            throw new expServiceException( 'The payment is already approved', 409 );
        $p->approve();
        eZPaymentObject::continueWorkflow( $p->attribute( 'workflowprocess_id' ) );
        return self::ok( self::encode( eZPaymentObject::fetchByID( $p->attribute( 'id' ) ) ) );
    }
}
