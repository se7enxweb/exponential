<?php
/**
 * A content job's batches on SQLite shrink while one holds the write lock too long, and grow back.
 *
 * SQLite has one writer at a time, and a batch is one transaction: a page's save waits for it. On the demo
 * (2026-11-02) batches of 10 help manual topics held the lock 4 to 7 s each. No database.
 *
 *  CJB-01  on SQLite a batch over SQLITE_BATCH_SECONDS halves the next one, down to 1
 *  CJB-02  a batch under a quarter of it doubles the next one, up to the configured BatchSize; in between it stays
 *  CJB-03  other databases keep the configured BatchSize
 *  CJB-04  the worker sizes each batch with it and measures from begin() done to commit
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expContentJobWorkerBatchSizeTest extends PHPUnit\Framework\TestCase
{
    /** CJB-01 */
    public function testALongBatchHalvesTheNextOnSQLite()
    {
        $long = expContentJobWorker::SQLITE_BATCH_SECONDS + 0.5;
        $this->assertSame( 5, expContentJobWorker::nextBatchSize( 10, 10, $long, true ) );
        $this->assertSame( 2, expContentJobWorker::nextBatchSize( 5, 10, $long, true ) );
        $this->assertSame( 1, expContentJobWorker::nextBatchSize( 1, 10, 30.0, true ) );
    }

    /** CJB-02 */
    public function testAShortBatchDoublesTheNextUpToBatchSize()
    {
        $this->assertSame( 4, expContentJobWorker::nextBatchSize( 2, 10, 0.1, true ) );
        $this->assertSame( 10, expContentJobWorker::nextBatchSize( 8, 10, 0.1, true ) );
        $this->assertSame( 10, expContentJobWorker::nextBatchSize( 10, 10, 0.1, true ) );
        $this->assertSame( 3, expContentJobWorker::nextBatchSize( 3, 10, expContentJobWorker::SQLITE_BATCH_SECONDS / 2, true ) );
    }

    /** CJB-03 */
    public function testOtherDatabasesKeepBatchSize()
    {
        $this->assertSame( 10, expContentJobWorker::nextBatchSize( 10, 10, 60.0, false ) );
        $this->assertSame( 25, expContentJobWorker::nextBatchSize( 3, 25, 0.1, false ) );
    }

    /** CJB-04 */
    public function testTheWorkerSizesEachBatchWithIt()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/contentjob/expcontentjobworker.php' );
        $this->assertNotFalse( $source );
        $this->assertSame( 1, preg_match(
            '/\$this->begin\(\);\s*\$begun = microtime\( true \);\s*\$size = \$nextSize;.*?runBatch\( \$job, \$size \).*?'
            . '\$this->commit\(\).*?\$nextSize = self::nextBatchSize\( \$size, \$batchSize, microtime\( true \) - \$begun, \$sqlite \);/s',
            $source ), 'the batch runs with the adapted size, timed from begin() done to commit' );
    }
}
?>
