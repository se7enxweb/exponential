<?php
/**
 * Loads the classes of the data tables and the secret store from this checkout for the tests of
 * tests/tests/kernel/classes/datatable and tests/tests/kernel/classes/secrets, before the autoloader can take another
 * copy (a test run whose vendor/ belongs to another checkout loads that checkout's kernel). A class that is loaded
 * already is left as it is.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expDataTableTestLoader
{
    /** class => file, in loading order (eZPersistentObject before its subclasses) */
    const CLASSES = array(
        'eZPersistentObject' => 'kernel/classes/ezpersistentobject.php',
        'expPDODB' => 'lib/ezdb/classes/exppdodb.php',
        'expKernelTableSchema' => 'kernel/classes/datatable/expkerneltableschema.php',
        'expDataConnection' => 'kernel/classes/datatable/expdataconnection.php',
        'expDataConnections' => 'kernel/classes/datatable/expdataconnections.php',
        'expDataTableCatalogue' => 'kernel/classes/datatable/expdatatablecatalogue.php',
        'expDataTableDescriber' => 'kernel/classes/datatable/expdatatabledescriber.php',
        'expSecretKey' => 'kernel/classes/secrets/expsecretkey.php',
        'expSecret' => 'kernel/classes/secrets/expsecret.php',
        'expSecrets' => 'kernel/classes/secrets/expsecrets.php',
    );

    /** @return string the root of this checkout */
    public static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public static function load()
    {
        foreach ( self::CLASSES as $class => $file )
        {
            if ( !class_exists( $class, false ) && !interface_exists( $class, false ) )
                require_once self::root() . '/' . $file;
        }
    }
}

expDataTableTestLoader::load();
