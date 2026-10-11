<?php
/**
 * File containing the expJsonSchemaValidator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Checks decoded JSON (json_decode( ..., true )) against a JSON Schema (draft 2020-12), for the keywords the
 * kernel's own schemas use: type, enum, const, required, properties, additionalProperties, patternProperties,
 * propertyNames, minProperties, items, prefixItems, minItems, maxItems, minLength, maxLength, pattern, oneOf,
 * anyOf, allOf, not, $ref (to "#" and "#/$defs/<name>"). Other keywords ($schema, $id, title, description,
 * examples, $comment, format) are ignored.
 *
 * A JSON object and a JSON array both decode as PHP arrays; an array with keys 0..n-1 (or an empty one) is read as
 * an array, unless the schema expects an object there.
 */
class expJsonSchemaValidator
{
    /** @var int the most errors reported */
    const MAX_ERRORS = 50;

    /** @var array */
    private $root;
    /** @var string[] */
    private $errors = array();
    /** @var int */
    private $depth = 0;
    /** @var bool */
    private $failFast = false;

    /**
     * @param mixed $data
     * @param array $schema
     * @return string[] the errors, "<JSON pointer>: <message>"; empty when the data is valid
     */
    static function validate( $data, array $schema )
    {
        $validator = new self( $schema );
        $validator->check( $data, $schema, '' );
        return $validator->errors;
    }

    private function __construct( array $schema )
    {
        $this->root = $schema;
    }

    /**
     * Checks without recording errors.
     * @return bool
     */
    private function matches( $data, $schema, $path )
    {
        $saved = $this->errors;
        $savedFailFast = $this->failFast;
        $this->errors = array();
        // the first error decides: an alternative that does not match is not checked any further
        $this->failFast = true;
        $this->check( $data, $schema, $path );
        $ok = !$this->errors;
        $this->errors = $saved;
        $this->failFast = $savedFailFast;
        return $ok;
    }

    /** @return bool whether checking stops here (fail-fast mode with an error) */
    private function stop()
    {
        return $this->failFast && $this->errors;
    }

    private function error( $path, $message )
    {
        if ( count( $this->errors ) < self::MAX_ERRORS )
            $this->errors[] = ( $path === '' ? '/' : $path ) . ': ' . $message;
    }

    private function resolve( $ref )
    {
        if ( $ref === '#' )
            return $this->root;
        if ( strpos( $ref, '#/' ) !== 0 )
            return null;
        $node = $this->root;
        foreach ( explode( '/', substr( $ref, 2 ) ) as $part )
        {
            $part = str_replace( array( '~1', '~0' ), array( '/', '~' ), $part );
            if ( !is_array( $node ) || !array_key_exists( $part, $node ) )
                return null;
            $node = $node[$part];
        }
        return $node;
    }

    /**
     * A JSON Schema (ECMA-262) pattern as a PCRE pattern: delimiters, Unicode mode, \uXXXX as \x{XXXX}.
     * @param string $pattern
     * @return string
     */
    private static function regex( $pattern )
    {
        $pattern = preg_replace( '/(?<!\\\\)((?:\\\\\\\\)*)\\\\u([0-9A-Fa-f]{4})/', '$1\\x{$2}', (string)$pattern );
        return '/' . str_replace( '/', '\\/', $pattern ) . '/u';
    }

    private static function typeOf( $value, $preferObject )
    {
        if ( $value === null )
            return 'null';
        if ( is_bool( $value ) )
            return 'boolean';
        if ( is_int( $value ) )
            return 'integer';
        if ( is_float( $value ) )
            return floor( $value ) == $value && abs( $value ) < 9.0E15 ? 'integer' : 'number';
        if ( is_string( $value ) )
            return 'string';
        if ( is_array( $value ) )
        {
            if ( $value === array() )
                return $preferObject ? 'object' : 'array';
            return array_is_list( $value ) ? 'array' : 'object';
        }
        return 'unknown';
    }

