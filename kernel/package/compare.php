<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * package/compare/<PackageName>: what the package carries compared with the site's content tree and
 * classes, paginated and filtered, with a side by side word level difference of the item opened.
 * Read-only (eZPackageComparison): nothing is installed or stored; "Compare again" only rebuilds
 * the comparison's own cache. The state is in view parameters, like the contents browser on
 * package/view/full:
 *   package/compare/<name>/(filter)/changed/(class)/slash_quote/(search)/abc/(limit)/100/(offset)/200/(item)/17
 * each left out at its default (every status, every class, no search, 50 per page, offset 0,
 * nothing opened). The search is written encoded twice, as on package/view/full, because the kernel
 * URL-decodes the whole path before it splits it.
 */

$module = $Params['Module'];
$packageName = $Params['PackageName'];

$package = eZPackage::fetch( $packageName );
if ( !is_object( $package ) )
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
if ( !$package->attribute( 'can_read' ) )
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

$http = eZHTTPTool::instance();
$userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
$statuses = eZPackageComparison::statuses();
$limitChoices = array( '25', '50', '100', '250' );

$normalise = function ( $filter, $class, $search, $limit, $offset, $item ) use ( $statuses, $limitChoices )
{
    return array(
        'filter' => in_array( (string)$filter, $statuses, true ) ? (string)$filter : '',
        'class' => preg_match( '/^[A-Za-z0-9_]+$/', (string)$class ) ? (string)$class : '',
        'search' => trim( (string)$search ),
        'limit' => in_array( (string)$limit, $limitChoices, true ) ? (string)$limit : '50',
        'offset' => (string)$offset === 'last' ? 'last' : ( ctype_digit( (string)$offset ) ? (int)$offset : 0 ),
        'item' => ctype_digit( (string)$item ) ? (int)$item : -1,
    );
};
// The view's path for a state; only what differs from the defaults is written
$compareURL = function ( array $state ) use ( $packageName )
{
    $url = '/package/compare/' . $packageName;
    if ( $state['filter'] !== '' )
        $url .= '/(filter)/' . $state['filter'];
    if ( $state['class'] !== '' )
        $url .= '/(class)/' . $state['class'];
    if ( $state['search'] !== '' )
        $url .= '/(search)/' . rawurlencode( rawurlencode( $state['search'] ) );
    if ( $state['limit'] !== '50' )
        $url .= '/(limit)/' . $state['limit'];
    if ( $state['offset'] === 'last' || $state['offset'] > 0 )
        $url .= '/(offset)/' . $state['offset'];
    if ( $state['item'] >= 0 )
        $url .= '/(item)/' . $state['item'];
    return $url;
};
// Not $module->redirectTo(): the kernel adds the request's query string to every module redirect,
// and the filter form's own fields would bring the next request straight back here
$redirect = function ( $target )
{
    eZURI::transformURI( $target, false, 'full' );
    eZHTTPTool::redirect( $target, array(), '302 Found', false );
    eZExecution::cleanExit();
};

$state = $normalise(
    isset( $userParameters['filter'] ) ? $userParameters['filter'] : '',
    isset( $userParameters['class'] ) ? $userParameters['class'] : '',
    isset( $userParameters['search'] ) ? rawurldecode( $userParameters['search'] ) : '',
    isset( $userParameters['limit'] ) ? $userParameters['limit'] : '50',
    isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0,
    isset( $userParameters['item'] ) ? $userParameters['item'] : -1
);

// The filter form submits GET fields; they are answered with one redirect to the same state as view
// parameters, a changed filter starting on the first page with nothing opened
if ( $http->hasGetVariable( 'CompareApply' ) )
{
    $get = function ( $name, $fallback ) use ( $http ) { return $http->hasGetVariable( $name ) ? $http->getVariable( $name ) : $fallback; };
    $redirect( $compareURL( $normalise( $get( 'CompareFilter', $state['filter'] ), $get( 'CompareClass', '' ), $get( 'CompareSearch', '' ),
                                        $get( 'CompareLimit', '50' ), 0, -1 ) ) );
}

// "Compare again": the comparison built anew, then the same page without the item opened
if ( $module->isCurrentAction( 'Refresh' ) )
{
    eZPackageComparison::cachedIndex( $package, true );
    $redirect( $compareURL( array( 'item' => -1 ) + $state ) );
}

