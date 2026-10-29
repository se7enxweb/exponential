<?php
/**
 * File containing the expUpgradeStepLibrariesUpdate class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * composer update of the packages named with the option update (--update=vendor/package,...), with their
 * dependencies: for a security fix in a library before the next release's composer.lock. Has work only when asked;
 * not applicable otherwise. composer.lock changes: commit it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepLibrariesUpdate extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-libraries-update';
    }

    public function stage()
    {
        return 'libraries';
    }

    public function description()
    {
        return 'composer update of the packages named with --update (only when asked)';
    }

    /** @return string[] the packages asked for */
    protected function packages( expUpgradeContext $context )
    {
        $out = array();
        foreach ( expUpgrade::listOption( $context->option( 'update', array() ) ) as $name )
            if ( preg_match( '#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9]([_.-]?[a-z0-9]+)*$#i', $name ) || preg_match( '#^[a-z0-9_.-]+/\*$#i', $name ) )
                $out[] = $name;
            else
                throw new InvalidArgumentException( "--update: not a package name: $name" );
        return $out;
    }

    public function detect( expUpgradeContext $context )
    {
        $packages = $this->packages( $context );
        if ( !$packages )
            return self::effect( self::NOT_APPLICABLE, 'no package named with --update' );
        if ( $context->option( 'libraries-update-done', false ) )
            return self::effect( self::PRESENT, 'updated in this run: ' . implode( ', ', $packages ) );
        return self::effect( self::MISSING, 'update asked for: ' . implode( ', ', $packages ) );
    }

    public function plan( expUpgradeContext $context )
    {
        return array( 'run composer update ' . implode( ' ', $this->packages( $context ) ) . ' --with-dependencies; commit the new composer.lock' );
    }

    public function apply( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        $problem = expUpgradeComposer::ownerProblem( "$root/composer.lock" ) ?: expUpgradeComposer::ownerProblem( $root );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        $args = array_merge( array( 'update' ), $this->packages( $context ), array( '--with-dependencies', '--no-progress' ) );
        if ( !expUpgradeComposer::installedWithDev( $root ) )
            $args[] = '--no-dev';
        $r = expUpgradeComposer::run( $context, $args );
        if ( $r['exit'] !== 0 )
            throw new RuntimeException( 'composer ' . implode( ' ', $args ) . ' failed (exit ' . $r['exit'] . '): '
                                        . preg_replace( '/\s+/', ' ', mb_substr( trim( $r['errors'] ), -600 ) ) );
        $context->setOption( 'libraries-update-done', true );
        return array( 'composer ' . implode( ' ', $args ), 'composer.lock changed: commit it' );
    }
}
