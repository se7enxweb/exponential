<?php
/**
 * File containing the expMigrationStepSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The schema of a step type (expMigrationStepType::schema()): checks a schema itself, checks a step's keys against
 * it, and fills in its defaults. Pure.
 *
 * Before a run (check(), the static check) a value with a placeholder is not checked against its type, it is
 * replaced only when the step runs; check( ..., true ) then checks the replaced values again.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepSchema
{
    /** The types a key can have. */
    const TYPES = array( 'string', 'int', 'number', 'bool', 'scalar', 'list', 'map', 'any', 'identifier', 'condition' );

    /** The keys of a key's definition. */
    const KEY_FIELDS = array( 'type', 'required', 'default', 'description', 'values', 'modes', 'kind' );

    /** The keys of a schema. */
    const FIELDS = array( 'description', 'modes', 'mode', 'keys', 'values', 'example' );

    /**
     * The problems of a schema (a step type's), so a broken step type is found when it is registered. Pure.
     *
     * @param mixed $schema
     * @return string[]
     */
    public static function problems( $schema )
    {
        if ( !is_array( $schema ) )
            return array( 'the schema is not an array' );
        $errors = array();
        foreach ( array_diff( array_keys( $schema ), self::FIELDS ) as $field )
            $errors[] = "the schema has the unknown field $field (" . implode( ', ', self::FIELDS ) . ')';
        $modes = isset( $schema['modes'] ) ? $schema['modes'] : array();
        if ( !is_array( $modes ) )
            $errors[] = 'modes is a list';
        else if ( isset( $schema['mode'] ) && !in_array( $schema['mode'], $modes, true ) )
            $errors[] = 'the default mode ' . $schema['mode'] . ' is not one of the modes';
        $keys = isset( $schema['keys'] ) ? $schema['keys'] : array();
        if ( !is_array( $keys ) )
            return array_merge( $errors, array( 'keys is a map' ) );
        foreach ( $keys as $key => $definition )
        {
            if ( !is_string( $key ) || !preg_match( '/^[a-z][a-z0-9_]*$/', $key ) )
                $errors[] = "the key $key: keys are lower case letters, digits and _";
            else if ( in_array( $key, expMigrationYaml::STEP_KEYS, true ) )
                $errors[] = "the key $key is the engine's (" . implode( ', ', expMigrationYaml::STEP_KEYS ) . ')';
            if ( !is_array( $definition ) )
            {
                $errors[] = "the key $key: its definition is an array";
                continue;
            }
            foreach ( array_diff( array_keys( $definition ), self::KEY_FIELDS ) as $field )
                $errors[] = "the key $key has the unknown field $field";
            if ( !isset( $definition['type'] ) || !in_array( $definition['type'], self::TYPES, true ) )
                $errors[] = "the key $key: the type is one of " . implode( ', ', self::TYPES );
            if ( isset( $definition['type'] ) && $definition['type'] === 'identifier' && ( !isset( $definition['kind'] ) || !in_array( $definition['kind'], expMigrationIdentifier::KINDS, true ) ) )
                $errors[] = "the key $key: an identifier names its kind (" . implode( ', ', expMigrationIdentifier::KINDS ) . ')';
            if ( isset( $definition['modes'] ) && is_array( $modes ) && array_diff( (array)$definition['modes'], $modes ) )
                $errors[] = "the key $key names modes the step does not have";
        }
        if ( isset( $schema['values'] ) && !is_array( $schema['values'] ) )
            $errors[] = 'values is a map';
        return $errors;
    }

    /**
     * A step's mode and keys checked against a schema. Pure.
     *
     * @param array $schema
     * @param array $step the step's own keys (without the engine's keys) and mode
     * @param string $where where the step is (steps[2]), for the messages
     * @param bool $resolved true: the placeholders are replaced, every value is checked
     * @return string[]
     */
    public static function check( array $schema, array $step, $where, $resolved = false )
    {
        $errors = array();
        $modes = isset( $schema['modes'] ) ? (array)$schema['modes'] : array();
        $mode = null;
        if ( $modes )
        {
            $mode = isset( $step['mode'] ) ? $step['mode'] : ( isset( $schema['mode'] ) ? $schema['mode'] : null );
            if ( $mode === null )
                $errors[] = "$where: mode is missing (" . implode( ', ', $modes ) . ')';
            else if ( !in_array( $mode, $modes, true ) )
                $errors[] = "$where.mode: " . self::show( $mode ) . ' is not one of ' . implode( ', ', $modes );
        }
        else if ( array_key_exists( 'mode', $step ) )
        {
            $errors[] = "$where: the step takes no mode";
        }
        $keys = isset( $schema['keys'] ) ? (array)$schema['keys'] : array();
        foreach ( $step as $key => $value )
        {
            if ( $key === 'mode' )
                continue;
            if ( !isset( $keys[$key] ) )
            {
                $errors[] = "$where: the unknown key $key" . ( $keys ? ' (' . implode( ', ', array_keys( $keys ) ) . ')' : ' (the step takes none)' );
                continue;
            }
            $definition = $keys[$key];
            if ( $mode !== null && isset( $definition['modes'] ) && !in_array( $mode, (array)$definition['modes'], true ) )
            {
                $errors[] = "$where.$key: only in the mode " . implode( ', ', (array)$definition['modes'] );
                continue;
            }
            if ( !$resolved )
                expMigrationReferences::validate( $value, "$where.$key", $errors );
            $problem = self::typeProblem( $definition, $value, $resolved );
            if ( $problem !== null )
                $errors[] = "$where.$key: $problem";
            if ( !$resolved && isset( $definition['type'] ) && $definition['type'] === 'condition' )
                foreach ( expMigrationCondition::validate( $value, "$where.$key" ) as $error )
                    $errors[] = $error;
        }
        foreach ( $keys as $key => $definition )
        {
            if ( empty( $definition['required'] ) || array_key_exists( $key, $step ) )
                continue;
            if ( $mode !== null && isset( $definition['modes'] ) && !in_array( $mode, (array)$definition['modes'], true ) )
                continue;
            $errors[] = "$where: $key is missing";
        }
        return $errors;
    }

    /**
     * What is wrong with a value for its key, or null. Pure.
     *
     * @param array $definition the key's definition
     * @param mixed $value
     * @param bool $resolved false: a value with a placeholder passes
     * @return string|null
     */
    public static function typeProblem( array $definition, $value, $resolved = true )
    {
        if ( !$resolved && expMigrationReferences::contains( $value ) && ( !is_array( $value ) || in_array( $definition['type'], array( 'list', 'map', 'any', 'condition' ), true ) ) )
            return null;
        $type = isset( $definition['type'] ) ? $definition['type'] : 'any';
        $ok = true;
        switch ( $type )
        {
            case 'string':
                $ok = is_string( $value ) || is_int( $value ) || is_float( $value );
                break;
            case 'int':
                $ok = is_int( $value ) || ( is_string( $value ) && preg_match( '/^-?\d+$/', $value ) );
                break;
            case 'number':
                $ok = ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && is_numeric( $value ) ) );
                break;
            case 'bool':
                $ok = is_bool( $value );
                break;
            case 'scalar':
                $ok = is_scalar( $value ) || $value === null;
                break;
            case 'list':
                $ok = is_array( $value ) && ( !$value || array_keys( $value ) === range( 0, count( $value ) - 1 ) );
                break;
            case 'map':
                $ok = is_array( $value ) && ( !$value || array_keys( $value ) !== range( 0, count( $value ) - 1 ) );
                break;
            case 'identifier':
                $kind = isset( $definition['kind'] ) ? $definition['kind'] : 'node';
                $problem = expMigrationIdentifier::problem( $kind, $value );
                if ( $problem !== null )
                    return $problem;
                break;
            case 'condition':
            case 'any':
                break;
        }
        if ( !$ok )
            return 'a ' . ( $type === 'int' ? 'whole number' : $type ) . ' is expected, not ' . self::show( $value );
        if ( isset( $definition['values'] ) && is_array( $definition['values'] ) && !in_array( $value, $definition['values'], true ) )
            return self::show( $value ) . ' is not one of ' . implode( ', ', array_map( array( __CLASS__, 'show' ), $definition['values'] ) );
        return null;
    }

    /**
     * A step's keys with the defaults of the schema, the default mode, and int/number keys made numbers. Pure.
     *
     * @param array $schema
     * @param array $step
     * @return array
     */
    public static function withDefaults( array $schema, array $step )
    {
        if ( !empty( $schema['modes'] ) && !isset( $step['mode'] ) && isset( $schema['mode'] ) )
            $step['mode'] = $schema['mode'];
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        foreach ( isset( $schema['keys'] ) ? (array)$schema['keys'] : array() as $key => $definition )
        {
            if ( !array_key_exists( $key, $step ) && array_key_exists( 'default', $definition )
                 && ( $mode === null || !isset( $definition['modes'] ) || in_array( $mode, (array)$definition['modes'], true ) ) )
                $step[$key] = $definition['default'];
            if ( !array_key_exists( $key, $step ) || !is_string( $step[$key] ) || !isset( $definition['type'] ) )
                continue;
            if ( $definition['type'] === 'int' && preg_match( '/^-?\d+$/', $step[$key] ) )
                $step[$key] = (int)$step[$key];
            else if ( $definition['type'] === 'number' && is_numeric( $step[$key] ) )
                $step[$key] = $step[$key] + 0;
        }
        return $step;
    }

    /**
     * A value as JSON, for messages. Pure.
     *
     * @param mixed $value
     * @return string
     */
    public static function show( $value )
    {
        $json = json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        if ( $json === false )
            return '?';
        return strlen( $json ) > 80 ? substr( $json, 0, 77 ) . '...' : $json;
    }
}
