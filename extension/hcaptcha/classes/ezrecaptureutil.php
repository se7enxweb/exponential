<?php

/**
 * Utility class
 *
 * @copyright Copyright (C) 2011 Bruce Morrison. All rights reserved.
 * @author Bruce Morrison <bruce.morrison@stuffandcontent.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 */
class ezRecaptureUtil {

    /**
     * Returns the PrivateKey associated with the current host.
     *
     * @access public
     * @return string
     */
    static function getReCapturePrivateKey()
    {
        $privatekey = '';
        $ini = eZINI::instance( 'recaptcha.ini' );

        // If PrivateKey is an array try and find a match for the current host
        $privateKeyIni = $ini->variable( 'Keys', 'PrivateKey' );
        if ( is_array( $privateKeyIni) )
        {
            $hostname = eZSys::hostname();
            if ( isset( $privateKeyIni[$hostname]) )
                $privatekey = $privateKeyIni[$hostname];
            else
                // try our luck with the first entry
                $privatekey = array_shift( $privateKeyIni );
        }
        else
            $privatekey = $privateKeyIni;

        return $privatekey;
    }

}

?>
