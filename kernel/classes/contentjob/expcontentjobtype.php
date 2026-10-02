<?php
/**
 * File containing the expContentJobType interface.
 *
 * A kind of content job, registered in content.ini [ContentJobSettings] JobTypes[<name>]=<class>. The engine
 * (expContentJob, expContentJobWorker) does the rest: the store, the locks, the worker process, the batches
 * with a transaction each, the checkpoint after each batch, the heartbeat, cancel and resume.
 *
 * The contract a type keeps so a job survives kill -9 at any point:
 *   - runBatch() does one batch of work and writes only to the database; the worker runs it inside a
 *     transaction of its own and commits it, so a batch is done completely or not at all;
 *   - runBatch() is idempotent: run again after a crash between the commit and the checkpoint, it finds the
 *     work already done in the database and neither doubles nor loses it;
 *   - what a type keeps between batches is in $job->checkpoint() (saved after every batch) or in its own side
 *     files, written in afterBatch() once the batch has been committed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expContentJobType
{
    /**
     * Checks the parameters and the user's permissions for the whole operation, when the job is created.
     *
     * @param array $params
     * @param eZUser $user
     * @return array the normalized parameters, stored with the job
     * @throws expContentJobException with a message for the user when the job is refused
     */
    public function validate( array $params, eZUser $user );

    /**
     * How many nodes the operation touches (for the synchronous-or-job decision and the progress total).
     *
     * @param array $params
     * @return int
     */
    public function countNodes( array $params );

    /**
     * The locks the job takes when it is created: array( array( 'path' => path string, 'mode' => 'subtree'|'node' ) ).
     *
     * @param array $params normalized
     * @return array
     */
    public function locks( array $params );

    /**
     * A short sentence describing the job ("Remove 3 subtrees (2,140 nodes) to the trash").
     *
     * @param array $params normalized
     * @return string
     */
    public function describe( array $params );

    /**
     * Runs in the worker before the first batch of every run (also after a resume): sets up the checkpoint
     * the first time, checks it is still valid afterwards. Must be idempotent.
     *
     * @param expContentJob $job
     */
    public function prepare( expContentJob $job );

    /**
     * One batch, inside the worker's transaction.
     *
     * @param expContentJob $job
     * @param int $batchSize
     * @return array( 'done' => int units done in this batch, 'finished' => bool, 'message' => string, and optionally
     *               'done_total' => int units done in all, counted from the database: the progress then stays exact
     *               when a batch committed before a crash is not done again )
     * @throws Exception to fail the job (the batch is rolled back)
     */
    public function runBatch( expContentJob $job, $batchSize );

    /**
     * After the batch has been committed, before the checkpoint is saved (side files, locks).
     *
     * @param expContentJob $job
     */
    public function afterBatch( expContentJob $job );

    /**
     * After the last batch (the job is about to be marked done).
     *
     * @param expContentJob $job
     */
    public function finish( expContentJob $job );
}
