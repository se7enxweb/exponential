<?php
/**
 * File containing the bccieExportFormatOutputHandler class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportFormatOutputHandler
{
    static protected $handlers = null;
    static protected $handlerKey = null;
    static protected $handlerClassName = null;

    static public $outputCharset = 'utf-8';
    static public $outputContentType = 'text/csv';
    static public $outputFileName = 'bccie_cie_export.csv';

    public static function instance( $argumentHandlerKey = null )
    {
        self::$handlers = eZINI::instance( 'cie.ini' )->variable( 'CieSettings', 'ExportOutputFormatHandlers' );

        if( $argumentHandlerKey == null )
        {
            self::$handlerKey = eZINI::instance( 'cie.ini' )->variable( 'CieSettings', 'ExportOutputFormatHandlerDefault' );
        } else {
            self::$handlerKey = $argumentHandlerKey;
        }

        self::$handlerClassName = self::$handlers[ self::$handlerKey ];

        if ( class_exists( self::$handlerClassName ) )
        {
            return new self::$handlerClassName();
        }
    }

    public function output( $outputStringInput )
    {
        self::outputHeaders();

        echo self::formatOutput( $outputStringInput );
    }

    public function formatOutput( $outputStringInput )
    {
        $output = $outputStringInput;

        return $output;
    }

    public function setOutputFileName( $fileName )
    {
        self::$outputFileName = $fileName;
    }

    public function setContentType( $contentType )
    {
        self::$outputContentType = $contentType;
    }

    public function setOutputCharset( $charset )
    {
        self::$outputCharset = $charset;
    }

    public function outputHeaders()
    {
        header( "Content-type:" . self::$outputContentType . "; charset=" . self::$outputCharset );
        header( "Content-Disposition: attachment; filename=" . self::$outputFileName );
    }
}

?>