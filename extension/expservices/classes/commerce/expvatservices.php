<?php
/**
 * ezjscore/call/expvat::<method>: VAT types, VAT charging rules by country and product category, the product
 * categories the rules use, and the VAT of a product for a country. Reads are public where a shop needs them
 * (the types, the rate); the writes need the shop/administrate policy. The kernel writes the commerce.vat.change
 * audit event of every change.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expVatServices extends expServiceBase
{
    public static $services = array(
        'types' => array( 'summary' => 'The VAT types with their percentage', 'access' => 'public', 'write' => false,
            'args' => array( 'skip_dynamic' => 'bool' ), 'returns' => 'list of VAT types' ),
        'type' => array( 'summary' => 'One VAT type', 'access' => 'public', 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'VAT type' ),
        'typeUsage' => array( 'summary' => 'How many products, product classes and rules use a VAT type', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'id' => 'int' ), 'returns' => 'products, classes, rules' ),
        'createType' => array( 'summary' => 'Creates a VAT type', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'name' => 'string POST', 'percentage' => 'string POST' ), 'returns' => 'the VAT type' ),
        'updateType' => array( 'summary' => 'Changes the name or percentage of a VAT type', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'name' => 'string POST', 'percentage' => 'string POST' ), 'returns' => 'the VAT type' ),
        'removeType' => array( 'summary' => 'Removes a VAT type, its rules; products fall back to the default of their class', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'rules' => array( 'summary' => 'The VAT charging rules', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of rules' ),
        'rule' => array( 'summary' => 'One VAT charging rule', 'access' => array( 'shop', 'administrate' ), 'write' => false, 'args' => array( 'id' => 'int' ), 'returns' => 'rule' ),
        'createRule' => array( 'summary' => 'Creates a rule: country code (or Any), VAT type id, product category ids', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'country_code' => 'string POST', 'vat_type' => 'int POST', 'categories' => 'list POST' ), 'returns' => 'the rule' ),
        'updateRule' => array( 'summary' => 'Changes a rule', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST', 'country_code' => 'string POST', 'vat_type' => 'int POST', 'categories' => 'list POST' ), 'returns' => 'the rule' ),
        'removeRule' => array( 'summary' => 'Removes a rule', 'access' => array( 'shop', 'administrate' ), 'write' => true, 'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'categories' => array( 'summary' => 'The product categories the rules refer to, with their product count', 'access' => array( 'shop', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'list of categories' ),
        'createCategory' => array( 'summary' => 'Creates a product category', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'name' => 'string POST' ), 'returns' => 'the category' ),
        'removeCategory' => array( 'summary' => 'Removes a product category and its references', 'access' => array( 'shop', 'administrate' ), 'write' => true,
            'args' => array( 'id' => 'int POST' ), 'returns' => 'removed id' ),
        'forProduct' => array( 'summary' => 'The VAT percent of a product for a country (dynamic VAT handler), or the product\'s own', 'access' => 'public', 'write' => false,
            'args' => array( 'node_id' => 'int', 'country' => 'string' ), 'returns' => 'percent, handler' ),
        'settings' => array( 'summary' => 'The VAT settings of the shop: dynamic charging, handler, country requirement', 'access' => 'public', 'write' => false,
            'args' => array(), 'returns' => 'settings' ),
        'userCountry' => array( 'summary' => 'The country used for the VAT of the current user', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'country, required' ),
        'setUserCountry' => array( 'summary' => 'Sets the preferred country of the current session for the VAT', 'access' => array( 'content', 'read' ), 'write' => true,
            'args' => array( 'country' => 'string POST' ), 'returns' => 'country' ),
        'countries' => array( 'summary' => 'The country codes with VAT rules and the countries of the installation', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'list of codes' ),
    );

    protected static function fetchType( $id )
    {
        $t = eZVatType::fetch( (int)$id );
        if ( !$t instanceof eZVatType )
            throw new expServiceException( "No VAT type $id", 404 );
        return $t;
    }

    protected static function fetchRule( $id )
    {
        $r = eZVatRule::fetch( (int)$id );
        if ( !$r instanceof eZVatRule )
            throw new expServiceException( "No VAT rule $id", 404 );
        return $r;
    }

    /**
     * Removes a rule and its category references. The kernel's eZVatRule::removeVatRule() calls an instance method
     * statically, which PHP 8 refuses, so the rule is removed here the way that method does, with its audit event.
     */
    protected static function deleteRule( eZVatRule $r )
    {
        $id = (int)$r->attribute( 'id' );
        if ( class_exists( 'expAuditHook' ) && expAuditHook::on( 'commerce.vat.change' ) )
            expAuditHook::emit( 'commerce.vat.change', array( 'object' => array( 'type' => 'vat_rule', 'id' => $id ), 'verb' => 'remove',
                'before' => array( 'country_code' => $r->attribute( 'country_code' ), 'vat_type' => $r->attribute( 'vat_type' ) ) ) );
        $db = eZDB::instance();
        $db->begin();
        $r->removeProductCategories( $id );
        eZPersistentObject::removeObject( eZVatRule::definition(), array( 'id' => $id ) );
        $db->commit();
    }
    protected static function percent( $v )
    {
        $v = str_replace( ',', '.', trim( $v ) );
        if ( !is_numeric( $v ) || (float)$v < 0 || (float)$v > 100 )
            throw new expServiceException( 'The percentage is a number between 0 and 100', 422 );
        return (float)$v;
    }

    public static function types( array $args )
    {
        self::guard( 'types' );
        $out = array();
        foreach ( eZVatType::fetchList( true, self::arg( $args, 0, 'bool', false ) ) as $t )
            $out[] = expCommerceExport::vatType( $t );
        return self::ok( $out );
    }

    public static function type( array $args )
    {
        self::guard( 'type' );
        return self::ok( expCommerceExport::vatType( self::fetchType( self::arg( $args, 0, 'int' ) ) ) );
    }

    public static function typeUsage( array $args )
    {
        self::guard( 'typeUsage' );
        $t = self::fetchType( self::arg( $args, 0, 'int' ) );
        $id = (int)$t->attribute( 'id' );
        return self::ok( array( 'id' => $id, 'products' => (int)eZVatType::fetchDependentProductsCount( $id ), 'classes' => (int)eZVatType::fetchDependentClassesCount( $id ),
            'rules' => (int)eZVatRule::fetchCountByVatType( $id ) ) );
    }

    public static function createType( array $args )
    {
        self::guard( 'createType' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' || mb_strlen( $name ) > 255 )
            throw new expServiceException( 'The name is required, up to 255 characters', 422 );
        $t = eZVatType::create();
        $t->setAttribute( 'name', $name );
        $t->setAttribute( 'percentage', self::percent( self::post( 'percentage', 'string' ) ) );
        $t->store();
        return self::ok( expCommerceExport::vatType( eZVatType::fetch( $t->attribute( 'id' ) ) ) );
    }

    public static function updateType( array $args )
    {
        self::guard( 'updateType' );
        $t = self::fetchType( self::post( 'id', 'int' ) );
        $name = self::post( 'name', 'string', null );
        $p = self::post( 'percentage', 'string', null );
        if ( $name === null && $p === null )
            throw new expServiceException( 'Nothing to change: send name or percentage', 400 );
        if ( $name !== null )
        {
            if ( trim( $name ) === '' )
                throw new expServiceException( 'The name must not be empty', 422 );
            $t->setAttribute( 'name', trim( $name ) );
        }
        if ( $p !== null )
            $t->setAttribute( 'percentage', self::percent( $p ) );
        $t->store();
        return self::ok( expCommerceExport::vatType( eZVatType::fetch( $t->attribute( 'id' ) ) ) );
    }

    public static function removeType( array $args )
    {
        self::guard( 'removeType' );
        $t = self::fetchType( self::post( 'id', 'int' ) );
        if ( $t->isDynamic() )
            throw new expServiceException( 'The dynamic VAT type cannot be removed', 409 );
        $id = (int)$t->attribute( 'id' );
        foreach ( eZVatRule::fetchByVatType( $id ) as $rule )
            self::deleteRule( $rule );
        $t->removeThis();
        return self::ok( array( 'removed' => $id ) );
    }

    public static function rules( array $args )
    {
        self::guard( 'rules' );
        $out = array();
        foreach ( eZVatRule::fetchList() as $r )
            $out[] = expCommerceExport::vatRule( $r );
        return self::pageOf( $out, $args, 0, 1 );
    }

    public static function rule( array $args )
    {
        self::guard( 'rule' );
        return self::ok( expCommerceExport::vatRule( self::fetchRule( self::arg( $args, 0, 'int' ) ) ) );
    }

    protected static function categoryRows( array $ids )
    {
        $rows = array();
        foreach ( $ids as $id )
        {
            if ( !is_numeric( $id ) || !eZProductCategory::fetch( (int)$id ) )
                throw new expServiceException( "No product category $id", 422 );
            $rows[] = array( 'id' => (int)$id );
        }
        return $rows;
    }

    protected static function applyRule( eZVatRule $r )
    {
        $country = self::post( 'country_code', 'string', null );
        if ( $country !== null )
        {
            if ( $country !== 'Any' && !preg_match( '/^[A-Z]{2}$/', $country ) )
                throw new expServiceException( 'country_code is a two letter code in capitals, or Any', 422 );
            $r->setAttribute( 'country_code', $country );
        }
        $vt = self::post( 'vat_type', 'int', null );
        if ( $vt !== null )
        {
            self::fetchType( $vt );
            $r->setAttribute( 'vat_type', $vt );
        }
        $cats = self::post( 'categories', 'list', null );
        if ( $cats !== null )
            $r->setAttribute( 'product_categories', self::categoryRows( $cats ) );
        return $r;
    }

    public static function createRule( array $args )
    {
        self::guard( 'createRule' );
        self::post( 'country_code', 'string' );
        self::post( 'vat_type', 'int' );
        $r = eZVatRule::create();
        $r->setAttribute( 'product_categories', array() );
        self::applyRule( $r );
        $r->store();
        return self::ok( expCommerceExport::vatRule( eZVatRule::fetch( $r->attribute( 'id' ) ) ) );
    }

    public static function updateRule( array $args )
    {
        self::guard( 'updateRule' );
        $r = self::applyRule( self::fetchRule( self::post( 'id', 'int' ) ) );
        $r->store();
        return self::ok( expCommerceExport::vatRule( eZVatRule::fetch( $r->attribute( 'id' ) ) ) );
    }

    public static function removeRule( array $args )
    {
        self::guard( 'removeRule' );
        $r = self::fetchRule( self::post( 'id', 'int' ) );
        $id = (int)$r->attribute( 'id' );
        self::deleteRule( $r );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function categories( array $args )
    {
        self::guard( 'categories' );
        $out = array();
        foreach ( (array)eZProductCategory::fetchList() as $c )
            $out[] = array( 'id' => (int)$c->attribute( 'id' ), 'name' => $c->attribute( 'name' ), 'products' => (int)eZProductCategory::fetchProductCountByCategory( $c->attribute( 'id' ) ) );
        return self::ok( $out );
    }

    public static function createCategory( array $args )
    {
        self::guard( 'createCategory' );
        $name = trim( self::post( 'name', 'string' ) );
        if ( $name === '' )
            throw new expServiceException( 'The name is required', 422 );
        $c = eZProductCategory::create();
        $c->setAttribute( 'name', $name );
        $c->store();
        return self::ok( array( 'id' => (int)$c->attribute( 'id' ), 'name' => $c->attribute( 'name' ) ) );
    }

    public static function removeCategory( array $args )
    {
        self::guard( 'removeCategory' );
        $id = self::post( 'id', 'int' );
        if ( !eZProductCategory::fetch( $id ) )
            throw new expServiceException( "No product category $id", 404 );
        eZProductCategory::removeByID( $id );
        return self::ok( array( 'removed' => $id ) );
    }

    public static function forProduct( array $args )
    {
        self::guard( 'forProduct' );
        $node = self::node( self::arg( $args, 0, 'int' ), 'read' );
        $country = self::arg( $args, 1, 'string', false );
        $object = $node->attribute( 'object' );
        $dynamic = (bool)eZVATManager::isDynamicVatChargingEnabled();
        $percent = null;
        $source = 'product';
        if ( $dynamic )
        {
            $percent = eZVATManager::getVAT( $object, $country );
            $source = 'handler';
        }
        if ( $percent === null )
        {
            $p = expCommerceExport::price( $node->attribute( 'data_map' ) );
            $percent = $p ? $p['vat_percent'] : null;
            $source = 'product';
        }
        return self::ok( array( 'percent' => expCommerceExport::num( $percent ), 'source' => $source, 'country' => $country ?: null ) );
    }

    public static function settings( array $args )
    {
        self::guard( 'settings' );
        $ini = eZINI::instance( 'shop.ini' );
        return self::ok( array( 'dynamic' => (bool)eZVATManager::isDynamicVatChargingEnabled(),
            'handler' => $ini->hasVariable( 'VATSettings', 'Handler' ) ? $ini->variable( 'VATSettings', 'Handler' ) : null,
            'require_user_country' => (bool)eZVATManager::isUserCountryRequired(),
            'user_country_attribute' => $ini->hasVariable( 'VATSettings', 'UserCountryAttribute' ) ? $ini->variable( 'VATSettings', 'UserCountryAttribute' ) : null,
            'product_category_attribute' => $ini->hasVariable( 'VATSettings', 'ProductCategoryAttribute' ) ? $ini->variable( 'VATSettings', 'ProductCategoryAttribute' ) : null ) );
    }

    public static function userCountry( array $args )
    {
        self::guard( 'userCountry' );
        $user = eZUser::currentUser();
        return self::ok( array( 'country' => $user->isRegistered() || eZShopFunctions::getPreferredUserCountry() ? eZVATManager::getUserCountry( $user ) : eZShopFunctions::getPreferredUserCountry(),
            'preferred' => eZShopFunctions::getPreferredUserCountry() ?: null, 'required' => (bool)eZVATManager::isUserCountryRequired() ) );
    }

    public static function setUserCountry( array $args )
    {
        self::guard( 'setUserCountry' );
        $c = self::post( 'country', 'string' );
        if ( !preg_match( '/^[A-Z]{2,3}$/', $c ) )
            throw new expServiceException( 'The country is a code like DE', 422 );
        eZShopFunctions::setPreferredUserCountry( $c );
        return self::ok( array( 'country' => $c ) );
    }

    public static function countries( array $args )
    {
        self::guard( 'countries' );
        $codes = array();
        foreach ( eZVatRule::fetchList() as $r )
            $codes[] = $r->attribute( 'country_code' );
        return self::ok( array( 'with_rules' => array_values( array_unique( $codes ) ) ) );
    }
}
