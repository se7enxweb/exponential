<?php
/**
 * The checks of the static publishing record (expStaticStatus, tables exp_static_run and exp_static_page) that run
 * on every engine the same way: SQLite in process (expStaticPublishSqliteTest), MySQL/MariaDB and PostgreSQL in the
 * database job (expStaticStatusDatabaseTest), MongoDB in the live MongoDB suite (expStaticStatusMongoDBTest). Each is
 * given a connection it may drop both tables in.
 *
 *  SS-E1 the upgrade step 6.0.15-static-publish-tables is missing without the tables, makes both, is present
 *        afterwards and a second apply changes nothing; without the tables nothing is read or written
 *  SS-E2 runs: written, replaced by a second write, listed newest first per target, the pending ones oldest first,
 *        their push state set; strings cut to their column length
 *  SS-E3 pages: a manifest's pages replace a target's pages (files and extras are not pages), the other target's
 *        stay
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

trait expStaticStatusEngineChecks
{
    /** @return string the root of the checkout */
    protected static function statusRoot()
    {
        return dirname( __DIR__, 5 );
    }

    /** Drops a table of the test connection (MongoDB: the collection). */
    protected function dropStatusTable( $db, $table )
    {
        $db->query( 'DROP TABLE ' . $table );
    }

    /** SS-E1 */
    protected function checkStatusTables( $db )
    {
        expKernelTableSchema::reset();
        foreach ( array( expStaticStatus::RUN_TABLE, expStaticStatus::PAGE_TABLE ) as $table )
            if ( expKernelTableSchema::exists( $table, $db, true ) === true )
                $this->dropStatusTable( $db, $table );
        expKernelTableSchema::reset();
        $this->assertFalse( expStaticStatus::exists( $db ) );
        $this->assertFalse( expStaticStatus::writeRun( $db, array( 'run_id' => 'r0', 'target' => 'site' ) ), 'nothing is written without the tables' );
        $this->assertSame( array(), expStaticStatus::runs( $db ) );
        $this->assertSame( array(), expStaticStatus::pages( $db, 'site' ) );

        $context = new expUpgradeContext( $db, true, array(), self::statusRoot() . '/share/db_schema.dba' );
        $step = new expUpgradeStepStaticPublishTables();
        $this->assertSame( '6.0.15-static-publish-tables', $step->id() );
        $effect = $step->detect( $context );
        $this->assertSame( expUpgradeStep::MISSING, $effect['effect'], $effect['detail'] );
        $this->assertSame( array( 'created table exp_static_run', 'created table exp_static_page' ), $step->apply( $context ) );
        expKernelTableSchema::reset();
        $effect = $step->detect( $context );
        $this->assertSame( expUpgradeStep::PRESENT, $effect['effect'], $effect['detail'] );
        $this->assertTrue( expStaticStatus::exists( $db ) );
        $this->assertSame( array(), $step->apply( $context ), 'a second apply changes nothing' );
    }

    /** SS-E2, SS-E3 */
    protected function checkStatusWriteAndRead( $db )
    {
        $run = array( 'run_id' => '20261101-120000-aaaaaa', 'target' => 'site', 'kind' => 'publish', 'state' => 'done', 'pages' => 12, 'files' => 40,
                      'bytes' => 123456, 'added' => 40, 'changed' => 0, 'removed' => 0, 'commit_id' => str_repeat( 'a', 40 ), 'tag_name' => 'static/site/1',
                      'push_state' => 'pending', 'run_by' => 'cli alpha', 'started_at' => 1761998400, 'finished_at' => 1761998410,
                      'message' => str_repeat( 'x', 1200 ) );
        $this->assertTrue( expStaticStatus::writeRun( $db, $run ) );
        $this->assertTrue( expStaticStatus::writeRun( $db, array( 'run_id' => '20261101-130000-bbbbbb', 'started_at' => 1762002000, 'tag_name' => 'static/site/2',
                                                                  'push_state' => 'pending' ) + $run ) );
        $this->assertTrue( expStaticStatus::writeRun( $db, array( 'run_id' => '20261101-140000-cccccc', 'target' => 'other', 'started_at' => 1762005600,
                                                                  'push_state' => 'pushed' ) + $run ) );
        $run['state'] = 'done';
        $run['message'] = 'replaced';
        $this->assertTrue( expStaticStatus::writeRun( $db, $run ), 'a second write replaces the run' );

        $runs = expStaticStatus::runs( $db, 'site' );
        $this->assertSame( array( '20261101-130000-bbbbbb', '20261101-120000-aaaaaa' ), array_column( $runs, 'run_id' ), 'newest first' );
        $this->assertSame( 'replaced', $runs[1]['message'] );
        $this->assertSame( 123456, $runs[1]['bytes'] );
        $this->assertSame( 'static/site/1', $runs[1]['tag_name'] );
        $this->assertSame( 1000, mb_strlen( expStaticStatus::normalize( array( 'message' => str_repeat( 'x', 1200 ) ), expStaticStatus::RUN_COLUMNS )['message'] ) );
        $this->assertCount( 3, expStaticStatus::runs( $db ) );
        $this->assertCount( 1, expStaticStatus::runs( $db, 'site', 1 ) );
        $this->assertSame( array( '20261101-120000-aaaaaa', '20261101-130000-bbbbbb' ), array_column( expStaticStatus::pending( $db, 'site' ), 'run_id' ),
                           'pending runs oldest first' );
        $this->assertSame( array(), expStaticStatus::pending( $db, 'other' ) );
        $this->assertSame( 2, expStaticStatus::setPushState( $db, array( '20261101-120000-aaaaaa', '20261101-130000-bbbbbb' ), 'pushed' ) );
        $this->assertSame( array(), expStaticStatus::pending( $db ) );

        $manifest = new expStaticManifest( array( 'run_id' => 'r1', 'built_at' => '2026-11-01T12:00:00+00:00' ) );
        $manifest->add( 'index.html', 'page', str_repeat( '1', 64 ), 100, array( 'url' => '/', 'node' => 2 ) );
        $manifest->add( 'about-us/index.html', 'page', str_repeat( '2', 64 ), 200, array( 'url' => '/about-us', 'node' => 60 ) );
        $manifest->add( 'design/site.css', 'file', str_repeat( '3', 64 ), 300 );
        $manifest->add( 'robots.txt', 'extra', str_repeat( '4', 64 ), 10 );
        $this->assertTrue( expStaticStatus::writePages( $db, 'site', $manifest ) );
        $other = new expStaticManifest( array( 'run_id' => 'r2' ) );
        $other->add( 'index.html', 'page', str_repeat( '5', 64 ), 50, array( 'node' => 2 ) );
        $this->assertTrue( expStaticStatus::writePages( $db, 'other', $other ) );
        $pages = expStaticStatus::pages( $db, 'site' );
        $this->assertSame( array( 'about-us/index.html', 'index.html' ), array_keys( $pages ) );
        $this->assertSame( array( 'target' => 'site', 'path' => 'index.html', 'node_id' => 2, 'hash' => str_repeat( '1', 64 ), 'page_bytes' => 100,
                                  'built_at' => strtotime( '2026-11-01T12:00:00+00:00' ), 'stale' => 0 ), $pages['index.html'] );
        $smaller = new expStaticManifest( array( 'run_id' => 'r3', 'built_at' => '2026-11-01T13:00:00+00:00' ) );
        $smaller->add( 'index.html', 'page', str_repeat( '6', 64 ), 101, array( 'node' => 2 ) );
        $this->assertTrue( expStaticStatus::writePages( $db, 'site', $smaller ) );
        $this->assertSame( array( 'index.html' ), array_keys( expStaticStatus::pages( $db, 'site' ) ), 'the pages are replaced' );
        $this->assertCount( 1, expStaticStatus::pages( $db, 'other' ), 'the other target keeps its pages' );
    }
}
