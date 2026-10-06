<?php
/**
 * File containing the oauthadmin/list view definition
 *
 * The REST applications (OAuth clients), with how many users authorized each and how many of its tokens are still
 * valid, and the figures of the personal API keys (their own page is oauthadmin/keys).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$tpl = eZTemplate::factory();
$module = $Params['Module'];

$session = ezcPersistentSessionInstance::get();

// Paged in the query rather than after it: an installation that hands out a
// REST application per integration has no ceiling on how many there are.
$pageLimit  = expAdminPagination::limit( 'oauthadmin/list' );
$pageOffset = expAdminPagination::offset( $Params );

$countQuery = $session->createFindQuery( 'ezpRestClient' );
$countQuery->where( $countQuery->expr->eq( 'version', ezpRestClient::STATUS_PUBLISHED ) );
$pageCount = count( $session->find( $countQuery, 'ezpRestClient' ) );

$q = $session->createFindQuery( 'ezpRestClient' );
$q->where( $q->expr->eq( 'version', ezpRestClient::STATUS_PUBLISHED ) )
  ->orderBy( 'name', ezcQuerySelect::ASC )
  ->limit( $pageLimit, $pageOffset );
$applications = $session->find( $q, 'ezpRestClient' );

// Per application: the users who authorized it and the tokens that have not expired. Two grouped queries for the
// whole page instead of two per row.
$now = time();
$db = eZDB::instance();
$authorizedBy = array();
$tokensBy = array();
$authorizedTotal = 0;
$tokensTotal = 0;
if ( $db->databaseName() !== 'mongo' )
{
    foreach ( (array)$db->arrayQuery( 'SELECT rest_client_id, COUNT(*) AS c FROM ezprest_authorized_clients GROUP BY rest_client_id' ) as $row )
    {
        $authorizedBy[(int)$row['rest_client_id']] = (int)$row['c'];
        $authorizedTotal += (int)$row['c'];
    }
    foreach ( (array)$db->arrayQuery( "SELECT client_id, COUNT(*) AS c FROM ezprest_token WHERE expirytime = 0 OR expirytime > $now GROUP BY client_id" ) as $row )
    {
        $tokensBy[(string)$row['client_id']] = (int)$row['c'];
        $tokensTotal += (int)$row['c'];
    }
}
$rows = array();
foreach ( $applications as $application )
{
    $rows[] = array( 'application' => $application,
                     'authorized' => isset( $authorizedBy[(int)$application->id] ) ? $authorizedBy[(int)$application->id] : 0,
                     'tokens' => isset( $tokensBy[(string)$application->client_id] ) ? $tokensBy[(string)$application->client_id] : 0 );
}

$tpl->setVariable( 'applications', $applications );
$tpl->setVariable( 'application_rows', $rows );
$tpl->setVariable( 'application_count', $pageCount );
$tpl->setVariable( 'authorized_total', $authorizedTotal );
$tpl->setVariable( 'tokens_total', $tokensTotal );
$tpl->setVariable( 'key_counts', class_exists( 'expApiKey' ) ? expApiKey::statusCounts() : false );
$tpl->setVariable( 'limit', $pageLimit );
$tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

$tpl->setVariable( 'module', $module );

$Result['path'] = array( array( 'url' => 'oauthadmin/list',
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'Registered REST applications' ) ) );

$Result['content'] = $tpl->fetch( 'design:oauthadmin/list.tpl' );

return $Result;
?>
