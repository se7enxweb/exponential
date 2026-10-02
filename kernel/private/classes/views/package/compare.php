<?php
/**
 * The code of kernel/package/compare.php, moved into a class (#207 stage 1). The file kernel/package/compare.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/package/compare.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Package
{

class Compare extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $packageName = $Params['PackageName'];

        $package = \eZPackage::fetch( $packageName );
        if ( !is_object( $package ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        if ( !$package->attribute( 'can_read' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );
        $canImport = (bool)$package->attribute( 'can_install' );

        $http = \eZHTTPTool::instance();
        $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
        $statuses = \eZPackageComparison::statuses();
        $sortFields = \eZPackageComparison::sortFields();
        $limitChoices = array( '25', '50', '100', '250' );

        $normalise = function ( array $raw ) use ( $statuses, $sortFields, $limitChoices )
        {
            $value = function ( $key, $fallback ) use ( $raw ) { return isset( $raw[$key] ) ? (string)$raw[$key] : $fallback; };
            $sort = $value( 'sort', '' );
            $offset = $value( 'offset', '0' );
            $item = $value( 'item', '-1' );
            return array(
                'filter' => in_array( $value( 'filter', '' ), $statuses, true ) ? $value( 'filter', '' ) : '',
                'class' => preg_match( '/^[A-Za-z0-9_]+$/', $value( 'class', '' ) ) ? $value( 'class', '' ) : '',
                'search' => trim( $value( 'search', '' ) ),
                'sort' => in_array( $sort, $sortFields, true ) ? $sort : '',
                'dir' => in_array( $sort, $sortFields, true ) && $value( 'dir', 'asc' ) === 'desc' ? 'desc' : 'asc',
                'limit' => in_array( $value( 'limit', '50' ), $limitChoices, true ) ? $value( 'limit', '50' ) : '50',
                'offset' => $offset === 'last' ? 'last' : ( ctype_digit( $offset ) ? (int)$offset : 0 ),
                'item' => ctype_digit( $item ) ? (int)$item : -1,
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
            if ( $state['sort'] !== '' )
                $url .= '/(sort)/' . $state['sort'] . '/(dir)/' . $state['dir'];
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
            \eZURI::transformURI( $target, false, 'full' );
            \eZHTTPTool::redirect( $target, array(), '302 Found', false );
            \eZExecution::cleanExit();
        };

        $rawState = $userParameters;
        if ( isset( $rawState['search'] ) )
            $rawState['search'] = rawurldecode( $rawState['search'] );
        $state = $normalise( $rawState );

        // The filter form submits GET fields; they are answered with one redirect to the same state as view
        // parameters, a changed filter starting on the first page with nothing opened (the sorting is kept)
        if ( $http->hasGetVariable( 'CompareApply' ) )
        {
            $get = function ( $name, $fallback ) use ( $http ) { return $http->hasGetVariable( $name ) ? $http->getVariable( $name ) : $fallback; };
            $redirect( $compareURL( $normalise( array(
                'filter' => $get( 'CompareFilter', $state['filter'] ), 'class' => $get( 'CompareClass', '' ), 'search' => $get( 'CompareSearch', '' ),
                'limit' => $get( 'CompareLimit', '50' ), 'sort' => $state['sort'], 'dir' => $state['dir'],
            ) ) ) );
        }

        // "Compare again": the comparison built anew, then the same page without the item opened
        if ( $module->isCurrentAction( 'Refresh' ) )
        {
            \eZPackageComparison::cachedIndex( $package, true );
            $redirect( $compareURL( array( 'item' => -1 ) + $state ) );
        }

        $index = \eZPackageComparison::cachedIndex( $package );
        $pageOptions = array(
            'status' => $state['filter'], 'class' => $state['class'], 'search' => $state['search'],
            'sort' => $state['sort'], 'dir' => $state['dir'], 'offset' => $state['offset'], 'limit' => (int)$state['limit'],
        );
        $page = \eZPackageComparison::filteredPage( $index, $pageOptions );

        // Every item of the current filter that an import is offered for, in the package's order
        $offeredInFilter = function ( array $index, array $page )
        {
            $out = array();
            foreach ( $page['filtered_indices'] as $i )
            {
                if ( \eZPackageComparisonImport::isOffered( $index['items'][$i], $index ) )
                    $out[] = $i;
            }
            sort( $out );
            return $out;
        };

        // ------------------------------------------------------------------ importing
        // The import actions are POSTs (the admin's form token guards them), each checked against the
        // install policy here, whatever the page showed
        $importActions = array( 'ImportItem', 'ImportSelected', 'ImportFilter', 'ImportViewed', 'ConfirmImport', 'CancelImport' );
        $Import = false;
        $importAction = false;
        foreach ( $importActions as $action )
        {
            if ( $module->isCurrentAction( $action ) )
                $importAction = $action;
        }
        if ( $importAction !== false && !$canImport )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        if ( $importAction === 'CancelImport' )
            $redirect( $compareURL( $state ) );

        if ( $importAction !== false )
        {
            $selected = array();
            $excluded = array();
            $mode = 'items';
            if ( $importAction === 'ImportItem' )
                $selected = array( (int)$http->postVariable( 'ImportItemButton' ) );
            elseif ( $importAction === 'ImportSelected' )
                $selected = array_map( 'intval', (array)( $http->hasPostVariable( 'SelectedItems' ) ? $http->postVariable( 'SelectedItems' ) : array() ) );
            elseif ( $importAction === 'ImportFilter' )
            {
                $mode = 'filter';
                $selected = array_slice( $offeredInFilter( $index, $page ), 0, \eZPackageComparisonImport::MAX_ITEMS );
            }
            elseif ( $importAction === 'ImportViewed' )
            {
                // The opened item, less the values the user unticked (every value offered was listed in
                // ImportAttributeShown, the ticked ones come back in ImportAttribute)
                $viewedIndex = (int)$http->postVariable( 'ImportViewedIndex' );
                $selected = array( $viewedIndex );
                $shown = (array)( $http->hasPostVariable( 'ImportAttributeShown' ) ? $http->postVariable( 'ImportAttributeShown' ) : array() );
                $ticked = (array)( $http->hasPostVariable( 'ImportAttribute' ) ? $http->postVariable( 'ImportAttribute' ) : array() );
                foreach ( array_diff( $shown, $ticked ) as $key )
                {
                    if ( preg_match( '#^[A-Za-z0-9_@-]+/[A-Za-z0-9_]+$#', (string)$key ) )
                        $excluded[$viewedIndex][(string)$key] = true;
                }
            }
            elseif ( $importAction === 'ConfirmImport' )
            {
                // What the confirmation listed: item indexes with the remote id each stood for then (an item
                // that is no longer the same one is left out), the items the user kept ticked
                // (ImportInclude), and per item the values offered (ImportValueShown, "<index>|<language>/<identifier>")
                // and those left ticked (ImportValue): every one shown and not ticked is kept as the site has it
                $mode = $http->hasPostVariable( 'ImportMode' ) && $http->postVariable( 'ImportMode' ) === 'filter' ? 'filter' : 'items';
                $remoteIDs = (array)( $http->hasPostVariable( 'ImportRemoteID' ) ? $http->postVariable( 'ImportRemoteID' ) : array() );
                $included = array_map( 'intval', (array)( $http->hasPostVariable( 'ImportInclude' ) ? $http->postVariable( 'ImportInclude' ) : array() ) );
                foreach ( $remoteIDs as $i => $remoteID )
                {
                    if ( in_array( (int)$i, $included, true ) && isset( $index['items'][(int)$i] ) && $index['items'][(int)$i]['remote_id'] === (string)$remoteID )
                        $selected[] = (int)$i;
                }
                $valueKeys = function ( $name ) use ( $http )
                {
                    $out = array();
                    foreach ( (array)( $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : array() ) as $value )
                    {
                        if ( preg_match( '#^(\d+)\|([A-Za-z0-9_@-]+/[A-Za-z0-9_]+)$#', (string)$value, $matches ) )
                            $out[(int)$matches[1] . '|' . $matches[2]] = array( (int)$matches[1], $matches[2] );
                    }
                    return $out;
                };
                $ticked = $valueKeys( 'ImportValue' );
                foreach ( $valueKeys( 'ImportValueShown' ) as $combined => $pair )
                {
                    if ( !isset( $ticked[$combined] ) )
                        $excluded[$pair[0]][$pair[1]] = true;
                }
                $leftOut = count( $remoteIDs ) - count( $selected );
            }
            $selected = array_values( array_unique( array_filter( $selected, function ( $i ) use ( $index ) { return isset( $index['items'][$i] ); } ) ) );
            sort( $selected );


            if ( $importAction === 'ConfirmImport' )
            {
                // Exactly the confirmed items: a class the confirmation added and the user left out stays out
                $result = $selected ? \eZPackageComparisonImport::run( $package, $index, $selected, $excluded, false ) : array();
                // The comparison as it is now (the imported items compared again)
                $index = \eZPackageComparison::cachedIndex( $package );
                $page = \eZPackageComparison::filteredPage( $index, $pageOptions );
                $remaining = $mode === 'filter' ? count( $offeredInFilter( $index, $page ) ) : 0;
                $Import = array( 'step' => 'result', 'mode' => $mode, 'entries' => $result, 'remaining' => $remaining, 'left_out' => $leftOut,
                                 'done' => count( array_filter( $result, function ( $e ) { return $e['result'] === 'done'; } ) ),
                                 'failed' => count( array_filter( $result, function ( $e ) { return $e['result'] === 'failed'; } ) ) );
            }
            else
            {
                $plan = \eZPackageComparisonImport::plan( $package, $index, $selected, $excluded );
                $Import = array( 'step' => 'confirm', 'mode' => $mode, 'entries' => $plan,
                                 'importable' => count( array_filter( $plan, function ( $e ) { return $e['importable']; } ) ),
                                 'filter_total' => $mode === 'filter' ? count( $offeredInFilter( $index, $page ) ) : 0 );
            }
            $Import['max'] = \eZPackageComparisonImport::MAX_ITEMS;
        }

        $statusLabels = array(
            'new' => \ezpI18n::tr( 'design/admin/package', 'New' ),
            'changed' => \ezpI18n::tr( 'design/admin/package', 'Changed' ),
            'removed' => \ezpI18n::tr( 'design/admin/package', 'Only on the site' ),
            'identical' => \ezpI18n::tr( 'design/admin/package', 'Identical' ),
            'class_missing' => \ezpI18n::tr( 'design/admin/package', 'Class missing' ),
        );
        $statusHints = array(
            'new' => \ezpI18n::tr( 'design/admin/package', 'In the package, not on the site: installing creates it' ),
            'changed' => \ezpI18n::tr( 'design/admin/package', 'On both, with differences' ),
            'removed' => \ezpI18n::tr( 'design/admin/package', 'On the site under the package\'s top node, not in the package' ),
            'identical' => \ezpI18n::tr( 'design/admin/package', 'On both, the same' ),
            'class_missing' => \ezpI18n::tr( 'design/admin/package', 'Not on the site, and the site has no class for it' ),
        );

        // A short account of an item's differences, for the list
        $summaryOf = function ( array $item ) use ( $statusLabels )
        {
            $parts = array();
            if ( $item['kind'] === 'class' )
            {
                if ( $item['status'] === 'new' )
                    return \ezpI18n::tr( 'design/admin/package', 'Class not on the site: installing creates it' );
                $counts = $item['attributes'];
                if ( $counts['added'] )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count attribute(s) only in the package', null, array( '%count' => $counts['added'] ) );
                if ( $counts['site_only'] )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count attribute(s) only on the site', null, array( '%count' => $counts['site_only'] ) );
                if ( $counts['datatype'] )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count datatype(s) changed', null, array( '%count' => $counts['datatype'] ) );
                if ( $counts['names'] )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count attribute name(s) changed', null, array( '%count' => $counts['names'] ) );
                if ( $counts['other'] )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count attribute(s) with other changes', null, array( '%count' => $counts['other'] ) );
                $propertyChanges = $item['fields'] - array_sum( $counts );
                if ( $propertyChanges > 0 )
                    $parts[] = \ezpI18n::tr( 'design/admin/package', '%count class setting(s) changed', null, array( '%count' => $propertyChanges ) );
                return implode( ', ', $parts );
            }
            switch ( $item['status'] )
            {
                case 'new':
                    return \ezpI18n::tr( 'design/admin/package', 'Not on the site: installing creates it' );
                case 'removed':
                    return \ezpI18n::tr( 'design/admin/package', 'Only on the site' );
                case 'class_missing':
                    return \ezpI18n::tr( 'design/admin/package', 'The site has no class "%class"', null, array( '%class' => $item['class_identifier'] ) );
                case 'identical':
                    return '';
            }
            if ( $item['fields'] )
                $parts[] = \ezpI18n::tr( 'design/admin/package', '%count value(s) differ', null, array( '%count' => $item['fields'] ) );
            if ( $item['lang_package'] )
                $parts[] = \ezpI18n::tr( 'design/admin/package', '%count translation(s) only in the package', null, array( '%count' => $item['lang_package'] ) );
            if ( $item['lang_site'] )
                $parts[] = \ezpI18n::tr( 'design/admin/package', '%count translation(s) only on the site', null, array( '%count' => $item['lang_site'] ) );
            if ( $item['placement'] )
                $parts[] = \ezpI18n::tr( 'design/admin/package', 'placement differs' );
            if ( $item['class_changed'] )
                $parts[] = \ezpI18n::tr( 'design/admin/package', 'class differs' );
            return implode( ', ', $parts );
        };

        $limitInt = (int)$page['limit'];
        $here = array( 'offset' => (int)$page['offset'], 'item' => -1 ) + $state;

        $items = array();
        foreach ( $page['items'] as $item )
        {
            $item['summary'] = $summaryOf( $item );
            $item['url_view'] = $compareURL( array( 'item' => (int)$item['index'] ) + $here );
            // Whether it is offered, why not, and the class it needs imported first
            $offer = \eZPackageComparisonImport::offerState( $item, $index );
            $item['offered'] = $canImport && $offer['offered'];
            $item['offer_reason'] = $offer['offered'] ? '' : $offer['reason'];
            $item['needs_class_name'] = $offer['needs_class'] !== null ? $offer['class_name'] : '';
            $item['blocked'] = $offer['blocked'];
            $item['difference_count'] = \eZPackageComparison::differenceCount( $item );
            $items[] = $item;
        }

        $chips = array( array( 'status' => '', 'label' => \ezpI18n::tr( 'design/admin/package', 'All' ), 'hint' => '',
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
        if ( $Import === false && $state['item'] >= 0 && isset( $index['items'][$state['item']] ) )
        {
            $viewed = $index['items'][$state['item']];
            $viewed['summary'] = $summaryOf( $viewed );
            $offer = \eZPackageComparisonImport::offerState( $viewed, $index );
            $viewed['offered'] = $canImport && $offer['offered'];
            $viewed['offer_reason'] = $offer['offered'] ? '' : $offer['reason'];
            $viewed['needs_class_name'] = $offer['needs_class'] !== null ? $offer['class_name'] : '';
            $viewed['blocked'] = $offer['blocked'];
            $viewedDetail = \eZPackageComparison::itemDetail( $package, $viewed );
            // The item's own file in the contents browser on package/view/full
            $viewed['url_file'] = false;
            if ( $viewed['file'] !== null )
            {
                foreach ( \eZPackageFileBrowser::allFiles( $package ) as $fileIndex => $fileRow )
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
                // Which values of an object may be unticked before importing it (see eZPackageComparisonImport::untickable())
                $untickable = $viewed['offered'] && $viewed['kind'] === 'object' && $viewed['status'] === 'changed'
                            ? \eZPackageComparisonImport::untickable( $package, $viewed ) : array();
                // Per section: the values that differ, and how many are the same (shown folded)
                foreach ( $viewedDetail['sections'] as &$section )
                {
                    $section['differing'] = array();
                    $section['same'] = array();
                    foreach ( $section['rows'] as $row )
                    {
                        $key = $section['language'] . '/' . $row['identifier'];
                        // An object's value the import sets (a class is imported as a whole)
                        // A value for an attribute the site's class lacks comes only with the class import (fixed tick)
                        $viaClass = $row['state'] === 'package_only' && $section['state'] === 'both' && $viewed['needs_class_name'] !== ''
                                  && in_array( $row['identifier'], (array)$viewed['missing_attributes'], true ) && !in_array( $row['identifier'], $viewed['blocked'], true );
                        $row['via_class'] = $viaClass;
                        $row['import_key'] = $viewed['offered'] && $viewed['kind'] === 'object' && $viewed['status'] === 'changed'
                                             && ( $row['state'] === 'changed' || ( $row['state'] === 'package_only' && $section['state'] === 'package' ) || $viaClass ) ? $key : '';
                        $row['untickable'] = $row['import_key'] !== '' && !empty( $untickable[$key] );
                        if ( $row['state'] === 'identical' )
                            $section['same'][] = $row;
                        else
                            $section['differing'][] = $row;
                    }
                }
                unset( $section );
            }
        }

        $offered = $canImport ? $offeredInFilter( $index, $page ) : array();
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
            'sort' => array( 'field' => $state['sort'], 'direction' => $state['dir'], 'opposite' => $state['dir'] === 'asc' ? 'desc' : 'asc' ),
            // parts/sortheader.tpl appends (sort)/<column>/(dir)/<direction> to this: the state without the
            // sorting and on the first page
            'sort_uri' => $compareURL( array( 'sort' => '', 'offset' => 0 ) + $here ),
            'built' => $index['built'],
            'build_seconds' => $index['build_seconds'],
            'from_cache' => $index['from_cache'],
            'object_count' => $index['object_count'],
            'class_count' => $index['class_count'],
            'can_import' => $canImport,
            'offered_in_filter' => count( $offered ),
            'import_max' => \eZPackageComparisonImport::MAX_ITEMS,
            'import' => $Import,
            // The confirmation stands alone; the result is shown above the list as it is now
            'show_list' => $Import === false || $Import['step'] === 'result',
            // The filter form and "Clear filters" keep the sorting
            'url_filter' => $compareURL( array( 'filter' => '', 'class' => '', 'search' => '', 'limit' => '50', 'offset' => 0, 'item' => -1 ) + $state ),
            'viewed' => $viewed,
            'viewed_index' => $viewed ? (int)$viewed['index'] : -1,
            'detail' => $viewedDetail,
            'url_here' => $compareURL( $here ),
            'url_base' => '/package/compare/' . $packageName,
            'url_close' => $compareURL( $here ),
            'url_first' => $compareURL( array( 'offset' => 0 ) + $here ),
            'url_prev' => $compareURL( array( 'offset' => max( 0, $page['offset'] - $limitInt ) ) + $here ),
            'url_next' => $compareURL( array( 'offset' => $page['offset'] + $limitInt ) + $here ),
            'url_last' => $compareURL( array( 'offset' => 'last' ) + $here ),
            'url_refresh' => $compareURL( array( 'item' => $state['item'] ) + $here ),
            // The import forms post to the page they are on; the opened item stays open when its own is used
            'url_post' => $compareURL( array( 'item' => $viewed ? (int)$viewed['index'] : -1 ) + $here ),
        );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'package', $package );
        $tpl->setVariable( 'Compare', $Compare );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:package/compare.tpl' );
        $Result['path'] = array(
            array( 'url' => 'package/list', 'text' => \ezpI18n::tr( 'kernel/package', 'Packages' ) ),
            array( 'url' => 'package/view/full/' . $packageName, 'text' => $package->attribute( 'name' ) ),
            array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/package', 'Compare' ) ),
        );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
