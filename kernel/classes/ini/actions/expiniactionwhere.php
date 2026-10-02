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
                  "Files of extensions that are not active for the siteaccess are listed after the load order,\n" .
                  "marked as such: they set the variable, but nothing reads them.\n" .
                  "Exit 2 when no file this siteaccess loads sets it.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $siteAccess = self::siteAccessArgument( $c->shift() );
        $c->noMoreArguments();
        $c->requireOwnInstallation( 'where' );

        $where = expIniLocator::where( $setting['file'], $setting['block'], $setting['variable'], $siteAccess );
        $variable = $where['variable'];
        $text = $setting['file'] . '.ini/' . $setting['block'] . '/' . $variable;
        $this->report( $c, $text, $variable, $where );

        $c->line( $text . ( $where['siteaccess'] ? ' (load order of siteaccess ' . $where['siteaccess'] . ')' : '' ) );
        if ( !$where['files'] && !$where['notLoaded'] )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: no file sets $text" );

        $number = 0;
        foreach ( $where['files'] as $f )
            $this->printFile( $c, ++$number, $f['path'], $this->scopeLabel( $f ), $variable, $f['value'] );
        if ( $where['notLoaded'] )
        {
            $c->line( 'Not loaded for this siteaccess:' );
            foreach ( $where['notLoaded'] as $f )
                $this->printFile( $c, ++$number, $f['path'], 'scope ' . $f['scope'] . ' (' . $f['reason'] . ')', $variable, $f['value'] );
        }
        if ( !$where['files'] )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND,
                               "Not in effect: only files this siteaccess does not load set $text" );

        $c->line( 'In effect:' );
        foreach ( $this->valueLines( $c, $variable, $where['effective'] ) as $l )
            $c->line( '      ' . $l );
        return expIniCommandContext::EXIT_OK;
    }

    /** 'siteaccess:admin' and 'admin' both name the siteaccess admin; null stays null. */
    protected static function siteAccessArgument( $argument )
    {
        if ( $argument !== null && strpos( $argument, 'siteaccess:' ) === 0 )
            return substr( $argument, strlen( 'siteaccess:' ) );
        return $argument;
    }

    /** The JSON data of where: the files with their values as displayed (secrets masked). */
    protected function report( expIniCommandContext $c, $text, $variable, array $where )
    {
        $c->data( 'setting', $text );
        $c->data( 'siteaccess', $where['siteaccess'] );
        $c->data( 'files', self::displayed( $c, $variable, $where['files'] ) );
        $c->data( 'notLoaded', self::displayed( $c, $variable, $where['notLoaded'] ) );
        $c->data( 'effective', $c->display( $variable, $where['effective'] ) );
        $c->data( 'found', (bool)$where['found'] );
    }

    /** Rows of the locator with each value as displayed (secrets masked). */
    protected static function displayed( expIniCommandContext $c, $variable, array $files )
    {
        foreach ( $files as $i => $f )
            $files[$i]['value'] = $c->display( $variable, $f['value'] );
        return $files;
    }

    /** 'scope <name>', or eZINI's placement in brackets for a file no scope writes. */
    protected function scopeLabel( array $file )
    {
        return $file['scope'] !== null && $file['scope'] !== '' ? 'scope ' . $file['scope'] : '(' . $file['placement'] . ')';
    }

    /** One numbered file of the list and the lines it sets. */
    protected function printFile( expIniCommandContext $c, $number, $path, $label, $variable, $value )
    {
        $c->line( sprintf( '%2d. %-60s %s', $number, $path, $label ) );
        foreach ( $this->valueLines( $c, $variable, $value ) as $l )
            $c->line( '      ' . $l );
    }
}
