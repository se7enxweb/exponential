<?php
/**
 * File containing the expIniActionWhere class.
 *
 * exp:ini where: every file that sets a variable, in the order eZINI loads them (the last one wins for a plain
 * value, arrays add up unless a file resets them), with the scope that writes each and the value in effect.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionWhere extends expIniActionBase
{
    const NAME = 'where';
    const DESCRIPTION = 'List every file that sets a variable, in load order, and the value in effect';
    const USAGE = "<file>/<Block>/<Variable> [siteaccess]\n\n" .
                  "  exp:ini where site.ini/SiteSettings/SiteName\n" .
                  "  exp:ini where site.ini/ExtensionSettings/ActiveAccessExtensions admin\n" .
                  "The siteaccess is the load order to follow (default: the current one, or -s).\n" .
                  "Exit 2 when no file sets it.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $siteAccess = $c->shift();
        $c->noMoreArguments();
        $c->requireOwnInstallation( 'where' );
        if ( $siteAccess !== null && strpos( $siteAccess, 'siteaccess:' ) === 0 )
            $siteAccess = substr( $siteAccess, strlen( 'siteaccess:' ) );

        $where = expIniLocator::where( $setting['file'], $setting['block'], $setting['variable'], $siteAccess );
        $variable = $setting['variable'];
        $text = $setting['file'] . '.ini/' . $setting['block'] . '/' . $variable;

        $files = array();
        foreach ( (array)$where['files'] as $f )
        {
            $f['value'] = $c->display( $variable, $f['value'] );
            $files[] = $f;
        }
        $c->data( 'setting', $text );
        $c->data( 'siteaccess', $where['siteaccess'] );
        $c->data( 'files', $files );
        $c->data( 'effective', $c->display( $variable, $where['effective'] ) );
        $c->data( 'found', (bool)$where['found'] );

        $c->line( "$text" . ( $where['siteaccess'] ? ' (load order of siteaccess ' . $where['siteaccess'] . ')' : '' ) );
        if ( !$files )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: no file sets $text" );

        foreach ( $files as $i => $f )
        {
            $c->line( sprintf( '%2d. %-60s %s', $i + 1, $f['path'],
                               $f['scope'] !== null && $f['scope'] !== '' ? 'scope ' . $f['scope'] : '(' . $f['placement'] . ')' ) );
            foreach ( $this->valueLines( $c, $variable, $where['files'][$i]['value'] ) as $l )
                $c->line( '      ' . $l );
        }
        $c->line( 'In effect:' );
        foreach ( $this->valueLines( $c, $variable, $where['effective'] ) as $l )
            $c->line( '      ' . $l );
        return expIniCommandContext::EXIT_OK;
    }
}
