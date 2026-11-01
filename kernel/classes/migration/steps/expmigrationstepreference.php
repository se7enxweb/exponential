<?php
/**
 * File containing the expMigrationStepReference class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "reference": sets references, for the steps and migrations after it in the run.
 *
 *   - type: reference
 *     set:
 *         news_parent: ${idmap.node.news}
 *         news_title: "News of ${run.environment}"
 *
 * Nothing in the installation changes, so there is nothing to undo.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepReference extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'reference';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Sets references (${ref.<name>}) for the steps and migrations after it in the run',
            'keys' => array(
                'set' => array( 'type' => 'map', 'required' => true, 'description' => 'name => value: the references to set' ),
            ),
            'values' => array( '*' => 'every name the step sets, with its value' ),
            'example' => "- type: reference\n  set:\n    news_parent: \${idmap.node.news}\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['set'] ) && is_array( $step['set'] ) )
            foreach ( array_keys( $step['set'] ) as $name )
                if ( !expMigrationReferences::validName( $name ) )
                    $errors[] = "set.$name: a reference name is letters, digits and _, not starting with a digit";
        return $errors;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $lines = array();
        foreach ( (array)$step['set'] as $name => $value )
            $lines[] = "set the reference $name to " . expMigrationStepSchema::show( $value );
        return $lines;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $lines = array();
        foreach ( (array)$step['set'] as $name => $value )
        {
            $context->setReference( (string)$name, $value );
            $lines[] = "set the reference $name";
        }
        return self::result( $lines, (array)$step['set'] );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        return array();
    }
}
