<?php
/**
 * File containing the basehandler class.
 *
 * @copyright Copyright (C) 1999 - 2017 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or any later version)
 * @version //autogentag//
 * @package bccie
 */

class BaseHandler
{
    //place holder
    function exportAttribute( &$attribute, $separationChar )
    {
        return $this->escape( $attribute->content(), $separationChar );
    }

    //escape the string to use it in a CSV file type
    function escape( $stringtoescape, $separationChar )
    {
        $stringtoescape = preg_replace( "(\r\n|\n|\r)", " ", $stringtoescape );

        return utf8_decode( $stringtoescape );
        // return addcslashes($stringtoescape, "$separationChar\0..\37\177..\377");
    }
}

?>