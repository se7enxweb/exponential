<?php
/**
 * File containing the expUpgradeStepSiteVersion class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The version the database says it has (ezsite_data ezpublish-version), raised to 6.0.15 once the database steps
 * before it are in place, as the 6.0.15 database update does. Never lowered: a database at a later version is left
 * alone.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepSiteVersion extends expUpgradeBaseStep
{
    const VALUE = '6.0.15stable';

    public function id()
    {
        return '6.0.15-site-version';
    }

    public function stage()
    {
        return 'database';
    }

    public function description()
    {
        return 'The database version (ezsite_data ezpublish-version) is 6.0.15 or later';
    }

    /**
     * The stored version, '' when there is no row, null when it cannot be read.
     *
     * @param eZDBInterface $db
     * @return string|null
     */
    public static function stored( $db )
    {
        $rows = expKernelTableSchema::tryArrayQuery( $db, "SELECT value FROM ezsite_data WHERE name = 'ezpublish-version'" );
        if ( $rows === null )
            return null;
        if ( !$rows )
            return '';
        $row = array_change_key_case( (array)$rows[0], CASE_LOWER );
        return (string)$row['value'];
    }

    public function detect( expUpgradeContext $context )
    {
        $stored = self::stored( $context->db() );
        if ( $stored === null )
            return self::effect( self::UNKNOWN, 'cannot read ezsite_data' );
        if ( $stored !== '' && version_compare( self::number( $stored ), '6.0.15', '>=' ) )
            return self::effect( self::PRESENT, "the database says $stored" );
        return self::effect( self::MISSING, $stored === '' ? 'no version row' : "the database says $stored" );
    }

    /**
     * The number of a version string: 6.0.15stable -> 6.0.15.
     *
     * @param string $value
     * @return string
     */
    public static function number( $value )
    {
        return preg_match( '/^(\d+(?:\.\d+)*)/', (string)$value, $m ) ? $m[1] : '0';
    }

    public function plan( expUpgradeContext $context )
    {
        return array( 'set ezsite_data ezpublish-version to ' . self::VALUE );
    }

    public function apply( expUpgradeContext $context )
    {
        $db = $context->db();
        $stored = self::stored( $db );
        $db->begin();
        if ( $stored === '' )
            $db->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( 'ezpublish-version', '" . self::VALUE . "' )" );
        else
            $db->query( "UPDATE ezsite_data SET value = '" . self::VALUE . "' WHERE name = 'ezpublish-version'" );
        $db->query( "UPDATE ezsite_data SET value = '1' WHERE name = 'ezpublish-release'" );
        $db->commit();
        return array( 'ezpublish-version is ' . self::VALUE );
    }
}
