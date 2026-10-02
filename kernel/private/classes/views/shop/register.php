<?php
/**
 * The code of kernel/shop/register.php, moved into a class (#207 stage 1). The file kernel/shop/register.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/register.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Shop
{

class Register extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];


        $tpl = \eZTemplate::factory();

        if ( $module->isCurrentAction( 'Cancel' ) )
        {
            $module->redirectTo( '/shop/' . \eZBasket::viewName() . '/' );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $tpl->setVariable( "input_error", false );
        if ( $module->isCurrentAction( 'Store' ) )
        {
            $inputIsValid = true;
            $firstName = $http->postVariable( "FirstName" );
            if ( trim( $firstName ) == "" )
                $inputIsValid = false;
            $lastName = $http->postVariable( "LastName" );
            if ( trim( $lastName ) == "" )
                $inputIsValid = false;
            $email = $http->postVariable( "EMail" );
            if ( ! \eZMail::validate( $email ) )
                $inputIsValid = false;
            $address = $http->postVariable( "Address" );
            if ( trim( $address ) == "" )
                $inputIsValid = false;
            $tpl->setVariable( "first_name", $firstName );
            $tpl->setVariable( "last_name", $lastName );
            $tpl->setVariable( "email", $email );
            $tpl->setVariable( "address", $address );

            if ( $inputIsValid == true )
            {
                // Check for validation
                $basket = \eZBasket::currentBasket();
                $order = $basket->createOrder();

                $doc = new \DOMDocument( '1.0', 'utf-8' );

                $root = $doc->createElement( 'shop_account' );
                $doc->appendChild( $root );

                $firstNameNode = $doc->createElement( "first-name", $firstName );
                $root->appendChild( $firstNameNode );

                $lastNameNode = $doc->createElement( "last-name", $lastName );
                $root->appendChild( $lastNameNode );

                $emailNode = $doc->createElement( "email", $email );
                $root->appendChild( $emailNode );

                $addressNode = $doc->createElement( "address", $address );
                $root->appendChild( $addressNode );

                $xmlString = $doc->saveXML();

                $order->setAttribute( 'data_text_1', $xmlString );
                $order->setAttribute( 'account_identifier', "simple" );
                $order->store();

                $http->setSessionVariable( 'MyTemporaryOrderID', $order->attribute( 'id' ) );

                $module->redirectTo( '/shop/confirmorder/' );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
            else
            {
                $tpl->setVariable( "input_error", true );
            }
        }

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/register.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Enter account information' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
