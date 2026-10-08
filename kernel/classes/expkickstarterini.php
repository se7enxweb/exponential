<?php
/**
 * @description Interactive kickstart.ini generator. Copies kickstart.ini-dist to kickstart.ini and populates all sections via modern CLI form input.
 * @package   kernel
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 */

if ( !function_exists( 'readline' ) )
{
    function readline( $prompt = '' )
    {
        echo $prompt;
        $line = fgets( STDIN );
        return $line === false ? false : rtrim( $line, "\r\n" );
    }
}

class expKickstarterIni
{
    private $cli;
    private $rootDir;
    private $distFile;
    private $iniFile;
    private $sections = array();
    private $defaults = false;
    private $yes = false;
    private $fromInstalled = false;
    private $tty = false;
    private $width = 68;
    private $argv = array();

    private $choiceOptions = array(
        'database_choice' => array(
            'Type' => array(
                'sqlite3' => 'SQLite 3 (recommended, no database server needed)',
                'mysqli'  => 'MySQL (mysqli)',
                'pgsql'   => 'PostgreSQL (pgsql)',
                'mongodb' => 'MongoDB',
                'oci8'    => 'Oracle (oci8, needs the ezoracle extension)',
            ),
        ),
        'email_settings' => array(
            'Type' => array(
                'mta'  => 'MTA / Sendmail',
                'smtp' => 'SMTP server',
            ),
        ),
        'site_access' => array(
            'Access' => array(
                'url'      => 'URL path (e.g. /news)',
                'port'     => 'TCP port',
                'hostname' => 'Hostname',
            ),
        ),
        'site_details' => array(
            'DatabaseAction' => array(
                'ignore' => 'ignore  (install into the database as it is; drops nothing: for a new, empty database)',
                'remove' => 'remove  (EMPTY the database first, then install: destroys what is in it)',
                'skip'   => 'skip    (install nothing; only write the settings)',
            ),
        ),
    );

    /**
     * The values to check in every written file before a run: section, key,
     * what to check. Written as a list in the header and as a "# REVIEW"
     * line above each key (eZINI skips lines starting with #).
     */
    private static $reviewNotes = array(
        array( 'database_init', 'Database', 'the database to install into; SQLite: a file name in var/storage/sqlite3/ ending in .db, a server: a database you created' ),
        array( 'site_details', 'Database', 'the same value as [database_init] Database' ),
        array( 'site_details', 'DatabaseAction', 'ignore installs into an empty database and drops nothing; remove EMPTIES the database first; skip installs nothing' ),
        array( 'site_details', 'URL', 'the address of the site; empty means http://localhost' ),
        array( 'site_admin', 'Email', 'the administrator\'s real address' ),
        array( 'site_admin', 'Password', 'empty: a random password is generated, printed once and written to var/log/initial-admin-password' ),
    );

    private $fieldDefaults = array(
        'database_choice' => array( 'Type' => 'sqlite3' ),
        'database_init'   => array(
            'Server'   => 'localhost',
            'Port'     => '',
            'Database' => 'exponential.db', // set for the default Type in the constructor
            'User'     => 'root',
            'Password' => '',
            'Socket'   => '',
        ),
        'language_options' => array(
            'Primary'    => 'eng-US', // the language the bundled data is in, as in the web wizard
            'Languages'  => array(),
        ),
        'site_types' => array(
            // a package that is in var/storage/packages (7x/sevenx_multisite), as exp:install installs
            'Site_package' => 'sevenx_multisite',
        ),
        'site_access' => array(
            'Access' => 'url',
        ),
        'site_details' => array(
            'Title'               => 'My Exponential Site',
            'URL'                 => '',
            'Access'              => 'sevenx_site_user',
            'AdminAccess'         => 'sevenx_site_admin',
            'AccessPort'          => '8080',
            'AdminAccessPort'     => '8081',
            'AccessHostname'      => 'sevenx-site.test.com',
            'AdminAccessHostname' => 'sevenx-site-admin.test.com',
            // the editor siteaccess, the administration for content editing only, is always installed
            'EditorAccess'         => 'editor',
            'EditorAccessPort'     => '8082',
            'EditorAccessHostname' => 'edit.sevenx-site.test.com',
            'Database'            => 'exponential.db', // set for the default Type in the constructor
            // installs schema, data and package into an empty database and drops nothing;
            // remove (empty the database first) is only ever written when chosen
            'DatabaseAction'      => 'ignore',
        ),
        'site_admin' => array(
            'FirstName' => 'Admin',
            'LastName'  => 'User',
            'Email'     => 'admin@example.com',
            'Password'  => '', // empty: setup generates a random one
        ),
        'registration' => array(
            'Comments' => '',
            'Send'     => 'false',
        ),
        'security' => array(),
    );

