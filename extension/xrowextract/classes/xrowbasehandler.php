<?php

class XrowBaseHandler
{
    //place holder
    function exportAttribute( &$attribute )
    {
    }

    //escape the string to use it in a CSV file type
    public function escape( $stringtoescape )
    {
        //ASCII/CRLF=0x0D 0x0A   13 10
        if ( $this->escape and ( strpos( $stringtoescape, $this->encloseChar ) >=0 or
             strpos( $stringtoescape, $this->separationChar ) >=0 or
             strpos( $stringtoescape, chr(13)) >=0 or // CR
             strpos( $stringtoescape, chr(10)) >=0 )    // LF
           )
        {
           $stringtoescape = str_replace( $this->encloseChar, $this->encloseChar . $this->encloseChar, $stringtoescape );
           return $this->encloseChar . $stringtoescape . $this->encloseChar;
        }
        else
        {
            $stringtoescape = str_replace( chr(13), '', $stringtoescape );
            $stringtoescape = str_replace( chr(10), '', $stringtoescape );
            return $stringtoescape;
        }
    }

    public $encloseChar  = '"';
    public $separationChar = ",";
    public $escape = false;
}
?>
