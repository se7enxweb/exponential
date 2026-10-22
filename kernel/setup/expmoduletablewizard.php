<?php
/**
 * File containing the expModuleTableWizard class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';
require_once 'kernel/setup/expmoduletablecode.php';

if ( !class_exists( 'expModuleTableWizard', false ) ) {
/**
 * The module table wizard (Setup > RAD > Module table, setup/moduletable; doc/eng-us/features/6.0/data-tables.md,
 * "The module table wizard"): a NEW data table and everything around it, from a list of fields, a content class, an
 * existing table or the fields of a source an extension offers (expDataTableFieldSource, e.g. an import of 7x SQLI
 * Import):
 *
 *   - the eZPersistentObject class, on the site's database or a named connection, with its relations to content;
 *   - share/db_schema.dba and the install SQL of MySQL, PostgreSQL, SQLite, Oracle and SQL Server;
 *   - a module with list, edit, remove and a REST/JSON endpoint, and the policies read and edit;
 *   - the admin templates (admin4 components; the same files serve design/admin);
 *   - template fetch functions;
 *   - the settings (module, design, the Setup menu's Data tables group).
 *
 * Written as a new extension, or added to an existing one (files that are there are left alone and named; settings
 * files get the wizard's block appended once). Then, from the same page: create the table on its connection, activate
 * the extension, regenerate the autoload arrays and clear the caches; no step needs the command line.
 *
 * @since 6.0.15
 * @package kernel
 */
class expModuleTableWizard extends expExtensionWizard
{
    /** The marker of the block appended to an existing settings file */
    const MARKER = '# module table wizard:';

    public static function wizardName()
    {
        return 'module table wizard';
    }

    public static function parts()
    {
        return array(
            'class' => array( 'label' => 'Persistent class', 'description' => 'The eZPersistentObject class of the table, with its fields, connection and relations.', 'default' => true ),
            'schema' => array( 'label' => 'Schema and install SQL', 'description' => 'share/db_schema.dba and sql/<engine>/schema.sql for MySQL, PostgreSQL, SQLite, Oracle and SQL Server.', 'default' => true ),
            'module' => array( 'label' => 'Module', 'description' => 'list, edit, remove and the REST/JSON endpoint json, with the policies read and edit.', 'default' => true ),
            'templates' => array( 'label' => 'Admin templates', 'description' => 'The pages of the module in the administration (admin4 components, no inline styles).', 'default' => true ),
            'fetch' => array( 'label' => 'Template fetch functions', 'description' => 'fetch( <module>, list|count|row ).', 'default' => true ),
            'menu' => array( 'label' => 'Setup menu entry', 'description' => 'A link in the Data tables group of the Setup menu, for users with the policy read.', 'default' => true ),
            'ezinfo' => array( 'label' => 'ezinfo.php and extension.xml', 'description' => 'What the about page shows (a new extension only).', 'default' => true ),
            'composer' => array( 'label' => 'composer.json', 'description' => 'To install the extension as a package (a new extension only).', 'default' => false ),
            'licence' => array( 'label' => 'LICENSE', 'description' => 'The license text (a new extension only).', 'default' => true ),
            'readme' => array( 'label' => 'README.md', 'description' => 'What was made, and how to install and use it.', 'default' => true ),
        );
    }

