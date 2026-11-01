<?php
/**
 * The checks of YAML migrations that run on every engine the same way: SQLite in process (expMigrationYamlSqliteTest),
 * MySQL/MariaDB and PostgreSQL in the database job (expMigrationYamlDatabaseTest), MongoDB in the live MongoDB suite
 * (expMigrationYamlMongoDBTest). Each gives a connection with the kernel schema and the clean data; the runner's
 * directory, lock and repository come from expMigrationRunnerEngineChecks.
 *
 *  YE-1 the migrations of fixtures/yaml-engine through the runner: a dry run writes nothing; the run writes the notes
 *       of a foreach loop over a reference, resolves the users node by path (through the id map of the environment)
 *       and by remote id on the engine, writes a note only when a condition on both holds, skips one for another
 *       environment, asserts the count; the record has the files' checksums; a second run has nothing to do
 *  YE-2 rollback: the second migration by its step type's revert(), the first by its down: steps, the last first;
 *       the notes and the records are gone
 *  YE-3 transactions: a YAML migration that writes a note and then fails an assert. PostgreSQL, SQLite and
 *       MySQL/MariaDB roll the note back (no schema change in it); MongoDB keeps it (no transactions)
 *
 * The step types wp9_note and wp9_node (expMigrationYamlFakes.php) are registered in migration.ini [StepSettings]
 * StepTypeList for the test, as an extension registers its own.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerEngineChecks.php';
require_once __DIR__ . '/expMigrationYamlFakes.php';

trait expMigrationYamlEngineChecks
{
    use expMigrationRunnerEngineChecks;

    /** @var array|null [StepSettings] StepTypeList before the test */
    protected $yamlSavedTypes;

    /** Registers the test's step types; skips without symfony/yaml. */
    protected function yamlBegin()
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        $ini = eZINI::instance( 'migration.ini' );
        $this->yamlSavedTypes = $ini->hasVariable( 'StepSettings', 'StepTypeList' ) ? $ini->variable( 'StepSettings', 'StepTypeList' ) : array();
        $types = (array)$this->yamlSavedTypes;
        $types['wp9_note'] = 'expMigrationYamlNoteStep';
        $types['wp9_node'] = 'expMigrationYamlNodeStep';
        $ini->setVariable( 'StepSettings', 'StepTypeList', $types );
        expMigrationStepRegistry::forget();
        expMigrationIdMap::forget();
    }

    /** Puts the step types back. */
    protected function yamlEnd()
    {
        if ( $this->yamlSavedTypes !== null )
            eZINI::instance( 'migration.ini' )->setVariable( 'StepSettings', 'StepTypeList', $this->yamlSavedTypes );
        $this->yamlSavedTypes = null;
        expMigrationStepRegistry::forget();
        expMigrationIdMap::forget();
    }

    /** @return expMigration[] the migrations of fixtures/yaml-engine, in order */
    protected function yamlFixtures()
    {
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => __DIR__ . '/fixtures/yaml-engine' ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( ( new expMigrationGraph( $locator->find() ) )->migrations(), $errors );
        $this->assertSame( array(), $errors );
        $this->assertCount( 2, $migrations );
        return $migrations;
    }

    /** @return string[] the texts of the notes, by id */
    protected static function yamlNotes( $db )
    {
        $texts = array();
        foreach ( (array)$db->arrayQuery( 'SELECT id, note_text FROM ' . expMigrationYamlNoteStep::TABLE ) as $row )
            $texts[(int)$row['id']] = (string)$row['note_text'];
        ksort( $texts );
        return $texts;
    }

    /**
     * YE-1 and YE-2
     *
     * @param eZDBInterface $db
     */
    protected function checkYamlRunAndRollback( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->dropRunnerTable( $db, expMigrationYamlNoteStep::TABLE );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id LIKE '20261101%wp9%'" );
        $db->query( 'DELETE FROM ' . expMigrationRecord::RUN_TABLE );
        mkdir( $this->runnerDir() . '/migrations/idmap', 0775, true );
        file_put_contents( $this->runnerDir() . '/migrations/idmap/testing.json', expMigrationIdMap::encode( array( 'node' => array( 'users' => 'path:users' ) ) ) );
        $migrations = $this->yamlFixtures();

        $dry = ( new expMigrationRunner( $this->runnerContext( $db, false ) ) )->run( $migrations );
        $this->assertSame( 0, $dry['exit'], implode( '; ', $dry['errors'] ) );
        $this->assertSame( array( 'would-apply', 'would-apply' ), array_column( $dry['migrations'], 'action' ) );
        $this->assertContains( "  write the note note 0 in eng-US on $engine", $dry['migrations'][0]['plan'] );
        expKernelTableSchema::reset();
        $this->assertNotTrue( expKernelTableSchema::exists( expMigrationYamlNoteStep::TABLE, $db, true ), 'the dry run created nothing' );

        $context = $this->runnerContext( $db );
        $report = ( new expMigrationRunner( $context ) )->run( $migrations );
        $this->assertSame( 0, $report['exit'], implode( '; ', $report['errors'] ) . ' ' . json_encode( array_column( $report['migrations'], 'messages' ) ) );
        $this->assertSame( array( 'applied', 'applied' ), array_column( $report['migrations'], 'action' ) );
        $this->assertSame( array( 1 => "note 0 in eng-US on $engine", 2 => "note 1 in ger-DE on $engine", 3 => 'users is node 5 of object 4',
                                  4 => "after the notes on $engine" ), self::yamlNotes( $db ) );
        $this->assertSame( 5, $context->reference( 'users_node' ) );
        $this->assertSame( 2, $context->reference( 'last_note' ) );
        $this->assertContains( 'skipped steps[5] (wp9_note): the condition is false', $report['migrations'][0]['messages'] );
        foreach ( $migrations as $migration )
        {
            $row = expMigrationRecord::get( $db, 'site', $migration->id() );
            $this->assertSame( 'done', $row['state'] );
            $this->assertSame( $migration->checksum(), $row['checksum'] );
            $this->assertSame( 'yml', pathinfo( $row['file_path'], PATHINFO_EXTENSION ) );
        }
        $again = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $this->yamlFixtures() );
        $this->assertSame( 0, $again['summary']['applied'] );
        $this->assertSame( 2, $again['summary']['done'] );

        $this->assertNotNull( $migrations[0]->downSteps(), 'reverted by its down: steps' );
        $this->assertTrue( $migrations[1]->reversible(), 'reverted by its step type' );
        $rollback = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->rollback( $this->yamlFixtures() );
        $this->assertSame( 0, $rollback['exit'], implode( '; ', $rollback['errors'] ) . ' ' . json_encode( array_column( $rollback['migrations'], 'messages' ) ) );
        $this->assertSame( array( 'undone', 'undone' ), array_column( $rollback['migrations'], 'action' ) );
        $this->assertSame( array(), self::yamlNotes( $db ) );
        foreach ( $migrations as $migration )
            $this->assertNull( expMigrationRecord::get( $db, 'site', $migration->id() ) );
        $this->dropRunnerTable( $db, expMigrationYamlNoteStep::TABLE );
    }

    /**
     * YE-3
     *
     * @param eZDBInterface $db
     */
    protected function checkYamlTransactions( $db )
    {
        $engine = expKernelTableSchema::type( $db );
        $this->dropRunnerTable( $db, expMigrationYamlNoteStep::TABLE );
        // the table first, so the failing migration changes data only
        expMigrationYamlNoteStep::ensureTable( $this->runnerContext( $db ) );
        $dir = $this->runnerDir() . '/fail';
        mkdir( $dir );
        file_put_contents( "$dir/20261101100200_wp9_fail.yml", "# description: Writes a note and fails\n- type: wp9_note\n  text: kept only without transactions\n"
                                                              . "- type: assert\n  that: false\n  message: failed on purpose after the note\n" );
        $locator = new expMigrationLocator( array( array( 'source' => 'site', 'path' => $dir ) ) );
        $errors = array();
        $migrations = expMigrationRunner::load( $locator->find(), $errors );
        $this->assertSame( array(), $errors );

        $report = ( new expMigrationRunner( $this->runnerContext( $db ) ) )->run( $migrations );
        $this->assertSame( 1, $report['exit'] );
        $entry = $report['migrations'][0];
        $this->assertSame( 'failed', $entry['action'] );
        $this->assertContains( 'steps[1] (assert): failed on purpose after the note', $entry['messages'] );
        $this->assertSame( expMigrationContext::transactionModeOf( $engine ), $entry['transaction'] );
        $this->assertSame( 'failed', expMigrationRecord::get( $db, 'site', '20261101100200_wp9_fail' )['state'] );
        $notes = self::yamlNotes( $db );
        if ( expMigrationContext::transactionModeOf( $engine ) === expMigrationContext::TRANSACTION_NONE )
            $this->assertSame( array( 1 => 'kept only without transactions' ), $notes, "$engine: no transaction, the note stays" );
        else
            $this->assertSame( array(), $notes, "$engine: the note was rolled back" );
        $this->assertSame( 0, $db->transactionCounter(), 'no transaction left open' );
        $this->dropRunnerTable( $db, expMigrationYamlNoteStep::TABLE );
        $db->query( 'DELETE FROM ' . expMigrationRecord::TABLE . " WHERE migration_id = '20261101100200_wp9_fail'" );
    }
}
