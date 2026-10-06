<?php
/**
 * File containing the expFileConsistencyReport class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Compares the files of an installation with the checksum lists that ship with it: share/filelist.md5 of
 * Exponential and the share/filelist.md5 an extension may carry of its own. The upgrade check of the
 * administration (setup/systemupgrade) and the command line check (bin/php/checkmanifest.php) both use it, so they
 * always say the same about the same tree.
 *
 * It only reads. It needs no kernel, no settings and no database, so the command line check can include this file
 * on its own.
 *
 * What it finds, per file (the states):
 * - modified:   listed, present, with another checksum
 * - missing:    listed, not there
 * - unreadable: listed, present, but no checksum could be read (a directory, no permission)
 * - unlisted:   tracked by git in the manifest's directory, not listed (only where a git file list is given)
 * and per manifest line:
 * - malformed:  a line that is not "<32 hex digits><two spaces><relative path>", a path outside the installation,
 *               or a path listed twice
 * - unordered:  a line that sorts before the one above it (only for manifests that are kept sorted)
 *
 * Modified, missing and unreadable files are problems: a check with any of them does not pass. Unlisted files,
 * malformed and unordered lines are notes: they are shown and counted, and the check still passes.
 *
 * Guide: doc/guides/upgrade-check.md
 *
 * @package kernel
 */
class expFileConsistencyReport
{
    /** The manifest of Exponential, relative to the root of the installation */
    const MANIFEST_FILE = 'share/filelist.md5';

    const STATE_MODIFIED = 'modified';
    const STATE_MISSING = 'missing';
    const STATE_UNREADABLE = 'unreadable';
    const STATE_UNLISTED = 'unlisted';
    const STATE_MALFORMED = 'malformed';
    const STATE_UNORDERED = 'unordered';

    /** The states that fail the check */
    const PROBLEM_STATES = array( self::STATE_MODIFIED, self::STATE_MISSING, self::STATE_UNREADABLE );

    /** Every state, in the order they are shown */
    const STATES = array( self::STATE_MODIFIED, self::STATE_MISSING, self::STATE_UNREADABLE, self::STATE_UNLISTED,
                          self::STATE_MALFORMED, self::STATE_UNORDERED );

    /**
     * Paths of the root that are never expected in its manifest: the manifest itself, what the installation writes
     * while it runs, and an extension that once shipped a list of its own. A directory ends in "/".
     */
    const ROOT_EXCLUDES = array( 'share/filelist.md5', 'var/', 'extension/ezoe/' );

    /** The overall results */
    const STATUS_OK = 'ok';
    const STATUS_DIFFERENCES = 'differences';
    const STATUS_FAILED = 'failed';

    /** @var string the root of the installation, without a trailing slash */
    private $root;

    /** @var array the manifests to check, in order: id => array( label, kind, file, base, sorted ) */
    private $manifests = array();

    /** @var array id => list of tracked paths (relative to the manifest's base) and their excludes */
    private $tracked = array();

    /** @var callable|null returns the md5 of a file, or false; md5_file() when not set */
    private $hasher;

    /** @var array|null the result of run() */
    private $result;

    /**
     * @param string $root The root of the installation; paths in the manifests are relative to it
     * @param callable|null $hasher function( string $file ): string|false, for tests; md5_file() by default
     */
    public function __construct( $root = '.', $hasher = null )
    {
        $root = rtrim( (string)$root, '/' );
        $this->root = $root === '' ? '/' : $root;
        $this->hasher = $hasher;
    }

    /**
     * Adds a manifest to check.
     *
     * @param string $id A short unique name: 'exponential' for the root, the extension's name for an extension
     * @param string $file The manifest, relative to the root
     * @param string $base The directory its paths are relative to, relative to the root, '' or ending in '/'
     * @param string $label What the page calls it
     * @param string $kind 'root' or 'extension'
     * @param bool $sorted Whether its lines are kept in sorted order (the root manifest is)
     * @return expFileConsistencyReport
     */
    public function addManifest( $id, $file, $base = '', $label = '', $kind = 'root', $sorted = false )
    {
        $base = (string)$base;
        if ( $base !== '' && substr( $base, -1 ) !== '/' )
            $base .= '/';
        $this->manifests[(string)$id] = array( 'label' => $label !== '' ? (string)$label : (string)$id, 'kind' => (string)$kind,
                                               'file' => (string)$file, 'base' => $base, 'sorted' => (bool)$sorted );
        $this->result = null;
        return $this;
    }

