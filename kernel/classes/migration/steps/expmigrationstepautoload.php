<?php
/**
 * File containing the expMigrationStepAutoload class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "autoload": regenerates an autoload array, as bin/php/ezpgenerateautoloads.php does.
 *
 *   - type: autoload
 *     scope: extension     extension (the default): var/autoload/ezp_extension.php, the classes of the extensions;
 *                          kernel-override: var/autoload/ezp_override.php, the kernel classes extensions replace
 *
 * For a migration that comes with an extension activated by an earlier step, or a release whose classes the next
 * steps use. The generator runs in a process of its own (expMigrationProcess), the array written is checked with
 * php -l, and the classes are found by this run at once; PHP-FPM and Velocity load them after
 * ./console exp:velocity deploy --apply (which regenerates the arrays itself too). The kernel's own array
 * (autoload/ezp_kernel.php) belongs to the code of a release and is never written by a migration.
 *
 * Regenerating undoes nothing: the step reverts itself by regenerating the array again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepAutoload extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** scope => array( option of the generator, the file it writes ) */
    const SCOPES = array( 'extension' => array( '-e', 'var/autoload/ezp_extension.php' ),
                          'kernel-override' => array( '-o', 'var/autoload/ezp_override.php' ) );

    /** @return string */
    public function name()
    {
        return 'autoload';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Regenerates the autoload array of the extensions or of the kernel classes extensions replace',
            'keys' => array(
                'scope' => array( 'type' => 'string', 'default' => 'extension', 'values' => array_keys( self::SCOPES ),
                                  'description' => 'extension: the classes of the extensions; kernel-override: the kernel classes extensions replace' ),
            ),
            'values' => array(
                'file' => 'the autoload array written, below the root',
            ),
            'example' => "- type: autoload\n  scope: extension\n",
        );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        list( $option, $file ) = self::SCOPES[$step['scope']];
        return array( "regenerate $file (bin/php/ezpgenerateautoloads.php $option)" );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        list( $option, $file ) = self::SCOPES[$step['scope']];
        $root = expMigrationProcess::root( $context );
        list( $code, $output ) = expMigrationProcess::run( 'bin/php/ezpgenerateautoloads.php', array( $option, '-q', '--allow-root-user' ), $root );
        // the generator exits 0 even when it failed, and prints why
        if ( $code !== 0 || preg_match( '/(Fatal error|Exception|Parse error)/', $output ) )
            throw new RuntimeException( "the autoload generator failed (exit $code): " . expMigrationProcess::tail( $output ) );
        list( $lint, $lintOutput ) = expMigrationProcess::run( '-l', array( $root . '/' . $file ), $root, 60 );
        if ( $lint !== 0 )
            throw new RuntimeException( "$file does not parse: " . expMigrationProcess::tail( $lintOutput ) );
        if ( class_exists( 'ezpAutoloader' ) && method_exists( 'ezpAutoloader', 'reset' ) )
            ezpAutoloader::reset();
        return self::result( array( "regenerated $file; ./console exp:velocity deploy --apply makes PHP-FPM and Velocity load it" ), array( 'file' => $file ) );
    }

    /**
     * Regenerates the array again, from the classes that are there after the rollback.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        $result = $this->apply( $step, $context );
        return $result['lines'];
    }
}
