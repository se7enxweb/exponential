<?php
/**
 * File containing the bcgooglesitemaps siteaccess sitemap generator cronjob part
 *
 * @copyright Copyright (C) 1999 - 2014 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2008 MEDIATA Communications GmbH. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package bcgooglesitemaps
 */

$Module = array( "name" => "googlesitemapdynamic" );

$ViewList = array();
$ViewList['sitemap'] = array( 'functions' => array( 'sitemap' ),
                              'script' => 'sitemap.php',
                              'params' => array( 'NodeID' ) );

$FunctionList['sitemap'] = array( );

?>