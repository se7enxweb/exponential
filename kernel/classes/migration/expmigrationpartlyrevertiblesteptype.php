<?php
/**
 * File containing the expMigrationPartlyRevertibleStepType interface.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A step type that can undo some of its steps but not all: a content step that created an object with a remote id can
 * remove it again, one that updated an object cannot know the content before. expMigrationYaml::reversible() asks
 * revertible() for each step of a migration without "down:"; a migration with a step that answers false needs a
 * "down:" (or a rollback to its caption).
 *
 * @since 6.0.15
 * @package kernel
 */
interface expMigrationPartlyRevertibleStepType extends expMigrationRevertibleStepType
{
    /**
     * Whether revert() can undo this step. Pure: the keys are as written, placeholders not replaced, the defaults of
     * the schema and the mode filled in.
     *
     * @param array $step the step's keys and mode
     * @return bool
     */
    public function revertible( array $step );
}
