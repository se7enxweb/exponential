<?php
/**
 * File containing the oauthadmin/list view definition
 *
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

$tpl->setVariable( 'applications', $session->find( $q, 'ezpRestClient' ) );
$tpl->setVariable( 'application_count', $pageCount );
$tpl->setVariable( 'limit', $pageLimit );
$tpl->setVariable( 'view_parameters', array( 'offset' => $pageOffset ) );

$tpl->setVariable( 'module', $module );

$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'oAuth admin' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/oauthadmin', 'Registered REST applications' ) ) );

$Result['content'] = $tpl->fetch( 'design:oauthadmin/list.tpl' );

return $Result;
?>
