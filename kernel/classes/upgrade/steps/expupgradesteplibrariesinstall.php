<?php
/**
 * File containing the expUpgradeStepLibrariesInstall class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The libraries vendor/ holds are the ones composer.lock names: composer install (with the dev packages only when
 * vendor/ had them), with the security audit (composer audit --locked) before and after, which reports and never
 * stops the run. Refused when Composer reports local changes in vendor/ (composer status), unless the option
 * allow-vendor-changes is given, and when the process is not the owner of the installation. Not applicable without
 * composer.json.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepLibrariesInstall extends expUpgradeBaseStep
{
    public function id()
    {
        return '6.0.15-libraries-install';
    }

    public function stage()
    {
        return 'libraries';
    }

    public function description()
    {
        return 'The libraries in vendor/ are the ones composer.lock names (composer install, audit before and after)';
    }

    public function after()
    {
        return array( '6.0.15-libraries-update' );
    }

    public function detect( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        if ( !is_file( "$root/composer.json" ) )
            return self::effect( self::NOT_APPLICABLE, 'no composer.json' );
        $diff = expUpgradeComposer::lockDifferences( $root );
        if ( $diff === null )
            return self::effect( self::UNKNOWN, 'no composer.lock: run composer update once, by hand, and commit the lock' );
        if ( !$diff )
            return self::effect( self::PRESENT, 'vendor/ matches composer.lock' );
        return self::effect( self::MISSING, count( $diff ) . ' package(s) differ from composer.lock: ' . implode( ', ', array_slice( array_keys( $diff ), 0, 8 ) ) . ( count( $diff ) > 8 ? ', ...' : '' ) );
    }

    /** @return string[] the install command's arguments */
    protected function installArguments( expUpgradeContext $context )
    {
        $args = array( 'install', '--no-progress' );
        if ( !expUpgradeComposer::installedWithDev( (string)$context->option( 'root' ) ) )
            $args[] = '--no-dev';
        return $args;
    }

    /** @return array name => path of the packages to change that are git checkouts (refused without allow-vendor-changes) */
    protected function checkouts( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        return expUpgradeComposer::gitCheckouts( $root, array_keys( (array)expUpgradeComposer::lockDifferences( $root ) ) );
    }

    public function plan( expUpgradeContext $context )
    {
        $lines = array();
        $checkouts = $context->option( 'allow-vendor-changes', false ) ? array() : $this->checkouts( $context );
        foreach ( (array)expUpgradeComposer::lockDifferences( (string)$context->option( 'root' ) ) as $name => $change )
            $lines[] = "$name: $change" . ( isset( $checkouts[$name] ) ? " REFUSED: {$checkouts[$name]} is a git checkout" : '' );
        if ( $checkouts )
            $lines[] = count( $checkouts ) . ' package(s) are git checkouts: the install is refused (move them with the code stage, --repos=; or --allow-vendor-changes)';
        $lines[] = 'run composer ' . implode( ' ', $this->installArguments( $context ) );
        if ( !$context->option( 'no-audit', false ) )
        {
            $audit = expUpgradeComposer::audit( $context );
            if ( $audit === null )
                $lines[] = 'audit: composer audit could not run';
            else
                foreach ( $audit ? $audit : array( 'no advisories' ) as $line )
                    $lines[] = "audit: $line";
        }
        return $lines;
    }

    public function apply( expUpgradeContext $context )
    {
        $root = (string)$context->option( 'root' );
        $problem = expUpgradeComposer::ownerProblem( "$root/vendor" ) ?: expUpgradeComposer::ownerProblem( $root );
        if ( $problem !== '' )
            throw new RuntimeException( $problem );
        $done = array();
        if ( !$context->option( 'no-audit', false ) )
            foreach ( (array)expUpgradeComposer::audit( $context ) as $line )
                $done[] = "audit before: $line";
        $checkouts = $context->option( 'allow-vendor-changes', false ) ? array() : $this->checkouts( $context );
        if ( $checkouts )
            throw new RuntimeException( 'composer install would replace ' . count( $checkouts ) . ' git checkout(s), nothing was installed: '
                                        . implode( ', ', array_map( function ( $n, $p ) { return "$n ($p)"; }, array_keys( $checkouts ), $checkouts ) )
                                        . ' (move them with the code stage, --repos=; or --allow-vendor-changes)' );
        if ( is_dir( "$root/vendor" ) && !$context->option( 'allow-vendor-changes', false ) )
        {
            $status = expUpgradeComposer::run( $context, array( 'status', '--verbose' ) );
            if ( $status['exit'] !== 0 )
                throw new RuntimeException( 'Composer reports local changes in vendor/, nothing was installed: '
                                            . preg_replace( '/\s+/', ' ', trim( $status['output'] . ' ' . $status['errors'] ) )
                                            . ' (--allow-vendor-changes installs over them)' );
        }
        $args = $this->installArguments( $context );
        $r = expUpgradeComposer::run( $context, $args );
        if ( $r['exit'] !== 0 )
            throw new RuntimeException( 'composer ' . implode( ' ', $args ) . ' failed (exit ' . $r['exit'] . '): '
                                        . preg_replace( '/\s+/', ' ', mb_substr( trim( $r['errors'] ), -600 ) ) );
        $done[] = 'composer ' . implode( ' ', $args );
        if ( !$context->option( 'no-audit', false ) )
        {
            $audit = expUpgradeComposer::audit( $context );
            foreach ( $audit ? $audit : array( $audit === null ? 'could not run' : 'no advisories' ) as $line )
                $done[] = "audit after: $line";
        }
        return $done;
    }
}