$index = eZPackageComparison::cachedIndex( $package );
$page = eZPackageComparison::filteredPage( $index, array(
    'status' => $state['filter'], 'class' => $state['class'], 'search' => $state['search'],
    'offset' => $state['offset'], 'limit' => (int)$state['limit'],
) );

$statusLabels = array(
    'new' => ezpI18n::tr( 'design/admin/package', 'New' ),
    'changed' => ezpI18n::tr( 'design/admin/package', 'Changed' ),
    'removed' => ezpI18n::tr( 'design/admin/package', 'Only on the site' ),
    'identical' => ezpI18n::tr( 'design/admin/package', 'Identical' ),
    'class_missing' => ezpI18n::tr( 'design/admin/package', 'Class missing' ),
);
$statusHints = array(
    'new' => ezpI18n::tr( 'design/admin/package', 'In the package, not on the site: installing creates it' ),
    'changed' => ezpI18n::tr( 'design/admin/package', 'On both, with differences' ),
    'removed' => ezpI18n::tr( 'design/admin/package', 'On the site under the package\'s top node, not in the package' ),
    'identical' => ezpI18n::tr( 'design/admin/package', 'On both, the same' ),
    'class_missing' => ezpI18n::tr( 'design/admin/package', 'Not on the site, and the site has no class for it' ),
);

// A short account of an item's differences, for the list
$summaryOf = function ( array $item ) use ( $statusLabels )
{
    $parts = array();
    if ( $item['kind'] === 'class' )
    {
        if ( $item['status'] === 'new' )
            return ezpI18n::tr( 'design/admin/package', 'Class not on the site: installing creates it' );
        $counts = $item['attributes'];
        if ( $counts['added'] )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count attribute(s) only in the package', null, array( '%count' => $counts['added'] ) );
        if ( $counts['site_only'] )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count attribute(s) only on the site', null, array( '%count' => $counts['site_only'] ) );
        if ( $counts['datatype'] )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count datatype(s) changed', null, array( '%count' => $counts['datatype'] ) );
        if ( $counts['names'] )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count attribute name(s) changed', null, array( '%count' => $counts['names'] ) );
        if ( $counts['other'] )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count attribute(s) with other changes', null, array( '%count' => $counts['other'] ) );
        $propertyChanges = $item['fields'] - array_sum( $counts );
        if ( $propertyChanges > 0 )
            $parts[] = ezpI18n::tr( 'design/admin/package', '%count class setting(s) changed', null, array( '%count' => $propertyChanges ) );
        return implode( ', ', $parts );
    }
    switch ( $item['status'] )
    {
        case 'new':
            return ezpI18n::tr( 'design/admin/package', 'Not on the site: installing creates it' );
        case 'removed':
            return ezpI18n::tr( 'design/admin/package', 'Only on the site' );
        case 'class_missing':
            return ezpI18n::tr( 'design/admin/package', 'The site has no class "%class"', null, array( '%class' => $item['class_identifier'] ) );
        case 'identical':
            return '';
    }
    if ( $item['fields'] )
        $parts[] = ezpI18n::tr( 'design/admin/package', '%count value(s) differ', null, array( '%count' => $item['fields'] ) );
    if ( $item['lang_package'] )
        $parts[] = ezpI18n::tr( 'design/admin/package', '%count translation(s) only in the package', null, array( '%count' => $item['lang_package'] ) );
    if ( $item['lang_site'] )
        $parts[] = ezpI18n::tr( 'design/admin/package', '%count translation(s) only on the site', null, array( '%count' => $item['lang_site'] ) );
    if ( $item['placement'] )
        $parts[] = ezpI18n::tr( 'design/admin/package', 'placement differs' );
    if ( $item['class_changed'] )
        $parts[] = ezpI18n::tr( 'design/admin/package', 'class differs' );
    return implode( ', ', $parts );
};

$limitInt = (int)$page['limit'];
$here = array( 'filter' => $state['filter'], 'class' => $state['class'], 'search' => $state['search'],
               'limit' => $state['limit'], 'offset' => (int)$page['offset'], 'item' => -1 );

$items = array();
foreach ( $page['items'] as $item )
{
    $item['summary'] = $summaryOf( $item );
    $item['url_view'] = $compareURL( array( 'item' => (int)$item['index'] ) + $here );
    $items[] = $item;
}