    /**
     * The wizard's input made safe, with the defaults.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $get = function ( $key, $default = '' ) use ( $input ) {
            return isset( $input[$key] ) && is_scalar( $input[$key] ) ? trim( (string)$input[$key] ) : $default;
        };
        $name = self::safeName( $get( 'name' ) );
        $module = self::safeName( $get( 'module' ), true );
        if ( $module === '' )
            $module = $name;
        $table = expDataTableFieldSources::columnName( $get( 'table' ) );
        if ( $table === '' && $module !== '' )
            $table = substr( $module . '_row', 0, 30 );
        $class = preg_replace( '/[^A-Za-z0-9]/', '', $get( 'class' ) );
        if ( $class === '' && $table !== '' )
            $class = lcfirst( expModuleTableCode::camel( $table ) );
        if ( $class !== '' && !preg_match( '/^[A-Za-z]/', $class ) )
            $class = 'x' . $class;
        $connection = $get( 'connection', 'site' );
        $settings = array(
            'name' => $name,
            'existing' => $name !== '' && is_dir( self::extensionPath( $name ) ),
            'module' => $module,
            'table' => $table,
            'class' => $class,
            'title' => self::text( $get( 'title' ), 120 ) ?: ( $table !== '' ? ucfirst( str_replace( '_', ' ', $table ) ) : '' ),
            'summary' => self::text( $get( 'summary' ), 250 ),
            'author' => self::text( $get( 'author' ), 120 ),
            'vendor' => self::safeName( $get( 'vendor' ), true ) ?: 'exponential',
            'version' => self::text( $get( 'version' ), 20 ) ?: '1.0.0',
            'licence' => self::licence_id( $get( 'licence' ) ),
            'connection' => $connection === 'site' || $connection === '' ? '' : ( preg_match( expDataConnections::IDENTIFIER_PATTERN, $connection ) ? $connection : '' ),
            'source' => preg_match( '/^[a-z][a-z0-9_.]{0,63}$/', $get( 'source', 'list' ) ) ? $get( 'source', 'list' ) : 'list',
            'ref' => mb_substr( $get( 'ref' ), 0, 255 ),
            'fields_text' => isset( $input['fields_text'] ) && is_string( $input['fields_text'] ) ? mb_substr( str_replace( "\r", '', $input['fields_text'] ), 0, 20000 ) : '',
            'content' => in_array( $get( 'content', 'none' ), array( 'none', 'object', 'node', 'both' ), true ) ? $get( 'content', 'none' ) : 'none',
            'stamps' => !empty( $input['stamps'] ),
            'source_label' => '',
        );
        if ( $settings['summary'] === '' && $settings['title'] !== '' )
            $settings['summary'] = 'The data table ' . $settings['title'] . ', its administration and its JSON endpoint.';
        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );
        return $settings;
    }

    /**
     * The fields of the table: from the source, plus the content relation columns and the stamps asked for.
     *
     * @param array $settings (source_label is filled in)
     * @param string[] $errors
     * @return array list of fields (expDataTableFieldSources::normalizeField())
     */
    public static function fields( array &$settings, &$errors = array() )
    {
        $raw = array();
        if ( $settings['source'] === 'list' )
        {
            $raw = expDataTableFieldSources::parseList( $settings['fields_text'], $errors );
            $settings['source_label'] = 'a list of fields';
        }
        else
        {
            $source = expDataTableFieldSources::get( $settings['source'] );
            if ( !$source )
                $errors[] = 'There is no field source ' . $settings['source'] . '.';
            else
            {
                try
                {
                    $raw = $source->dataTableFields( array( 'ref' => $settings['ref'] ) );
                    $settings['source_label'] = $source->dataTableSourceLabel( array( 'ref' => $settings['ref'] ) );
                }
                catch ( Throwable $e )
                {
                    $errors[] = $e->getMessage();
                }
            }
        }
        $fields = array();
        foreach ( $raw as $field )
        {
            $field = is_array( $field ) ? expDataTableFieldSources::normalizeField( $field ) : null;
            if ( $field === null || $field['name'] === 'id' || isset( $fields[$field['name']] ) )
                continue;
            $fields[$field['name']] = $field;
        }
        if ( in_array( $settings['content'], array( 'object', 'both' ), true ) && !isset( $fields['contentobject_id'] ) )
            $fields['contentobject_id'] = expDataTableFieldSources::normalizeField( array( 'name' => 'contentobject_id', 'label' => 'Content object', 'type' => 'object' ) );
        if ( in_array( $settings['content'], array( 'node', 'both' ), true ) && !isset( $fields['node_id'] ) )
            $fields['node_id'] = expDataTableFieldSources::normalizeField( array( 'name' => 'node_id', 'label' => 'Node', 'type' => 'node' ) );
        if ( $settings['stamps'] )
        {
            foreach ( array( 'created' => 'Created', 'modified' => 'Modified' ) as $name => $label )
                if ( !isset( $fields[$name] ) )
                    $fields[$name] = expDataTableFieldSources::normalizeField( array( 'name' => $name, 'label' => $label, 'type' => 'datetime' ) );
        }
        if ( count( $fields ) > 200 )
        {
            $errors[] = 'More than 200 fields: the table is not made.';
            return array();
        }
        return array_values( $fields );
    }

