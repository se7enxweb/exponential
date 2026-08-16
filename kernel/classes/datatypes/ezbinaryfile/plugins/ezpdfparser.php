<?php
/**
 * File containing the eZPDFParser class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZPDFParser ezpdfparser.php
  \ingroup eZKernel
  \brief The class eZPDFParser handles parsing of PDF files and returns the metadata

*/

class eZPDFParser
{
    function parseFile( $fileName )
    {
        $binaryINI = eZINI::instance( 'binaryfile.ini' );

        $textExtractionTool = $binaryINI->variable( 'PDFHandlerSettings', 'TextExtractionTool' );

        // save the buffer contents
        $buffer = ob_get_contents();
        if ( $buffer )
        {
            ob_end_clean();
        }

        // fetch the module printout
        ob_start();
        // Security hardening (F-05, CWE-78): escape the shell arguments so a path
        // containing shell metacharacters cannot alter the command.
        passthru( escapeshellcmd( $textExtractionTool ) . ' ' . escapeshellarg( $fileName ) );
        $metaData = ob_get_contents();
        ob_end_clean();

        // fill the buffer with the old values
        ob_start();
        print( $buffer );

        return $metaData;
    }
}

?>
