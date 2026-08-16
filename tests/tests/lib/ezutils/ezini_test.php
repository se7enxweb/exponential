<?php
/**
 * File containing the compatibility alias for the eZINI round-trip test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

// Load the canonical test class from eZINITest.php, then provide the legacy
// lowercase-name alias for PHPUnit runners / ad-hoc invocations that still
// look for this filename.
require_once __DIR__ . '/eZINITest.php';

if ( !class_exists( 'ezini_test', false ) )
{
    class ezini_test extends eZINITest
    {
    }
}