    /**
     * What keeps the files from being made.
     *
     * @param array $settings
     * @return array key => message
     */
    public static function problems( array $settings )
    {
        $problems = array();
        if ( $settings['name'] === '' )
            $problems['name'] = 'Give the extension a name: lower case letters, digits and _.';
        if ( $settings['module'] === '' )
            $problems['module'] = 'Give the module a name.';
        if ( $settings['table'] === '' || !preg_match( '/^[a-z][a-z0-9_]{0,29}$/', $settings['table'] ) )
            $problems['table'] = 'The table name is lower case letters, digits and _, at most 30 characters.';
        if ( $settings['class'] === '' || !preg_match( '/^[A-Za-z][A-Za-z0-9]{0,63}$/', $settings['class'] ) )
            $problems['class'] = 'The class name is letters and digits, starting with a letter.';
        elseif ( self::classTaken( $settings['class'] ) && !self::classIsOurs( $settings ) )
            $problems['class_exists'] = 'A class ' . $settings['class'] . ' exists already in this installation.';
        if ( $settings['connection'] !== '' && expDataConnections::get( $settings['connection'] ) === null )
            $problems['connection'] = 'There is no connection ' . $settings['connection'] . '.';
        if ( $settings['table'] !== '' && $settings['connection'] === '' && preg_match( '/^(ez|exp_)/', $settings['table'] ) )
            $problems['table_kernel'] = 'Tables starting with ez or exp_ belong to the kernel on the site\'s database: choose another name.';
        if ( $settings['module'] !== '' && in_array( $settings['module'], (array)eZINI::instance( 'module.ini' )->variable( 'ModuleSettings', 'ModuleList' ), true )
             && !self::moduleIsOurs( $settings ) )
            $problems['module_exists'] = 'A module ' . $settings['module'] . ' exists already.';
        if ( !$settings['existing'] )
            foreach ( self::licenceProblems( $settings ) as $key => $problem )
                $problems['licence_' . $key] = $problem;
        $errors = array();
        $copy = $settings;
        $fields = self::fields( $copy, $errors );
        foreach ( $errors as $i => $error )
            $problems['fields_' . $i] = $error;
        if ( !$fields )
            $problems['fields'] = 'The table needs at least one field.';
        return $problems;
    }

