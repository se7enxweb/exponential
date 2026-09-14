<?php
/**
 * The RAD tools page: every point this system can be extended at, and the tool
 * for it where there is one.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$module = $Params['Module'];

require_once 'kernel/setup/expradcatalogue.php';

$tpl = eZTemplate::factory();

$filter = expRADCatalogue::filter( isset( $Params['Show'] ) ? $Params['Show'] : false );

// The groups, each carrying the points the filter leaves, so the template walks
// one list. A group with nothing left in it is left out rather than shown empty.
$groups = array();
$shown  = 0;

foreach ( expRADCatalogue::groups() as $key => $group )
{
    $points = array();
    foreach ( expRADCatalogue::pointsOf( $key ) as $pointKey => $point )
    {
        if ( !expRADCatalogue::matches( $point, $filter ) )
            continue;

        $points[] = array_merge( $point, array( 'key' => $pointKey ) );
    }

    if ( !count( $points ) )
        continue;

    $shown += count( $points );
    $groups[] = array_merge( $group, array( 'key' => $key, 'points' => $points ) );
}

// The filter controls, each knowing how many it would show.
$counts  = expRADCatalogue::filterCounts();
$filters = array();
foreach ( expRADCatalogue::filters() as $key => $label )
    $filters[] = array( 'key'     => $key,
                        'label'   => $label,
                        'count'   => isset( $counts[$key] ) ? $counts[$key] : 0,
                        'current' => $key === $filter,
                        'url'     => $key === 'all' ? '/setup/rad' : '/setup/rad/(show)/' . $key );

$tpl->setVariable( 'rad_groups', $groups );
$tpl->setVariable( 'rad_mechanisms', expRADCatalogue::mechanisms() );
$tpl->setVariable( 'rad_coverage', expRADCatalogue::coverage() );
$tpl->setVariable( 'rad_filters', $filters );
$tpl->setVariable( 'rad_filter', $filter );
$tpl->setVariable( 'rad_shown', $shown );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/rad.tpl' );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ) );
