<?php

class ExampleOperator extends OWSimpleOperator
{
    
    /*!
     * Return the sum of two numbers
     */
    public function example_sum( $number1, $number2 )
    {
        return ( $number1 + $number2 );
    }
    
}