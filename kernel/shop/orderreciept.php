<?php
/**
 * shop/orderreciept/<token>: the common misspelling of shop/orderreceipt,
 * answered with a permanent redirect so either address keeps working.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$module = $Params['Module'];
$token = isset( $Params['Token'] ) ? (string)$Params['Token'] : '';
// redirectTo() carries the query string (?download=1) across by itself.
$module->redirectTo( '/shop/orderreceipt/' . rawurlencode( $token ) );
$module->setRedirectStatus( '301 Moved Permanently' );
return;
?>
