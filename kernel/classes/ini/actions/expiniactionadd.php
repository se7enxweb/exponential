<?php
/**
 * File containing the expIniActionAdd class.
 *
 * exp:ini add: appends a value to an array (Variable[]=value) in one scope's file, creating the file and
 * the block when they are missing (unless --no-create). A value the scope's file already lists is not
 * added twice. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionAdd extends expIniActionBase
{
    const NAME = 'add';
    const DESCRIPTION = 'Append a value to an array (Variable[]=value) in one scope';
    const USAGE = "<file>/<Block>/<Variable>[] <value> <scope>\n\n" .
                  "  exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] myext global\n" .
                  "  exp:ini add site.ini/SiteAccessSettings/AvailableSiteAccessList[] intranet global\n" .
                  "The [] may be left out. A value the scope's file already has is not added again.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        self::refuseKind( $setting, 'hash', 'add appends to an array (Variable[]); set a hash entry with: exp:ini set '
                                            . expIniCommandContext::settingText( $setting ) . ' <value> <scope>' );
        $setting['kind'] = 'array';
        $text = expIniCommandContext::settingText( $setting );
        $value = $c->shift( 'value' );
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $editor = $c->editor( $scope, $setting['file'] );
        $c->data( 'setting', $text );
        $c->data( 'value', $c->display( $setting['variable'], $value ) );
        if ( self::hasValue( $editor->get( $setting['block'], $setting['variable'] ), $value ) )
        {
            $c->data( 'changed', false );
            return $c->finish( expIniCommandContext::EXIT_OK, "Nothing to change: $text in " . $scope->name() . ' already has that value' );
        }
        $editor->add( $setting['block'], $setting['variable'], $value );
        return $c->commit( $editor, $scope, $setting['file'], "add to $text" );
    }
}
