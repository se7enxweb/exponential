<?php
/*
 * Copyright (C) 2011 OPEN WIDE
 * 
 * This file is part of OWSimpleOperator.
 *
 * OWSimpleOperator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Foobar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */
 
class OWSimpleOperator
{
    
    public $max_operator_parameter = 10;
    public $automatic_debug_output = false;
    public $operator_list;
    

    /*!
     * Constructor : Disable or enable automatic debug output based on ini settings
     */
    function __construct()
    {
        if( ( eZINI::instance( 'owsimpleoperator.ini' )->variable( 'OWSimpleOperatorSettings', 'DebugOutput' ) == 'enabled' ) )
        {
            $this->automatic_debug_output = true;
        }
    }

    /*********************************************************************
     UTILS : STRING MANIPULATION
     *********************************************************************/
    /*!
     * Checks if a string contains a specific element.
     */
    protected function string_contains( $hay, $needle )
    {
        return strpos( $hay, $needle ) !== false;
    }
    /*!
     * Checks if a string starts with a specific character/sequence.
     */
    protected function string_startswith( $hay, $needle )
    {
        return substr( $hay, 0, strlen( $needle ) ) == $needle;
    }
    

    /*********************************************************************
     UTILS : EZ OBJECT ATTRIBUTE MANIPULATION
     *********************************************************************/
    /*!
     * Checks if an ezobject ( or eznode ) has an attribute.
     */
    protected function ezattribute_has( $ezobject, $attribute_name )
    {
        $attribute_value = $this->get_attribute( $ezobject, $attribute_name );
        return ( $attribute_value !== null );
    }
    /*!
     * Get an ezobject ( or eznode ) attribute content 
     */
    protected function ezattribute_get( $ezobject, $attribute_name )
    {
        $attribute_value = null;
        if ( $this->is_ezobject_or_eznode( $ezobject, 'get_attribute' ) )
        {
            $data_map = $ezobject->DataMap( );
            if ( isset( $data_map[ $attribute_name ] ) )
            {
                $attribute_value = $data_map[ $attribute_name ]->value( );
            }
        }
        return $attribute_value;
    }
    

    /*********************************************************************
     UTILS : OBJECT TYPE CONTROL
     *********************************************************************/
    /*!
     * Checks if the variable is an ezobject or eznode
     * Log an error if the method name is passed as second parameter
     */
    protected function is_ezobject_or_eznode( $node, $method_name = false )
    {
        return $this->is_of_object_type( $node, array( 'eZContentObject', 'eZContentObjectTreeNode' ), $method_name );
    }
    /*!
     * Checks if the variable is an ezobject
     * Log an error if the method name is passed as second parameter
     */
    protected function is_ezobject( $object, $method_name = false )
    {
        return $this->is_of_object_type( $object, 'eZContentObject', $method_name );
    }
    /*!
     * Checks if the variable is an eznode
     * Log an error if the method name is passed as second parameter
     */
    protected function is_eznode( $node, $method_name = false )
    {
        return $this->is_of_object_type( $node, 'eZContentObjectTreeNode', $method_name );
    }
    /*!
     * Checks if the variable is an object of specified type
     * The specified type is passed as second parameter. It could be a string or an array.
     * Log an error if the method name is passed as third parameter
     */
    protected function is_of_object_type( $var, $classes, $method_name = false )
    {
        $is = false;
        
        // The classes could be an array or a string
        if ( !is_array( $classes ) )
        {
            $classes = array( $classes );
        }
        
        // Verify if the object type of var is in classes
        foreach ( $classes as $class )
        {
            if ( is_a( $var, $class ) )
            {
                $is = true;
                break;
            }
        }
        
        // Manage error case. If there are no function name, we don't generate an error
        if ( !$is && $method_name )
        {
            $type = is_object( $var ) ? get_class( $var ) : gettype( $var );
            $this->output_error( $method_name, 'The argument must be a "' . ( implode( ' or ' , $classes ) ) . '". "' . $type . '" given.' );
        }
        
        return $is;
    }
    