    public function __construct( $rootDir, $distFile, $iniFile, $argv = null )
    {
        $this->cli      = eZCLI::instance();
        $this->rootDir  = $rootDir;
        $this->distFile = $distFile;
        $this->iniFile  = $iniFile;
        $this->tty      = function_exists( 'posix_isatty' ) && posix_isatty( STDIN );

        if ( $argv === null )
        {
            $argv = isset( $GLOBALS['argv'] ) ? $GLOBALS['argv'] : array();
        }

        // Strip the script name from the argument list if it is present.
        if ( isset( $argv[0] ) && strpos( $argv[0], 'kickstarter' ) !== false )
        {
            $argv = array_slice( $argv, 1 );
        }
        $this->argv = $argv;

        $this->parseArgs();
        $databaseName = self::defaultDatabaseName( $this->fieldDefaults['database_choice']['Type'] );
        $this->fieldDefaults['database_init']['Database'] = $databaseName;
        $this->fieldDefaults['site_details']['Database']  = $databaseName;
        // Only on request: the settings of an installed siteaccess point at a
        // live database, and a kickstart.ini written from them would install over it
        if ( $this->fromInstalled )
            $this->loadSiteDefaults();
        if ( !$this->defaults && !$this->yes && !$this->tty )
        {
            $this->error( 'No TTY detected. Run with --defaults or --yes for non-interactive mode.' );
            exit( 1 );
        }
    }

    /**
     * The default of a field, as --yes would write it.
     *
     * @param string $section
     * @param string $key
     * @return mixed null when there is none
     */
    public function fieldDefault( $section, $key )
    {
        return isset( $this->fieldDefaults[$section][$key] ) ? $this->fieldDefaults[$section][$key] : null;
    }

    /**
     * A database name the given database type accepts, as exp:install uses:
     * SQLite needs a file name ending in .db, .db3, .sqlite or .sqlite3 (kept
     * in var/storage/sqlite3/), Oracle a connect string, the others a name.
     *
     * @param string $type the [database_choice] Type, aliases allowed
     * @return string
     */
    public static function defaultDatabaseName( $type )
    {
        switch ( strtolower( trim( (string)$type ) ) )
        {
            case 'sqlite3':
            case 'sqlite':
                return 'exponential.db';
            case 'oci8':
            case 'oracle':
            case 'ezoracle':
                return 'localhost:1521/FREEPDB1';
            default:
                return 'exponential';
        }
    }

    /**
     * After the database type was changed interactively: a Database that is
     * empty or still the default of a type follows the new type. A name the
     * user typed is kept.
     *
     * @param string $type
     */
    private function followDatabaseType( $type )
    {
        $defaults = array( '', 'ezp' );
        foreach ( array( 'sqlite3', 'mysqli', 'oci8' ) as $knownType )
            $defaults[] = self::defaultDatabaseName( $knownType );
        foreach ( array( 'database_init', 'site_details' ) as $sectionName )
        {
            if ( !isset( $this->sections[$sectionName] ) )
                continue;
            foreach ( $this->sections[$sectionName]['fields'] as $idx => $field )
            {
                if ( $field['key'] === 'Database' && in_array( $field['value'], $defaults, true ) )
                    $this->sections[$sectionName]['fields'][$idx]['value'] = self::defaultDatabaseName( $type );
            }
        }
    }

