<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Stands in for an eZPackage when eZPackageComparison asks a datatype to serialize one of the
 * site's own attributes, so that the site's value comes out in exactly the form a package carries
 * it. A datatype that stores a file (ezimage, ezbinaryfile, ezmedia, ...) hands that file to
 * $package->appendSimpleFile(), which on a real package copies it into the package; here the file
 * is only noted, by key, so the comparison can tell its name, size and checksum. Nothing is
 * written anywhere. Any other package method a datatype may call answers false.
 */
class eZPackageComparisonFileCollector
{
    /** @var array file key => site file path, as the datatype passed it */
    public $Files = array();

    function appendSimpleFile( $key, $filepath )
    {
        $this->Files[(string)$key] = (string)$filepath;
        return true;
    }

    function simpleFilePath( $key )
    {
        return isset( $this->Files[(string)$key] ) ? $this->Files[(string)$key] : false;
    }

    function attribute( $name )
    {
        return false;
    }

    function hasAttribute( $name )
    {
        return false;
    }

    function __call( $name, $arguments )
    {
        return false;
    }
}

?>
