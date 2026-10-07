<?php
/**
 * The code of kernel/rss/list.php, moved into a class (#207 stage 1). The file kernel/rss/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/rss/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Rss
{

class ListView extends \Exponential\Runnable\ModuleView
{
    /** The session variable that carries the result of a removal over the redirect back to the list. */
    const FEEDBACK_KEY = 'eZRSSListFeedback';

    /** The cronjob script that reads the active imports. */
    const IMPORT_SCRIPT = 'rssimport.php';

    /** How many sources of an export a card names; the rest are counted. */
    const SOURCES_SHOWN = 5;

    /**
     * Ids from a form: whole positive numbers, once each, in the order given.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function idList( $value )
    {
        $ids = array();
        foreach ( is_array( $value ) ? $value : array( $value ) as $id )
        {
            if ( is_int( $id ) || ( is_string( $id ) && ctype_digit( $id ) ) )
            {
                $id = (int)$id;
                if ( $id > 0 && !in_array( $id, $ids, true ) )
                    $ids[] = $id;
            }
        }
        return $ids;
    }

    /**
     * The remote id prefix the rssimport cronjob gives every object it creates for an import, as a LIKE pattern
     * with "!" as its escape character: "RSSImport_12_" must not also match the objects of import 123.
     *
     * @param int|false $importID false for the objects of every import
     * @return string
     */
    public static function importedObjectPattern( $importID = false )
    {
        $prefix = $importID === false ? 'RSSImport_' : 'RSSImport_' . (int)$importID . '_';
        return str_replace( array( '!', '%', '_' ), array( '!!', '!%', '!_' ), $prefix ) . '%';
    }

    /**
     * The name of the cached copy rss/feed writes of a feed for one siteaccess (kernel/rss/feed.php).
     *
     * @param string $siteAccess
     * @param string $accessURL
     * @return string relative to the cache directory
     */
    public static function feedCacheFile( $siteAccess, $accessURL )
    {
        return 'rss/' . md5( $siteAccess . $accessURL ) . '.xml';
    }

    /**
     * How long rss/feed keeps a cached copy (site.ini [RSSSettings] CacheTime); 0 when it writes the feed anew
     * for every request.
     *
     * @return int seconds
     */
    public static function feedCacheTime()
    {
        $ini = \eZINI::instance();
        return $ini->hasVariable( 'RSSSettings', 'CacheTime' ) ? max( 0, (int)$ini->variable( 'RSSSettings', 'CacheTime' ) ) : 0;
    }

    /**
     * When the feed was last written: the newest cached copy of any siteaccess. False when there is none.
     *
     * @param string $accessURL
     * @return int|false
     */
    public static function lastGenerated( $accessURL )
    {
        if ( !is_string( $accessURL ) || $accessURL === '' )
            return false;
        $ini = \eZINI::instance();
        $siteAccesses = $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' )
                      ? (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) : array();
        $cacheDir = \eZSys::cacheDirectory();
        $newest = false;
        foreach ( array_unique( $siteAccesses ) as $siteAccess )
        {
            $file = \eZClusterFileHandler::instance( $cacheDir . '/' . self::feedCacheFile( $siteAccess, $accessURL ) );
            if ( $file->exists() )
            {
                $mtime = (int)$file->mtime();
                if ( $newest === false || $mtime > $newest )
                    $newest = $mtime;
            }
        }
        return $newest;
    }

    /**
     * What an export card shows beyond the row: the public feed address, the format in words, its sources, when
     * it was last written, and what needs attention.
     *
     * @param \eZRSSExport $export
     * @return array
     */
    public static function exportInfo( $export )
    {
        $c = 'design/admin/rss/list';
        $accessURL = (string)$export->attribute( 'access_url' );
        $siteAccess = (string)$export->attribute( 'site_access' );
        $version = (string)$export->attribute( 'rss_version' );
        $isOPML = $version === 'OPML';

        $sources = array();
        $missing = 0;
        $sourceCount = 0;
        if ( $isOPML )
        {
            $lines = (array)$export->opmlItemList();
            $sourceCount = count( $lines );
        }
        else
        {
            $items = (array)\eZRSSExportItem::fetchFilteredList( array( 'rssexport_id' => (int)$export->attribute( 'id' ),
                                                                     'status' => (int)$export->attribute( 'status' ) ) );
            $sourceCount = count( $items );
            $classNames = array();
            foreach ( $items as $item )
            {
                $node = \eZContentObjectTreeNode::fetch( (int)$item->attribute( 'source_node_id' ) );
                if ( !$node )
                    $missing++;
                if ( count( $sources ) >= self::SOURCES_SHOWN )
                    continue;
                $classID = (int)$item->attribute( 'class_id' );
                if ( !isset( $classNames[$classID] ) )
                {
                    $class = $classID ? \eZContentClass::fetch( $classID ) : null;
                    $classNames[$classID] = $class ? (string)$class->attribute( 'name' ) : '';
                }
                $sources[] = array( 'node_id' => (int)$item->attribute( 'source_node_id' ),
                                    'name' => $node ? (string)$node->attribute( 'name' ) : '',
                                    'url' => $node ? (string)$node->attribute( 'url_alias' ) : '',
                                    'found' => (bool)$node,
                                    'class_name' => $classNames[$classID],
                                    'subnodes' => (bool)$item->attribute( 'subnodes' ) );
            }
        }

        $feedURL = '';
        if ( $accessURL !== '' )
            $feedURL = \eZRSSExport::publicSiteURL( $siteAccess !== '' ? $siteAccess : false ) . '/rss/feed/' . $accessURL;

        $warnings = array();
        if ( $accessURL === '' )
            $warnings[] = \ezpI18n::tr( $c, 'No feed address: readers cannot reach it.' );
        if ( $sourceCount === 0 )
            $warnings[] = $isOPML ? \ezpI18n::tr( $c, 'The list of feeds is empty.' )
                                  : \ezpI18n::tr( $c, 'No source: the feed has no items.' );
        if ( $missing > 0 )
            $warnings[] = \ezpI18n::tr( $c, '%count sources no longer exist.', null, array( '%count' => $missing ) );

        $active = (int)$export->attribute( 'active' ) === 1;
        $lastGenerated = $active ? self::lastGenerated( $accessURL ) : false;
        $search = mb_strtolower( implode( ' ', array( $export->attribute( 'title' ), $accessURL, $feedURL, $version,
                                                      \eZRSSExport::formatLabel( $version ), $export->attribute( 'id' ) ) ) );
        foreach ( $sources as $source )
            $search .= ' ' . mb_strtolower( $source['name'] . ' ' . $source['class_name'] );

        return array( 'feed_url' => $feedURL,
                      'feed_path' => $accessURL !== '' ? 'rss/feed/' . $accessURL : '',
                      'format' => \eZRSSExport::formatLabel( $version ),
                      'is_opml' => $isOPML,
                      'site_access' => $siteAccess,
                      'number_of_objects' => (int)$export->attribute( 'number_of_objects' ),
                      'main_node_only' => (int)$export->attribute( 'main_node_only' ) === 1,
                      'sources' => $sources,
                      'source_count' => $sourceCount,
                      'more_sources' => ( $isOPML ? 0 : max( 0, $sourceCount - count( $sources ) ) ),
                      'missing_sources' => $missing,
                      'last_generated' => $lastGenerated === false ? 0 : $lastGenerated,
                      'warnings' => $warnings,
                      'attention' => $active && count( $warnings ) > 0,
                      'search' => $search );
    }

    /**
     * How many objects an import has brought in, and when the newest of them was published. The rssimport
     * cronjob gives each one the remote id RSSImport_<import id>_<md5 of the item>.
     *
     * @param int|false $importID false for every import together
     * @return array count, newest (timestamp, 0 when none)
     */
    public static function importedObjects( $importID = false )
    {
        $db = \eZDB::instance();
        $pattern = $db->escapeString( self::importedObjectPattern( $importID ) );
        try
        {
            $rows = $db->arrayQuery( "SELECT COUNT(*) AS object_count, MAX(published) AS newest FROM ezcontentobject "
                                   . "WHERE remote_id LIKE '$pattern' ESCAPE '!'" );
        }
        catch ( \Exception $e )
        {
            $rows = array();
        }
        $row = is_array( $rows ) && isset( $rows[0] ) ? array_change_key_case( $rows[0], CASE_LOWER ) : array();
        return array( 'count' => isset( $row['object_count'] ) ? (int)$row['object_count'] : 0,
                      'newest' => isset( $row['newest'] ) ? (int)$row['newest'] : 0 );
    }

    /**
     * What an import card shows beyond the row: where it puts its items, as which class and owner, what it has
     * brought in, and what needs attention.
     *
     * @param \eZRSSImport $import
     * @return array
     */
    public static function importInfo( $import )
    {
        $c = 'design/admin/rss/list';
        $destinationID = (int)$import->attribute( 'destination_node_id' );
        $node = $destinationID ? \eZContentObjectTreeNode::fetch( $destinationID ) : null;
        $classID = (int)$import->attribute( 'class_id' );
        $class = $classID ? \eZContentClass::fetch( $classID ) : null;
        $ownerID = (int)$import->attribute( 'object_owner_id' );
        $owner = $ownerID ? \eZContentObject::fetch( $ownerID ) : null;
        $url = (string)$import->attribute( 'url' );
        $host = $url !== '' ? (string)parse_url( $url, PHP_URL_HOST ) : '';
        $imported = self::importedObjects( (int)$import->attribute( 'id' ) );

        $warnings = array();
        if ( $url === '' )
            $warnings[] = \ezpI18n::tr( $c, 'No source address.' );
        if ( !$node )
            $warnings[] = $destinationID ? \ezpI18n::tr( $c, 'The destination no longer exists.' )
                                         : \ezpI18n::tr( $c, 'No destination is set.' );
        if ( !$class )
            $warnings[] = \ezpI18n::tr( $c, 'No class is set for the imported items.' );

        $active = (int)$import->attribute( 'active' ) === 1;
        return array( 'destination_node_id' => $destinationID,
                      'destination_name' => $node ? (string)$node->attribute( 'name' ) : '',
                      'destination_url' => $node ? (string)$node->attribute( 'url_alias' ) : '',
                      'class_name' => $class ? (string)$class->attribute( 'name' ) : '',
                      'class_id' => $classID,
                      'owner_name' => $owner ? (string)$owner->attribute( 'name' ) : '',
                      'host' => $host,
                      'imported_count' => $imported['count'],
                      'newest_import' => $imported['newest'],
                      'warnings' => $warnings,
                      'attention' => $active && count( $warnings ) > 0,
                      'search' => mb_strtolower( implode( ' ', array( $import->attribute( 'name' ), $url,
                                                                      $node ? $node->attribute( 'name' ) : '',
                                                                      $class ? $class->attribute( 'name' ) : '',
                                                                      $import->attribute( 'id' ) ) ) ) );
    }

    /**
     * The overview: exports and imports, how many of each are active, and the objects all imports brought in.
     *
     * @param int $exportCount
     * @param int $importCount
     * @return array
     */
    public static function summary( $exportCount, $importCount )
    {
        $activeExports = (int)\eZPersistentObject::count( \eZRSSExport::definition(),
                                                          array( 'status' => \eZRSSExport::STATUS_VALID, 'active' => 1 ) );
        $activeImports = (int)\eZPersistentObject::count( \eZRSSImport::definition(),
                                                          array( 'status' => \eZRSSImport::STATUS_VALID, 'active' => 1 ) );
        $imported = self::importedObjects( false );
        return self::summaryOf( $exportCount, $activeExports, $importCount, $activeImports, $imported['count'] );
    }

    /**
     * @return array exports, active_exports, inactive_exports, imports, active_imports, inactive_imports, imported
     */
    public static function summaryOf( $exports, $activeExports, $imports, $activeImports, $imported )
    {
        $exports = max( 0, (int)$exports );
        $imports = max( 0, (int)$imports );
        $activeExports = min( $exports, max( 0, (int)$activeExports ) );
        $activeImports = min( $imports, max( 0, (int)$activeImports ) );
        return array( 'exports' => $exports, 'active_exports' => $activeExports, 'inactive_exports' => $exports - $activeExports,
                      'imports' => $imports, 'active_imports' => $activeImports, 'inactive_imports' => $imports - $activeImports,
                      'imported' => max( 0, (int)$imported ) );
    }

    /**
     * Removes exports, the published row and a draft alike, with their sources.
     *
     * @param int[] $ids
     * @return string[] the names of those removed
     */
    public static function removeExports( array $ids )
    {
        $names = array();
        $db = \eZDB::instance();
        $db->begin();
        foreach ( $ids as $id )
        {
            foreach ( array( \eZRSSExport::STATUS_VALID, \eZRSSExport::STATUS_DRAFT ) as $status )
            {
                $export = \eZRSSExport::fetch( $id, true, $status );
                if ( $export )
                {
                    if ( !in_array( (string)$export->attribute( 'title' ), $names, true ) )
                        $names[] = (string)$export->attribute( 'title' );
                    // removeThis() takes the sources and OPML lines of that status with it; remove() left them behind.
                    $export->removeThis();
                }
            }
        }
        $db->commit();
        return $names;
    }

    /**
     * Removes imports, the published row and a draft alike. The objects they brought in stay.
     *
     * @param int[] $ids
     * @return string[] the names of those removed
     */
    public static function removeImports( array $ids )
    {
        $names = array();
        $db = \eZDB::instance();
        $db->begin();
        foreach ( $ids as $id )
        {
            foreach ( array( \eZRSSImport::STATUS_VALID, \eZRSSImport::STATUS_DRAFT ) as $status )
            {
                $import = \eZRSSImport::fetch( $id, true, $status );
                if ( $import )
                {
                    if ( !in_array( (string)$import->attribute( 'name' ), $names, true ) )
                        $names[] = (string)$import->attribute( 'name' );
                    $import->remove();
                }
            }
        }
        $db->commit();
        return $names;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];


        $http = \eZHTTPTool::instance();

        if ( $http->hasPostVariable( 'NewExportButton' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( 'edit_export', array() ) );
        }
        else if ( $http->hasPostVariable( 'NewImportButton' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->run( 'edit_import', array() ) );
        }

        // Removing takes two steps. Remove selected only shows what would go and what that means for the feed's
        // readers or the imported content (design:rss/confirmremove.tpl); the button on that page posts the same
        // ids again with ConfirmRemoveButton, and only then is anything removed. The result comes back after a
        // redirect, so a reload never removes twice.
        $feedback = $http->hasSessionVariable( self::FEEDBACK_KEY ) ? $http->sessionVariable( self::FEEDBACK_KEY ) : false;
        if ( $feedback !== false )
            $http->removeSessionVariable( self::FEEDBACK_KEY );

        foreach ( array( 'export' => array( 'RemoveExportButton', 'DeleteIDArray' ),
                         'import' => array( 'RemoveImportButton', 'DeleteIDArrayImport' ) ) as $kind => $names )
        {
            if ( !$http->hasPostVariable( $names[0] ) )
                continue;
            $ids = self::idList( $http->hasPostVariable( $names[1] ) ? $http->postVariable( $names[1] ) : array() );
            if ( !$ids )
            {
                $feedback = array( 'type' => 'none_selected', 'kind' => $kind, 'names' => array() );
                break;
            }
            if ( !$http->hasPostVariable( 'ConfirmRemoveButton' ) )
            {
                $items = array();
                foreach ( $ids as $id )
                {
                    if ( $kind === 'export' )
                    {
                        $export = \eZRSSExport::fetch( $id, true, \eZRSSExport::STATUS_VALID );
                        if ( !$export )
                            $export = \eZRSSExport::fetch( $id, true, \eZRSSExport::STATUS_DRAFT );
                        if ( $export )
                            $items[] = array( 'object' => $export, 'info' => self::exportInfo( $export ) );
                    }
                    else
                    {
                        $import = \eZRSSImport::fetch( $id, true, \eZRSSImport::STATUS_VALID );
                        if ( !$import )
                            $import = \eZRSSImport::fetch( $id, true, \eZRSSImport::STATUS_DRAFT );
                        if ( $import )
                            $items[] = array( 'object' => $import, 'info' => self::importInfo( $import ) );
                    }
                }
                if ( !$items )
                {
                    $feedback = array( 'type' => 'gone', 'kind' => $kind, 'names' => array() );
                    break;
                }
                $tpl = \eZTemplate::factory();
                $tpl->setVariable( 'rss_remove_kind', $kind );
                $tpl->setVariable( 'rss_remove_items', $items );
                $tpl->setVariable( 'rss_remove_button', $names[0] );
                $tpl->setVariable( 'rss_remove_field', $names[1] );
                $Result = array();
                $Result['content'] = $tpl->fetch( 'design:rss/confirmremove.tpl' );
                $Result['path'] = array( array( 'url' => 'rss/list',
                                                'text' => \ezpI18n::tr( 'kernel/rss', 'Really Simple Syndication' ) ),
                                         array( 'url' => false,
                                                'text' => \ezpI18n::tr( 'design/admin/rss/list', 'Confirm removal' ) ) );
                return $this->viewResult( $Result, null );
            }

            $removed = $kind === 'export' ? self::removeExports( $ids ) : self::removeImports( $ids );
            $http->setSessionVariable( self::FEEDBACK_KEY, array( 'type' => 'removed', 'kind' => $kind, 'names' => $removed ) );
            $Module->redirectTo( '/rss/list' );
            return $this->viewResult( null, null );
        }


        // ---------------------------------------------------------------- paging ---
        //
        // The lists are fetched a page at a time. Asking for every row and showing
        // twenty five of them costs the same whether there are ten feeds or ten
        // thousand, and at ten thousand it is the difference between a page that opens
        // and one that does not.

        require_once 'kernel/rss/ezrsslistpager.php';

        $user = \eZUser::currentUser();
        $remembered = $user->isRegistered() ? \eZPreferences::value( 'admin_rss_list_limit' ) : false;
        $limit = \eZRSSListPager::limit( isset( $Params['Limit'] ) ? $Params['Limit'] : false, $remembered );

        // Remember a size the user picked, so the next visit opens the way they left it.
        if ( $user->isRegistered() && isset( $Params['Limit'] ) && $Params['Limit'] !== false
             && (string) $limit === (string) (int) $Params['Limit'] && (string) $remembered !== (string) $limit )
        {
            \eZPreferences::setValue( 'admin_rss_list_limit', $limit );
        }

        $exportCount = \eZRSSExport::fetchListCount();
        $importCount = \eZRSSImport::fetchListCount();

        // Removing the last rows of the last page would otherwise leave it empty.
        $exportOffset = \eZRSSListPager::offset( isset( $Params['Offset'] ) ? $Params['Offset'] : 0,
                                                $limit, $exportCount );
        $importOffset = \eZRSSListPager::offset( isset( $Params['ImportOffset'] ) ? $Params['ImportOffset'] : 0,
                                                $limit, $importCount );

        // Which column each list is ordered by. A column the list does not offer is
        // ignored, so nothing but a real field of the table reaches the order clause.
        $exportSort = \eZRSSListPager::sort( isset( $Params['Sort'] ) ? $Params['Sort'] : false,
                                            isset( $Params['Dir'] ) ? $Params['Dir'] : false,
                                            \eZRSSExport::sortableFields(), 'title' );

        $importSort = \eZRSSListPager::sort( isset( $Params['ImportSort'] ) ? $Params['ImportSort'] : false,
                                            isset( $Params['ImportDir'] ) ? $Params['ImportDir'] : false,
                                            \eZRSSImport::sortableFields(), 'name' );

        // Everything the page is currently showing. Each link writes the parameters it
        // owns and carries the rest along, so moving one list never disturbs the other.
        $state = array( 'limit'        => $limit,
                        'offset'       => $exportOffset,
                        'sort'         => $exportSort['field'],
                        'dir'          => $exportSort['direction'],
                        'importoffset' => $importOffset,
                        'importsort'   => $importSort['field'],
                        'importdir'    => $importSort['direction'] );

        $exportPager = \eZRSSListPager::data(
            $exportCount, $limit, $exportOffset, 'offset',
            \eZRSSListPager::suffixExcept( $state, array( 'offset' ) ) );

        $importPager = \eZRSSListPager::data(
            $importCount, $limit, $importOffset, 'importoffset',
            \eZRSSListPager::suffixExcept( $state, array( 'importoffset' ) ) );

        // Sorting a list starts it again from the top: page nine of the old order has
        // nothing to do with page nine of the new one.
        $exportSort['suffix'] = \eZRSSListPager::suffixExcept( $state, array( 'offset', 'sort', 'dir' ) );
        $importSort['suffix'] = \eZRSSListPager::suffixExcept( $state, array( 'importoffset', 'importsort', 'importdir' ) );

        // Changing the page size starts both lists again from the top - page 40 of 25
        // is not page 40 of 250, and landing past the end of a shorter list is worse
        // than landing at the beginning - but it keeps whatever order they are in.
        $limitLinks = array();
        foreach ( \eZRSSListPager::limits() as $option )
        {
            $sizeState = $state;
            $sizeState['limit'] = $option;
            $limitLinks[] = array( 'limit'   => $option,
                                   'suffix'  => \eZRSSListPager::suffixExcept( $sizeState,
                                                    array( 'offset', 'importoffset' ) ),
                                   'current' => $option === $limit );
        }

        // One page of RSS exports.
        $exportArray = \eZRSSExport::fetchList( true, $exportPager['offset'], $limit, $exportSort['sorts'] );
        $exportList = array();
        foreach( $exportArray as $export )
        {
            $exportList[$export->attribute( 'id' )] = $export;
        }

        // One page of RSS imports.
        $importArray = \eZRSSImport::fetchList( true, \eZRSSImport::STATUS_VALID, $importPager['offset'], $limit,
                                               $importSort['sorts'] );
        $importList = array();
        foreach( $importArray as $import )
        {
            $importList[$import->attribute( 'id' )] = $import;
        }

        $tpl = \eZTemplate::factory();

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

        // What the cards show beyond the rows themselves: the feed address, the sources, when the feed was last
        // written; where an import puts its items, how many it has brought in and when. Keyed like the lists.
        $exportInfo = array();
        foreach ( $exportList as $id => $export )
            $exportInfo[$id] = self::exportInfo( $export );
        $importInfo = array();
        foreach ( $importList as $id => $import )
            $importInfo[$id] = self::importInfo( $import );
        $tpl->setVariable( 'rssexport_info', $exportInfo );
        $tpl->setVariable( 'rssimport_info', $importInfo );
        $tpl->setVariable( 'rss_summary', self::summary( $exportCount, $importCount ) );
        $tpl->setVariable( 'rss_import_cronjob', \Exponential\View\Kernel\Workflow\Processlist::scriptCronjob(
            self::IMPORT_SCRIPT, \expCronjobRunner::parts(), \expCronjobRunner::scheduledParts(), time() ) );
        $tpl->setVariable( 'rss_feed_cache_time', self::feedCacheTime() );
        $tpl->setVariable( 'rss_feedback', $feedback );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:rss/list.tpl" );
        $Result['path'] = array( array( 'url' => 'rss/list',
                                        'text' => \ezpI18n::tr( 'kernel/rss', 'Really Simple Syndication' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