    private function loadSiteDefaults()
    {
        // With --from-installed: the server, database and user of the first
        // installed siteaccess that names a database. Never its password: it is
        // asked for, or entered in kickstart.ini by hand.
        $siteAccessDirs = glob( $this->rootDir . '/settings/siteaccess/*', GLOB_ONLYDIR );
        if ( !is_array( $siteAccessDirs ) )
            return;

        foreach ( $siteAccessDirs as $siteAccessDir )
        {
            // eZINI reads relative to the installation root, which is
            // $this->rootDir for every real run
            $installationRoot = rtrim( (string)realpath( dirname( __DIR__, 2 ) ), '/' ) . '/';
            $real = (string)realpath( $siteAccessDir );
            if ( strpos( $real, $installationRoot ) !== 0 )
                continue;
            $ini = @eZINI::instance( 'site.ini', substr( $real, strlen( $installationRoot ) ), null, null, null, true );
            if ( !$ini || !is_object( $ini ) )
                continue;

            $database = $ini->variable( 'DatabaseSettings', 'Database' );
            $user     = $ini->variable( 'DatabaseSettings', 'User' );
            $server   = $ini->variable( 'DatabaseSettings', 'Server' );

            if ( !$database || !$user )
                continue;

            if ( $server )  $this->fieldDefaults['database_init']['Server']   = $server;
            if ( $database ) $this->fieldDefaults['database_init']['Database'] = $database;
            if ( $user )    $this->fieldDefaults['database_init']['User']     = $user;

            if ( $database ) $this->fieldDefaults['site_details']['Database'] = $database;
            break;
        }
    }

    public function run()
    {
        if ( !file_exists( $this->distFile ) )
        {
            $this->error( 'kickstart.ini-dist not found in project root.' );
            exit( 1 );
        }

        if ( $this->defaults || $this->yes )
        {
            $this->writeNonInteractive();
            $this->cli->output( 'Wrote ' . $this->rel( $this->iniFile ) . ( $this->yes ? ' with sensible defaults.' : ' with defaults from kickstart.ini-dist.' ) );
            $this->cli->output( 'Review the lines marked "# REVIEW" before "exp:kickstarter run --dry-run".' );
            exit( 0 );
        }

        $this->loadSections();
        $this->banner();
        $this->mainLoop();
        exit( 0 );
    }

    /**
     * What --yes and --defaults do, without the exit: read kickstart.ini-dist
     * (and an existing kickstart.ini), apply the built-in defaults with --yes,
     * and write the file.
     *
     * @return string the file written
     */
    public function writeNonInteractive()
    {
        $this->loadSections();
        if ( $this->yes )
            $this->applyHardDefaults();
        $this->writeIni();
        return $this->iniFile;
    }

    private function parseArgs()
    {
        foreach ( $this->argv as $arg )
        {
            if ( $arg === '--defaults' || $arg === '-d' )
                $this->defaults = true;
            elseif ( $arg === '--yes' || $arg === '-y' )
                $this->yes = true;
            elseif ( $arg === '--from-installed' )
                $this->fromInstalled = true;
            elseif ( $arg === '--help' || $arg === '-h' )
            {
                $this->showHelp();
                exit( 0 );
            }
        }
    }

    private function showHelp()
    {
        $this->cli->output( 'Usage: ./bin/php/console exp:kickstarter ini [options]' );
        $this->cli->output( '       ./bin/php/kickstarter.php ini [options]' );
        $this->cli->output( '' );
        $this->cli->output( 'Interactive generator for kickstart.ini.' );
        $this->cli->output( 'Copies kickstart.ini-dist to kickstart.ini and lets you populate every section.' );
        $this->cli->output( '' );
        $this->cli->output( 'Options:' );
        $this->cli->output( '  --defaults, -d  Copy kickstart.ini-dist values to kickstart.ini and exit' );
        $this->cli->output( '  --yes, -y       Accept sensible defaults and write kickstart.ini without prompting' );
        $this->cli->output( '  --from-installed  Take the database server, name and user (never the password) from the' );
        $this->cli->output( '                  first installed siteaccess as defaults. Off by default: those settings' );
        $this->cli->output( '                  name a live database, and a run with DatabaseAction=remove empties it' );
        $this->cli->output( '  --help, -h      Show this help' );
    }

    private function loadSections()
    {
        $this->sections = $this->parseDist();
        if ( file_exists( $this->iniFile ) )
        {
            $existing = $this->parseIniFile( $this->iniFile, false );
            $this->mergeSections( $existing );
        }
    }

    private function parseDist()
    {
        return $this->parseIniFile( $this->distFile, true );
    }

