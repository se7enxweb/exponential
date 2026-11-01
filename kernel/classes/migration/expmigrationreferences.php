<?php
/**
 * File containing the expMigrationReferences class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The placeholders of a YAML migration: ${scope.name} in a value is replaced by what the scope holds. Pure: the
 * values come in as a scope array, nothing is looked up elsewhere.
 *
 *   ${ref.<name>}             a reference: set by an earlier step ("references:"), of this migration or of one
 *                             before it in the same run (expMigrationContext::setReference())
 *   ${ref.<name>.<key>}       a key of a reference whose value is a list or a map (also ${loop.item.title} ...)
 *   ${loop.<name>}            the current item of a loop (foreach: "as" names it, default item; "key" names the key,
 *                             default key) and ${loop.index}, the number of the turn from 0
 *   ${idmap.<kind>.<name>}    the id map of the environment (migrations/idmap/<environment>.json)
 *   ${run.id}, ${run.environment}, ${run.engine}   the run's id, environment and database engine
 *   $${                       a literal "${"
 *
 * A value that is one placeholder and nothing else keeps the type of what it stands for (a number stays a number, a
 * list stays a list); a placeholder inside a longer text is written into it (true/false for a boolean, nothing for
 * null; a list or map there is an error). Keys of maps are never replaced, only values.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationReferences
{
    /** The scopes a placeholder can name. */
    const SCOPES = array( 'ref', 'loop', 'idmap', 'run' );

    /** A reference or loop variable name. */
    const NAME_PATTERN = '/^[A-Za-z_][A-Za-z0-9_]*$/';

    /** A placeholder or the escape $${. */
    const TOKEN_PATTERN = '/\$\$\{|\$\{([^}]*)\}/';

    /**
     * Whether a name can name a reference or a loop variable. Pure.
     *
     * @param mixed $name
     * @return bool
     */
    public static function validName( $name )
    {
        return is_string( $name ) && preg_match( self::NAME_PATTERN, $name ) === 1;
    }

    /**
     * Whether a value (or a value inside it) has a placeholder. Pure.
     *
     * @param mixed $value
     * @return bool
     */
    public static function contains( $value )
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $item )
                if ( self::contains( $item ) )
                    return true;
            return false;
        }
        return is_string( $value ) && preg_match( '/(?<!\$)\$\{/', $value ) === 1;
    }

    /**
     * The names a value's placeholders use in one scope (ref.news.id and ref.news give news). Pure.
     *
     * @param mixed $value
     * @param string $scope ref, loop, idmap, run
     * @return string[] unique, in order
     */
    public static function used( $value, $scope )
    {
        $names = array();
        if ( is_array( $value ) )
        {
            foreach ( $value as $item )
                foreach ( self::used( $item, $scope ) as $name )
                    $names[] = $name;
            return array_values( array_unique( $names ) );
        }
        if ( !is_string( $value ) || !preg_match_all( self::TOKEN_PATTERN, $value, $matches, PREG_SET_ORDER ) )
            return array();
        foreach ( $matches as $m )
        {
            if ( $m[0] === '$${' )
                continue;
            $parts = explode( '.', trim( $m[1] ) );
            if ( count( $parts ) > 1 && $parts[0] === $scope && $parts[1] !== '' )
                $names[] = $parts[1];
        }
        return array_values( array_unique( $names ) );
    }

    /**
     * The syntax errors of the placeholders in a value. Pure.
     *
     * @param mixed $value
     * @param string $where where the value is, for the messages (steps[2].name)
     * @param string[] $errors problems are added here
     */
    public static function validate( $value, $where, array &$errors )
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $item )
                self::validate( $item, $where . ( is_int( $key ) ? "[$key]" : ".$key" ), $errors );
            return;
        }
        if ( !is_string( $value ) || strpos( $value, '${' ) === false )
            return;
        $rest = preg_replace_callback( self::TOKEN_PATTERN, function ( $m ) use ( $where, &$errors )
        {
            if ( $m[0] === '$${' )
                return '';
            $problem = expMigrationReferences::expressionProblem( $m[1] );
            if ( $problem !== null )
                $errors[] = "$where: \${" . $m[1] . "}: $problem";
            return '';
        }, $value );
        if ( strpos( (string)$rest, '${' ) !== false )
            $errors[] = "$where: a \${ without its closing }";
    }

    /**
     * What is wrong with the inside of a placeholder (ref.name), or null. Pure.
     *
     * @param string $expression
     * @return string|null
     */
    public static function expressionProblem( $expression )
    {
        $parts = explode( '.', trim( (string)$expression ) );
        if ( count( $parts ) < 2 || $parts[0] === '' )
            return 'a placeholder is ${scope.name}, with the scope ' . implode( ', ', self::SCOPES );
        if ( !in_array( $parts[0], self::SCOPES, true ) )
            return "the unknown scope {$parts[0]} (" . implode( ', ', self::SCOPES ) . ')';
        foreach ( array_slice( $parts, 1 ) as $part )
            if ( !preg_match( '/^[A-Za-z0-9_-]+$/', $part ) )
                return 'a name part is letters, digits, _ and -';
        if ( $parts[0] === 'idmap' && count( $parts ) !== 3 )
            return 'the id map is read as ${idmap.<kind>.<name>}';
        if ( $parts[0] === 'run' && ( count( $parts ) !== 2 || !in_array( $parts[1], array( 'id', 'environment', 'engine' ), true ) ) )
            return 'the run has id, environment and engine';
        return null;
    }

    /**
     * A value with its placeholders replaced. Pure.
     *
     * @param mixed $value
     * @param array $scope scope => array( name => value ): ref, loop, idmap (kind => name => value), run
     * @param bool $strict true: a placeholder that cannot be replaced throws; false (the dry run): it stays as written
     *                     and is listed in $unresolved
     * @param string[] $unresolved the placeholders left as written
     * @return mixed
     * @throws RuntimeException in strict mode, for a placeholder that names nothing or a list inside a text
     */
    public static function interpolate( $value, array $scope, $strict = true, array &$unresolved = array() )
    {
        if ( is_array( $value ) )
        {
            $out = array();
            foreach ( $value as $key => $item )
                $out[$key] = self::interpolate( $item, $scope, $strict, $unresolved );
            return $out;
        }
        if ( !is_string( $value ) || strpos( $value, '${' ) === false )
            return $value;
        // one placeholder and nothing else: the value as it is
        if ( preg_match( '/^\$\{([^}]*)\}$/', $value, $m ) )
        {
            $found = false;
            $result = self::lookup( $m[1], $scope, $found );
            if ( $found )
                return $result;
            if ( $strict )
                throw new RuntimeException( self::unknownMessage( $m[1] ) );
            $unresolved[] = $value;
            return $value;
        }
        return preg_replace_callback( self::TOKEN_PATTERN, function ( $m ) use ( $scope, $strict, &$unresolved )
        {
            if ( $m[0] === '$${' )
                return '${';
            $found = false;
            $result = expMigrationReferences::lookup( $m[1], $scope, $found );
            if ( !$found )
            {
                if ( $strict )
                    throw new RuntimeException( expMigrationReferences::unknownMessage( $m[1] ) );
                $unresolved[] = $m[0];
                return $m[0];
            }
            if ( is_array( $result ) || is_object( $result ) )
            {
                if ( $strict )
                    throw new RuntimeException( '${' . $m[1] . '} is a list or a map and cannot be written into a text' );
                $unresolved[] = $m[0];
                return $m[0];
            }
            if ( is_bool( $result ) )
                return $result ? 'true' : 'false';
            return (string)$result;
        }, $value );
    }

    /**
     * What a placeholder stands for. Pure.
     *
     * @param string $expression ref.name, loop.item.title ...
     * @param array $scope
     * @param bool $found set to whether it names something
     * @return mixed
     */
    public static function lookup( $expression, array $scope, &$found )
    {
        $found = false;
        if ( self::expressionProblem( $expression ) !== null )
            return null;
        $parts = explode( '.', trim( (string)$expression ) );
        $current = isset( $scope[$parts[0]] ) && is_array( $scope[$parts[0]] ) ? $scope[$parts[0]] : array();
        foreach ( array_slice( $parts, 1 ) as $part )
        {
            if ( is_array( $current ) && array_key_exists( $part, $current ) )
                $current = $current[$part];
            else if ( is_array( $current ) && ctype_digit( $part ) && array_key_exists( (int)$part, $current ) )
                $current = $current[(int)$part];
            else
                return null;
        }
        $found = true;
        return $current;
    }

    /**
     * @param string $expression
     * @return string
     */
    public static function unknownMessage( $expression )
    {
        $parts = explode( '.', (string)$expression );
        switch ( $parts[0] )
        {
            case 'ref':
                return '${' . $expression . '}: no such reference (it is set by "references:" of an earlier step, or by an earlier migration of the same run)';
            case 'loop':
                return '${' . $expression . '}: no such loop variable here';
            case 'idmap':
                return '${' . $expression . '}: not in the id map of the environment';
        }
        return '${' . $expression . '}: names nothing';
    }
}
