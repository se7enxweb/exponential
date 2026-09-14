<?php
//
// eZSetup - init part initialization
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

$Module = $Params['Module'];


$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'NewExportButton' ) )
{
    return $Module->run( 'edit_export', array() );
}
else if ( $http->hasPostVariable( 'RemoveExportButton' ) && $http->hasPostVariable( 'DeleteIDArray' ) )
{
    $deleteArray = $http->postVariable( 'DeleteIDArray' );
    foreach ( $deleteArray as $deleteID )
    {
        $rssExport = eZRSSExport::fetch( $deleteID, true, eZRSSExport::STATUS_DRAFT );
        if ( $rssExport )
        {
            $rssExport->remove();
        }
        $rssExport = eZRSSExport::fetch( $deleteID, true, eZRSSExport::STATUS_VALID );
        if ( $rssExport )
        {
            $rssExport->remove();
        }
    }
}
else if ( $http->hasPostVariable( 'NewImportButton' ) )
{
    return $Module->run( 'edit_import', array() );
}
else if ( $http->hasPostVariable( 'RemoveImportButton' ) && $http->hasPostVariable( 'DeleteIDArrayImport' ) )
{
    $deleteArray = $http->postVariable( 'DeleteIDArrayImport' );
    foreach ( $deleteArray as $deleteID )
    {
        $rssImport = eZRSSImport::fetch( $deleteID, true, eZRSSImport::STATUS_DRAFT );
        if ( $rssImport )
        {
            $rssImport->remove();
        }
        $rssImport = eZRSSImport::fetch( $deleteID, true, eZRSSImport::STATUS_VALID );
        if ( $rssImport )
        {
            $rssImport->remove();
        }
    }
}


// ---------------------------------------------------------------- paging ---
//
// The lists are fetched a page at a time. Asking for every row and showing
// twenty five of them costs the same whether there are ten feeds or ten
// thousand, and at ten thousand it is the difference between a page that opens
// and one that does not.

require_once 'kernel/rss/ezrsslistpager.php';

$user = eZUser::currentUser();
$remembered = $user->isLoggedIn() ? eZPreferences::value( 'admin_rss_list_limit' ) : false;
$limit = eZRSSListPager::limit( isset( $Params['Limit'] ) ? $Params['Limit'] : false, $remembered );

// Remember a size the user picked, so the next visit opens the way they left it.
if ( $user->isLoggedIn() && isset( $Params['Limit'] ) && $Params['Limit'] !== false
     && (string) $limit === (string) (int) $Params['Limit'] && (string) $remembered !== (string) $limit )
{
    eZPreferences::setValue( 'admin_rss_list_limit', $limit );
}

$exportCount = eZRSSExport::fetchListCount();
$importCount = eZRSSImport::fetchListCount();

// Removing the last rows of the last page would otherwise leave it empty.
$exportOffset = eZRSSListPager::offset( isset( $Params['Offset'] ) ? $Params['Offset'] : 0,
                                        $limit, $exportCount );
$importOffset = eZRSSListPager::offset( isset( $Params['ImportOffset'] ) ? $Params['ImportOffset'] : 0,
                                        $limit, $importCount );

// Which column each list is ordered by. A column the list does not offer is
// ignored, so nothing but a real field of the table reaches the order clause.
$exportSort = eZRSSListPager::sort( isset( $Params['Sort'] ) ? $Params['Sort'] : false,
                                    isset( $Params['Dir'] ) ? $Params['Dir'] : false,
                                    eZRSSExport::sortableFields(), 'title' );

$importSort = eZRSSListPager::sort( isset( $Params['ImportSort'] ) ? $Params['ImportSort'] : false,
                                    isset( $Params['ImportDir'] ) ? $Params['ImportDir'] : false,
                                    eZRSSImport::sortableFields(), 'name' );

// Everything the page is currently showing. Each link writes the parameters it
// owns and carries the rest along, so moving one list never disturbs the other.
$state = array( 'limit'        => $limit,
                'offset'       => $exportOffset,
                'sort'         => $exportSort['field'],
                'dir'          => $exportSort['direction'],
                'importoffset' => $importOffset,
                'importsort'   => $importSort['field'],
                'importdir'    => $importSort['direction'] );

$exportPager = eZRSSListPager::data(
    $exportCount, $limit, $exportOffset, 'offset',
    eZRSSListPager::suffixExcept( $state, array( 'offset' ) ) );

$importPager = eZRSSListPager::data(
    $importCount, $limit, $importOffset, 'importoffset',
    eZRSSListPager::suffixExcept( $state, array( 'importoffset' ) ) );

// Sorting a list starts it again from the top: page nine of the old order has
// nothing to do with page nine of the new one.
$exportSort['suffix'] = eZRSSListPager::suffixExcept( $state, array( 'offset', 'sort', 'dir' ) );
$importSort['suffix'] = eZRSSListPager::suffixExcept( $state, array( 'importoffset', 'importsort', 'importdir' ) );

// Changing the page size starts both lists again from the top - page 40 of 25
// is not page 40 of 250, and landing past the end of a shorter list is worse
// than landing at the beginning - but it keeps whatever order they are in.
$limitLinks = array();
foreach ( eZRSSListPager::limits() as $option )
{
    $sizeState = $state;
    $sizeState['limit'] = $option;
    $limitLinks[] = array( 'limit'   => $option,
                           'suffix'  => eZRSSListPager::suffixExcept( $sizeState,
                                            array( 'offset', 'importoffset' ) ),
                           'current' => $option === $limit );
}

// One page of RSS exports.
$exportArray = eZRSSExport::fetchList( true, $exportPager['offset'], $limit, $exportSort['sorts'] );
$exportList = array();
foreach( $exportArray as $export )
{
    $exportList[$export->attribute( 'id' )] = $export;
}

// One page of RSS imports.
$importArray = eZRSSImport::fetchList( true, eZRSSImport::STATUS_VALID, $importPager['offset'], $limit,
                                       $importSort['sorts'] );
$importList = array();
foreach( $importArray as $import )
{
    $importList[$import->attribute( 'id' )] = $import;
}

$tpl = eZTemplate::factory();

$tpl->setVariable( 'rssexport_list', $exportList );
$tpl->setVariable( 'rssimport_list', $importList );
$tpl->setVariable( 'rssexport_count', $exportCount );
$tpl->setVariable( 'rssimport_count', $importCount );
$tpl->setVariable( 'rssexport_pager', $exportPager );
$tpl->setVariable( 'rssimport_pager', $importPager );
$tpl->setVariable( 'page_limit', $limit );
$tpl->setVariable( 'page_limit_links', $limitLinks );
$tpl->setVariable( 'rssexport_sort', $exportSort );
$tpl->setVariable( 'rssimport_sort', $importSort );

$Result = array();
$Result['content'] = $tpl->fetch( "design:rss/list.tpl" );
$Result['path'] = array( array( 'url' => 'rss/list',
                                'text' => ezpI18n::tr( 'kernel/rss', 'Really Simple Syndication' ) ) );


?>
