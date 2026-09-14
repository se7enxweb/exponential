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

$valid = true;
$validationErrors = array();

if ( isset( $Params['RSSExportID'] ) )
    $RSSExportID = $Params['RSSExportID'];
else
    $RSSExportID = false;

if ( $http->hasPostVariable( 'RSSExport_ID' ) )
    $RSSExportID = $http->postVariable( 'RSSExport_ID' );

if ( $Module->isCurrentAction( 'Store' ) )
{

    $storeResult = eZRSSEditFunction::storeRSSExport( $Module, $http, true );

    if ( $storeResult['valid'] && $storeResult['published'] )
        return $Module->redirectTo( '/rss/list' );
    if ( !$storeResult['valid'] )
    {
        $valid = false;
        $validationErrors = $storeResult['validation_errors'];
    }
}
else if ( $Module->isCurrentAction( 'UpdateItem' ) )
{
    eZRSSEditFunction::storeRSSExport( $Module, $http );
}
else if ( $Module->isCurrentAction( 'AddItem' ) )
{
    $rssExportItem = eZRSSExportItem::create( $RSSExportID );
    $rssExportItem->store();
    eZRSSEditFunction::storeRSSExport( $Module, $http );
}
else if ( $Module->isCurrentAction( 'Cancel' ) )
{
    $rssExport = eZRSSExport::fetch( $RSSExportID, true, eZRSSExport::STATUS_DRAFT );
    if ( $rssExport )
        $rssExport->removeThis();
    return $Module->redirectTo( '/rss/list' );
}
else if ( $Module->isCurrentAction( 'BrowseImage' ) )
{
    eZRSSEditFunction::storeRSSExport( $Module, $http );
    eZContentBrowse::browse( array( 'action_name' => 'RSSExportImageBrowse',
                                    'description_template' => 'design:rss/browse_image.tpl',
                                    'from_page' => '/rss/edit_export/'. $RSSExportID .'/0/ImageSource' ),
                             $Module );
}
else if ( $Module->isCurrentAction( 'RemoveImage' ) )
{
    // The draft can have been collected while the page sat open; the block
    // below takes the export up again, so there is nothing to do here yet.
    $rssExport = eZRSSExport::fetch( $RSSExportID, true, eZRSSExport::STATUS_DRAFT );
    if ( $rssExport instanceof eZRSSExport )
    {
        $rssExport->setAttribute( 'image_id', 0 );
        $rssExport->store();
    }
}


if ( $http->hasPostVariable( 'Item_Count' ) )
{

    $db = eZDB::instance();
    $db->begin();
    for ( $itemCount = 0; $itemCount < $http->postVariable( 'Item_Count' ); $itemCount++ )
    {
        if ( $http->hasPostVariable( 'SourceBrowse_'.$itemCount ) )
        {
            $skipValues = $http->hasPostVariable( 'Ignore_Values_On_Browse_' . $itemCount ) && $http->postVariable( 'Ignore_Values_On_Browse_' . $itemCount );
            eZRSSEditFunction::storeRSSExport( $Module, $http, false, $skipValues ? $http->postVariable( 'Item_ID_'.$itemCount ) : null );//
            eZContentBrowse::browse( array( 'action_name' => 'RSSObjectBrowse',
                                            'description_template' => 'design:rss/browse_source.tpl',
                                            'from_page' => '/rss/edit_export/'. $RSSExportID .'/'. $http->postVariable( 'Item_ID_'.$itemCount ) .'/NodeSource' ),
                                     $Module );
            break;
        }

        // remove selected source (if any)
        if ( $http->hasPostVariable( 'RemoveSource_'.$itemCount ) )
        {
            $itemID = $http->postVariable( 'Item_ID_'.$itemCount );
            if ( ( $rssExportItem = eZRSSExportItem::fetch( $itemID, true, eZRSSExport::STATUS_DRAFT ) ) )
            {
                // remove the draft version
                $rssExportItem->remove();
                // remove the published version
                $rssExportItem->setAttribute( 'status', eZRSSExport::STATUS_VALID );
                $rssExportItem->remove();
                eZRSSEditFunction::storeRSSExport( $Module, $http );
            }

            break;
        }
    }
    $db->commit();
}

