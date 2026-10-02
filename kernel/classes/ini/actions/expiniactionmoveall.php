<?php
/**
 * File containing the expIniActionMoveAll class.
 *
 * exp:ini move-all: moves every INI file of a scope (or those --files names) into another scope, block by
 * block, one transaction per file as exp:ini move does; for example everything in settings/siteaccess/admin
 * into extension:mysite:siteaccess:admin. It stops at the first file whose move is refused; the files moved
 * before it stay moved. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionMoveAll extends expIniActionBase
{
    const NAME = 'move-all';
    const DESCRIPTION = 'Move every INI file of a scope into another scope (e.g. a siteaccess into an extension)';
    const USAGE = "<from-scope> <to-scope> [--files=site,content,...] [--keep-target] [--force] [--create-extension] [--activate] [--dry-run]\n\n" .
                  "  exp:ini move-all siteaccess:admin extension:mysite:siteaccess:admin --create-extension --activate --dry-run\n" .
                  "  exp:ini move-all global extension:mysite --files=design,menu\n" .
                  "One transaction per file; it stops at the first refused file (those before it stay moved).";

    public function run( expIniCommandContext $c )
    {
        $mover = new expIniMover( $c );
        $from = $mover->sourceScope( $c->shift( 'from-scope' ) );
        $toSpec = $c->shift( 'to-scope' );
        $c->noMoreArguments();
        if ( $c->option( 'only' ) !== null )
            throw expIniException::usage( '--only belongs to exp:ini move <file>/<Block>' );
        $to = $mover->targetScope( $toSpec, $from );
        $c->data( 'from', $from->name() );
        $c->data( 'to', $to->name() );

        $files = expIniMover::filesOf( $from );
        if ( $c->option( 'files' ) !== null )
        {
            $wanted = array_values( array_filter( array_map( function ( $f ) {
                return preg_replace( '/\.ini(\.append(\.php)?)?$/', '', trim( $f ) );
            }, explode( ',', $c->option( 'files' ) ) ), 'strlen' ) );
            $missing = array_diff( $wanted, $files );
            if ( $missing )
                return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, 'Not found: ' . $from->name() . ' has no '
                                                                        . implode( '.ini, ', $missing ) . '.ini' );
            $files = $wanted;
        }
        if ( !$files )
            return $c->finish( expIniCommandContext::EXIT_OK, 'Nothing to move: ' . $from->name() . ' has no INI files' );

        foreach ( $files as $file )
        {
            $code = $mover->moveFile( $from, $to, $file );
            if ( $code !== expIniCommandContext::EXIT_OK )
            {
                $t = $mover->totals();
                $c->data( 'totals', $t );
                $c->data( 'files', $mover->report() );
                if ( $t['files'] > 0 && !$c->isDryRun() )
                {
                    $c->line( sprintf( 'Moved before it stopped: %d block(s), %d variable(s), %d file(s)', $t['blocks'], $t['variables'], $t['files'] ) );
                    $c->afterWrite();
                }
                return $code;
            }
        }
        return $mover->finish();
    }
}
