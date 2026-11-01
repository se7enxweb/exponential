<?php
/**
 * File containing the expMigrationCaption class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The captions of migration runs. A caption is exp:upgrade's (expUpgradeCaption::take(): git_manager's full caption,
 * or exp:upgrade's own dump without git_manager, with settings/override and the commits of the code), of one of the
 * kinds migration-pre, migration-post and migration-failure, so there is one list of captions for both
 * (./console exp:upgrade:captions). What a migration run adds in the caption's migration/ folder:
 *
 *   migration/marker.json          what the installation looked like when the caption was taken (databaseMarker(),
 *                                  var/ storage and settings/override as file lists, the commit of the root
 *                                  checkout): a caption whose marker equals the installation's is reused instead
 *                                  of a new one ([CaptionSettings] ReuseUnchanged)
 *   migration/uncommitted.json     with --include-uncommitted: the checkouts whose code trees had changes that were
 *   migration/uncommitted/         not committed, a patch of each (git diff --binary HEAD) and a copy of its new files
 *
 * upgrade/checksums.json is written again after these are added, so exp:upgrade:captions --verify covers them.
 *
 * Restore (restore()) is exp:upgrade's rollback (expUpgradeRollback::run()) of the parts in [CaptionSettings]
 * RestoreParts, without its own safety caption: the flow of a failed run takes the migration-failure caption itself.
 *
 * Everything that reaches git, the caption store or the rollback goes through a protected method (git(),
 * takeCaption(), captions(), rollback(), databaseMarker()), so a test replaces them.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationCaption
{
    /** The caption before a run. */
    const PRE = 'migration-pre';
    /** The caption after a run that succeeded (the re-caption). */
    const POST = 'migration-post';
    /** The caption of the state a failed run left, taken before the restore. */
    const FAILURE = 'migration-failure';

    /** The marker file in a caption. */
    const MARKER = 'migration/marker.json';
    /** The version of the marker file. */
    const MARKER_FORMAT = 'exp-migration-marker/1';
    /** The list of the changes that were not committed. */
    const UNCOMMITTED = 'migration/uncommitted.json';
    /** The folder of their patches and new files. */
    const UNCOMMITTED_DIR = 'migration/uncommitted';
    /** The version of uncommitted.json. */
    const UNCOMMITTED_FORMAT = 'exp-migration-uncommitted/1';

    /** A SQLite database larger than this is not read row by row for the marker: no reuse. */
    const SQLITE_MARKER_MAX_BYTES = 268435456;

    /** @var expMigrationContext */
    protected $context;

    /** @var array */
    protected $settings;

    /**
     * @param expMigrationContext $context
     * @param array $settings as defaults()
     */
    public function __construct( expMigrationContext $context, array $settings = array() )
    {
        $this->context = $context;
        $this->settings = $settings + self::defaults();
    }

    /**
     * The settings when migration.ini gives none.
     *
     * @return array before, after, reuse, restore (bool), restore-parts, trees, ignore-tables (string[]), backup-dir,
     *               include-uncommitted (bool)
     */
    public static function defaults()
    {
        return array( 'before' => true, 'after' => true, 'reuse' => true, 'restore' => true,
                      'restore-parts' => array( 'database', 'storage', 'settings' ), 'trees' => array( 'settings', 'extension', 'design' ),
                      'ignore-tables' => array( 'exp_migration', 'exp_migration_run', 'ezsession', 'expaudit_*' ),
                      'backup-dir' => '', 'include-uncommitted' => false );
    }

    /**
     * The settings of migration.ini [CaptionSettings] with the options of a run over them: no-caption (neither
     * caption), no-restore, include-uncommitted.
     *
     * @param array $options
     * @param eZINI|null $ini
     * @return array as defaults()
     */
    public static function settingsFrom( array $options = array(), $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $settings = self::defaults();
        $flag = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'CaptionSettings', $name ) ? $ini->variable( 'CaptionSettings', $name ) !== 'disabled' : $default;
        };
        $list = function ( $name, $default ) use ( $ini ) {
            if ( !$ini->hasVariable( 'CaptionSettings', $name ) )
                return $default;
            return array_values( array_filter( array_map( 'trim', (array)$ini->variable( 'CaptionSettings', $name ) ), 'strlen' ) );
        };
        $settings['before'] = $flag( 'Before', true );
        $settings['after'] = $flag( 'After', true );
        $settings['reuse'] = $flag( 'ReuseUnchanged', true );
        $settings['restore'] = $flag( 'RestoreOnFailure', true );
        $settings['restore-parts'] = $list( 'RestoreParts', $settings['restore-parts'] );
        $settings['trees'] = $list( 'CodeTreeList', $settings['trees'] );
        $settings['ignore-tables'] = array_map( 'strtolower', $list( 'ReuseIgnoreTableList', $settings['ignore-tables'] ) );
        $settings['backup-dir'] = $ini->hasVariable( 'CaptionSettings', 'BackupDirectory' ) ? trim( (string)$ini->variable( 'CaptionSettings', 'BackupDirectory' ) ) : '';
        if ( !empty( $options['no-caption'] ) )
            $settings['before'] = $settings['after'] = false;
        if ( !empty( $options['no-restore'] ) )
            $settings['restore'] = false;
        $settings['include-uncommitted'] = !empty( $options['include-uncommitted'] );
        return $settings;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function setting( $name )
    {
        return isset( $this->settings[$name] ) ? $this->settings[$name] : null;
    }

    /**
     * The label of a caption: the run, its environment, the kind and the migrations. Pure.
     *
     * @param string $kind PRE, POST, FAILURE
     * @param string $runId
     * @param string $environment
     * @param string[] $ids the migrations of the run (pre: to apply; post: applied; failure: the failed one first)
     * @return string at most 250 characters
     */
    public static function label( $kind, $runId, $environment, array $ids )
    {
        $short = array( self::PRE => 'pre', self::POST => 'post', self::FAILURE => 'failure' );
        $label = 'migration run ' . $runId . ( (string)$environment !== '' ? ' in ' . $environment : '' ) . ' '
               . ( isset( $short[$kind] ) ? $short[$kind] : $kind );
        if ( $ids )
        {
            $names = array_slice( array_values( $ids ), 0, 5 );
            $label .= ': ' . count( $ids ) . ' migration(s) ' . implode( ', ', $names ) . ( count( $ids ) > 5 ? ' ...' : '' );
        }
        return mb_substr( $label, 0, 250 );
    }

    /**
     * Takes a caption (or reuses one, see the class), and adds the marker and the changes that were not committed.
     *
     * @param string $kind PRE, POST, FAILURE
     * @param string[] $ids the migrations (label())
     * @param array[] $uncommitted what uncommitted() found, to keep in the caption (with include-uncommitted)
     * @return array ok, id, dir, source, label, kind, reused (bool), messages
     */
    public function take( $kind, array $ids, array $uncommitted = array() )
    {
        $context = $this->context;
        $label = self::label( $kind, $context->runId(), $context->environment(), $ids );
        $marker = null;
        if ( $kind !== self::FAILURE )
        {
            try
            {
                $marker = $this->marker();
            }
            catch ( Throwable $e )
            {
                $marker = null;
            }
        }
        if ( $kind === self::PRE && $this->settings['reuse'] && !$uncommitted && $marker !== null )
        {
            $reuse = $this->reusable( $marker );
            if ( $reuse !== null )
            {
                $result = array( 'ok' => true, 'id' => $reuse['id'], 'dir' => $reuse['dir'], 'source' => $reuse['source'], 'label' => $reuse['label'],
                                 'kind' => $reuse['kind'], 'reused' => true,
                                 'messages' => array( 'reused the caption ' . $reuse['id'] . ': nothing has changed since it was taken' ) );
                $this->audit( $result, 'reuse' );
                return $result;
            }
        }
        if ( $this->settings['backup-dir'] !== '' && $context->option( 'backup-dir' ) === null )
            $context->setOption( 'backup-dir', $this->settings['backup-dir'] );
        try
        {
            $result = $this->takeCaption( $kind, $label );
        }
        catch ( Throwable $e )
        {
            $result = array( 'ok' => false, 'id' => '', 'dir' => '', 'source' => '', 'label' => $label, 'kind' => $kind,
                             'messages' => array( 'the caption failed: ' . $e->getMessage() ) );
        }
        $result += array( 'messages' => array() );
        $result['reused'] = false;
        if ( !empty( $result['ok'] ) && $result['dir'] !== '' && is_dir( $result['dir'] ) )
        {
            try
            {
                $added = false;
                if ( $marker !== null )
                {
                    $this->writeJson( $result['dir'] . '/' . self::MARKER, $marker );
                    $added = true;
                }
                if ( $uncommitted )
                {
                    $result['messages'] = array_merge( $result['messages'], $this->keepUncommitted( $result['dir'], $uncommitted ) );
                    $added = true;
                }
                if ( $added && is_dir( $result['dir'] . '/upgrade' ) )
                    $this->writeJson( $result['dir'] . '/upgrade/checksums.json', expUpgradeCaption::checksums( $result['dir'] ) );
            }
            catch ( Throwable $e )
            {
                $result['messages'][] = 'the migration part of the caption failed: ' . $e->getMessage();
                if ( $uncommitted )
                    $result['ok'] = false;
            }
        }
        $this->audit( $result, 'take' );
        return $result;
    }

    /**
     * Puts the installation back to a caption: the parts of [CaptionSettings] RestoreParts through exp:upgrade's
     * rollback, under the lock the run holds, without a safety caption of its own (the flow took the failure
     * caption). Engines without a built-in restore (Oracle) are not tried: manual holds the steps.
     *
     * @param string $captionId
     * @return array ok (bool), parts (part => lines), errors (string[]), manual (string[] the steps by hand)
     */
    public function restore( $captionId )
    {
        $context = $this->context;
        $result = array( 'ok' => false, 'caption' => (string)$captionId, 'parts' => array(), 'errors' => array(), 'manual' => array() );
        $parts = array_values( array_intersect( (array)$this->settings['restore-parts'], expUpgradeRollback::PARTS ) );
        if ( in_array( 'database', $parts, true ) && !in_array( $context->engine(), expUpgradeRollback::RESTORE_ENGINES, true ) )
        {
            $result['manual'] = expUpgradeRollback::manualSteps( $context->engine(), $context->db(), null );
            $result['errors'][] = 'no built-in restore for ' . $context->engine() . ': restore it by hand';
            return $result;
        }
        $options = array( 'apply' => true, 'parts' => implode( ',', $parts ), 'db' => $context->db(), 'root' => $context->root(),
                          'no-caption' => true, 'force-no-caption' => true, 'no-verify' => true );
        if ( $context->option( 'backup-dir' ) !== null )
            $options['backup-dir'] = $context->option( 'backup-dir' );
        if ( $context->option( 'lock-file' ) !== null )
            $options['lock-file'] = $context->option( 'lock-file' );
        try
        {
            $report = $this->rollback( (string)$captionId, $options );
        }
        catch ( Throwable $e )
        {
            $report = array( 'exit' => expUpgrade::EXIT_INTERNAL, 'errors' => array( $e->getMessage() ), 'rollback' => array( 'parts' => array() ) );
        }
        $result['parts'] = isset( $report['rollback']['parts'] ) ? (array)$report['rollback']['parts'] : array();
        $result['errors'] = isset( $report['errors'] ) ? array_values( (array)$report['errors'] ) : array();
        $result['ok'] = isset( $report['exit'] ) && (int)$report['exit'] === expUpgrade::EXIT_OK;
        if ( !$result['ok'] )
            $result['manual'] = expUpgradeRollback::manualSteps( $context->engine(), $context->db(), null );
        return $result;
    }

    // ---- reuse -------------------------------------------------------------------------------------------------

    /**
     * What the installation looks like now, for the reuse check: null when the database cannot be told exactly
     * (MongoDB, Oracle, a large SQLite file), then a new caption is always taken.
     *
     * @return array|null format, engine, database, storage, settings, code
     */
    public function marker()
    {
        $database = $this->databaseMarker();
        if ( $database === null )
            return null;
        $root = rtrim( $this->context->root(), '/' );
        $storage = $this->context->option( 'storage-dir' );
        if ( $storage === null )
            $storage = eZSys::storageDirectory();
        if ( $storage !== '' && $storage[0] !== '/' )
            $storage = "$root/$storage";
        $code = '';
        if ( file_exists( "$root/.git" ) && $this->gitAvailable() )
        {
            $head = $this->git( $root, array( 'rev-parse', '--verify', 'HEAD' ) );
            $code = $head['ok'] ? trim( $head['output'] ) : '';
        }
        return array( 'format' => self::MARKER_FORMAT, 'engine' => $this->context->engine(), 'database' => $database,
                      'storage' => self::filesMarker( $storage ), 'settings' => self::filesMarker( $this->settingsDir( $root ) ), 'code' => $code );
    }

    /**
     * The settings folder the marker covers: the option settings-dir (relative to the root, '' for none, as storage-dir),
     * else settings/override.
     *
     * @param string $root
     * @return string
     */
    protected function settingsDir( $root )
    {
        $dir = $this->context->option( 'settings-dir' );
        if ( $dir === null )
            return "$root/settings/override";
        $dir = (string)$dir;
        return ( $dir === '' || $dir[0] === '/' ) ? $dir : "$root/$dir";
    }

    /**
     * Whether two markers describe the same state (the creation time aside). Pure.
     *
     * @param array|null $a
     * @param array|null $b
     * @return bool
     */
    public static function sameMarker( $a, $b )
    {
        if ( !is_array( $a ) || !is_array( $b ) || empty( $a['database'] ) || empty( $b['database'] ) )
            return false;
        foreach ( array( 'format', 'engine', 'database', 'storage', 'settings', 'code' ) as $key )
            if ( !array_key_exists( $key, $a ) || !array_key_exists( $key, $b ) || (string)$a[$key] !== (string)$b[$key] )
                return false;
        return true;
    }

    /**
     * The newest migration caption (pre or post) whose marker equals the given one, or null.
     *
     * @param array $marker
     * @return array|null as expUpgradeCaption::describe()
     */
    public function reusable( array $marker )
    {
        foreach ( $this->captions() as $caption )
        {
            if ( !in_array( $caption['kind'], array( self::PRE, self::POST ), true ) || empty( $caption['upgrade_part'] ) )
                continue;
            $file = rtrim( $caption['dir'], '/' ) . '/' . self::MARKER;
            $stored = is_file( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
            if ( self::sameMarker( $stored, $marker ) )
                return $caption;
        }
        return null;
    }

    /**
     * A digest of the files under a folder: relative path, size and modification time of each. 'none' without the
     * folder. Symbolic links are not followed.
     *
     * @param string $dir
     * @return string
     */
    public static function filesMarker( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        if ( $dir === '' || !is_dir( $dir ) )
            return 'none';
        $lines = array();
        // a folder that cannot be read is left out instead of ending the whole marker (an unreadable var/log of a test
        // fixture below var/tmp made every caption of the run fail, 2026-11-01)
        $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                             RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );
        foreach ( $it as $file )
        {
            if ( $file->isLink() || !$file->isFile() )
                continue;
            $lines[] = substr( $file->getPathname(), strlen( $dir ) + 1 ) . "\t" . $file->getSize() . "\t" . $file->getMTime();
        }
        sort( $lines, SORT_STRING );
        return hash( 'sha256', implode( "\n", $lines ) );
    }

    /**
     * A digest that changes whenever a table the check does not ignore changes, or null when the engine gives none
     * that is exact:
     *
     *   sqlite      every row of every table (none for a file over SQLITE_MARKER_MAX_BYTES)
     *   postgresql  the rows of every table and the sum of their row versions (xmin)
     *   mysql       CHECKSUM TABLE of every table
     *
     * @return string|null
     */
    protected function databaseMarker()
    {
        $db = $this->context->db();
        $engine = $this->context->engine();
        $ignore = array_map( 'strtolower', (array)$this->settings['ignore-tables'] );
        $keep = function ( $table ) use ( $ignore ) {
            return !self::ignoredTable( $table, $ignore );
        };
        switch ( $engine )
        {
            case 'sqlite':
                $file = eZSQLite3DB::filePath( (string)$db->DB );
                if ( $file !== '' && $file[0] !== '/' )
                    $file = rtrim( $this->context->root(), '/' ) . '/' . $file;
                if ( is_file( $file ) && filesize( $file ) > self::SQLITE_MARKER_MAX_BYTES )
                    return null;
                $hash = hash_init( 'sha256' );
                $tables = (array)$db->arrayQuery( "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name" );
                foreach ( $tables as $row )
                {
                    $name = (string)array_values( (array)$row )[0];
                    if ( !$keep( $name ) )
                        continue;
                    hash_update( $hash, "\n#" . $name . "\n" );
                    foreach ( (array)$db->arrayQuery( 'SELECT * FROM "' . str_replace( '"', '""', $name ) . '"' ) as $r )
                        hash_update( $hash, serialize( array_values( (array)$r ) ) );
                }
                return 'sqlite:' . hash_final( $hash );
            case 'postgresql':
                // per table the rows and the sum of their row versions (xmin): an insert or update writes a new
                // version, a delete changes the count; freezing keeps xmin (9.4+)
                $tables = array_values( array_filter( (array)expKernelTableSchema::tables( $db ), $keep ) );
                if ( !$tables )
                    return null;
                sort( $tables, SORT_STRING );
                $lines = array();
                foreach ( $tables as $table )
                {
                    $rows = expKernelTableSchema::tryArrayQuery( $db, 'SELECT count(*) AS n, coalesce(sum(xmin::text::bigint), 0) AS x FROM "'
                                                                      . str_replace( '"', '""', $table ) . '"' );
                    if ( !$rows )
                        return null;
                    $lines[] = $table . "\t" . implode( "\t", array_values( (array)$rows[0] ) );
                }
                return 'postgresql:' . hash( 'sha256', implode( "\n", $lines ) );
            case 'mysql':
                $tables = array_values( array_filter( (array)expKernelTableSchema::tables( $db ), $keep ) );
                if ( !$tables )
                    return null;
                sort( $tables, SORT_STRING );
                $lines = array();
                foreach ( array_chunk( $tables, 50 ) as $chunk )
                {
                    $sql = 'CHECKSUM TABLE ' . implode( ', ', array_map( function ( $t ) { return '`' . str_replace( '`', '``', $t ) . '`'; }, $chunk ) );
                    $rows = expKernelTableSchema::tryArrayQuery( $db, $sql );
                    if ( !$rows )
                        return null;
                    foreach ( $rows as $row )
                    {
                        $row = array_values( (array)$row );
                        if ( !isset( $row[1] ) || $row[1] === null )
                            return null;
                        $lines[] = $row[0] . "\t" . $row[1];
                    }
                }
                return 'mysql:' . hash( 'sha256', implode( "\n", $lines ) );
        }
        return null;
    }

    /**
     * Whether the reuse check leaves a table out: its name in the list, or matching an entry that ends in "*". Pure.
     *
     * @param string $table
     * @param string[] $ignore lower case
     * @return bool
     */
    public static function ignoredTable( $table, array $ignore )
    {
        $table = strtolower( (string)$table );
        foreach ( $ignore as $pattern )
        {
            $pattern = strtolower( (string)$pattern );
            if ( $pattern === $table || ( substr( $pattern, -1 ) === '*' && strpos( $table, substr( $pattern, 0, -1 ) ) === 0 ) )
                return true;
        }
        return false;
    }

    // ---- code that was not committed ----------------------------------------------------------------------------

    /**
     * The changes that are not committed in the code trees ([CaptionSettings] CodeTreeList) of the installation's
     * checkout, and in every checkout of its own one level inside them (extension/<name>, design/<name>).
     *
     * @param string[] $problems what could not be checked is added here
     * @return array[] one per checkout with changes: label, path, head, changed (files), untracked (files)
     */
    public function uncommitted( array &$problems = array() )
    {
        $root = rtrim( $this->context->root(), '/' );
        $trees = array_values( array_filter( (array)$this->settings['trees'], function ( $t ) {
            return (string)$t !== '' && strpos( (string)$t, '..' ) === false && (string)$t[0] !== '/';
        } ) );
        if ( !$trees || !file_exists( "$root/.git" ) )
            return array();
        if ( !$this->gitAvailable() )
        {
            $problems[] = 'the code trees (' . implode( ', ', $trees ) . ') could not be checked for changes that are not committed: git_manager is not active';
            return array();
        }
        $nested = array();
        foreach ( $trees as $tree )
            foreach ( glob( "$root/$tree/*/.git" ) ?: array() as $git )
                $nested[] = substr( dirname( $git ), strlen( $root ) + 1 );
        $out = array();
        $rootChanges = $this->status( $root, $trees, $problems );
        if ( $rootChanges !== null )
        {
            foreach ( array( 'changed', 'untracked' ) as $kind )
                $rootChanges[$kind] = array_values( array_filter( $rootChanges[$kind], function ( $file ) use ( $nested ) {
                    foreach ( $nested as $n )
                        if ( $file === $n || $file === "$n/" || strpos( $file, "$n/" ) === 0 )
                            return false;
                    return true;
                } ) );
            if ( $rootChanges['changed'] || $rootChanges['untracked'] )
                $out[] = array( 'label' => 'root', 'path' => $root ) + $rootChanges;
        }
        foreach ( $nested as $rel )
        {
            $changes = $this->status( "$root/$rel", array(), $problems );
            if ( $changes !== null && ( $changes['changed'] || $changes['untracked'] ) )
                $out[] = array( 'label' => $rel, 'path' => "$root/$rel" ) + $changes;
        }
        return $out;
    }

    /**
     * Parses git status --porcelain=v1 -z. Pure.
     *
     * @param string $output
     * @return array changed (tracked files with changes, staged or not), untracked
     */
    public static function parseStatus( $output )
    {
        $changed = array();
        $untracked = array();
        $items = explode( "\0", (string)$output );
        for ( $i = 0; $i < count( $items ); $i++ )
        {
            $item = $items[$i];
            if ( strlen( $item ) < 4 )
                continue;
            $code = substr( $item, 0, 2 );
            $path = substr( $item, 3 );
            if ( $code === '??' )
                $untracked[] = $path;
            else if ( $code !== '!!' )
            {
                $changed[] = $path;
                // a rename or copy is followed by the old name
                if ( $code[0] === 'R' || $code[0] === 'C' )
                    $i++;
            }
        }
        return array( 'changed' => $changed, 'untracked' => $untracked );
    }

    /**
     * @param string $path a checkout
     * @param string[] $trees limit to these folders ('' none)
     * @param string[] $problems
     * @return array|null head, changed, untracked; null when git cannot tell
     */
    protected function status( $path, array $trees, array &$problems )
    {
        $args = array( '--no-optional-locks', 'status', '--porcelain=v1', '-z', '--untracked-files=all' );
        if ( $trees )
            $args = array_merge( $args, array( '--' ), $trees );
        $status = $this->git( $path, $args );
        if ( !$status['ok'] )
        {
            $problems[] = "$path: git status failed (exit {$status['exit']}): the changes that are not committed could not be checked";
            return null;
        }
        $head = $this->git( $path, array( 'rev-parse', '--verify', 'HEAD' ) );
        return array( 'head' => $head['ok'] ? trim( $head['output'] ) : '' ) + self::parseStatus( $status['output'] );
    }

    /**
     * Keeps the changes that were not committed in a caption: a patch of each checkout and a copy of its new files.
     *
     * @param string $dir the caption
     * @param array[] $uncommitted as uncommitted()
     * @return string[] messages
     */
    protected function keepUncommitted( $dir, array $uncommitted )
    {
        $base = $dir . '/' . self::UNCOMMITTED_DIR;
        eZDir::mkdir( $base, 0700, true );
        $list = array();
        $messages = array();
        foreach ( $uncommitted as $repo )
        {
            $name = preg_replace( '/[^A-Za-z0-9_.-]+/', '-', (string)$repo['label'] );
            $entry = array( 'label' => $repo['label'], 'path' => $repo['path'], 'head' => $repo['head'], 'changed' => $repo['changed'],
                            'untracked' => $repo['untracked'], 'patch' => '', 'files' => '' );
            if ( $repo['changed'] )
            {
                $args = array( 'diff', '--binary', 'HEAD', '--' );
                $args = array_merge( $args, $repo['changed'] );
                $diff = $this->git( $repo['path'], $args, 300 );
                if ( !$diff['ok'] )
                    throw new RuntimeException( $repo['label'] . ': git diff failed' );
                file_put_contents( "$base/$name.patch", $diff['output'] );
                $entry['patch'] = self::UNCOMMITTED_DIR . "/$name.patch";
            }
            if ( $repo['untracked'] )
            {
                $target = "$base/$name";
                foreach ( $repo['untracked'] as $file )
                {
                    if ( strpos( $file, '..' ) !== false || $file === '' || $file[0] === '/' )
                        continue;
                    $from = rtrim( $repo['path'], '/' ) . '/' . $file;
                    if ( !is_file( $from ) || is_link( $from ) )
                        continue;
                    eZDir::mkdir( dirname( "$target/$file" ), 0700, true );
                    if ( !copy( $from, "$target/$file" ) )
                        throw new RuntimeException( "cannot copy $from into the caption" );
                }
                $entry['files'] = self::UNCOMMITTED_DIR . "/$name";
            }
            $list[] = $entry;
            $messages[] = $repo['label'] . ': ' . count( $repo['changed'] ) . ' changed and ' . count( $repo['untracked'] ) . ' new file(s) kept in the caption';
        }
        $this->writeJson( $dir . '/' . self::UNCOMMITTED, array( 'format' => self::UNCOMMITTED_FORMAT, 'created' => date( 'c' ), 'repos' => $list ) );
        return $messages;
    }

    // ---- what a test replaces ---------------------------------------------------------------------------------

    /**
     * expUpgradeCaption::take() with the run's context.
     *
     * @param string $kind
     * @param string $label
     * @return array
     */
    protected function takeCaption( $kind, $label )
    {
        return expUpgradeCaption::take( $kind, $label, $this->context );
    }

    /** @return array[] every caption, newest first (expUpgradeCaption::all()) */
    protected function captions()
    {
        return expUpgradeCaption::all( $this->context->option( 'backup-dir' ) );
    }

    /**
     * expUpgradeRollback::run().
     *
     * @param string $captionId
     * @param array $options
     * @return array the report
     */
    protected function rollback( $captionId, array $options )
    {
        return expUpgradeRollback::run( $captionId, $options );
    }

    /** @return bool */
    protected function gitAvailable()
    {
        return expUpgradeGit::available();
    }

    /**
     * expUpgradeGit::git().
     *
     * @param string $path
     * @param string[] $arguments
     * @param int $seconds
     * @return array ok, exit, output
     */
    protected function git( $path, array $arguments, $seconds = 120 )
    {
        return expUpgradeGit::git( $path, $arguments, $seconds );
    }

    /**
     * The audit event system.migration.caption.
     *
     * @param array $result
     * @param string $verb take, reuse
     */
    protected function audit( array $result, $verb )
    {
        $this->context->audit( 'system.migration.caption', array(
            'object' => array( 'type' => 'caption', 'id' => (string)$result['id'] ),
            'verb' => $verb,
            'result' => !empty( $result['ok'] ) ? 'success' : 'failure',
            'reason' => !empty( $result['ok'] ) ? '' : mb_substr( implode( '; ', (array)$result['messages'] ), 0, 200 ),
            'x' => array( 'kind' => (string)$result['kind'], 'source' => (string)$result['source'], 'run_id' => $this->context->runId(),
                          'reused' => !empty( $result['reused'] ) ),
        ) );
    }

    /**
     * @param string $file
     * @param array $data
     */
    protected function writeJson( $file, array $data )
    {
        if ( !is_dir( dirname( $file ) ) )
            eZDir::mkdir( dirname( $file ), 0700, true );
        if ( file_put_contents( $file, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" ) === false )
            throw new RuntimeException( "cannot write $file" );
    }
}
