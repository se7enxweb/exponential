<?php
/**
 * File containing the expIniActionList class.
 *
 * exp:ini list: the blocks of an INI file, or the variables of one block, in effect (every file merged, for
 * the current siteaccess) or as one scope's file alone has them. Secrets are masked unless --show-secrets.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionList extends expIniActionBase
{
    const NAME = 'list';
    const DESCRIPTION = 'List the blocks of a file, or the variables of a block (in effect or in one scope)';
    const USAGE = "<file>[/<Block>] [scope]\n\n" .
                  "  exp:ini list site.ini                        the blocks in effect\n" .
                  "  exp:ini list site.ini/DatabaseSettings       its variables in effect, secrets masked\n" .
                  "  exp:ini list site.ini global                 the blocks of settings/override/site.ini.append.php\n" .
                  "  exp:ini list site.ini/SiteSettings siteaccess:admin\n" .
                  "Exit 2 when the file or the block is not there.";

    public function run( expIniCommandContext $c )
    {
        $target = $c->fileAndBlock();
        $spec = $c->shift();
        $c->noMoreArguments();
        $file = $target['file'];
        $block = $target['block'];

        if ( $spec === null )
        {
            $c->requireOwnInstallation( 'list without a scope' );
            if ( !eZINI::exists( $file . '.ini' ) )
                return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: there is no $file.ini" );
            $ini = eZINI::instance( $file . '.ini' );
            $from = 'in effect';
            $blocks = array_keys( $ini->groups() );
            $variables = function ( $b ) use ( $ini ) { return $ini->group( $b ); };
        }
        else
        {
            $scope = $c->scope( $spec );
            $editor = $c->editor( $scope, $file );
            $from = 'in ' . $scope->name();
            $c->data( 'scope', $scope->name() );
            if ( !is_file( $scope->path( $file ) ) )
                return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: " . $scope->name()
                                                                        . " has no $file.ini file (" . $scope->relativePath( $file ) . ')' );
            $c->data( 'path', $scope->relativePath( $file ) );
            $blocks = $editor->blocks();
            $variables = function ( $b ) use ( $editor ) { return $editor->variables( $b ); };
        }

        $c->data( 'file', $file );
        if ( $block === null )
        {
            $c->data( 'blocks', array_values( $blocks ) );
            foreach ( $blocks as $b )
                $c->line( "[$b]" );
            return $c->finish( expIniCommandContext::EXIT_OK, count( $blocks ) . ' block' . ( count( $blocks ) === 1 ? '' : 's' )
                                                              . " in $file.ini $from" );
        }

        if ( !in_array( $block, $blocks, true ) )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $file.ini has no block [$block] $from" );

        $out = array();
        $c->line( "[$block]" );
        foreach ( (array)call_user_func( $variables, $block ) as $name => $value )
        {
            $out[$name] = $c->display( $name, $value );
            foreach ( $this->valueLines( $c, $name, $value ) as $l )
                $c->line( $l );
        }
        $c->data( 'block', $block );
        $c->data( 'variables', $out );
        return $c->finish( expIniCommandContext::EXIT_OK, count( $out ) . ' variable' . ( count( $out ) === 1 ? '' : 's' )
                                                          . " in [$block] of $file.ini $from" );
    }
}
