<?php

//
// Definition of eZPaypalGateway class
//
// Created on: <18-Jul-2004 14:18:58 dl>
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

/* ! \file ezpaypalgateway.php
 */

/* !
  \class eZPaypalGateway ezpaypalgateway.php
  \brief The class eZPaypalGateway implements
  functions to perform redirection to the PayPal
  payment server.
 */

define( "EZ_PAYMENT_GATEWAY_TYPE_PAYPAL", "ezpaypal" );

class eZPaypalGateway extends eZRedirectGateway
{
    /* !
      Constructor.
     */

    function __construct()
    {
        //__DEBUG__
        $this->logger = eZPaymentLogger::CreateForAdd( "var/log/eZPaypalType.log" );
        $this->logger->writeTimedString( 'eZPaypalGateway::eZPaypalGateway()' );
        //___end____
    }

    /* !
      Creates new eZPaypalGateway object.
     */

    function createPaymentObject( $processID, $orderID )
    {
        //__DEBUG__
        $this->logger->writeTimedString( "createPaymentObject" );
        //___end____

        return eZPaymentObject::createNew( $processID, $orderID, 'Paypal' );
    }

    /* !
      Creates redirectional url to paypal server.
     */

    function createRedirectionUrl( $process )
    {
        //__DEBUG__
        $this->logger->writeTimedString( "createRedirectionUrl" );
        //___end____

        $paypalINI = eZINI::instance( 'paypal.ini' );

        $paypalServer = $paypalINI->variable( 'ServerSettings', 'ServerName' );
        $requestURI = $paypalINI->variable( 'ServerSettings', 'RequestURI' );
        $business = urlencode( $paypalINI->variable( 'PaypalSettings', 'Business' ) );

        $processParams = $process->attribute( 'parameter_list' );
        $orderID = $processParams['order_id'];

        $indexDir = eZSys::indexDir();
        $localHost = eZSys::serverURL();
        $localURI = eZSys::serverVariable( 'REQUEST_URI' );

        $order = eZOrder::fetch( $orderID );
        $amount = urlencode( $order->attribute( 'total_inc_vat' ) );
        $currency = urlencode( $order->currencyCode() );

//        include_once( 'lib/ezlocale/classes/ezlocale.php' );
        $locale = eZLocale::instance();

        $countryCode = urlencode( $locale->countryCode() );

        $maxDescLen = $paypalINI->variable( 'PaypalSettings', 'MaxDescriptionLength' );
        $itemName = urlencode( $this->createShortDescription( $order, $maxDescLen ) );

        $accountInfo = $order->attribute( 'account_information' );
        $first_name = urlencode( $accountInfo['first_name'] );
        $last_name = urlencode( $accountInfo['last_name'] );
        $street = urlencode( $accountInfo['street2'] );
        $zip = urlencode( $accountInfo['zip'] );
        $state = urlencode( $accountInfo['state'] );
        $place = urlencode( $accountInfo['place'] );
        $image_url = "$localHost" . urlencode( $paypalINI->variable( 'PaypalSettings', 'LogoURI' ) );
        $background = urlencode( $paypalINI->variable( 'PaypalSettings', 'BackgroundColor' ) );
        $pageStyle = urlencode( $paypalINI->variable( 'PaypalSettings', 'PageStyle' ) );
        $noNote = urlencode( $paypalINI->variable( 'PaypalSettings', 'NoNote' ) );
        $noteLabel = ($noNote == 1) ? '' : urlencode( $paypalINI->variable( 'PaypalSettings', 'NoteLabel' ) );
        $noShipping = 1;

        $url = $paypalServer . $requestURI .
                "?cmd=_ext-enter" .
                "&redirect_cmd=_xclick" .
                "&business=$business" .
                "&item_name=$itemName" .
                "&custom=$orderID" .
                "&amount=$amount" .
                "&currency_code=$currency" .
                "&first_name=$first_name" .
                "&last_name=$last_name" .
                "&address1=$street" .
                "&zip=$zip" .
                "&state=$state" .
                "&city=$place" .
                "&image_url=$image_url" .
                "&cs=$background" .
                "&page_style=$pageStyle" .
                "&no_shipping=$noShipping" .
                "&cn=$noteLabel" .
                "&no_note=$noNote" .
                "&lc=$countryCode" .
                "&notify_url=$localHost" . $indexDir . "/paypal/notify_url/" .
                "&return=$localHost" . $indexDir . "/shop/checkout/" .
                "&cancel_return=$localHost" . $indexDir . "/shop/basket/";

        //__DEBUG__
        $this->logger->writeTimedString( "business       = $business" );
        $this->logger->writeTimedString( "item_name      = $itemName" );
        $this->logger->writeTimedString( "custom         = $orderID" );
        $this->logger->writeTimedString( "no_shipping    = $noShipping" );
        $this->logger->writeTimedString( "localHost      = $localHost" );
        $this->logger->writeTimedString( "amount         = $amount" );
        $this->logger->writeTimedString( "currency_code  = $currency" );
        $this->logger->writeTimedString( "notify_url     = $localHost" . $indexDir . "/paypal/notify_url/" );
        $this->logger->writeTimedString( "return         = $localHost" . $indexDir . "/shop/checkout/" );
        $this->logger->writeTimedString( "cancel_return  = $localHost" . $indexDir . "/shop/basket/" );
        //___end____

        return $url;
    }

}

eZPaymentGatewayType::registerGateway( EZ_PAYMENT_GATEWAY_TYPE_PAYPAL, "ezpaypalgateway", "Paypal" );
?>