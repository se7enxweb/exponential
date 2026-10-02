<?php
/**
 * File containing the expIniActionMove class.
 *
 * exp:ini move: moves one block (with its comments and blank lines, in order) or every block of a file from
 * one scope to another, typically from settings/override or settings/siteaccess/<sa> into an extension's
 * settings, which --create-extension creates and --activate activates. The values in effect of every moved
 * variable are compared for every siteaccess before and after; a move that changes one is rolled back
 * (unless --force). The work is expIniMover's. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionMove extends expIniActionBase
{
    const NAME = 'move';
    const DESCRIPTION = 'Move a block, or every block of a file, from one scope to another (e.g. into an extension)';
    const USAGE = "<file>[/<Block>] <from-scope> <to-scope> [--only=<Variable,...>] [--keep-target] [--force]\n" .
                  "     [--create-extension] [--activate] [--dry-run]\n\n" .
                  "  exp:ini move site.ini/DebugSettings global extension:mysite --dry-run\n" .
                  "  exp:ini move site.ini siteaccess:admin extension:mysite:siteaccess:admin --create-extension --activate\n" .
                  "  exp:ini move site.ini/SiteSettings global siteaccess:site --only=SiteName,SiteURL\n" .
                  "The target is written first, then the block leaves the source (an emptied source keeps its file).\n" .
                  "A variable both blocks have keeps the source's value (--keep-target: the target's).\n" .
                  "Exit 3 when a value in effect would change: the move is rolled back and the file that wins named.";

    public function run( expIniCommandContext $c )
    {
        $target = $c->fileAndBlock();
        $mover = new expIniMover( $c );
        $from = $mover->sourceScope( $c->shift( 'from-scope' ) );
        $toSpec = $c->shift( 'to-scope' );
        $c->noMoreArguments();
        $to = $mover->targetScope( $toSpec, $from );
        $c->data( 'from', $from->name() );
        $c->data( 'to', $to->name() );

        if ( $c->option( 'only' ) !== null && $target['block'] === null )
            throw expIniException::usage( '--only moves variables of one block: give <file>/<Block>' );

        $code = $mover->moveFile( $from, $to, $target['file'], $target['block'] );
        if ( $code !== expIniCommandContext::EXIT_OK )
            return $code;
        return $mover->finish();
    }
}