if ( is_numeric( $RSSExportID ) )
{
    $rssExportID = $RSSExportID;
    $rssExport = eZRSSExport::fetch( $RSSExportID, true, eZRSSExport::STATUS_DRAFT );

    if ( $rssExport )
    {
        $user = eZUser::currentUser();
        $contentIni = eZINI::instance( 'content.ini' );
        $timeOut = $contentIni->variable( 'RSSExportSettings', 'DraftTimeout' );
        if ( $rssExport->attribute( 'modifier_id' ) != $user->attribute( 'contentobject_id' ) &&
             $rssExport->attribute( 'modified' ) + $timeOut > time() )
        {
            // locked editing
            $tpl = eZTemplate::factory();

            $tpl->setVariable( 'rss_export', $rssExport );
            $tpl->setVariable( 'rss_export_id', $rssExportID );
            $tpl->setVariable( 'lock_timeout', $timeOut );

            $Result = array();
            $Result['content'] = $tpl->fetch( 'design:rss/edit_export_denied.tpl' );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'kernel/rss', 'Really Simple Syndication' ) ) );
            return $Result;
        }
        else if ( $timeOut > 0 && $rssExport->attribute( 'modified' ) + $timeOut < time() )
        {
            $rssExport->removeThis();
            $rssExport = false;
        }
    }
    if ( !$rssExport )
    {
        $rssExport = eZRSSExport::fetch( $RSSExportID, true, eZRSSExport::STATUS_VALID );
        if ( $rssExport )
        {
            $db = eZDB::instance();
            $db->begin();
            $rssItems = $rssExport->fetchItems();
            $rssExport->setAttribute( 'status', eZRSSExport::STATUS_DRAFT );
            $rssExport->store();
            foreach( $rssItems as $rssItem )
            {
                $rssItem->setAttribute( 'status', eZRSSExport::STATUS_DRAFT );
                $rssItem->store();
            }
            // An OPML export's outlines are copied rather than moved, so the
            // published document keeps working while the draft is edited.
            eZRSSExportOPMLItem::copyStatus( $RSSExportID,
                                             eZRSSExport::STATUS_VALID,
                                             eZRSSExport::STATUS_DRAFT );
            $db->commit();
        }
        else
        {
            return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        }
    }

    switch ( $Params['BrowseType'] )
    {
        case 'NodeSource':
        {
            $nodeIDArray = $http->hasPostVariable( 'SelectedNodeIDArray' ) ? $http->postVariable( 'SelectedNodeIDArray' ) : null;
            if ( isset( $nodeIDArray ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $rssExportItem = eZRSSExportItem::fetch( $Params['RSSExportItemID'], true, eZRSSExport::STATUS_DRAFT );
                $rssExportItem->setAttribute( 'source_node_id', $nodeIDArray[0] );

                if ( $rssExportItem->attribute('title') == '' && $rssExportItem->attribute('description') == '' )
                {
                    eZRSSEditFunction::setItemDefaults( $rssExportItem );
                }

                $rssExportItem->store();
            }
        } break;

        case 'OPMLNodeSource':
        {
            $nodeIDArray = $http->hasPostVariable( 'SelectedNodeIDArray' ) ? $http->postVariable( 'SelectedNodeIDArray' ) : null;
            if ( isset( $nodeIDArray ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $opmlItem = eZRSSExportOPMLItem::fetch( $Params['RSSExportItemID'], true, eZRSSExport::STATUS_DRAFT );
                if ( $opmlItem && (int) $opmlItem->attribute( 'rssexport_id' ) === (int) $RSSExportID )
                {
                    $opmlItem->setAttribute( 'source_node_id', (int) $nodeIDArray[0] );
                    $opmlItem->setAttribute( 'target_export_id', 0 );
                    // A node is something to link to, not a feed to subscribe
                    // to, unless the editor says otherwise afterwards.
                    if ( $opmlItem->attribute( 'outline_type' ) === 'rss' )
                        $opmlItem->setAttribute( 'outline_type', 'link' );
                    $opmlItem->store();
                }
            }
        } break;

        case 'ImageSource':
        {
            $imageNodeIDArray = $http->hasPostVariable( 'SelectedNodeIDArray' ) ? $http->postVariable( 'SelectedNodeIDArray' ) : null;
            if ( isset( $imageNodeIDArray ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $rssExport->setAttribute( 'image_id', $imageNodeIDArray[0] );
            }
        } break;
    }
}
else // New RSSExport
{
    $user = eZUser::currentUser();
    $user_id = $user->attribute( "contentobject_id" );


    $db = eZDB::instance();
    $db->begin();

    // Create default rssExport object to use
    $rssExport = eZRSSExport::create( $user_id );
    $rssExport->store();
    $rssExportID = $rssExport->attribute( 'id' );

    // Create One empty export item
    $rssExportItem = eZRSSExportItem::create( $rssExportID );
    $rssExportItem->store();

    $db->commit();
}

// ------------------------------------------------------------------ OPML ---
//
// An OPML export lists feeds rather than articles, so its half of this page is
// a browser over the other exports. Its controls are submit buttons rather than
// links, because they live inside the edit form: a link would leave the page
// and take everything typed into it with it. Each one therefore writes the
// draft first and then acts.

require_once 'kernel/rss/ezrsslistpager.php';

$isOPML = $rssExport->attribute( 'rss_version' ) === 'OPML'
          || ( $http->hasPostVariable( 'RSSVersion' ) && $http->postVariable( 'RSSVersion' ) === 'OPML' );

$opmlBrowserSearch = '';
$opmlBrowserPager  = null;
$opmlBrowserList   = array();
$opmlSelected      = array();

if ( $isOPML && is_numeric( $rssExportID ) )
{
    // Anything that moves the browser, or changes the outlines, saves what is
    // on the page first - otherwise paging the browser would throw away an
    // edit made just above it.
    $touched = false;
    foreach ( array( 'AddFeedsButton', 'RemoveOPMLItemsButton', 'AddOPMLGroupButton',
                     'FeedBrowserApply', 'FeedBrowserSetOffset', 'FeedBrowserSetSort',
                     'BrowseOPMLNode', 'ClearOPMLNode' ) as $control )
    {
        if ( $http->hasPostVariable( $control ) )
        {
            $touched = true;
            break;
        }
    }

    if ( $touched && $http->hasPostVariable( 'RSSExport_ID' ) )
    {
        eZRSSEditFunction::storeRSSExport( $Module, $http );
        $rssExport = eZRSSExport::fetch( $rssExportID, true, eZRSSExport::STATUS_DRAFT );
    }

    // Feeds ticked in the browser join the document.
    if ( $http->hasPostVariable( 'AddFeedsButton' ) && $http->hasPostVariable( 'FeedBrowserSelect' ) )
    {
        $chosen = $http->postVariable( 'FeedBrowserSelect' );
        if ( is_array( $chosen ) )
            eZRSSExportOPMLItem::addTargets( $rssExportID, $chosen, eZRSSExport::STATUS_DRAFT );
    }

    // Outlines ticked in the document leave it.
    if ( $http->hasPostVariable( 'RemoveOPMLItemsButton' ) && $http->hasPostVariable( 'OPMLItemRemove' ) )
    {
        $doomed = $http->postVariable( 'OPMLItemRemove' );
        if ( is_array( $doomed ) )
            eZRSSExportOPMLItem::removeItems( $rssExportID, $doomed );
    }

    // A folder to put feeds in. OPML nests outlines, and a reader shows that
    // nesting as groups.
    if ( $http->hasPostVariable( 'AddOPMLGroupButton' ) )
    {
        $group = eZRSSExportOPMLItem::create(
            $rssExportID, 0, eZRSSExportOPMLItem::nextPriority( $rssExportID, eZRSSExport::STATUS_DRAFT ) );
        $group->setAttribute( 'outline_type', 'group' );
        $group->setAttribute( 'outline_text', ezpI18n::tr( 'kernel/rss/edit_export', 'New group' ) );
        $group->setAttribute( 'status', eZRSSExport::STATUS_DRAFT );
        $group->store();
    }

    // An outline can be fed by content instead: the browse button carries the
    // row it belongs to as its value.
    if ( $http->hasPostVariable( 'BrowseOPMLNode' ) )
    {
        $opmlItemID = (int) $http->postVariable( 'BrowseOPMLNode' );
        $opmlItem = eZRSSExportOPMLItem::fetch( $opmlItemID, true, eZRSSExport::STATUS_DRAFT );
        if ( $opmlItem && (int) $opmlItem->attribute( 'rssexport_id' ) === (int) $rssExportID )
        {
            eZContentBrowse::browse( array( 'action_name' => 'RSSOPMLNodeBrowse',
                                            'description_template' => 'design:rss/browse_source.tpl',
                                            'from_page' => '/rss/edit_export/' . $rssExportID . '/' . $opmlItemID . '/OPMLNodeSource' ),
                                     $Module );
        }
    }

    if ( $http->hasPostVariable( 'ClearOPMLNode' ) )
    {
        $opmlItemID = (int) $http->postVariable( 'ClearOPMLNode' );
        $opmlItem = eZRSSExportOPMLItem::fetch( $opmlItemID, true, eZRSSExport::STATUS_DRAFT );
        if ( $opmlItem && (int) $opmlItem->attribute( 'rssexport_id' ) === (int) $rssExportID )
        {
            $opmlItem->setAttribute( 'source_node_id', 0 );
            $opmlItem->store();
        }
    }

    // Where the browser is looking. The same helper the rss list uses settles
    // the page size, the offset and the column, so a size or a column nobody
    // offers is ignored here too.
    $opmlBrowserSearch = $http->hasPostVariable( 'FeedBrowserSearch' )
                         ? trim( $http->postVariable( 'FeedBrowserSearch' ) ) : '';

    $browserLimit = eZRSSListPager::limit(
        $http->hasPostVariable( 'FeedBrowserLimit' ) ? $http->postVariable( 'FeedBrowserLimit' ) : false );

    $browserSortField = $http->hasPostVariable( 'FeedBrowserSort' ) ? $http->postVariable( 'FeedBrowserSort' ) : false;
    $browserSortDir   = $http->hasPostVariable( 'FeedBrowserDir' ) ? $http->postVariable( 'FeedBrowserDir' ) : false;

    // A heading press carries both halves in one value, so the browser needs
    // no javascript to send them together.
    if ( $http->hasPostVariable( 'FeedBrowserSetSort' ) )
    {
        $pressed = explode( '|', $http->postVariable( 'FeedBrowserSetSort' ) );
        $browserSortField = isset( $pressed[0] ) ? $pressed[0] : false;
        $browserSortDir   = isset( $pressed[1] ) ? $pressed[1] : 'asc';
    }

    $browserSort = eZRSSListPager::sort( $browserSortField, $browserSortDir,
                                         eZRSSExport::sortableFields(), 'title' );

    // Searching or sorting starts the browser again from the top; paging moves
    // it. Anything else leaves it where the hidden field says it was.
    if ( $http->hasPostVariable( 'FeedBrowserSetOffset' ) )
        $browserOffset = $http->postVariable( 'FeedBrowserSetOffset' );
    else if ( $http->hasPostVariable( 'FeedBrowserApply' ) || $http->hasPostVariable( 'FeedBrowserSetSort' ) )
        $browserOffset = 0;
    else
        $browserOffset = $http->hasPostVariable( 'FeedBrowserOffset' ) ? $http->postVariable( 'FeedBrowserOffset' ) : 0;

    $browserCount  = eZRSSExport::fetchBrowserListCount( $opmlBrowserSearch, $rssExportID );
    $browserOffset = eZRSSListPager::offset( $browserOffset, $browserLimit, $browserCount );

    $opmlBrowserPager = eZRSSListPager::data( $browserCount, $browserLimit, $browserOffset, 'offset' );
    $opmlBrowserPager['sort'] = $browserSort;

    $opmlSelected = eZRSSExportOPMLItem::selectedTargetIDs( $rssExportID, eZRSSExport::STATUS_DRAFT );

    // The rows are handed over already knowing whether they are in the document,
    // so the template does not have to look each one up in a list of ids.
    foreach ( eZRSSExport::fetchBrowserList( $opmlBrowserSearch, $browserOffset, $browserLimit,
                                             $browserSort['sorts'], $rssExportID ) as $candidate )
    {
        $candidateID = (int) $candidate->attribute( 'id' );
        $opmlBrowserList[] = array(
            'id'          => $candidateID,
            'title'       => $candidate->attribute( 'title' ),
            'access_url'  => $candidate->attribute( 'access_url' ),
            'description' => $candidate->attribute( 'description' ),
            'rss_version' => $candidate->attribute( 'rss_version' ),
            'active'      => (int) $candidate->attribute( 'active' ),
            'modified'    => (int) $candidate->attribute( 'modified' ),
            'selected'    => isset( $opmlSelected[$candidateID] ) );
    }
}

$tpl = eZTemplate::factory();
$config = eZINI::instance( 'site.ini' );

$rssVersionArray = $config->variable( 'RSSSettings', 'AvailableVersionList' );
$rssDefaultVersion = $config->variable( 'RSSSettings', 'DefaultVersion' );
$numberOfObjectsArray = $config->variable( 'RSSSettings', 'NumberOfObjectsList' );
$numberOfObjectsDefault = $config->variable( 'RSSSettings', 'NumberOfObjectsDefault' );

// Get Classes and class attributes
$classArray = eZContentClass::fetchList();

$tpl->setVariable( 'rss_version_array', $rssVersionArray );
$tpl->setVariable( 'rss_version_default', $rssDefaultVersion );
$tpl->setVariable( 'number_of_objects_array', $numberOfObjectsArray );
$tpl->setVariable( 'number_of_objects_default', $numberOfObjectsDefault );

$tpl->setVariable( 'rss_class_array', $classArray );

// What the OPML half of the page draws itself from.
$tpl->setVariable( 'rss_is_opml', $isOPML );
$tpl->setVariable( 'opml_head', $rssExport->opmlHead() );
$opmlItems = $isOPML && is_numeric( $rssExportID )
             ? eZRSSExportOPMLItem::fetchList( $rssExportID, eZRSSExport::STATUS_DRAFT )
             : array();

// The rows that can hold other rows, for the "inside" menu on each one.
$opmlGroups = array();
foreach ( $opmlItems as $opmlItem )
{
    if ( $opmlItem->attribute( 'outline_type' ) !== 'group' )
        continue;
    $label = $opmlItem->attribute( 'outline_text' );
    $opmlGroups[] = array( 'id'    => (int) $opmlItem->attribute( 'id' ),
                           'label' => $label !== '' ? $label : ezpI18n::tr( 'kernel/rss/edit_export', 'Group' ) );
}

$tpl->setVariable( 'opml_items', $opmlItems );
$tpl->setVariable( 'opml_groups', $opmlGroups );
$tpl->setVariable( 'opml_outline_types', eZRSSExportOPMLItem::outlineTypes() );
$tpl->setVariable( 'opml_browser_list', $opmlBrowserList );
$tpl->setVariable( 'opml_browser_pager', $opmlBrowserPager );
$tpl->setVariable( 'opml_browser_search', $opmlBrowserSearch );
$tpl->setVariable( 'opml_browser_limits', eZRSSListPager::limits() );
$tpl->setVariable( 'opml_selected_ids', $opmlSelected );
$tpl->setVariable( 'rss_export', $rssExport );
$tpl->setVariable( 'rss_export_id', $rssExportID );

// BC for old templates
$tpl->setVariable( 'validaton', !$valid );
// New validation handling
$tpl->setVariable( 'valid', $valid );
$tpl->setVariable( 'validation_errors', $validationErrors );

$Result = array();
$Result['content'] = $tpl->fetch( "design:rss/edit_export.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/rss', 'Really Simple Syndication' ) ) );


?>
