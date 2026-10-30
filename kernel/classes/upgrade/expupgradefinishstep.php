<?php
/**
 * File containing the expUpgradeFinishStep class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A step of the finish stage: what makes the changes of a run live (autoloads, caches, the deploy) and checks the
 * site afterwards. It has work only when the run changed something, or when exp:upgrade:finish is run on its own
 * (the option finish-needed, set by expUpgrade); otherwise it is not-needed. It is never "in place" in the database,
 * so a status without a run shows it as not-needed.
 *
 * @since 6.0.15
 * @package kernel
 */
abstract class expUpgradeFinishStep extends expUpgradeBaseStep
{
    public function stage()
    {
        return 'finish';
    }

    public function detect( expUpgradeContext $context )
    {
        if ( !$context->option( 'finish-needed', false ) )
            return self::effect( self::PRESENT, 'nothing changed in this run' );
        if ( $context->option( 'finish-done-' . $this->id(), false ) )
            return self::effect( self::PRESENT, 'done in this run' );
        return self::effect( self::MISSING, 'the run changed the installation' );
    }

    public function apply( expUpgradeContext $context )
    {
        $done = $this->work( $context );
        $context->setOption( 'finish-done-' . $this->id(), true );
        return $done;
    }

    /**
     * The work; throws on failure.
     *
     * @param expUpgradeContext $context
     * @return string[]
     */
    abstract protected function work( expUpgradeContext $context );

    /**
     * Runs a PHP script of the installation in its own process (the new code, not the classes of this one).
     *
     * @param string $script relative to the installation root, e.g. bin/php/velocity.php
     * @param string[] $arguments
     * @param string[] $output the lines it wrote
     * @return int its exit code
     */
    public static function php( $script, array $arguments, &$output = array() )
    {
        $root = rtrim( eZSys::rootDir(), '/' );
        $cmd = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $root . '/' . $script );
        foreach ( $arguments as $a )
            $cmd .= ' ' . escapeshellarg( $a );
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
            $cmd .= ' --allow-root-user';
        $output = array();
        $code = 0;
        exec( 'cd ' . escapeshellarg( $root ) . ' && ' . $cmd . ' 2>&1', $output, $code );
        // the root warning's countdown and colour codes are not part of what the script says
        $output = array_values( array_filter( array_map( function ( $l ) { return trim( preg_replace( '/\e\[[0-9;]*m/', '', $l ) ); }, $output ),
                                              function ( $l ) { return $l !== '' && strpos( $l, 'great power' ) === false && strpos( $l, 'seconds to break' ) === false; } ) );
        return (int)$code;
    }
}
