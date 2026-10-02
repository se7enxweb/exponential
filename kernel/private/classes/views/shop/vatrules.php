<?php
/**
 * The code of kernel/shop/vatrules.php, moved into a class (#207 stage 1). The file kernel/shop/vatrules.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/vatrules.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'findErrors' ) ) {
/**
 * Find errors in VAT charging rules.
 *
 * \return list of errors, or false if no errors found.
 */
function findErrors( $vatRules )
{
    $errors = false;

    // 1. Check if default rule exists.
    $defaultRuleExists = false;
    foreach ( $vatRules as $rule )
    {
        if ( $rule->attribute( 'country' ) == '*' &&
             !$rule->attribute( 'product_categories' ) )
        {
            $defaultRuleExists = true;
            break;
        }
    }

    if ( !$defaultRuleExists && count( $vatRules ) > 0 )
        $errors[] = ezpI18n::tr( 'kernel/shop/vatrules', 'No default rule found. ' .
                            'Please add rule having "Any" country and "Any" category.' );

    // 2. Check for conflicting rules.
    // Conflicting rules are those having the same country and equal or intersecting categories sets.
    $vatRulesCount = count( $vatRules );
    for ( $i=0; $i < $vatRulesCount; $i++ )
    {
        $iRule       = $vatRules[$i];
        $iCountry    = $iRule->attribute( 'country_code' );
        $iCategories = $iRule->attribute( 'product_categories_names' );

        for ( $j=$i+1; $j < $vatRulesCount; $j++ )
        {
            $jRule       = $vatRules[$j];
            $jCountry    = $jRule->attribute( 'country_code' );

            if ( $iCountry != $jCountry )
                continue;

            $jCategories = $jRule->attribute( 'product_categories_names' );

            // Multiple default rules.
            if ( !$iCategories && !$jCategories )
            {
                if ( $iCountry == '*' )
                {
                    $errorMessage = "Conflict: There are multiple default rules.";
                    $errors[] = ezpI18n::tr( 'kernel/shop/vatrules', $errorMessage );
                }
                else
                {
                    $errorMessage = "Conflict: There are multiple default rules for country '%1'.";
                    $errors[] = ezpI18n::tr( 'kernel/shop/vatrules', $errorMessage, null, array( $iCountry ) );
                }
            }
            // Intersecting rules.
            elseif ( $iCategories && $jCategories && $commonCategories = array_intersect( $iCategories, $jCategories ) )
            {
                if ( $iCountry == '*' )
                {
                    $errorMessage = "Conflict: The following categories for any country are mentioned in multiple rules: %2.";
                    $errors[] = ezpI18n::tr( 'kernel/shop/vatrules', $errorMessage, null, array( $iCountry, join( ',', $commonCategories ) ) );
                }
                else
                {
                    $errorMessage = "Conflict: The following categories for country '%1' are mentioned in multiple rules: %2.";
                    $errors[] = ezpI18n::tr( 'kernel/shop/vatrules', $errorMessage, null, array( $iCountry, join( ',', $commonCategories ) ) );
                }
            }
        }
    }

    if ( is_array( $errors ) )
    {
        // Remove duplicated error messages.
        $errors = array_unique( $errors );
        sort( $errors );
    }

    return $errors;
}
}

if ( !function_exists( 'compareVatRules' ) ) {
/**
 * Auxiliary function used to sort VAT rules.
 *
 * Rules are sorted by country and categories.
 * Any specific categories list or country is considered less than '*' (Any).
 */
function compareVatRules($a, $b)
{
    // Compare countries.

    $aCountry = $a->attribute( 'country' );
    $bCountry = $b->attribute( 'country' );

    if ( $aCountry != $bCountry )
    {
        if ( $aCountry == '*' )
            return 1;
        if ( $bCountry == '*' )
            return -1;

        return ( $aCountry < $bCountry ? -1 : 1 );
    }

    // Ok, countries are equal. Let's compare categories then.

    if ( $a->attribute( 'product_categories' ) )
        $aCategory = $a->attribute( 'product_categories_string' );
    else
        $aCategory = '*';

    if ( $b->attribute( 'product_categories' ) )
        $bCategory = $b->attribute( 'product_categories_string' );
    else
        $bCategory = '*';

    if ( $aCategory != $bCategory )
    {
        if ( $aCategory == '*' )
            return 1;
        if ( $bCategory == '*' )
            return -1;

        return ( $aCategory < $bCategory ? -1 : 1 );
    }

    return 0;
}
}
}

namespace Exponential\View\Kernel\Shop
{

class Vatrules extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http   = \eZHTTPTool::instance();
        $tpl = \eZTemplate::factory();

        if ( $http->hasPostVariable( "AddRuleButton" ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectTo( $module->functionURI( "editvatrule" ) ) );
        }

        if ( $http->hasPostVariable( "RemoveRuleButton" ) )
        {
            if ( !$http->hasPostVariable( "RuleIDList" ) )
                $ruleIDList = array();
            else
                $ruleIDList = $http->postVariable( "RuleIDList" );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $ruleIDList as $ruleID )
                \eZVatRule::removeVatRule( $ruleID );
            $db->commit();
        }

        if ( $http->hasPostVariable( "SaveCategoriesButton" ) )
        {
            $db = \eZDB::instance();
            $db->begin();
            foreach ( $productCategories as $cat )
            {
                $id = $cat->attribute( 'id' );

                if ( !$http->hasPostVariable( "category_name_" . $id ) )
                    continue;

                $name = $http->postVariable( "category_name_" . $id );
                $cat->setAttribute( 'name', $name );
                $cat->store();
            }
            $db->commit();
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectTo( $module->functionURI( "productcategories" ) ) );
        }

        $vatRules = \eZVatRule::fetchList();

        // Paged. The whole list was read and every row of it drawn.
        $pageCount  = count( $vatRules );
        $pageLimit  = \expAdminPagination::limit( 'shop/vatrules' );
        $pageOffset = \expAdminPagination::offset( $Params );
        $vatRules = \expAdminPagination::page( $vatRules, $pageOffset, $pageLimit );
        $errors = findErrors( $vatRules );
        usort( $vatRules, 'compareVatRules' );

        $tpl->setVariable( 'rules', $vatRules );
        $tpl->setVariable( 'errors', $errors );
        $tpl->setVariable( 'vatrule_count', $pageCount );
        $tpl->setVariable( 'limit', $pageLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

        $path = array();
        $path[] = array( 'text' => \ezpI18n::tr( 'kernel/shop/vatrules', 'VAT rules' ),
                         'url' => false );

        $Result = array();
        $Result['path'] = $path;
        $Result['content'] = $tpl->fetch( "design:shop/vatrules.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
