<?php

//
// Definition of eZPaypalChecker class
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

/* ! \file ezpaypalchecker.php
 */

/* !
  \class eZPaypalChecker ezpaypalchecker.php
  \brief The class eZPaypalChecker implements
  functions to perform verification of the
  paypal's callback.
 */

// include_once( 'kernel/shop/classes/ezpaymentcallbackchecker.php' );

class eZPaypalChecker extends eZPaymentCallbackChecker
{
    /* !
      Constructor.
     */

    function __construct( $iniFile )
    {
        parent::__construct( $iniFile );
        $this->logger = eZPaymentLogger::CreateForAdd( 'var/log/eZPaypalChecker.log' );
    }

    /* !
      Asks paypal's server to validate callback.
     */

    function requestValidation()
    {
        $server = $this->ini->variable( 'ServerSettings', 'ServerName' );
        $serverPort = $this->ini->variable( 'ServerSettings', 'ServerPort' );
        $requestURI = $this->ini->variable( 'ServerSettings', 'RequestURI' );
        $request = $this->buildRequestString();
        $response = $this->sendPOSTRequest( $server, $serverPort, $requestURI, $request );

        $this->logger->writeTimedString( $response, 'requestValidation. response from server is' );

        if( $response && strcasecmp( $response, 'VERIFIED' ) == 0 )
        {
            return true;
        }

        $this->logger->writeTimedString( 'invalid response ' . var_export( $response, true ) );
        $this->logger->writeTimedString( 'request ' . var_export( $request, true ) );
        return false;
    }

    /* !
      Convinces of completion of the payment.
     */

    function checkPaymentStatus()
    {
        if( $this->checkDataField( 'payment_status', 'Completed' ) )
        {
            return true;
        }

        $this->logger->writeTimedString( 'checkPaymentStatus faild' );
        return false;
    }

    // overrides
    /* !
      Creates resquest string which is used to
      confirm paypal's callback.
     */
    function buildRequestString()
    {
        $request = "cmd=_notify-validate";
        foreach( $this->callbackData as $key => $value )
        {
            $request .= "&$key=" . urlencode( $value );
        }
        return $request;
    }

    /* !
      Sends POST request.
     */

    function sendPOSTRequest( $server, $port, $serverMethod, $request, $timeout = 30 )
    {
        $pos = strpos( $server, '://' );
        if( $pos !== false )
            $server = substr( $server, $pos + 3 );

        if( $port == 443 )
	{
            $openServer = 'ssl://' . $server;
	}
        else
	{
            $openServer = $server;
	}

        $fp = fsockopen( $openServer, $port, $errno, $errstr, $timeout );

        if( $fp )
        {
            $theCall = "POST $serverMethod HTTP/1.0\r\n" .
                    "Host: $server\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen( $request ) . "\r\n\r\n" .
                    $request . "\r\n\r\n";

            if( !fputs( $fp, "$theCall", strlen( $theCall ) ) )
            {
                $this->logger->writeTimedString( "Could not write to server socket: $server:$port", 'sendPOSTRequest failed' );
                return null;
            }

            return $this->handleResponse( $fp );
        }

        $this->logger->writeTimedString( "Unable to open socket on $server:$port. errno = $errno, errstr = $errstr", 'sendPOSTRequest failed' );
        return null;
    }

    function handleResponse( $socket )
    {
        if( $socket )
        {
            while( !feof( $socket ) )
            {
                $response = fgets( $socket, 1024 );
            }

            fclose( $socket );
            return $response;
        }

        $this->logger->writeTimedString( "socket = $socket is invalid.", 'handlePOSTResponse faild' );
        return null;
    }

    function updateStatus()
    {
        //refetch the $this->order - it's an old object, just the order ID is good
        //Changing the status with an old version for $this->order will write old
        //values to the order
        $this->logger->writeTimedString( 'ReFetch Order ID: ' . $this->order->ID );

        $order = eZOrder::fetch( $this->order->ID );
        $this->order = $order;

        $this->order->modifyStatus( $this->ini->variable( 'OrderSettings', 'PaymentMadeOrderStatusID' ) );
    }

}

?>
