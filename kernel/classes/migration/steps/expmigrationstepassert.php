<?php
/**
 * File containing the expMigrationStepAssert class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "assert": stops the migration (it fails, and its transaction is rolled back) when a condition is
 * false. The condition is written as an "if:" (expMigrationCondition).
 *
 *   - type: assert
 *     that: { value: "${ref.news_count}", gt: 0 }
 *     message: The news folder has no articles
 *
 * Nothing in the installation changes, so there is nothing to undo.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepAssert extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'assert';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Fails the migration when a condition is false',
            'keys' => array(
                'that' => array( 'type' => 'condition', 'required' => true, 'description' => 'the condition that must be true (as an "if:")' ),
                'message' => array( 'type' => 'string', 'description' => 'the message of the failure (default: the condition)' ),
            ),
            'example' => "- type: assert\n  that: { ref: news_parent, set: true }\n  message: The news folder is missing\n",
        );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        return array( 'check that ' . expMigrationCondition::describe( $step['that'] ) );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     * @throws RuntimeException when the condition is false
     */
    public function apply( array $step, $context )
    {
        $scope = array( 'ref' => $context->references(), 'run' => array( 'id' => $context->runId(), 'environment' => $context->environment(),
                                                                         'engine' => $context->engine() ) );
        if ( !expMigrationCondition::evaluate( $step['that'], $scope, true ) )
            throw new RuntimeException( isset( $step['message'] ) && (string)$step['message'] !== ''
                                        ? (string)$step['message']
                                        : 'the assertion is false: ' . expMigrationCondition::describe( $step['that'] ) );
        return self::result( array( 'checked that ' . expMigrationCondition::describe( $step['that'] ) ) );
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