    private function parseIniFile( $file, $isCommented )
    {
        $sections = array();
        $current  = null;
        $comments = array();

        foreach ( file( $file ) as $rawLine )
        {
            $line    = rtrim( $rawLine, "\r\n" );
            $trimmed = ltrim( $line );

            if ( $trimmed === '' )
            {
                $comments = array();
                continue;
            }

            $section = $this->matchSection( $trimmed, $isCommented );
            if ( $section !== null )
            {
                $current = $section;
                if ( !isset( $sections[$current] ) )
                    $sections[$current] = array( 'fields' => array() );
                $comments = array();
                continue;
            }

            if ( $isCommented && $trimmed[0] === '#' )
            {
                if ( preg_match( '/^##\s*(.*)$/', $trimmed, $m ) )
                {
                    $comments[] = $m[1];
                    continue;
                }

                if ( preg_match( '/^#([A-Za-z0-9_]+)(\[\])?\s*(?:=\s*(.*))?$/', $trimmed, $m ) )
                {
                    $key   = $m[1] . ( !empty( $m[2] ) ? '[]' : '' );
                    $value = isset( $m[3] ) ? $m[3] : '';
                    $this->addField( $sections, $current, $key, $value, $comments );
                    $comments = array();
                }
                continue;
            }

            if ( preg_match( '/^([A-Za-z0-9_]+)(\[\])?\s*(?:=\s*(.*))?$/', $trimmed, $m ) )
            {
                $key   = $m[1] . ( !empty( $m[2] ) ? '[]' : '' );
                $value = isset( $m[3] ) ? $m[3] : '';
                $this->addField( $sections, $current, $key, $value, $comments );
                $comments = array();
            }
        }

        return $sections;
    }

    private function matchSection( $trimmed, $isCommented )
    {
        $pattern = $isCommented ? '/^#\[([A-Za-z0-9_]+)\]\s*$/' : '/^\[([A-Za-z0-9_]+)\]\s*$/';
        if ( preg_match( $pattern, $trimmed, $m ) )
            return $m[1];
        return null;
    }

    private function addField( &$sections, $section, $key, $value, $comments )
    {
        if ( $section === null || !isset( $sections[$section] ) )
            return;

        $type        = $this->inferType( $section, $key );
        $description = $this->extractDescription( $comments, $key );

        // Merge duplicate fields into a single entry and prefer dist
        foreach ( $sections[$section]['fields'] as $idx => $field )
        {
            if ( $field['key'] === $key )
            {
                if ( $type === 'array' )
                {
                    if ( $value !== '' )
                        $sections[$section]['fields'][$idx]['values'][] = $value;
                }
                else
                {
                    $sections[$section]['fields'][$idx]['value'] = $value;
                }
                return;
            }
        }

        if ( $type === 'array' )
        {
            $sections[$section]['fields'][] = array(
                'key'         => $key,
                'values'      => $value !== '' ? array( $value ) : array(),
                'type'        => $type,
                'description' => $description,
            );
        }
        else
        {
            $sections[$section]['fields'][] = array(
                'key'         => $key,
                'value'       => $value,
                'type'        => $type,
                'description' => $description,
            );
        }
    }

    private function mergeSections( $existing )
    {
        foreach ( $existing as $sectionName => $section )
        {
            if ( !isset( $this->sections[$sectionName] ) )
                $this->sections[$sectionName] = array( 'fields' => array() );

            foreach ( $section['fields'] as $field )
            {
                $found = false;
                foreach ( $this->sections[$sectionName]['fields'] as $idx => $existingField )
                {
                    if ( $existingField['key'] === $field['key'] )
                    {
                        if ( $field['type'] === 'array' )
                        {
                            if ( !empty( $field['values'] ) )
                                $this->sections[$sectionName]['fields'][$idx]['values'] = $field['values'];
                        }
                        else
                        {
                            $this->sections[$sectionName]['fields'][$idx]['value'] = $field['value'];
                        }
                        $found = true;
                        break;
                    }
                }
                if ( !$found )
                {
                    $this->sections[$sectionName]['fields'][] = $field;
                }
            }
        }
    }

    private function inferType( $section, $key )
    {
        $base = rtrim( $key, '[]' );
        if ( $base === 'Continue' || $base === 'Send' )
            return 'bool';
        if ( $base === 'Type' && $section === 'database_choice' )
            return 'choice';
        if ( $base === 'Type' && $section === 'email_settings' )
            return 'choice';
        if ( $base === 'Access' && $section === 'site_access' )
            return 'choice';
        if ( $base === 'DatabaseAction' && $section === 'site_details' )
            return 'choice';
        if ( in_array( $base, array( 'Port', 'AccessPort', 'AdminAccessPort', 'EditorAccessPort' ) ) )
            return 'int';
        if ( $base === 'Password' )
            return 'hidden';
        if ( substr( $key, -2 ) === '[]' )
            return 'array';
        return 'string';
    }

