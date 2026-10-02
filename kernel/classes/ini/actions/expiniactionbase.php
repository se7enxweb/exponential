<?php
/**
 * File containing the expIniActionBase class.
 *
 * What the built-in exp:ini actions share: the three describing methods from class constants and the
 * printing of a value as INI lines. An action of an extension may extend it or implement expIniAction
 * on its own. Guide: doc/bc/6.0/console-exp-ini.md.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

abstract class expIniActionBase implements expIniAction
{
    /** The action's name. */
    const NAME = '';

    /** One line for the action list. */
    const DESCRIPTION = '';

    /** The arguments, then lines of explanation and examples. */
    const USAGE = '';

    public function name()
    {
        return static::NAME;
    }

    public function description()
    {
        return static::DESCRIPTION;
    }

    public function usage()
    {
        return static::USAGE;
    }

    /**
     * A value as the lines of an INI file: Variable=value, Variable[]=value, Variable[key]=value,
     * secrets masked unless --show-secrets.
     *
     * @param expIniCommandContext $c
     * @param string $variable
     * @param mixed $value
     * @return array lines
     */
    protected function valueLines( expIniCommandContext $c, $variable, $value )
    {
        $value = $c->display( $variable, $value );
        if ( !is_array( $value ) )
            return array( $variable . '=' . self::scalar( $value ) );
        if ( !$value )
            return array( $variable . '[]' );
        $lines = array();
        $isList = array_keys( $value ) === range( 0, count( $value ) - 1 );
        foreach ( $value as $key => $v )
            $lines[] = $variable . '[' . ( $isList ? '' : $key ) . ']=' . self::scalar( $v );
        return $lines;
    }

    /** @return string a scalar as written in an INI file */
    protected static function scalar( $value )
    {
        if ( $value === null )
            return '';
        if ( is_bool( $value ) )
            return $value ? 'true' : 'false';
        if ( is_array( $value ) )
            return json_encode( $value );
        return (string)$value;
    }

    /**
     * The value of a hash entry or an array, as get() and remove() see it.
     *
     * @param mixed $value the variable's value
     * @param array $setting parseSetting()
     * @return mixed null when the entry is not there
     */
    protected static function pick( $value, array $setting )
    {
        if ( $setting['kind'] !== 'hash' )
            return $value;
        return is_array( $value ) && array_key_exists( $setting['key'], $value ) ? $value[$setting['key']] : null;
    }
}
