<?php
/**
 * File containing the expMigrationCondition class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The conditions of a YAML migration: the "if:" of a step, the "while:" of a loop, the "that:" of an assert step.
 * Pure: placeholders are replaced through expMigrationReferences with the scope given.
 *
 * A condition is one of:
 *
 *   true / false                         as written
 *   "${ref.flag}"                        the value is true unless it is null, false, 0, "", "0", "false", "no" or an
 *                                        empty list
 *   { all: [ <condition>, ... ] }        every one is true (an empty list is true)
 *   { any: [ <condition>, ... ] }        at least one is true (an empty list is false)
 *   { not: <condition> }
 *   { ref: <name>, set: true|false }     whether a reference is set
 *   { environment: <name or list> }      the run's environment is one of them (!name: is not that one)
 *   { engine: <name or list> }           the database engine is one of them (mysql, postgresql, sqlite, oracle, mongodb)
 *   { value: <value>, <operator>: <operand> }
 *        equals, not_equals              the same value (numbers compare as numbers: 3 equals "3")
 *        in, not_in                      one of a list
 *        gt, gte, lt, lte                compared as numbers
 *        matches                         a regular expression (PCRE, with its delimiters: "/^news-/")
 *        empty                           true: the value is empty (null, "", an empty list); false: it is not
 *
 * In the dry run a condition whose placeholders cannot be replaced yet (a reference a step sets when it runs) is
 * neither true nor false: evaluate() answers null and the plan says it is decided when the migration runs.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationCondition
{
    /** The comparison operators of { value: ..., <operator>: ... }. */
    const OPERATORS = array( 'equals', 'not_equals', 'in', 'not_in', 'gt', 'gte', 'lt', 'lte', 'matches', 'empty' );

    /** The forms a condition map can take, by their first key. */
    const FORMS = array( 'all', 'any', 'not', 'ref', 'environment', 'engine', 'value' );

    /**
     * The problems of a condition as written. Pure.
     *
     * @param mixed $condition
     * @param string $where where the condition is (steps[2].if), for the messages
     * @return string[]
     */
    public static function validate( $condition, $where )
    {
        $errors = array();
        self::check( $condition, (string)$where, $errors );
        return $errors;
    }

    /**
     * @param mixed $condition
     * @param string $where
     * @param string[] $errors
     */
    protected static function check( $condition, $where, array &$errors )
    {
        if ( is_bool( $condition ) )
            return;
        if ( is_string( $condition ) )
        {
            if ( !expMigrationReferences::contains( $condition ) )
                $errors[] = "$where: a condition text must be a placeholder such as \"\${ref.flag}\" (or true, false, or a map)";
            expMigrationReferences::validate( $condition, $where, $errors );
            return;
        }
        if ( !is_array( $condition ) || !$condition || array_keys( $condition ) === range( 0, count( $condition ) - 1 ) )
        {
            $errors[] = "$where: a condition is true, false, a placeholder or a map (" . implode( ', ', self::FORMS ) . ')';
            return;
        }
        $keys = array_keys( $condition );
        if ( isset( $condition['all'] ) || isset( $condition['any'] ) )
        {
            $form = isset( $condition['all'] ) ? 'all' : 'any';
            if ( count( $keys ) !== 1 )
                $errors[] = "$where: $form takes no other key (" . implode( ', ', array_diff( $keys, array( $form ) ) ) . ')';
            if ( !is_array( $condition[$form] ) || ( $condition[$form] && array_keys( $condition[$form] ) !== range( 0, count( $condition[$form] ) - 1 ) ) )
            {
                $errors[] = "$where.$form: a list of conditions";
                return;
            }
            foreach ( $condition[$form] as $i => $item )
                self::check( $item, "$where.{$form}[$i]", $errors );
            return;
        }
        if ( array_key_exists( 'not', $condition ) )
        {
            if ( count( $keys ) !== 1 )
                $errors[] = "$where: not takes no other key";
            self::check( $condition['not'], "$where.not", $errors );
            return;
        }
        if ( array_key_exists( 'ref', $condition ) )
        {
            if ( !expMigrationReferences::validName( $condition['ref'] ) )
                $errors[] = "$where.ref: a reference name (letters, digits and _, not starting with a digit)";
            if ( !array_key_exists( 'set', $condition ) || !is_bool( $condition['set'] ) )
                $errors[] = "$where: ref needs set: true or set: false";
            foreach ( array_diff( $keys, array( 'ref', 'set' ) ) as $key )
                $errors[] = "$where: ref takes no key $key";
            return;
        }
        foreach ( array( 'environment', 'engine' ) as $form )
        {
            if ( !array_key_exists( $form, $condition ) )
                continue;
            if ( count( $keys ) !== 1 )
                $errors[] = "$where: $form takes no other key";
            $names = is_array( $condition[$form] ) ? $condition[$form] : array( $condition[$form] );
            if ( !$names )
                $errors[] = "$where.$form: name one at least";
            foreach ( $names as $name )
            {
                if ( !is_string( $name ) || trim( $name, '!' ) === '' )
                    $errors[] = "$where.$form: names are texts";
                else if ( $form === 'engine' && !in_array( ltrim( $name, '!' ), expMigrationDefinition::ENGINES, true ) )
                    $errors[] = "$where.engine: the unknown engine $name (" . implode( ', ', expMigrationDefinition::ENGINES ) . ')';
            }
            return;
        }
        if ( array_key_exists( 'value', $condition ) )
        {
            $operators = array_values( array_diff( $keys, array( 'value' ) ) );
            if ( count( $operators ) !== 1 || !in_array( $operators[0], self::OPERATORS, true ) )
            {
                $errors[] = "$where: value takes exactly one operator (" . implode( ', ', self::OPERATORS ) . ')'
                            . ( $operators ? ', not ' . implode( ', ', $operators ) : '' );
                return;
            }
            expMigrationReferences::validate( $condition['value'], "$where.value", $errors );
            $operator = $operators[0];
            $operand = $condition[$operator];
            expMigrationReferences::validate( $operand, "$where.$operator", $errors );
            if ( expMigrationReferences::contains( $operand ) )
                return;
            if ( ( $operator === 'in' || $operator === 'not_in' ) && !is_array( $operand ) )
                $errors[] = "$where.$operator: a list";
            else if ( in_array( $operator, array( 'gt', 'gte', 'lt', 'lte' ), true ) && !is_int( $operand ) && !is_float( $operand ) )
                $errors[] = "$where.$operator: a number";
            else if ( $operator === 'empty' && !is_bool( $operand ) )
                $errors[] = "$where.empty: true or false";
            else if ( $operator === 'matches' && ( !is_string( $operand ) || @preg_match( $operand, '' ) === false ) )
                $errors[] = "$where.matches: a regular expression with its delimiters, e.g. \"/^news-/\"";
            return;
        }
        $errors[] = "$where: the unknown condition " . implode( ', ', $keys ) . ' (' . implode( ', ', self::FORMS ) . ')';
    }

    /**
     * Whether a condition is true. Pure.
     *
     * @param mixed $condition a valid condition (validate())
     * @param array $scope the scope of the placeholders (expMigrationReferences::interpolate()), with run.environment
     *                     and run.engine for the environment and engine forms
     * @param bool $strict true: a placeholder that cannot be replaced throws; false: the answer is null then
     * @return bool|null null: cannot be told yet (only when not strict)
     * @throws RuntimeException
     */
    public static function evaluate( $condition, array $scope, $strict = true )
    {
        if ( is_bool( $condition ) )
            return $condition;
        if ( is_string( $condition ) )
        {
            $unresolved = array();
            $value = expMigrationReferences::interpolate( $condition, $scope, $strict, $unresolved );
            return $unresolved ? null : self::truthy( $value );
        }
        if ( !is_array( $condition ) )
            return self::truthy( $condition );
        if ( isset( $condition['all'] ) || isset( $condition['any'] ) )
        {
            $all = isset( $condition['all'] );
            $unknown = false;
            foreach ( (array)( $all ? $condition['all'] : $condition['any'] ) as $item )
            {
                $result = self::evaluate( $item, $scope, $strict );
                if ( $result === null )
                    $unknown = true;
                else if ( $result !== $all )
                    return $result;
            }
            return $unknown ? null : $all;
        }
        if ( array_key_exists( 'not', $condition ) )
        {
            $result = self::evaluate( $condition['not'], $scope, $strict );
            return $result === null ? null : !$result;
        }
        if ( array_key_exists( 'ref', $condition ) )
        {
            $set = isset( $scope['ref'] ) && is_array( $scope['ref'] ) && array_key_exists( (string)$condition['ref'], $scope['ref'] );
            if ( !$set && !$strict && !empty( $scope['pending'] ) && in_array( (string)$condition['ref'], (array)$scope['pending'], true ) )
                return null;
            return $set === (bool)$condition['set'];
        }
        foreach ( array( 'environment', 'engine' ) as $form )
        {
            if ( !array_key_exists( $form, $condition ) )
                continue;
            $actual = isset( $scope['run'][$form] ) ? (string)$scope['run'][$form] : '';
            $names = is_array( $condition[$form] ) ? array_map( 'strval', $condition[$form] ) : array( (string)$condition[$form] );
            return expMigrationRunner::appliesTo( $names, $actual );
        }
        $unresolved = array();
        $value = expMigrationReferences::interpolate( $condition['value'], $scope, $strict, $unresolved );
        $operators = array_values( array_diff( array_keys( $condition ), array( 'value' ) ) );
        $operator = $operators[0];
        $operand = expMigrationReferences::interpolate( $condition[$operator], $scope, $strict, $unresolved );
        if ( $unresolved )
            return null;
        return self::compare( $value, $operator, $operand );
    }

    /**
     * One comparison. Pure.
     *
     * @param mixed $value
     * @param string $operator
     * @param mixed $operand
     * @return bool
     * @throws RuntimeException for an operand of the wrong kind (after its placeholders were replaced)
     */
    public static function compare( $value, $operator, $operand )
    {
        switch ( $operator )
        {
            case 'equals':
                return self::same( $value, $operand );
            case 'not_equals':
                return !self::same( $value, $operand );
            case 'in':
            case 'not_in':
                if ( !is_array( $operand ) )
                    throw new RuntimeException( "$operator needs a list" );
                $in = false;
                foreach ( $operand as $item )
                    if ( self::same( $value, $item ) )
                        $in = true;
                return $operator === 'in' ? $in : !$in;
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':
                if ( !is_numeric( $value ) || !is_numeric( $operand ) )
                    throw new RuntimeException( "$operator compares numbers: " . self::show( $value ) . ' and ' . self::show( $operand ) );
                $a = $value + 0;
                $b = $operand + 0;
                return $operator === 'gt' ? $a > $b : ( $operator === 'gte' ? $a >= $b : ( $operator === 'lt' ? $a < $b : $a <= $b ) );
            case 'matches':
                if ( is_array( $value ) )
                    return false;
                $result = @preg_match( (string)$operand, is_bool( $value ) ? ( $value ? 'true' : 'false' ) : (string)$value );
                if ( $result === false )
                    throw new RuntimeException( 'matches: not a regular expression: ' . self::show( $operand ) );
                return $result === 1;
            case 'empty':
                $empty = $value === null || $value === '' || $value === array();
                return $operand ? $empty : !$empty;
        }
        throw new RuntimeException( "the unknown operator $operator" );
    }

    /**
     * Whether two values are the same; numbers (and numeric texts) compare as numbers. Pure.
     *
     * @param mixed $a
     * @param mixed $b
     * @return bool
     */
    public static function same( $a, $b )
    {
        if ( is_numeric( $a ) && is_numeric( $b ) && !is_bool( $a ) && !is_bool( $b ) )
            return $a + 0 == $b + 0;
        if ( is_array( $a ) || is_array( $b ) )
            return $a === $b;
        return $a === $b || ( !is_bool( $a ) && !is_bool( $b ) && $a !== null && $b !== null && (string)$a === (string)$b );
    }

    /**
     * Whether a value counts as true. Pure.
     *
     * @param mixed $value
     * @return bool
     */
    public static function truthy( $value )
    {
        if ( is_string( $value ) )
            return !in_array( strtolower( trim( $value ) ), array( '', '0', 'false', 'no' ), true );
        if ( is_array( $value ) )
            return $value !== array();
        return (bool)$value;
    }

    /**
     * A condition as one line, for the plan. Pure.
     *
     * @param mixed $condition
     * @return string
     */
    public static function describe( $condition )
    {
        return self::show( $condition );
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected static function show( $value )
    {
        $json = json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        return $json === false ? '?' : $json;
    }
}