    private function extractDescription( $comments, $key )
    {
        $desc = '';
        foreach ( $comments as $c )
        {
            if ( !preg_match( '/^' . preg_quote( $key, '/' ) . '=<[^>]+>$/', $c ) )
            {
                $desc = $c;
            }
        }
        return $desc;
    }

    private function applyHardDefaults()
    {
        foreach ( $this->sections as $sectionName => &$section )
        {
            foreach ( $section['fields'] as $idx => $field )
            {
                $base = rtrim( $field['key'], '[]' );
                if ( isset( $this->fieldDefaults[$sectionName][$base] ) )
                {
                    if ( $field['type'] === 'array' )
                    {
                        if ( is_array( $this->fieldDefaults[$sectionName][$base] ) )
                            $section['fields'][$idx]['values'] = $this->fieldDefaults[$sectionName][$base];
                    }
                    else
                    {
                        $section['fields'][$idx]['value'] = (string)$this->fieldDefaults[$sectionName][$base];
                    }
                }
                elseif ( $field['type'] === 'bool' && $field['value'] === '' )
                {
                    $section['fields'][$idx]['value'] = 'true';
                }
            }
        }
        unset( $section );
    }

    private function writeIni()
    {
        $content = "# Kickstart configuration generated by exp:kickstarter ini\n";
        $content .= "# Edit with ./bin/php/console exp:kickstarter ini; every key is described in kickstart.ini-dist.\n";
        $content .= "# This file holds passwords: it is written with mode 0600; keep it so and never commit it.\n";
        $content .= "#\n";
        $content .= "# REVIEW before \"exp:kickstarter run --dry-run\" (each line is marked \"# REVIEW\" below):\n";
        foreach ( self::$reviewNotes as $note )
            $content .= "#  - [" . $note[0] . "] " . $note[1] . ": " . $note[2] . "\n";

        foreach ( $this->sections as $sectionName => $section )
        {
            $content .= "\n[$sectionName]\n";
            foreach ( $section['fields'] as $field )
            {
                foreach ( self::$reviewNotes as $note )
                {
                    if ( $note[0] === $sectionName && $note[1] === $field['key'] )
                        $content .= "# REVIEW: " . $note[2] . "\n";
                }
                if ( $field['type'] === 'array' )
                {
                    foreach ( $field['values'] as $v )
                    {
                        $content .= $field['key'] . '=' . $v . "\n";
                    }
                    if ( empty( $field['values'] ) )
                    {
                        $content .= $field['key'] . "\n";
                    }
                }
                else
                {
                    $content .= $field['key'] . '=' . $field['value'] . "\n";
                }
            }
        }

        // The file holds the database and administrator passwords: owner only,
        // also when it existed before with a wider mode
        $oldUmask = umask( eZFile::creationUmask( 0077 ) );
        $written = file_put_contents( $this->iniFile, $content );
        umask( $oldUmask );
        if ( $written === false || !chmod( $this->iniFile, eZFile::fileMode( 0600 ) ) )
        {
            $this->error( 'Failed to write ' . $this->rel( $this->iniFile ) . ' with mode 0600' );
            exit( 1 );
        }

        $this->clearKickstartCache();
    }

