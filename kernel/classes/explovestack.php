<?php
/**
 * File containing the expLoveStack class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Finds what a site coming from LoveStack (mugoweb/ezpublish-legacy) brings along that Exponential
 * handles differently: the markers of its database (the version row, its schema counter, the unique
 * login index, a data_text column that refuses NULL), the legacy_2018 extension, LoveStack-only
 * settings in settings/override and the siteaccesses, and the LoveStack package in composer.json.
 *
 * findings() is read-only: it runs SELECT and PRAGMA statements and reads files, nothing else.
 * migrationPlan() is read-only too; migrate( true ) writes the two ezsite_data rows of the plan and
 * nothing else (never content, never settings). No state outlives the object, so it is safe in
 * Velocity's persistent workers.
 *
 *   $check = new expLoveStack();            // eZDB::instance() and the installation root
 *   $findings = $check->findings();
 *   $needsAttention = expLoveStack::needsAttention( $findings );
 *   $plan = $check->migrate( false );       // what migrate would write; true writes it
 *
 * Each finding: id, label, status (one of the STATUS_ constants), detail, action.
 * Used by ./console exp:lovestack check and exp:lovestack migrate (bin/php/explovestack.php).
 */
class expLoveStack
{
    /** A LoveStack marker is present: something to do before or after moving the site. */
    const STATUS_FOUND = 'found';
    /** Present, but Exponential works with it as it is (reported for information). */
    const STATUS_INFO = 'info';
    /** Not present. */
    const STATUS_CLEAR = 'clear';
    /** Could not be checked (an engine without the information, a missing table). */
    const STATUS_UNKNOWN = 'unknown';

    /**
     * Settings that only LoveStack has (from LoveStack master e61e9fdf0c, compared with Exponential's
     * shipped INI files), with what Exponential does with them:
     *   rename  Exponential has the feature under another name: use that one
     *   remove  Exponential has no such setting; it is ignored
     *   same    Exponential reads the same name (or will, where the feature is being added)
     * file => block => variable => array( action, Exponential's name or a note )
     *
     * @var array
     */
    public static $loveStackSettings = array(
        'site.ini' => array(
            'Session' => array( 'SameSite' => array( 'rename', '[Session] CookieSameSite' ) ),
            'HTTPHeaderSettings' => array( 'SetContentLengthHeader' => array( 'remove', 'Velocity, FrankenPHP and the HTTP cache send Content-Length themselves' ) ),
            'DebugSettings' => array( 'UsedTemplateEditUrl' => array( 'same', '[DebugSettings] UsedTemplateEditUrl' ) ),
            'Cache_ezjscore' => array( 'Maxage' => array( 'remove', 'packed files are cleared by exp:cache' ) ),
        ),
        'image.ini' => array(
            'ValidUploadFormats' => array( 'MIMEList' => array( 'rename', 'image.ini [ImageUploadSettings] AllowedMIMETypes[]' ) ),
        ),
        'error.ini' => array(
            'HTTPError-301' => array( 'HeaderList' => array( 'same', 'error.ini [HTTPError-301] HeaderList[]' ) ),
            'HTTPError-401' => array( 'HeaderList' => array( 'same', 'error.ini [HTTPError-401] HeaderList[]' ) ),
            'HTTPError-404' => array( 'HeaderList' => array( 'same', 'error.ini [HTTPError-404] HeaderList[]' ) ),
        ),
        'ezfind.ini' => array(
            'SearchSettings' => array( 'SortBy' => array( 'same', 'ezfind.ini [SearchSettings] SortBy[] (ezjscore search)' ) ),
        ),
        'ezjscore.ini' => array(
            'eZJSCore' => array( 'LocalScriptBasePath' => array( 'remove', 'Exponential\'s ezjscore has no YUI 2 / YUI 3 local copies' ) ),
            'YUI3' => array( 'LoaderOptions' => array( 'remove', 'YUI is not shipped' ) ),
            'ezjscServer_ezjscnode' => array( 'Functions' => array( 'same', 'ezjscore.ini [ezjscServer_ezjscnode] Functions[]' ) ),
        ),
        'menu.ini' => array(
            'Leftmenu_my' => array( 'PolicyList_edit_profile' => array( 'remove', 'not read by Exponential\'s admin menu' ) ),
        ),
    );

