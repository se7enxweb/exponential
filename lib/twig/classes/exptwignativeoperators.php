<?php
/**
 * The logic, arithmetic and control operators of the template system (eZTemplateLogicOperator,
 * eZTemplateArithmeticOperator, eZTemplateControlOperator), wash (xhtml), implode, contains and concat run directly in PHP for Twig templates, with exactly the results of their modify(): what the template compiler does
 * for a .tpl, where these operators become PHP code instead of operator calls. A bridged operator call goes through
 * eZTemplate::processOperator() (parameters as template elements, named parameters, the operator's own dispatch);
 * these are the operators a converted template calls most (in every condition and loop), and a call of one costs a
 * fraction of it here.
 *
 * Used only while the operator of that name is still the kernel's own class: an extension that registers its own
 * operator of the same name keeps it. Anything this class does not cover returns NOT_HANDLED and goes the normal way.
 * The parity tests (the engine's 453 cases, every logic and arithmetic case of eZTemplateOperatorMatrixTest) hold the
 * results to the template system's.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */
class expTwigNativeOperators
{
    /** The operators covered and the kernel class that must own each. */
    const OPERATORS = array(
        'eq' => 'eZTemplateLogicOperator', 'ne' => 'eZTemplateLogicOperator', 'lt' => 'eZTemplateLogicOperator',
        'gt' => 'eZTemplateLogicOperator', 'le' => 'eZTemplateLogicOperator', 'ge' => 'eZTemplateLogicOperator',
        'and' => 'eZTemplateLogicOperator', 'or' => 'eZTemplateLogicOperator', 'not' => 'eZTemplateLogicOperator',
        'count' => 'eZTemplateArithmeticOperator', 'sum' => 'eZTemplateArithmeticOperator', 'sub' => 'eZTemplateArithmeticOperator',
        'mul' => 'eZTemplateArithmeticOperator', 'div' => 'eZTemplateArithmeticOperator', 'mod' => 'eZTemplateArithmeticOperator',
        'inc' => 'eZTemplateArithmeticOperator', 'dec' => 'eZTemplateArithmeticOperator',
        'cond' => 'eZTemplateControlOperator', 'first_set' => 'eZTemplateControlOperator',
        'wash' => 'eZTemplateStringOperator',
        'implode' => 'eZTemplateArrayOperator', 'contains' => 'eZTemplateArrayOperator',
        'concat' => 'eZTemplateTextOperator',
    );

    /** The result when the call is left to the template system. */
    const NOT_HANDLED = "\0exp-not-handled";

    /** @var array name => bool */
    private static $supported = array();

    /**
     * @param string $name
     * @return bool whether the operator runs here: covered, and still the kernel's own class
     */
    public static function supports( $name )
    {
        if ( !isset( self::$supported[$name] ) )
        {
            $supported = false;
            if ( isset( self::OPERATORS[$name] ) )
            {
                $tpl = eZTemplate::factory();
                if ( isset( $tpl->Operators[$name] ) && is_array( $tpl->Operators[$name] ) )
                    $tpl->loadAndRegisterOperators( $tpl->Operators[$name] );
                $supported = isset( $tpl->Operators[$name] ) && get_class( $tpl->Operators[$name] ) === self::OPERATORS[$name];
            }
            self::$supported[$name] = $supported;
        }
        return self::$supported[$name];
    }

    /** @var eZTemplate|null the template object whose operators are checked (eZTemplate::factory()) */
    private static $tpl = null;

    /** @var array name => bool: the input is the first parameter ('param' mode of the kernel class) */
    private static $asParameter = array();

    public static function reset()
    {
        self::$supported = array();
        self::$tpl = null;
        self::$asParameter = array();
    }

    /**
     * A filter: input|name( parameters ), with the input mode of the bridge (expTemplateBridge::callOperator()).
     *
     * @return mixed the result, or NOT_HANDLED
     */
    public static function filter( $name, $input, array $parameters )
    {
        if ( !self::held( $name ) )
            return self::NOT_HANDLED;
        // cond and first_set get the input as the template system hands it, which they ignore (expTemplateBridge::callOperatorWithInput())
        if ( $name === 'cond' || $name === 'first_set' )
            return self::run( $name, self::value( $input ), $parameters );
        // the mode of the kernel class, which holds the name (held())
        if ( !isset( self::$asParameter[$name] ) )
            self::$asParameter[$name] = expTemplateBridge::operatorInputMode( $name ) === 'param';
        if ( self::$asParameter[$name] && !( $input === null && expTemplateBridge::isParameterOperator( $name ) ) )
        {
            array_unshift( $parameters, $input );
            $input = null;
        }
        return self::run( $name, self::value( $input ), $parameters );
    }

