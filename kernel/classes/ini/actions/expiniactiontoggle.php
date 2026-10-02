<?php
/**
 * File containing the expIniActionToggle class.
 *
 * exp:ini toggle: flips a switch in one scope's file the way the setting writes it: enabled<->disabled,
 * true<->false, yes<->no, on<->off, 1<->0 (the case is kept). A value that is no switch is refused.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionToggle extends expIniActionBase
{
    const NAME = 'toggle';
    const DESCRIPTION = 'Flip a switch in one scope: enabled<->disabled, true<->false, yes<->no, 1<->0';
    const USAGE = "<file>/<Block>/<Variable> <scope>\n\n" .
                  "  exp:ini toggle site.ini/ContentSettings/ViewCaching global          enabled -> disabled\n" .
                  "  exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin     true -> false\n" .
                  "  exp:ini toggle site.ini/DebugSettings/DebugOutput global --dry-run  show the diff only\n" .
                  "When the scope's file does not set the variable, the value in effect is flipped and written\n" .
                  "to that scope. Exit 3 when the value is no switch.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        if ( $setting['kind'] !== 'plain' )
            throw expIniException::usage( 'toggle flips a plain variable (Variable=value)' );
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $editor = $c->editor( $scope, $setting['file'] );
        $before = $editor->get( $setting['block'], $setting['variable'] );
        $after = $editor->toggle( $setting['block'], $setting['variable'] );
        $text = expIniCommandContext::settingText( $setting );
        $c->data( 'setting', $text );
        $c->data( 'before', $before );
        $c->data( 'value', $after );
        $c->line( "$text: " . ( $before === null ? '(not set in ' . $scope->name() . ')' : $before ) . " -> $after" );
        return $c->commit( $editor, $scope, $setting['file'], "toggle $text" );
    }
}
