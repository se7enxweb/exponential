<?php
/**
 * File containing the expUpgradeStep interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One upgrade step of exp:upgrade: something an installation of an older version needs once (a table, a column, a
 * policy, a row), with a way to tell from the installation itself whether it is there.
 *
 * A step never trusts the record alone: detect() looks at the real database, settings or files, so a step that was
 * applied by hand (the SQL file, the old script) is seen as present, and one whose effect went missing again is seen
 * as missing. apply() must be idempotent and must complete a half-applied state.
 *
 * Steps are listed in upgrade.ini [UpgradeSettings] StepList[] (kernel) and ExtensionStepList[] (extensions), by
 * class name. Guide for extension authors: doc/eng-us/guides/upgrade-steps-for-extensions.md.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expUpgradeStep
{
    /** The effect of the step is in place. */
    const PRESENT = 'present';
    /** The effect is missing: the step has work to do. */
    const MISSING = 'missing';
    /** The step does not apply to this installation (engine, missing feature, extension not active). */
    const NOT_APPLICABLE = 'not-applicable';
    /** It cannot be told (catalogue not readable, settings incomplete). */
    const UNKNOWN = 'unknown';

    /**
     * The stable id, never changed once released: "<version>-<what>", e.g. 6.0.15-user-audit-table.
     *
     * @return string
     */
    public function id();

    /**
     * The stage: database (schema and data) or scripts (the PHP upgrade steps).
     *
     * @return string
     */
    public function stage();

    /**
     * The release that brought the step, e.g. 6.0.15.
     *
     * @return string
     */
    public function version();

    /**
     * One line: what the step does.
     *
     * @return string
     */
    public function description();

    /**
     * The engines the step is for: mysql, postgresql, sqlite, oracle, mongodb; array( '*' ) for every engine.
     *
     * @return string[]
     */
    public function engines();

    /**
     * Step ids that must be in place before this one (in the same or an earlier stage).
     *
     * @return string[]
     */
    public function after();

    /**
     * Whether the step runs only when named with --only (a repair that is not part of every upgrade).
     *
     * @return bool
     */
    public function optional();

    /**
     * What the installation holds: array( 'effect' => PRESENT|MISSING|NOT_APPLICABLE|UNKNOWN, 'detail' => string ).
     *
     * @param expUpgradeContext $context
     * @return array
     */
    public function detect( expUpgradeContext $context );

    /**
     * What apply() would change, one line per change, for the dry run. Changes nothing.
     *
     * @param expUpgradeContext $context
     * @return string[]
     */
    public function plan( expUpgradeContext $context );

    /**
     * Makes the change. Returns what was done, one line per change; throws on failure.
     *
     * @param expUpgradeContext $context
     * @return string[]
     * @throws Exception
     */
    public function apply( expUpgradeContext $context );
}