    /**
     * Gives the files git tracks under a manifest's base, so files missing from the manifest are found.
     *
     * @param string $id The manifest's id
     * @param string[] $paths Relative to the manifest's base
     * @param string[] $excludes Paths and directories (ending in "/") that do not belong in the manifest
     * @return expFileConsistencyReport
     */
    public function setTrackedFiles( $id, array $paths, array $excludes = array() )
    {
        $this->tracked[(string)$id] = array( 'paths' => $paths, 'excludes' => $excludes );
        $this->result = null;
        return $this;
    }

    /**
     * The manifest of the root and every active extension's own manifest, as the upgrade check reads them.
     *
     * @param string $root
     * @param string[] $extensionDirectories name => directory relative to the root (extension/<name>)
     * @return expFileConsistencyReport
     */
    public static function forInstallation( $root, array $extensionDirectories = array() )
    {
        $report = new self( $root );
        $report->addManifest( 'exponential', self::MANIFEST_FILE, '', 'Exponential', 'root', true );
        foreach ( $extensionDirectories as $name => $directory )
        {
            $directory = rtrim( (string)$directory, '/' ) . '/';
            if ( is_file( $report->full( $directory . self::MANIFEST_FILE ) ) )
                $report->addManifest( (string)$name, $directory . self::MANIFEST_FILE, $directory, (string)$name, 'extension', false );
        }
        return $report;
    }

    /**
     * Every directory under extension/ of the root that carries its own manifest, active or not: what the command
     * line check reads with --extensions, since it runs without settings.
     *
     * @param string $root
     * @return string[] name => extension/<name>
     */
    public static function extensionDirectoriesWithManifest( $root )
    {
        $found = array();
        $root = rtrim( (string)$root, '/' );
        foreach ( (array)glob( $root . '/extension/*/' . self::MANIFEST_FILE ) as $file )
        {
            $name = basename( dirname( dirname( $file ) ) );
            $found[$name] = 'extension/' . $name;
        }
        ksort( $found, SORT_STRING );
        return $found;
    }

