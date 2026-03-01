<?php
/**
 * File containing the eZCollectExport ExportSylk Cronjob.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

// Settings
$ini = eZINI::instance( "cie.ini" );

$debug = $ini->variable( 'CieSettings', 'Debug' ) == 'enabled' ? true : false;
$collection = $ini->variable( "CieSettings", "Collection" );
$dir = $ini->variable( "CieSettings", "Directory" );
$format = $ini->variable( "CieSettings", "SylkFormat" );
$separator = $ini->variable( "CieSettings", "SylkSeparator" );
$limitedRange = $ini->variable( "CieSettings", "ExportLimitedRange" ) == 'enabled' ? true : false;
$removeExported = $ini->variable( "CieSettings", "RemoveExported" ) == 'enabled' ? true : false;

// Test range
if ( $limitedRange == true )
{
    $days = $ini->variable( "CieSettings", "DateRangeToExport" );
}
else
{
    $days = false;
}

// Export collections
exportCollections( $collection, $dir, $format, $separator, $days, $removeExported, $debug );

?>