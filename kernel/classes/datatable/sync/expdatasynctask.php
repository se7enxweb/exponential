<?php
/**
 * File containing the expDataSyncTask class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One run of a data table synchronisation, as the transfer runner calls it (expKernelTransferTask): parameters sync
 * (the definition's id) and, for the live mirror run in the background, object.
 *
 * @since 6.0.15
 * @package kernel
 */
class expDataSyncTask implements expKernelTransferTask
{
    public function run( array $params, expKernelTransferContext $context )
    {
        $sync = isset( $params['sync'] ) ? expDataSyncDefinition::fetchByID( (int)$params['sync'] ) : null;
        if ( !$sync )
            throw new RuntimeException( 'There is no such data table synchronisation.' );
        $counts = expDataSync::run( $sync, $context, isset( $params['object'] ) ? (int)$params['object'] : 0 );
        $done = $counts['rows_written'] + $counts['objects_written'] + $counts['removed'];
        return array(
            'rows_done' => $done + $counts['unchanged'],
            'rows_failed' => $counts['failed'],
            'title' => (string)$sync->attribute( 'name' ),
            'detail_url' => 'datatable/sync/' . (int)$sync->attribute( 'id' ),
            'errors' => array_slice( $counts['errors'], 0, 20 ),
            'detail' => array_diff_key( $counts, array( 'errors' => true ) ),
            'state' => $counts['failed'] && !$done && !$counts['unchanged'] ? 'failed' : 'done',
        );
    }
}
?>