    private function mainLoop()
    {
        $sectionKeys = array_keys( $this->sections );
        $current     = 0;

        while ( true )
        {
            $this->clearScreen();
            $this->banner();
            $this->listSections( $current );
            $this->cli->output( '' );
            $this->cli->output( '  [n]ext  [p]revious  [s]ave  [v]iew  [w]izard  [q]uit & save  [?] help' );
            $choice = $this->prompt( 'Choose a section number or command: ' );

            if ( $choice === 'q' )
            {
                $this->writeIni();
                $this->cli->output( '' );
                $this->cli->output( 'Saved ' . $this->rel( $this->iniFile ) );
                break;
            }
            elseif ( $choice === 's' )
            {
                $this->writeIni();
                $this->cli->output( 'Saved.' );
                $this->pause();
            }
            elseif ( $choice === 'v' )
            {
                $this->preview();
                $this->pause();
            }
            elseif ( $choice === 'w' )
            {
                $this->wizardMode();
                $current = 0;
            }
            elseif ( $choice === 'n' )
            {
                $current = min( $current + 1, count( $sectionKeys ) - 1 );
            }
            elseif ( $choice === 'p' )
            {
                $current = max( $current - 1, 0 );
            }
            elseif ( $choice === '?' )
            {
                $this->helpScreen();
                $this->pause();
            }
            elseif ( ctype_digit( $choice ) )
            {
                $idx = (int)$choice - 1;
                if ( isset( $sectionKeys[$idx] ) )
                {
                    $nav = $this->editSection( $sectionKeys[$idx] );
                    $current = $idx;
                    if ( $nav === 'n' )
                    {
                        $current = min( $current + 1, count( $sectionKeys ) - 1 );
                    }
                    elseif ( $nav === 'p' )
                    {
                        $current = max( $current - 1, 0 );
                    }
                }
            }
        }
    }

    private function listSections( $active = null )
    {
        $this->cli->output( 'Sections:' );
        $this->hr();
        $i = 1;
        foreach ( $this->sections as $sectionName => $section )
        {
            $summary = $this->sectionSummary( $sectionName, $section );
            $marker  = ( $i - 1 === $active ) ? ' ▶ ' : '   ';
            $this->cli->output( sprintf( '%s[%2d] %-20s %s', $marker, $i, $sectionName, $summary ) );
            $i++;
        }
    }

    private function sectionSummary( $sectionName, $section )
    {
        $parts = array();
        foreach ( $section['fields'] as $field )
        {
            $base = rtrim( $field['key'], '[]' );
            if ( $base === 'Continue' )
            {
                $continue = $this->formatValue( $field );
                continue;
            }
            if ( $base === 'Type' || $base === 'Access' || $base === 'DatabaseAction' || $base === 'Send' || $base === 'Site_package' )
            {
                $parts[] = $base . ': ' . $this->formatValue( $field );
            }
        }

        $continue = isset( $continue ) ? $continue : 'true';
        return 'Continue=' . $continue . ( $parts ? ' | ' . implode( ' | ', $parts ) : '' );
    }

    private function formatValue( $field )
    {
        if ( $field['type'] === 'array' )
            return implode( ',', $field['values'] );
        return $field['value'];
    }

    private function editSection( $sectionName )
    {
        $section = &$this->sections[$sectionName];
        while ( true )
        {
            $this->clearScreen();
            $this->cli->output( $this->cli->stylize( 'emphasize', $sectionName ) );
            $this->hr();
            $i = 1;
            foreach ( $section['fields'] as $field )
            {
                $label = $this->fieldLabel( $field );
                $val   = $this->displayValue( $field );
                $this->cli->output( sprintf( ' [%2d] %-18s %s', $i, $label, $val ) );
                if ( $field['description'] !== '' )
                {
                    $this->cli->output( '      ' . $this->cli->stylize( 'dim', $field['description'] ) );
                }
                $i++;
            }
            $this->cli->output( '' );
            $this->cli->output( '  [n]ext  [p]revious  [m]ain menu  [s]ave  [q]uit & save  [?] help' );
            $choice = $this->prompt( 'Select a field number or command: ' );

            if ( $choice === 'q' )
            {
                $this->writeIni();
                $this->cli->output( '' );
                $this->cli->output( 'Saved and exiting.' );
                exit( 0 );
            }
            elseif ( $choice === 's' )
            {
                $this->writeIni();
                $this->cli->output( 'Saved.' );
                $this->pause();
            }
            elseif ( $choice === 'm' || $choice === 'n' || $choice === 'p' )
            {
                return $choice;
            }
            elseif ( $choice === '?' )
            {
                $this->helpScreen();
                $this->pause();
            }
            elseif ( ctype_digit( $choice ) )
            {
                $idx = (int)$choice - 1;
                if ( isset( $section['fields'][$idx] ) )
                {
                    $this->editField( $sectionName, $section['fields'][$idx] );
                }
            }
        }
    }

