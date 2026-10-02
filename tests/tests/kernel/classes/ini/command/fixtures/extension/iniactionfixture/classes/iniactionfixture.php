<?php
/**
 * The classes of the fixture extension of IniCommandExtensionTest: an exp:ini action and a scope provider,
 * as an extension writes them (guide doc/bc/6.0/console-exp-ini.md, "Adding an action" and "Adding a scope").
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

/**
 * exp:ini count <file>[/<Block>] <scope>: how many blocks, or variables in a block, one scope's file has.
 */
class iniActionFixtureCount extends expIniActionBase
{
    const NAME = 'count';
    const DESCRIPTION = 'Count the blocks of a file, or the variables of a block, in one scope';
    const USAGE = "<file>[/<Block>] <scope>\n\n  exp:ini count site.ini global";

    public function run( expIniCommandContext $c )
    {
        $target = $c->fileAndBlock();
        $scope = $c->scope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $editor = $c->editor( $scope, $target['file'] );
        $n = $target['block'] === null ? count( $editor->blocks() ) : count( $editor->variables( $target['block'] ) );
        $c->data( 'count', $n );
        return $c->finish( expIniCommandContext::EXIT_OK, (string)$n );
    }
}

/**
 * A scope "shared": settings/shared/<file>.ini.append.php of the root, e.g. a directory every node of a
 * cluster mounts.
 */
class iniActionFixtureScopeProvider implements expIniScopeProvider
{
    public function scopes( $root )
    {
        return array( new expIniScope( 'shared', 'shared', 'settings/shared', $root, 'Shared by every node',
                                       array( 'policyWritable' => true ) ) );
    }
}
