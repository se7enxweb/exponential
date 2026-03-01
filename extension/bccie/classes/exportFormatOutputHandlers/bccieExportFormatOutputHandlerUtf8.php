<?php
/**
 * File containing the bccieExportFormatOutputHandlerUtf8 class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportFormatOutputHandlerUtf8 extends bccieExportFormatOutputHandler
{
    public function formatOutput( $outputStringInput )
    {
        /*
            Default output string format is and always has been utf8. Which is provided by default automatically.
        */

        return $outputStringInput;
    }

    public function output( $outputStringInput )
    {
        self::outputHeaders();

        echo self::formatOutput( $outputStringInput );
    }
}

?>