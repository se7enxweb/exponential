<?php
/**
 * File containing the bccieExportFormatOutputHandlerUtf16Le class
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class bccieExportFormatOutputHandlerUtf16Le extends bccieExportFormatOutputHandler
{
    public function formatOutput( $outputStringInput )
    {
        /*
        Originally introduced to provide for double click open suport for ms-excell on macosx
        Pull: https://github.com/brookinsconsulting/bccie/pull/10
        Origin: https://github.com/styleflashernewmedia/bccie/commit/0f25726eeef880de781cc4fd009f709cadac8126
        Documented: https://github.com/styleflashernewmedia/bccie/blob/ae0b29efe1c737f840ca0f1b24011777bd584321/doc/FAQ
        */

        $output = chr( 255 ) . chr( 254 ) . mb_convert_encoding( $outputStringInput, 'UTF-16LE', 'UTF-8' );

        return $output;
    }

    public function output( $outputStringInput )
    {
        self::outputHeaders();

        echo self::formatOutput( $outputStringInput );
    }
}

?>