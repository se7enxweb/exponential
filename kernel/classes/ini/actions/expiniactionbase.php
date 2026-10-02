<?php
/**
 * File containing the expIniActionBase class.
 *
 * What the built-in exp:ini actions share: the three describing methods from class constants, the printing
 * of a value as INI lines, and the small checks several actions make (a setting of the wrong kind, a value an
 * array already has, a scope without the file). An action of an extension may extend it or implement
 * expIniAction on its own. Guide: doc/bc/6.0/console-exp-ini.md.
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
        $isList = self::isList( $value );
        foreach ( $value as $key => $v )
            $lines[] = $variable . '[' . ( $isList ? '' : $key ) . ']=' . self::scalar( $v );
        return $lines;
    }

    /**
     * Whether an array value is a list (Variable[]=...) rather than a hash (Variable[key]=...).
     *
     * @param array $value
     * @return bool
     */
    protected static function isList( array $value )
    {
        return array_keys( $value ) === range( 0, count( $value ) - 1 );
    }

    /**
     * Whether an array value (as the editor reads it) already holds a value, compared as strings.
     *
     * @param mixed $current the variable's value; anything but an array holds nothing
     * @param mixed $value
     * @return bool
     */
    protected static function hasValue( $current, $value )
    {
        return is_array( $current ) && in_array( (string)$value, array_map( 'strval', $current ), true );
    }

    /**
     * Refuses a setting of a kind the action does not take, as a usage error.
     *
     * @param array $setting parseSetting()
     * @param string $kind plain|array|hash: the kind that is refused
     * @param string $message
     * @throws expIniException (usage) when the setting is of that kind
     */
    protected static function refuseKind( array $setting, $kind, $message )
    {
        if ( $setting['kind'] === $kind )
            throw expIniException::usage( $message );
    }

    /**
     * The end of a "Not found" message for a scope that has no such file: "<scope> has no <file>.ini file".
     *
     * @param expIniScope $scope
     * @param string $file without .ini
     * @return string
     */
    protected static function noFile( expIniScope $scope, $file )
    {
        return $scope->name() . ' has no ' . $file . '.ini file';
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
