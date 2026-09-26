<?php
/**
 * An order's permanent receipt: shop/orderreceipt/<token>.
 *
 * The address never expires and can be bookmarked. Its signed token is the
 * key: whoever holds a genuine one sees the receipt, from any browser or
 * device, no sign-in needed (see eZShopReceipt::canView()).
 * ?download=1 answers the same receipt as a self-contained HTML file.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$module = $Params['Module'];
$token = isset( $Params['Token'] ) ? (string)$Params['Token'] : '';

// One answer for every token that does not open a receipt: whether an order
// exists is nobody's business but its customer's. An order resolved here came
// from a verified token, so the caller holds it -- which is what authorises it.
$order = $token !== '' ? eZShopReceipt::orderFromToken( $token ) : null;
if ( !$order || !eZShopReceipt::canView( $order, null, true ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$tpl->setVariable( 'order', $order );
$tpl->setVariable( 'receipt_url', eZShopReceipt::receiptURL( $order ) );

if ( $http->hasGetVariable( 'download' ) )
{
    // A file the customer can keep: the whole receipt with its styles inline,
    // no site navigation, readable offline.
    $tpl->setVariable( 'download', true );
    $html = ltrim( $tpl->fetch( 'design:shop/orderreceipt_document.tpl' ) );
    $name = 'receipt-' . preg_replace( '/[^A-Za-z0-9_-]/', '', (string)$order->attribute( 'order_nr' ) ) . '.html';
    header( 'Content-Type: text/html; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $name . '"' );
    header( 'Content-Length: ' . strlen( $html ) );
    header( 'Cache-Control: private, no-store' );
    header( 'X-Robots-Tag: noindex' );
    echo $html;
    eZExecution::cleanExit();
}

// A receipt is personal: never kept by a shared cache, never indexed.
header( 'Cache-Control: private, no-store' );
header( 'X-Robots-Tag: noindex' );

$tpl->setVariable( 'download', false );
$Result = array();
$Result['content'] = $tpl->fetch( 'design:shop/orderreceipt.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/shop', 'Receipt for order #%order_id', null,
                                                       array( '%order_id' => $order->attribute( 'order_nr' ) ) ) ) );
?>