$chips = array( array( 'status' => '', 'label' => ezpI18n::tr( 'design/admin/package', 'All' ), 'hint' => '',
                       'count' => array_sum( $page['counts'] ), 'url' => $compareURL( array( 'filter' => '', 'offset' => 0 ) + $here ),
                       'active' => $state['filter'] === '' ) );
foreach ( $statuses as $status )
{
    $chips[] = array( 'status' => $status, 'label' => $statusLabels[$status], 'hint' => $statusHints[$status],
                      'count' => $page['counts'][$status],
                      'url' => $compareURL( array( 'filter' => $state['filter'] === $status ? '' : $status, 'offset' => 0 ) + $here ),
                      'active' => $state['filter'] === $status );
}

// The item opened: compared again in full, now
$viewed = null;
$viewedDetail = null;
if ( $state['item'] >= 0 && isset( $index['items'][$state['item']] ) )
{
    $viewed = $index['items'][$state['item']];
    $viewed['summary'] = $summaryOf( $viewed );
    $viewedDetail = eZPackageComparison::itemDetail( $package, $viewed );
    // The item's own file in the contents browser on package/view/full
    $viewed['url_file'] = false;
    if ( $viewed['file'] !== null )
    {
        foreach ( eZPackageFileBrowser::allFiles( $package ) as $fileIndex => $fileRow )
        {
            if ( $fileRow['path'] === $viewed['file'] )
            {
                $viewed['url_file'] = '/package/view/full/' . $packageName . '/(file)/' . $fileIndex;
                break;
            }
        }
    }
    $viewed['url_site'] = false;
    if ( $viewedDetail && $viewedDetail['site'] )
    {
        if ( $viewed['kind'] === 'class' )
            $viewed['url_site'] = '/class/view/' . (int)$viewedDetail['site']['id'];
        elseif ( !empty( $viewedDetail['site']['node_id'] ) )
            $viewed['url_site'] = '/content/view/full/' . (int)$viewedDetail['site']['node_id'];
    }
    if ( $viewedDetail )
    {
        // Per section: the values that differ, and how many are the same (shown folded)
        foreach ( $viewedDetail['sections'] as &$section )
        {
            $section['differing'] = array();
            $section['same'] = array();
            foreach ( $section['rows'] as $row )
            {
                if ( $row['state'] === 'identical' )
                    $section['same'][] = $row;
                else
                    $section['differing'][] = $row;
            }
        }
        unset( $section );
    }
}

$Compare = array(
    'items' => $items,
    'total_all' => $page['total_all'],
    'total_filtered' => $page['total_filtered'],
    'offset' => $page['offset'],
    'limit' => (string)$page['limit'],
    'page' => $page['page'],
    'pages' => $page['pages'],
    'counts' => $page['counts'],
    'classes' => $page['classes'],
    'chips' => $chips,
    'status_labels' => $statusLabels,
    'filter' => $state['filter'],
    'class' => $state['class'],
    'search' => $state['search'],
    'built' => $index['built'],
    'build_seconds' => $index['build_seconds'],
    'from_cache' => $index['from_cache'],
    'object_count' => $index['object_count'],
    'class_count' => $index['class_count'],
    'viewed' => $viewed,
    'viewed_index' => $viewed ? (int)$viewed['index'] : -1,
    'detail' => $viewedDetail,
    'url_here' => $compareURL( $here + array() ),
    'url_base' => '/package/compare/' . $packageName,
    'url_close' => $compareURL( $here ),
    'url_first' => $compareURL( array( 'offset' => 0 ) + $here ),
    'url_prev' => $compareURL( array( 'offset' => max( 0, $page['offset'] - $limitInt ) ) + $here ),
    'url_next' => $compareURL( array( 'offset' => $page['offset'] + $limitInt ) + $here ),
    'url_last' => $compareURL( array( 'offset' => 'last' ) + $here ),
    'url_refresh' => $compareURL( array( 'item' => $state['item'] ) + $here ),
);

$tpl = eZTemplate::factory();
$tpl->setVariable( 'package', $package );
$tpl->setVariable( 'Compare', $Compare );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:package/compare.tpl' );
$Result['path'] = array(
    array( 'url' => 'package/list', 'text' => ezpI18n::tr( 'kernel/package', 'Packages' ) ),
    array( 'url' => 'package/view/full/' . $packageName, 'text' => $package->attribute( 'name' ) ),
    array( 'url' => false, 'text' => ezpI18n::tr( 'design/admin/package', 'Compare' ) ),
);

?>
