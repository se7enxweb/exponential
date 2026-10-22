<?php
/**
 * The datatable/syncs view (doc/eng-us/features/6.0/data-tables.md, "Synchronisation"): every synchronisation between
 * content and a data table, with its direction, mode, last and next run, open conflicts, and "Run now" (through the
 * transfer runner; the run shows in the run history). Creates the sync tables when they are missing. Policy
 * datatable/sync.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Syncs extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $messages = array();
        if ( $isPost && $http->hasPostVariable( 'CreateTablesButton' ) )
        {
            $log = array();
            $ok = \expDataSync::install( $log );
            $messages[] = array( 'kind' => $ok ? 'ok' : 'bad', 'text' => implode( '; ', $log ) );
        }
        $installed = \expDataSync::installed( true );
        if ( $isPost && $installed && $http->hasPostVariable( 'RunButton' ) )
        {
            $ids = array_keys( (array)$http->postVariable( 'RunButton' ) );
            $sync = $ids ? \expDataSyncDefinition::fetchByID( (int)$ids[0] ) : null;
            if ( $sync )
            {
                $run = \expDataSync::start( $sync );
                $messages[] = self::runMessage( $sync, $run );
            }
        }
        $rows = array();
        if ( $installed )
            foreach ( \expDataSyncDefinition::fetchList() as $sync )
                $rows[] = $sync;
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'syncs', $rows );
        $tpl->setVariable( 'installed', $installed );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'open_conflicts', $installed ? (int)\eZPersistentObject::count( \expDataSyncConflict::definition(), array( 'state' => 'open' ) ) : 0 );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/syncs.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => 'datatable/dashboard', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Synchronisations' ) ) );
        return $Result;
    }

    /**
     * The message about a started run.
     *
     * @param \expDataSyncDefinition $sync
     * @param array $run
     * @return array kind, text
     */
    public static function runMessage( $sync, array $run )
    {
        if ( !empty( $run['counts'] ) && is_array( $run['counts'] ) )
        {
            $d = isset( $run['counts']['detail'] ) ? $run['counts']['detail'] : array();
            return array( 'kind' => !empty( $run['counts']['rows_failed'] ) ? 'warn' : 'ok',
                          'text' => \ezpI18n::tr( 'design/admin/datatable', '%name ran: %rows rows and %objects objects written, %same unchanged, %conflicts conflicts, %failed failed.', null,
                                                  array( '%name' => $sync->attribute( 'name' ), '%rows' => isset( $d['rows_written'] ) ? $d['rows_written'] : 0,
                                                         '%objects' => isset( $d['objects_written'] ) ? $d['objects_written'] : 0, '%same' => isset( $d['unchanged'] ) ? $d['unchanged'] : 0,
                                                         '%conflicts' => isset( $d['conflicts'] ) ? $d['conflicts'] : 0, '%failed' => isset( $run['counts']['rows_failed'] ) ? $run['counts']['rows_failed'] : 0 ) )
                                    . ( !empty( $run['counts']['errors'] ) ? ' ' . implode( '; ', array_slice( (array)$run['counts']['errors'], 0, 3 ) ) : '' ) );
        }
        if ( !empty( $run['error'] ) && ( empty( $run['engine'] ) || $run['engine'] === 'process' ) )
            return array( 'kind' => 'bad', 'text' => $run['error'] );
        return array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/datatable', '%name was started in the background (%engine); the run history shows it.', null,
                                                               array( '%name' => $sync->attribute( 'name' ), '%engine' => isset( $run['engine'] ) ? $run['engine'] : '' ) ) );
    }
}

}
?>
