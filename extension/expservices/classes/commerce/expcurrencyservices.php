<?php
/**
 * ezjscore/call/expcurrency::<method>: the currencies of the shop with their rates, conversion, the preferred
 * currency of the user. The kernel writes the commerce.currency.change audit event of every change.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expCurrencyServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The currencies with symbol, status and rates', 'access' => 'public', 'write' => false,
            'args' => array( 'only_active' => 'bool' ), 'returns' => 'list of currencies' ),
        'view' => array( 'summary' => 'One currency by its code', 'access' => 'public', 'write' => false, 'args' => array( 'code' => 'string' ), 'returns' => 'currency' ),
        'count' => array( 'summary' => 'How many currencies exist', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'count' ),
        'codes' => array( 'summary' => 'The codes of the active currencies', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'list of codes' ),
        'exists' => array( 'summary' => 'Whether a currency code exists', 'access' => 'public', 'write' => false, 'args' => array( 'code' => 'string' ), 'returns' => 'exists, valid' ),
        'baseCurrency' => array( 'summary' => 'The base currency of the exchange rates and the shop\'s default', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'base, default' ),
        'rate' => array( 'summary' => 'The rate of a currency against the base', 'access' => 'public', 'write' => false, 'args' => array( 'code' => 'string' ), 'returns' => 'code, rate' ),
        'crossRate' => array( 'summary' => 'The rate between two currencies', 'access' => 'public', 'write' => false, 'args' => array( 'from' => 'string', 'to' => 'string' ), 'returns' => 'from, to, rate' ),
        'convert' => array( 'summary' => 'Converts an amount from one currency to another with the rates and rounding of the shop', 'access' => 'public', 'write' => false,
            'args' => array( 'amount' => 'string', 'from' => 'string', 'to' => 'string' ), 'returns' => 'amount, converted' ),
        'rounding' => array( 'summary' => 'The rounding settings of conversions', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'type, precision, target' ),
        'preferred' => array( 'summary' => 'The preferred currency of the current user', 'access' => 'public', 'write' => false, 'args' => array(), 'returns' => 'code, valid' ),
        'setPreferred' => array( 'summary' => 'Sets the preferred currency of the current user', 'access' => 'user', 'write' => true, 'args' => array( 'code' => 'string POST' ), 'returns' => 'code' ),
        'create' => array( 'summary' => 'Creates a currency', 'access' => array( 'shop', 'setup' ), 'write' => true,
            'args' => array( 'code' => 'string POST', 'symbol' => 'string POST', 'locale' => 'string POST', 'custom_rate' => 'string POST', 'rate_factor' => 'string POST', 'status' => 'string POST active|inactive' ),
            'returns' => 'the currency' ),
        'update' => array( 'summary' => 'Changes symbol, locale, rates, factor or status of a currency', 'access' => array( 'shop', 'setup' ), 'write' => true,
            'args' => array( 'code' => 'string POST', 'symbol' => 'string POST', 'locale' => 'string POST', 'custom_rate' => 'string POST', 'rate_factor' => 'string POST', 'status' => 'string POST' ),
            'returns' => 'the currency' ),
        'setStatus' => array( 'summary' => 'Activates or deactivates a currency', 'access' => array( 'shop', 'setup' ), 'write' => true,
            'args' => array( 'code' => 'string POST', 'status' => 'string POST active|inactive' ), 'returns' => 'the currency' ),
        'remove' => array( 'summary' => 'Removes a currency', 'access' => array( 'shop', 'setup' ), 'write' => true, 'args' => array( 'code' => 'string POST' ), 'returns' => 'removed code' ),
        'updateRates' => array( 'summary' => 'Fetches the automatic rates from the configured provider and stores them', 'access' => array( 'shop', 'setup' ), 'write' => true, 'args' => array(), 'returns' => 'rates updated' ),
        'updateAutoprices' => array( 'summary' => 'Recalculates the automatic prices of multi price products from the rates', 'access' => array( 'shop', 'setup' ), 'write' => true, 'args' => array(), 'returns' => 'done' ),
        'providers' => array( 'summary' => 'The exchange rate settings: provider, server, base', 'access' => array( 'shop', 'setup' ), 'write' => false, 'args' => array(), 'returns' => 'settings' ),
    );

    protected static function code( $raw )
    {
        $c = strtoupper( trim( $raw ) );
        if ( !preg_match( '/^[A-Z]{3}$/', $c ) )
            throw new expServiceException( 'A currency code is three letters', 400 );
        return $c;
    }

    protected static function fetchCurrency( $code )
    {
        $c = eZCurrencyData::fetch( self::code( $code ) );
        if ( !$c instanceof eZCurrencyData )
            throw new expServiceException( "No currency $code", 404 );
        return $c;
    }

    protected static function statusOf( $s )
    {
        $s = strtolower( $s );
        if ( $s === 'active' )
            return eZCurrencyData::STATUS_ACTIVE;
        if ( $s === 'inactive' )
            return eZCurrencyData::STATUS_INACTIVE;
        throw new expServiceException( 'status is active or inactive', 422 );
    }

    protected static function rateNum( $v, $what )
    {
        $v = str_replace( ',', '.', trim( $v ) );
        if ( !is_numeric( $v ) || (float)$v < 0 )
            throw new expServiceException( "$what must be a number of 0 or more", 422 );
        return sprintf( '%.5f', (float)$v );
    }

    public static function list( array $args )
    {
        self::guard( 'list' );
        $only = self::arg( $args, 0, 'bool', false );
        $out = array();
        foreach ( (array)eZCurrencyData::fetchList( null, true ) as $c )
            if ( !$only || $c->isActive() )
                $out[] = expCommerceExport::currency( $c );
        return self::ok( $out );
    }

    public static function view( array $args )
    {
        self::guard( 'view' );
        return self::ok( expCommerceExport::currency( self::fetchCurrency( self::arg( $args, 0, 'string' ) ) ) );
    }

    public static function count( array $args )
    {
        self::guard( 'count' );
        return self::ok( array( 'count' => (int)eZCurrencyData::fetchListCount() ) );
    }

    public static function codes( array $args )
    {
        self::guard( 'codes' );
        $out = array();
        foreach ( (array)eZCurrencyData::fetchList( array( 'status' => eZCurrencyData::STATUS_ACTIVE ), true ) as $code => $c )
            $out[] = $code;
        return self::ok( $out );
    }

    public static function exists( array $args )
    {
        self::guard( 'exists' );
        $raw = strtoupper( trim( self::arg( $args, 0, 'string' ) ) );
        $valid = eZCurrencyData::validateCurrencyCode( $raw ) === eZCurrencyData::ERROR_OK;
        return self::ok( array( 'code' => $raw, 'valid' => $valid, 'exists' => $valid && eZCurrencyData::currencyExists( $raw ) ) );
    }

    public static function baseCurrency( array $args )
    {
        self::guard( 'baseCurrency' );
        $ini = eZINI::instance( 'shop.ini' );
        return self::ok( array( 'base' => $ini->hasVariable( 'ExchangeRatesSettings', 'BaseCurrency' ) ? $ini->variable( 'ExchangeRatesSettings', 'BaseCurrency' ) : null,
            'default' => $ini->hasVariable( 'CurrencySettings', 'PreferredCurrency' ) ? $ini->variable( 'CurrencySettings', 'PreferredCurrency' ) : null ) );
    }

    public static function rate( array $args )
    {
        self::guard( 'rate' );
        $c = self::fetchCurrency( self::arg( $args, 0, 'string' ) );
        return self::ok( array( 'code' => $c->attribute( 'code' ), 'rate' => expCommerceExport::num( $c->attribute( 'rate_value' ) ) ) );
    }

    public static function crossRate( array $args )
    {
        self::guard( 'crossRate' );
        $from = self::fetchCurrency( self::arg( $args, 0, 'string' ) )->attribute( 'code' );
        $to = self::fetchCurrency( self::arg( $args, 1, 'string' ) )->attribute( 'code' );
        $conv = eZCurrencyConverter::instance();
        if ( $conv->rateValue( $from ) <= 0 || $conv->rateValue( $to ) <= 0 )
            throw new expServiceException( 'A rate is not set for one of the currencies', 409 );
        return self::ok( array( 'from' => $from, 'to' => $to, 'rate' => expCommerceExport::num( $conv->crossRate( $from, $to ) ) ) );
    }

    public static function convert( array $args )
    {
        self::guard( 'convert' );
        $amount = str_replace( ',', '.', self::arg( $args, 0, 'string' ) );
        if ( !is_numeric( $amount ) )
            throw new expServiceException( 'The amount must be a number', 400 );
        $from = self::fetchCurrency( self::arg( $args, 1, 'string' ) )->attribute( 'code' );
        $to = self::fetchCurrency( self::arg( $args, 2, 'string' ) )->attribute( 'code' );
        $r = $from === $to ? (float)$amount : expCommerceExport::convert( $from, $to, (float)$amount );
        if ( $r === null )
            throw new expServiceException( 'A rate is not set for one of the currencies', 409 );
        return self::ok( array( 'amount' => (float)$amount, 'from' => $from, 'to' => $to, 'converted' => $r ) );
    }

    public static function rounding( array $args )
    {
        self::guard( 'rounding' );
        $c = eZCurrencyConverter::instance();
        return self::ok( array( 'type' => $c->roundingType(), 'precision' => $c->roundingPrecision(), 'target' => $c->roundingTarget() ) );
    }

    public static function preferred( array $args )
    {
        self::guard( 'preferred' );
        return self::ok( array( 'code' => eZShopFunctions::preferredCurrencyCode(), 'valid' => eZShopFunctions::isPreferredCurrencyValid() === eZError::SHOP_OK ) );
    }

    public static function setPreferred( array $args )
    {
        self::guard( 'setPreferred' );
        $code = self::code( self::post( 'code', 'string' ) );
        if ( eZShopFunctions::setPreferredCurrencyCode( $code ) !== eZError::SHOP_OK )
            throw new expServiceException( "The currency $code does not exist or is not active", 422 );
        return self::ok( array( 'code' => $code ) );
    }

    public static function create( array $args )
    {
        self::guard( 'create' );
        $code = self::code( self::post( 'code', 'string' ) );
        $symbol = trim( self::post( 'symbol', 'string' ) );
        if ( $symbol === '' )
            throw new expServiceException( 'The symbol is required', 422 );
        $err = eZCurrencyData::canCreate( $code );
        if ( $err !== eZCurrencyData::ERROR_OK )
            throw new expServiceException( eZCurrencyData::errorMessage( $err ), $err === eZCurrencyData::ERROR_CURRENCY_EXISTS ? 409 : 422 );
        eZShopFunctions::createCurrency( array( 'code' => $code, 'symbol' => $symbol, 'locale' => self::post( 'locale', 'string', '' ),
            'custom_rate_value' => self::rateNum( self::post( 'custom_rate', 'string', '0' ), 'custom_rate' ),
            'rate_factor' => self::rateNum( self::post( 'rate_factor', 'string', '1' ), 'rate_factor' ) ) );
        $c = eZCurrencyData::fetch( $code );
        $status = self::statusOf( self::post( 'status', 'string', 'active' ) );
        if ( $c && (string)$c->attribute( 'status' ) !== (string)$status )
            $c->setStatus( $status );
        return self::ok( expCommerceExport::currency( eZCurrencyData::fetch( $code ) ) );
    }

    public static function update( array $args )
    {
        self::guard( 'update' );
        $c = self::fetchCurrency( self::post( 'code', 'string' ) );
        $changed = false;
        if ( ( $v = self::post( 'symbol', 'string', null ) ) !== null )
        {
            $c->setAttribute( 'symbol', $v );
            $changed = true;
        }
        if ( ( $v = self::post( 'locale', 'string', null ) ) !== null )
        {
            $c->setAttribute( 'locale', $v );
            $changed = true;
        }
        if ( ( $v = self::post( 'custom_rate', 'string', null ) ) !== null )
        {
            $c->setAttribute( 'custom_rate_value', self::rateNum( $v, 'custom_rate' ) );
            $changed = true;
        }
        if ( ( $v = self::post( 'rate_factor', 'string', null ) ) !== null )
        {
            $c->setAttribute( 'rate_factor', self::rateNum( $v, 'rate_factor' ) );
            $changed = true;
        }
        if ( ( $v = self::post( 'status', 'string', null ) ) !== null )
        {
            $c->setAttribute( 'status', self::statusOf( $v ) );
            $changed = true;
        }
        if ( !$changed )
            throw new expServiceException( 'Nothing to change', 400 );
        $c->store();
        return self::ok( expCommerceExport::currency( eZCurrencyData::fetch( $c->attribute( 'code' ) ) ) );
    }

    public static function setStatus( array $args )
    {
        self::guard( 'setStatus' );
        $c = self::fetchCurrency( self::post( 'code', 'string' ) );
        $c->setStatus( self::statusOf( self::post( 'status', 'string' ) ) );
        $c->store();
        return self::ok( expCommerceExport::currency( eZCurrencyData::fetch( $c->attribute( 'code' ) ) ) );
    }

    public static function remove( array $args )
    {
        self::guard( 'remove' );
        $c = self::fetchCurrency( self::post( 'code', 'string' ) );
        eZShopFunctions::removeCurrency( array( $c->attribute( 'code' ) ) );
        return self::ok( array( 'removed' => $c->attribute( 'code' ) ) );
    }

    public static function updateRates( array $args )
    {
        self::guard( 'updateRates' );
        eZShopFunctions::updateAutoRates();
        return self::ok( array( 'updated' => true ) );
    }

    public static function updateAutoprices( array $args )
    {
        self::guard( 'updateAutoprices' );
        eZShopFunctions::updateAutoprices();
        return self::ok( array( 'updated' => true ) );
    }

    public static function providers( array $args )
    {
        self::guard( 'providers' );
        $ini = eZINI::instance( 'shop.ini' );
        return self::ok( array( 'exchange' => $ini->hasGroup( 'ExchangeRatesSettings' ) ? $ini->group( 'ExchangeRatesSettings' ) : array(),
            'ecb_server' => $ini->hasVariable( 'ECBExchangeRatesSettings', 'ServerName' ) ? $ini->variable( 'ECBExchangeRatesSettings', 'ServerName' ) : null ) );
    }
}
