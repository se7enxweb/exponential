<?php
/**
 * File containing the expMigrationStepTypeBase class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The defaults of a step type (expMigrationStepType): no checks beyond the schema, no caches, and a plan of one line
 * that names the step. A step type extends it and writes name(), schema() and apply().
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expMigrationStepTypeBase implements expMigrationStepType
{
    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        return array();
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array();
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        return array( $this->name() . ( isset( $step['mode'] ) ? ' (' . $step['mode'] . ')' : '' ) );
    }

    /**
     * A result of apply(). Pure.
     *
     * @param string[] $lines
     * @param array $values
     * @return array
     */
    protected static function result( array $lines, array $values = array() )
    {
        return array( 'lines' => array_values( array_map( 'strval', $lines ) ), 'values' => $values );
    }
}
