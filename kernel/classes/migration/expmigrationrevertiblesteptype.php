<?php
/**
 * File containing the expMigrationRevertibleStepType interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A step type that can undo its own step. A YAML migration without "down:" is reversible when each of its steps is
 * of such a type (and none is a loop): expMigrationYaml::down() then calls revert() of each step, the last first.
 *
 * revert() gets the step as written in the migration, its placeholders replaced with the references of the run that
 * rolls back, so a step whose keys use a reference that an earlier step of the same migration sets cannot be reverted
 * this way: the migration needs a "down:" then.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expMigrationRevertibleStepType extends expMigrationStepType
{
    /**
     * Undoes the step. Throws on failure.
     *
     * @param array $step the step's keys and mode
     * @param expMigrationContext $context
     * @return string[] what was done, one line per change
     */
    public function revert( array $step, $context );
}
