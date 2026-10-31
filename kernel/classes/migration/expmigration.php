<?php
/**
 * File containing the expMigration interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One migration: a change to an installation (content, classes, roles, settings, schema) that is written once, kept
 * in a file under version control and applied to every installation of the site in the same order.
 *
 * Migrations come from four sources: the kernel, the site (migrations/), active extensions
 * (extension/<name>/migrations/) and site packages. Each one has a unique id; the id of a migration file is its file
 * name without the type (20261031120000_add_news_class). They run in the order of their ids (the timestamp first),
 * after the migrations each one requires.
 *
 * The state of a migration is either record-based (it ran or it did not; a site migration) or detect-based (it asks
 * the installation whether its change is there; a kernel upgrade step). state() hides the difference from the runner.
 * The context passed to state(), plan(), up() and down() is the run's context: the database, the environment, the
 * run's user and options.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expMigration
{
    /** Applied (by this installation's migration runs). */
    const DONE = 'done';
    /** Not applied yet: the migration has work to do. */
    const PENDING = 'pending';
    /** Not for this installation (environment, engine) or its change is already in place without a run. */
    const NOT_NEEDED = 'not-needed';
    /** The last run failed. */
    const FAILED = 'failed';
    /** The state cannot be told (no tracking table yet, a detection that cannot read the installation). */
    const UNKNOWN = 'unknown';

    /**
     * The unique id, e.g. 20261031120000_add_news_class (a site, extension or package migration) or the id of a
     * kernel upgrade step.
     *
     * @return string
     */
    public function id();

    /**
     * Where the migration comes from: kernel, site, extension:<name> or package:<name>.
     *
     * @return string
     */
    public function source();

    /**
     * One line that says what the migration does.
     *
     * @return string
     */
    public function description();

    /**
     * The environments the migration runs in ([AdminEnvironment] Level: development, staging, production ...); an
     * entry !name excludes one. Empty: every environment.
     *
     * @return string[]
     */
    public function environments();

    /**
     * What has to be in place first: ids of other migrations, and extension:<name> for an extension that must be
     * active.
     *
     * @return string[]
     */
    public function requires();

    /**
     * Whether the installation is put into maintenance mode while the migration runs.
     *
     * @return bool
     */
    public function maintenance();

    /**
     * The database engines the migration is for: mysql, postgresql, sqlite, oracle, mongodb; array( '*' ) for every
     * engine.
     *
     * @return string[]
     */
    public function engines();

    /**
     * The state on this installation: one of DONE, PENDING, NOT_NEEDED, FAILED, UNKNOWN.
     *
     * @param mixed $context the run's context
     * @return string
     */
    public function state( $context );

    /**
     * What up() would do, one line per change, without changing anything (the dry run).
     *
     * @param mixed $context the run's context
     * @return string[]
     */
    public function plan( $context );

    /**
     * Applies the migration. Throws on failure; the runner records the failure and restores the caption taken
     * before the run.
     *
     * @param mixed $context the run's context
     * @return string[] what was done, one line per change
     */
    public function up( $context );

    /**
     * Whether down() can undo the migration. A migration without down steps is undone only by restoring a caption.
     *
     * @return bool
     */
    public function reversible();

    /**
     * Undoes the migration (only called when reversible() is true). Throws on failure.
     *
     * @param mixed $context the run's context
     * @return string[] what was done, one line per change
     */
    public function down( $context );
}
