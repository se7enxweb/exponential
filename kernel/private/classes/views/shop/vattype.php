<?php
/**
 * The code of kernel/shop/vattype.php, moved into a class (#207 stage 1). The file kernel/shop/vattype.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/vattype.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'applyChanges' ) ) {
/*!
  Apply changes made to VAT types' names and/or percentages.

  \return errors array
 */
function applyChanges( $module, $http, $vatTypeArray = false )
{
    $errors = array();
    if ( $vatTypeArray === false )
        $vatTypeArray = eZVatType::fetchList( true, true );

    $db = eZDB::instance();
    $db->begin();
    foreach ( $vatTypeArray as $vatType )
    {
        $id = $vatType->attribute( 'id' );

        if ( $id == -1 ) // avoid storing changes to the "fake" dynamic VAT type
            continue;

        if ( $http->hasPostVariable( "vattype_name_" . $id ) )
        {
            $name = $http->postVariable( "vattype_name_" . $id );
        }
        if ( $http->hasPostVariable( "vattype_percentage_" . $id ) )
        {
            $percentage = $http->postVariable( "vattype_percentage_" . $id );
        }

        if ( !$name || $percentage < 0 || $percentage > 100 )
        {
            if ( !$name )
                $errors[] = ezpI18n::tr( 'kernel/shop/vattype', 'Empty VAT type names are not allowed (corrected).' );
            else
                $errors[] = ezpI18n::tr( 'kernel/shop/vattype', 'Wrong VAT percentage (corrected).' );

            continue;
        }

        $vatType->setAttribute( 'name', $name );
        $vatType->setAttribute( 'percentage', $percentage );
        $vatType->store();
    }
    $db->commit();

    return $errors;
}
}

if ( !function_exists( 'generateUniqueVatTypeName' ) ) {
/**
 * Generate a unique VAT type name.
 *
 * The generated name looks like "VAT type X"
 * where X is a unique number.
 */
function generateUniqueVatTypeName( $vatTypes )
{
    $commonPart = ezpI18n::tr( 'kernel/shop', 'VAT type' );
    $maxNumber = 0;
    foreach ( $vatTypes as $type )
    {
        $typeName = $type->attribute( 'name' );

        if ( !preg_match( "/^$commonPart (\d+)/", $typeName, $matches ) )
            continue;

        $curNumber = $matches[1];
        if ( $curNumber > $maxNumber )
            $maxNumber = $curNumber;
    }

    $maxNumber++;
    return "$commonPart $maxNumber";
}
}

if ( !function_exists( 'findDependencies' ) ) {
/**
 * Determine dependent VAT rules and products for the given VAT types.
 *
 * \private
 */
function findDependencies( $vatTypeIDList, &$deps, &$haveDeps, &$canRemove )
{
    // Find dependencies (products and/or VAT rules).
    $deps = array();
    $haveDeps = false;
    $canRemove = true;
    foreach ( $vatTypeIDList as $vatID )
    {
        $vatType = eZVatType::fetch( $vatID );
        $vatName = $vatType->attribute( 'name' );

        // Find dependent VAT rules.
        $nRules = eZVatRule::fetchCountByVatType( $vatID );

        // Find dependent products.
        $nProducts = eZVatType::fetchDependentProductsCount( $vatID );

        // Find product classes having this VAT type set as default.
        $nClasses = eZVatType::fetchDependentClassesCount( $vatID );

        if ( $nClasses )
            $canRemove = false;

        $deps[$vatID] = array( 'name' => $vatName,
                               'affected_rules_count' => $nRules,
                               'affected_products_count' => $nProducts,
                               'affected_classes_count' => $nClasses );

        if ( !$haveDeps && ( $nRules > 0 || $nProducts > 0 ) )
            $haveDeps = true;
    }
}
}
}

namespace Exponential\View\Kernel\Shop
{

class Vattype extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $tpl = \eZTemplate::factory();
        $errors = false;




