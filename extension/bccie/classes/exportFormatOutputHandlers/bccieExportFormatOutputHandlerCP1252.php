<?php
/**
 * File containing the bccieExportFormatOutputHandlerCP1252 class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportFormatOutputHandlerCP1252 extends bccieExportFormatOutputHandler
{
    public function formatOutput( $outputStringInput )
    {
        /*
        Originally introduced to provide for windows-1251 charset output
        Pull: https://github.com/brookinsconsulting/bccie/pull/16
        Origin: https://github.com/Open-Wide/bccie/commit/a5e6976d79979a5a0ee565a0bd1e87aadb8646ca
        Format documentation: http://en.wikipedia.org/wiki/Windows-1252
        http://www.cp1252.com
        */

        $output = mb_convert_encoding( $outputStringInput, 'CP1252' );

        return $output;
    }

    public function output( $outputStringInput )
    {
        self::outputHeaders();

        echo self::formatOutput( $outputStringInput );
    }
}

?>