    private function editField( $sectionName, &$field )
    {
        $base = rtrim( $field['key'], '[]' );
        $this->cli->output( '' );
        if ( $field['description'] !== '' )
            $this->cli->output( $this->cli->stylize( 'dim', $field['description'] ) );

        if ( $field['type'] === 'bool' )
        {
            $current = $field['value'] === 'true' || $field['value'] === '1' ? true : false;
            $answer = $this->confirm( $base, $current );
            $field['value'] = $answer ? 'true' : 'false';
        }
        elseif ( $field['type'] === 'choice' )
        {
            $field['value'] = $this->choose( $sectionName, $base, $field['value'] );
            if ( $sectionName === 'database_choice' && $base === 'Type' )
                $this->followDatabaseType( $field['value'] );
        }
        elseif ( $field['type'] === 'int' )
        {
            $field['value'] = $this->askInt( $base, $field['value'] );
        }
        elseif ( $field['type'] === 'hidden' )
        {
            $field['value'] = $this->askHidden( $base, $field['value'] );
        }
        elseif ( $field['type'] === 'array' )
        {
            $field['values'] = $this->askArray( $base, $field['values'] );
        }
        else
        {
            $field['value'] = $this->ask( $base, $field['value'] );
        }
    }

    private function ask( $label, $default )
    {
        $prompt = $this->promptText( $label, $default );
        $answer = readline( $prompt );
        return $answer !== '' ? $answer : $default;
    }

    private function askInt( $label, $default )
    {
        while ( true )
        {
            $prompt = $this->promptText( $label, $default );
            $answer = readline( $prompt );
            if ( $answer === '' )
                return $default;
            if ( ctype_digit( $answer ) )
                return $answer;
            $this->cli->output( $this->cli->stylize( 'warning', 'Please enter a number.' ) );
        }
    }

    private function askHidden( $label, $default )
    {
        $prompt = $this->promptText( $label, $default !== '' ? '******' : '' );
        $this->cli->output( $prompt, false );
        if ( defined( 'STDOUT' ) )
            fflush( STDOUT );
        $this->disableEcho();
        $answer = fgets( STDIN );
        $this->enableEcho();
        $this->cli->output( '' );
        $answer = $answer === false ? '' : rtrim( $answer, "\r\n" );
        return $answer !== '' ? $answer : $default;
    }

