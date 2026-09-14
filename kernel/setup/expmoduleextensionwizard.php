<?php
/**
 * File containing the expModuleExtensionWizard class.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

/**
 * Builds a module extension over tables that already exist.
 *
 * A table that was not made by eZ - one an older system left behind, one another
 * application writes, one on a database of its own - has no way into the admin
 * interface and no way into a template. Writing that by hand means an
 * eZPersistentObject class whose definition matches the columns exactly, a
 * module with its views and policies, templates for each of them, and the ini
 * that registers all of it.
 *
 * This reads the table as the database describes it, maps each column to what
 * eZPersistentObject calls that type, and writes the lot: the classes, a module
 * with list, edit and remove, the templates, the fetch functions that make the
 * same data reachable from a template, and the settings that put it in the
 * admin menu.
 *
 * Nothing is written until it is asked for, and nothing is guessed that the
 * database can be asked about instead.
 */
class expModuleExtensionWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'module extension wizard';
    }
    /** Tables whose names cannot be turned into a class name are not offered. */
    const TABLE_PATTERN = '/^[A-Za-z][A-Za-z0-9_]{0,62}$/';

    /** More than this in one extension is a sign the wrong box was ticked. */
    const MAX_TABLES = 40;

    /**
     * The parts this wizard can put in.
     *
     * @return array key => array( label, description, default )
     */
    public static function parts()
    {
        return array(
            'classes' => array(
                'label' => 'Persistent object classes',
                'description' => 'One eZPersistentObject class per table, with its definition built from the columns the database reports.',
                'default' => true ),
            'module' => array(
                'label' => 'Admin module',
                'description' => 'A module with list, edit and remove views, and the policy functions that go with them.',
                'default' => true ),
            'templates' => array(
                'label' => 'Admin templates',
                'description' => 'The templates those views draw with, in the admin style.',
                'default' => true ),
            'fetch_functions' => array(
                'label' => 'Template fetch functions',
                'description' => 'function_definition.php, so the same rows can be reached with fetch() from any template.',
                'default' => true ),
            'menu' => array(
                'label' => 'Admin menu entry',
                'description' => 'Puts the module in the Setup menu, with a policy so access can be granted to it.',
                'default' => true ),
            'dba' => array(
                'label' => 'Schema description',
                'description' => 'share/db_schema.dba describing the tables, so the installer and the consistency check know about them.',
                'default' => false ),
            'ezinfo' => array(
                'label' => 'ezinfo.php',
                'description' => 'What the admin interface reads to show the extension name, version and licence.',
                'default' => true ),
            'extension_xml' => array(
                'label' => 'extension.xml',
                'description' => 'The packaged description of the extension.',
                'default' => true ),
            'composer' => array(
                'label' => 'composer.json',
                'description' => 'So the extension can be required by name rather than copied in.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What it covers, how to switch it on, and what each piece is.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    /**
     * Where the tables can be read from.
     *
     * @return array key => label
     */
    public static function sources()
    {
        return array( 'current'  => 'This installation\'s own database',
                      'external' => 'Another database - given below' );
    }

    /**
     * Every kind of database this wizard knows how to read, and what it would
     * take to read one.
     *
     * Three things differ between them and all three matter here:
     *
     *  - whether eZ has a handler, which decides whether the generated classes
     *    can be eZPersistentObject or have to carry their own connection;
     *  - whether this machine has the driver, which decides whether anything
     *    can be read at all;
     *  - whether it stores tables or collections, which decides how the shape
     *    of the data is worked out.
     *
     * Nothing here is asserted about a driver that is not installed: the page
     * says what is missing rather than failing when the button is pressed.
     *
     * @return array key => array( label, ez_handler, pdo_driver, php_extension, kind, notes )
     */
    public static function databaseTypes()
    {
        return array(
            'mysqli' => array(
                'label'   => 'MySQL / MariaDB',
                'ez'      => 'mysqli',
                'pdo'     => 'mysql',
                'ext'     => 'mysqli',
                'kind'    => 'relational',
                'port'    => 3306,
                'notes'   => '' ),

            'postgresql' => array(
                'label'   => 'PostgreSQL',
                'ez'      => 'postgresql',
                'pdo'     => 'pgsql',
                'ext'     => 'pgsql',
                'kind'    => 'relational',
                'port'    => 5432,
                'notes'   => '' ),

            'sqlite' => array(
                'label'   => 'SQLite',
                'ez'      => 'sqlite3',
                'pdo'     => 'sqlite',
                'ext'     => 'sqlite3',
                'kind'    => 'relational',
                'port'    => 0,
                'notes'   => 'The database name is the path to the file.' ),

            'mongodb' => array(
                'label'   => 'MongoDB',
                'ez'      => 'mongodb',
                'pdo'     => false,
                'ext'     => 'mongodb',
                'kind'    => 'document',
                'port'    => 27017,
                'notes'   => 'Collections have no fixed shape, so the fields are worked out by reading a sample of documents.' ),

            'oracle' => array(
                'label'   => 'Oracle',
                'ez'      => false,
                'pdo'     => 'oci',
                'ext'     => 'pdo_oci',
                'kind'    => 'relational',
                'port'    => 1521,
                'notes'   => 'eZ has no handler for Oracle, so the generated classes carry their own connection rather than going through eZPersistentObject.' ),

            'odbc' => array(
                'label'   => 'ODBC',
                'ez'      => false,
                'pdo'     => 'odbc',
                'ext'     => 'pdo_odbc',
                'kind'    => 'relational',
                'port'    => 0,
                'notes'   => 'The database name is the DSN. eZ has no handler for ODBC, so the generated classes carry their own connection.' ),
        );
    }

    /**
     * The same list, with what this machine can actually do about each one.
     *
     * @return array key => the entry above, plus available, via and why.
     */
    public static function databaseCapabilities()
    {
        $ini      = eZINI::instance( 'site.ini' );
        $aliases  = (array) $ini->variable( 'DatabaseSettings', 'ImplementationAlias' );
        $pdo      = class_exists( 'PDO' ) ? PDO::getAvailableDrivers() : array();

        $types = array();
        foreach ( self::databaseTypes() as $key => $type )
        {
            $viaEZ  = $type['ez'] !== false && isset( $aliases[$type['ez']] )
                      && class_exists( $aliases[$type['ez']] );
            $viaPDO = $type['pdo'] !== false && in_array( $type['pdo'], $pdo, true );

            $why = '';
            if ( !$viaEZ && !$viaPDO )
            {
                $why = $type['ez'] !== false && !isset( $aliases[$type['ez']] )
                       ? 'No handler is registered for it, and PDO has no ' . $type['pdo'] . ' driver on this machine.'
                       : 'This machine has no ' . $type['ext'] . ' driver.';
            }

            $types[$key] = array_merge( $type, array(
                'available' => $viaEZ || $viaPDO,
                'via'       => $viaEZ ? 'ez' : ( $viaPDO ? 'pdo' : false ),
                'handler'   => $viaEZ ? $aliases[$type['ez']] : '',
                'why'       => $why ) );
        }

        return $types;
    }

    /**
     * The types as the drop-down lists them.
     *
     * @return array key => label
     */
    public static function databaseTypeLabels()
    {
        $labels = array();
        foreach ( self::databaseCapabilities() as $key => $type )
        {
            $labels[$key] = $type['label']
                            . ( $type['available']
                                ? ( $type['via'] === 'ez' ? '' : ' (read only, through PDO)' )
                                : ' - not available here' );
        }

        return $labels;
    }

    /**
     * Everything the wizard was asked for, with the gaps filled in.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $name = self::safeName( isset( $input['name'] ) ? $input['name'] : '' );

        $settings = array(
            'name'      => $name,
            'module'    => self::safeName( isset( $input['module'] ) ? $input['module'] : '', true ),
            'prefix'    => self::safePrefix( isset( $input['prefix'] ) ? $input['prefix'] : '' ),
            'title'     => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'   => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'    => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'    => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'   => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'   => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'source'    => isset( $input['source'] ) && isset( self::sources()[$input['source']] )
                           ? $input['source'] : 'current',
            'db_type'   => isset( $input['db_type'] ) && isset( self::databaseTypes()[$input['db_type']] )
                           ? $input['db_type'] : 'mysqli',
            'db_sample' => isset( $input['db_sample'] ) && is_numeric( $input['db_sample'] )
                           ? max( 1, min( 500, (int) $input['db_sample'] ) ) : 50,
            'db_server' => self::text( isset( $input['db_server'] ) ? $input['db_server'] : '', 200 ),
            'db_port'   => isset( $input['db_port'] ) && is_numeric( $input['db_port'] )
                           ? (int) $input['db_port'] : 0,
            'db_name'   => self::text( isset( $input['db_name'] ) ? $input['db_name'] : '', 400 ),
            'db_file'   => self::safePath( isset( $input['db_file'] ) ? $input['db_file'] : '' ),
            'db_user'   => self::text( isset( $input['db_user'] ) ? $input['db_user'] : '', 200 ),
            'db_password' => is_scalar( isset( $input['db_password'] ) ? $input['db_password'] : '' )
                             ? (string) ( isset( $input['db_password'] ) ? $input['db_password'] : '' ) : '',
            'tables'    => self::safeTables( isset( $input['tables'] ) ? $input['tables'] : array() ),
        );

        if ( $settings['module'] === '' )
            $settings['module'] = $settings['name'];
        if ( $settings['prefix'] === '' )
            $settings['prefix'] = $settings['name'] !== '' ? self::safePrefix( $settings['name'] ) : '';
        if ( $settings['title'] === '' )
            $settings['title'] = $settings['name'] !== '' ? ucwords( str_replace( '_', ' ', $settings['name'] ) ) : '';
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' && $settings['title'] !== '' )
            $settings['summary'] = 'Administration for ' . count( $settings['tables'] ) . ' database table(s), through ' . $settings['title'] . '.';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * A prefix for generated class names: letters and digits, starting with a letter.
     *
     * @param string $value
     * @return string
     */
    public static function safePrefix( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = preg_replace( '/[^A-Za-z0-9]+/', '', $value );
        if ( $value === null || $value === '' )
            return '';

        return preg_match( '/^[A-Za-z]/', $value ) ? substr( $value, 0, 20 ) : '';
    }

    /**
     * Table names that are really table names.
     *
     * What comes back from the form is a list of strings somebody could have
     * typed, and every one of them ends up in a query. Only names the pattern
     * allows survive, and never more than MAX_TABLES of them.
     *
     * @param mixed $tables
     * @return array of string
     */
    public static function safeTables( $tables )
    {
        if ( is_string( $tables ) )
            $tables = array( $tables );
        if ( !is_array( $tables ) )
            return array();

        $clean = array();
        foreach ( $tables as $table )
        {
            if ( !is_string( $table ) )
                continue;

            $table = trim( $table );
            if ( !preg_match( self::TABLE_PATTERN, $table ) || isset( $clean[$table] ) )
                continue;

            $clean[$table] = $table;
            if ( count( $clean ) >= self::MAX_TABLES )
                break;
        }

        return array_values( $clean );
    }

    /**
     * What is wrong with these settings.
     *
     * @param array $settings
     * @return array of string
     */
    public static function problems( array $settings )
    {
        $problems = array();

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['prefix'] === '' )
            $problems[] = 'The class name prefix has to start with a letter.';

        if ( !count( $settings['tables'] ) )
            $problems[] = 'Choose at least one table.';

        if ( $settings['source'] === 'external' )
        {
            $types = self::databaseCapabilities();

            if ( !isset( $types[$settings['db_type']] ) )
            {
                $problems[] = 'That kind of database is not one this page knows.';
            }
            else if ( !$types[$settings['db_type']]['available'] )
            {
                $problems[] = $types[$settings['db_type']]['label'] . ' cannot be read here. '
                              . $types[$settings['db_type']]['why'];
            }

            // Each kind is asked for what it actually needs. A database that is
            // a file has no server, and one reached through a data source name
            // has neither a server nor a database name of its own.
            switch ( $settings['db_type'] )
            {
                case 'sqlite':
                    if ( $settings['db_file'] === '' )
                        $problems[] = 'Choose the database file, or give a path to one inside the installation.';
                    break;

                case 'odbc':
                    if ( $settings['db_name'] === '' )
                        $problems[] = 'An ODBC connection needs a data source name.';
                    break;

                default:
                    if ( $settings['db_name'] === '' )
                        $problems[] = 'An external database needs a database name.';
                    if ( $settings['db_server'] === '' )
                        $problems[] = 'An external database needs a server.';
            }
        }

        return $problems;
    }

    /**
     * What the connection form asks for, for one kind of database.
     *
     * Every kind wants something different, and asking for a server and a port
     * when the answer is the path to a file is how a form teaches somebody that
     * it was not written for what they are doing.
     *
     * @param string $type
     * @return array field => array( label, hint, show )
     */
    public static function fieldsFor( $type )
    {
        $types = self::databaseTypes();
        $kind  = isset( $types[$type] ) ? $type : 'mysqli';

        $server = array( 'label' => 'Server', 'hint' => '', 'show' => true );
        $port   = array( 'label' => 'Port', 'hint' => '', 'show' => true );
        $name   = array( 'label' => 'Database name', 'hint' => '', 'show' => true );
        $user   = array( 'label' => 'User', 'hint' => '', 'show' => true );
        $pass   = array( 'label' => 'Password', 'hint' => '', 'show' => true );
        $file   = array( 'label' => 'File', 'hint' => '', 'show' => false );
        $sample = array( 'label' => 'Documents to sample', 'hint' => '', 'show' => false );

        switch ( $kind )
        {
            case 'sqlite':
                // One file, and nothing else. A server and a user mean nothing.
                $server['show'] = $port['show'] = $user['show'] = $pass['show'] = false;
                $name['show']   = false;
                $file['show']   = true;
                $file['hint']   = 'The path to the database file, relative to the installation or absolute.';
                break;

            case 'mongodb':
                $name['label']  = 'Database';
                $user['hint']   = 'Leave the user and password empty if the server does not ask for them.';
                $sample['show'] = true;
                $sample['hint'] = 'A collection has no fixed shape, so this many documents are read to work out its fields.';
                break;

            case 'oracle':
                $name['label']  = 'Service name or SID';
                $name['hint']   = 'What comes after the slash in a connect string, such as ORCLPDB1.';
                break;

            case 'odbc':
                $server['show'] = false;
                $port['show']   = false;
                $name['label']  = 'DSN';
                $name['hint']   = 'The data source name as it is configured on this machine, or a full connection string.';
                break;
        }

        return array( 'db_server' => $server, 'db_port' => $port, 'db_name' => $name,
                      'db_file' => $file, 'db_user' => $user, 'db_password' => $pass,
                      'db_sample' => $sample );
    }

    /**
     * SQLite files this installation can see, so one can be picked rather than
     * typed from memory.
     *
     * Only where a database would sensibly be: the installation root and var/.
     * Walking the whole tree of a large site to find files nobody put there is
     * a page that takes minutes to draw.
     *
     * @return array of path, relative to the installation.
     */
    public static function sqliteCandidates()
    {
        $root  = self::installationRoot();
        $found = array();

        foreach ( array( '', 'var', 'var/storage', 'var/site/storage', 'share', 'settings' ) as $directory )
        {
            $path = $root . ( $directory === '' ? '' : '/' . $directory );
            if ( !is_dir( $path ) )
                continue;

            $entries = @scandir( $path );
            if ( !is_array( $entries ) )
                continue;

            foreach ( $entries as $entry )
            {
                if ( $entry === '.' || $entry === '..' )
                    continue;

                $full = $path . '/' . $entry;
                if ( !is_file( $full ) || !is_readable( $full ) )
                    continue;

                if ( !preg_match( '/\.(db|db3|sqlite|sqlite3)$/i', $entry ) )
                    continue;

                $relative = $directory === '' ? $entry : $directory . '/' . $entry;
                $found[$relative] = $relative . ' (' . self::readableSize( filesize( $full ) ) . ')';
            }
        }

        ksort( $found );

        return $found;
    }

    /**
     * A size a person can read at a glance.
     *
     * @param int $bytes
     * @return string
     */
    protected static function readableSize( $bytes )
    {
        $bytes = (int) $bytes;

        if ( $bytes >= 1048576 )
            return round( $bytes / 1048576, 1 ) . ' MB';
        if ( $bytes >= 1024 )
            return round( $bytes / 1024 ) . ' KB';

        return $bytes . ' bytes';
    }

    // ── Reading the database ─────────────────────────────────────────────────

    /**
     * A path that stays inside the installation, for a database that is a file.
     *
     * @param mixed $value
     * @return string
     */
    public static function safePath( $value )
    {
        if ( !is_string( $value ) )
            return '';

        $value = trim( $value );
        if ( $value === '' || strpos( $value, "\0" ) !== false )
            return '';

        // Nothing that climbs out of the installation, and nothing absolute
        // pointing anywhere else: this is read by the web server, and a file
        // picker that will open any path on the disk is a file reader.
        $value = str_replace( '\\', '/', $value );
        if ( strpos( $value, '..' ) !== false )
            return '';

        $root = self::installationRoot();
        $full = strpos( $value, '/' ) === 0 ? $value : $root . '/' . $value;
        $real = realpath( $full );

        if ( $real === false || strpos( $real, $root ) !== 0 )
            return '';

        return substr( $real, strlen( $root ) + 1 );
    }

    /**
     * The database the tables are read from.
     *
     * There are three ways in, and which one applies is decided by what the
     * type needs and what this machine has:
     *
     *  - this installation's own connection, when nothing else was asked for;
     *  - an eZ handler, for the kinds eZ has one for;
     *  - PDO, for the kinds it does not - which is read only, and enough to
     *    describe a table even though eZ could never query it.
     *
     * @param array $settings
     * @return array ok, message, db or pdo, and via.
     */
    public static function connection( array $settings )
    {
        if ( $settings['source'] !== 'external' )
        {
            $db = eZDB::instance();

            return $db instanceof eZDBInterface && $db->isConnected()
                   ? array( 'ok' => true, 'via' => 'ez', 'db' => $db,
                            'type' => self::currentType(),
                            'message' => 'Using this installation\'s own database.' )
                   : array( 'ok' => false, 'via' => false,
                            'message' => 'This installation\'s own database is not connected.' );
        }

        $types = self::databaseCapabilities();
        $type  = $settings['db_type'];

        if ( !isset( $types[$type] ) )
            return array( 'ok' => false, 'via' => false, 'message' => 'That kind of database is not one this page knows.' );

        if ( !$types[$type]['available'] )
            return array( 'ok' => false, 'via' => false,
                          'message' => $types[$type]['label'] . ' cannot be read here. ' . $types[$type]['why'] );

        return $types[$type]['via'] === 'ez'
               ? self::connectThroughEZ( $settings, $types[$type] )
               : self::connectThroughPDO( $settings, $types[$type] );
    }

    /**
     * What kind of database this installation's own connection is.
     *
     * @return string
     */
    public static function currentType()
    {
        $implementation = strtolower( (string) eZINI::instance( 'site.ini' )->variable( 'DatabaseSettings', 'DatabaseImplementation' ) );

        foreach ( self::databaseTypes() as $key => $type )
            if ( $type['ez'] === $implementation || $key === $implementation )
                return $key;

        return 'mysqli';
    }

    /**
     * A connection through an eZ handler.
     *
     * @param array $settings
     * @param array $type
     * @return array
     */
    protected static function connectThroughEZ( array $settings, array $type )
    {
        $parameters = array( 'server'   => $settings['db_server'],
                             'user'     => $settings['db_user'],
                             'password' => $settings['db_password'],
                             'database' => $settings['db_name'] );

        if ( $settings['db_type'] === 'sqlite' )
        {
            if ( $settings['db_file'] === '' )
                return array( 'ok' => false, 'via' => false,
                              'message' => 'Choose a database file.' );

            $parameters['database'] = self::installationRoot() . '/' . $settings['db_file'];
            $parameters['server']   = '';
        }

        if ( $settings['db_port'] > 0 )
            $parameters['port'] = $settings['db_port'];

        $db = false;
        try
        {
            $db = eZDB::instance( $type['ez'], $parameters, true );
        }
        catch ( Exception $e )
        {
            return array( 'ok' => false, 'via' => false, 'message' => 'Could not connect: ' . $e->getMessage() );
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'via' => false, 'message' => 'Could not connect: ' . $e->getMessage() );
        }

        if ( !$db instanceof eZDBInterface || !$db->isConnected() )
            return array( 'ok' => false, 'via' => false,
                          'message' => 'Could not connect to ' . self::describeTarget( $settings )
                                       . '. Check the details, and that the user may read it.' );

        return array( 'ok' => true, 'via' => 'ez', 'db' => $db, 'type' => $settings['db_type'],
                      'message' => 'Connected to ' . self::describeTarget( $settings ) . '.' );
    }

    /**
     * A connection through PDO, for a kind eZ has no handler for.
     *
     * Read only, and said so: nothing generated from it goes through
     * eZPersistentObject, because eZ could not talk to it if it tried.
     *
     * @param array $settings
     * @param array $type
     * @return array
     */
    protected static function connectThroughPDO( array $settings, array $type )
    {
        $dsn = self::dsn( $settings, $type );
        if ( $dsn === false )
            return array( 'ok' => false, 'via' => false, 'message' => 'Not enough to build a connection string with.' );

        try
        {
            $pdo = new PDO( $dsn, $settings['db_user'], $settings['db_password'],
                            array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                   PDO::ATTR_TIMEOUT => 10 ) );
        }
        catch ( Exception $e )
        {
            return array( 'ok' => false, 'via' => false, 'message' => 'Could not connect: ' . $e->getMessage() );
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'via' => false, 'message' => 'Could not connect: ' . $e->getMessage() );
        }

        return array( 'ok' => true, 'via' => 'pdo', 'pdo' => $pdo, 'type' => $settings['db_type'],
                      'message' => 'Connected to ' . self::describeTarget( $settings )
                                   . ' through PDO. eZ has no handler for it, so the generated classes will carry their own connection.' );
    }

    /**
     * The connection string for a kind PDO handles.
     *
     * @param array $settings
     * @param array $type
     * @return string|false
     */
    public static function dsn( array $settings, array $type )
    {
        $server = $settings['db_server'] !== '' ? $settings['db_server'] : 'localhost';
        $port   = $settings['db_port'] > 0 ? $settings['db_port'] : $type['port'];

        switch ( $settings['db_type'] )
        {
            case 'oracle':
                if ( $settings['db_name'] === '' )
                    return false;

                return 'oci:dbname=//' . $server . ':' . $port . '/' . $settings['db_name'] . ';charset=AL32UTF8';

            case 'odbc':
                if ( $settings['db_name'] === '' )
                    return false;

                // Either a configured data source, or a whole connection string
                // when somebody has pasted one in.
                return strpos( $settings['db_name'], '=' ) !== false
                       ? 'odbc:' . $settings['db_name']
                       : 'odbc:' . $settings['db_name'];

            case 'mysqli':
                return 'mysql:host=' . $server . ';port=' . $port . ';dbname=' . $settings['db_name'] . ';charset=utf8mb4';

            case 'postgresql':
                return 'pgsql:host=' . $server . ';port=' . $port . ';dbname=' . $settings['db_name'];

            case 'sqlite':
                return $settings['db_file'] === ''
                       ? false
                       : 'sqlite:' . self::installationRoot() . '/' . $settings['db_file'];
        }

        return false;
    }

    /**
     * What the connection is being made to, in words.
     *
     * @param array $settings
     * @return string
     */
    public static function describeTarget( array $settings )
    {
        switch ( $settings['db_type'] )
        {
            case 'sqlite':
                return $settings['db_file'] !== '' ? $settings['db_file'] : 'a file';

            case 'odbc':
                return 'the data source ' . ( $settings['db_name'] !== '' ? $settings['db_name'] : '(unnamed)' );
        }

        $where = $settings['db_server'] !== '' ? ' on ' . $settings['db_server'] : '';

        return ( $settings['db_name'] !== '' ? $settings['db_name'] : 'the database' ) . $where;
    }

    /**
     * Everything the connection can see, whatever kind of thing it holds.
     *
     * The shape that comes back is the one eZDbSchema uses, whichever way it
     * was read, so nothing downstream has to know which kind of database it
     * came from.
     *
     * @param array $connection from connection().
     * @return array name => its schema.
     */
    public static function schema( array $connection )
    {
        if ( !$connection['ok'] )
            return array();

        if ( isset( $connection['type'] ) && $connection['type'] === 'mongodb' )
            return self::mongoSchema( $connection );

        if ( $connection['via'] === 'pdo' )
            return self::pdoSchema( $connection );

        return self::ezSchema( $connection['db'] );
    }

    /**
     * A relational schema, through eZDbSchema.
     *
     * @param eZDBInterface $db
     * @return array
     */
    protected static function ezSchema( $db )
    {
        $dbSchema = eZDbSchema::instance( $db );
        if ( !is_object( $dbSchema ) )
            return array();

        $schema = $dbSchema->schema( array( 'format' => 'generic' ) );

        return is_array( $schema ) ? self::onlyTables( $schema ) : array();
    }

    /**
     * A relational schema, through PDO, for a kind eZDbSchema cannot read.
     *
     * PDO cannot describe a table without selecting from it, so one row is
     * asked for and the column metadata of the result is read. Nothing is
     * fetched: the limit is zero wherever the dialect allows it.
     *
     * @param array $connection
     * @return array
     */
    protected static function pdoSchema( array $connection )
    {
        $pdo  = $connection['pdo'];
        $type = $connection['type'];

        $names = array();
        try
        {
            switch ( $type )
            {
                case 'oracle':
                    $statement = $pdo->query( 'SELECT table_name FROM user_tables ORDER BY table_name' );
                    break;

                case 'odbc':
                    // Not every driver answers this; the catch below covers the
                    // ones that do not, and the page says so.
                    $statement = $pdo->query( "SELECT table_name FROM information_schema.tables ORDER BY table_name" );
                    break;

                default:
                    return array();
            }

            foreach ( $statement->fetchAll( PDO::FETCH_COLUMN ) as $name )
                if ( is_string( $name ) && preg_match( self::TABLE_PATTERN, $name ) )
                    $names[] = $name;
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'Could not list tables: ' . $e->getMessage(), __METHOD__ );
            return array();
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not list tables: ' . $e->getMessage(), __METHOD__ );
            return array();
        }

        $schema = array();
        foreach ( $names as $name )
        {
            $fields = self::pdoColumns( $pdo, $type, $name );
            if ( !count( $fields ) )
                continue;

            $schema[$name] = array( 'name' => $name, 'fields' => $fields, 'indexes' => array() );
        }

        ksort( $schema );

        return $schema;
    }

    /**
     * One table's columns, as PDO describes them.
     *
     * @param PDO $pdo
     * @param string $type
     * @param string $table
     * @return array
     */
    protected static function pdoColumns( $pdo, $type, $table )
    {
        // The name came out of the database's own table list and has been
        // through the pattern, so it is a name and not a fragment of a query.
        $sql = $type === 'oracle'
               ? 'SELECT * FROM "' . $table . '" WHERE ROWNUM < 1'
               : 'SELECT * FROM ' . $table . ' WHERE 1 = 0';

        $fields = array();
        try
        {
            $statement = $pdo->query( $sql );
            $count     = $statement->columnCount();

            for ( $i = 0; $i < $count; $i++ )
            {
                $meta = $statement->getColumnMeta( $i );
                if ( !is_array( $meta ) || !isset( $meta['name'] ) )
                    continue;

                $fields[$meta['name']] = array(
                    'type'     => self::dbTypeOfPdo( isset( $meta['native_type'] ) ? $meta['native_type'] : '' ),
                    'length'   => isset( $meta['len'] ) && $meta['len'] > 0 ? (int) $meta['len'] : 0,
                    'not_null' => false,
                    'default'  => false );
            }
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( 'Could not describe ' . $table . ': ' . $e->getMessage(), __METHOD__ );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Could not describe ' . $table . ': ' . $e->getMessage(), __METHOD__ );
        }

        return $fields;
    }

    /**
     * What a PDO native type is in the terms the rest of this uses.
     *
     * @param string $native
     * @return string
     */
    protected static function dbTypeOfPdo( $native )
    {
        $native = strtolower( (string) $native );

        foreach ( array( 'int' => 'int', 'long' => 'int', 'short' => 'int',
                         'number' => 'float', 'float' => 'float', 'double' => 'float',
                         'decimal' => 'float', 'numeric' => 'float' ) as $needle => $type )
        {
            if ( strpos( $native, $needle ) !== false )
                return $type;
        }

        return 'varchar';
    }

    /**
     * A document store's collections, with the fields worked out by reading
     * some of what is in them.
     *
     * A collection has no declared shape, so there is nothing to read but the
     * documents. A sample is taken rather than the lot, and a field that only
     * some documents carry is still reported - with the caveat that this is a
     * sample and not a promise, which the page says out loud.
     *
     * @param array $connection
     * @return array
     */
    protected static function mongoSchema( array $connection )
    {
        $db = $connection['db'];

        if ( !method_exists( $db, 'listCollectionNames' ) )
            return array();

        $names = $db->listCollectionNames();
        if ( !is_array( $names ) )
            return array();

        $sample = isset( $connection['sample'] ) ? (int) $connection['sample'] : 50;
        $schema = array();

        foreach ( $names as $name )
        {
            if ( !is_string( $name ) || !preg_match( self::TABLE_PATTERN, $name ) )
                continue;

            $fields = self::mongoFields( $db, $name, $sample );
            if ( !count( $fields ) )
                continue;

            // _id is the key of every document, and is what a single one is
            // addressed by, so it is the primary key whether or not it was in
            // the sample under that name.
            $indexes = isset( $fields['_id'] )
                       ? array( 'PRIMARY' => array( 'type' => 'primary', 'fields' => array( '_id' ) ) )
                       : array();

            $schema[$name] = array( 'name' => $name, 'fields' => $fields, 'indexes' => $indexes );
        }

        ksort( $schema );

        return $schema;
    }

    /**
     * The fields a sample of documents carries, and what type each looks like.
     *
     * @param eZDBInterface $db
     * @param string $collection
     * @param int $sample
     * @return array
     */
    protected static function mongoFields( $db, $collection, $sample )
    {
        $rows = $db->arrayQuery( 'SELECT * FROM ' . $collection,
                                 array( 'limit' => max( 1, min( 500, (int) $sample ) ) ) );

        if ( !is_array( $rows ) )
            return array();

        $fields = array();
        foreach ( $rows as $row )
        {
            if ( !is_array( $row ) )
                continue;

            foreach ( $row as $field => $value )
            {
                if ( !is_string( $field ) || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $field ) )
                    continue;

                $type = is_int( $value ) ? 'int'
                        : ( is_float( $value ) ? 'float'
                        : ( is_bool( $value ) ? 'int' : 'varchar' ) );

                // A field that is a number in one document and text in another
                // is text, because that is the only thing both fit in.
                if ( isset( $fields[$field] ) && $fields[$field]['type'] !== $type )
                    $type = 'varchar';

                $fields[$field] = array( 'type' => $type, 'length' => 255,
                                         'not_null' => false, 'default' => false );
            }
        }

        // _id is always there whether or not the projection returned it.
        if ( count( $fields ) && !isset( $fields['_id'] ) )
            $fields = array_merge( array( '_id' => array( 'type' => 'varchar', 'length' => 64,
                                                          'not_null' => true, 'default' => false ) ),
                                   $fields );

        return $fields;
    }

    /**
     * Entries that are really tables, with names that can become class names.
     *
     * @param array $schema
     * @return array
     */
    protected static function onlyTables( array $schema )
    {
        $tables = array();
        foreach ( $schema as $name => $table )
        {
            if ( !is_string( $name ) || !preg_match( self::TABLE_PATTERN, $name ) )
                continue;
            if ( !is_array( $table ) || !isset( $table['fields'] ) || !is_array( $table['fields'] ) )
                continue;

            $tables[$name] = $table;
        }

        ksort( $tables );

        return $tables;
    }

    /**
     * One table's columns, in the terms eZPersistentObject uses.
     *
     * @param array $table one entry from schema().
     * @return array of array( name, property, datatype, db_type, length, required, default, primary, increment )
     */
    public static function columns( array $table )
    {
        $primary = array();
        if ( isset( $table['indexes']['PRIMARY']['fields'] ) && is_array( $table['indexes']['PRIMARY']['fields'] ) )
            $primary = $table['indexes']['PRIMARY']['fields'];

        $columns = array();
        foreach ( $table['fields'] as $name => $field )
        {
            if ( !is_string( $name ) || !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) )
                continue;

            $type      = isset( $field['type'] ) ? $field['type'] : 'varchar';
            $increment = $type === 'auto_increment';

            $columns[] = array(
                'name'      => $name,
                'property'  => self::propertyName( $name ),
                'datatype'  => self::datatypeOf( $type ),
                'db_type'   => $type,
                'length'    => isset( $field['length'] ) ? (int) $field['length'] : 0,
                'required'  => $increment || ( isset( $field['not_null'] ) && $field['not_null'] ),
                'default'   => isset( $field['default'] ) ? $field['default'] : false,
                'primary'   => $increment || in_array( $name, $primary, true ),
                'increment' => $increment );
        }

        return $columns;
    }

    /**
     * What eZPersistentObject calls a column of this type.
     *
     * @param string $dbType
     * @return string
     */
    public static function datatypeOf( $dbType )
    {
        switch ( strtolower( (string) $dbType ) )
        {
            case 'auto_increment':
            case 'int':
            case 'integer':
            case 'int4':
            case 'int8':
            case 'bigint':
            case 'smallint':
            case 'tinyint':
            case 'mediumint':
            case 'serial':
            case 'bigserial':
            case 'bool':
            case 'boolean':
            case 'timestamp':
                return 'integer';

            case 'float':
            case 'double':
            case 'real':
            case 'decimal':
            case 'numeric':
            case 'number':
            case 'money':
                return 'float';
        }

        return 'string';
    }

    /**
     * The property a column is read and written through.
     *
     * eZPersistentObject puts each field on the object under the name the
     * definition gives it, so contentobject_id becomes ContentObjectID rather
     * than something a person would have to look up.
     *
     * @param string $column
     * @return string
     */
    public static function propertyName( $column )
    {
        // Words that are written as one in a column name but read as two, and
        // short words eZ writes in capitals. Only a convenience: the column is
        // still reached by its own name through attribute(), and a word that is
        // not in here is simply capitalised at the start.
        $compound = array( 'contentobject' => 'ContentObject',
                           'contentclass'  => 'ContentClass',
                           'classattribute' => 'ClassAttribute',
                           'objectattribute' => 'ObjectAttribute',
                           'nodeassignment' => 'NodeAssignment',
                           'siteaccess'    => 'SiteAccess',
                           'datatype'      => 'DataType',
                           'workflow'      => 'Workflow',
                           'rssexport'     => 'RSSExport',
                           'pdfexport'     => 'PDFExport',
                           'parentnode'    => 'ParentNode',
                           'mainnode'      => 'MainNode',
                           'subtree'       => 'Subtree' );

        $capitals = array( 'id', 'url', 'uri', 'xml', 'html', 'rss', 'api', 'db',
                           'ip', 'md5', 'pdf', 'css', 'js', 'utc' );

        $name = '';
        foreach ( explode( '_', strtolower( (string) $column ) ) as $part )
        {
            if ( $part === '' )
                continue;

            if ( isset( $compound[$part] ) )
                $name .= $compound[$part];
            else if ( in_array( $part, $capitals, true ) )
                $name .= strtoupper( $part );
            else
                $name .= ucfirst( $part );
        }

        return $name === '' ? 'Field' : $name;
    }

    /**
     * The class a table is given.
     *
     * @param array $settings
     * @param string $table
     * @return string
     */
    public static function className( array $settings, $table )
    {
        return $settings['prefix'] . self::propertyName( $table );
    }

    /**
     * The keys eZPersistentObject identifies a row by.
     *
     * Without a primary key there is nothing to fetch a single row with, so the
     * first column is used and the generated class says so in a comment rather
     * than pretending otherwise.
     *
     * @param array $columns from columns().
     * @return array of column name
     */
    public static function keys( array $columns )
    {
        $keys = array();
        foreach ( $columns as $column )
            if ( $column['primary'] )
                $keys[] = $column['name'];

        if ( !count( $keys ) && count( $columns ) )
            $keys[] = $columns[0]['name'];

        return $keys;
    }

    /**
     * The column rows are numbered by, if any.
     *
     * @param array $columns
     * @return string|false
     */
    public static function incrementKey( array $columns )
    {
        foreach ( $columns as $column )
            if ( $column['increment'] )
                return $column['name'];

        return false;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of, as path => contents.
     *
     * The tables are read once, here, and everything below is written from what
     * the database said rather than from what was typed.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' || !count( $settings['tables'] ) )
            return array();

        $connection = self::connection( $settings );
        if ( !$connection['ok'] )
            return array();

        $connection['sample'] = $settings['db_sample'];
        $schema = self::schema( $connection );
        $tables = array();

        foreach ( $settings['tables'] as $table )
        {
            if ( !isset( $schema[$table] ) )
                continue;

            $columns = self::columns( $schema[$table] );
            if ( !count( $columns ) )
                continue;

            $tables[$table] = array(
                'name'      => $table,
                'class'     => self::className( $settings, $table ),
                'columns'   => $columns,
                'keys'      => self::keys( $columns ),
                'increment' => self::incrementKey( $columns ),
                'schema'    => $schema[$table] );
        }

        if ( !count( $tables ) )
            return array();

        $parts  = $settings['parts'];
        $module = $settings['module'];
        $files  = array();

        $external = $settings['source'] === 'external';

        if ( $parts['classes'] )
        {
            foreach ( $tables as $table )
            {
                // eZPersistentObject always talks to this installation's own
                // database. For a table that lives on another one that is the
                // wrong database, so it gets a class that holds its own
                // connection and offers the same methods.
                $files['classes/' . strtolower( $table['class'] ) . '.php'] = $external
                    ? self::externalClass( $settings, $table )
                    : self::persistentClass( $settings, $table );
            }

            $files['classes/' . strtolower( $settings['prefix'] ) . 'registry.php'] = self::registryClass( $settings, $tables );

            if ( $external )
            {
                $files['classes/' . strtolower( $settings['prefix'] ) . 'connection.php'] = self::connectionClass( $settings );
                $files['settings/' . $settings['name'] . '.ini.append.php'] = self::connectionIni( $settings );
            }
        }

        if ( $parts['module'] )
        {
            $files['modules/' . $module . '/module.php'] = self::moduleDefinition( $settings, $tables );
            $files['modules/' . $module . '/list.php']   = self::listView( $settings );
            $files['modules/' . $module . '/edit.php']   = self::editView( $settings );
            $files['modules/' . $module . '/remove.php'] = self::removeView( $settings );
            $files['settings/module.ini.append.php']     = self::moduleIni( $settings );
        }

        if ( $parts['templates'] )
        {
            $files['design/standard/templates/' . $module . '/list.tpl']   = self::listTemplate( $settings );
            $files['design/standard/templates/' . $module . '/edit.tpl']   = self::editTemplate( $settings );
            $files['design/standard/templates/' . $module . '/remove.tpl'] = self::removeTemplate( $settings );
            $files['settings/design.ini.append.php']                       = self::designIni( $settings );
        }

        if ( $parts['fetch_functions'] )
        {
            $files['modules/' . $module . '/function_definition.php'] = self::functionDefinition( $settings );
            $files['modules/' . $module . '/' . $module . 'functioncollection.php'] = self::functionCollection( $settings );
        }

        if ( $parts['menu'] )
            $files['settings/menu.ini.append.php'] = self::menuIni( $settings );

        if ( $parts['dba'] )
            $files['share/db_schema.dba'] = self::dba( $settings, $tables );

        if ( $parts['ezinfo'] )
            $files['ezinfo.php'] = self::ezinfo( $settings );

        if ( $parts['extension_xml'] )
            $files['extension.xml'] = self::extensionXml( $settings );

        if ( $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );

        if ( $parts['gitignore'] )
            $files['.gitignore'] = self::gitignore( $settings );

        if ( $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );

        ksort( $files );

        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, $tables, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
    }

    /**
     * The lines that switch the extension on.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    /**
     * One eZPersistentObject class, over one table.
     *
     * @param array $settings
     * @param array $table
     * @return string
     */
    protected static function persistentClass( array $settings, array $table )
    {
        $class = $table['class'];

        $php  = "<?php\n/**\n * " . $class . " - the rows of " . $table['name'] . ".\n *\n";
        $php .= " * Generated from the table as the database described it. The definition below\n";
        $php .= " * has to keep matching the table: change one and change the other.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\nclass " . $class . " extends eZPersistentObject\n{\n";

        foreach ( $table['columns'] as $column )
            $php .= "    public $" . $column['property'] . ";\n";

        $php .= "\n    static function definition()\n    {\n        return array( 'fields' => array(\n";

        $fields = array();
        foreach ( $table['columns'] as $column )
        {
            $field  = "            '" . $column['name'] . "' => array(\n";
            $field .= "                'name' => '" . $column['property'] . "',\n";
            $field .= "                'datatype' => '" . $column['datatype'] . "',\n";
            $field .= "                'default' => " . self::phpDefault( $column ) . ",\n";
            $field .= "                'required' => " . ( $column['required'] ? 'true' : 'false' ) . " )";
            $fields[] = $field;
        }

        $php .= implode( ",\n", $fields ) . " ),\n";
        $php .= "                      'keys' => array( '" . implode( "', '", $table['keys'] ) . "' ),\n";

        if ( $table['increment'] !== false )
            $php .= "                      'increment_key' => '" . $table['increment'] . "',\n";
        else
            $php .= "                      // This table has no column that numbers itself, so a new\n"
                  . "                      // row has to be given its key before it is stored.\n";

        $php .= "                      'class_name' => '" . $class . "',\n";
        $php .= "                      'sort' => array( '" . $table['keys'][0] . "' => 'asc' ),\n";
        $php .= "                      'name' => '" . $table['name'] . "' );\n    }\n\n";

        // create()
        $php .= "    /**\n     * A new row, not yet stored.\n     *\n     * @param array \$row values to start from.\n     * @return " . $class . "\n     */\n";
        $php .= "    static function create( array \$row = array() )\n    {\n";
        $php .= "        \$defaults = array(\n";
        $defaults = array();
        foreach ( $table['columns'] as $column )
            $defaults[] = "            '" . $column['name'] . "' => " . ( $column['increment'] ? 'null' : self::phpDefault( $column ) );
        $php .= implode( ",\n", $defaults ) . " );\n\n";
        $php .= "        return new " . $class . "( array_merge( \$defaults, \$row ) );\n    }\n\n";

        // fetch()
        $keyArguments = array();
        $keyConditions = array();
        foreach ( $table['keys'] as $key )
        {
            $keyArguments[] = '$' . $key;
            $keyConditions[] = "'" . $key . "' => \$" . $key;
        }

        $php .= "    /**\n     * One row, by its key.\n     *\n     * @return " . $class . "|null\n     */\n";
        $php .= "    static function fetch( " . implode( ', ', $keyArguments ) . ", \$asObject = true )\n    {\n";
        $php .= "        return eZPersistentObject::fetchObject( self::definition(), null,\n";
        $php .= "                                                array( " . implode( ', ', $keyConditions ) . " ),\n";
        $php .= "                                                \$asObject );\n    }\n\n";

        // fetchList()
        $php .= "    /**\n     * A page of rows.\n     *\n"
              . "     * Nothing calls this without a limit by accident: a table with a million\n"
              . "     * rows in it would answer, and the answer would be the last thing the\n"
              . "     * request did.\n     *\n     * @return array of " . $class . "\n     */\n";
        $php .= "    static function fetchList( \$conditions = null, \$offset = false, \$limit = false, \$sorts = null, \$asObject = true )\n    {\n";
        $php .= "        \$limits = null;\n";
        $php .= "        if ( \$limit !== false && \$limit !== null )\n";
        $php .= "            \$limits = array( 'offset' => (int) \$offset, 'length' => (int) \$limit );\n\n";
        $php .= "        return eZPersistentObject::fetchObjectList( self::definition(), null, \$conditions,\n";
        $php .= "                                                   \$sorts, \$limits, \$asObject );\n    }\n\n";

        // count
        $php .= "    /**\n     * How many rows there are, without fetching any.\n     *\n     * @return int\n     */\n";
        $php .= "    static function fetchListCount( \$conditions = null )\n    {\n";
        $php .= "        return (int) eZPersistentObject::count( self::definition(), \$conditions );\n    }\n\n";

        // removeThis
        $php .= "    /**\n     * Removes this row.\n     */\n";
        $php .= "    function removeThis()\n    {\n";
        $php .= "        \$conditions = array();\n";
        foreach ( $table['keys'] as $key )
            $php .= "        \$conditions['" . $key . "'] = \$this->attribute( '" . $key . "' );\n";
        $php .= "\n        eZPersistentObject::removeObject( self::definition(), \$conditions );\n    }\n\n";

        // the columns, for a template or a form to walk
        $php .= "    /**\n     * The columns, in the order the table declares them.\n     *\n     * @return array of field name\n     */\n";
        $php .= "    static function columns()\n    {\n";
        $php .= "        \$definition = self::definition();\n\n";
        $php .= "        return array_keys( \$definition['fields'] );\n    }\n";

        $php .= "}\n";

        return $php;
    }

    /**
     * A value written into generated php as a default.
     *
     * @param array $column
     * @return string
     */
    protected static function phpDefault( array $column )
    {
        if ( $column['increment'] )
            return 'null';

        $default = $column['default'];

        if ( $default === false || $default === null )
            return $column['datatype'] === 'string' ? "''" : '0';

        if ( $column['datatype'] === 'integer' )
            return (string) (int) $default;

        if ( $column['datatype'] === 'float' )
            return (string) (float) $default;

        return "'" . self::phpString( $default ) . "'";
    }

    /**
     * The map from table to class, so one module can serve them all.
     *
     * @param array $settings
     * @param array $tables
     * @return string
     */
    protected static function registryClass( array $settings, array $tables )
    {
        $class = $settings['prefix'] . 'Registry';

        $php  = "<?php\n/**\n * " . $class . " - which class reads which table.\n *\n";
        $php .= " * The module takes a table name from the address, and a name from an address\n";
        $php .= " * is a name somebody could have typed. Nothing is looked up anywhere but in\n";
        $php .= " * this list, so a table that is not in it cannot be reached at all.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\nclass " . $class . "\n{\n";
        $php .= "    /**\n     * Table name => the class that reads it.\n     *\n     * @return array\n     */\n";
        $php .= "    static function tables()\n    {\n        return array(\n";

        $rows = array();
        foreach ( $tables as $table )
            $rows[] = "            '" . $table['name'] . "' => '" . $table['class'] . "'";
        $php .= implode( ",\n", $rows ) . " );\n    }\n\n";

        $php .= "    /**\n     * The class for a table, or false if it is not one of ours.\n     *\n"
              . "     * @param string \$table\n     * @return string|false\n     */\n";
        $php .= "    static function classOf( \$table )\n    {\n";
        $php .= "        \$tables = self::tables();\n\n";
        $php .= "        return is_string( \$table ) && isset( \$tables[\$table] ) ? \$tables[\$table] : false;\n    }\n\n";

        $php .= "    /**\n     * Loads the class for a table and hands back its name.\n     *\n"
              . "     * Extension classes are only autoloaded once the autoload map has been\n"
              . "     * regenerated, and an extension that has just been dropped in has not had\n"
              . "     * that done yet, so the file is required directly.\n     *\n"
              . "     * @param string \$table\n     * @return string|false\n     */\n";
        $php .= "    static function load( \$table )\n    {\n";
        $php .= "        \$class = self::classOf( \$table );\n";
        $php .= "        if ( \$class === false )\n            return false;\n\n";
        $php .= "        if ( !class_exists( \$class ) )\n        {\n";
        $php .= "            \$file = 'extension/" . $settings['name'] . "/classes/' . strtolower( \$class ) . '.php';\n";
        $php .= "            if ( !file_exists( \$file ) )\n                return false;\n";
        $php .= "            require_once \$file;\n        }\n\n";
        $php .= "        return class_exists( \$class ) ? \$class : false;\n    }\n\n";

        $php .= "    /**\n     * The first table, for a view reached without one named.\n     *\n     * @return string\n     */\n";
        $php .= "    static function firstTable()\n    {\n";
        $php .= "        \$tables = array_keys( self::tables() );\n\n";
        $php .= "        return count( \$tables ) ? \$tables[0] : '';\n    }\n";
        $php .= "}\n";

        return $php;
    }

    /**
     * The module's own description: its views, their parameters and its policies.
     *
     * @param array $settings
     * @param array $tables
     * @return string
     */
    protected static function moduleDefinition( array $settings, array $tables )
    {
        $module = $settings['module'];

        $php  = "<?php\n/**\n * The " . $settings['title'] . " module.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$Module = array( 'name' => '" . self::phpString( $settings['title'] ) . "' );\n\n";
        $php .= "\$ViewList = array();\n\n";
        $php .= "\$ViewList['list'] = array(\n";
        $php .= "    'script' => 'list.php',\n";
        $php .= "    'functions' => array( 'read' ),\n";
        $php .= "    'ui_context' => 'administration',\n";
        $php .= "    'default_navigation_part' => 'ezsetupnavigationpart',\n";
        $php .= "    'params' => array( 'Table' ),\n";
        $php .= "    'unordered_params' => array( 'offset' => 'Offset', 'limit' => 'Limit',\n";
        $php .= "                                 'sort' => 'Sort', 'dir' => 'Dir' ) );\n\n";
        $php .= "\$ViewList['edit'] = array(\n";
        $php .= "    'script' => 'edit.php',\n";
        $php .= "    'functions' => array( 'edit' ),\n";
        $php .= "    'ui_context' => 'edit',\n";
        $php .= "    'default_navigation_part' => 'ezsetupnavigationpart',\n";
        $php .= "    'single_post_actions' => array( 'StoreButton' => 'Store',\n";
        $php .= "                                    'CancelButton' => 'Cancel' ),\n";
        $php .= "    'params' => array( 'Table', 'Key' ) );\n\n";
        $php .= "\$ViewList['remove'] = array(\n";
        $php .= "    'script' => 'remove.php',\n";
        $php .= "    'functions' => array( 'edit' ),\n";
        $php .= "    'ui_context' => 'administration',\n";
        $php .= "    'default_navigation_part' => 'ezsetupnavigationpart',\n";
        $php .= "    'single_post_actions' => array( 'ConfirmButton' => 'Confirm',\n";
        $php .= "                                    'CancelButton' => 'Cancel' ),\n";
        $php .= "    'params' => array( 'Table', 'Key' ) );\n\n";
        $php .= "// Two policies rather than one, so reading can be granted without writing.\n";
        $php .= "\$FunctionList = array();\n";
        $php .= "\$FunctionList['read'] = array();\n";
        $php .= "\$FunctionList['edit'] = array();\n";

        return $php;
    }

    /**
     * The list view: one page of one table, sorted, with the controls to move.
     *
     * @param array $settings
     * @return string
     */
    protected static function listView( array $settings )
    {
        $registry = $settings['prefix'] . 'Registry';

        $php  = "<?php\n/**\n * One page of one table.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$Module = \$Params['Module'];\n\n";
        $php .= "require_once 'extension/" . $settings['name'] . "/classes/" . strtolower( $registry ) . ".php';\n\n";
        $php .= "\$table = isset( \$Params['Table'] ) && \$Params['Table'] !== '' ? \$Params['Table'] : " . $registry . "::firstTable();\n";
        $php .= "\$class = " . $registry . "::load( \$table );\n\n";
        $php .= "// A table that is not one of this extension's is not a table at all here.\n";
        $php .= "if ( \$class === false )\n";
        $php .= "    return \$Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );\n\n";
        $php .= "\$definition = call_user_func( array( \$class, 'definition' ) );\n";
        $php .= "\$columns = array_keys( \$definition['fields'] );\n\n";
        $php .= "// The page size and the column to sort by both come from the address, so\n";
        $php .= "// both are matched against what the table actually offers.\n";
        $php .= "\$limit = isset( \$Params['Limit'] ) && is_numeric( \$Params['Limit'] ) ? (int) \$Params['Limit'] : 25;\n";
        $php .= "if ( !in_array( \$limit, array( 25, 50, 250 ), true ) )\n    \$limit = 25;\n\n";
        $php .= "\$sort = isset( \$Params['Sort'] ) && in_array( \$Params['Sort'], \$columns, true )\n";
        $php .= "        ? \$Params['Sort'] : \$columns[0];\n";
        $php .= "\$dir = isset( \$Params['Dir'] ) && strtolower( \$Params['Dir'] ) === 'desc' ? 'desc' : 'asc';\n\n";
        $php .= "\$count = call_user_func( array( \$class, 'fetchListCount' ), null );\n\n";
        $php .= "\$offset = isset( \$Params['Offset'] ) && is_numeric( \$Params['Offset'] ) ? (int) \$Params['Offset'] : 0;\n";
        $php .= "if ( \$offset < 0 || \$offset >= \$count )\n";
        $php .= "    \$offset = \$count > 0 ? ( (int) ceil( \$count / \$limit ) - 1 ) * \$limit : 0;\n";
        $php .= "\$offset = (int) ( floor( \$offset / \$limit ) * \$limit );\n\n";
        $php .= "\$rows = call_user_func( array( \$class, 'fetchList' ), null, \$offset, \$limit, array( \$sort => \$dir ) );\n\n";
        $php .= "// Each row is handed over with its key already joined, because a template\n";
        $php .= "// has no way to put the key columns back together itself.\n";
        $php .= "\$listed = array();\n";
        $php .= "foreach ( \$rows as \$row )\n{\n";
        $php .= "    \$values = array();\n";
        $php .= "    foreach ( \$definition['keys'] as \$keyColumn )\n";
        $php .= "        \$values[] = \$row->attribute( \$keyColumn );\n\n";
        $php .= "    \$listed[] = array( 'object' => \$row, 'key' => implode( ',', \$values ) );\n}\n\n";
        $php .= "\$tpl = eZTemplate::factory();\n";
        $php .= "\$tpl->setVariable( 'table', \$table );\n";
        $php .= "\$tpl->setVariable( 'tables', " . $registry . "::tables() );\n";
        $php .= "\$tpl->setVariable( 'class_name', \$class );\n";
        $php .= "\$tpl->setVariable( 'columns', \$columns );\n";
        $php .= "\$tpl->setVariable( 'rows', \$listed );\n";
        $php .= "\$tpl->setVariable( 'row_count', \$count );\n";
        $php .= "\$tpl->setVariable( 'offset', \$offset );\n";
        $php .= "\$tpl->setVariable( 'limit', \$limit );\n";
        $php .= "\$tpl->setVariable( 'sort', \$sort );\n";
        $php .= "\$tpl->setVariable( 'dir', \$dir );\n";
        $php .= "\$tpl->setVariable( 'keys', \$definition['keys'] );\n";
        $php .= "\$tpl->setVariable( 'page_count', \$limit > 0 ? (int) ceil( \$count / \$limit ) : 1 );\n";
        $php .= "\$tpl->setVariable( 'page', \$limit > 0 ? (int) floor( \$offset / \$limit ) + 1 : 1 );\n\n";
        $php .= "\$Result = array();\n";
        $php .= "\$Result['content'] = \$tpl->fetch( 'design:" . $settings['module'] . "/list.tpl' );\n";
        $php .= "\$Result['path'] = array( array( 'url' => false, 'text' => '" . self::phpString( $settings['title'] ) . "' ),\n";
        $php .= "                         array( 'url' => false, 'text' => \$table ) );\n";

        return $php;
    }

    /**
     * The edit view: one row, or a new one.
     *
     * @param array $settings
     * @return string
     */
    protected static function editView( array $settings )
    {
        $registry = $settings['prefix'] . 'Registry';

        $php  = "<?php\n/**\n * One row of one table, on a form.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$Module = \$Params['Module'];\n";
        $php .= "\$http = eZHTTPTool::instance();\n\n";
        $php .= "require_once 'extension/" . $settings['name'] . "/classes/" . strtolower( $registry ) . ".php';\n\n";
        $php .= "\$table = isset( \$Params['Table'] ) && \$Params['Table'] !== '' ? \$Params['Table'] : " . $registry . "::firstTable();\n";
        $php .= "\$class = " . $registry . "::load( \$table );\n\n";
        $php .= "if ( \$class === false )\n";
        $php .= "    return \$Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );\n\n";
        $php .= "\$definition = call_user_func( array( \$class, 'definition' ) );\n";
        $php .= "\$fields = \$definition['fields'];\n";
        $php .= "\$keys = \$definition['keys'];\n";
        $php .= "\$increment = isset( \$definition['increment_key'] ) ? \$definition['increment_key'] : false;\n\n";
        $php .= "if ( \$Module->isCurrentAction( 'Cancel' ) )\n";
        $php .= "    return \$Module->redirectTo( '/" . $settings['module'] . "/list/' . \$table );\n\n";
        $php .= "// The key comes from the address as one value per key column, joined by a\n";
        $php .= "// comma, because a table may be keyed by more than one column.\n";
        $php .= "\$key = isset( \$Params['Key'] ) ? (string) \$Params['Key'] : '';\n";
        $php .= "\$keyValues = \$key === '' ? array() : explode( ',', \$key );\n\n";
        $php .= "\$object = null;\n";
        $php .= "if ( count( \$keyValues ) === count( \$keys ) )\n";
        $php .= "    \$object = call_user_func_array( array( \$class, 'fetch' ), \$keyValues );\n\n";
        $php .= "\$isNew = !( \$object instanceof \$class );\n";
        $php .= "if ( \$isNew )\n    \$object = call_user_func( array( \$class, 'create' ) );\n\n";
        $php .= "\$errors = array();\n\n";
        $php .= "if ( \$Module->isCurrentAction( 'Store' ) )\n{\n";
        $php .= "    foreach ( \$fields as \$field => \$description )\n    {\n";
        $php .= "        // A column the database fills in is not a column to type into.\n";
        $php .= "        if ( \$increment === \$field && \$isNew )\n            continue;\n";
        $php .= "        if ( !\$http->hasPostVariable( 'Field_' . \$field ) )\n            continue;\n\n";
        $php .= "        \$value = \$http->postVariable( 'Field_' . \$field );\n";
        $php .= "        if ( !is_scalar( \$value ) )\n            continue;\n\n";
        $php .= "        switch ( \$description['datatype'] )\n        {\n";
        $php .= "            case 'integer': \$value = (int) \$value; break;\n";
        $php .= "            case 'float':   \$value = (float) \$value; break;\n";
        $php .= "            default:        \$value = (string) \$value;\n        }\n\n";
        $php .= "        if ( !empty( \$description['required'] ) && \$description['datatype'] === 'string' && trim( \$value ) === '' )\n";
        $php .= "            \$errors[] = \$field . ' cannot be empty.';\n\n";
        $php .= "        \$object->setAttribute( \$field, \$value );\n    }\n\n";
        $php .= "    if ( !count( \$errors ) )\n    {\n";
        $php .= "        \$object->store();\n";
        $php .= "        return \$Module->redirectTo( '/" . $settings['module'] . "/list/' . \$table );\n";
        $php .= "    }\n}\n\n";
        $php .= "\$tpl = eZTemplate::factory();\n";
        $php .= "\$tpl->setVariable( 'table', \$table );\n";
        $php .= "\$tpl->setVariable( 'class_name', \$class );\n";
        $php .= "\$tpl->setVariable( 'fields', \$fields );\n";
        $php .= "\$tpl->setVariable( 'keys', \$keys );\n";
        $php .= "\$tpl->setVariable( 'increment', \$increment );\n";
        $php .= "\$tpl->setVariable( 'object', \$object );\n";
        $php .= "\$tpl->setVariable( 'is_new', \$isNew );\n";
        $php .= "\$tpl->setVariable( 'key', \$key );\n";
        $php .= "\$tpl->setVariable( 'errors', \$errors );\n\n";
        $php .= "\$Result = array();\n";
        $php .= "\$Result['content'] = \$tpl->fetch( 'design:" . $settings['module'] . "/edit.tpl' );\n";
        $php .= "\$Result['path'] = array( array( 'url' => '/" . $settings['module'] . "/list/' . \$table, 'text' => \$table ),\n";
        $php .= "                         array( 'url' => false, 'text' => \$isNew ? 'New' : 'Edit' ) );\n";

        return $php;
    }

    /**
     * The remove view: asks first.
     *
     * @param array $settings
     * @return string
     */
    protected static function removeView( array $settings )
    {
        $registry = $settings['prefix'] . 'Registry';

        $php  = "<?php\n/**\n * Removing one row, once it has been confirmed.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$Module = \$Params['Module'];\n\n";
        $php .= "require_once 'extension/" . $settings['name'] . "/classes/" . strtolower( $registry ) . ".php';\n\n";
        $php .= "\$table = isset( \$Params['Table'] ) && \$Params['Table'] !== '' ? \$Params['Table'] : " . $registry . "::firstTable();\n";
        $php .= "\$class = " . $registry . "::load( \$table );\n\n";
        $php .= "if ( \$class === false )\n";
        $php .= "    return \$Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );\n\n";
        $php .= "\$definition = call_user_func( array( \$class, 'definition' ) );\n";
        $php .= "\$keys = \$definition['keys'];\n\n";
        $php .= "\$key = isset( \$Params['Key'] ) ? (string) \$Params['Key'] : '';\n";
        $php .= "\$keyValues = \$key === '' ? array() : explode( ',', \$key );\n\n";
        $php .= "if ( count( \$keyValues ) !== count( \$keys ) )\n";
        $php .= "    return \$Module->redirectTo( '/" . $settings['module'] . "/list/' . \$table );\n\n";
        $php .= "\$object = call_user_func_array( array( \$class, 'fetch' ), \$keyValues );\n\n";
        $php .= "if ( \$Module->isCurrentAction( 'Cancel' ) || !( \$object instanceof \$class ) )\n";
        $php .= "    return \$Module->redirectTo( '/" . $settings['module'] . "/list/' . \$table );\n\n";
        $php .= "// Asked for twice, because the first time is a link and a link is followed by\n";
        $php .= "// anything that crawls the page.\n";
        $php .= "if ( \$Module->isCurrentAction( 'Confirm' ) )\n{\n";
        $php .= "    \$object->removeThis();\n";
        $php .= "    return \$Module->redirectTo( '/" . $settings['module'] . "/list/' . \$table );\n}\n\n";
        $php .= "\$tpl = eZTemplate::factory();\n";
        $php .= "\$tpl->setVariable( 'table', \$table );\n";
        $php .= "\$tpl->setVariable( 'object', \$object );\n";
        $php .= "\$tpl->setVariable( 'fields', \$definition['fields'] );\n";
        $php .= "\$tpl->setVariable( 'key', \$key );\n\n";
        $php .= "\$Result = array();\n";
        $php .= "\$Result['content'] = \$tpl->fetch( 'design:" . $settings['module'] . "/remove.tpl' );\n";
        $php .= "\$Result['path'] = array( array( 'url' => '/" . $settings['module'] . "/list/' . \$table, 'text' => \$table ),\n";
        $php .= "                         array( 'url' => false, 'text' => 'Remove' ) );\n";

        return $php;
    }

    /**
     * The fetch functions, so a template can reach the same rows.
     *
     * @param array $settings
     * @return string
     */
    protected static function functionDefinition( array $settings )
    {
        $module = $settings['module'];

        $php  = "<?php\n/**\n * What fetch() can ask this module for.\n *\n";
        $php .= " * From a template:\n *\n";
        $php .= " *   {def \$rows=fetch( '" . $module . "', 'list',\n";
        $php .= " *                     hash( 'table', '<table>', 'limit', 10 ) )}\n";
        $php .= " *   {def \$total=fetch( '" . $module . "', 'count', hash( 'table', '<table>' ) )}\n";
        $php .= " *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "\$FunctionList = array();\n\n";
        $php .= "\$FunctionList['list'] = array(\n";
        $php .= "    'name' => 'list',\n";
        $php .= "    'operation_types' => array( 'read' ),\n";
        $php .= "    'call_method' => array( 'class' => '" . $module . "FunctionCollection',\n";
        $php .= "                            'method' => 'fetchList' ),\n";
        $php .= "    'parameter_type' => 'standard',\n";
        $php .= "    'parameters' => array(\n";
        $php .= "        array( 'name' => 'table', 'type' => 'string', 'required' => true ),\n";
        $php .= "        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ),\n";
        $php .= "        array( 'name' => 'limit', 'type' => 'integer', 'required' => false, 'default' => 25 ),\n";
        $php .= "        array( 'name' => 'sort', 'type' => 'string', 'required' => false, 'default' => false ),\n";
        $php .= "        array( 'name' => 'dir', 'type' => 'string', 'required' => false, 'default' => 'asc' ) ) );\n\n";
        $php .= "\$FunctionList['count'] = array(\n";
        $php .= "    'name' => 'count',\n";
        $php .= "    'operation_types' => array( 'read' ),\n";
        $php .= "    'call_method' => array( 'class' => '" . $module . "FunctionCollection',\n";
        $php .= "                            'method' => 'fetchCount' ),\n";
        $php .= "    'parameter_type' => 'standard',\n";
        $php .= "    'parameters' => array(\n";
        $php .= "        array( 'name' => 'table', 'type' => 'string', 'required' => true ) ) );\n\n";
        $php .= "\$FunctionList['object'] = array(\n";
        $php .= "    'name' => 'object',\n";
        $php .= "    'operation_types' => array( 'read' ),\n";
        $php .= "    'call_method' => array( 'class' => '" . $module . "FunctionCollection',\n";
        $php .= "                            'method' => 'fetchObject' ),\n";
        $php .= "    'parameter_type' => 'standard',\n";
        $php .= "    'parameters' => array(\n";
        $php .= "        array( 'name' => 'table', 'type' => 'string', 'required' => true ),\n";
        $php .= "        array( 'name' => 'key', 'type' => 'string', 'required' => true ) ) );\n";

        return $php;
    }

    /**
     * What those fetch functions call.
     *
     * @param array $settings
     * @return string
     */
    protected static function functionCollection( array $settings )
    {
        $registry = $settings['prefix'] . 'Registry';
        $class    = $settings['module'] . 'FunctionCollection';

        $php  = "<?php\n/**\n * " . $class . " - what fetch() calls.\n *\n";
        $php .= " * Every one of these takes a table name from a template, which is to say from\n";
        $php .= " * whoever wrote the template; the registry is the only thing that turns one\n";
        $php .= " * into a class, so a name that is not in it reaches nothing.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "require_once 'extension/" . $settings['name'] . "/classes/" . strtolower( $registry ) . ".php';\n\n";
        $php .= "class " . $class . "\n{\n";
        $php .= "    static function fetchList( \$table, \$offset, \$limit, \$sort, \$dir )\n    {\n";
        $php .= "        \$class = " . $registry . "::load( \$table );\n";
        $php .= "        if ( \$class === false )\n            return array( 'error' => array( 'error_type' => 'kernel', 'error_code' => eZError::KERNEL_NOT_AVAILABLE ) );\n\n";
        $php .= "        \$definition = call_user_func( array( \$class, 'definition' ) );\n";
        $php .= "        \$columns = array_keys( \$definition['fields'] );\n\n";
        $php .= "        \$sorts = null;\n";
        $php .= "        if ( is_string( \$sort ) && in_array( \$sort, \$columns, true ) )\n";
        $php .= "            \$sorts = array( \$sort => strtolower( (string) \$dir ) === 'desc' ? 'desc' : 'asc' );\n\n";
        $php .= "        \$limit = (int) \$limit;\n";
        $php .= "        if ( \$limit < 1 || \$limit > 1000 )\n            \$limit = 25;\n\n";
        $php .= "        return array( 'result' => call_user_func( array( \$class, 'fetchList' ), null,\n";
        $php .= "                                                 max( 0, (int) \$offset ), \$limit, \$sorts ) );\n    }\n\n";
        $php .= "    static function fetchCount( \$table )\n    {\n";
        $php .= "        \$class = " . $registry . "::load( \$table );\n";
        $php .= "        if ( \$class === false )\n            return array( 'result' => 0 );\n\n";
        $php .= "        return array( 'result' => call_user_func( array( \$class, 'fetchListCount' ), null ) );\n    }\n\n";
        $php .= "    static function fetchObject( \$table, \$key )\n    {\n";
        $php .= "        \$class = " . $registry . "::load( \$table );\n";
        $php .= "        if ( \$class === false )\n            return array( 'result' => null );\n\n";
        $php .= "        \$definition = call_user_func( array( \$class, 'definition' ) );\n";
        $php .= "        \$values = explode( ',', (string) \$key );\n\n";
        $php .= "        if ( count( \$values ) !== count( \$definition['keys'] ) )\n            return array( 'result' => null );\n\n";
        $php .= "        return array( 'result' => call_user_func_array( array( \$class, 'fetch' ), \$values ) );\n    }\n}\n";

        return $php;
    }

    // ── The templates ────────────────────────────────────────────────────────

    protected static function listTemplate( array $settings )
    {
        $module = $settings['module'];

        return "{* One page of one table.\n\n"
             . "   The columns come from the class definition rather than from anything\n"
             . "   written here, so a column added to the table shows up by adding it to the\n"
             . "   definition and nowhere else. *}\n\n"
             . "<div class=\"context-block\">\n\n"
             . "<div class=\"box-header\"><div class=\"box-ml\">\n"
             . "<h1 class=\"context-title\">{\$table|wash} ({\$row_count|wash})</h1>\n"
             . "<div class=\"header-mainline\"></div>\n"
             . "</div></div>\n\n"
             . "<div class=\"box-ml\"><div class=\"box-mr\"><div class=\"box-content\">\n\n"
             . "{* The other tables this module covers. *}\n"
             . "{if \$tables|count|gt( 1 )}\n"
             . "<div class=\"context-toolbar\"><div class=\"button-left\"><p class=\"table-preferences\">\n"
             . "{foreach \$tables as \$" . $module . "_table => \$" . $module . "_class}\n"
             . "    {if eq( \$" . $module . "_table, \$table )}<span class=\"current\">{\$" . $module . "_table|wash}</span>\n"
             . "    {else}<a href={concat( '/" . $module . "/list/', \$" . $module . "_table )|ezurl}>{\$" . $module . "_table|wash}</a>{/if}\n"
             . "{/foreach}\n"
             . "</p></div></div>\n"
             . "{/if}\n\n"
             . "{* How many rows a page holds. *}\n"
             . "<div class=\"context-toolbar\"><div class=\"button-left\"><p class=\"table-preferences\">\n"
             . "{foreach array( 25, 50, 250 ) as \$" . $module . "_limit}\n"
             . "    {if eq( \$" . $module . "_limit, \$limit )}<span class=\"current\">{\$" . $module . "_limit|wash}</span>\n"
             . "    {else}<a href={concat( '/" . $module . "/list/', \$table, '/(limit)/', \$" . $module . "_limit, '/(sort)/', \$sort, '/(dir)/', \$dir )|ezurl}>{\$" . $module . "_limit|wash}</a>{/if}\n"
             . "{/foreach}\n"
             . "</p></div></div>\n\n"
             . "{if \$rows|count}\n"
             . "<table class=\"list\" cellspacing=\"0\">\n"
             . "<tr>\n"
             . "{foreach \$columns as \$" . $module . "_column}\n"
             . "    <th><a href={concat( '/" . $module . "/list/', \$table, '/(sort)/', \$" . $module . "_column,\n"
             . "                         '/(dir)/', cond( and( eq( \$" . $module . "_column, \$sort ), eq( \$dir, 'asc' ) ), 'desc', 'asc' ),\n"
             . "                         '/(limit)/', \$limit )|ezurl}>{\$" . $module . "_column|wash}{if eq( \$" . $module . "_column, \$sort )}{if eq( \$dir, 'asc' )} &#9650;{else} &#9660;{/if}{/if}</a></th>\n"
             . "{/foreach}\n"
             . "    <th class=\"tight\">&nbsp;</th>\n"
             . "    <th class=\"tight\">&nbsp;</th>\n"
             . "</tr>\n"
             . "{foreach \$rows as \$" . $module . "_row sequence array( bglight, bgdark ) as \$" . $module . "_seq}\n"
             . "<tr class=\"{\$" . $module . "_seq|wash}\">\n"
             . "{foreach \$columns as \$" . $module . "_column}\n"
             . "    <td>{\$" . $module . "_row.object.\$" . $module . "_column|wash}</td>\n"
             . "{/foreach}\n"
             . "    <td class=\"tight\"><a href={concat( '/" . $module . "/edit/', \$table, '/', \$" . $module . "_row.key )|ezurl}>{'Edit'|i18n( 'extension/" . $settings['name'] . "' )}</a></td>\n"
             . "    <td class=\"tight\"><a href={concat( '/" . $module . "/remove/', \$table, '/', \$" . $module . "_row.key )|ezurl}>{'Remove'|i18n( 'extension/" . $settings['name'] . "' )}</a></td>\n"
             . "</tr>\n"
             . "{/foreach}\n"
             . "</table>\n"
             . "{else}\n"
             . "<div class=\"block\"><p>{'This table has no rows.'|i18n( 'extension/" . $settings['name'] . "' )}</p></div>\n"
             . "{/if}\n\n"
             . "{* Paging. *}\n"
             . "{if \$page_count|gt( 1 )}\n"
             . "<div class=\"context-toolbar\"><div class=\"pagenavigator\"><p>\n"
             . "{if \$offset|gt( 0 )}\n"
             . "    <span class=\"previous\"><a href={concat( '/" . $module . "/list/', \$table, '/(offset)/', sub( \$offset, \$limit ), '/(limit)/', \$limit, '/(sort)/', \$sort, '/(dir)/', \$dir )|ezurl}><span class=\"text\">&laquo;&nbsp;{'Previous'|i18n( 'design/admin/navigator' )}</span></a></span>\n"
             . "{else}\n"
             . "    <span class=\"previous\"><span class=\"text disabled\">&laquo;&nbsp;{'Previous'|i18n( 'design/admin/navigator' )}</span></span>\n"
             . "{/if}\n"
             . "{if sum( \$offset, \$limit )|lt( \$row_count )}\n"
             . "    <span class=\"next\"><a href={concat( '/" . $module . "/list/', \$table, '/(offset)/', sum( \$offset, \$limit ), '/(limit)/', \$limit, '/(sort)/', \$sort, '/(dir)/', \$dir )|ezurl}><span class=\"text\">{'Next'|i18n( 'design/admin/navigator' )}&nbsp;&raquo;</span></a></span>\n"
             . "{else}\n"
             . "    <span class=\"next\"><span class=\"text disabled\">{'Next'|i18n( 'design/admin/navigator' )}&nbsp;&raquo;</span></span>\n"
             . "{/if}\n"
             . "<span class=\"pages\"><span class=\"current\">{\$page|wash} / {\$page_count|wash}</span></span>\n"
             . "</p><div class=\"break\"></div></div></div>\n"
             . "{/if}\n\n"
             . "</div></div></div>\n\n"
             . "<div class=\"controlbar\"><div class=\"box-bc\"><div class=\"box-ml\">\n"
             . "<div class=\"block\">\n"
             . "    <a class=\"button\" href={concat( '/" . $module . "/edit/', \$table )|ezurl}>{'New row'|i18n( 'extension/" . $settings['name'] . "' )}</a>\n"
             . "</div>\n"
             . "</div></div></div>\n\n"
             . "</div>\n";
    }

    protected static function editTemplate( array $settings )
    {
        $module = $settings['module'];
        $domain = 'extension/' . $settings['name'];

        return "{* One row, on a form. Every column of the table gets a box, except one the\n"
             . "   database numbers itself, which is shown but not typed into. *}\n\n"
             . "<form method=\"post\" action={concat( '/" . $module . "/edit/', \$table, '/', \$key )|ezurl}>\n\n"
             . "<div class=\"context-block\">\n\n"
             . "<div class=\"box-header\"><div class=\"box-ml\">\n"
             . "<h1 class=\"context-title\">{if \$is_new}{'New row in %table'|i18n( '" . $domain . "',, hash( '%table', \$table ) )}"
             . "{else}{'Editing %table'|i18n( '" . $domain . "',, hash( '%table', \$table ) )}{/if}</h1>\n"
             . "<div class=\"header-mainline\"></div>\n"
             . "</div></div>\n\n"
             . "<div class=\"box-ml\"><div class=\"box-mr\"><div class=\"box-content\">\n\n"
             . "{if \$errors|count}\n"
             . "<div class=\"error\"><ul>\n"
             . "{foreach \$errors as \$" . $module . "_error}<li>{\$" . $module . "_error|wash}</li>{/foreach}\n"
             . "</ul></div>\n"
             . "{/if}\n\n"
             . "<div class=\"context-attributes\">\n"
             . "{foreach \$fields as \$" . $module . "_field => \$" . $module . "_description}\n"
             . "<div class=\"block\">\n"
             . "    <label for=\"field_{\$" . $module . "_field|wash}\">{\$" . $module . "_field|wash}"
             . "{if \$" . $module . "_description.required} *{/if}</label>\n"
             . "    {if and( \$is_new, eq( \$" . $module . "_field, \$increment ) )}\n"
             . "    <p class=\"small\">{'Filled in by the database when the row is stored.'|i18n( '" . $domain . "' )}</p>\n"
             . "    {elseif eq( \$" . $module . "_field, \$increment )}\n"
             . "    <input class=\"halfbox\" type=\"text\" id=\"field_{\$" . $module . "_field|wash}\" value=\"{\$object.\$" . $module . "_field|wash}\" readonly=\"readonly\" />\n"
             . "    {else}\n"
             . "    <input class=\"halfbox\" type=\"text\" id=\"field_{\$" . $module . "_field|wash}\" name=\"Field_{\$" . $module . "_field|wash}\" value=\"{\$object.\$" . $module . "_field|wash}\" />\n"
             . "    {/if}\n"
             . "    <p class=\"small\">{\$" . $module . "_description.datatype|wash}</p>\n"
             . "</div>\n"
             . "{/foreach}\n"
             . "</div>\n\n"
             . "</div></div></div>\n\n"
             . "<div class=\"controlbar\"><div class=\"box-bc\"><div class=\"box-ml\">\n"
             . "<div class=\"block\">\n"
             . "    <input class=\"defaultbutton\" type=\"submit\" name=\"StoreButton\" value=\"{'Store'|i18n( '" . $domain . "' )}\" />\n"
             . "    <input class=\"button\" type=\"submit\" name=\"CancelButton\" value=\"{'Cancel'|i18n( '" . $domain . "' )}\" />\n"
             . "</div>\n"
             . "</div></div></div>\n\n"
             . "</div>\n</form>\n";
    }

    protected static function removeTemplate( array $settings )
    {
        $module = $settings['module'];
        $domain = 'extension/' . $settings['name'];

        return "{* Asked before anything goes. *}\n\n"
             . "<form method=\"post\" action={concat( '/" . $module . "/remove/', \$table, '/', \$key )|ezurl}>\n\n"
             . "<div class=\"context-block\">\n\n"
             . "<div class=\"box-header\"><div class=\"box-ml\">\n"
             . "<h1 class=\"context-title\">{'Remove this row?'|i18n( '" . $domain . "' )}</h1>\n"
             . "<div class=\"header-mainline\"></div>\n"
             . "</div></div>\n\n"
             . "<div class=\"box-ml\"><div class=\"box-mr\"><div class=\"box-content\">\n"
             . "<div class=\"context-attributes\">\n"
             . "<p>{'This row of %table will be removed. It cannot be brought back.'|i18n( '" . $domain . "',, hash( '%table', \$table ) )}</p>\n"
             . "<table class=\"list\" cellspacing=\"0\">\n"
             . "{foreach \$fields as \$" . $module . "_field => \$" . $module . "_description}\n"
             . "<tr><th>{\$" . $module . "_field|wash}</th><td>{\$object.\$" . $module . "_field|wash}</td></tr>\n"
             . "{/foreach}\n"
             . "</table>\n"
             . "</div>\n"
             . "</div></div></div>\n\n"
             . "<div class=\"controlbar\"><div class=\"box-bc\"><div class=\"box-ml\">\n"
             . "<div class=\"block\">\n"
             . "    <input class=\"defaultbutton\" type=\"submit\" name=\"ConfirmButton\" value=\"{'Remove'|i18n( '" . $domain . "' )}\" />\n"
             . "    <input class=\"button\" type=\"submit\" name=\"CancelButton\" value=\"{'Cancel'|i18n( '" . $domain . "' )}\" />\n"
             . "</div>\n"
             . "</div></div></div>\n\n"
             . "</div>\n</form>\n";
    }

    // ── The settings, the schema and the readme ──────────────────────────────

    protected static function moduleIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Module registration' );
        $ini .= "[ModuleSettings]\n";
        $ini .= "# Where eZ looks for this module, and which module it is.\n";
        $ini .= "ExtensionRepositories[]=" . $settings['name'] . "\n";
        $ini .= "ModuleList[]=" . $settings['module'] . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function designIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Puts this extension's templates into the design chain, which is how the\n";
        $ini .= "# module's own templates are found.\n";
        $ini .= "DesignExtensions[]=" . $settings['name'] . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function menuIni( array $settings )
    {
        $module = $settings['module'];

        $ini  = self::iniHeader( $settings, 'Admin menu' );
        $ini .= "[Leftmenu_setup]\n";
        $ini .= "# The entry in Setup. LinkNames is what it reads as; without it the menu\n";
        $ini .= "# falls back to the key, and a module name is not a label.\n";
        $ini .= "Links[" . $module . "]=" . $module . "/list\n";
        $ini .= "LinkNames[" . $module . "]=" . $settings['title'] . "\n";
        $ini .= "PolicyList_" . $module . "[]=" . $module . "/read\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    /**
     * The tables as eZDbSchema describes them, for the installer and the
     * consistency check.
     *
     * @param array $settings
     * @param array $tables
     * @return string
     */
    protected static function dba( array $settings, array $tables )
    {
        $schema = array();
        foreach ( $tables as $table )
            $schema[$table['name']] = $table['schema'];

        return "<?php\n/**\n * The tables this extension reads, as the database described them when it\n"
             . " * was generated. Setup > System Upgrade > Database consistency check compares\n"
             . " * the live database against this, so it has to be kept in step with the table.\n */\n\n"
             . "return " . var_export( $schema, true ) . ";\n";
    }

    protected static function readme( array $settings, array $tables, array $paths = array() )
    {
        $module = $settings['module'];

        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "Administration and a template API for tables that already exist, built with\n";
        $readme .= "the module extension wizard in Setup > RAD.\n\n";

        $readme .= "## Tables it covers\n\n";
        $readme .= "| table | class | keys |\n|---|---|---|\n";
        foreach ( $tables as $table )
            $readme .= "| `" . $table['name'] . "` | `" . $table['class'] . "` | `"
                     . implode( '`, `', $table['keys'] ) . "` |\n";

        $readme .= "\n## Switching it on\n\n";
        $readme .= "Add it to the active extensions in `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```ini\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "Then clear the caches and regenerate the autoload map, so the classes are\n";
        $readme .= "found without each file being required by hand:\n\n";
        $readme .= "```sh\nphp bin/php/ezcache.php --clear-all\nphp bin/php/ezpgenerateautoloads.php --extension\n```\n\n";
        $readme .= "It appears under **Setup** as *" . $settings['title'] . "*.\n\n";

        $readme .= "## From a template\n\n";
        $readme .= "```\n";
        $readme .= "{def \$rows=fetch( '" . $module . "', 'list',\n";
        $readme .= "                  hash( 'table', '" . ( count( $tables ) ? reset( $tables )['name'] : 'table' ) . "',\n";
        $readme .= "                        'limit', 10, 'sort', 'id', 'dir', 'desc' ) )}\n";
        $readme .= "{def \$total=fetch( '" . $module . "', 'count', hash( 'table', '"
                 . ( count( $tables ) ? reset( $tables )['name'] : 'table' ) . "' ) )}\n";
        $readme .= "```\n\n";

        $readme .= "## From php\n\n";
        if ( count( $tables ) )
        {
            $first = reset( $tables );
            $readme .= "```php\n";
            $readme .= "\$rows  = " . $first['class'] . "::fetchList( null, 0, 25, array( '" . $first['keys'][0] . "' => 'asc' ) );\n";
            $readme .= "\$total = " . $first['class'] . "::fetchListCount();\n";
            $readme .= "\$one   = " . $first['class'] . "::fetch( \$" . $first['keys'][0] . " );\n\n";
            $readme .= "\$new = " . $first['class'] . "::create();\n";
            $readme .= "\$new->setAttribute( '" . $first['columns'][0]['name'] . "', \$value );\n";
            $readme .= "\$new->store();\n";
            $readme .= "```\n\n";
        }

        $readme .= "## Access\n\n";
        $readme .= "Two policies: `" . $module . "/read` for the list, `" . $module . "/edit` for\n";
        $readme .= "changing and removing. Grant them to a role in User accounts > Roles.\n\n";

        $readme .= "## A word about the definitions\n\n";
        $readme .= "Each class's `definition()` was written from the table as the database\n";
        $readme .= "described it at the time. It is not read from the table at run time, so a\n";
        $readme .= "column added to the table later has to be added to the definition too.\n\n";

        $readme .= "## Licence\n\n" . self::licenceLine( $settings ) . "\n\n";

        if ( count( $paths ) )
        {
            $readme .= "## What is inside\n\n";
            foreach ( $paths as $path )
                $readme .= "- `" . $path . "`\n";
        }

        return $readme;
    }

    /**
     * The tables as the picker shows them.
     *
     * A row count is only asked for on a table that has been chosen: counting
     * every table of a database with hundreds of them is a page that never
     * finishes, and the number is only interesting once somebody is interested
     * in the table.
     *
     * @param eZDBInterface $db
     * @param array $schema from schema().
     * @param array $selected table names.
     * @return array
     */
    public static function summaries( $db, array $schema, array $selected = array() )
    {
        $summaries = array();

        foreach ( $schema as $name => $table )
        {
            $columns = self::columns( $table );
            $keys    = self::keys( $columns );
            $chosen  = in_array( $name, $selected, true );

            $rows = '';
            if ( $chosen && $db instanceof eZDBInterface )
            {
                // The name has been through the pattern, and nothing that did
                // not come out of the database's own table list reaches here.
                $result = $db->arrayQuery( 'SELECT COUNT(*) AS c FROM ' . $name );
                if ( is_array( $result ) && isset( $result[0]['c'] ) )
                    $rows = (string) $result[0]['c'];
            }

            $summaries[] = array(
                'name'      => $name,
                'columns'   => count( $columns ),
                'keys'      => implode( ', ', $keys ),
                'has_key'   => isset( $table['indexes']['PRIMARY'] ),
                'increment' => self::incrementKey( $columns ) !== false,
                'rows'      => $rows,
                'selected'  => $chosen,
                'class'     => $name );
        }

        return $summaries;
    }

    // ── Tables that live on another database ─────────────────────────────────

    /**
     * The connection the generated classes use when the tables are not on this
     * installation's own database.
     *
     * Where eZ has a handler it is used, so everything eZ knows about that kind
     * of database still applies - including the MongoDB handler, which turns the
     * queries below into what a document store understands. Where it has none,
     * PDO is used directly.
     *
     * @param array $settings
     * @return string
     */
    protected static function connectionClass( array $settings )
    {
        $class = $settings['prefix'] . 'Connection';
        $ini   = $settings['name'] . '.ini';

        $php  = "<?php\n/**\n * " . $class . " - the database these classes read.\n *\n";
        $php .= " * The tables are not on this installation's own database, so nothing here\n";
        $php .= " * goes through eZDB::instance() without arguments: that would quietly read\n";
        $php .= " * the wrong database and answer as though it were right.\n *\n";
        $php .= " * The details come from " . $ini . ", so they can be changed without touching\n";
        $php .= " * any code, and can differ between a development machine and a live one.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\nclass " . $class . "\n{\n";
        $php .= "    protected static \$Instance = null;\n\n";
        $php .= "    /**\n     * The connection, opened once per request.\n     *\n";
        $php .= "     * @return eZDBInterface|PDO|false\n     */\n";
        $php .= "    static function instance()\n    {\n";
        $php .= "        if ( self::\$Instance !== null )\n            return self::\$Instance;\n\n";
        $php .= "        \$ini = eZINI::instance( '" . $ini . "' );\n";
        $php .= "        \$type = \$ini->variable( 'DatabaseSettings', 'Type' );\n";
        $php .= "        \$handler = \$ini->hasVariable( 'DatabaseSettings', 'Handler' )\n";
        $php .= "                   ? \$ini->variable( 'DatabaseSettings', 'Handler' ) : '';\n\n";
        $php .= "        \$parameters = array(\n";
        $php .= "            'server'   => \$ini->variable( 'DatabaseSettings', 'Server' ),\n";
        $php .= "            'user'     => \$ini->variable( 'DatabaseSettings', 'User' ),\n";
        $php .= "            'password' => \$ini->variable( 'DatabaseSettings', 'Password' ),\n";
        $php .= "            'database' => \$ini->variable( 'DatabaseSettings', 'Database' ) );\n\n";
        $php .= "        \$port = (int) \$ini->variable( 'DatabaseSettings', 'Port' );\n";
        $php .= "        if ( \$port > 0 )\n            \$parameters['port'] = \$port;\n\n";
        $php .= "        try\n        {\n";
        $php .= "            if ( \$handler !== '' )\n            {\n";
        $php .= "                \$db = eZDB::instance( \$handler, \$parameters, true );\n";
        $php .= "                self::\$Instance = \$db instanceof eZDBInterface && \$db->isConnected() ? \$db : false;\n";
        $php .= "            }\n            else\n            {\n";
        $php .= "                self::\$Instance = new PDO( \$ini->variable( 'DatabaseSettings', 'DSN' ),\n";
        $php .= "                                           \$parameters['user'], \$parameters['password'],\n";
        $php .= "                                           array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );\n";
        $php .= "            }\n        }\n";
        $php .= "        catch ( Exception \$e )\n        {\n";
        $php .= "            eZDebug::writeError( \$e->getMessage(), __METHOD__ );\n";
        $php .= "            self::\$Instance = false;\n        }\n";
        $php .= "        catch ( Throwable \$e )\n        {\n";
        $php .= "            eZDebug::writeError( \$e->getMessage(), __METHOD__ );\n";
        $php .= "            self::\$Instance = false;\n        }\n\n";
        $php .= "        return self::\$Instance;\n    }\n\n";
        $php .= "    /**\n     * Rows from a query, whichever kind of connection this is.\n     *\n";
        $php .= "     * @param string \$sql\n     * @param array \$parameters values for the ? placeholders in it.\n";
        $php .= "     * @return array\n     */\n";
        $php .= "    static function rows( \$sql, array \$parameters = array() )\n    {\n";
        $php .= "        \$connection = self::instance();\n";
        $php .= "        if ( \$connection === false )\n            return array();\n\n";
        $php .= "        try\n        {\n";
        $php .= "            if ( \$connection instanceof PDO )\n            {\n";
        $php .= "                \$statement = \$connection->prepare( \$sql );\n";
        $php .= "                \$statement->execute( \$parameters );\n\n";
        $php .= "                return \$statement->fetchAll( PDO::FETCH_ASSOC );\n";
        $php .= "            }\n\n";
        $php .= "            // An eZ handler has no placeholders, so the values are put in by\n";
        $php .= "            // the caller through quote() before they ever get here.\n";
        $php .= "            \$rows = \$connection->arrayQuery( \$sql );\n\n";
        $php .= "            return is_array( \$rows ) ? \$rows : array();\n";
        $php .= "        }\n";
        $php .= "        catch ( Exception \$e )\n        {\n";
        $php .= "            eZDebug::writeError( \$e->getMessage(), __METHOD__ );\n";
        $php .= "        }\n";
        $php .= "        catch ( Throwable \$e )\n        {\n";
        $php .= "            eZDebug::writeError( \$e->getMessage(), __METHOD__ );\n";
        $php .= "        }\n\n";
        $php .= "        return array();\n    }\n\n";
        $php .= "    /**\n     * A value, safe to put in a query.\n     *\n";
        $php .= "     * @param mixed \$value\n     * @return string\n     */\n";
        $php .= "    static function quote( \$value )\n    {\n";
        $php .= "        if ( \$value === null )\n            return 'NULL';\n";
        $php .= "        if ( is_int( \$value ) || is_float( \$value ) )\n            return (string) \$value;\n\n";
        $php .= "        \$connection = self::instance();\n\n";
        $php .= "        if ( \$connection instanceof PDO )\n            return \$connection->quote( (string) \$value );\n";
        $php .= "        if ( \$connection instanceof eZDBInterface )\n            return \"'\" . \$connection->escapeString( (string) \$value ) . \"'\";\n\n";
        $php .= "        // No connection means no query either; this is only reached by code\n";
        $php .= "        // building a string it will not get to run.\n";
        $php .= "        return \"''\";\n    }\n}\n";

        return $php;
    }

    /**
     * The ini the connection details live in.
     *
     * @param array $settings
     * @return string
     */
    protected static function connectionIni( array $settings )
    {
        $types = self::databaseCapabilities();
        $type  = isset( $types[$settings['db_type']] ) ? $types[$settings['db_type']] : $types['mysqli'];

        $ini  = "<?php /* #?ini charset=\"utf-8\"?\n\n";
        $ini .= "#\n# Where " . $settings['title'] . " reads its tables from.\n#\n";
        $ini .= "# These are the details the wizard connected with. Change them here rather\n";
        $ini .= "# than in any code - a development machine and a live one rarely read the\n";
        $ini .= "# same database.\n";
        $ini .= "#\n# The password is left empty on purpose. Put it in an override that is not\n";
        $ini .= "# in version control:\n";
        $ini .= "#\n#   settings/override/" . $settings['name'] . ".ini.append.php\n#\n\n";
        $ini .= "[DatabaseSettings]\n";
        $ini .= "# The eZ handler to use, when eZ has one for this kind. Empty means PDO,\n";
        $ini .= "# and then DSN below is what is connected with.\n";
        $ini .= "Handler=" . ( $type['via'] === 'ez' ? $type['ez'] : '' ) . "\n";
        $ini .= "Type=" . $settings['db_type'] . "\n";
        $ini .= "Server=" . ( $settings['db_type'] === 'sqlite' ? '' : $settings['db_server'] ) . "\n";
        $ini .= "Port=" . ( $settings['db_port'] > 0 ? $settings['db_port'] : '' ) . "\n";
        $ini .= "Database=" . ( $settings['db_type'] === 'sqlite' ? $settings['db_file'] : $settings['db_name'] ) . "\n";
        $ini .= "User=" . $settings['db_user'] . "\n";
        $ini .= "Password=\n";

        $dsn = self::dsn( $settings, $type );
        $ini .= "DSN=" . ( $dsn === false ? '' : $dsn ) . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    /**
     * A class over a table on another database.
     *
     * The same methods as the eZPersistentObject one, so the module, the
     * templates and the fetch functions do not know the difference - but the
     * queries go through the connection above rather than through eZ's own.
     *
     * @param array $settings
     * @param array $table
     * @return string
     */
    protected static function externalClass( array $settings, array $table )
    {
        $class      = $table['class'];
        $connection = $settings['prefix'] . 'Connection';
        $columns    = array();
        foreach ( $table['columns'] as $column )
            $columns[] = $column['name'];

        $php  = "<?php\n/**\n * " . $class . " - the rows of " . $table['name'] . ", which lives on\n";
        $php .= " * another database.\n *\n";
        $php .= " * Not an eZPersistentObject: that reads this installation's own database, and\n";
        $php .= " * this table is not on it. The methods are the same ones, so anything written\n";
        $php .= " * against them works either way.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "require_once 'extension/" . $settings['name'] . "/classes/" . strtolower( $connection ) . ".php';\n\n";
        $php .= "class " . $class . "\n{\n";
        $php .= "    const TABLE = '" . $table['name'] . "';\n\n";
        $php .= "    protected \$Row = array();\n\n";
        $php .= "    function __construct( array \$row = array() )\n    {\n";
        $php .= "        \$this->Row = \$row;\n    }\n\n";
        $php .= "    /**\n     * The same shape eZPersistentObject describes a table with.\n     *\n     * @return array\n     */\n";
        $php .= "    static function definition()\n    {\n        return array( 'fields' => array(\n";

        $fields = array();
        foreach ( $table['columns'] as $column )
        {
            $field  = "            '" . $column['name'] . "' => array(\n";
            $field .= "                'name' => '" . $column['property'] . "',\n";
            $field .= "                'datatype' => '" . $column['datatype'] . "',\n";
            $field .= "                'default' => " . self::phpDefault( $column ) . ",\n";
            $field .= "                'required' => " . ( $column['required'] ? 'true' : 'false' ) . " )";
            $fields[] = $field;
        }

        $php .= implode( ",\n", $fields ) . " ),\n";
        $php .= "                      'keys' => array( '" . implode( "', '", $table['keys'] ) . "' ),\n";
        if ( $table['increment'] !== false )
            $php .= "                      'increment_key' => '" . $table['increment'] . "',\n";
        $php .= "                      'class_name' => '" . $class . "',\n";
        $php .= "                      'name' => '" . $table['name'] . "' );\n    }\n\n";

        $php .= "    /**\n     * The columns, in the order the table declares them.\n     *\n     * @return array\n     */\n";
        $php .= "    static function columns()\n    {\n";
        $php .= "        return array( '" . implode( "', '", $columns ) . "' );\n    }\n\n";

        $php .= "    /**\n     * One column of this row.\n     *\n     * @param string \$column\n     * @return mixed\n     */\n";
        $php .= "    function attribute( \$column )\n    {\n";
        $php .= "        return isset( \$this->Row[\$column] ) ? \$this->Row[\$column] : null;\n    }\n\n";
        $php .= "    function hasAttribute( \$column )\n    {\n";
        $php .= "        return in_array( \$column, self::columns(), true );\n    }\n\n";
        $php .= "    function attributes()\n    {\n        return self::columns();\n    }\n\n";
        $php .= "    function setAttribute( \$column, \$value )\n    {\n";
        $php .= "        if ( \$this->hasAttribute( \$column ) )\n            \$this->Row[\$column] = \$value;\n    }\n\n";
        $php .= "    function row()\n    {\n        return \$this->Row;\n    }\n\n";

        $php .= "    static function create( array \$row = array() )\n    {\n";
        $php .= "        \$defaults = array(\n";
        $defaults = array();
        foreach ( $table['columns'] as $column )
            $defaults[] = "            '" . $column['name'] . "' => " . ( $column['increment'] ? 'null' : self::phpDefault( $column ) );
        $php .= implode( ",\n", $defaults ) . " );\n\n";
        $php .= "        return new " . $class . "( array_merge( \$defaults, \$row ) );\n    }\n\n";

        // fetch by key
        $keyArguments = array();
        foreach ( $table['keys'] as $key )
            $keyArguments[] = '$' . $key;

        $php .= "    /**\n     * One row, by its key.\n     *\n     * @return " . $class . "|null\n     */\n";
        $php .= "    static function fetch( " . implode( ', ', $keyArguments ) . " )\n    {\n";
        $php .= "        \$where = array();\n";
        foreach ( $table['keys'] as $key )
            $php .= "        \$where[] = '" . $key . " = ' . " . $connection . "::quote( \$" . $key . " );\n";
        $php .= "\n        \$rows = " . $connection . "::rows( 'SELECT * FROM ' . self::TABLE . ' WHERE ' . implode( ' AND ', \$where ) );\n\n";
        $php .= "        return count( \$rows ) ? new " . $class . "( \$rows[0] ) : null;\n    }\n\n";

        $php .= "    /**\n     * A page of rows.\n     *\n";
        $php .= "     * The sort column is checked against the ones this class knows, so a value\n";
        $php .= "     * from a request cannot become part of the query.\n     *\n";
        $php .= "     * @return array of " . $class . "\n     */\n";
        $php .= "    static function fetchList( \$conditions = null, \$offset = false, \$limit = false, \$sorts = null )\n    {\n";
        $php .= "        \$sql = 'SELECT * FROM ' . self::TABLE;\n\n";
        $php .= "        if ( is_array( \$conditions ) && count( \$conditions ) )\n        {\n";
        $php .= "            \$where = array();\n";
        $php .= "            foreach ( \$conditions as \$column => \$value )\n";
        $php .= "                if ( in_array( \$column, self::columns(), true ) )\n";
        $php .= "                    \$where[] = \$column . ' = ' . " . $connection . "::quote( \$value );\n\n";
        $php .= "            if ( count( \$where ) )\n                \$sql .= ' WHERE ' . implode( ' AND ', \$where );\n";
        $php .= "        }\n\n";
        $php .= "        if ( is_array( \$sorts ) && count( \$sorts ) )\n        {\n";
        $php .= "            \$order = array();\n";
        $php .= "            foreach ( \$sorts as \$column => \$direction )\n";
        $php .= "                if ( in_array( \$column, self::columns(), true ) )\n";
        $php .= "                    \$order[] = \$column . ( strtolower( (string) \$direction ) === 'desc' ? ' DESC' : ' ASC' );\n\n";
        $php .= "            if ( count( \$order ) )\n                \$sql .= ' ORDER BY ' . implode( ', ', \$order );\n";
        $php .= "        }\n\n";
        $php .= "        if ( \$limit !== false && \$limit !== null )\n";
        $php .= "            \$sql .= ' LIMIT ' . (int) \$limit . ' OFFSET ' . max( 0, (int) \$offset );\n\n";
        $php .= "        \$objects = array();\n";
        $php .= "        foreach ( " . $connection . "::rows( \$sql ) as \$row )\n";
        $php .= "            \$objects[] = new " . $class . "( \$row );\n\n";
        $php .= "        return \$objects;\n    }\n\n";

        $php .= "    static function fetchListCount( \$conditions = null )\n    {\n";
        $php .= "        \$rows = " . $connection . "::rows( 'SELECT COUNT(*) AS row_count FROM ' . self::TABLE );\n\n";
        $php .= "        return count( \$rows ) && isset( \$rows[0]['row_count'] ) ? (int) \$rows[0]['row_count'] : 0;\n    }\n\n";

        $php .= "    /**\n     * Writes this row back.\n     *\n";
        $php .= "     * Left to be written: this reads a database somebody else owns, and a\n";
        $php .= "     * wizard that writes to one by default is a wizard that damages one by\n";
        $php .= "     * accident. Fill it in when you know the table should be written to.\n     */\n";
        $php .= "    function store()\n    {\n";
        $php .= "        eZDebug::writeWarning( 'Writing to " . $table['name'] . " is not implemented; "
              . "this extension reads an external database.', __METHOD__ );\n    }\n\n";
        $php .= "    function removeThis()\n    {\n";
        $php .= "        eZDebug::writeWarning( 'Removing from " . $table['name'] . " is not implemented; "
              . "this extension reads an external database.', __METHOD__ );\n    }\n}\n";

        return $php;
    }
}