    /*********************************************************************
     UTILS : OUTPUT MANIPULATION
     *********************************************************************/
    /*!
     * Log an error text
     */
    protected function output_error( $method_name, $error )
    {
        ezDebug::writeError( $this->output_method_name( $method_name ) . "\n" . $error );
    }
    /*!
     * Log a debug text
     */
    protected function output_debug( $method_name, $debug )
    {
        ezDebug::writeDebug( $this->output_method_name( $method_name ) . "\n" . $debug );
    }
    /*!
     * Log a debug method result
     */
    protected function output_debug_method_result( $method_name, $arguments, $result )
    {
        if ( is_array( $arguments ) )
        {
            $length = count( $arguments );
            while ( $length > 0 && $arguments[ $length-1 ] == null )
            {
                $length--;
            }
            $arguments = array_slice( $arguments, 0, $length );
        }
        if ( $result !== null )
        {
            $result = ' => ' . $this->output_var( $result );
        }
        ezDebug::writeDebug( $this->output_method_name( $method_name, $arguments ) . $result );
    }
    /*!
     * Log a debug method result
     */
    protected function output_debug_function_result( $method_name, $arguments, $result )
    {
        if ( is_array( $arguments ) )
        {
            $length = count( $arguments );
            while ( $length > 0 && $arguments[ $length-1 ] == null )
            {
                $length--;
            }
            $arguments = array_slice( $arguments, 0, $length );
        }
        if ( $result !== null )
        {
            $result = ' => ' . $this->output_var( $result );
        }
        ezDebug::writeDebug( $this->output_function_name( $method_name, $arguments ) . $result );
    }
    /*!
     * Log a debug variable
     */
    protected function output_debug_value( $method_name, $name, $value )
    {
        $this->output_debug( $method_name, $name . ' => ' . $this->output_var( $value ) );
    }
    /*!
     * Get a readable version of a method name 
     */
    protected function output_method_name( $method_name, $arguments = null )
    {
        if ( is_array( $arguments ) )
        {
            $arguments = array_map( array( $this, 'output_var' ), $arguments );
        }
        else
        {
            $arguments = array( );
        }
        return get_class( $this ) . '->' . $method_name . '(' . implode( ', ', $arguments ) . ')';
    }
    /*!
     * Get a readable version of a method name 
     */
    protected function output_function_name( $method_name, $arguments = null )
    {
        if ( is_array( $arguments ) )
        {
            $arguments = array_map( array( $this, 'output_var' ), $arguments );
        }
        else
        {
            $arguments = array( );
        }
        return 'PHP Function: ' . $method_name . '(' . implode( ', ', $arguments ) . ')';
    }
    /*!
     * Get a readable version of a variable value  
     */
    protected function output_var( $value )
    {
        if ( is_bool( $value ) )
        {
            $data_value = $value ? 'true' : 'false';
        }
        else if ( is_null( $value ) )
        {
            $data_value = 'null';
        }
        else if ( is_float( $value ) )
        {
            $data_value = $value;
        }
        else if ( is_object( $value ) )
        {
            if ( $this->is_ezobject( $value ) )
            {
                $data_value = $value->attribute( 'class_identifier' ) . '(Object#' . $value->ID . ')';
            }
            else if ( $this->is_eznode( $value ) )
            {
                $data_value = $value->attribute( 'class_identifier' ) . '(Node#' . $value->attribute( 'node_id' ) . ')';
            }
            else
            {
                $data_value = get_class( $value ) . '()';
            }
        }
        else if ( is_array( $value ) )
        {
            $data_value = print_r( $value, true );
        }
        else 
        {
            $data_value = '"' . $value . '"';
        }
        return $data_value;
    }
    
    
    
    
    
    


