<?php
/**
 * shop/myorders: the signed-in customer's own orders.
 *
 * The list the customer of a shop could not see before: the kernel listed a customer's orders only in
 * shop/customerorderview, which needs shop/administrate. This view lists the completed orders of the current user
 * and nobody else's (the user id comes from the session, never from the address), newest first, a page at a time,
 * each linked where the shop links orders (eZOrder::linkURL(): the order view or the permanent receipt, shop.ini
 * [OrderViewSettings] OrderLinkView). Policy shop/myorders; a visitor who is not signed in gets the login form.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Shop
{

class Myorders extends \Exponential\Runnable\ModuleView
{
    /** Orders per page (shop.ini [MyOrdersSettings] Limit overrides it). */
    const LIMIT = 20;

    public function run( array $scope )
    {
        // the including function's variables: only $Params is needed
        $Params = isset( $scope['Params'] ) && is_array( $scope['Params'] ) ? $scope['Params'] : array();
        $module = $Params['Module'];
        $user = \eZUser::currentUser();
        if ( !$user->isRegistered() )
        {
            return $this->viewResult( null, $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        }

        $limit = self::limit();
        $offset = self::offset( isset( $Params['Offset'] ) ? $Params['Offset'] : 0 );
        $userID = (int)$user->attribute( 'contentobject_id' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'order_list', \eZOrder::fetchListByUserID( $userID, $offset, $limit ) );
        $tpl->setVariable( 'order_count', \eZOrder::countByUserID( $userID ) );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $offset ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:shop/myorders.tpl' );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Order list' ) ) );
        return $this->viewResult( $Result, null );
    }

    /**
     * The page size: shop.ini [MyOrdersSettings] Limit when it is a positive number, else LIMIT; at most 100.
     *
     * @return int
     */
    public static function limit()
    {
        $ini = \eZINI::instance( 'shop.ini' );
        $limit = $ini->hasVariable( 'MyOrdersSettings', 'Limit' ) ? (int)$ini->variable( 'MyOrdersSettings', 'Limit' ) : 0;
        return $limit > 0 ? min( $limit, 100 ) : self::LIMIT;
    }

    /**
     * The offset view parameter as a number: anything that is not a non-negative whole number is 0.
     *
     * @param mixed $offset
     * @return int
     */
    public static function offset( $offset )
    {
        return is_numeric( $offset ) && (int)$offset > 0 ? (int)$offset : 0;
    }
}

}