    /** The extension LoveStack ships that must not be active on Exponential (duplicate eZEnumType). */
    const LEGACY_EXTENSION = 'legacy_2018';

    /** @var eZDBInterface|object|null */
    protected $db;

    /** @var string */
    protected $root;

    /**
     * @param eZDBInterface|object|null $db a database handler (arrayQuery(), databaseName()); null for eZDB::instance()
     * @param string|null $root the installation root; null for the current directory
     */
    public function __construct( $db = null, $root = null )
    {
        $this->db = $db;
        $this->root = rtrim( $root !== null ? (string)$root : (string)getcwd(), '/' );
    }

    /**
     * Every check, in report order.
     *
     * @return array[] list of array( 'id', 'label', 'status', 'detail', 'action' )
     */
    public function findings()
    {
        $findings = array();
        $db = $this->database();
        $findings[] = $this->checkVersionRow( $db );
        $findings[] = $this->checkSchemaVersionRow( $db );
        $findings[] = $this->checkUniqueLogin( $db );
        $findings[] = $this->checkDataTextNotNull( $db );
        $findings[] = $this->checkLegacyExtension();
        foreach ( $this->checkSettings() as $finding )
            $findings[] = $finding;
        $findings[] = $this->checkComposer();
        return $findings;
    }

    /**
     * Whether any finding asks for something to be done.
     *
     * @param array[] $findings
     * @return bool
     */
    public static function needsAttention( array $findings )
    {
        foreach ( $findings as $finding )
        {
            if ( $finding['status'] === self::STATUS_FOUND )
                return true;
        }
        return false;
    }

    /**
     * The Exponential version a database of this release carries in ezsite_data, e.g. "6.0.15stable".
     *
     * @return string
     */
    public static function expectedDatabaseVersion()
    {
        return class_exists( 'ExponentialSDK' ) ? ExponentialSDK::version() : '';
    }

