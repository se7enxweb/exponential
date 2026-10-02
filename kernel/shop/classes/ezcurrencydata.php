<?php
/**
 * File containing the eZCurrencyData class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZCurrencyData extends eZPersistentObject
{
    const DEFAULT_AUTO_RATE_VALUE = '0.0000';
    const DEFAULT_CUSTOM_RATE_VALUE = '0.0000';
    const DEFAULT_RATE_FACTOR_VALUE = '1.0000';

    const ERROR_OK = 0;
    const ERROR_UNKNOWN = 1;
    const ERROR_INVALID_CURRENCY_CODE = 2;
    const ERROR_CURRENCY_EXISTS = 3;

    const STATUS_ACTIVE = '1';
    const STATUS_INACTIVE = '2';

    public function __construct( $row )
    {
        parent::__construct( $row );
        $this->RateValue = false;
    }

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'code' => array( 'name' => 'Code',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'symbol' => array( 'name' => 'Symbol',
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => false ),
                                         'locale' => array( 'name' => 'Locale',
                                                            'datatype' => 'string',
                                                            'default' => '',
                                                            'required' => false ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'auto_rate_value' => array( 'name' => 'AutoRateValue',
                                                                'datatype' => 'string',
                                                                'default' => self::DEFAULT_AUTO_RATE_VALUE,
                                                                'required' => false ),
                                         'custom_rate_value' => array( 'name' => 'CustomRateValue',
                                                                  'datatype' => 'string',
                                                                  'default' => self::DEFAULT_CUSTOM_RATE_VALUE,
                                                                  'required' => false ),
                                         'rate_factor' => array( 'name' => 'RateFactor',
                                                                 'datatype' => 'string',
                                                                 'default' => self::DEFAULT_RATE_FACTOR_VALUE,
                                                                 'required' => false ) ),
                      'keys' => array( 'id' ),
                      'increment_key' => 'id',
                      'function_attributes' => array( 'rate_value' => 'rateValue' ),
                      'class_name' => "eZCurrencyData",
                      'sort' => array( 'code' => 'asc' ),
                      'name' => "ezcurrencydata" );
    }

    /*!
     \static
     \params codeList can be a single code like 'USD' or an array like array( 'USD', 'NOK' )
     or 'false' (means all currencies).
    */
    static function fetchList( $conditions = null, $asObjects = true, $offset = false, $limit = false, $asHash = true )
    {
        $currencyList = array();
        $sort = null;
        $limitation = null;
        if ( $offset !== false or $limit !== false )
            $limitation = array( 'offset' => $offset, 'length' => $limit );

        $rows = eZPersistentObject::fetchObjectList( eZCurrencyData::definition(),
                                                     null,
                                                     $conditions,
                                                     $sort,
                                                     $limitation,
                                                     $asObjects );

        if ( count( $rows ) > 0 )
        {
            if ( $asHash )
            {
                $keys = array_keys( $rows );
                foreach ( $keys as $key )
                {
                    if ( $asObjects )
                        $currencyList[$rows[$key]->attribute( 'code' )] = $rows[$key];
                    else
                        $currencyList[$rows[$key]['code']] = $rows[$key];
                }
            }
            else
            {
                $currencyList = $rows;
            }
        }

        return $currencyList;
    }

    /*!
     \static
    */
    static function fetchListCount( $conditions = null )
    {
        $rows = eZPersistentObject::fetchObjectList( eZCurrencyData::definition(),
                                                     array(),
                                                     $conditions,
                                                     false,
                                                     null,
                                                     false,
                                                     false,
                                                     array( array( 'operation' => 'count( * )',
                                                                   'name' => 'count' ) ) );
        return $rows[0]['count'];
    }

    /*!
     \static
    */
    static function fetch( $currencyCode, $asObject = true )
    {
        if ( $currencyCode )
        {
            $currency = eZCurrencyData::fetchList( array( 'code' => $currencyCode ), $asObject );
            if ( is_array( $currency ) && count( $currency ) > 0 )
                return $currency[$currencyCode];
        }

        return null;
    }

    /*!
     functional attribute
    */
    function rateValue()
    {
        if ( $this->RateValue === false )
        {
            /*
            $rateValue = '0.00000';
            if ( $this->attribute( 'custom_rate_value' ) > 0 )
            {
                $rateValue = $this->attribute( 'custom_rate_value' );
            }
            else
            {
                $rateValue = $this->attribute( 'auto_rate_value' );
                $rateValue = $rateValue * $this->attribute( 'rate_factor' );
                $rateValue = sprintf( "%7.5f", $rateValue );
            }
            */

            $rateValue = '0.00000';
            if ( $this->attribute( 'custom_rate_value' ) > 0 )
                $rateValue = $this->attribute( 'custom_rate_value' );
            else
                $rateValue = $this->attribute( 'auto_rate_value' );

            if ( $rateValue > 0 )
                $rateValue = $rateValue * $this->attribute( 'rate_factor' );

            $rateValue = sprintf( "%7.5f", $rateValue );

            $this->RateValue = $rateValue;
        }

        return $this->RateValue;
    }

    function invalidateRateValue()
    {
        $this->RateValue = false;
    }

    /*!
     \static
    */
    static function create( $code, $symbol, $locale, $autoRateValue, $customRateValue, $rateFactor, $status = self::STATUS_ACTIVE )
    {
        $code = strtoupper( $code );
        $errCode = eZCurrencyData::canCreate( $code );
        if ( $errCode === self::ERROR_OK )
        {
            $currency = new eZCurrencyData( array( 'code' => $code,
                                                   'symbol' => $symbol,
                                                   'locale' => $locale,
                                                   'status' => $status,
                                                   'auto_rate_value' => $autoRateValue,
                                                   'custom_rate_value' => $customRateValue,
                                                   'rate_factor' => $rateFactor ) );
            $currency->setHasDirtyData( true );
            return $currency;
        }

        return $errCode;
    }

    /*!
     \static
   */
    static function canCreate( $code )
    {
        $errCode = eZCurrencyData::validateCurrencyCode( $code );
        if ( $errCode === self::ERROR_OK && eZCurrencyData::currencyExists( $code ) )
            $errCode = self::ERROR_CURRENCY_EXISTS;

        return $errCode;
    }

    /*!
     \static
    */
    static function validateCurrencyCode( $code )
    {
        if ( !preg_match( "/^[A-Z]{3}$/", $code ) )
            return self::ERROR_INVALID_CURRENCY_CODE;

        return self::ERROR_OK;
    }

    /*!
     \static
    */
    static function currencyExists( $code )
    {
        return ( eZCurrencyData::fetch( $code ) !== null );
    }

    /*!
     \static
    */
    static function removeCurrencyList( $currencyCodeList )
    {
        if ( is_array( $currencyCodeList ) && count( $currencyCodeList ) > 0 )
        {
            $db = eZDB::instance();
            $db->begin();
                eZPersistentObject::removeObject( eZCurrencyData::definition(),
                                                  array( 'code' => array( $currencyCodeList ) ) );
            $db->commit();
            // Audit (doc/bc/6.0/audit.md, commerce.currency.change)
            if ( class_exists( 'expAuditHook' ) )
                expAuditHook::emit( 'commerce.currency.change', array( 'object' => array( 'type' => 'currency', 'id' => implode( ',', array_map( 'strval', $currencyCodeList ) ) ),
                    'verb' => 'remove', 'before' => array( 'codes' => array_values( array_map( 'strval', $currencyCodeList ) ) ) ) );
        }
    }

    function setStatus( $status )
    {
        $statusNumeric = eZCurrencyData::statusStringToNumeric( $status );
        if ( $statusNumeric !== false )
        {
            $this->setAttribute( 'status', $statusNumeric );
        }
        else
        {
            eZDebug::writeError( "Unknow currency's status '$status'", __METHOD__ );
        }
    }

    static function statusStringToNumeric( $statusString )
    {
        $status = false;
        if ( is_numeric( $statusString ) )
        {
            $status = $statusString;
        }
        if ( is_string( $statusString ) )
        {
            $statusString = strtoupper( $statusString );
            if ( defined( "self::STATUS_{$statusString}" ) )
                $status = constant( "self::STATUS_{$statusString}" );
        }

        return $status;
    }

    /*!
     \static
    */
    static function errorMessage( $errorCode )
    {
        switch ( $errorCode )
        {
            case self::ERROR_INVALID_CURRENCY_CODE:
                return ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Invalid characters in currency code.' );

            case self::ERROR_CURRENCY_EXISTS:
                return ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Currency already exists.' );

            case self::ERROR_UNKNOWN:
            default:
                return ezpI18n::tr( 'kernel/shop/classes/ezcurrencydata', 'Unknown error.' );
        }
    }

    function store( $fieldFilters = null )
    {
        // Audit (doc/bc/6.0/audit.md, commerce.currency.change): the stored row before
        $auditBefore = null;
        if ( class_exists( 'expAuditHook' ) && expAuditHook::on( 'commerce.currency.change' ) )
        {
            $row = eZPersistentObject::fetchObject( eZCurrencyData::definition(), null, array( 'code' => (string)$this->attribute( 'code' ) ), false );
            $auditBefore = is_array( $row ) ? $row : false;
        }
        // data changed => reset RateValue
        $this->invalidateRateValue();
        parent::store( $fieldFilters );
        if ( $auditBefore !== null )
        {
            $fields = array( 'symbol', 'locale', 'status', 'auto_rate_value', 'custom_rate_value', 'rate_factor' );
            $before = array();
            $after = array();
            foreach ( $fields as $field )
            {
                $new = (string)$this->attribute( $field );
                $old = $auditBefore === false ? null : (string)$auditBefore[$field];
                if ( $old !== $new && !( is_numeric( $old ) && is_numeric( $new ) && (float)$old == (float)$new ) )
                {
                    $before[$field] = $old;
                    $after[$field] = $new;
                }
            }
            if ( $after )
                expAuditHook::emit( 'commerce.currency.change', array( 'object' => array( 'type' => 'currency', 'id' => (string)$this->attribute( 'code' ) ),
                    'verb' => $auditBefore === false ? 'create' : 'change',
                    'before' => $auditBefore === false ? null : $before, 'after' => $after ) );
        }
    }

    function isActive()
    {
        return ( $this->attribute( 'status' ) == self::STATUS_ACTIVE );
    }

    public $RateValue;
}

?>
