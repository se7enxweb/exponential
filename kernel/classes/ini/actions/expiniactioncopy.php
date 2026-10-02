<?php
/**
 * File containing the expIniActionCopy class.
 *
 * exp:ini copy: copies a setting from one scope's file to another's: a plain variable or a hash entry is set
 * to the same value, a whole array is written as its reset line followed by the same values, so the target
 * ends with what the source has. It is the worked example of the guide's "Adding an action" chapter and is
 * registered as a built-in, so the example is tested with the others. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expIniActionCopy extends expIniActionBase
{
    const NAME = 'copy';
    const DESCRIPTION = 'Copy a setting from one scope to another (a whole array as reset line + values)';
    const USAGE = "<file>/<Block>/<Variable>[<key>] <from-scope> <to-scope>\n\n" .
                  "  exp:ini copy site.ini/SiteSettings/SiteName global siteaccess:admin\n" .
                  "  exp:ini copy site.ini/ExtensionSettings/ActiveAccessExtensions siteaccess:site siteaccess:intranet\n" .
                  "Exit 2 when the source scope's file does not set it.";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $from = $c->scope( $c->shift( 'from-scope' ) );
        $to = $c->writeScope( $c->shift( 'to-scope' ) );
        $c->noMoreArguments();

        $text = expIniCommandContext::settingText( $setting );
        $c->data( 'setting', $text );
        $c->data( 'from', $from->name() );
        if ( $from->name() === $to->name() )
            throw expIniException::usage( 'copy needs two different scopes' );

        $value = self::pick( $c->editor( $from, $setting['file'] )->get( $setting['block'], $setting['variable'] ), $setting );
        if ( $value === null )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, "Not found: $text is not set in " . $from->name() );
        $c->data( 'value', $c->display( $setting['variable'], $value ) );

        $editor = $c->editor( $to, $setting['file'] );
        if ( $setting['kind'] === 'hash' )
            $editor->set( $setting['block'], $setting['variable'], (string)$value, $setting['key'] );
        else if ( !is_array( $value ) )
            $editor->set( $setting['block'], $setting['variable'], (string)$value );
        else
            self::writeArray( $editor, $setting['block'], $setting['variable'], $value );
        return $c->commit( $editor, $to, $setting['file'], "copy $text from " . $from->name() );
    }

    /**
     * Writes a whole array so that the target ends with exactly these values: its reset line, then each value
     * (Variable[]=value for a list, Variable[key]=value for a hash).
     *
     * @param expIniEditor $editor
     * @param string $block
     * @param string $variable
     * @param array $value
     */
    private static function writeArray( $editor, $block, $variable, array $value )
    {
        $editor->clearArray( $block, $variable );
        $isList = self::isList( $value );
        foreach ( $value as $key => $v )
        {
            if ( $isList )
                $editor->add( $block, $variable, (string)$v );
            else
                $editor->set( $block, $variable, (string)$v, (string)$key );
        }
    }
}