    /**
     * @return eZDBInterface|object|null
     */
    protected function database()
    {
        if ( $this->db === null && class_exists( 'eZDB' ) )
            $this->db = eZDB::instance();
        return $this->db;
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $status
     * @param string $detail
     * @param string $action
     * @return array
     */
    protected static function finding( $id, $label, $status, $detail, $action = '' )
    {
        return array( 'id' => $id, 'label' => $label, 'status' => $status, 'detail' => $detail, 'action' => $action );
    }

    /**
     * Rows of a read-only query, or null when the query fails (missing table, engine without it).
     *
     * @param object|null $db
     * @param string $sql
     * @return array|null
     */
    protected static function rows( $db, $sql )
    {
        if ( !is_object( $db ) )
            return null;
        try
        {
            $rows = @$db->arrayQuery( $sql );
        }
        catch ( Throwable $e )
        {
            return null;
        }
        return is_array( $rows ) ? $rows : null;
    }

    /**
     * The engine name of a handler: mysql, postgresql, sqlite, oracle, mongodb, or '' when unknown.
     *
     * @param object|null $db
     * @return string
     */
    protected static function engine( $db )
    {
        if ( !is_object( $db ) || !method_exists( $db, 'databaseName' ) )
            return '';
        $name = strtolower( (string)$db->databaseName() );
        return $name === 'sqlite3' ? 'sqlite' : $name;
    }

    /**
     * The value of one ezsite_data row: false when there is no such row, null when the table cannot be read.
     *
     * @param object|null $db
     * @param string $name
     * @return string|false|null
     */
    protected static function siteData( $db, $name )
    {
        $rows = self::rows( $db, "SELECT value FROM ezsite_data WHERE name='" . str_replace( "'", "''", $name ) . "'" );
        if ( $rows === null )
            return null;
        if ( !$rows )
            return false;
        $row = array_change_key_case( $rows[0], CASE_LOWER );
        return (string)$row['value'];
    }

    /**
     * @param object|null $db
     * @return array
     */
    protected function checkVersionRow( $db )
    {
        $label = 'Database version row (ezsite_data ezpublish-version)';
        $value = self::siteData( $db, 'ezpublish-version' );
        if ( $value === null )
            return self::finding( 'db.version', $label, self::STATUS_UNKNOWN, 'ezsite_data cannot be read' );
        if ( $value === false )
            return self::finding( 'db.version', $label, self::STATUS_UNKNOWN, 'no ezpublish-version row' );
        if ( strtolower( $value ) === 'lovestack' )
        {
            return self::finding( 'db.version', $label, self::STATUS_FOUND, "'$value': a LoveStack database",
                                  "set it to '" . self::expectedDatabaseVersion() . "' with the upgrade SQL (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql) or exp:lovestack migrate --apply, or by hand: UPDATE ezsite_data SET value='" . self::expectedDatabaseVersion() . "' WHERE name='ezpublish-version')" );
        }
        $expected = self::expectedDatabaseVersion();
        if ( $expected !== '' && $value !== $expected )
            return self::finding( 'db.version', $label, self::STATUS_INFO, "'$value' (this release is '$expected')", 'run the upgrade SQL of update/database/<engine>/6.0/ up to this release' );
        return self::finding( 'db.version', $label, self::STATUS_CLEAR, "'$value'" );
    }

    /**
     * @param object|null $db
     * @return array
     */
    protected function checkSchemaVersionRow( $db )
    {
        $label = 'LoveStack schema counter (ezsite_data db-schema-version)';
        $value = self::siteData( $db, 'db-schema-version' );
        if ( $value === null )
            return self::finding( 'db.schema_version', $label, self::STATUS_UNKNOWN, 'ezsite_data cannot be read' );
        if ( $value === false )
            return self::finding( 'db.schema_version', $label, self::STATUS_CLEAR, 'no such row' );
        return self::finding( 'db.schema_version', $label, self::STATUS_INFO, "'$value': LoveStack's update/run.php has run on this database",
                              'Exponential does not read it; keep it as a record (exp:lovestack migrate --apply records it as lovestack-schema-version too)' );
    }

    /**
     * @param object|null $db
     * @return array
     */
    protected function checkUniqueLogin( $db )
    {
        $label = 'Unique index on ezuser.login';
        $unique = $this->uniqueLoginIndex( $db );
        if ( $unique === null )
            return self::finding( 'db.unique_login', $label, self::STATUS_UNKNOWN, 'the indexes of ezuser cannot be read on this engine' );
        if ( $unique === '' )
            return self::finding( 'db.unique_login', $label, self::STATUS_CLEAR, 'login has a plain (non-unique) index' );
        return self::finding( 'db.unique_login', $label, self::STATUS_INFO, "unique index '$unique' (as LoveStack's schema step 4 makes it)",
                              'keep it: Exponential works with it; new duplicate logins are refused by the database' );
    }

    /**
     * The name of a unique index (or constraint) on ezuser( login ) alone: '' when there is none, null when unknown.
     *
     * @param object|null $db
     * @return string|null
     */
    protected function uniqueLoginIndex( $db )
    {
        switch ( self::engine( $db ) )
        {
            case 'sqlite':
                $indexes = self::rows( $db, "PRAGMA index_list('ezuser')" );
                if ( $indexes === null )
                    return null;
                foreach ( $indexes as $index )
                {
                    if ( (int)$index['unique'] !== 1 )
                        continue;
                    $columns = self::rows( $db, "PRAGMA index_info('" . str_replace( "'", "''", $index['name'] ) . "')" );
                    if ( is_array( $columns ) && count( $columns ) === 1 && strtolower( $columns[0]['name'] ) === 'login' )
                        return (string)$index['name'];
                }
                return '';

            case 'mysql':
                $rows = self::rows( $db, "SELECT INDEX_NAME AS index_name, NON_UNIQUE AS non_unique, COLUMN_NAME AS column_name
                                          FROM information_schema.STATISTICS
                                          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ezuser'" );
                return $rows === null ? null : self::singleColumnUnique( $rows );

            case 'postgresql':
                $rows = self::rows( $db, "SELECT i.relname AS index_name, CASE WHEN ix.indisunique THEN 0 ELSE 1 END AS non_unique, a.attname AS column_name
                                          FROM pg_class t JOIN pg_index ix ON t.oid = ix.indrelid JOIN pg_class i ON i.oid = ix.indexrelid
                                          JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY( ix.indkey )
                                          WHERE t.relname = 'ezuser' AND t.relkind = 'r' AND pg_table_is_visible( t.oid )" );
                return $rows === null ? null : self::singleColumnUnique( $rows );

            case 'oracle':
                $rows = self::rows( $db, "SELECT i.index_name AS index_name, CASE WHEN i.uniqueness = 'UNIQUE' THEN 0 ELSE 1 END AS non_unique, c.column_name AS column_name
                                          FROM user_indexes i JOIN user_ind_columns c ON c.index_name = i.index_name
                                          WHERE i.table_name = 'EZUSER'" );
                return $rows === null ? null : self::singleColumnUnique( $rows );
        }
        return null;
    }

    /**
     * From rows of ( index_name, non_unique, column_name ), the unique index whose only column is login.
     *
     * @param array $rows
     * @return string
     */
    protected static function singleColumnUnique( array $rows )
    {
        $indexes = array();
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $name = (string)$row['index_name'];
            $indexes[$name]['unique'] = (int)$row['non_unique'] === 0;
            $indexes[$name]['columns'][] = strtolower( (string)$row['column_name'] );
        }
        foreach ( $indexes as $name => $index )
        {
            if ( $index['unique'] && $index['columns'] === array( 'login' ) )
                return $name;
        }
        return '';
    }

    /**
     * @param object|null $db
     * @return array
     */
    protected function checkDataTextNotNull( $db )
    {
        $label = 'ezcontentobject_attribute.data_text NOT NULL';
        $notNull = $this->dataTextNotNull( $db );
        if ( $notNull === null )
            return self::finding( 'db.data_text_not_null', $label, self::STATUS_UNKNOWN, 'the column cannot be read on this engine' );
        if ( !$notNull )
            return self::finding( 'db.data_text_not_null', $label, self::STATUS_CLEAR, 'nullable, as Exponential ships it' );
        return self::finding( 'db.data_text_not_null', $label, self::STATUS_INFO, "NOT NULL DEFAULT '' (as LoveStack's schema step 4 makes it)",
                              'keep it: Exponential stores an empty string, never NULL, in data_text' );
    }

    /**
     * Whether data_text refuses NULL: null when unknown.
     *
     * @param object|null $db
     * @return bool|null
     */
    protected function dataTextNotNull( $db )
    {
        switch ( self::engine( $db ) )
        {
            case 'sqlite':
                $columns = self::rows( $db, "PRAGMA table_info('ezcontentobject_attribute')" );
                if ( !$columns )
                    return null;
                foreach ( $columns as $column )
                {
                    if ( strtolower( $column['name'] ) === 'data_text' )
                        return (int)$column['notnull'] === 1;
                }
                return null;

            case 'mysql':
                $rows = self::rows( $db, "SELECT IS_NULLABLE AS is_nullable FROM information_schema.COLUMNS
                                          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ezcontentobject_attribute' AND COLUMN_NAME = 'data_text'" );
                break;

            case 'postgresql':
                $rows = self::rows( $db, "SELECT is_nullable FROM information_schema.columns
                                          WHERE table_schema = current_schema() AND table_name = 'ezcontentobject_attribute' AND column_name = 'data_text'" );
                break;

            case 'oracle':
                $rows = self::rows( $db, "SELECT CASE WHEN nullable = 'N' THEN 'NO' ELSE 'YES' END AS is_nullable FROM user_tab_columns
                                          WHERE table_name = 'EZCONTENTOBJECT_ATTRIBUTE' AND column_name = 'DATA_TEXT'" );
                break;

            default:
                return null;
        }
        if ( !$rows )
            return null;
        $row = array_change_key_case( $rows[0], CASE_LOWER );
        return strtoupper( (string)$row['is_nullable'] ) === 'NO';
    }

    /**
     * @return array
     */
    protected function checkLegacyExtension()
    {
        $label = 'Extension ' . self::LEGACY_EXTENSION;
        $where = array();
        foreach ( $this->settingFiles( 'site.ini' ) as $file => $scope )
        {
            $ini = self::parseIni( $file );
            foreach ( array( 'ActiveExtensions', 'ActiveAccessExtensions' ) as $variable )
            {
                if ( isset( $ini['ExtensionSettings'][$variable] ) && in_array( self::LEGACY_EXTENSION, (array)$ini['ExtensionSettings'][$variable], true ) )
                    $where[] = "$scope ($variable)";
            }
        }
        if ( $where )
        {
            return self::finding( 'ext.legacy_2018', $label, self::STATUS_FOUND, 'active in ' . implode( ', ', $where ),
                                  'remove it from ActiveExtensions[] / ActiveAccessExtensions[]: Exponential keeps ezenum in the kernel and has no YUI 2' );
        }
        if ( is_dir( $this->root . '/extension/' . self::LEGACY_EXTENSION ) )
            return self::finding( 'ext.legacy_2018', $label, self::STATUS_INFO, 'extension/' . self::LEGACY_EXTENSION . ' exists but is not active', 'it can be removed' );
        return self::finding( 'ext.legacy_2018', $label, self::STATUS_CLEAR, 'not active, not installed' );
    }

    /**
     * One finding per LoveStack-only setting that settings/override or a siteaccess sets.
     *
     * @return array[]
     */
    protected function checkSettings()
    {
        $findings = array();
        foreach ( self::$loveStackSettings as $iniName => $blocks )
        {
            foreach ( $this->settingFiles( $iniName, false ) as $file => $scope )
            {
                $ini = self::parseIni( $file );
                foreach ( $blocks as $block => $variables )
                {
                    foreach ( $variables as $variable => $use )
                    {
                        if ( !isset( $ini[$block] ) || !array_key_exists( $variable, $ini[$block] ) )
                            continue;
                        list( $action, $note ) = $use;
                        $label = "$iniName [$block] $variable";
                        $detail = "set in $scope";
                        if ( $action === 'rename' )
                            $findings[] = self::finding( 'ini.setting', $label, self::STATUS_FOUND, $detail, "rename it to $note" );
                        else if ( $action === 'remove' )
                            $findings[] = self::finding( 'ini.setting', $label, self::STATUS_FOUND, $detail, "remove it: $note" );
                        else
                            $findings[] = self::finding( 'ini.setting', $label, self::STATUS_INFO, $detail, "the same name in Exponential: $note" );
                    }
                }
            }
        }
        if ( !$findings )
            $findings[] = self::finding( 'ini.setting', 'LoveStack-only settings', self::STATUS_CLEAR, 'none in settings/override, the siteaccesses or the extensions' );
        return $findings;
    }

    /**
     * @return array
     */
    protected function checkComposer()
    {
        $label = 'LoveStack package in composer.json / composer.lock';
        $where = array();
        foreach ( array( 'composer.json', 'composer.lock' ) as $name )
        {
            $file = $this->root . '/' . $name;
            if ( is_file( $file ) && strpos( (string)@file_get_contents( $file ), '"mugoweb/ezpublish-legacy"' ) !== false )
                $where[] = $name;
        }
        if ( $where )
            return self::finding( 'composer.package', $label, self::STATUS_FOUND, 'mugoweb/ezpublish-legacy in ' . implode( ', ', $where ), 'replace it with Exponential\'s package' );
        return self::finding( 'composer.package', $label, self::STATUS_CLEAR, 'not required' );
    }

    /** The row that tells Exponential's version checks which release the database is for. */
    const ROW_VERSION = 'ezpublish-version';
    /** The counter LoveStack's update/run.php keeps. */
    const ROW_SCHEMA_VERSION = 'db-schema-version';
    /** Where exp:lovestack migrate records the former LoveStack schema version. */
    const ROW_FORMER_SCHEMA_VERSION = 'lovestack-schema-version';
    /** The value of ezpublish-version on a database that LoveStack has upgraded. */
    const LOVESTACK_VERSION = 'lovestack';

    /**
     * What exp:lovestack migrate would do on this database, without writing anything.
     *
     * A database comes from LoveStack when its version row says 'lovestack', or when it has LoveStack's
     * db-schema-version row or an earlier migrate's lovestack-schema-version row (so the plan of a database
     * migrated before, or one that the 6.0.15 update SQL has already given the version row, is empty rather
     * than refused). Two rows are planned, nothing else:
     *   ezpublish-version         'lovestack' becomes this release's version (another value is left to the
     *                             upgrade SQL of update/database/<engine>/, which sets it)
     *   lovestack-schema-version  the value of db-schema-version ('unknown' when there is none), once
     * The settings to change come from findings(): LoveStack-only settings, legacy_2018, the LoveStack package.
     *
     * @return array 'loveStack' (bool), 'refused' (string, '' when not refused), 'version' (the current
     *               ezpublish-version, false without the row), 'expected' (string), 'writes' (list of array(
     *               'name', 'from' (string|null for a new row), 'to' )), 'notes' (string[]), 'settings' (list of
     *               array( 'label', 'detail', 'action' ))
     */
    public function migrationPlan()
    {
        $db = $this->database();
        $plan = array( 'loveStack' => false, 'refused' => '', 'version' => false, 'expected' => self::expectedDatabaseVersion(),
                       'writes' => array(), 'notes' => array(), 'settings' => array() );

        $version = self::siteData( $db, self::ROW_VERSION );
        $schema = self::siteData( $db, self::ROW_SCHEMA_VERSION );
        $former = self::siteData( $db, self::ROW_FORMER_SCHEMA_VERSION );
        if ( $version === null )
        {
            $plan['refused'] = 'ezsite_data cannot be read: this command needs the database of an installation. Nothing was written.';
            return $plan;
        }
        $plan['version'] = $version;
        $isLoveStackRow = $version !== false && strtolower( $version ) === self::LOVESTACK_VERSION;
        if ( !$isLoveStackRow && $schema === false && $former === false )
        {
            $plan['refused'] = "This database does not come from LoveStack: ezsite_data " . self::ROW_VERSION . " is " .
                               ( $version === false ? 'missing' : "'$version'" ) . " and there is no " . self::ROW_SCHEMA_VERSION .
                               " or " . self::ROW_FORMER_SCHEMA_VERSION . " row. exp:lovestack migrate only changes databases that" .
                               " LoveStack has upgraded. Nothing was written.";
            return $plan;
        }
        if ( $plan['expected'] === '' )
        {
            $plan['refused'] = 'The version of this Exponential release cannot be read (lib/version.php). Nothing was written.';
            return $plan;
        }
        $plan['loveStack'] = true;

        if ( $isLoveStackRow )
        {
            $plan['writes'][] = array( 'name' => self::ROW_VERSION, 'from' => $version, 'to' => $plan['expected'] );
            $plan['notes'][] = "Apply the database update of this release (update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql) if you have not yet:" .
                               " the version row only records which release the database is for.";
        }
        else if ( $version === false )
            $plan['notes'][] = "There is no " . self::ROW_VERSION . " row: apply the database update of this release (update/database/<engine>/6.0/), which writes it.";
        else if ( $version !== $plan['expected'] )
            $plan['notes'][] = self::ROW_VERSION . " is '$version' (this release is '{$plan['expected']}'): it is left to the database update of" .
                               " update/database/<engine>/6.0/, which sets it.";

        if ( $former === false )
        {
            $plan['writes'][] = array( 'name' => self::ROW_FORMER_SCHEMA_VERSION, 'from' => null,
                                       'to' => $schema !== false ? $schema : 'unknown' );
        }
        if ( $schema !== false )
            $plan['notes'][] = "LoveStack's " . self::ROW_SCHEMA_VERSION . " row ('$schema') is kept as it is; Exponential does not read it.";

        foreach ( $this->findings() as $finding )
        {
            if ( $finding['status'] !== self::STATUS_FOUND || !in_array( $finding['id'], array( 'ini.setting', 'ext.legacy_2018', 'composer.package' ), true ) )
                continue;
            $action = $finding['action'];
            if ( $finding['label'] === 'site.ini [Session] SameSite' )
                $action .= ' (until then Exponential reads [Session] SameSite as CookieSameSite and logs a deprecation notice; CookieSameSite wins when both are set)';
            $plan['settings'][] = array( 'label' => $finding['label'], 'detail' => $finding['detail'], 'action' => $action );
        }
        return $plan;
    }

    /**
     * Runs the migration plan: with $apply false it only reports (a dry run), with $apply true it writes the
     * planned ezsite_data rows in one transaction. Running it again writes nothing, since the plan is then empty.
     * Never touches content, settings or any other table.
     *
     * @param bool $apply
     * @return array the plan of migrationPlan(), plus 'applied' (bool) and 'written' (int, rows written)
     */
    public function migrate( $apply = false )
    {
        $plan = $this->migrationPlan();
        $plan['applied'] = false;
        $plan['written'] = 0;
        if ( !$apply || $plan['refused'] !== '' || !$plan['writes'] )
            return $plan;

        $db = $this->database();
        if ( method_exists( $db, 'begin' ) )
            $db->begin();
        foreach ( $plan['writes'] as $write )
        {
            $name = self::escape( $db, $write['name'] );
            $value = self::escape( $db, $write['to'] );
            if ( $write['from'] === null )
                $db->query( "INSERT INTO ezsite_data ( name, value ) VALUES ( '$name', '$value' )" );
            else
                $db->query( "UPDATE ezsite_data SET value='$value' WHERE name='$name'" );
            ++$plan['written'];
        }
        if ( method_exists( $db, 'commit' ) )
            $db->commit();
        $plan['applied'] = true;
        return $plan;
    }

    /**
     * @param object $db
     * @param string $text
     * @return string
     */
    protected static function escape( $db, $text )
    {
        return method_exists( $db, 'escapeString' ) ? $db->escapeString( (string)$text ) : str_replace( "'", "''", (string)$text );
    }

    /**
     * The files of one INI name in settings/override and in each siteaccess (for settings, also those
     * of each extension and its siteaccess directories), with a scope label.
     *
     * @param string $iniName e.g. site.ini
     * @param bool $withShipped also settings/<name> itself (for ActiveExtensions[])
     * @return array file path => scope
     */
    protected function settingFiles( $iniName, $withShipped = true )
    {
        $files = array();
        $suffixes = array( '', '.append', '.append.php' );
        $dirs = array( 'settings/override' => 'settings/override' );
        foreach ( (array)glob( $this->root . '/settings/siteaccess/*', GLOB_ONLYDIR ) as $dir )
            $dirs['settings/siteaccess/' . basename( $dir )] = 'siteaccess ' . basename( $dir );
        if ( !$withShipped )
        {
            // the settings an extension brings, and those of its siteaccess directories
            foreach ( (array)glob( $this->root . '/extension/*/settings', GLOB_ONLYDIR ) as $dir )
            {
                $extension = basename( dirname( $dir ) );
                $dirs['extension/' . $extension . '/settings'] = 'extension ' . $extension;
                foreach ( (array)glob( $dir . '/siteaccess/*', GLOB_ONLYDIR ) as $siteaccessDir )
                    $dirs['extension/' . $extension . '/settings/siteaccess/' . basename( $siteaccessDir )] = 'extension ' . $extension . ', siteaccess ' . basename( $siteaccessDir );
            }
        }
        foreach ( $dirs as $dir => $scope )
        {
            foreach ( $suffixes as $suffix )
            {
                $file = $this->root . '/' . $dir . '/' . $iniName . $suffix;
                if ( is_file( $file ) )
                    $files[$file] = $scope;
            }
        }
        if ( $withShipped && is_file( $this->root . '/settings/' . $iniName ) )
            $files = array( $this->root . '/settings/' . $iniName => 'settings/' . $iniName ) + $files;
        return $files;
    }

    /**
     * A plain read of an INI file: block => variable => value (array for Name[] and Name[key]).
     * Comments, the PHP wrapper of .append.php files and empty Name[] resets are skipped.
     *
     * @param string $file
     * @return array
     */
    public static function parseIni( $file )
    {
        $result = array();
        $lines = @file( $file, FILE_IGNORE_NEW_LINES );
        if ( !is_array( $lines ) )
            return $result;
        $block = null;
        foreach ( $lines as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' || $line[0] === ';' || strpos( $line, '<?php' ) === 0 || strpos( $line, '*/' ) === 0 || strpos( $line, '?>' ) === 0 )
                continue;
            if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
            {
                $block = trim( $m[1] );
                if ( !isset( $result[$block] ) )
                    $result[$block] = array();
                continue;
            }
            if ( $block === null || !preg_match( '/^([A-Za-z0-9_\-]+)(\[([^\]]*)\])?=(.*)$/', $line, $m ) )
                continue;
            $name = $m[1];
            if ( isset( $m[2] ) && $m[2] !== '' )
            {
                if ( !isset( $result[$block][$name] ) || !is_array( $result[$block][$name] ) )
                    $result[$block][$name] = array();
                if ( $m[3] === '' )
                {
                    if ( $m[4] !== '' )
                        $result[$block][$name][] = $m[4];
                }
                else
                    $result[$block][$name][$m[3]] = $m[4];
            }
            else
                $result[$block][$name] = $m[4];
        }
        return $result;
    }
}
