<?php
/**
 * File containing the eZSiteData class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 55029bd473, a679a13176 and 94b7b6c239),
 * Copyright (C) Mugo Web, GPL v2 (site data read from templates and listed in the admin).
 */

/**
 * eZPersistentObject implementation for ezsite_data table.
 * Enables you to store and fetch key/value pairs
 *
 * Class eZSiteData
 */
class eZSiteData extends eZPersistentObject
{
    /**
     * Schema definition
     * @see kernel/classes/ezpersistentobject.php
     * @return array
     */
    public static function definition()
    {
        return array( 'fields'       => array( 'name'               => array( 'name'     => 'name',
                                                                              'datatype' => 'string',
                                                                              'default'  => null,
                                                                              'required' => true ),

                                               'value'             => array( 'name'     => 'value',
                                                                             'datatype' => 'string',
                                                                             'default'  => null,
                                                                             'required' => true ),

                                            ),

                      'keys'                 => array( 'name' ),
                      'class_name'           => 'eZSiteData',
                      'name'                 => 'ezsite_data',
                      'function_attributes'  => array()
        );
    }

    /**
     * Constructs a new eZSiteData instance. You need to call 'store()'
     * in order to store it into the DB.
     *
     * @param string $key
     * @param string $value
     * @return eZSiteData
     */
    public static function create( $name, $value )
    {
        return new eZSiteData( array(
            'name' => $name,
            'value' => $value
        ) );
    }

    /**
     * Creates a row and stores it at once (create() followed by store()); a row of that name is replaced.
     *
     * @param string $name
     * @param string $value
     * @return eZSiteData
     */
    public static function createNew( $name, $value )
    {
        $siteData = self::create( $name, $value );
        $siteData->store();
        return $siteData;
    }

    /**
     * Fetches a site data by name
     *
     * @param string $name
     * @return eZPersistentObject
     */
    public static function fetchByName( $name )
    {
        $result = parent::fetchObject( self::definition(), null, array( 'name' => $name ) );
        return $result;
    }

    /**
     * The names templates may read with fetch( 'content', 'site_data', hash( 'name', ... ) ):
     * site.ini [SiteDataSettings] TemplateReadable[], whole names. Empty unless a site lists some.
     * Read from the settings each time (no state kept between requests).
     *
     * @return string[]
     */
    public static function templateReadableNames()
    {
        $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'SiteDataSettings', 'TemplateReadable' ) )
            return array();
        $names = array();
        foreach ( (array)$ini->variable( 'SiteDataSettings', 'TemplateReadable' ) as $name )
        {
            $name = trim( (string)$name );
            if ( $name !== '' )
                $names[$name] = $name;
        }
        return array_values( $names );
    }

    /**
     * Whether templates may read the row $name (it is listed in [SiteDataSettings] TemplateReadable[]).
     *
     * @param string $name
     * @return bool
     */
    public static function isTemplateReadable( $name )
    {
        return is_string( $name ) && $name !== '' && in_array( $name, self::templateReadableNames(), true );
    }

    /**
     * The rows for a list, by name, optionally filtered: $filter is a whole name, or a pattern with * for any
     * characters (version*, *-release).
     *
     * @param string $filter
     * @param int $offset
     * @param int $limit
     * @return eZSiteData[]
     */
    public static function fetchList( $filter = '', $offset = 0, $limit = 50 )
    {
        return parent::fetchObjectList( self::definition(), null, self::filterConditions( $filter ), array( 'name' => 'asc' ),
                                        array( 'offset' => max( 0, (int)$offset ), 'limit' => max( 1, (int)$limit ) ) );
    }

    /**
     * How many rows fetchList() finds for $filter, without the paging.
     *
     * @param string $filter
     * @return int
     */
    public static function fetchListCount( $filter = '' )
    {
        return (int)parent::count( self::definition(), self::filterConditions( $filter ) );
    }

    /**
     * @param string $filter
     * @return array|null
     */
    protected static function filterConditions( $filter )
    {
        $filter = trim( (string)$filter );
        if ( $filter === '' )
            return null;
        if ( strpos( $filter, '*' ) === false )
            return array( 'name' => $filter );
        // LIKE has no escape character every engine shares, so a % or _ typed in the filter matches as LIKE says
        return array( 'name' => array( 'like', str_replace( '*', '%', $filter ) ) );
    }

}

?>
