<?php
/**
 * File containing the eZPaymentLogger class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPaymentLogger
*/

class eZPaymentLogger
{
    /**
     * The file is opened for each write rather than held open: loggers live on
     * workflow type objects, which a persistent worker keeps for its whole life,
     * so a held handle would outlive log rotation and pin the file.
     */
    public function __construct( $fileName, $mode )
    {
        $this->fileName = $fileName;
        $this->mode = $mode;
    }

    /**
     * Opens the log for one write. A truncating mode ("wt") applies to the
     * first write only, as it did when the file was opened once; later writes append.
     *
     * @return resource|false
     */
    protected function openFile()
    {
        $handle = @fopen( $this->fileName, $this->mode );
        if ( $handle && $this->mode[0] === 'w' )
            $this->mode = 'a' . substr( $this->mode, 1 );
        return $handle;
    }

    protected function writeLine( $line )
    {
        $handle = $this->openFile();
        if ( $handle )
        {
            fputs( $handle, $line );
            fclose( $handle );
        }
    }

    static function CreateNew($fileName)
    {
        return new eZPaymentLogger( $fileName, "wt" );
    }

    static function CreateForAdd($fileName)
    {
        return new eZPaymentLogger( $fileName, "a+t" );
    }

    function writeString( $string, $label='' )
    {
        if ( is_object( $string ) || is_array( $string ) )
            $string = eZDebug::dumpVariable( $string );

        if( $label == '' )
            $this->writeLine( $string."\r\n" );
        else
            $this->writeLine( $label . ': ' . $string."\r\n" );
    }

    function writeTimedString( $string, $label='' )
    {
        $time = $this->getTime();

        if ( is_object( $string ) || is_array( $string ) )
            $string = eZDebug::dumpVariable( $string );

        if( $label == '' )
            $this->writeLine( $time. '  '. $string. "\n" );
        else
            $this->writeLine( $time. '  '. $label. ': '. $string. "\n" );
    }

    static function getTime()
    {
        $time = date( "d-m-Y H-i" );
        return $time;
    }

    /**
     * @deprecated No handle is held any more (see __construct()); always null.
     */
    public $file = null;
    public $fileName;
    public $mode;
}
?>
