<?php
/**
 * The datatable/conflicts view (doc/eng-us/features/6.0/data-tables.md, "Conflicts"): the open conflicts of the
 * two-way synchronisations with the rule "ask" (both sides changed since the last run), the values that differ, and
 * "Take the content" / "Take the table" per conflict. Policy datatable/sync.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Conflicts extends \Exponential\Runnable\ModuleView
{
    const LIMIT = 25;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        if ( !\expDataSync::installed() )
            return $Module->redirectTo( 'datatable/syncs' );
        $messages = array();
        foreach ( array( 'TakeContentButton' => 'content', 'TakeTableButton' => 'table' ) as $button => $take )
        {
            if ( !$isPost || !$http->hasPostVariable( $button ) )
                continue;
            $ids = array_keys( (array)$http->postVariable( $button ) );
            $conflict = $ids ? \eZPersistentObject::fetchObject( \expDataSyncConflict::definition(), null, array( 'id' => (int)$ids[0] ) ) : null;
            $ok = $conflict && \expDataSync::resolve( $conflict, $take );
            $messages[] = $ok
                ? array( 'kind' => 'ok', 'text' => $take === 'content' ? \ezpI18n::tr( 'design/admin/datatable', 'The content\'s values were written to the table.' )
                                                                       : \ezpI18n::tr( 'design/admin/datatable', 'The table\'s values were published to the content.' ) )
                : array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'The conflict could not be resolved; it stays open.' ) );
        }
        $syncID = isset( $Params['SyncID'] ) ? (int)$Params['SyncID'] : 0;
        $offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
        $conditions = array( 'state' => 'open' ) + ( $syncID ? array( 'sync_id' => $syncID ) : array() );
        $count = (int)\eZPersistentObject::count( \expDataSyncConflict::definition(), $conditions );
        $list = \eZPersistentObject::fetchObjectList( \expDataSyncConflict::definition(), null, $conditions, array( 'detected' => 'desc' ),
                                                      array( 'offset' => $offset, 'length' => self::LIMIT ) );
        $names = array();
        foreach ( \expDataSyncDefinition::fetchList() as $sync )
            $names[(int)$sync->attribute( 'id' )] = (string)$sync->attribute( 'name' );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'conflicts', is_array( $list ) ? $list : array() );
        $tpl->setVariable( 'count', $count );
        $tpl->setVariable( 'offset', $offset );
        $tpl->setVariable( 'limit', self::LIMIT );
        $tpl->setVariable( 'sync_names', $names );
        $tpl->setVariable( 'sync_id', $syncID );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'page_uri', 'datatable/conflicts' . ( $syncID ? '/(sync)/' . $syncID : '' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/conflicts.tpl' );
        $Result['path'] = array( array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => 'datatable/syncs', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Synchronisations' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Conflicts' ) ) );
        return $Result;
    }
}

}
?>