        // Add new VAT type.
        if ( $module->isCurrentAction( 'Add' ) )
        {
            $vatTypeArray = \eZVatType::fetchList( true, true );
            $errors = applyChanges( $module, $http, $vatTypeArray );

            $vatType = \eZVatType::create();
            $vatType->setAttribute( 'name', generateUniqueVatTypeName( $vatTypeArray ) );
            $vatType->store();
            $tpl->setVariable( 'last_added_id', $vatType->attribute( 'id' ) );
        }
        // Save changes made to names and percentages.
        elseif ( $module->isCurrentAction( 'SaveChanges' ) )
        {
            $errors = applyChanges( $module, $http );
        }
        // Remove checked VAT types [with or without confirmation].
        elseif ( $module->isCurrentAction( 'Remove' ) )
        {
            $vatIDsToRemove = $module->actionParameter( 'vatTypeIDList' );
            $deps = array();
            $haveDeps = false;
            $canRemove = true;
            findDependencies( $vatIDsToRemove, $deps, $haveDeps, $canRemove );

            $errors = false;
            $showDeps = true;
            // If there are dependendant rules and/or products
            // then show confifmation dialog.
            if ( $haveDeps )
            {
                // Let the user choose another VAT to set

                $allVatTypes = \eZVatType::fetchList( true, true );

                if ( ( count( $allVatTypes ) - count( $vatIDsToRemove ) ) == 0 )
                {
                    $errorMsg = 'You cannot remove all VAT types. ' .
                                'If you do not neet to charge any VAT for your ' .
                                'products then just leave one VAT type and set ' .
                                'its percentage to zero.';
                    $errors[] = \ezpI18n::tr( 'kernel/shop/vattype', $errorMsg );
                    $showDeps = false;
                }

                $tpl->setVariable( 'can_remove', $canRemove ); // true if we allow the removal
                $tpl->setVariable( 'show_dependencies', $showDeps ); // true if we'll show the VAT types' dependencies
                $tpl->setVariable( 'errors', $errors ); // array of error messages, false if there are no errors
                $tpl->setVariable( 'dependencies', $deps );
                $tpl->setVariable( 'vat_type_ids', join( ',', $vatIDsToRemove ) );
                $path = array( array( 'text' => \ezpI18n::tr( 'kernel/shop', 'VAT types' ),
                                      'url'  => false ) );

                $Result = array();
                $Result['path'] = $path;
                $Result['content'] = $tpl->fetch( "design:shop/removevattypes.tpl" );
                return $this->viewResult( isset( $Result ) ? $Result : null, null );
            }
            else // otherwise just silently remove the VAT types.
            {
                $module->setCurrentAction( 'ConfirmRemoval' );
                // pass through
            }
        }
        // Do actually remove checked VAT types.
        if ( $module->isCurrentAction( 'ConfirmRemoval' ) )
        {
            $afterConfirmation = false;

            $vatIDsToRemove = $module->actionParameter( 'vatTypeIDList' );

            // The list of VAT types to remove is a string
            // if passed from the confirmation dialog
            // and an array if passed from another action.
            if ( is_string( $vatIDsToRemove ) )
            {
                $vatIDsToRemove = explode( ',', $vatIDsToRemove );
                $afterConfirmation = true;
            }

            if ( !$afterConfirmation )
                $errors = applyChanges( $module, $http );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $vatIDsToRemove as $vatID )
            {
                $vatType = \eZVatType::fetch( $vatID );
                if ( is_object( $vatType ) )
                {
                    $vatType->removeThis();
                }
            }
            $db->commit();
        }

        $vatTypeArray = \eZVatType::fetchList( true, true );

        // Paged. The whole list was read and every row of it drawn.
        $pageCount  = count( $vatTypeArray );
        $pageLimit  = \expAdminPagination::limit( 'shop/vattype' );
        $pageOffset = \expAdminPagination::offset( $Params );
        $vatTypeArray = \expAdminPagination::page( $vatTypeArray, $pageOffset, $pageLimit );

        if ( is_array( $errors ) )
            $errors = array_unique( $errors );

        $tpl->setVariable( "vattype_array", $vatTypeArray );
        $tpl->setVariable( "module", $module );
        $tpl->setVariable( 'errors', $errors );
        $tpl->setVariable( 'vattype_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

        $path = array();
        $path[] = array( 'text' => \ezpI18n::tr( 'kernel/shop', 'VAT types' ),
                         'url' => false );


        $Result = array();
        $Result['path'] = $path;
        $Result['content'] = $tpl->fetch( "design:shop/vattype.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