    /**
     * The paths git tracks in $directory, or false when it is no git checkout or git cannot be run. Only reads.
     *
     * @param string $directory
     * @return string[]|false
     */
    public static function gitTrackedFiles( $directory )
    {
        if ( !is_dir( rtrim( $directory, '/' ) . '/.git' ) && !is_file( rtrim( $directory, '/' ) . '/.git' ) )
            return false;
        if ( !function_exists( 'shell_exec' ) || in_array( 'shell_exec', array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) ), true ) )
            return false;
        $output = @shell_exec( 'git -C ' . escapeshellarg( $directory ) . ' ls-files -z 2>/dev/null' );
        if ( !is_string( $output ) || $output === '' )
            return false;
        return array_values( array_filter( explode( "\0", $output ), 'strlen' ) );
    }

    /**
     * Splits manifest text into its header (lines "key: value" before the first entry, as an extension's manifest
     * starts), its entries and its malformed lines. A line ending in "\r\n" is read as if it ended in "\n".
     *
     * @param string $text
     * @return array 'header' => key => value, 'entries' => list of array( 'md5', 'path', 'line' ),
     *               'malformed' => list of array( 'line', 'text', 'reason' ) with reason 'format', 'unsafe_path' or
     *               'duplicate'
     */
    public static function parseManifest( $text )
    {
        $header = array();
        $entries = array();
        $malformed = array();
        $seen = array();
        $lines = explode( "\n", (string)$text );
        if ( end( $lines ) === '' )
            array_pop( $lines );
        foreach ( $lines as $index => $line )
        {
            $number = $index + 1;
            if ( substr( $line, -1 ) === "\r" )
                $line = substr( $line, 0, -1 );
            if ( trim( $line ) === '' )
                continue;
            if ( preg_match( '/^([0-9a-fA-F]{32})  (.+)$/', $line, $match ) )
            {
                $path = $match[2];
                if ( !self::isSafePath( $path ) )
                {
                    $malformed[] = array( 'line' => $number, 'text' => $line, 'reason' => 'unsafe_path' );
                    continue;
                }
                if ( isset( $seen[$path] ) )
                {
                    $malformed[] = array( 'line' => $number, 'text' => $line, 'reason' => 'duplicate' );
                    continue;
                }
                $seen[$path] = true;
                $entries[] = array( 'md5' => strtolower( $match[1] ), 'path' => $path, 'line' => $number );
                continue;
            }
            if ( !$entries && preg_match( '/^([a-z][a-z0-9_]*):[ \t]*(.*)$/', $line, $match ) )
            {
                $header[$match[1]] = trim( $match[2] );
                continue;
            }
            $malformed[] = array( 'line' => $number, 'text' => $line, 'reason' => 'format' );
        }
        return array( 'header' => $header, 'entries' => $entries, 'malformed' => $malformed );
    }

    /**
     * Whether a manifest path stays inside the directory it is relative to.
     *
     * @param string $path
     * @return bool
     */
    public static function isSafePath( $path )
    {
        $path = (string)$path;
        if ( $path === '' || $path[0] === '/' || $path[0] === '\\' || strpos( $path, "\0" ) !== false || preg_match( '/^[A-Za-z]:/', $path ) )
            return false;
        foreach ( preg_split( '#[/\\\\]#', $path ) as $segment )
        {
            if ( $segment === '..' )
                return false;
        }
        return true;
    }

    /**
     * Whether $path belongs in a manifest that leaves out $excludes.
     *
     * @param string $path
     * @param string[] $excludes
     * @return bool
     */
    public static function covers( $path, array $excludes )
    {
        foreach ( $excludes as $exclude )
        {
            if ( $path === $exclude || ( substr( $exclude, -1 ) === '/' && strpos( $path, $exclude ) === 0 ) )
                return false;
        }
        if ( strpos( $path, '/__pycache__/' ) !== false || strpos( $path, '__pycache__/' ) === 0 || substr( $path, -4 ) === '.pyc' )
            return false;
        return true;
    }

    /**
     * Runs the check: every listed file is read once.
     *
     * @return array see result()
     */
    public function run()
    {
        $started = microtime( true );
        $items = array();
        $manifests = array();
        $totals = array_fill_keys( self::STATES, 0 );
        $totals['matching'] = 0;
        $checked = 0;
        $status = self::STATUS_OK;
        $failure = '';

        foreach ( $this->manifests as $id => $manifest )
        {
            $file = $this->full( $manifest['file'] );
            $info = array( 'id' => $id, 'label' => $manifest['label'], 'kind' => $manifest['kind'], 'file' => $manifest['file'],
                           'base' => $manifest['base'], 'exists' => false, 'readable' => false, 'entries' => 0,
                           'header' => array(), 'mtime' => 0, 'unlisted_checked' => false, 'problems' => 0,
                           'counts' => array_fill_keys( self::STATES, 0 ) + array( 'matching' => 0 ) );
            $info['exists'] = is_file( $file );
            $text = $info['exists'] ? @file_get_contents( $file ) : false;
            if ( !is_string( $text ) )
            {
                $manifests[] = $info;
                if ( $manifest['kind'] === 'root' )
                {
                    $status = self::STATUS_FAILED;
                    $failure = $info['exists'] ? 'unreadable_manifest' : 'missing_manifest';
                }
                else if ( $status === self::STATUS_OK )
                {
                    $status = self::STATUS_DIFFERENCES;
                }
                continue;
            }
            $info['readable'] = true;
            $info['mtime'] = (int)@filemtime( $file );
            $parsed = self::parseManifest( $text );
            $info['header'] = $parsed['header'];
            $info['entries'] = count( $parsed['entries'] );
            if ( $manifest['kind'] === 'root' && !$parsed['entries'] )
            {
                $status = self::STATUS_FAILED;
                $failure = 'empty_manifest';
            }

            $listed = array();
            $previous = '';
            foreach ( $parsed['entries'] as $entry )
            {
                $relative = $entry['path'];
                $path = $manifest['base'] . $relative;
                $listed[$relative] = true;
                $checked++;
                $current = $this->hash( $this->full( $path ) );
                $state = null;
                if ( $current === false )
                {
                    $state = file_exists( $this->full( $path ) ) || is_link( $this->full( $path ) ) ? self::STATE_UNREADABLE : self::STATE_MISSING;
                }
                else if ( $current !== $entry['md5'] )
                {
                    $state = self::STATE_MODIFIED;
                }
                if ( $state !== null )
                {
                    $items[] = $this->item( $state, $path, $id, $entry['md5'], $current === false ? '' : $current, $entry['line'], '' );
                    $info['counts'][$state]++;
                }
                else
                {
                    $info['counts']['matching']++;
                }
                if ( $manifest['sorted'] )
                {
                    if ( strcmp( $previous, $relative ) > 0 )
                    {
                        $items[] = $this->item( self::STATE_UNORDERED, $path, $id, '', '', $entry['line'], $manifest['base'] . $previous );
                        $info['counts'][self::STATE_UNORDERED]++;
                    }
                    $previous = $relative;
                }
            }

            foreach ( $parsed['malformed'] as $bad )
            {
                $items[] = $this->item( self::STATE_MALFORMED, $manifest['file'], $id, '', '', $bad['line'], $bad['reason'], $bad['text'] );
                $info['counts'][self::STATE_MALFORMED]++;
            }

            if ( isset( $this->tracked[$id] ) )
            {
                $info['unlisted_checked'] = true;
                foreach ( $this->tracked[$id]['paths'] as $relative )
                {
                    if ( isset( $listed[$relative] ) || !self::covers( $relative, $this->tracked[$id]['excludes'] ) )
                        continue;
                    $path = $manifest['base'] . $relative;
                    if ( !is_file( $this->full( $path ) ) )
                        continue;
                    $items[] = $this->item( self::STATE_UNLISTED, $path, $id, '', '', 0, '' );
                    $info['counts'][self::STATE_UNLISTED]++;
                }
            }

            foreach ( $info['counts'] as $state => $count )
                $totals[$state] += $count;
            foreach ( self::PROBLEM_STATES as $state )
                $info['problems'] += $info['counts'][$state];
            if ( $info['problems'] > 0 && $status === self::STATUS_OK )
                $status = self::STATUS_DIFFERENCES;
            $manifests[] = $info;
        }

        if ( !$this->manifests )
        {
            $status = self::STATUS_FAILED;
            $failure = 'missing_manifest';
        }

        $problems = 0;
        foreach ( self::PROBLEM_STATES as $state )
            $problems += $totals[$state];

        $this->result = array(
            'status' => $status,
            'failure' => $failure,
            'checked' => $checked,
            'problems' => $problems,
            'notes' => $totals[self::STATE_UNLISTED] + $totals[self::STATE_MALFORMED] + $totals[self::STATE_UNORDERED],
            'counts' => $totals,
            'manifests' => $manifests,
            'items' => $items,
            'areas' => self::areas( $items ),
            'seconds' => round( microtime( true ) - $started, 3 ),
        );
        return $this->result;
    }

    /**
     * The result of the last run() (running it when it has not run):
     * - status:    'ok', 'differences' (something to look at) or 'failed' (the manifest of Exponential is missing,
     *              unreadable or empty)
     * - failure:   '', 'missing_manifest', 'unreadable_manifest' or 'empty_manifest'
     * - checked:   the number of listed files read
     * - problems:  modified + missing + unreadable files
     * - notes:     unlisted files + malformed and unordered lines
     * - counts:    state => number, and 'matching'
     * - manifests: list of array( id, label, kind, file, base, exists, readable, entries, header, mtime, counts,
     *              problems, unlisted_checked )
     * - items:     list of array( state, path, manifest, area, expected, actual, line, detail, text )
     * - areas:     list of array( name, count ): where the items are, 'kernel' or 'extension/<name>'
     * - seconds:   how long it took
     *
     * @return array
     */
    public function result()
    {
        return $this->result !== null ? $this->result : $this->run();
    }

    /**
     * The paths with a problem (modified, missing, unreadable), as the upgrade check listed them before it grouped
     * them: the template variable md5_result.
     *
     * @return string[]
     */
    public function problemPaths()
    {
        $paths = array();
        foreach ( $this->result()['items'] as $item )
        {
            if ( in_array( $item['state'], self::PROBLEM_STATES, true ) )
                $paths[] = $item['path'];
        }
        return $paths;
    }

    /**
     * The items of one state.
     *
     * @param string $state
     * @return array
     */
    public function itemsOf( $state )
    {
        return array_values( array_filter( $this->result()['items'], function ( $item ) use ( $state ) {
            return $item['state'] === $state;
        } ) );
    }

    /**
     * The report as CSV, one line per finding, with a header line. Cells that a spreadsheet would run as a formula
     * are quoted with a leading apostrophe.
     *
     * @param resource $handle
     * @param bool $bom Whether to start with the UTF-8 byte order mark
     */
    public function writeCsv( $handle, $bom = true )
    {
        if ( $bom )
            fwrite( $handle, "\xEF\xBB\xBF" );
        self::csvLine( $handle, array( 'manifest', 'area', 'state', 'path', 'expected_md5', 'actual_md5', 'line', 'detail' ) );
        foreach ( $this->result()['items'] as $item )
        {
            self::csvLine( $handle, array( $item['manifest'], $item['area'], $item['state'], $item['path'], $item['expected'],
                                           $item['actual'], $item['line'] ? (string)$item['line'] : '', $item['detail'] ) );
        }
    }

    /**
     * The report as plain text: a summary, then the findings grouped by manifest and state.
     *
     * @param string $title The first line
     * @return string
     */
    public function text( $title = 'File consistency check' )
    {
        $result = $this->result();
        $out = $title . "\n" . str_repeat( '=', strlen( $title ) ) . "\n\n";
        $out .= 'Result: ' . $result['status'] . ( $result['failure'] !== '' ? ' (' . $result['failure'] . ')' : '' ) . "\n";
        $out .= 'Files checked: ' . $result['checked'] . ', matching: ' . $result['counts']['matching'] . "\n";
        foreach ( self::STATES as $state )
            $out .= ucfirst( $state ) . ': ' . $result['counts'][$state] . "\n";
        $out .= 'Time: ' . sprintf( '%.2f', $result['seconds'] ) . " s\n";
        foreach ( $result['manifests'] as $manifest )
        {
            $out .= "\n" . $manifest['label'] . ' (' . $manifest['file'] . ')';
            if ( isset( $manifest['header']['version'] ) )
                $out .= ', version ' . $manifest['header']['version'];
            $out .= ': ' . ( $manifest['readable'] ? $manifest['entries'] . ' files listed' : ( $manifest['exists'] ? 'unreadable' : 'missing' ) ) . "\n";
            foreach ( self::STATES as $state )
            {
                $lines = array();
                foreach ( $result['items'] as $item )
                {
                    if ( $item['manifest'] === $manifest['id'] && $item['state'] === $state )
                        $lines[] = '  ' . self::describe( $item );
                }
                if ( $lines )
                    $out .= ' ' . ucfirst( $state ) . ' (' . count( $lines ) . "):\n" . implode( "\n", $lines ) . "\n";
            }
        }
        return $out;
    }

    /**
     * One finding in words, in the wording of bin/php/checkmanifest.php.
     *
     * @param array $item
     * @return string
     */
    public static function describe( array $item )
    {
        switch ( $item['state'] )
        {
            case self::STATE_MODIFIED:
                return $item['path'] . ' has checksum ' . $item['expected'] . ' in the manifest, the file has ' . $item['actual'];
            case self::STATE_MISSING:
                return $item['path'] . ' is listed but does not exist';
            case self::STATE_UNREADABLE:
                return $item['path'] . ' is listed but cannot be read';
            case self::STATE_UNLISTED:
                return $item['path'] . ' is in git but not listed';
            case self::STATE_UNORDERED:
                return $item['path'] . ' is out of order (after ' . $item['detail'] . ')';
            case self::STATE_MALFORMED:
                $why = array( 'format' => 'is not "<md5>  <path>"', 'unsafe_path' => 'names a path outside the installation',
                              'duplicate' => 'lists a file a second time' );
                return $item['path'] . ' line ' . $item['line'] . ' ' . ( isset( $why[$item['detail']] ) ? $why[$item['detail']] : 'is malformed' );
        }
        return $item['path'];
    }

    /**
     * Where a path lies: 'extension/<name>' or 'kernel'.
     *
     * @param string $path
     * @return string
     */
    public static function areaOf( $path )
    {
        if ( preg_match( '#^extension/([^/]+)/#', $path, $match ) )
            return 'extension/' . $match[1];
        return 'kernel';
    }

    /**
     * @param array $items
     * @return array list of array( name, count ), the kernel first
     */
    private static function areas( array $items )
    {
        $areas = array();
        foreach ( $items as $item )
            $areas[$item['area']] = ( isset( $areas[$item['area']] ) ? $areas[$item['area']] : 0 ) + 1;
        uksort( $areas, function ( $a, $b ) {
            if ( $a === 'kernel' || $b === 'kernel' )
                return $a === 'kernel' ? ( $b === 'kernel' ? 0 : -1 ) : 1;
            return strcmp( $a, $b );
        } );
        $list = array();
        foreach ( $areas as $name => $count )
            $list[] = array( 'name' => $name, 'count' => $count );
        return $list;
    }

    /**
     * What a manifest says about itself without checking any file: whether it is there, when it was written, how
     * many files it lists and where they lie, its header (an extension's name, version and files_count) and how many
     * of its lines are malformed. Cheap enough for every page view.
     *
     * @param string $file
     * @return array 'exists', 'readable', 'mtime', 'entries', 'header', 'malformed', 'areas' (area => entries)
     */
    public static function summarize( $file )
    {
        $summary = array( 'exists' => is_file( $file ), 'readable' => false, 'mtime' => 0, 'entries' => 0, 'header' => array(),
                          'malformed' => 0, 'areas' => array() );
        $text = $summary['exists'] ? @file_get_contents( $file ) : false;
        if ( !is_string( $text ) )
            return $summary;
        $parsed = self::parseManifest( $text );
        $summary['readable'] = true;
        $summary['mtime'] = (int)@filemtime( $file );
        $summary['entries'] = count( $parsed['entries'] );
        $summary['header'] = $parsed['header'];
        $summary['malformed'] = count( $parsed['malformed'] );
        foreach ( $parsed['entries'] as $entry )
        {
            $area = self::areaOf( $entry['path'] );
            $summary['areas'][$area] = ( isset( $summary['areas'][$area] ) ? $summary['areas'][$area] : 0 ) + 1;
        }
        return $summary;
    }

    /**
     * When git last committed a change to $file in the checkout $directory, as a Unix time; 0 when there is no git.
     *
     * @param string $directory
     * @param string $file Relative to $directory
     * @return int
     */
    public static function gitLastChange( $directory, $file )
    {
        if ( !is_dir( rtrim( $directory, '/' ) . '/.git' ) && !is_file( rtrim( $directory, '/' ) . '/.git' ) )
            return 0;
        if ( !function_exists( 'shell_exec' ) || in_array( 'shell_exec', array_map( 'trim', explode( ',', (string)ini_get( 'disable_functions' ) ) ), true ) )
            return 0;
        $output = @shell_exec( 'git -C ' . escapeshellarg( $directory ) . ' log -1 --format=%ct -- ' . escapeshellarg( $file ) . ' 2>/dev/null' );
        return is_string( $output ) && ctype_digit( trim( $output ) ) ? (int)trim( $output ) : 0;
    }

    /** A path of a manifest as a path on disk: relative ones are relative to the root */
    private function full( $path )
    {
        if ( $path !== '' && $path[0] === '/' )
            return $path;
        return $this->root . '/' . $path;
    }

    private function item( $state, $path, $manifest, $expected, $actual, $line, $detail, $text = '' )
    {
        return array( 'state' => $state, 'path' => $path, 'manifest' => $manifest, 'area' => self::areaOf( $path ),
                      'expected' => $expected, 'actual' => $actual, 'line' => (int)$line, 'detail' => (string)$detail,
                      'text' => (string)$text );
    }

    private function hash( $file )
    {
        if ( $this->hasher !== null )
            return call_user_func( $this->hasher, $file );
        $sum = @md5_file( $file );
        // a directory reads as empty on some systems: only then is it worth a stat
        if ( $sum === 'd41d8cd98f00b204e9800998ecf8427e' && is_dir( $file ) )
            return false;
        return $sum;
    }

    private static function csvLine( $handle, array $cells )
    {
        foreach ( $cells as $index => $cell )
        {
            $cell = (string)$cell;
            if ( $cell !== '' && strpbrk( $cell[0], "=+-@\t\r" ) !== false && !is_numeric( $cell ) )
                $cell = "'" . $cell;
            $cells[$index] = '"' . str_replace( '"', '""', $cell ) . '"';
        }
        fwrite( $handle, implode( ',', $cells ) . "\r\n" );
    }
}
