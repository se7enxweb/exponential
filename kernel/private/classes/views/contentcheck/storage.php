<?php
/**
 * The contentcheck/storage view (doc/features/6.0/content-checks.md, "Storage files"): the result of the last scan
 * of var/<site>/storage, in three lists (GET Show):
 *
 *   orphans     files no content uses, with size and age and the totals (the whole storage: they belong to no node)
 *   problems    used files with a wrong owner or mode, empty files, and name or path mismatches, of the content below
 *               the start location (contentcheck/storage/<node id>, see expContentCheckScope)
 *   quarantine  the files moved to var/<site>/storage-quarantine, to restore or delete
 *
 * The scan itself and every action are posted to contentcheck/storageaction. Needs contentcheck/storage; the actions
 * need contentcheck/quarantine or contentcheck/permissions as well. Answers on administration siteaccesses only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Storage extends \Exponential\Runnable\ModuleView
{
    /** The session variable the action view leaves its outcome in */
    const FEEDBACK = 'ContentCheckStorageFeedback';

    /** The preference that holds the chosen page size (its position in the sizes) */
    const LIMIT_PREFERENCE = 'admin_contentcheck_storage_limit';

    /** The lists */
    const SHOWS = array( 'orphans', 'problems', 'quarantine' );

    /** The orders of the orphans */
    const SORTS = array( 'path', 'size', 'age' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $http = \eZHTTPTool::instance();
        list( $limit, $limitChoice, $limitChoices ) = \expAdminPagination::chosen( 'contentcheck/storage', self::LIMIT_PREFERENCE );
        $get = array();
        foreach ( array( 'Show', 'Sort', 'Order', 'Kind' ) as $name )
            $get[$name] = $http->hasGetVariable( $name ) ? $http->getVariable( $name ) : null;
        $params = self::parameters( $get );
        $params['offset'] = \expAdminPagination::offset( $Params );
        $params['limit'] = $limit;
        $nodeID = \expContentCheckScope::resolve( isset( $Params['NodeID'] ) ? $Params['NodeID'] : null,
                                                 $http->hasGetVariable( 'NodeID' ) ? $http->getVariable( 'NodeID' ) : null );
        if ( !\expContentCheck::readableNode( $nodeID ) )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        $params['node_id'] = $nodeID;

        $feedback = null;
        if ( $http->hasSessionVariable( self::FEEDBACK ) )
        {
            $feedback = $http->sessionVariable( self::FEEDBACK );
            $http->removeSessionVariable( self::FEEDBACK );
            if ( !is_array( $feedback ) )
                $feedback = null;
        }

        $state = \expContentCheckStorage::lastScan();
        $rows = array();
        $count = 0;
        $quarantine = null;
        if ( $params['show'] === 'quarantine' )
        {
            $quarantine = \expContentCheckQuarantine::listing();
            $count = count( $quarantine['items'] );
            $rows = array_slice( $quarantine['items'], $params['offset'], $limit );
        }
        else if ( $state && $state['phase'] === 'done' )
        {
            if ( $params['show'] === 'orphans' )
            {
                $all = \expContentCheckStorage::sortedOrphans( $state, $params['sort'], $params['order'] );
                $count = count( $all );
                foreach ( array_slice( $all, $params['offset'], $limit ) as $orphan )
                    $rows[] = array( 'path' => $orphan['p'], 'size' => (int)$orphan['s'], 'mtime' => (int)$orphan['m'],
                                     'age_days' => (int)floor( max( 0, time() - (int)$orphan['m'] ) / 86400 ),
                                     'owner' => $orphan['u'] === null ? '' : \expContentCheckStorage::userName( (int)$orphan['u'] ),
                                     'mode' => $orphan['md'] === null ? '' : sprintf( '%04o', (int)$orphan['md'] ) );
            }
            else
            {
                $all = \expContentCheckStorage::problemsBelow( $state, $nodeID, $params['kinds'] );
                $count = count( $all );
                $rows = array_slice( $all, $params['offset'], $limit );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'scope', \expContentCheckScope::forTemplate( $nodeID, 'storage' ) );
        $tpl->setVariable( 'params', $params );
        $tpl->setVariable( 'state', $state );
        $tpl->setVariable( 'percent', $state ? \expContentCheckStorage::percent( $state ) : 0 );
        $tpl->setVariable( 'rows', $rows );
        $tpl->setVariable( 'count', $count );
        $tpl->setVariable( 'quarantine', $quarantine );
        $tpl->setVariable( 'quarantine_root', \expContentCheckQuarantine::root() );
        $tpl->setVariable( 'storage_dir', \expContentCheckStorage::storageDirectory() );
        $tpl->setVariable( 'kinds', \expContentCheckStorage::PROBLEMS );
        $tpl->setVariable( 'sorts', self::SORTS );
        $tpl->setVariable( 'min_age_days', (int)ceil( \expContentCheckStorage::minAge() / 86400 ) );
        $tpl->setVariable( 'cluster', !\expContentCheckStorage::canChangeFiles() );
        $tpl->setVariable( 'can_quarantine', \expContentCheck::hasPolicy( 'contentcheck', 'quarantine' ) && \expContentCheckStorage::canChangeFiles() );
        $tpl->setVariable( 'can_fix', \expContentCheck::hasPolicy( 'contentcheck', 'permissions' ) && \expContentCheckStorage::canChangeFiles() );
        $tpl->setVariable( 'batch_limit', \expContentCheckQuarantine::batchLimit() );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'select_all', $http->hasGetVariable( 'SelectAll' ) && $http->getVariable( 'SelectAll' ) === '1' );
        $tpl->setVariable( 'page_uri', '/contentcheck/storage/' . $nodeID );
        $tpl->setVariable( 'page_uri_suffix', self::querySuffix( $params ) );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $params['offset'] ) );
        $tpl->setVariable( 'limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_preference', self::LIMIT_PREFERENCE );
        $tpl->setVariable( 'form_token', \expContentCheck::formToken() );
        $tpl->setVariable( 'token_field', \expContentCheck::TOKEN_FIELD );

        return array( 'content' => $tpl->fetch( 'design:contentcheck/storage.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Storage files' ), 'url' => false ) ) );
    }

    /**
     * The list, the order and the kinds of problem from the request; anything else falls back to the default.
     *
     * @param array $get Show, Sort, Order, Kind (null: not given)
     * @return array array( 'show', 'sort', 'order', 'kinds' )
     */
    public static function parameters( array $get )
    {
        $show = isset( $get['Show'] ) && in_array( $get['Show'], self::SHOWS, true ) ? $get['Show'] : 'orphans';
        $sort = isset( $get['Sort'] ) && in_array( $get['Sort'], self::SORTS, true ) ? $get['Sort'] : 'size';
        $order = isset( $get['Order'] ) && in_array( $get['Order'], array( 'asc', 'desc' ), true ) ? $get['Order'] : ( $sort === 'path' ? 'asc' : 'desc' );
        $kinds = array();
        foreach ( isset( $get['Kind'] ) && is_array( $get['Kind'] ) ? $get['Kind'] : array() as $kind )
        {
            if ( is_string( $kind ) && in_array( $kind, \expContentCheckStorage::PROBLEMS, true ) && !in_array( $kind, $kinds, true ) )
                $kinds[] = $kind;
        }
        return array( 'show' => $show, 'sort' => $sort, 'order' => $order, 'kinds' => $kinds ?: \expContentCheckStorage::PROBLEMS );
    }

    /**
     * The query string that keeps the list, the order and the kinds on the pages.
     *
     * @param array $params
     * @return string starting with ?
     */
    public static function querySuffix( array $params )
    {
        $query = array( 'Show=' . rawurlencode( $params['show'] ) );
        if ( $params['show'] === 'orphans' )
        {
            $query[] = 'Sort=' . rawurlencode( $params['sort'] );
            $query[] = 'Order=' . rawurlencode( $params['order'] );
        }
        if ( $params['show'] === 'problems' && count( $params['kinds'] ) < count( \expContentCheckStorage::PROBLEMS ) )
        {
            foreach ( $params['kinds'] as $kind )
                $query[] = 'Kind%5B%5D=' . rawurlencode( $kind );
        }
        return '?' . implode( '&', $query );
    }
}

}