    /**
     * The files, path below the extension => contents.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        $errors = array();
        $fields = self::fields( $settings, $errors );
        if ( !$fields || $settings['name'] === '' || $settings['table'] === '' || $settings['class'] === '' || $settings['module'] === '' )
            return array();
        $parts = $settings['parts'];
        $new = !$settings['existing'];
        $header = self::licenceNotice( $settings );
        $m = $settings['module'];
        $files = array();
        if ( $parts['class'] )
            $files['classes/' . strtolower( $settings['class'] ) . '.php'] = expModuleTableCode::persistentClass( $settings, $fields, $header );
        if ( $parts['schema'] )
        {
            $schema = self::schema( $settings, $fields );
            $files['share/db_schema.dba'] = "<?php\n// The schema of " . $settings['table'] . ", made by the module table wizard. Read by the installer and by\n"
                                          . "// Setup > Data tables > Schema, which creates and upgrades the table on its connection.\n"
                                          . '$schema = ' . var_export( $schema, true ) . ";\n";
            foreach ( expDataTableSchema::installSql( $schema ) as $engine => $sql )
                $files['sql/' . $engine . '/schema.sql'] = '-- The table ' . $settings['table'] . ' (' . $settings['class'] . ") on $engine, made by the module table wizard.\n\n" . $sql;
        }
        if ( $parts['module'] )
        {
            $files['modules/' . $m . '/module.php'] = expModuleTableCode::moduleDefinition( $settings );
            $files['modules/' . $m . '/list.php'] = expModuleTableCode::listView( $settings, $fields );
            $files['modules/' . $m . '/edit.php'] = expModuleTableCode::editView( $settings );
            $files['modules/' . $m . '/remove.php'] = expModuleTableCode::removeView( $settings );
            $files['modules/' . $m . '/json.php'] = expModuleTableCode::jsonView( $settings );
            $files['settings/module.ini.append.php'] = self::iniBlock( $settings, 'module',
                "[ModuleSettings]\nExtensionRepositories[]=" . $settings['name'] . "\nModuleList[]=" . $m . "\n" );
        }
        if ( $parts['templates'] )
        {
            foreach ( array( 'admin4', 'admin' ) as $design )
            {
                $files['design/' . $design . '/templates/' . $m . '/list.tpl'] = expModuleTableCode::listTemplate( $settings, $fields );
                $files['design/' . $design . '/templates/' . $m . '/edit.tpl'] = expModuleTableCode::editTemplate( $settings, $fields );
                $files['design/' . $design . '/templates/' . $m . '/remove.tpl'] = expModuleTableCode::removeTemplate( $settings );
            }
            $files['settings/design.ini.append.php'] = self::iniBlock( $settings, 'design', "[ExtensionSettings]\nDesignExtensions[]=" . $settings['name'] . "\n" );
        }
        if ( $parts['fetch'] && $parts['module'] )
        {
            $files['modules/' . $m . '/function_definition.php'] = expModuleTableCode::functionDefinition( $settings );
            $files['classes/' . strtolower( $settings['class'] ) . 'functioncollection.php'] = expModuleTableCode::functionCollection( $settings );
        }
        if ( $parts['menu'] && $parts['module'] )
        {
            $key = 'datatable_' . $m;
            $files['settings/menu.ini.append.php'] = self::iniBlock( $settings, 'menu',
                "[Leftmenu_setup]\nGroupLinks_data[]=$key\nLinks[$key]=$m/list\nLinkNames[$key]=" . self::iniValue( $settings['title'] ) . "\n"
                . "PolicyList_{$key}[]=$m/read\nLinkIcons[$key]=table\nLinkDescriptions[$key]=" . self::iniValue( 'The rows of the data table ' . $settings['table'] . '.' ) . "\n" );
        }
        if ( $new && $parts['ezinfo'] )
        {
            $files['ezinfo.php'] = self::ezinfo( $settings );
            $files['extension.xml'] = self::extensionXml( $settings );
        }
        if ( $new && $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );
        if ( $new && $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );
        if ( $parts['readme'] )
            $files[$new ? 'README.md' : 'doc/' . $settings['table'] . '.md'] = self::readme( $settings, $fields, array_keys( $files ) );
        ksort( $files );
        return $files;
    }

    /**
     * The .dba schema of the table.
     *
     * @param array $settings
     * @param array $fields
     * @return array
     */
    public static function schema( array $settings, array $fields )
    {
        return expDataTableSchema::fromDefinition( expModuleTableCode::definition( $settings, $fields ), expModuleTableCode::lengths( $fields ) );
    }