    private function check( $data, $schema, $path )
    {
        if ( $schema === true || $schema === array() )
            return;
        if ( $schema === false )
        {
            $this->error( $path, 'not allowed' );
            return;
        }
        if ( !is_array( $schema ) || $this->stop() )
            return;
        if ( ++$this->depth > 1000 )
        {
            $this->error( $path, 'nested too deep' );
            --$this->depth;
            return;
        }

        if ( isset( $schema['$ref'] ) )
        {
            $target = $this->resolve( $schema['$ref'] );
            if ( $target === null )
                $this->error( $path, 'unresolvable $ref ' . $schema['$ref'] );
            else
                $this->check( $data, $target, $path );
        }

        $types = isset( $schema['type'] ) ? (array)$schema['type'] : array();
        $preferObject = in_array( 'object', $types, true ) || isset( $schema['properties'] ) || isset( $schema['required'] );
        $type = self::typeOf( $data, $preferObject );
        if ( $types )
        {
            $ok = in_array( $type, $types, true ) || ( $type === 'integer' && in_array( 'number', $types, true ) );
            if ( !$ok )
            {
                $this->error( $path, 'expected ' . implode( ' or ', $types ) . ', got ' . $type );
                --$this->depth;
                return;
            }
        }
        if ( array_key_exists( 'const', $schema ) && $data !== $schema['const'] )
            $this->error( $path, 'must be ' . json_encode( $schema['const'] ) );
        if ( isset( $schema['enum'] ) && !in_array( $data, $schema['enum'], true ) )
            $this->error( $path, 'must be one of ' . json_encode( $schema['enum'] ) );

        if ( $type === 'string' )
        {
            $length = function_exists( 'mb_strlen' ) ? mb_strlen( $data, 'UTF-8' ) : strlen( $data );
            if ( isset( $schema['minLength'] ) && $length < $schema['minLength'] )
                $this->error( $path, 'shorter than ' . $schema['minLength'] );
            if ( isset( $schema['maxLength'] ) && $length > $schema['maxLength'] )
                $this->error( $path, 'longer than ' . $schema['maxLength'] );
            if ( isset( $schema['pattern'] ) && !preg_match( self::regex( $schema['pattern'] ), $data ) )
                $this->error( $path, 'does not match ' . $schema['pattern'] );
        }
        if ( $type === 'object' )
        {
            if ( isset( $schema['required'] ) )
                foreach ( $schema['required'] as $name )
                    if ( !array_key_exists( $name, $data ) )
                        $this->error( $path, 'missing ' . $name );
            if ( isset( $schema['minProperties'] ) && count( $data ) < $schema['minProperties'] )
                $this->error( $path, 'fewer than ' . $schema['minProperties'] . ' members' );
            foreach ( $data as $name => $value )
            {
                if ( $this->stop() )
                    break;
                $name = (string)$name;
                $memberPath = $path . '/' . str_replace( array( '~', '/' ), array( '~0', '~1' ), $name );
                if ( isset( $schema['propertyNames'] ) )
                    $this->check( $name, $schema['propertyNames'], $memberPath );
                $known = false;
                if ( isset( $schema['properties'] ) && array_key_exists( $name, $schema['properties'] ) )
                {
                    $known = true;
                    $this->check( $value, $schema['properties'][$name], $memberPath );
                }
                if ( isset( $schema['patternProperties'] ) )
                    foreach ( $schema['patternProperties'] as $pattern => $sub )
                        if ( preg_match( self::regex( $pattern ), $name ) )
                        {
                            $known = true;
                            $this->check( $value, $sub, $memberPath );
                        }
                if ( !$known && array_key_exists( 'additionalProperties', $schema ) )
                {
                    if ( $schema['additionalProperties'] === false )
                        $this->error( $memberPath, 'not allowed here' );
                    else
                        $this->check( $value, $schema['additionalProperties'], $memberPath );
                }
            }
        }
        if ( $type === 'array' )
        {
            $count = count( $data );
            if ( isset( $schema['minItems'] ) && $count < $schema['minItems'] )
                $this->error( $path, 'fewer than ' . $schema['minItems'] . ' items' );
            if ( isset( $schema['maxItems'] ) && $count > $schema['maxItems'] )
                $this->error( $path, 'more than ' . $schema['maxItems'] . ' items' );
            $prefix = isset( $schema['prefixItems'] ) ? $schema['prefixItems'] : array();
            foreach ( $data as $i => $value )
            {
                if ( $this->stop() )
                    break;
                if ( isset( $prefix[$i] ) )
                    $this->check( $value, $prefix[$i], $path . '/' . $i );
                elseif ( array_key_exists( 'items', $schema ) )
                {
                    if ( $schema['items'] === false )
                        $this->error( $path . '/' . $i, 'no more items allowed' );
                    else
                        $this->check( $value, $schema['items'], $path . '/' . $i );
                }
            }
        }

        if ( isset( $schema['allOf'] ) )
            foreach ( $schema['allOf'] as $sub )
                $this->check( $data, $sub, $path );
        if ( isset( $schema['anyOf'] ) )
        {
            $any = false;
            foreach ( $schema['anyOf'] as $sub )
                if ( $this->matches( $data, $sub, $path ) )
                {
                    $any = true;
                    break;
                }
            if ( !$any )
                $this->error( $path, 'matches none of the allowed forms' );
        }
        if ( isset( $schema['oneOf'] ) )
        {
            $matched = 0;
            foreach ( $schema['oneOf'] as $sub )
                if ( $this->matches( $data, $sub, $path ) )
                    ++$matched;
            if ( $matched !== 1 )
                $this->error( $path, $matched === 0 ? 'matches none of the allowed forms' : 'matches more than one form' );
        }
        if ( isset( $schema['not'] ) && $this->matches( $data, $schema['not'], $path ) )
            $this->error( $path, 'matches a form that is not allowed' );
        --$this->depth;
    }
}
