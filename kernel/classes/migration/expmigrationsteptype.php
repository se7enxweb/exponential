<?php
/**
 * File containing the expMigrationStepType interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A step type of YAML migrations: what "type: <name>" in a step does. The kernel's step types and those of
 * extensions are registered the same way, in migration.ini:
 *
 *   [StepSettings]
 *   StepTypeList[news_import]=expNewsImportStep
 *
 * The engine (expMigrationYaml) does everything around a step: it checks the step's keys against schema(), replaces
 * the placeholders (${ref.name} ...), evaluates "if:", "environments:" and "engines:", runs loops, and keeps what a
 * step's "references:" names. A step type only sees the step's own keys, with every placeholder replaced, the
 * defaults of its schema filled in and the mode under the key "mode" (when its schema has modes).
 *
 * The schema (schema()) is an array:
 *
 *   description  one line: what the step does
 *   modes        the modes the step takes (create, update, ensure, delete ...); empty or missing: no mode key
 *   mode         the mode when the step names none; missing: a step must name one
 *   keys         key => array( type, required (bool), default, description, values (the allowed values),
 *                modes (the modes the key belongs to), kind (node or object, for type identifier) )
 *                types: string, int, number, bool, scalar, list, map, any, identifier, condition
 *   values       name => description: what "references:" can take from the result of apply()
 *   example      a step in YAML, for the step reference
 *
 * A step type that can undo its step without a "down:" of the migration implements expMigrationRevertibleStepType.
 * expMigrationStepTypeBase has the defaults of everything but name(), schema() and apply().
 *
 * @since 6.0.15
 * @package kernel
 */
interface expMigrationStepType
{
    /**
     * The name of the type, as "type:" names it: lower case letters, digits and _.
     *
     * @return string
     */
    public function name();

    /**
     * The keys of the step (see the interface).
     *
     * @return array
     */
    public function schema();

    /**
     * Problems of a step beyond its schema (two keys that exclude each other ...), checked before anything runs.
     * Values with a placeholder are not replaced yet here.
     *
     * @param array $step the step's keys and mode
     * @return string[]
     */
    public function validate( array $step );

    /**
     * The caches a step touches, by the names of exp:velocity deploy and ezcache (content, template-block ...).
     *
     * @param array $step the step's keys and mode
     * @return string[]
     */
    public function caches( array $step );

    /**
     * What apply() would do, one line per change, without changing anything (the dry run). A placeholder that a
     * step before sets when it runs is still written as it is (${ref.name}).
     *
     * @param array $step the step's keys and mode
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context );

    /**
     * Does the step. Throws on failure; the migration stops there and its transaction is rolled back.
     *
     * @param array $step the step's keys and mode
     * @param expMigrationContext $context
     * @return array array( 'lines' => string[] what was done, 'values' => array name => value for "references:" );
     *               a plain list of lines is taken as the lines
     */
    public function apply( array $step, $context );
}