    private function confirm( $label, $default )
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        $answer = readline( $label . ' [' . $defaultText . ']: ' );
        if ( $answer === '' )
            return $default;
        $answer = strtolower( $answer );
        return in_array( $answer, array( 'y', 'yes', 'true', '1' ) );
    }

    private function choose( $sectionName, $label, $default )
    {
        $base = rtrim( $label, '[]' );
        if ( !isset( $this->choiceOptions[$sectionName][$base] ) )
            return $this->ask( $label, $default );

        $choices = $this->choiceOptions[$sectionName][$base];
        $this->cli->output( 'Available options:' );
        $i = 1;
        foreach ( $choices as $val => $text )
        {
            $marker = ( $val === $default ) ? ' (default)' : '';
            $this->cli->output( '  ' . $i . '. ' . $text . ' [' . $val . ']' . $marker );
            $i++;
        }
        $answer = readline( $label . ' [' . $default . ']: ' );
        if ( $answer === '' )
            return $default;
        if ( isset( $choices[$answer] ) )
            return $answer;
        if ( ctype_digit( $answer ) )
        {
            $idx = (int)$answer - 1;
            $keys = array_keys( $choices );
            if ( isset( $keys[$idx] ) )
                return $keys[$idx];
        }
        $this->cli->output( $this->cli->stylize( 'warning', 'Unknown choice, keeping default.' ) );
        return $default;
    }

    private function askArray( $label, $values )
    {
        $current = implode( ',', $values );
        $this->cli->output( 'Current values: ' . ( $current !== '' ? $current : '(none)' ) );
        $this->cli->output( 'Enter comma-separated values, +value to add one, - to clear, or empty to keep.' );
        $answer = readline( $label . ': ' );
        if ( $answer === '' )
            return $values;
        if ( $answer === '-' )
            return array();
        if ( $answer[0] === '+' )
            return array_merge( $values, array( substr( $answer, 1 ) ) );
        return array_filter( array_map( 'trim', explode( ',', $answer ) ), 'strlen' );
    }

    private function promptText( $label, $default )
    {
        return $label . ' [' . $default . ']: ';
    }

    private function prompt( $label, $default = '' )
    {
        if ( $default !== '' )
            $text = $label . ' [' . $default . ']: ';
        else
            $text = $label . ': ';
        $answer = readline( $text );
        return $answer !== '' ? $answer : $default;
    }

    private function displayValue( $field )
    {
        if ( $field['type'] === 'bool' )
            return $this->cli->stylize( 'emphasize', $field['value'] );
        if ( $field['type'] === 'hidden' )
            return $field['value'] !== '' ? '******' : '';
        if ( $field['type'] === 'array' )
            return empty( $field['values'] ) ? '(none)' : implode( ',', $field['values'] );
        $v = $field['value'];
        return $v !== '' ? $v : '';
    }

    private function fieldLabel( $field )
    {
        $base = rtrim( $field['key'], '[]' );
        if ( $field['type'] === 'choice' )
            return $base . ' (choice)';
        if ( $field['type'] === 'bool' )
            return $base . ' (bool)';
        if ( $field['type'] === 'int' )
            return $base . ' (int)';
        if ( $field['type'] === 'hidden' )
            return $base . ' (secret)';
        if ( $field['type'] === 'array' )
            return $base . '[]';
        return $base;
    }

    private function wizardMode()
    {
        $sectionKeys = array_keys( $this->sections );
        foreach ( $sectionKeys as $sectionName )
        {
            $this->editSectionWizard( $sectionName );
        }
        $this->writeIni();
        $this->cli->output( '' );
        $this->cli->output( 'Wizard complete. Saved ' . $this->rel( $this->iniFile ) );
    }

    private function editSectionWizard( $sectionName )
    {
        $section = &$this->sections[$sectionName];
        $this->clearScreen();
        $this->cli->output( $this->cli->stylize( 'emphasize', 'Wizard: ' . $sectionName ) );
        $this->hr();
        foreach ( $section['fields'] as &$field )
        {
            $this->editField( $sectionName, $field );
        }
        unset( $field );
    }

    private function preview()
    {
        $this->clearScreen();
        $this->cli->output( 'Preview of ' . $this->rel( $this->iniFile ) . ':' );
        $this->hr();
        $temp = $this->iniFile . '.preview';
        $this->writeIni();
        $content = file_get_contents( $this->iniFile );
        $this->cli->output( $content );
    }

    private function helpScreen()
    {
        $this->clearScreen();
        $this->cli->output( 'Help' );
        $this->hr();
        $this->cli->output( 'Navigate with section numbers, n/p, and edit fields by number.' );
        $this->cli->output( 'q saves and exits, s saves without exiting, m returns to the main menu.' );
        $this->cli->output( 'v previews the current INI contents, w runs a sequential wizard.' );
        $this->cli->output( 'For arrays, enter comma-separated values or +value to append.' );
    }

    private function banner()
    {
        $this->hr( '=' );
        $this->cli->output( '  ' . $this->cli->stylize( 'emphasize', 'kickstarter' ) );
        $this->cli->output( '  Generate kickstart.ini from kickstart.ini-dist' );
        $this->hr( '=' );
    }

    private function hr( $char = '-' )
    {
        $this->cli->output( str_repeat( $char, $this->width ) );
    }

    private function clearScreen()
    {
        if ( $this->tty && !getenv( 'NO_CLEAR' ) )
        {
            $this->cli->output( "\033[2J\033[H", false );
        }
    }

    private function pause()
    {
        readline( 'Press Enter to continue...' );
    }

    private function disableEcho()
    {
        if ( function_exists( 'shell_exec' ) )
        {
            shell_exec( 'stty -echo' );
        }
    }

    private function enableEcho()
    {
        if ( function_exists( 'shell_exec' ) )
        {
            shell_exec( 'stty echo' );
        }
    }

    private function clearKickstartCache()
    {
        // config.php disables EZP_INI_FILEMTIME_CHECK, so stale eZINI kickstart
        // caches will not be invalidated. Delete them explicitly after writing
        // kickstart.ini.
        $cacheDir = isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] )
            ? $GLOBALS['eZINI_CONFIG_CACHE_DIR']
            : $this->rootDir . '/var/cache/ini/';

        foreach ( glob( $cacheDir . 'kickstart-*.php' ) ?: array() as $file )
        {
            @unlink( $file );
        }
    }

    private function error( $message )
    {
        $this->cli->output( $this->cli->stylize( 'error', 'ERROR: ' . $message ) );
    }

    private function rel( $path )
    {
        return str_replace( $this->rootDir . '/', '', $path );
    }
}
