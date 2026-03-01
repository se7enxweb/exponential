<?php
//
// Processing of paypal's callback.
//
// Created on: <26-Apr-2004 14:18:58 dl>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Paypal Payment Gateway
// SOFTWARE RELEASE: 1.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file notify_url.php
*/

$logger  = eZPaymentLogger::CreateForAdd('var/log/eZPaypal_notify_url.log');
$checker = new eZPaypalChecker( 'paypal.ini' );
if( $checker->createDataFromPOST() )
{
    unset ($_POST);

    if( $checker->requestValidation() && $checker->checkPaymentStatus() )
    {
	$orderID = $checker->getFieldValue( 'custom' );

	if( $checker->setupOrderAndPaymentObject( $orderID ) )
	{
	    $amount   = $checker->getFieldValue( 'mc_gross' );
	    $currency = $checker->getFieldValue( 'mc_currency' );

	    if( $checker->checkAmount( $amount ) && $checker->checkCurrency( $currency ) )
	    {
		$checker->approvePayment();
		$checker->updateStatus();
		
		$order = eZOrder::fetch( $orderID );

		if ( isset( $order ) )
		{
		    // Any special processing for the order goes here
	        }
	    }
	}
    }
}

$logger->writeTimedString( 'notify_url.php was propertly ended' );
?>