<?php
/**
 * File containing the expIniActionClear class.
 *
 * exp:ini clear: writes the array reset line Variable[] in one scope's file, so the values the files loaded
 * before it give are dropped and only what follows counts. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionClear extends expIniActionBase
{
    const NAME = 'clear';
    const DESCRIPTION = 'Write the array reset line Variable[] in one scope (earlier files\' values are dropped)';
    const USAGE = "<file>/<Block>/<Variable>[] <scope>\n\n" .
                  "  exp:ini clear site.ini/SiteAccessSettings/AvailableSiteAccessList[] global\n" .
                  "  exp:ini clear site.ini/ExtensionSettings/ActiveAccessExtensions[] siteaccess:admin\n" .
                  "The values the same scope's file lists stay after the reset line.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        self::refuseKind( $setting, 'hash', 'clear writes Variable[]: give the variable without a key' );
        $setting['kind'] = 'array';
        $text = expIniCommandContext::settingText( $setting );
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $editor = $c->editor( $scope, $setting['file'] );
        $editor->clearArray( $setting['block'], $setting['variable'] );
        $c->data( 'setting', $text );
        return $c->commit( $editor, $scope, $setting['file'], "clear $text" );
    }
}
