<?php
//
// Swark - extension for eZ Publish
// Author: Jan Kudlicka <jk@seeds.no>
// Copyright (C) 2008 Seeds Consulting AS, http://www.seeds.no/
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of version 2.0 of the GNU General
// Public License as published by the Free Software Foundation.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
// MA 02110-1301, USA.
//

class SwarkOperator
{
    private $OperatorName = false;
    private $Parameters;

    function __construct( $operatorName = false )
    {
        $this->OperatorName = $operatorName;

        $parameters = func_get_args();
        array_shift( $parameters );
        $this->Parameters = array();
        foreach ( $parameters as $parameter )
        {
            $parameterArray = explode( '=', $parameter, 2 );
            $parameterName = $parameterArray[0];
            if ( count( $parameterArray ) == 2 )
            {
                $this->Parameters[$parameterName] = array( 'type' => 'mixed', 'required' => false, 'default' => $parameterArray[1] );
            }
            else
            {
                $this->Parameters[$parameterName] = array( 'type' => 'mixed', 'required' => true );
            }
        }
    }

    function operatorList()
    {
        return array( $this->OperatorName );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( $this->OperatorName => $this->Parameters );
    }

    static function execute( $operatorValue, $namedParameters )
    {
        return $operatorValue;
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace,
                     $currentNamespace, &$operatorValue, $namedParameters )
    {
        $operatorValue = call_user_func( array( get_class( $this ), 'execute' ), $operatorValue, $namedParameters );
    }

    function operatorTemplateHints()
    {
        return array( $this->OperatorName => array( 'parameters' => true,
                                                    'input' => true,
                                                    'output' => true,
                                                    'element-transformation' => true,
                                                    'transform-parameters' => true,
                                                    'input-as-parameter' => 'always',
                                                    'element-transformation-func' => 'transformation' ) );
    }

    function transformation( $operatorName, $node, $tpl, $resourceData,
                             $element, $lastElement, $elementList, $elementTree, $parameters )
    {
        include_once( 'extension/swark/autoloads/SwarkOperators.php' );
        
        $operators = SwarkOperators::operators();
        $operatorInfo = $operators[$operatorName];
        $namedParametersVarName = '$namedParameters' . md5( mt_rand() );

        $newElements[] = eZTemplateNodeTool::createCodePieceElement( "unset( $namedParametersVarName );\n$namedParametersVarName = array();\n" );
        $index = 2;
        foreach( $this->Parameters as $parameterName => $parameter )
        {
            if ( isset( $parameters[$index-1] ) )
            {
                $newElements[] = eZTemplateNodeTool::createCodePieceElement( "{$namedParametersVarName}['$parameterName'] = %$index%;\n", $parameters );
            }
            else
            {
                $newElements[] = eZTemplateNodeTool::createCodePieceElement( "{$namedParametersVarName}['$parameterName'] = " . 
                    var_export( $parameter['default'], true ) . ";\n" );
            }
            $index++;
        }
        // $newElements[] = eZTemplateNodeTool::createCodePieceElement( "include_once( '${operatorInfo['script']}' );\n" );
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( "%output% = {$operatorInfo['class']}::execute( %1%, $namedParametersVarName );\n", $parameters );
        $newElements[] = eZTemplateNodeTool::createCodePieceElement( "unset( $namedParametersVarName );\n" );

        return $newElements;
    }
}

?>