    /**
     * Writes the files: all of them for a new extension; for an existing one only the files that are not there yet, and
     * the wizard's block appended to its settings files (once).
     *
     * @param array $settings
     * @return array ok, message, written, skipped, appended
     */
    public static function write( array $settings )
    {
        if ( !$settings['existing'] )
        {
            $result = parent::write( $settings );
            return $result + array( 'skipped' => array(), 'appended' => array() );
        }
        $problems = static::problems( $settings );
        if ( $problems )
            return array( 'ok' => false, 'message' => implode( ' ', $problems ), 'written' => array(), 'skipped' => array(), 'appended' => array() );
        $target = self::extensionPath( $settings['name'] );
        $written = $skipped = $appended = array();
        foreach ( static::files( $settings ) as $path => $contents )
        {
            $full = $target . '/' . $path;
            if ( is_file( $full ) )
            {
                if ( preg_match( '#^settings/[a-z]+\.ini\.append\.php$#', $path ) )
                {
                    $existing = (string)file_get_contents( $full );
                    $block = self::blockOf( $contents );
                    if ( strpos( $existing, self::MARKER . ' ' . $settings['module'] ) === false
                         && @file_put_contents( $full, rtrim( preg_replace( '#\*/\s*\?>\s*$#', '', $existing ) ) . "\n\n" . $block . "\n*/ ?>\n" ) !== false )
                        $appended[] = $path;
                    continue;
                }
                if ( $path === 'share/db_schema.dba' )
                {
                    // the extension's schema gets this table (and keeps its others)
                    $schema = expKernelTableSchema::read( $full );
                    $schema[$settings['table']] = self::schema( $settings, self::fields( $settings ) )[$settings['table']];
                    ksort( $schema );
                    if ( @file_put_contents( $full, "<?php\n// The schema of the extension's data tables. Read by the installer and by Setup > Data tables > Schema.\n"
                                                    . '$schema = ' . var_export( $schema, true ) . ";\n" ) !== false )
                        $appended[] = $path;
                    continue;
                }
                if ( preg_match( '#^sql/[a-z]+/schema\.sql$#', $path ) )
                {
                    $existing = (string)file_get_contents( $full );
                    if ( strpos( $existing, '-- The table ' . $settings['table'] . ' ' ) === false
                         && @file_put_contents( $full, rtrim( $existing ) . "\n\n" . $contents ) !== false )
                        $appended[] = $path;
                    continue;
                }
                $skipped[] = $path;
                continue;
            }
            if ( !is_dir( dirname( $full ) ) && !@mkdir( dirname( $full ), eZDir::dirMode( 0775 ), true ) )
                return array( 'ok' => false, 'message' => 'Could not create ' . dirname( $path ) . '.', 'written' => $written, 'skipped' => $skipped, 'appended' => $appended );
            if ( @file_put_contents( $full, $contents ) === false )
                return array( 'ok' => false, 'message' => 'Could not write ' . $path . '.', 'written' => $written, 'skipped' => $skipped, 'appended' => $appended );
            @chmod( $full, eZFile::fileMode( 0644 ) );
            $written[] = $path;
        }
        return array( 'ok' => true, 'message' => count( $written ) . ' files added to extension/' . $settings['name'] . ', ' . count( $appended ) . ' settings files extended, '
                                                 . count( $skipped ) . ' files that exist were left as they are.',
                      'written' => $written, 'skipped' => $skipped, 'appended' => $appended );
    }

