<?php
/**
 * File containing the expKernelTransferTask interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The work of one background run of an import or export tool, whatever engine runs it (expKernelTransferRunner:
 * expqueue, the kernel's content jobs, the tool's own process). The engine calls run() once, in a worker process,
 * as the user who asked for the run, outside any database transaction; the runner has marked the run's history row
 * running and ends it afterwards from what run() returns or throws.
 *
 *   class xrowExtractTransferTask implements expKernelTransferTask
 *   {
 *       public function run( array $params, expKernelTransferContext $context )
 *       {
 *           ...  $context->progress( $done, $total );  $context->throwIfCancelled();  ...
 *           return array( 'rows_done' => $n, 'bytes' => $bytes, 'file_ref' => 'export.csv' );
 *       }
 *   }
 *
 * run() returns the run's result, which goes into its history row: any column of exp_transfer_run (rows_done,
 * rows_failed, rows_total, bytes, format, file_ref, title, detail_url, error, errors (a list), detail (an array)) and
 * 'state' (done, the default, or failed for a run that ended without an exception but did not do its work). It
 * throws expKernelTransferCancelledException when it stopped because the run was cancelled, any other exception
 * when it failed (the message is the run's error).
 *
 * The parameters are what the tool handed to expKernelTransferRunner::submit(): plain JSON values, never a secret
 * (the queue's admin pages show them). A task keeps its own state (a job file, its own table) itself; run() may be
 * called again for the same parameters after a worker was killed, so it either continues or starts over cleanly.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expKernelTransferTask
{
    /**
     * @param array $params
     * @param expKernelTransferContext $context
     * @return array|null the result (see above)
     * @throws expKernelTransferCancelledException
     * @throws Exception
     */
    public function run( array $params, expKernelTransferContext $context );
}
