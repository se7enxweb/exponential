<?php
/**
 * File containing a collection lazy initialisation hooks
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

class ezpRestDbConfig implements ezcBaseConfigurationInitializer
{
    public static function configureObject( $instance )
    {
        //Ignoring $instance
        $dsn = self::lazyDbHelper();
        return ezcDbFactory::create( $dsn );
    }

    protected static function lazyDbHelper()
    {
        $dbMapping = array( 'ezmysqli' => 'mysql',
                            'ezmysql' => 'mysql',
                            'mysql' => 'mysql',
                            'mysqli' => 'mysql',
                            'pgsql' => 'pgsql',
                            'postgresql' => 'pgsql',
                            'ezpostgresql' => 'pgsql',
                            'ezoracle' => 'oracle',
                            'oracle' => 'oracle' );

        $ini = eZINI::instance();
        list( $dbType, $dbHost, $dbPort, $dbUser, $dbPass, $dbName ) =
            $ini->variableMulti( 'DatabaseSettings',
                                 array( 'DatabaseImplementation', 'Server', 'Port',
                                        'User', 'Password', 'Database',
                                       )
                                );

        // SQLite: the file the site's own driver opens (eZSQLite3DB::filePath()),
        // as an absolute path, which ezcDbHandlerSqlite requires.
        if ( $dbType === 'sqlite3' || $dbType === 'sqlite' )
        {
            $file = eZSQLite3DB::filePath( $dbName );
            if ( $file === ':memory:' )
            {
                return array( 'phptype' => 'sqlite', 'port' => 'memory' );
            }
            if ( $file[0] !== '/' )
            {
                $file = getcwd() . '/' . $file;
            }
            return array( 'phptype' => 'sqlite', 'database' => $file );
        }

        if ( !isset( $dbMapping[$dbType] ) )
        {
            // @TODO: Add a proper exception type here.
            throw new Exception( "Unknown / unmapped DB type '$dbType'" );
        }

        $dbType = $dbMapping[$dbType];

        $dsnHost = $dbHost . ( $dbPort != '' ? ":$dbPort" : '' );
        // ezcDbFactory::parseDSN() rawurldecodes the user and the password, and splits them at the first colon
        $dsn = "{$dbType}://" . rawurlencode( (string)$dbUser ) . ":" . rawurlencode( (string)$dbPass ) . "@{$dsnHost}/{$dbName}";

        return $dsn;
    }

    /**
     * Registers the lazy initialisation callbacks of the REST database and
     * persistent session, unless they are registered already.
     *
     * Called every time the REST kernel is built, not only when this file is
     * loaded: a persistent application server worker loads the file once, but
     * puts every class's static properties, ezcBaseInit's callback map among
     * them, back to their initial values between two requests. Without the
     * callbacks ezcDbInstance::get() has no handler, so the second request in
     * a worker that had to look up an OAuth token died with a server error.
     */
    public static function registerCallbacks()
    {
        $callbacks = array( 'ezcInitDatabaseInstance' => 'ezpRestDbConfig',
                            'ezcInitPersistentSessionInstance' => 'ezpRestPoConfig' );
        foreach ( $callbacks as $identifier => $className )
        {
            try
            {
                ezcBaseInit::setCallback( $identifier, $className );
            }
            catch ( ezcBaseInitCallbackConfiguredException $e )
            {
                // Registered already, by this file or by the application.
            }
        }
    }
}

class ezpRestPoConfig implements ezcBaseConfigurationInitializer
{
    public static function configureObject( $instance )
    {
        return new ezcPersistentSession( ezcDbInstance::get(),
            new ezcPersistentMultiManager( array(
                new ezcPersistentCodeManager( 'kernel/private/rest/classes/po_maps/' ),
                new ezcPersistentCodeManager( 'kernel/private/oauth/classes/persistentobjects/' )
            ))
        );
    }
}
ezpRestDbConfig::registerCallbacks();

?>
