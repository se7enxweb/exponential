<?php


/*
    return the datatypes (identifiers) that can be exported
*/

class ParserInterface
{
    //holds the lookup map for the handler mapings
    public $handlerMap=array();
    public $exportableDatatypes;
    public $separationChar = ",";
    public $escape = true;

    public function __construct( $separationChar = null, $escape = null )
    {
        if ( $escape === true or $escape === false )
           $this->escape = $escape;
        if ( $separationChar !== null )
           $this->separationChar = $separationChar;
        $ini = eZINI::instance( "csv.ini" );
        $this->exportableDatatypes = $ini->variable( "General", "ExportableDatatypes" );
        foreach ($this->exportableDatatypes as $typename)
        {
            if ( file_exists( $ini->variable( $typename, 'HandlerFile' ) ) )
            {
                 //#include_once("extension/extract/classes/parsers/".$ini->variable( $typename, 'HandlerFile' ) );
                 $classname = $ini->variable( $typename, 'HandlerClass' );
                 $handler = new $classname();
                 if( isset( $handler->separationChar ) )
                 {
                     $handler->separationChar = $this->separationChar;
                 }
                 if( isset( $handler->escape ) )
                 {
                     $handler->escape = $this->escape;
                 }
                 $this->handlerMap[$typename] = array( "handler" => $handler,
                                                       "exportable" => true );
            }
            else
                eZDebug::writeError( "Error loading " . $ini->variable($typename, 'HandlerFile'), "Extract" );
        }
    }

    public function getExportableDatatypes()
    {
        return $this->exportableDatatypes;
    }

    /*
        Export an attribute to a string
    */
    public function exportAttribute( &$attribute )
    {
        if( isset( $this->handlerMap[$attribute->DataTypeString]['handler'] ) )
        {
            $handler = $this->handlerMap[$attribute->DataTypeString]['handler'];
        }

        if( isset($handler) && is_object( $handler ) )
        {
            return $handler->exportAttribute( $attribute ).$this->separationChar;
        }
        else
            return $this->separationChar;
    }

    public function escape( $text )
    {
        $handler = new BaseHandler();
        $handler->separationChar = $this->separationChar;
        $handler->escape = $this->escape;
        return $handler->escape( $text );
    }
}