    /**
     * Creates the table on its connection from the schema (expDataTableSchema).
     *
     * @param array $settings
     * @return array ok, message
     */
    public static function createTable( array $settings )
    {
        $errors = array();
        $fields = self::fields( $settings, $errors );
        if ( !$fields )
            return array( 'ok' => false, 'message' => implode( ' ', $errors ) ?: 'No fields.' );
        try
        {
            $db = expDataConnections::db( $settings['connection'] );
            $result = expDataTableSchema::apply( self::schema( $settings, $fields ), $db );
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'message' => $e->getMessage() );
        }
        $step = isset( $result['tables'][$settings['table']] ) ? $result['tables'][$settings['table']] : array( 'action' => 'none', 'errors' => array() );
        return array( 'ok' => $result['ok'], 'message' => $result['ok']
            ? ( $step['action'] === 'create' ? 'The table ' . $settings['table'] . ' was created.' : ( $step['action'] === 'upgrade' ? 'The table ' . $settings['table'] . ' was brought up to date.' : 'The table ' . $settings['table'] . ' is up to date.' ) )
            : 'The table could not be created: ' . implode( '; ', $step['errors'] ) );
    }

    /**
     * Activates the extension (the kernel's own activation: ActiveExtensions in settings/override/site.ini.append.php,
     * with a copy of the file kept first), clears the caches a new extension needs and regenerates the autoload arrays.
     *
     * @param array $settings
     * @return array ok, message, backup, added
     */
    public static function activate( array $settings )
    {
        if ( !is_dir( self::extensionPath( $settings['name'] ) ) )
            return array( 'ok' => false, 'message' => 'The extension is not written yet.', 'backup' => '', 'added' => false );
        $current = ezpActiveExtensions::current();
        $added = false;
        $backup = '';
        if ( !in_array( $settings['name'], $current, true ) )
        {
            $writer = new ezpActiveExtensions();
            if ( !$writer->write( array_merge( $current, array( $settings['name'] ) ) ) )
                return array( 'ok' => false, 'message' => $writer->error, 'backup' => '', 'added' => false );
            $backup = (string)$writer->backup;
            $added = true;
        }
        $refresh = self::refresh();
        return array( 'ok' => $refresh['ok'], 'message' => ( $added ? 'The extension ' . $settings['name'] . ' was activated (ActiveExtensions[]=' . $settings['name'] . ' added; the previous file is kept in ' . $backup . '). '
                                                                : 'The extension ' . $settings['name'] . ' was active already. ' ) . $refresh['message'],
                      'backup' => $backup, 'added' => $added );
    }

    /**
     * Regenerates the autoload arrays and clears the caches new classes, modules, templates and settings need.
     *
     * @return array ok, message
     */
    public static function refresh()
    {
        $warnings = array();
        try
        {
            eZCache::clearByTag( 'ini' );
            eZCache::clearByID( array( 'template-override', 'design_base', 'active_extensions', 'template', 'user_info_cache' ) );
            $generator = new eZAutoloadGenerator();
            $generator->buildAutoloadArrays();
            $warnings = $generator->getWarnings();
            if ( class_exists( 'expDataTableCatalogue' ) )
                expDataTableCatalogue::reset();
        }
        catch ( Throwable $e )
        {
            return array( 'ok' => false, 'message' => 'The autoload arrays could not be regenerated: ' . $e->getMessage() );
        }
        return array( 'ok' => true, 'message' => 'Autoload arrays regenerated and caches cleared' . ( $warnings ? ' (' . count( $warnings ) . ' warnings)' : '' ) . '.' );
    }

    /**
     * An INI file of the wizard, or its block for an existing file.
     *
     * @param array $settings
     * @param string $what
     * @param string $body
     * @return string
     */
    protected static function iniBlock( array $settings, $what, $body )
    {
        return "<?php /* #?ini charset=\"utf-8\"?\n\n" . self::MARKER . ' ' . $settings['module'] . "\n" . $body . "\n*/ ?>\n";
    }

    /**
     * The block of an INI file the wizard made (marker and settings, without the PHP wrapper).
     *
     * @param string $contents
     * @return string
     */
    protected static function blockOf( $contents )
    {
        $start = strpos( $contents, self::MARKER );
        $block = $start === false ? $contents : substr( $contents, $start );
        return rtrim( preg_replace( '#\*/\s*\?>\s*$#', '', $block ) );
    }

    /**
     * Whether a class name is taken here: loaded, or in an autoload array with its file present. The class is not
     * loaded to find out (an autoload array can still name the file of a removed extension).
     *
     * @param string $class
     * @return bool
     */
    public static function classTaken( $class )
    {
        if ( class_exists( $class, false ) || interface_exists( $class, false ) )
            return true;
        $root = rtrim( self::installationRoot(), '/' ) . '/';
        foreach ( array( 'autoload/ezp_kernel.php', 'var/autoload/ezp_extension.php' ) as $file )
        {
            $array = is_file( $root . $file ) ? @include $root . $file : array();
            if ( is_array( $array ) )
                foreach ( $array as $name => $path )
                    if ( strcasecmp( (string)$name, $class ) === 0 && is_string( $path ) && is_file( ( $path[0] === '/' ? '' : $root ) . $path ) )
                        return true;
        }
        return false;
    }

    /**
     * @param array $settings
     * @return bool whether the class of these settings is the one an earlier run of the wizard wrote into this extension
     */
    protected static function classIsOurs( array $settings )
    {
        $file = self::extensionPath( $settings['name'] ) . '/classes/' . strtolower( $settings['class'] ) . '.php';
        return is_file( $file ) && strpos( (string)file_get_contents( $file ), 'module table wizard' ) !== false;
    }

    /**
     * @param array $settings
     * @return bool whether the module is this extension's (an earlier run)
     */
    protected static function moduleIsOurs( array $settings )
    {
        return is_file( self::extensionPath( $settings['name'] ) . '/modules/' . $settings['module'] . '/module.php' );
    }

    /**
     * @param array $settings
     * @param array $fields
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $fields, array $paths )
    {
        $m = $settings['module'];
        $rows = '';
        foreach ( $fields as $f )
            $rows .= '| `' . $f['name'] . '` | ' . $f['type'] . ( $f['type'] === 'string' ? ' (' . ( $f['length'] ?: 255 ) . ')' : '' ) . ' | '
                   . ( $f['required'] ? 'yes' : '' ) . ' | ' . ( $f['unique'] ? 'yes' : '' ) . ' | ' . $f['label'] . " |\n";
        return '# ' . $settings['title'] . "\n\n"
             . 'The data table `' . $settings['table'] . '` (class `' . $settings['class'] . '`), on ' . ( $settings['connection'] !== '' ? 'the data table connection `' . $settings['connection'] . '`' : 'the site\'s database' )
             . ', made by the module table wizard from ' . $settings['source_label'] . ".\n\n"
             . "## Fields\n\n| Field | Type | Required | Unique | Label |\n|---|---|---|---|---|\n| `id` | integer, counted up | | yes | ID |\n" . $rows
             . "\n## Install\n\nEverything is done in the administration, on the wizard's page or later:\n\n"
             . "1. Setup > Data tables > Schema, class `" . $settings['class'] . "`: create the table on its connection (or run the file of\n   `sql/<engine>/schema.sql` that fits the database).\n"
             . "2. Setup > Extensions: activate `" . $settings['name'] . "`; the autoload arrays and caches are refreshed there.\n"
             . "3. Roles and policies: give `" . $m . "/read` (see the rows) and `" . $m . "/edit` (change them).\n\n"
             . "## Use\n\n- Administration: `" . $m . "/list` (Setup > Data tables group).\n"
             . "- JSON: `GET " . $m . "/json`, `GET " . $m . "/json/<id>`, `POST " . $m . "/json`, `PUT` and `DELETE " . $m . "/json/<id>`.\n"
             . "- Templates: `fetch( '" . $m . "', 'list', hash( 'conditions', hash( '<field>', <value> ), 'limit', 20 ) )`, `count`, `row`.\n"
             . "- PHP: `" . $settings['class'] . "::fetchList()`, `::fetch( \$id )`, `::create( \$row )->store()`; rows from a tool: `expDataTableWriter`.\n\n"
             . "## Files\n\n" . implode( "\n", array_map( function ( $p ) { return '- `' . $p . '`'; }, $paths ) ) . "\n\n"
             . "## License\n\n" . self::licenceLine( $settings ) . "\n";
    }
}
}
?>
