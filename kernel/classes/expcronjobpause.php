<?php
/**
 * File containing the expCronjobPause class.
 *
 * Pauses cronjob parts: one part, or all of them, until someone resumes them. A paused part is left out by
 * runcronjobs.php, whoever starts it - the crontab, `./console cron:<part>` or Setup > Cronjobs - and stays paused
 * across releases and restarts, because the state lives in a file rather than in a process.
 *
 * Operators wanted this for releases, data migrations and index rebuilds: stopping the crontab means editing it on
 * every server and remembering to put it back, and the extensions that had done the job depended on a patch of
 * runcronjobs.php that Exponential does not carry. The decision is made through the
 * runcronjobs.php itself (notice()), before it asks the cronjob/part/run filter, so a site's own listeners still decide
 * about the parts that are not paused; cronjob.ini [PauseSettings] Pause=disabled switches the check off.
 *
 * The state is one file per part - <cache>/cronjob_control/cronpart_deactivated_<part>.txt, "1" for paused,
 * anything else for running, "all" for every part and an empty part name for the scripts of [CronjobSettings] - the
 * layout such extensions already use, so an installation that paused parts that way keeps them, and such a page
 * and Setup > Cronjobs show the same state. Every pause and resume made here is written to a history file with the
 * user who did it; the console shows who paused a part and since when.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

class expCronjobPause
{
    /** The part name that pauses every part. A cronjob part named "all" cannot be paused on its own. */
    const ALL = 'all';

    /** How much of the end of the history file is read, and how many entries are kept from it. */
    const HISTORY_BYTES = 262144;
    const HISTORY_READ = 500;

    /** @var string|null A directory set by a test instead of the one from the settings. */
    protected static $directory = null;

    /** @var string|null A history file set by a test instead of the one from the settings. */
    protected static $historyFile = null;

    /** @var array[]|null The history read in this request, newest first; dropped when this request writes. */
    protected static $historyCache = null;

    /**
     * The notice runcronjobs.php prints when it leaves a run out because of a pause, or false when the run may go
     * ahead.
     *
     * While "all" is paused nothing runs. A part runs unless it is paused itself. A script run alone (--script) is
     * left out when a paused part lists it, so pausing a part also holds back its scripts when someone starts one of
     * them by hand; resume the part to run them.
     *
     * @param string $part The part, '' for [CronjobSettings] and for a single script.
     * @param string[] $scripts
     * @param bool $single
     * @return string|false
     */
    public static function notice( $part, array $scripts = array(), $single = false )
    {
        if ( !self::enabled() )
            return false;
        $reason = self::pausedBy( (string)$part, $scripts, (bool)$single );
        if ( $reason === false )
            return false;
        return $reason === self::ALL
             ? 'Notice: All cronjob parts are paused (Setup > Cronjobs).'
             : "Notice: The cronjob part '" . self::label( $reason ) . "' is paused (Setup > Cronjobs).";
    }

    /**
     * Why a run is left out: self::ALL, the name of the paused part, or false when it may run.
     *
     * @param string $part
     * @param string[] $scripts
     * @param bool $single
     * @return string|false
     */
    public static function pausedBy( $part, array $scripts = array(), $single = false )
    {
        if ( self::isPaused( self::ALL ) )
            return self::ALL;

        if ( !$single )
            return self::isPaused( $part ) ? $part : false;

        // A script run alone does not say which part it belongs to; every part that lists it counts.
        $script = isset( $scripts[0] ) ? basename( (string)$scripts[0] ) : '';
        foreach ( self::partsListing( $script ) as $candidate )
        {
            if ( self::isPaused( $candidate ) )
                return $candidate;
        }
        return false;
    }

    /**
     * Whether runcronjobs.php honours the pause files at all (cronjob.ini [PauseSettings] Pause=enabled, the default).
     *
     * @return bool
     */
    public static function enabled()
    {
        if ( !class_exists( 'eZINI' ) || !eZINI::exists( 'cronjob.ini' ) )
            return true;
        $ini = eZINI::instance( 'cronjob.ini' );
        return !$ini->hasVariable( 'PauseSettings', 'Pause' ) || $ini->variable( 'PauseSettings', 'Pause' ) !== 'disabled';
    }

    /**
     * Whether a part name may be paused: empty (the scripts of [CronjobSettings]), "all", or letters, digits and
     * _ . - without "..". The name becomes part of a file name.
     *
     * @param string $part
     * @return bool
     */
    public static function isValidPart( $part )
    {
        $part = (string)$part;
        return $part === '' || ( preg_match( '/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $part ) === 1 && strpos( $part, '..' ) === false );
    }

    /**
     * @param string $part
     * @return bool
     */
    public static function isPaused( $part )
    {
        if ( !self::isValidPart( $part ) )
            return false;
        $file = self::file( $part );
        return is_file( $file ) && trim( (string)@file_get_contents( $file ) ) === '1';
    }

    /**
     * The state of a part for the console: paused, since when (the time of the state file) and by whom (from the
     * history, false when the change was not made here).
     *
     * @param string $part
     * @return array paused, since, by (array user_id, login, time, or false)
     */
    public static function state( $part )
    {
        $paused = self::isPaused( $part );
        $file = self::file( $part );
        $since = $paused && is_file( $file ) ? (int)filemtime( $file ) : 0;
        $by = false;
        if ( $paused )
        {
            foreach ( self::history( 200 ) as $entry )
            {
                if ( $entry['part'] === (string)$part )
                {
                    // Only the pause that wrote the file: a file changed since (from a shell, by an extension)
                    // was paused by someone this history does not know.
                    $by = ( $entry['action'] === 'pause' && $entry['time'] >= $since - 2 ) ? $entry : false;
                    break;
                }
            }
        }
        return array( 'paused' => $paused, 'since' => $since, 'by' => $by );
    }

    /**
     * Pauses a part ("all" pauses every part).
     *
     * @param string $part
     * @param eZUser|null $user Who pauses it, for the history; the current user when null.
     * @return bool false for an invalid part name or a file that could not be written.
     */
    public static function pause( $part, $user = null )
    {
        return self::write( $part, true, $user );
    }

    /**
     * Resumes a part.
     *
     * @param string $part
     * @param eZUser|null $user
     * @return bool
     */
    public static function resume( $part, $user = null )
    {
        return self::write( $part, false, $user );
    }

    /**
     * The newest pauses and resumes first.
     *
     * @param int $limit
     * @return array[] time, part, action (pause|resume), user_id, login
     */
    public static function history( $limit = 20 )
    {
        if ( self::$historyCache === null )
            self::$historyCache = self::readHistory();
        return array_slice( self::$historyCache, 0, max( 0, (int)$limit ) );
    }

    /**
     * The last HISTORY_READ entries of the history file, newest first. Only the end of the file is read, so a history
     * that grows over the years costs the page nothing.
     *
     * @return array[]
     */
    private static function readHistory()
    {
        $file = self::historyFile();
        if ( !is_readable( $file ) )
            return array();
        $size = (int)filesize( $file );
        $handle = fopen( $file, 'rb' );
        if ( $handle === false )
            return array();
        $offset = max( 0, $size - self::HISTORY_BYTES );
        fseek( $handle, $offset );
        $text = (string)stream_get_contents( $handle );
        fclose( $handle );
        $lines = preg_split( '/\r?\n/', $text );
        // The first line of a read that starts inside the file is cut.
        if ( $offset > 0 )
            array_shift( $lines );

        $entries = array();
        foreach ( array_reverse( $lines ) as $line )
        {
            $entry = json_decode( $line, true );
            if ( !is_array( $entry ) || !isset( $entry['time'], $entry['part'], $entry['action'] )
                 || !is_string( $entry['part'] ) || !self::isValidPart( $entry['part'] ) )
                continue;
            $entries[] = array( 'time'    => (int)$entry['time'],
                                'part'    => $entry['part'],
                                'action'  => $entry['action'] === 'pause' ? 'pause' : 'resume',
                                'user_id' => isset( $entry['user_id'] ) && is_scalar( $entry['user_id'] ) ? (int)$entry['user_id'] : 0,
                                'login'   => isset( $entry['login'] ) && is_string( $entry['login'] ) ? $entry['login'] : '' );
            if ( count( $entries ) >= self::HISTORY_READ )
                break;
        }
        return $entries;
    }

    /**
     * The parts that are paused now, "all" included.
     *
     * @return string[]
     */
    public static function pausedParts()
    {
        $paused = array();
        foreach ( (array)glob( self::directory() . '/cronpart_deactivated_*.txt' ) as $file )
        {
            $part = substr( basename( $file, '.txt' ), strlen( 'cronpart_deactivated_' ) );
            if ( self::isValidPart( $part ) && self::isPaused( $part ) )
                $paused[] = $part;
        }
        sort( $paused );
        return $paused;
    }

    /**
     * How a part is named to a person: the scripts of [CronjobSettings] have no name on the command line.
     *
     * @param string $part
     * @return string
     */
    public static function label( $part )
    {
        return $part === '' ? 'global' : $part;
    }

    /**
     * For tests: use these paths instead of the ones from the settings; null for both goes back to the settings.
     *
     * @param string|null $directory
     * @param string|null $historyFile
     */
    public static function setPaths( $directory, $historyFile )
    {
        self::$directory = $directory;
        self::$historyFile = $historyFile;
        self::$historyCache = null;
    }

    /**
     * The directory of the state files: cronjob.ini [PauseSettings] Directory below the var directory, or the
     * cronjob_control directory below the cache directory.
     *
     * @return string
     */
    public static function directory()
    {
        if ( self::$directory !== null )
            return self::$directory;
        $setting = '';
        if ( class_exists( 'eZINI' ) && eZINI::exists( 'cronjob.ini' ) )
        {
            $ini = eZINI::instance( 'cronjob.ini' );
            if ( $ini->hasVariable( 'PauseSettings', 'Directory' ) )
                $setting = trim( (string)$ini->variable( 'PauseSettings', 'Directory' ), '/' );
        }
        if ( $setting !== '' && strpos( $setting, '..' ) === false )
            return rtrim( eZSys::varDirectory(), '/' ) . '/' . $setting;
        return rtrim( eZSys::cacheDirectory(), '/' ) . '/cronjob_control';
    }

    /**
     * @return string
     */
    public static function historyFile()
    {
        if ( self::$historyFile !== null )
            return self::$historyFile;
        return rtrim( eZSys::varDirectory(), '/' ) . '/' . eZINI::instance()->variable( 'FileSettings', 'LogDir' ) . '/cronjob_pause.log';
    }

    /**
     * @param string $part
     * @return string
     */
    private static function file( $part )
    {
        return self::directory() . '/cronpart_deactivated_' . $part . '.txt';
    }

    /**
     * The parts of cronjob.ini whose Scripts[] list a script, '' for [CronjobSettings].
     *
     * @param string $script
     * @return string[]
     */
    private static function partsListing( $script )
    {
        if ( $script === '' || !class_exists( 'eZINI' ) || !eZINI::exists( 'cronjob.ini' ) )
            return array();
        $ini = eZINI::instance( 'cronjob.ini' );
        $parts = array();
        foreach ( $ini->groups() as $group => $values )
        {
            if ( $group === 'CronjobSettings' )
                $name = '';
            elseif ( strpos( $group, 'CronjobPart-' ) === 0 )
                $name = substr( $group, strlen( 'CronjobPart-' ) );
            else
                continue;
            if ( isset( $values['Scripts'] ) && in_array( $script, array_map( 'basename', (array)$values['Scripts'] ), true ) )
                $parts[] = $name;
        }
        return $parts;
    }

    /**
     * @param string $part
     * @param bool $pause
     * @param eZUser|null $user
     * @return bool
     */
    private static function write( $part, $pause, $user )
    {
        $part = (string)$part;
        if ( !self::isValidPart( $part ) )
            return false;

        $dir = self::directory();
        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) )
            return false;
        // Written to a temporary file and renamed over the old one, so a reader never sees a half-written file, and a
        // file another user wrote (Velocity's workers and the command line may run as root, the web server as the site
        // user) is replaced rather than opened, which its mode could refuse.
        $file = self::file( $part );
        if ( !eZFile::create( basename( $file ), $dir, $pause ? '1' : '0', true ) || self::isPaused( $part ) !== $pause )
            return false;
        self::shareFile( $file );

        if ( $user === null && class_exists( 'eZUser' ) )
            $user = eZUser::currentUser();
        $line = json_encode( array( 'time'    => time(),
                                    'part'    => $part,
                                    'action'  => $pause ? 'pause' : 'resume',
                                    'user_id' => $user ? (int)$user->attribute( 'contentobject_id' ) : 0,
                                    'login'   => $user ? (string)$user->attribute( 'login' ) : '' ) );
        $history = self::historyFile();
        if ( !is_dir( dirname( $history ) ) )
            eZDir::mkdir( dirname( $history ), false, true );
        self::$historyCache = null;
        // The pause holds without its history line; say so in the debug log rather than undo it.
        if ( @file_put_contents( $history, $line . "\n", FILE_APPEND | LOCK_EX ) === false && class_exists( 'eZDebug' ) )
            eZDebug::writeWarning( "The pause of '" . self::label( $part ) . "' was not written to $history", __METHOD__ );
        else
            self::shareFile( $history );
        self::audit( $part, $pause, $user );
        return true;
    }

    /**
     * Records a run that runcronjobs.php left out because of a pause, in <LogDir>/cronjob_skipped.log, so it can be
     * told afterwards which runs a pause held back; the notice itself goes to the run's own output too.
     *
     * @param string $notice
     * @param string $part '' for [CronjobSettings]
     * @param string $script the script a single run named, or the first one left out of a part
     */
    public static function logSkip( $notice, $part, $script = '' )
    {
        if ( !class_exists( 'eZLog' ) )
            return;
        $siteaccess = class_exists( 'eZSiteAccess' ) && is_array( eZSiteAccess::current() ) ? eZSiteAccess::current()['name'] : '';
        eZLog::write( $notice . ' [part ' . self::label( (string)$part ) . ( $script !== '' ? ', script ' . basename( (string)$script ) : '' )
                      . ( $siteaccess !== '' ? ', siteaccess ' . $siteaccess : '' ) . ']', 'cronjob_skipped.log' );
    }

    /**
     * Gives a file this process owns the installation's file mode (site.ini [FileSettings] StorageFilePermissions,
     * within its limits), so the other user that runs the installation - root for Velocity and the command line, the
     * site user for the web server - can replace the state file and append to the history. A file another user owns
     * is left alone: only its owner may change its mode.
     *
     * @param string $file
     */
    private static function shareFile( $file )
    {
        if ( !is_file( $file ) || !function_exists( 'posix_geteuid' ) || @fileowner( $file ) !== posix_geteuid() || !class_exists( 'eZINI' ) )
            return;
        $mode = eZFile::fileMode( octdec( (string)eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) );
        if ( $mode > 0 && ( @fileperms( $file ) & 0777 ) !== $mode )
            @chmod( $file, $mode );
    }

    /**
     * Audit (doc/bc/6.0/audit.md, system.cronjob.pause and system.cronjob.resume): who paused or resumed which part.
     *
     * @param string $part
     * @param bool $pause
     * @param eZUser|null $user
     */
    private static function audit( $part, $pause, $user )
    {
        // Paths set by a test (setPaths()) are not the installation's pauses, so they are not the audit trail's either.
        if ( !class_exists( 'expAuditHook' ) || self::$directory !== null )
            return;
        expAuditHook::emit( $pause ? 'system.cronjob.pause' : 'system.cronjob.resume',
            array( 'object' => array( 'type' => 'cronjob_part', 'id' => self::label( $part ) ),
                   'verb' => $pause ? 'pause' : 'resume',
                   'after' => array( 'paused' => $pause ) ) );
    }
}
