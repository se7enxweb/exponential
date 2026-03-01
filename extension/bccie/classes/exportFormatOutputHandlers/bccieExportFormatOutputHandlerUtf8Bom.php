<?php
/**
 * File containing the bccieExportFormatOutputHandlerUtf8Bom class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportFormatOutputHandlerUtf8Bom extends bccieExportFormatOutputHandler
{
    public function formatOutput( $outputStringInput )
    {
        /*
        Originally introduced to provide for double click open suport for ms-excell on windows
        Discussion: http://projects.ez.no/cie/forum/general/special_characters_problem
        Origin: https://github.com/brookinsconsulting/bccie/commit/6cf034a06217ab4a63d8ee9826ea4203851562fc#diff-fe55f0c28888e171ca6920cbfc0dc978
        */

        $output = "\xEF\xBB\xBF" . $outputStringInput;

        return $output;
    }

    public function output( $outputStringInput )
    {
        self::outputHeaders();

        echo self::formatOutput( $outputStringInput );
    }
}

?>