    /**
     * A function: name( parameters ), without an input.
     *
     * @return mixed the result, or NOT_HANDLED
     */
    public static function call( $name, array $parameters )
    {
        if ( !self::held( $name ) )
            return self::NOT_HANDLED;
        return self::run( $name, null, $parameters );
    }

    /**
     * @return bool whether the kernel class still holds the name now: an operator class loaded later can register the
     *              same name and take it over (strip_tags: eZTemplatePHPOperator, then eZTemplateStringsOperator)
     */
    public static function held( $name )
    {
        $tpl = self::$tpl !== null ? self::$tpl : ( self::$tpl = eZTemplate::factory() );
        return isset( $tpl->Operators[$name] ) && is_object( $tpl->Operators[$name] ) && get_class( $tpl->Operators[$name] ) === self::OPERATORS[$name];
    }

    /** A value as the operator sees it: objects of the template system and Twig captures unwrapped. */
    private static function value( $value )
    {
        return is_object( $value ) || is_array( $value ) ? expTemplateValues::unwrap( $value ) : $value;
    }

    private static function run( $name, $value, array $parameters )
    {
        foreach ( $parameters as $index => $parameter )
        {
            // an empty parameter (expvoid()) is the operator's default: left to the template system
            if ( $parameter instanceof expTemplateVoid )
                return self::NOT_HANDLED;
            $parameters[$index] = self::value( $parameter );
        }
        $count = count( $parameters );
        switch ( $name )
        {
            case 'eq':
            case 'ne':
                if ( $count < 1 )
                    return self::NOT_HANDLED;
                if ( $count === 1 )
                    return $name === 'eq' ? ( $parameters[0] == $value ) : ( $parameters[0] != $value );
                for ( $i = 1; $i < $count; ++$i )
                {
                    if ( $parameters[$i] != $parameters[$i - 1] )
                        return $name === 'ne';
                }
                return $name === 'eq';
            case 'lt':
            case 'gt':
            case 'le':
            case 'ge':
                if ( $value !== null )
                {
                    if ( $count < 1 )
                        return self::NOT_HANDLED;
                    $a = $value;
                    $b = $parameters[0];
                }
                else
                {
                    if ( $count < 2 )
                        return self::NOT_HANDLED;
                    $a = $parameters[0];
                    $b = $parameters[1];
                }
                $countA = eZTemplateLogicOperator::comparisonCount( $a );
                $countB = eZTemplateLogicOperator::comparisonCount( $b );
                // the threshold (the first parameter) is checked first, as modify() does; a type without a count warns there
                if ( $countA === false || $countB === false || eZTemplateLogicOperator::comparisonCount( $parameters[0] ) === false )
                    return self::NOT_HANDLED;
                switch ( $name )
                {
                    case 'lt': return $countA < $countB;
                    case 'gt': return $countA > $countB;
                    case 'le': return $countA <= $countB;
                    default: return $countA >= $countB;
                }
            case 'and':
                $operand = null;
                foreach ( $parameters as $operand )
                {
                    if ( !self::andTruth( $operand ) )
                        return false;
                }
                return $operand;
            case 'or':
                foreach ( $parameters as $operand )
                {
                    if ( self::orTruth( $operand ) )
                        return $operand;
                }
                return false;
            case 'not':
                $operand = ( $value === null && $count > 0 ) ? $parameters[0] : $value;
                if ( $operand === null )
                    return true;
                if ( is_array( $operand ) )
                    return count( $operand ) == 0;
                if ( is_object( $operand ) && method_exists( $operand, 'attributes' ) )
                    return count( $operand->attributes() ) == 0;
                if ( is_numeric( $operand ) )
                    return $operand == 0;
                if ( is_string( $operand ) )
                    return strlen( $operand ) == 0;
                return !$operand;
            case 'count':
                if ( $count > 1 )
                    return self::NOT_HANDLED;
                $mixed = $count === 0 ? $value : $parameters[0];
                if ( is_array( $mixed ) )
                    return count( $mixed );
                if ( is_object( $mixed ) && method_exists( $mixed, 'attributes' ) )
                    return count( $mixed->attributes() );
                if ( is_string( $mixed ) )
                    return strlen( $mixed );
                return 0;
            case 'sum':
            case 'sub':
            case 'mul':
            case 'div':
                if ( ( $name === 'mul' || $name === 'div' ) && $count < 1 )
                    return self::NOT_HANDLED;
                $values = $value !== null ? array_merge( array( $value ), $parameters ) : $parameters;
                return eZTemplateArithmeticOperator::calculate( $name, $values );
            case 'mod':
                if ( $count < 1 )
                    return self::NOT_HANDLED;
                list( $dividend, $divisor ) = $count === 1 ? array( $value, $parameters[0] ) : array( $parameters[0], $parameters[1] );
                if ( !is_numeric( $dividend ) || !is_numeric( $divisor ) || (int)$divisor == 0 )
                    return self::NOT_HANDLED;
                return $dividend % $divisor;
            case 'cond':
                // condition, value pairs, then a default; a value is the result of the first true condition
                $clauses = intdiv( $count, 2 );
                for ( $i = 0; $i < $clauses; ++$i )
                {
                    if ( $parameters[$i * 2] )
                        return $parameters[$i * 2 + 1];
                }
                if ( $count % 2 && $parameters[$count - 1] )
                    return $parameters[$count - 1];
                return $value;
            case 'first_set':
                foreach ( $parameters as $operand )
                {
                    if ( $operand !== null )
                        return $operand;
                }
                return null;
            case 'implode':
                // eZTemplateArrayOperator: implode( separator, array ); a string input or an array separator goes the normal way
                if ( !is_array( $value ) || $count < 1 || is_array( $parameters[0] ) || is_object( $parameters[0] ) )
                    return self::NOT_HANDLED;
                foreach ( $value as $element )
                {
                    if ( is_array( $element ) || ( is_object( $element ) && !method_exists( $element, '__toString' ) ) )
                        return self::NOT_HANDLED;
                }
                return implode( (string)$parameters[0], $value );
            case 'contains':
                // eZTemplateArrayOperator: in_array() for an array, mb_strpos() for a text
                if ( $count < 1 || is_array( $parameters[0] ) )
                    return self::NOT_HANDLED;
                if ( is_array( $value ) )
                    return in_array( $parameters[0], $value );
                if ( is_string( $value ) && ( is_string( $parameters[0] ) || is_int( $parameters[0] ) ) )
                    return mb_strpos( $value, (string)$parameters[0] ) !== false;
                return self::NOT_HANDLED;
            case 'concat':
                // eZTemplateTextOperator: the input (when there is one) and the parameters joined, objects left out
                $operands = array();
                if ( $value !== null )
                    $operands[] = $value;
                foreach ( $parameters as $parameter )
                {
                    if ( !is_object( $parameter ) )
                        $operands[] = $parameter;
                }
                foreach ( $operands as $operand )
                {
                    if ( is_array( $operand ) || is_object( $operand ) )
                        return self::NOT_HANDLED;
                }
                return implode( '', $operands );
            case 'wash':
                if ( ( $count > 0 && $parameters[0] !== 'xhtml' ) || $count > 1 || is_object( $value ) || is_array( $value ) )
                    return self::NOT_HANDLED;
                return htmlspecialchars( (string)$value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );
            case 'inc':
            case 'dec':
                if ( $value !== null )
                    $number = $value;
                else if ( $count > 0 )
                    $number = $parameters[0];
                else
                    return self::NOT_HANDLED;
                if ( !is_int( $number ) && !is_float( $number ) && !( is_string( $number ) && is_numeric( $number ) ) )
                    return self::NOT_HANDLED;
                return $name === 'dec' ? $number - 1 : $number + 1;
        }
        return self::NOT_HANDLED;
    }

    /** The truth of an operand of and (modify() of eZTemplateLogicOperator). */
    private static function andTruth( $operand )
    {
        if ( $operand === null || $operand === '' )
            return false;
        if ( $operand === true || $operand === false )
            return $operand;
        if ( is_array( $operand ) )
            return count( $operand ) > 0;
        if ( is_numeric( $operand ) )
            return $operand != 0;
        if ( is_object( $operand ) )
            return method_exists( $operand, 'attributes' ) && method_exists( $operand, 'attribute' );
        if ( is_string( $operand ) )
            return strlen( trim( $operand ) ) > 0;
        return false;
    }

    /** The truth of an operand of or (modify() of eZTemplateLogicOperator). */
    private static function orTruth( $operand )
    {
        if ( $operand === null )
            return false;
        if ( $operand === true || $operand === false )
            return $operand;
        if ( is_array( $operand ) )
            return count( $operand ) > 0;
        if ( is_numeric( $operand ) )
            return $operand != 0;
        if ( is_object( $operand ) )
            return method_exists( $operand, 'attributes' ) && method_exists( $operand, 'attribute' );
        if ( is_string( $operand ) )
            return strlen( trim( $operand ) ) > 0;
        return false;
    }
}
