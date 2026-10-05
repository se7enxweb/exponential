<?php
/**
 * File containing the eZPlainXMLOutput class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class eZPlainXMLOutput extends eZXMLOutputHandler
{
    function &outputText()
    {
        $retText = "<pre>" . htmlspecialchars( $this->xmlData(), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . "</pre>";
        return $retText;
    }
}

?>
