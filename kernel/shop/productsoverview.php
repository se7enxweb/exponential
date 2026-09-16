<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$module = $Params['Module'];
$offset = $Params['Offset'];
$productClassIdentifier = $Params['ProductClass'];
$productClass = false;
$priceAttributeIdentifier = false;

if ( $module->isCurrentAction( 'Sort' ) )
{
    $productClassIdentifier = $module->hasActionParameter( 'ProductClass' ) ? $module->actionParameter( 'ProductClass' ) : false;
    $sortingField = $module->hasActionParameter( 'SortingField' ) ? $module->actionParameter( 'SortingField' ) : 'none';
    $sortingOrder = $module->hasActionParameter( 'SortingOrder' ) ? $module->actionParameter( 'SortingOrder' ) : 'asc';

    eZPreferences::setValue( 'productsoverview_sorting_field', $sortingField );
    eZPreferences::setValue( 'productsoverview_sorting_order', $sortingOrder );
}

if ( $module->isCurrentAction( 'ShowProducts' ) )
    $productClassIdentifier = $module->hasActionParameter( 'ProductClass' ) ? $module->actionParameter( 'ProductClass' ) : false;

$productClassList = eZShopFunctions::productClassList();

// find selected product class
if ( count( $productClassList ) > 0 )
{
    if ( $productClassIdentifier )
    {
        foreach( $productClassList as $productClassItem )
        {
            if ( $productClassItem->attribute( 'identifier' ) === $productClassIdentifier )
            {
                $productClass = $productClassItem;
                break;
            }
        }
    }
    else
    {
        // use first element of $productClassList
        $productClass = $productClassList[0];
    }
}

if ( is_object( $productClass ) )
    $priceAttributeIdentifier = eZShopFunctions::priceAttributeIdentifier( $productClass );

// The sizes on offer are configured, not written here; the preference holds
// the position in that list, which is what it has always held, so the sizes can
// be changed without resetting anybody's choice.
list( $limit, $limitChoice, $limitChoices ) = expAdminPagination::chosen( 'shop/productsoverview', 'productsoverview_list_limit' );

$sortingField = eZPreferences::value( 'productsoverview_sorting_field' );
$sortingOrder = eZPreferences::value( 'productsoverview_sorting_order' );

$viewParameters = array( 'offset' => $offset );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'product_class_list', $productClassList );
$tpl->setVariable( 'product_class', $productClass );
$tpl->setVariable( 'price_attribute_identifier', $priceAttributeIdentifier );
$tpl->setVariable( 'sorting_field', $sortingField );
$tpl->setVariable( 'sorting_order', $sortingOrder );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'limit_choices', $limitChoices );
$tpl->setVariable( 'limit_choice', $limitChoice );
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = array();
$Result['content'] = $tpl->fetch( "design:shop/productsoverview.tpl" );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'kernel/shop', 'Products overview' ),
                                'url' => false ) );

?>
