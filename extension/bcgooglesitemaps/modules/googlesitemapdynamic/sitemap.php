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

$Module = $Params['Module'];

if ( isset( $Params["NodeID"] ) ) {
    $NodeID = $Params["NodeID"];
}
else {
    $NodeID = 2;
}

$tpl = eZTemplate::factory();
$tpl->setVariable( "start_node_id", $NodeID );

header( 'Content-Type: text/xml' );

$Result = array();
$Result['content'] = $tpl->fetch( "design:googlesitemapdynamic/sitemap.tpl" );
$Result['pagelayout'] = 'googlesitemapdynamic_pagelayout.tpl';

?>