    /*********************************************************************
     KERNEL : OPERATORS LAUNCHER
     *********************************************************************/
    public function modify( $tpl, $operator_name, $operator_parameters, $root_namespace, $current_namespace, &$operator_value, $named_parameters )
    {
        if ( method_exists( $this, $operator_name ) )
        {
            $method_arguments = array_values( $named_parameters );
            
            // If there are an operator value, we switch it with our parameter
            if ( $operator_value !== null )
            {
                array_pop( $method_arguments );
                array_unshift( $method_arguments, $operator_value );
            }

            // We call directly the operator method with all parameter
            $method_call = array( $this, $operator_name );
            $operator_value = call_user_func_array( $method_call, $method_arguments );
            
            if ( $this->automatic_debug_output )
            {
                $this->output_debug_method_result( $operator_name, $method_arguments, $operator_value );
            }
        }
        elseif ( function_exists( $operator_name ) )
        {
	    $method_arguments = array();

            foreach( $named_parameters as $index => $parameter )
            {
	       if( isset( $parameter ) )
               {
                   $method_arguments[$index] = $parameter;
               }
            }
            
            // If there are an operator value, we switch it with our parameter
            if ( $operator_value !== null )
            {
                array_pop( $method_arguments );
                array_unshift( $method_arguments, $operator_value );
            }

            // We call directly the operator method with all parameter
            $method_call = $operator_name;
            $operator_value = call_user_func_array( $method_call, $method_arguments );
            
            if ( $this->automatic_debug_output )
            {
                $this->output_debug_function_result( $operator_name, $method_arguments, $operator_value );
            }
        }
        else
        {
            $this->output_error( 'modify', 'The method "' . $operator_name . '" doesn\'t exists.' );
            $operator_value = null;
        }
    }


    /*********************************************************************
     KERNEL : OPERATORS CONFIGURATION
     *********************************************************************/
    public function namedParameterList( )
    {
        // Get the default definition of an operator
        $default_parameter_definition = array( 'type' => 'mixed', 'required' => false, 'default' => null );
        $default_operator_definition = array_fill( 0, $this->max_operator_parameter, $default_parameter_definition );
        
        // Get the definition of the operators parameters
        $operators_definition = array_fill_keys( $this->operatorList( ), $default_operator_definition );
        return $operators_definition;
    }
    
    public function operatorList( )
    {
        if ( !isset( $this->operator_list ) )
        {
            $operator_list = array( );
            
            // We will search the template operator in the eztemplateautoload.php
            $class_name = get_class( $this );
            $parent_class_name = get_parent_class( $this );
            $folder_path = OWEzpAutoloader::get_class_folder_path( $parent_class_name );
            if ( !empty( $folder_path ) )
            {
                $autoload_file = $folder_path . '/eztemplateautoload.php';
                if ( file_exists( $autoload_file ) )
                {
                    include( $autoload_file );
                    if ( isset( $eZTemplateOperatorArray ) )
                    {
                        if ( is_array( $eZTemplateOperatorArray ) )
                        {
                            $found = false;
                            foreach ( $eZTemplateOperatorArray as $operator_definition )
                            {
                                if ( is_array( $operator_definition ) && $operator_definition[ 'class' ] == $class_name )
                                {
                                    $operator_list = $operator_definition[ 'operator_names' ];
                                    $found = true;
                                    break;
                                }
                            }
                            if ( !$found )
                            {
                                $this->output_error( 'operatorList', 'The variable "eZTemplateOperatorArray" does not contains the operator_names for the class "' . $class_name . '".' );
                            }
                        }
                        else
                        {
                            $this->output_error( 'operatorList', 'The variable "eZTemplateOperatorArray" must be an array in the "' . $autoload_file . '" file.' );
                        }
                    }
                    else
                    {
                        $this->output_error( 'operatorList', 'There are no "eZTemplateOperatorArray" variable in the "' . $autoload_file . '" file.' );
                    }
                }
                else
                {
                    $this->output_error( 'operatorList', 'The file "' . $autoload_file . '" does not exist.' );
                }
            }
            else
            {
                $this->output_error( 'operatorList', 'The class "' . $class_name . '" is not registered in the autoload array.' );
            }
            if ( empty( $operator_list ) )
            {
                $this->output_error( 'operatorList', 'There are no operators registered for the class "' . $class_name . '".' );
            }
            $this->operator_list = $operator_list;
        }
        return $this->operator_list;
    }

    public function namedParameterPerOperator( )
    {
        return true;
    }
}

?>