<?php
/**
 * File containing the expIniActionScopes class.
 *
 * exp:ini scopes: the scopes this installation has (global, default, every siteaccess, every extension and
 * the extension siteaccess directories, and those of the scope providers in ini.ini), with the directory each
 * writes, whether it exists and whether it may be written. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionScopes extends expIniActionBase
{
    const NAME = 'scopes';
    const DESCRIPTION = 'List the scopes this installation has and the directory each writes';
    const USAGE = "[kind]\n\n" .
                  "  exp:ini scopes\n" .
                  "  exp:ini scopes siteaccess        only one kind: global, default, siteaccess, extension,\n" .
                  "                                   extension-siteaccess or a scope provider's own\n" .
                  "  exp:ini scopes --json";

    public function run( expIniCommandContext $c )
    {
        $kind = $c->shift();
        $c->noMoreArguments();

        $rows = array();
        foreach ( expIniEditor::scopes() as $scope )
        {
            if ( $kind !== null && $scope->kind() !== $kind )
                continue;
            $rows[] = array( 'name' => $scope->name(), 'kind' => $scope->kind(), 'dir' => $scope->dir(),
                             'label' => $scope->label(), 'exists' => (bool)$scope->exists(),
                             'writable' => (bool)$scope->writable(), 'active' => $scope->isActive() );
        }
        $c->data( 'scopes', $rows );
        $c->data( 'providers', $c->registry()->scopeProviders() );

        foreach ( $rows as $r )
        {
            $flags = array();
            if ( !$r['exists'] )
                $flags[] = 'no directory yet';
            if ( !$r['writable'] )
                $flags[] = 'not writable';
            if ( $r['active'] === false )
                $flags[] = 'inactive extension';
            $c->line( sprintf( '%-44s %-20s %s%s', $r['name'], $r['kind'], $r['dir'],
                               $flags ? '  (' . implode( ', ', $flags ) . ')' : '' ) );
        }
        return $c->finish( expIniCommandContext::EXIT_OK, expIniCommandContext::counted( count( $rows ), 'scope' )
                                                          . '; providers: ' . implode( ', ', $c->registry()->scopeProviders() ) );
    }
}
