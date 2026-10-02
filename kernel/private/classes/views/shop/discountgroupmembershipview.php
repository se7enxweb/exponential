<?php
/**
 * The code of kernel/shop/discountgroupmembershipview.php, moved into a class (#207 stage 1). The file kernel/shop/discountgroupmembershipview.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/shop/discountgroupmembershipview.php:
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

class Discountgroupmembershipview extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $discountGroupID = null;
        if ( isset( $Params["DiscountGroupID"] ) )
            $discountGroupID = $Params["DiscountGroupID"];

        $discountGroup = \eZDiscountRule::fetch( $discountGroupID );
        if( $discountGroup === null )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }


        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( "AddRuleButton" ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->redirectTo( $module->functionURI( 'discountruleedit' ) . '/' . $discountGroupID ) );
        }

        if ( $http->hasPostVariable( "RemoveRuleButton" ) )
        {
            $discountRuleIDList = $http->postVariable( "removeRuleList" );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $discountRuleIDList  as $discountRuleID )
            {
                \eZDiscountSubRuleValue::removeBySubRuleID ( $discountRuleID );
                \eZDiscountSubRule::remove( $discountRuleID );
            }
            $db->commit();

            // we changed prices => remove content cache
            \eZContentCacheManager::clearAllContentCache();

            $module->redirectTo( $module->functionURI( "discountgroupview" ) . "/" . $discountGroupID );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        if ( $http->hasPostVariable( "AddCustomerButton" ) )
        {
            \eZContentBrowse::browse( array( 'action_name' => 'AddCustomer',
                                            'description_template' => 'design:shop/browse_discountcustomer.tpl',
                                            'keys' => array( 'discountgroup_id' => $discountGroupID ),
                                            'content' => array( 'discountgroup_id' => $discountGroupID ),
                                            'from_page' => "/shop/discountgroupview/$discountGroupID" ),
                                     $module );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        // Add customer or customer group to this rule
        if ( $module->isCurrentAction( 'AddCustomer' ) )
        {
            $selectedObjectIDArray = \eZContentBrowse::result( 'AddCustomer' );
            $userIDArray = \eZUserDiscountRule::fetchUserID( $discountGroupID );

            $db = \eZDB::instance();
            $db->begin();
            foreach ( $selectedObjectIDArray as $objectID )
            {
                if ( !in_array(  $objectID, $userIDArray ) )
                {
                    $userRule = \eZUserDiscountRule::create( $discountGroupID, $objectID );
                    $userRule->store();
                }
            }
            $db->commit();

            // because we changed users, we have to remove content cache
            \eZContentCacheManager::clearAllContentCache();
        }
        if ( $http->hasPostVariable( "RemoveCustomerButton" ) )
        {
            if (  $http->hasPostVariable( "CustomerIDArray" ) )
            {
                $customerIDArray = $http->postVariable( "CustomerIDArray" );

                $db = \eZDB::instance();
                $db->begin();
                foreach ( $customerIDArray as $customerID )
                {
                    \eZUserDiscountRule::removeUser( $customerID );
                }
                $db->commit();
            }

            \eZContentCacheManager::clearAllContentCache();
        }

        $membershipList = \eZUserDiscountRule::fetchByRuleID( $discountGroupID );
        $customers = array();
        foreach ( $membershipList as $membership )
        {
            $customers[] = \eZContentObject::fetch( $membership->attribute( 'contentobject_id' ) );
        }

        $ruleList = \eZDiscountSubRule::fetchByRuleID( $discountGroupID );

        $ruleArray = array();
        foreach ( $ruleList as $rule )
        {
            $name = $rule->attribute( 'name' );
            $percent = $rule->attribute( 'discount_percent' );
            $limitation = $rule->attribute( 'limitation' );
            $discountRuleID = $rule->attribute( 'id' );
            if ( $limitation != '*' )
            {
                $ruleValues = \eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID );
                if ( $ruleValues != null )
                {
                    $limitation = \ezpI18n::tr( 'kernel/shop', 'Classes' ).' ';
                    $firstLoop = true;
                    foreach ( $ruleValues as $ruleValue )
                    {
                        $classID = $ruleValue->attribute( 'value' );
                        $class = \eZContentClass::fetch( $classID );
                        if ( $class )
                        {
                            if ( !$firstLoop )
                            {
                                $limitation .= ', ';
                            }
                            else
                            {
                                $firstLoop = false;
                            }
                            $className = $class->attribute( 'name' );
                            $limitation .= "'". $className . "'";
                        }
                    }
                }
                else
                {
                    $limitation = \ezpI18n::tr( 'kernel/shop', 'Any class' );
                }
                $sectionRuleValues = \eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID, 1 );
                if ( $sectionRuleValues != null )
                {
                    $limitation .= ' '.\ezpI18n::tr( 'kernel/shop', 'in sections' ).' ';
                    $firstLoop = true;
                    foreach ( $sectionRuleValues as $sectionRuleValue )
                    {
                        $sectionID = $sectionRuleValue->attribute( 'value' );
                        $section = \eZSection::fetch( $sectionID );
                        if ( $section )
                        {
                            if ( !$firstLoop )
                            {
                                $limitation .= ', ';
                            }
                            else
                            {
                                $firstLoop = false;
                            }
                            $sectionName = $section->attribute( 'name' );
                            $limitation .= "'".$sectionName . "'";
                        }
                    }
                }
                else
                {
                    $limitation .= ' '.\ezpI18n::tr( 'kernel/shop', 'in any section' );
                }
                $productRuleValues = \eZDiscountSubRuleValue::fetchBySubRuleID( $discountRuleID, 2 );

                if ( $productRuleValues != null )
                {
                    $limitation = \ezpI18n::tr( 'kernel/shop', 'Products' ).' ';
                    $firstLoop = true;
                    foreach ( $productRuleValues as $productRuleValue )
                    {
                        $objectID = $productRuleValue->attribute( 'value' );
                        $product = \eZContentObject::fetch( $objectID );
                        if ( $product )
                        {
                            if ( !$firstLoop )
                            {
                                $limitation .= ', ';
                            }
                            else
                            {
                                $firstLoop = false;
                            }
                            $productName = $product->attribute( 'name' );
                            $limitation .= "'".$productName . "'";
                        }
                    }
                }
            }
            else
            {
                $limitation = \ezpI18n::tr( 'kernel/shop', 'Any product' );
            }

            $item = array( "name" => $name,
                           "discount_percent" => $percent,
                           "id" => $discountRuleID,
                           "limitation" => $limitation );
            $ruleArray[] = $item;
        }
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( "module", $module );
        $tpl->setVariable( "customers", $customers );
        $tpl->setVariable( "discountgroup", $discountGroup );
        $tpl->setVariable( "rule_list", $ruleArray );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:shop/discountgroupmembershipview.tpl" );
        $Result['path'] = array( array( 'url' => '/shop/discountgroup/',
                                        'text' => \ezpI18n::tr( 'kernel/shop', 'Group view of discount rule' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
