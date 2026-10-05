<?php
/**
 * File containing the eZTemplateArithmeticOperator class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateArithmeticOperator eztemplatearithmeticoperator.php
  \brief The class eZTemplateArithmeticOperator does

  sum
  sub
  inc
  dec

  div
  mod
  mul

  max
  min

  abs
  ceil
  floor
  round

  int
  float

  count

*/

class eZTemplateArithmeticOperator
{
    public function __construct()
    {
        $this->Operators = array( 'sum', 'sub', 'inc', 'dec',
                                  'div', 'mod', 'mul',
                                  'max', 'min',
                                  'abs', 'ceil', 'floor', 'round',
                                  'int', 'float',
                                  'count',
                                  'roman',
                                  'rand' );
        foreach ( $this->Operators as $operator )
        {
            $name = $operator . 'Name';
            $name[0] = $name[0] & "\xdf";
            $this->$name = $operator;
        }
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    function operatorTemplateHints()
    {
        return array( $this->SumName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'basicTransformation'),
                      $this->SubName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'basicTransformation'),
                      $this->MulName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'basicTransformation'),
                      $this->DivName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'basicTransformation'),

                      $this->IncName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => 1,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'decIncTransformation'),
                      $this->DecName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => 1,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'decIncTransformation'),

                      $this->ModName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => 2,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'modTransformation'),

                      $this->MaxName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'minMaxTransformation'),
                      $this->MinName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => true,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'minMaxTransformation'),

                      $this->AbsName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => 1,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'roundTransformation'),
                      $this->CeilName => array( 'input' => true,
                                                'output' => true,
                                                'parameters' => 1,
                                                'element-transformation' => true,
                                                'transform-parameters' => true,
                                                'input-as-parameter' => true,
                                                'element-transformation-func' => 'roundTransformation'),
                      $this->FloorName => array( 'input' => true,
                                                 'output' => true,
                                                 'parameters' => 1,
                                                 'element-transformation' => true,
                                                 'transform-parameters' => true,
                                                 'input-as-parameter' => true,
                                                 'element-transformation-func' => 'roundTransformation'),
                      $this->RoundName => array( 'input' => true,
                                                 'output' => true,
                                                 'parameters' => 1,
                                                 'element-transformation' => true,
                                                 'transform-parameters' => true,
                                                 'input-as-parameter' => true,
                                                 'element-transformation-func' => 'roundTransformation'),

                      $this->IntName => array( 'input' => true,
                                               'output' => true,
                                               'parameters' => 1,
                                               'element-transformation' => true,
                                               'transform-parameters' => true,
                                               'input-as-parameter' => true,
                                               'element-transformation-func' => 'castTransformation'),
                      $this->FloatName => array( 'input' => true,
                                                 'output' => true,
                                                 'parameters' => 1,
                                                 'element-transformation' => true,
                                                 'transform-parameters' => true,
                                                 'input-as-parameter' => true,
                                                 'element-transformation-func' => 'castTransformation'),

                      $this->RomanName => array( 'input' => true,
                                                 'output' => true,
                                                 'parameters' => 1,
                                                 'element-transformation' => true,
                                                 'transform-parameters' => true,
                                                 'input-as-parameter' => true,
                                                 'element-transformation-func' => 'romanTransformation'),

                      $this->CountName => array( 'input' => true,
                                                 'output' => true,
                                                 'parameters' => 1 ),

                      $this->RandName => array( 'input' => true,
                                                'output' => true,
                                                'parameters' => true,
                                                'element-transformation' => true,
                                                'transform-parameters' => true,
                                                'input-as-parameter' => true,
                                                'element-transformation-func' => 'randTransformation') );
    }

    /*!
     Compiles sum, sub, mul and div. With only constant operands the result is calculated now, otherwise the
     compiled template calls calculate() at run time, the same function the interpreter uses, so a template gives
     the same result however it is run.
    */
    function basicTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        if ( count( $parameters ) == 0 )
            return false;

        $allConstant = true;
        foreach ( $parameters as $parameter )
        {
            if ( !eZTemplateNodeTool::isConstantElement( $parameter ) )
            {
                $allConstant = false;
                break;
            }
        }

        if ( $allConstant )
        {
            $operands = array();
            foreach ( $parameters as $parameter )
                $operands[] = eZTemplateNodeTool::elementConstantValue( $parameter );
            return array( eZTemplateNodeTool::createNumericElement( self::calculate( $operatorName, $operands ) ) );
        }

        $values = array();
        $operandCode = array();
        $counter = 1;
        foreach ( $parameters as $parameter )
        {
            if ( eZTemplateNodeTool::isConstantElement( $parameter ) )
            {
                $operandCode[] = var_export( eZTemplateNodeTool::elementConstantValue( $parameter ), true );
            }
            else
            {
                $operandCode[] = "%$counter%";
                $values[] = $parameter;
                ++$counter;
            }
        }
        $code = '%output% = eZTemplateArithmeticOperator::calculate( ' . var_export( $operatorName, true ) .
                ', array( ' . implode( ', ', $operandCode ) . " ) );\n";
        return array( eZTemplateNodeTool::createCodePieceElement( $code, $values, false, false, 'integer' ) );
    }

    /**
     * The number a value stands for in sum, sub, mul and div.
     *
     * Integers and floats are used as they are, a numeric string as the number it holds, a string with a number in
     * front as that number, a boolean as 0 or 1, null and any other string as 0, an array as 0 when empty and 1
     * otherwise, and an object as 0.
     *
     * @param mixed $value
     * @return int|float
     */
    public static function numericOperand( $value )
    {
        if ( is_int( $value ) || is_float( $value ) )
            return $value;
        if ( is_string( $value ) )
        {
            if ( is_numeric( $value ) )
                return $value + 0;
            $number = (float)$value;
            return ( $number == (int)$number ) ? (int)$number : $number;
        }
        if ( is_object( $value ) )
            return 0;
        return (int)$value;
    }

    /**
     * Calculates sum, sub, mul or div over $operands, in order.
     *
     * sum adds them all, sub takes every following one from the first, mul multiplies them and div divides the
     * first by each following one; dividing by zero gives 0. No operands give 0. The interpreter and compiled
     * templates both calculate through here.
     *
     * @param string $operatorName sum, sub, mul or div
     * @param array $operands
     * @return int|float
     */
    public static function calculate( $operatorName, $operands )
    {
        $result = 0;
        $first = true;
        foreach ( $operands as $operand )
        {
            $operand = self::numericOperand( $operand );
            if ( $first )
            {
                $result = $operand;
                $first = false;
                continue;
            }
            switch ( $operatorName )
            {
                case 'sum':
                    $result += $operand;
                    break;
                case 'sub':
                    $result -= $operand;
                    break;
                case 'mul':
                    $result *= $operand;
                    break;
                case 'div':
                    $result = ( $operand == 0 ) ? 0 : $result / $operand;
                    break;
            }
        }
        return $result;
    }

    function minMaxTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                   $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        $function = $operatorName;

        if ( count( $parameters ) == 0 )
            return false;
        $newElements = array();

        /* Check if all variables are integers. This is for optimization */
        $staticResult = array();
        $allNumeric = true;
        foreach ( $parameters as $parameter )
        {
            if ( !eZTemplateNodeTool::isConstantElement( $parameter ) )
            {
                $allNumeric = false;
            }
            else
            {
                $staticResult[] = eZTemplateNodeTool::elementConstantValue( $parameter );
            }
        }

        if ( $allNumeric )
        {
            $staticResult = $function( $staticResult );
            return array( eZTemplateNodeTool::createNumericElement( $staticResult ) );
        }
        else
        {
            $code = "%output% = $function(";
            $counter = 1;
            foreach ( $parameters as $parameter )
            {
                if ( $counter > 1 )
                {
                    $code .= ', ';
                }
                $code .= " %$counter%";
                $values[] = $parameter;
                ++$counter;
            }
            $code .= ");\n";
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function modTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        if ( count( $parameters ) != 2 )
            return false;
        $newElements = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) && eZTemplateNodeTool::isConstantElement( $parameters[1] ) )
        {
            $staticResult = eZTemplateNodeTool::elementConstantValue( $parameters[0] ) % eZTemplateNodeTool::elementConstantValue( $parameters[1] );
            return array( eZTemplateNodeTool::createNumericElement( $staticResult ) );
        }
        else
        {
            $code = "%output% = %1% % %2%;\n";
            $values[] = $parameters[0];
            $values[] = $parameters[1];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function roundTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        $function = $operatorName;

        if ( count( $parameters ) != 1 )
            return false;
        $newElements = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = $function( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) );
            return array( eZTemplateNodeTool::createNumericElement( $staticResult ) );
        }
        else
        {
            $code = "%output% = $function( %1% );\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function decIncTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        $function = $operatorName;
        $direction = $this->DecName == $function ? -1 : 1;

        if ( count( $parameters ) < 1 )
            return false;
        $newElements = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            return array( eZTemplateNodeTool::createNumericElement( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) + $direction ) );
        }
        else
        {
            $code = "%output% = %1% + $direction;\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function castTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        if ( count( $parameters ) != 1 )
            return false;
        $newElements = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = ( $operatorName == $this->IntName ) ? (int) eZTemplateNodeTool::elementConstantValue( $parameters[0] ) : (float) eZTemplateNodeTool::elementConstantValue( $parameters[0] );
            return array( eZTemplateNodeTool::createNumericElement( $staticResult ) );
        }
        else
        {
            $code = "%output% = ($operatorName)%1%;\n";
            $values[] = $parameters[0];
        }
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function randTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                 $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $paramCount = count( $parameters );
        if ( $paramCount != 0 ||
             $paramCount != 2 )
        {
            return false;
        }
        $values = array();
        $newElements = array();

        if ( $paramCount == 2 )
        {
            $code = "%output% = mt_rand( %1%, %2% );\n";
            $values[] = $parameters[0];
            $values[] = $parameters[1];
        }
        else
        {
            $code = "%output% = mt_rand();\n";
        }

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( $code, $values );
        return $newElements;
    }

    function romanTransformation( $operatorName, &$node, $tpl, &$resourceData,
                                  $element, $lastElement, $elementList, $elementTree, &$parameters )
    {
        $values = array();
        if ( count( $parameters ) != 1 )
            return false;
        $newElements = array();

        if ( eZTemplateNodeTool::isConstantElement( $parameters[0] ) )
        {
            $staticResult = $this->buildRoman( eZTemplateNodeTool::elementConstantValue( $parameters[0] ) );
            return array( eZTemplateNodeTool::createNumericElement( $staticResult ) );
        }
        else
        {
            return false;
        }
    }

    /*!
     \return true to tell the template engine that the parameter list exists per operator type.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     See eZTemplateOperator::namedParameterList
    */
    function namedParameterList()
    {
        return array( $this->IncName => array( 'value' => array( 'type' => 'mixed',
                                                                 'required' => false,
                                                                 'default' => false ) ),
                      $this->DecName => array( 'value' => array( 'type' => 'mixed',
                                                                 'required' => false,
                                                                 'default' => false ) ),
                      $this->RomanName => array( 'value' => array( 'type' => 'mixed',
                                                                   'required' => false,
                                                                   'default' => false ) ) );
    }

    /*!
     \private
     \obsolete This function adds too much complexity, don't use it anymore
    */
    function numericalValue( $mixedValue )
    {
        if ( is_array( $mixedValue ) )
        {
            return count( $mixedValue );
        }
        else if ( is_object( $mixedValue ) )
        {
            if ( method_exists( $mixedValue, 'attributes' ) )
                return count( $mixedValue->attributes() );
            else if ( method_exists( $mixedValue, 'numericalValue' ) )
                return $mixedValue->numericalValue();
        }
        else if ( is_numeric( $mixedValue ) )
            return $mixedValue;
        else
            return 0;
    }

    /*!
     Examines the input value and outputs a boolean value. See class documentation for more information.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters,
                     $placement )
    {
        switch ( $operatorName )
        {
            case $this->RomanName:
            {
                if ( $namedParameters['value'] !== false )
                    $value = $namedParameters['value'];
                else
                    $value = $operatorValue;

                $operatorValue = $this->buildRoman( $value );
            } break;
            case $this->CountName:
            {
                if ( count( $operatorParameters ) == 0 )
                    $mixedValue =& $operatorValue;
                else
                    $mixedValue = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                if ( count( $operatorParameters ) > 1 )
                    $tpl->extraParameters( $operatorName, count( $operatorParameters ), 1 );
                if ( is_array( $mixedValue ) )
                    $operatorValue = count( $mixedValue );
                else if ( is_object( $mixedValue ) and
                          method_exists( $mixedValue, 'attributes' ) )
                    $operatorValue = count( $mixedValue->attributes() );
                else if ( is_string( $mixedValue ) )
                    $operatorValue = strlen( $mixedValue );
                else
                    $operatorValue = 0;
            } break;
            case $this->SumName:
            case $this->SubName:
            {
                $values = array();
                if ( $operatorValue !== null )
                    $values[] = $operatorValue;
                for ( $i = 0; $i < count( $operatorParameters ); ++$i )
                {
                    $values[] = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                }
                $operatorValue = self::calculate( $operatorName, $values );
            } break;
            case $this->IncName:
            case $this->DecName:
            {
                if ( $operatorValue !== null )
                    $value = $operatorValue;
                else
                    $value = $namedParameters['value'];
                if ( $operatorName == $this->DecName )
                    --$value;
                else
                    ++$value;
                $operatorValue = $value;
            } break;
            case $this->DivName:
            {
                if ( count( $operatorParameters ) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $values = array();
                if ( $operatorValue !== null )
                    $values[] = $operatorValue;
                for ( $i = 0; $i < count( $operatorParameters ); ++$i )
                {
                    $values[] = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                }
                $operatorValue = self::calculate( $operatorName, $values );
            } break;
            case $this->ModName:
            {
                if ( count( $operatorParameters ) < 1 )
                {
                    $tpl->warning( $operatorName, 'Missing dividend and divisor', $placement );
                    return;
                }
                if ( count( $operatorParameters ) == 1 )
                {
                    $dividend = $operatorValue;
                    $divisor = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                }
                else
                {
                    $dividend = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                    $divisor = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace, $placement );
                }
                $operatorValue = $dividend % $divisor;
            } break;
            case $this->MulName:
            {
                if ( count( $operatorParameters ) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $values = array();
                if ( $operatorValue !== null )
                    $values[] = $operatorValue;
                for ( $i = 0; $i < count( $operatorParameters ); ++$i )
                {
                    $values[] = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                }
                $operatorValue = self::calculate( $operatorName, $values );
            } break;
            case $this->MaxName:
            {
                if ( count( $operatorParameters ) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $i = 0;
                if ( $operatorValue !== null )
                {
                    $value = $operatorValue;
                }
                else
                {
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                    ++$i;
                }
                for ( ; $i < count( $operatorParameters ); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( $tmpValue > $value )
                        $value = $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->MinName:
            {
                if ( count( $operatorParameters ) < 1 )
                {
                    $tpl->warning( $operatorName, 'Requires at least 1 parameter value', $placement );
                    return;
                }
                $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                for ( $i = 1; $i < count( $operatorParameters ); ++$i )
                {
                    $tmpValue = $tpl->elementValue( $operatorParameters[$i], $rootNamespace, $currentNamespace, $placement );
                    if ( $tmpValue < $value )
                        $value = $tmpValue;
                }
                $operatorValue = $value;
            } break;
            case $this->AbsName:
            case $this->CeilName:
            case $this->FloorName:
            case $this->RoundName:
            {
                if ( count( $operatorParameters ) < 1 )
                    $value = $operatorValue;
                else
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                switch ( $operatorName )
                {
                    case $this->AbsName:
                    {
                        $operatorValue = abs( $value );
                    } break;
                    case $this->CeilName:
                    {
                        $operatorValue = ceil( $value );
                    } break;
                    case $this->FloorName:
                    {
                        $operatorValue = floor( $value );
                    } break;
                    case $this->RoundName:
                    {
                        $operatorValue = round( $value );
                    } break;
                }
            } break;
            case $this->IntName:
            {
                if ( count( $operatorParameters ) > 0 )
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                else
                    $value = $operatorValue;
                $operatorValue = (int)$value;
            } break;
            case $this->FloatName:
            {
                if ( count( $operatorParameters ) > 0 )
                    $value = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement );
                else
                    $value = $operatorValue;
                $operatorValue = (float)$value;
            } break;
            case $this->RandName:
            {
                if ( count( $operatorParameters ) == 2 )
                {
                    $operatorValue = mt_rand( $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace, $placement ),
                                              $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace, $placement ) );
                }
                else
                {
                    $operatorValue = mt_rand();
                }
            } break;
        }
    }

    /// \privatesection

    /*!
     \private

     Recursive function for calculating roman numeral from integer

     \param integer value
     \return next chars for for current value
    */
    function buildRoman( $value )
    {
        if ( $value >= 1000 )
            return 'M'.$this->buildRoman( $value - 1000 );
        if ( $value >= 500 )
        {
            if ( $value >= 900 )
                return 'CM'.$this->buildRoman( $value - 900 );
            else
                return 'D'.$this->buildRoman( $value - 500 );
        }
        if ( $value >= 100 )
        {
            if( $value >= 400 )
                return 'CD'.$this->buildRoman( $value - 400 );
            else
                return 'C'.$this->buildRoman( $value - 100 );
        }
        if ( $value >= 50 )
        {
            if( $value >= 90 )
                return 'XC'.$this->buildRoman( $value - 90 );
            else
                return 'L'.$this->buildRoman( $value - 50 );
        }
        if ( $value >= 10 )
        {
            if( $value >= 40 )
                return 'XL'.$this->buildRoman( $value - 40 );
            else
                return 'X'.$this->buildRoman( $value - 10 );
        }
        if ( $value >= 5 )
        {
            if( $value == 9 )
                return 'IX'.$this->buildRoman( $value - 9 );
            else
                return 'V'.$this->buildRoman( $value - 5 );
        }
        if ( $value >= 1 )
        {
            if( $value == 4 )
                return 'IV'.$this->buildRoman( $value - 4 );
            else
                return 'I'.$this->buildRoman( $value - 1 );
        }
        return '';
    }

    public $Operators;
    public $SumName;
    public $SubName;
    public $IncName;
    public $DecName;

    public $DivName;
    public $ModName;
    public $MulName;

    public $MaxName;
    public $MinName;

    public $AbsName;
    public $CeilName;
    public $FloorName;
    public $RoundName;

    public $IntName;
    public $FloatName;

    public $CountName;

    public $RomanName;

    public $RandName;
}

?>
