<?php
/**
 * The log viewer (Setup > Log files, setup/logs; doc/eng-us/features/6.0/log-viewer.md): what its page, its feed and its
 * command line use. It joins the parts: the files (expLogFileLocator), reading (expLogTail), entries
 * (expLogParser), searching (expLogSearch), masking (expLogRedactor), and the policies and the audit.
 *
 * Policies (module setup): logs (read), managelogs (rotate, empty, remove archives), unmasklogs (see unmasked).
 * Audit: system.log.read (a file opened, once per file and session per ReadInterval), system.log.download,
 * system.log.rotate, system.log.truncate, system.log.delete.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogViewer
{
    /** @var expLogViewerConfig */
    protected $config;

    /** @var expLogFileLocator */
    protected $locator;

    /** @var expLogRedactor */
    protected $redactor;

    /** @var bool the viewer switched masking off (and may) */
    protected $unmasked;

    /** @var string the key of the request keys (rk) of this viewer */
    protected $requestKeySalt;

    /** @var array identifier => class id (or 0), this request */
    protected $classIDs = array();

    /**
     * @param expLogViewerConfig $config
     * @param expLogFileLocator|null $locator
     * @param bool $unmasked masking switched off (the caller checked setup/unmasklogs)
     * @param string|null $salt the key of the request keys; null: from the session
     */
    public function __construct( expLogViewerConfig $config, ?expLogFileLocator $locator = null, $unmasked = false, $salt = null )
    {
        $this->config = $config;
        $this->locator = $locator !== null ? $locator : new expLogFileLocator( $config );
        $this->unmasked = (bool)$unmasked;
        $this->redactor = expLogRedactor::fromConfig( $config, $this->unmasked );
        $this->requestKeySalt = $salt !== null ? (string)$salt : 'logviewer|' . session_id() . '|' . ( isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '' );
    }

    /**
     * The viewer of the current user: masking off only when asked for and allowed.
     *
     * @param bool $askUnmasked
     * @return expLogViewer
     */
    public static function forCurrentUser( $askUnmasked = false )
    {
        return new self( expLogViewerConfig::instance(), null, $askUnmasked && self::can( 'unmasklogs' ) );
    }

    /**
     * Whether the current user has a function of the setup module (logs, managelogs, unmasklogs).
     *
     * @param string $function
     * @return bool
     */
    public static function can( $function )
    {
        if ( !class_exists( 'eZUser' ) )
            return false;
        $user = eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return false;
        $access = $user->hasAccessTo( 'setup', (string)$function );
        return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
    }

    /** The user preference that keeps the viewer's choice of wrapping long lines */
    const WRAP_PREFERENCE = 'admin_logviewer_wrap';

    /**
     * Whether long lines are wrapped, from a stored choice: '1' on, '0' off, anything else (no choice stored) the
     * default of [TailSettings] WrapLines (enabled). Pure.
     *
     * @param mixed $stored the preference's value (false or null when none is stored)
     * @param bool $default
     * @return bool
     */
    public static function wrapFrom( $stored, $default = true )
    {
        if ( $stored === '1' || $stored === 1 || $stored === true )
            return true;
        if ( $stored === '0' || $stored === 0 )
            return false;
        return (bool)$default;
    }

    /**
     * Whether the current user's viewer wraps long lines: their stored choice (eZPreferences admin_logviewer_wrap,
     * written by the page's switch through user/preferences/set_and_exit), else the default of the settings. The
     * same for every log, view and visit, so switching logs or opening a permalink never changes it.
     *
     * @return bool
     */
    public function wrapLines()
    {
        $stored = class_exists( 'eZPreferences' ) ? eZPreferences::value( self::WRAP_PREFERENCE ) : false;
        return self::wrapFrom( $stored, $this->config->enabled( 'TailSettings', 'WrapLines' ) );
    }

    /** @return bool whether the viewer is on ([LogViewerSettings] LogViewer) */
    public function enabled()
    {
        return $this->config->enabled( 'LogViewerSettings', 'LogViewer' );
    }

    /** @return expLogViewerConfig */
    public function config()
    {
        return $this->config;
    }

    /** @return expLogFileLocator */
    public function locator()
    {
        return $this->locator;
    }

    /** @return expLogRedactor */
    public function redactor()
    {
        return $this->redactor;
    }

    /** @return bool */
    public function unmasked()
    {
        return $this->unmasked;
    }

    /**
     * Which actions are switched on ([ActionSettings]) and allowed to the current user.
     *
     * @param bool|null $canManage null: setup/managelogs of the current user
     * @return array download, rotate, truncate, delete => bool
     */
    public function actions( $canManage = null )
    {
        $canManage = $canManage === null ? self::can( 'managelogs' ) : (bool)$canManage;
        return array( 'download' => $this->config->enabled( 'ActionSettings', 'Download' ),
                      'rotate' => $canManage && $this->config->enabled( 'ActionSettings', 'Rotate' ),
                      'truncate' => $canManage && $this->config->enabled( 'ActionSettings', 'Truncate' ),
                      'delete' => $canManage && $this->config->enabled( 'ActionSettings', 'DeleteArchives' ) );
    }

    /**
     * The actions that apply to one file, of those switched on and allowed (actions()). An archive (.N, .gz) is
     * read only: it is downloaded or removed, never rotated or emptied; a current file is never removed; a log
     * configured by key (the web server's) is only downloaded. Pure.
     *
     * @param array $file a resolved file (archive, configured)
     * @param array $actions actions()
     * @return array download, rotate, truncate, delete => bool
     */
    public static function actionsFor( array $file, array $actions )
    {
        $archive = !empty( $file['archive'] );
        $configured = !empty( $file['configured'] );
        return array( 'download' => !empty( $actions['download'] ),
                      'rotate' => !empty( $actions['rotate'] ) && !$archive && !$configured,
                      'truncate' => !empty( $actions['truncate'] ) && !$archive && !$configured,
                      'delete' => !empty( $actions['delete'] ) && $archive && !$configured );
    }

    /**
     * The navigator: the groups of files with size, time and line rate.
     *
     * @return array group => list of files (id, name, dir, group, base, rotation, gz, archive, level, size, mtime, rate,
     *               readable)
     */
    public function navigator()
    {
        $groups = $this->locator->listFiles();
        $stats = new expLogFileStats();
        return $stats->withRates( $groups, $this->locator );
    }

    /** @var array|null request key => navigator, so the page and its left menu list the files once */
    protected static $navigatorMemo = null;

    /**
     * The navigator for the current user (fetch( 'setup', 'log_files' )): empty without setup/logs or with the viewer
     * switched off; listed once per request.
     *
     * @return array
     */
    public static function navigatorForCurrentUser()
    {
        $key = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? (string)$_SERVER['REQUEST_TIME_FLOAT'] : '';
        if ( self::$navigatorMemo !== null && isset( self::$navigatorMemo[$key] ) )
            return self::$navigatorMemo[$key];
        $result = array();
        $config = expLogViewerConfig::instance();
        if ( $config->enabled( 'LogViewerSettings', 'LogViewer' ) && self::can( 'logs' ) )
        {
            $viewer = new self( $config );
            $result = $viewer->navigator();
        }
        self::$navigatorMemo = array( $key => $result );
        return $result;
    }

    /**
     * An entry as the page and the feed show it: masked, with its links.
     *
     * @param array $entry from expLogParser
     * @return array o (offset), e (end), c (continues the entry before), t (Unix time), ts (time as written),
     *               lv (level), src (source), sa (siteaccess), ip (masked), u (request, masked), p (the message's parts:
     *               text, or text, link type, value), rk (request key: equal for the lines of one request), refs
     *               (error references), f (file, in search results), x (cut)
     */
    public function present( array $entry )
    {
        $message = $this->redactor->text( isset( $entry['message'] ) ? $entry['message'] : $entry['text'] );
        $url = $this->redactor->text( (string)$entry['url'] );
        $parts = expLogParser::parts( $message );
        foreach ( $parts as &$part )
            if ( isset( $part[1] ) && $part[1] === 'class' )
                $part[2] = $this->classLink( $part[2] );
        unset( $part );
        $out = array( 'o' => (int)$entry['offset'], 'e' => (int)$entry['end'], 'c' => (bool)$entry['cont'],
                      't' => $entry['time'], 'ts' => (string)$entry['time_text'], 'lv' => (string)$entry['level'],
                      'src' => $this->redactor->text( (string)$entry['source'] ), 'sa' => (string)$entry['siteaccess'],
                      'ip' => $this->redactor->ip( (string)$entry['ip'] ), 'u' => $url, 'p' => $parts,
                      'rk' => $entry['url'] !== '' || $entry['ip'] !== ''
                              ? substr( hash_hmac( 'sha256', $entry['ip'] . '|' . $entry['url'], $this->requestKeySalt ), 0, 12 ) : '',
                      'refs' => $entry['references'] );
        if ( isset( $entry['file'] ) )
            $out['f'] = $entry['file'];
        if ( !empty( $entry['truncated'] ) )
            $out['x'] = true;
        return $out;
    }

    /**
     * A class identifier as a link value: "<id>|<identifier>", or the identifier alone when there is no such class.
     *
     * @param string $identifier
     * @return string
     */
    protected function classLink( $identifier )
    {
        if ( !isset( $this->classIDs[$identifier] ) )
        {
            $id = 0;
            try
            {
                if ( class_exists( 'eZContentClass' ) && class_exists( 'eZDB', false ) && eZDB::hasInstance() )
                    $id = (int)eZContentClass::classIDByIdentifier( $identifier );
            }
            catch ( Exception $e )
            {
                $id = 0;
            }
            $this->classIDs[$identifier] = $id;
        }
        return $this->classIDs[$identifier] > 0 ? $this->classIDs[$identifier] . '|' . $identifier : $identifier;
    }

    /**
     * Describes a resolved file for the page and the feed.
     *
     * @param array $file
     * @return array
     */
    public static function fileInfo( array $file )
    {
        return array( 'id' => $file['id'], 'name' => $file['name'], 'dir' => $file['dir'], 'group' => $file['group'],
                      'archive' => (bool)$file['archive'], 'gz' => (bool)$file['gz'], 'size' => (int)$file['size'],
                      'mtime' => (int)$file['mtime'], 'ino' => (int)$file['ino'], 'level' => $file['level'],
                      'key' => isset( $file['key'] ) ? $file['key'] : '', 'rotation' => isset( $file['rotation'] ) ? (int)$file['rotation'] : 0,
                      'label' => isset( $file['label'] ) ? $file['label'] : $file['name'], 'labeled' => !empty( $file['labeled'] ),
                      'configured' => !empty( $file['configured'] ) );
    }

    /**
     * What a file gained since an offset (a poll), or its end (offset -1).
     *
     * @param string $id
     * @param int $offset
     * @param int $inode
     * @return array|null null for a file the viewer may not open; else file, entries, offset, next, size, ino,
     *                    reset, more, cached, live (false for an archive), cost (stat, open, cache_hit, bytes_read, ms)
     */
    public function tail( $id, $offset, $inode = 0 )
    {
        $started = microtime( true );
        $file = $this->locator->resolve( $id );
        if ( $file === null )
            return null;
        if ( $file['gz'] )
        {
            $initial = $this->config->int( 'TailSettings', 'InitialBytes' );
            if ( (int)$offset !== -1 )
                return array( 'file' => self::fileInfo( $file ), 'entries' => array(), 'offset' => (int)$offset, 'next' => (int)$offset,
                              'size' => (int)$file['size'], 'ino' => (int)$file['ino'], 'reset' => false, 'more' => false, 'cached' => false,
                              'live' => false, 'cost' => array( 'ms' => 0 ) );
            $read = expLogTail::lastBytesOfGzip( $file['path'], $initial, $this->config->int( 'SearchSettings', 'MaxGzipBytes' ) );
            $entries = array();
            foreach ( expLogParser::parse( $read['data'], $read['offset'], $file['level'] ) as $e )
                $entries[] = $this->present( $e );
            return array( 'file' => self::fileInfo( $file ), 'entries' => $entries, 'offset' => $read['offset'], 'next' => $read['size'],
                          'size' => (int)$file['size'], 'ino' => (int)$file['ino'], 'reset' => false, 'more' => false, 'cached' => false,
                          'live' => false, 'cost' => array( 'ms' => (int)round( ( microtime( true ) - $started ) * 1000 ) ) );
        }
        $tail = new expLogTail( $this->config );
        $read = $tail->read( $file['path'], (int)$offset, (int)$inode );
        $entries = array();
        if ( $read['data'] !== '' )
            foreach ( expLogParser::parse( $read['data'], $read['offset'], $file['level'] ) as $e )
                $entries[] = $this->present( $e );
        $file['size'] = $read['size'];
        $file['mtime'] = $read['mtime'];
        $file['ino'] = $read['ino'];
        $cost = $tail->lastStats;
        $cost['ms'] = round( ( microtime( true ) - $started ) * 1000, 2 );
        return array( 'file' => self::fileInfo( $file ), 'entries' => $entries, 'offset' => $read['offset'], 'next' => $read['next'],
                      'size' => $read['size'], 'ino' => $read['ino'], 'reset' => $read['reset'], 'more' => $read['more'],
                      'cached' => $read['cached'], 'live' => !$file['archive'], 'cost' => $cost );
    }

    /**
     * The files a search covers: the file, and with $rotations its rotations; scope "kernel" searches every current
     * kernel log (and their rotations with $rotations), scope "all" every listed file.
     *
     * @param string $id
     * @param string $scope file | kernel | all
     * @param bool $rotations
     * @return array resolved files
     */
    public function searchFiles( $id, $scope = 'file', $rotations = false )
    {
        $rotations = $rotations && $this->config->enabled( 'SearchSettings', 'Rotations' );
        $files = array();
        if ( $scope === 'kernel' || $scope === 'all' )
        {
            foreach ( $this->locator->listFiles() as $group => $list )
            {
                foreach ( $list as $info )
                {
                    if ( $info['archive'] && !$rotations )
                        continue;
                    if ( $scope === 'kernel' && !( $group === expLogFileLocator::GROUP_KERNEL
                         || ( $group === expLogFileLocator::GROUP_ARCHIVES && $this->isKernelBase( $info ) ) ) )
                        continue;
                    $resolved = $this->locator->resolve( $info['id'] );
                    if ( $resolved !== null )
                        $files[] = $resolved;
                }
            }
            // newest first: each current file, then the rotations by number
            usort( $files, function ( $a, $b ) {
                $ra = $a['rotation'] < 0 ? 100000 : $a['rotation'];
                $rb = $b['rotation'] < 0 ? 100000 : $b['rotation'];
                return $ra === $rb ? strcmp( $a['id'], $b['id'] ) : ( $ra < $rb ? -1 : 1 );
            } );
            return $files;
        }
        $file = $this->locator->resolve( $id );
        if ( $file === null )
            return array();
        $files[] = $file;
        if ( $rotations && !$file['archive'] )
            foreach ( $this->locator->rotationsOf( $file ) as $r )
                $files[] = $r;
        return $files;
    }

    /** @return bool an archive whose current file is a kernel log */
    protected function isKernelBase( array $info )
    {
        $d = $this->locator->describe( $info['dir'], $info['base'] );
        return $d['group'] === expLogFileLocator::GROUP_KERNEL;
    }

    /**
     * A search.
     *
     * @param array $files from searchFiles()
     * @param array $criteria expLogSearch::criteria()
     * @param string $cursor
     * @return array entries (presented), cursor, stopped, scanned, ms, files, error, invalid
     */
    public function search( array $files, array $criteria, $cursor = '' )
    {
        $search = new expLogSearch( $this->config );
        // always the redactor, also unmasked: the values of the secret settings and a password in an address stay
        // hidden then too, and must not be found by searching for them
        $result = $search->run( $files, $criteria, $cursor, $this->redactor );
        $entries = array();
        foreach ( $result['entries'] as $e )
            $entries[] = $this->present( $e );
        $result['entries'] = $entries;
        $result['invalid'] = $criteria['invalid'];
        return $result;
    }

    /**
     * The entry at an offset of a file (unmasked; for the server's own use).
     *
     * @param array $file resolved
     * @param int $offset
     * @return array|null
     */
    public function entryAt( array $file, $offset )
    {
        $offset = (int)$offset;
        if ( $offset < 0 || $offset >= (int)$file['size'] )
            return null;
        if ( $file['gz'] )
        {
            $handle = @gzopen( $file['path'], 'rb' );
            if ( $handle === false )
                return null;
            if ( $offset > 0 )
                gzseek( $handle, $offset );
            $data = (string)gzread( $handle, 65536 );
            gzclose( $handle );
        }
        else
        {
            $handle = @fopen( $file['path'], 'rb' );
            if ( $handle === false )
                return null;
            fseek( $handle, $offset );
            $data = (string)fread( $handle, 65536 );
            fclose( $handle );
        }
        $entries = expLogParser::parse( $data, $offset, $file['level'] );
        return isset( $entries[0] ) && !$entries[0]['cont'] ? $entries[0] : null;
    }

    /**
     * The lines of the same request as an entry: the same request address and client, within RequestWindow seconds,
     * in the kernel logs (current and .1) and the entry's own file; with the audit window to link to.
     *
     * @param string $id
     * @param int $offset
     * @return array|null entry (presented), entries (presented, each with f), window (from, to as ISO times), audit
     *                    (the audit console filters: from, to), error
     */
    public function related( $id, $offset )
    {
        $file = $this->locator->resolve( $id );
        if ( $file === null )
            return null;
        $entry = $this->entryAt( $file, $offset );
        if ( $entry === null || $entry['time'] === null )
            return array( 'entry' => null, 'entries' => array(), 'window' => null, 'audit' => null, 'error' => 'no_entry' );
        $w = $this->config->int( 'SearchSettings', 'RequestWindow' );
        $criteria = expLogSearch::criteria( array(), $this->config->int( 'SearchSettings', 'MaxPatternLength' ) );
        $criteria['from'] = $entry['time'] - $w;
        $criteria['to'] = $entry['time'] + $w;
        // narrowed while reading (substring and prefix); compared exactly below
        $criteria['uri'] = (string)$entry['url'];
        $criteria['ip'] = (string)$entry['ip'];
        $files = array();
        foreach ( $this->searchFiles( $id, 'kernel', false ) as $f )
            $files[$f['id']] = $f;
        $files[$file['id']] = $file;
        // the first rotation of each, for a request that was split by a rotation
        foreach ( array_values( $files ) as $f )
            foreach ( $this->locator->rotationsOf( $f ) as $r )
                if ( $r['rotation'] === 1 && $r['mtime'] >= $criteria['from'] )
                    $files[$r['id']] = $r;
        $search = new expLogSearch( $this->config );
        $found = $search->run( array_values( $files ), $criteria, '' );
        $entries = array();
        foreach ( $found['entries'] as $e )
        {
            if ( $e['url'] !== $entry['url'] || $e['ip'] !== $entry['ip'] )
                continue;
            $entries[] = $this->present( $e );
        }
        usort( $entries, function ( $a, $b ) {
            return $a['t'] === $b['t'] ? strcmp( $a['f'], $b['f'] ) : ( $a['t'] < $b['t'] ? -1 : 1 );
        } );
        $from = date( 'Y-m-d\TH:i:s', $criteria['from'] );
        $to = date( 'Y-m-d\TH:i:s', $criteria['to'] );
        $presented = $this->present( $entry + array( 'file' => $file['id'] ) );
        return array( 'entry' => $presented, 'entries' => $entries, 'window' => array( 'from' => $from, 'to' => $to ),
                      'audit' => array( 'from' => date( 'Y-m-d\TH:i:s', $criteria['from'] - 60 ), 'to' => date( 'Y-m-d\TH:i:s', $criteria['to'] + 60 ) ),
                      'error' => $found['error'], 'stopped' => $found['stopped'] );
    }

    /**
     * Finds an error reference (ERR-XXXXXXXXXX) in the kernel logs and their rotations, and the lines of its request.
     *
     * @param string $reference
     * @return array entries (presented), related (of the first: as related()), error
     */
    public function reference( $reference )
    {
        $criteria = expLogSearch::criteria( array( 'reference' => $reference ), $this->config->int( 'SearchSettings', 'MaxPatternLength' ) );
        if ( $criteria['invalid'] )
            return array( 'entries' => array(), 'related' => null, 'error' => 'invalid' );
        $search = new expLogSearch( $this->config );
        $found = $search->run( $this->searchFiles( '', 'kernel', true ), $criteria, '' );
        $entries = array();
        foreach ( $found['entries'] as $e )
            $entries[] = $this->present( $e );
        $related = null;
        if ( $found['entries'] )
            $related = $this->related( $found['entries'][0]['file'], $found['entries'][0]['offset'] );
        return array( 'entries' => $entries, 'related' => $related, 'error' => $found['error'], 'stopped' => $found['stopped'],
                      'cursor' => $found['cursor'] );
    }

    /**
     * Records an action on a log file in the audit (system.log.read, .download, .rotate, .truncate, .delete).
     *
     * @param string $action read | download | rotate | truncate | delete
     * @param array $file resolved
     * @param array $after more about it (range, bytes ...)
     * @return void
     */
    public static function audit( $action, array $file, array $after = array() )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        $names = array( 'read' => 'system.log.read', 'download' => 'system.log.download', 'rotate' => 'system.log.rotate',
                        'truncate' => 'system.log.truncate', 'delete' => 'system.log.delete' );
        if ( !isset( $names[$action] ) )
            return;
        expAuditHook::emit( $names[$action], array( 'object' => array( 'type' => 'log_file', 'id' => (string)$file['id'] ),
                                                    'after' => array( 'size' => (int)$file['size'] ) + $after ) );
    }

    /**
     * Records system.log.read for a file, once per file and session per ReadInterval.
     *
     * @param array $file resolved
     * @param expLogRateLimit|null $limit
     * @param bool $unmasked
     */
    public function auditRead( array $file, ?expLogRateLimit $limit = null, $unmasked = false )
    {
        $interval = $this->config->int( 'ViewerAuditSettings', 'ReadInterval' );
        if ( $limit !== null && $interval > 0 && !$limit->shouldRecordRead( $file['id'] . ( $unmasked ? '|unmasked' : '' ), $interval ) )
            return;
        self::audit( 'read', $file, array( 'unmasked' => (bool)$unmasked ) );
    }

    /**
     * The form token of the actions' forms (the token of ezformtoken when it is active, else one kept in the
     * session for the log viewer).
     *
     * @return array field, token
     */
    public static function formToken()
    {
        if ( class_exists( 'ezxFormToken' ) )
        {
            try
            {
                return array( 'field' => defined( 'ezxFormToken::FORM_FIELD' ) ? (string)ezxFormToken::FORM_FIELD : 'ezxform_token',
                              'token' => (string)ezxFormToken::getToken() );
            }
            catch ( Exception $e )
            {
            }
        }
        $http = eZHTTPTool::instance();
        if ( !$http->hasSessionVariable( 'expLogViewerToken' ) )
            $http->setSessionVariable( 'expLogViewerToken', bin2hex( random_bytes( 16 ) ) );
        return array( 'field' => 'LogViewerToken', 'token' => (string)$http->sessionVariable( 'expLogViewerToken' ) );
    }

    /**
     * Whether a POST carries the form token (formToken()).
     *
     * @return bool
     */
    public static function checkFormToken()
    {
        $token = self::formToken();
        $http = eZHTTPTool::instance();
        if ( !$http->hasPostVariable( $token['field'] ) )
            return false;
        return $token['token'] !== '' && hash_equals( $token['token'], (string)$http->postVariable( $token['field'] ) );
    }
}
