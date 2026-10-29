<?php
/**
 * File containing the expOrderSiteaccess class.
 *
 * Which siteaccess (store or site) an order was placed in.
 *
 * Exponential serves many sites from one installation, and every one of them
 * can sell: a shop per country, a shop per brand, a site and its mobile twin.
 * The order kept no trace of which one it came from, so an order list mixed
 * them all and nobody could tell where an order was placed. ezorder.siteaccess
 * holds the name of the siteaccess the basket was turned into the order in;
 * this class records it, builds the filters the order lists use and names the
 * store for the customer.
 *
 * Orders placed before the column existed keep '' and are shown as unknown. An
 * order created on the command line records the siteaccess the script runs in
 * (its --siteaccess, or the configured default); one created through REST the
 * siteaccess REST runs in.
 *
 * Doc: doc/eng-us/features/6.0/order-siteaccess.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expOrderSiteaccess
{
    /** The longest name the column takes. */
    const MAX_LENGTH = 255;

    /** The value of the (siteaccess) view parameter that lists the orders without a recorded siteaccess. */
    const UNKNOWN_PARAMETER = '-';

    /** @var array siteaccess => store name, for the rest of the process */
    protected static $storeNames = array();

    /**
     * A siteaccess name as the column stores it: a string, trimmed, at most MAX_LENGTH long.
     *
     * @param mixed $name
     * @return string
     */
    public static function normalize( $name )
    {
        if ( !is_scalar( $name ) )
            return '';
        return substr( trim( (string)$name ), 0, self::MAX_LENGTH );
    }

    /**
     * The siteaccess the current request or script runs in, '' when none is known.
     *
     * @param array|null $access the current access as eZSiteAccess::current() gives it; null asks it
     * @return string
     */
    public static function current( $access = null )
    {
        if ( $access === null && class_exists( 'eZSiteAccess' ) )
            $access = eZSiteAccess::current();
        if ( !is_array( $access ) || !isset( $access['name'] ) )
            return '';
        return self::normalize( $access['name'] );
    }

    /**
     * The filter a (siteaccess) view parameter asks for.
     *
     * @param mixed $parameter
     * @return string|null null for no filter (every order), '' for the orders without a siteaccess, else the name
     */
    public static function filterFromParameter( $parameter )
    {
        if ( !is_scalar( $parameter ) )
            return null;
        $parameter = trim( (string)$parameter );
        if ( $parameter === self::UNKNOWN_PARAMETER )
            return '';
        // a siteaccess name is a word: letters, digits, _ - and .
        if ( $parameter === '' || strlen( $parameter ) > self::MAX_LENGTH || !preg_match( '/^[A-Za-z0-9_.\-]+$/', $parameter ) )
            return null;
        return $parameter;
    }

    /**
     * The (siteaccess) view parameter for a filter, the reverse of filterFromParameter().
     *
     * @param string|null $filter
     * @return string '' for no filter
     */
    public static function parameterFromFilter( $filter )
    {
        if ( $filter === null )
            return '';
        return $filter === '' ? self::UNKNOWN_PARAMETER : (string)$filter;
    }

    /**
     * The address part a list page keeps its filter in: '' for no filter, else "/(siteaccess)/<parameter>".
     *
     * @param string|null $filter
     * @return string
     */
    public static function uriSuffix( $filter )
    {
        $parameter = self::parameterFromFilter( $filter );
        return $parameter === '' ? '' : '/(siteaccess)/' . $parameter;
    }

    /**
     * The filter an order list shows: the choice of the filter form (sent with GET; '' is every order) when the
     * request has one, else the (siteaccess) view parameter the page's links carry. A module redirect keeps the
     * query string, so the form is answered directly rather than redirected to the address with the parameter.
     *
     * @param mixed $formValue the form's SiteaccessFilter, null when the request has none
     * @param mixed $viewParameter the (siteaccess) view parameter, null when the address has none
     * @return string|null see filterFromParameter()
     */
    public static function filterFromRequest( $formValue, $viewParameter )
    {
        return self::filterFromParameter( $formValue !== null ? $formValue : $viewParameter );
    }

    /**
     * What the template of an order list needs for its filter.
     *
     * @param string|null $filter
     * @param string[]|null $known the siteaccesses to offer; null for knownList()
     * @return array
     */
    public static function templateFilter( $filter, $known = null )
    {
        return array( 'parameter' => self::parameterFromFilter( $filter ),
                      'unknown'   => self::UNKNOWN_PARAMETER,
                      'suffix'    => self::uriSuffix( $filter ),
                      'list'      => $known === null ? self::knownList() : $known,
                      'enabled'   => eZOrder::hasSiteaccessColumn() );
    }

    /**
     * The SQL condition of a filter, to append to a WHERE clause: '' for no filter, else " AND <column> = '<name>'".
     * The orders without a siteaccess also match NULL: Oracle stores an empty string as NULL, so its column is
     * nullable (extension/ezoracle, settings/dbschema.ini.append.php) and holds NULL where the others hold ''.
     *
     * @param string|null $filter
     * @param eZDBInterface|null $db escapes the name; null for eZDB::instance()
     * @param string $column the column, with its table alias when the query has one
     * @return string
     */
    public static function sqlCondition( $filter, $db = null, $column = 'siteaccess' )
    {
        if ( $filter === null )
            return '';
        if ( $filter === '' )
            return ' AND ( ' . $column . " = '' OR " . $column . ' IS NULL )';
        if ( $db === null )
            $db = eZDB::instance();
        return ' AND ' . $column . " = '" . $db->escapeString( (string)$filter ) . "'";
    }

    /**
     * The conditions of a filter for eZPersistentObject::fetchObjectList() and the document store's $match.
     *
     * @param string|null $filter
     * @return array
     */
    public static function conditions( $filter )
    {
        return $filter === null ? array() : array( 'siteaccess' => (string)$filter );
    }

    /**
     * The siteaccesses the filter of the order lists offers: those configured (AvailableSiteAccessList) and those
     * orders were placed in, which may include a siteaccess removed since; sorted, without duplicates and without ''.
     *
     * @param array $configured
     * @param array $used
     * @return string[]
     */
    public static function mergeKnown( array $configured, array $used )
    {
        $names = array();
        foreach ( array_merge( $configured, $used ) as $name )
        {
            $name = self::normalize( $name );
            if ( $name !== '' )
                $names[$name] = $name;
        }
        ksort( $names, SORT_NATURAL | SORT_FLAG_CASE );
        return array_values( $names );
    }

    /**
     * The siteaccesses to offer in the filter of the order lists (see mergeKnown()).
     *
     * @return string[]
     */
    public static function knownList()
    {
        $ini = eZINI::instance();
        $configured = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
            ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        return self::mergeKnown( $configured, eZOrder::hasSiteaccessColumn() ? self::usedList() : array() );
    }

    /**
     * The siteaccesses placed orders were recorded in.
     *
     * @return string[]
     */
    public static function usedList()
    {
        $names = array();
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            foreach ( (array)$db->aggregate( 'ezorder', array( array( '$match' => array( 'is_temporary' => 0 ) ),
                                                              array( '$group' => array( '_id' => '$siteaccess' ) ) ) ) as $row )
                $names[] = isset( $row['_id'] ) ? (string)$row['_id'] : '';
        }
        else
        {
            foreach ( (array)$db->arrayQuery( "SELECT DISTINCT siteaccess FROM ezorder WHERE is_temporary = 0" ) as $row )
                $names[] = isset( $row['siteaccess'] ) ? (string)$row['siteaccess'] : '';
        }
        return $names;
    }

    /**
     * The name a customer knows the store by: the SiteName of the siteaccess's site.ini, or the siteaccess's name
     * when it names none or is no longer configured; '' for an order without a siteaccess.
     *
     * @param string $siteaccess
     * @return string
     */
    public static function storeName( $siteaccess )
    {
        $siteaccess = self::normalize( $siteaccess );
        if ( $siteaccess === '' )
            return '';
        if ( isset( self::$storeNames[$siteaccess] ) )
            return self::$storeNames[$siteaccess];

        $name = $siteaccess;
        try
        {
            $ini = eZINI::instance();
            $available = (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' );
            if ( in_array( $siteaccess, $available, true ) )
            {
                $siteIni = eZSiteAccess::getIni( $siteaccess, 'site.ini' );
                if ( $siteIni && $siteIni->hasVariable( 'SiteSettings', 'SiteName' ) )
                {
                    $siteName = trim( (string)$siteIni->variable( 'SiteSettings', 'SiteName' ) );
                    if ( $siteName !== '' )
                        $name = $siteName;
                }
            }
        }
        catch ( \Throwable $e )
        {
            eZDebug::writeWarning( $e->getMessage(), __METHOD__ );
        }
        return self::$storeNames[$siteaccess] = $name;
    }
}
