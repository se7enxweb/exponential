<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];

$tpl = eZTemplate::factory();

$offset = $Params['Offset'];
$limit = expAdminPagination::limit( 'shop/orderlist' );

// The list is sorted by the column whose heading was clicked, carried in the
// address as /(sort)/<column>/(dir)/<asc|desc> like the other admin lists, so
// paging, reloading and a link all show the same order. Without them the
// stored preferences (Time or Customer, ascending or descending) decide, as
// they always have. The column is checked against eZOrder::sortColumnsForList().
$userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
$columns = eZOrder::sortColumnsForList();
if ( isset( $userParameters['sort'] ) && isset( $columns[(string)$userParameters['sort']] ) )
{
    $sortField = (string)$userParameters['sort'];
}
else
{
    $sortField = eZPreferences::value( 'admin_orderlist_sortfield' ) === 'user_name' ? 'customer' : 'created';
}
if ( isset( $userParameters['dir'] ) )
{
    $sortOrder = strtolower( (string)$userParameters['dir'] ) === 'desc' ? 'desc' : 'asc';
}
else
{
    $sortOrder = eZPreferences::value( 'admin_orderlist_sortorder' ) === 'desc' ? 'desc' : 'asc';
}

$http = eZHTTPTool::instance();

// The RemoveButton is not present in the orderlist, but is here for backwards
// compatibility. Simply replace the ArchiveButton for the RemoveButton will
// do the trick.
//
// Note that removing order can cause wrong order numbers (order_nr are
// reused).  See eZOrder::activate.
if ( $http->hasPostVariable( 'RemoveButton' ) )
{
    if ( $http->hasPostVariable( 'OrderIDArray' ) )
    {
        $orderIDArray = $http->postVariable( 'OrderIDArray' );
        if ( $orderIDArray !== null )
        {
            $http->setSessionVariable( 'DeleteOrderIDArray', $orderIDArray );
            $Module->redirectTo( $Module->functionURI( 'removeorder' ) . '/' );
        }
    }
}

// Archive options.
if ( $http->hasPostVariable( 'ArchiveButton' ) )
{
    if ( $http->hasPostVariable( 'OrderIDArray' ) )
    {
        $orderIDArray = $http->postVariable( 'OrderIDArray' );
        if ( $orderIDArray !== null )
        {
            $http->setSessionVariable( 'OrderIDArray', $orderIDArray );
            $Module->redirectTo( $Module->functionURI( 'archiveorder' ) . '/' );
        }
    }
}

if ( $http->hasPostVariable( 'SaveOrderStatusButton' ) )
{
    if ( $http->hasPostVariable( 'StatusList' ) )
    {
        foreach ( $http->postVariable( 'StatusList' ) as $orderID => $statusID )
        {
            $order = eZOrder::fetch( $orderID );
            $access = $order->canModifyStatus( $statusID );
            if ( $access and $order->attribute( 'status_id' ) != $statusID )
            {
                $order->modifyStatus( $statusID );
            }
        }
    }
}

$orderArray = eZOrder::activeSorted( $offset, $limit, $sortField, $sortOrder );
$orderCount = eZOrder::activeCount();

$tpl->setVariable( 'order_list', $orderArray );
$tpl->setVariable( 'order_list_count', $orderCount );
$tpl->setVariable( 'limit', $limit );

$viewParameters = array( 'offset' => $offset,
                         'sort'   => $sortField,
                         'dir'    => $sortOrder );
$tpl->setVariable( 'view_parameters', $viewParameters );
// sort_field keeps the names it always had (created, user_name, order_nr) for
// site designs that show their own sort selector.
$legacyFields = array( 'customer' => 'user_name', 'id' => 'order_nr' );
$tpl->setVariable( 'sort_field', isset( $legacyFields[$sortField] ) ? $legacyFields[$sortField] : $sortField );
$tpl->setVariable( 'sort_order', $sortOrder );
$tpl->setVariable( 'order_sort', array( 'field'     => $sortField,
                                        'direction' => $sortOrder,
                                        'opposite'  => $sortOrder === 'asc' ? 'desc' : 'asc' ) );

$Result = array();
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/shop', 'Order list' ),
                                'url' => false ) );

$Result['content'] = $tpl->fetch( 'design:shop/orderlist.tpl' );
?>
