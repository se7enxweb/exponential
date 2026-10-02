<?php
/**
 * File containing the expIniActionActions class.
 *
 * exp:ini actions: the actions of the command, the built-in ones and those extensions register in
 * ini.ini [IniCommandSettings] Actions[], with their aliases and registrations that cannot work.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionActions extends expIniActionBase
{
    const NAME = 'actions';
    const DESCRIPTION = 'List the actions: built-in and registered by extensions in ini.ini';
    const USAGE = "(no arguments)\n\n  exp:ini actions\n  exp:ini actions --json\n" .
                  "exp:ini help <action> shows one action's usage.";

    public function run( expIniCommandContext $c )
    {
        $c->noMoreArguments();
        $registry = $c->registry();
        $aliases = array();
        foreach ( $registry->aliases() as $alias => $name )
            $aliases[$name][] = $alias;

        $rows = array();
        foreach ( $registry->actions() as $name => $class )
        {
            $described = $registry->describe( $name );
            $row = array( 'name' => $name, 'class' => $class, 'builtin' => $described['builtin'],
                          'aliases' => isset( $aliases[$name] ) ? $aliases[$name] : array(),
                          'description' => $described['description'], 'usage' => $described['usage'], 'ok' => $described['ok'] );
            $rows[] = $row;
            $c->line( sprintf( '%-10s %s%s', $name, $row['ok'] ? '' : 'BROKEN: ', $row['description'] )
                      . ( $row['aliases'] ? ' (alias: ' . implode( ', ', $row['aliases'] ) . ')' : '' )
                      . ( $row['builtin'] ? '' : ' [' . $class . ']' ) );
        }
        $c->data( 'actions', $rows );
        $c->data( 'problems', $registry->problems() );
        $builtIn = count( array_filter( $rows, function ( $r ) { return $r['builtin']; } ) );
        return $c->finish( expIniCommandContext::EXIT_OK, count( $rows ) . ' actions: ' . $builtIn . ' built-in, '
                                                          . ( count( $rows ) - $builtIn ) . ' registered' );
    }
}
