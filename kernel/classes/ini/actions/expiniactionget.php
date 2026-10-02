<?php
/**
 * File containing the expIniActionGet class.
 *
 * exp:ini get: the value in effect (every file merged in load order, for the current siteaccess or the one
 * of -s) or, with a scope, the value that scope's file alone gives. Secrets are masked unless --show-secrets.
 * Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionGet extends expIniActionBase
{
    const NAME = 'get';
    const DESCRIPTION = 'Show the value in effect, or the value one scope\'s file gives';
    const USAGE = "<file>/<Block>/<Variable>[<key>] [scope]\n\n" .
                  "  exp:ini get site.ini/SiteSettings/SiteName                  in effect (current siteaccess)\n" .
                  "  exp:ini get site.ini/SiteSettings/SiteName -s admin         in effect for admin\n" .
                  "  exp:ini get site.ini/SiteSettings/SiteName global           settings/override only\n" .
                  "  exp:ini get site.ini/SiteAccessSettings/RelatedSiteAccessList[admin] siteaccess:site\n" .
                  "Exit 2 when it is not set.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $spec = $c->shift();
        $c->noMoreArguments();
        $text = expIniCommandContext::settingText( $setting );
        $c->data( 'setting', $text );

        if ( $spec === null )
        {
            if ( !expIniEditor::isRealRoot() )
            {
                // another root: the editor's own chain of that root's files (default, extensions, siteaccess, global)
                $value = self::pick( expIniEditor::effectiveValue( $setting['file'], $setting['block'], $setting['variable'] ), $setting );
                if ( $value === null )
                    return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $text is not set in effect" );
                $c->data( 'value', $c->display( $setting['variable'], $value ) );
                foreach ( is_array( $value ) && $setting['kind'] !== 'hash' ? $this->valueLines( $c, $setting['variable'], $value )
                                                                             : array( (string)$c->display( $setting['variable'], self::scalar( $value ) ) ) as $l )
                    $c->line( $l );
                return expIniCommandContext::EXIT_OK;
            }
            $where = expIniLocator::where( $setting['file'], $setting['block'], $setting['variable'] );
            $value = !empty( $where['found'] ) ? self::pick( $where['effective'], $setting ) : null;
            $from = 'in effect' . ( !empty( $where['siteaccess'] ) ? ' for siteaccess ' . $where['siteaccess'] : '' );
            $c->data( 'siteaccess', isset( $where['siteaccess'] ) ? $where['siteaccess'] : null );
        }
        else
        {
            $scope = $c->scope( $spec );
            $value = self::pick( $c->editor( $scope, $setting['file'] )->get( $setting['block'], $setting['variable'] ), $setting );
            $from = 'in ' . $scope->name();
            $c->data( 'scope', $scope->name() );
        }

        if ( $value === null )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $text is not set $from" );

        $c->data( 'value', $c->display( $setting['variable'], $value ) );
        $c->data( 'secret', expIniCommandContext::isSecret( $setting['variable'] ) );
        $name = $setting['kind'] === 'hash' ? $setting['variable'] . '[' . $setting['key'] . ']' : $setting['variable'];
        if ( $setting['kind'] === 'hash' || !is_array( $value ) )
            $c->line( (string)$c->display( $setting['variable'], self::scalar( $value ) ) );
        else
            foreach ( $this->valueLines( $c, $name, $value ) as $l )
                $c->line( $l );
        return expIniCommandContext::EXIT_OK;
    }
}
