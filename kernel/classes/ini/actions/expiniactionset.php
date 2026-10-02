<?php
/**
 * File containing the expIniActionSet class.
 *
 * exp:ini set: sets a plain variable (Variable=value) or a hash entry (Variable[key]=value) in one scope's
 * file, creating the file and the block when they are missing (unless --no-create).
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionSet extends expIniActionBase
{
    const NAME = 'set';
    const DESCRIPTION = 'Set a variable (Variable=value) or a hash entry (Variable[key]=value) in one scope';
    const USAGE = "<file>/<Block>/<Variable> <value> <scope>\n<file>/<Block>/<Variable>[<key>] <value> <scope>\n\n" .
                  "  exp:ini set site.ini/SiteSettings/SiteName \"My site\" global\n" .
                  "  exp:ini set site.ini/SiteAccessSettings/RelatedSiteAccessList[admin] admin siteaccess:site\n" .
                  "An array entry (Variable[]) is added with add, not set.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $text = expIniCommandContext::settingText( $setting );
        self::refuseKind( $setting, 'array', "set writes a plain variable or a hash entry; append to an array with: exp:ini add $text <value> <scope>" );
        $value = $c->shift( 'value' );
        $scope = $c->writeScope( $c->shift( 'scope' ) );
        $c->noMoreArguments();

        $editor = $c->editor( $scope, $setting['file'] );
        $editor->set( $setting['block'], $setting['variable'], $value,
                      $setting['kind'] === 'hash' ? $setting['key'] : null );
        $c->data( 'setting', $text );
        $c->data( 'value', $c->display( $setting['variable'], $value ) );
        return $c->commit( $editor, $scope, $setting['file'], "set $text" );
    }
}
