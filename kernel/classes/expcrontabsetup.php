<?php
/**
 * File containing the expCrontabSetup class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The cron job of an installation: the crontab lines that run its cronjob parts (runcronjobs.php), written by the
 * installers and by bin/php/installcrontab.php, and the record of when each part last ran.
 *
 * A new installation activates extensions with cronjob parts (expqueue, sqliimport, expai) and the kernel's own parts
 * (global, frequent, infrequent) do the notifications, workflows, content jobs, audit and indexing; nothing ran them
 * until somebody added runcronjobs.php to a crontab by hand. The setup wizard, the kickstarter and exp:install now
 * write the lines (eZStepFinal), and bin/php/installcrontab.php adds, shows or removes them on an installation that
 * exists.
 *
 * - **Whose crontab**: the system user that owns the installation directory. A process running as that user writes
 *   its own crontab; root writes it with crontab -u <owner>. Nothing is written for an installation owned by root,
 *   or by a process that is neither the owner nor root: the lines are given to add by hand. Never root's crontab
 *   for a site of another user.
 * - **Which parts**: cronjob.ini [CrontabSettings] Parts[] (and the Parts[] an active extension adds in its own
 *   cronjob.ini.append.php), each only when the installation defines it with at least one script: the kernel's
 *   always, an extension's when the extension is active (ActiveExtensions[] or the admin siteaccess's
 *   ActiveAccessExtensions[]). AdminParts[] run with the admin siteaccess (-s), the others with the default one.
 * - **When**: [AdminSettings] CrontabSchedule_<part>, the schedule Setup > Cronjobs proposes as well.
 * - **Marked and idempotent**: the lines stand between "# BEGIN Exponential cronjobs: <root>" and
 *   "# END Exponential cronjobs: <root>". A second run replaces that block (or leaves the crontab alone when it is
 *   the same); every other line of the crontab is kept as it is, in its place. A part that a line of the crontab
 *   outside the block runs already for this installation is left out of the block, so it never runs twice.
 * - **Switched off** by [CrontabSettings] Install=disabled, or for one installation run by exp:install --no-crontab
 *   (the environment variable EXP_INSTALL_CRONTAB=0).
 * - **No crontab** (a container, no crontab binary, no permission): nothing fails; the result has status
 *   "fallback" with the exact lines and why, for the installer to print.
 *
 * runcronjobs.php records each part it starts and finishes in var/log/cronjob-lastrun.json (recordRun()), which
 * the dashboard block "cronjobs" and exp:syscard read (status()).
 *
 * The decisions (entries(), block(), merge(), strip(), target(), runState()) work on plain values and the crontab
 * command is a callable, so everything is tested without a database and without touching a real crontab
 * (tests/tests/kernel/classes/expCrontabSetupTest.php).
 *
 * Guide: doc/eng-us/bc/6.0/installer-crontab.md
 */
class expCrontabSetup
{
    const BEGIN = '# BEGIN Exponential cronjobs: ';
    const END = '# END Exponential cronjobs: ';

    /** The run record, relative to the installation root */
    const RUN_FILE = 'var/log/cronjob-lastrun.json';

    /** The part name of the scripts of [CronjobSettings] (runcronjobs.php without a part), as Setup > Cronjobs names it */
    const GLOBAL_PART = 'global';

    /** @var callable function( array $argv, string|null $stdin ): array( int status, string stdout, string stderr ) */
    private $runner;

    /** @var string|null the crontab command; null: cronjob.ini [CrontabSettings] CrontabCommand */
    private $command;

    /** @var int|null the effective user id; null: posix_geteuid() */
    private $euid;

    /** @var array|null the owner of the installation (uid, name); null: owner() */
    private $owner;

    /**
     * @param callable|null $runner runs a command; null: proc_open
     * @param string|null $command the crontab binary; null: the setting (crontab on the PATH by default)
     * @param int|null $euid the user id this process runs as; null: the real one
     * @param array|null $owner the owner of the installation, array( 'uid' => int, 'name' => string ); null: the real one
     */
    public function __construct( $runner = null, $command = null, $euid = null, $owner = null )
    {
        $this->runner = $runner !== null ? $runner : array( __CLASS__, 'runCommand' );
        $this->command = $command;
        $this->euid = $euid;
        $this->owner = $owner;
    }

    // ------------------------------------------------------------------ settings

    /**
     * Whether the installers write the crontab: [CrontabSettings] Install (enabled by default), off for one run with
     * the environment variable EXP_INSTALL_CRONTAB=0 (exp:install --no-crontab).
     *
     * @param string|null $root the installation root, for its settings/override; null: the current directory
     * @return bool
     */
    public static function enabled( $root = null )
    {
        $env = getenv( 'EXP_INSTALL_CRONTAB' );
        if ( $env !== false && in_array( strtolower( trim( $env ) ), array( '0', 'no', 'false', 'off', 'disabled' ), true ) )
            return false;
        $settings = self::settings( $root === null ? getcwd() : $root, array() );
        return strtolower( (string)$settings['install'] ) !== 'disabled';
    }

    /**
     * The crontab settings of an installation, read from its files rather than from the INI system: the setup wizard
     * runs before the new site's extensions are active. Order: the kernel's settings/cronjob.ini, each active
     * extension's settings/cronjob.ini.append.php, settings/override, the admin siteaccess. Lists (Parts[],
     * AdminParts[]) are joined, single values are taken from the last file that sets them.
     *
     * @param string $root
     * @param string[] $extensions the active extensions (ActiveExtensions[] and the admin's ActiveAccessExtensions[])
     * @param string $adminSiteaccess
     * @return array install, parts, admin_parts, schedules (part => schedule), command, php, part_scripts (part => count)
     */
    public static function settings( $root, array $extensions, $adminSiteaccess = '' )
    {
        $root = rtrim( (string)$root, '/' );
        $files = array( $root . '/settings/cronjob.ini' );
        foreach ( $extensions as $extension )
            $files[] = $root . '/extension/' . $extension . '/settings/cronjob.ini.append.php';
        $files[] = $root . '/settings/override/cronjob.ini.append.php';
        if ( $adminSiteaccess !== '' )
            $files[] = $root . '/settings/siteaccess/' . $adminSiteaccess . '/cronjob.ini.append.php';

        $result = array( 'install' => 'enabled', 'parts' => array(), 'admin_parts' => array(), 'schedules' => array(),
                         'command' => '', 'php' => '', 'part_scripts' => array() );
        foreach ( $files as $file )
        {
            if ( !is_file( $file ) )
                continue;
            $groups = self::iniGroups( $file );
            $result = self::mergeSettings( $result, $groups );
        }
        return $result;
    }

    /**
     * Adds the groups of one cronjob.ini file to the settings read so far (settings()).
     *
     * @param array $result as settings() returns it
     * @param array $groups group => variable => value (string or list), as eZINI::groups() gives them
     * @return array
     */
    public static function mergeSettings( array $result, array $groups )
    {
        if ( isset( $groups['CrontabSettings'] ) )
        {
            $c = $groups['CrontabSettings'];
            if ( isset( $c['Install'] ) && is_string( $c['Install'] ) && $c['Install'] !== '' )
                $result['install'] = $c['Install'];
            if ( isset( $c['CrontabCommand'] ) && is_string( $c['CrontabCommand'] ) && $c['CrontabCommand'] !== '' )
                $result['command'] = $c['CrontabCommand'];
            foreach ( array( 'Parts' => 'parts', 'AdminParts' => 'admin_parts' ) as $variable => $key )
            {
                if ( isset( $c[$variable] ) && is_array( $c[$variable] ) )
                    $result[$key] = array_values( array_unique( array_merge( $result[$key],
                                                                             array_filter( array_map( 'trim', $c[$variable] ), 'strlen' ) ) ) );
            }
        }
        if ( isset( $groups['AdminSettings'] ) )
        {
            foreach ( $groups['AdminSettings'] as $variable => $value )
            {
                if ( strpos( $variable, 'CrontabSchedule_' ) === 0 && is_string( $value ) && trim( $value ) !== '' )
                    $result['schedules'][substr( $variable, 16 )] = trim( $value );
                else if ( $variable === 'PhpCliPath' && is_string( $value ) && trim( $value ) !== '' )
                    $result['php'] = trim( $value );
            }
        }
        foreach ( $groups as $group => $variables )
        {
            $part = null;
            if ( $group === 'CronjobSettings' )
                $part = self::GLOBAL_PART;
            else if ( strpos( $group, 'CronjobPart-' ) === 0 )
                $part = substr( $group, 12 );
            if ( $part === null || !isset( $variables['Scripts'] ) || !is_array( $variables['Scripts'] ) )
                continue;
            $scripts = count( array_filter( array_map( 'trim', $variables['Scripts'] ), 'strlen' ) );
            $result['part_scripts'][$part] = ( isset( $result['part_scripts'][$part] ) ? $result['part_scripts'][$part] : 0 ) + $scripts;
        }
        return $result;
    }

    /**
     * The schedule of a part: [AdminSettings] CrontabSchedule_<part>, else the default Setup > Cronjobs proposes
     * (expCronjobRunner::crontabSchedule()).
     *
     * @param string $part
     * @param array $schedules part => schedule
     * @return string
     */
    public static function schedule( $part, array $schedules )
    {
        if ( isset( $schedules[$part] ) && trim( $schedules[$part] ) !== '' )
            return trim( $schedules[$part] );
        $defaults = array( 'frequent' => '*/5 * * * *', self::GLOBAL_PART => '*/15 * * * *', 'infrequent' => '17 * * * *' );
        return isset( $defaults[$part] ) ? $defaults[$part] : '0 * * * *';
    }

    // ------------------------------------------------------------------ the lines

    /**
     * The parts the cron job runs: each of $parts that the installation defines with at least one script.
     *
     * @param array $settings as settings() returns them
     * @param string $adminSiteaccess the siteaccess of AdminParts[]
     * @return array each: part, siteaccess ('' for the default siteaccess), schedule
     */
    public static function entries( array $settings, $adminSiteaccess )
    {
        $entries = array();
        foreach ( $settings['parts'] as $part )
        {
            if ( empty( $settings['part_scripts'][$part] ) )
                continue;
            $entries[] = array( 'part' => $part,
                                'siteaccess' => in_array( $part, $settings['admin_parts'], true ) ? (string)$adminSiteaccess : '',
                                'schedule' => self::schedule( $part, $settings['schedules'] ) );
        }
        return $entries;
    }

    /**
     * One crontab line: the schedule, then cd to the installation and runcronjobs.php with the PHP binary, quietly
     * (-q), the siteaccess when the part has one, the part (none for the global part), and the output appended to
     * var/log/cronjob-<part>.log.
     *
     * @param array $entry as entries() gives it
     * @param string $root
     * @param string $php
     * @return string
     */
    public static function line( array $entry, $root, $php )
    {
        $command = self::shellWord( $php ) . ' runcronjobs.php -q';
        if ( $entry['siteaccess'] !== '' )
            $command .= ' -s ' . self::shellWord( $entry['siteaccess'] );
        if ( $entry['part'] !== self::GLOBAL_PART )
            $command .= ' ' . self::shellWord( $entry['part'] );
        $log = 'var/log/cronjob-' . preg_replace( '/[^A-Za-z0-9_.-]/', '_', $entry['part'] ) . '.log';
        return $entry['schedule'] . ' cd ' . self::shellWord( $root ) . ' && ' . $command . ' >> ' . $log . ' 2>&1';
    }

    /**
     * The marked block of an installation.
     *
     * @param array $entries
     * @param string $root
     * @param string $php
     * @return string[]
     */
    public static function block( array $entries, $root, $php )
    {
        if ( !$entries )
            return array();
        $lines = array( self::BEGIN . $root,
                        '# The cronjob parts of this installation; php bin/php/installcrontab.php --show, --add or --remove (in ' . $root . ').' );
        foreach ( $entries as $entry )
            $lines[] = self::line( $entry, $root, $php );
        $lines[] = self::END . $root;
        return $lines;
    }

    /**
     * Where the block of an installation is in a crontab.
     *
     * @param string[] $lines the crontab, one entry per line
     * @param string $root
     * @return array|null array( first index, last index ), null when there is no complete block
     */
    public static function blockRange( array $lines, $root )
    {
        $begin = self::BEGIN . $root;
        $end = self::END . $root;
        $first = null;
        foreach ( array_values( $lines ) as $index => $line )
        {
            $line = rtrim( $line, "\r\n \t" );
            if ( $first === null && $line === $begin )
                $first = $index;
            else if ( $first !== null && $line === $end )
                return array( $first, $index );
        }
        return null;
    }

    /**
     * The crontab without the block of the installation; every other line as it was.
     *
     * @param string[] $lines
     * @param string $root
     * @return string[]
     */
    public static function strip( array $lines, $root )
    {
        $lines = array_values( $lines );
        $range = self::blockRange( $lines, $root );
        if ( $range === null )
            return $lines;
        array_splice( $lines, $range[0], $range[1] - $range[0] + 1 );
        return $lines;
    }

    /**
     * The crontab with the installation's block: in the place of the old block, else at the end. Every other line
     * stays as it was, in its place. An empty block removes the old one.
     *
     * @param string[] $lines
     * @param string[] $block
     * @param string $root
     * @return string[]
     */
    public static function merge( array $lines, array $block, $root )
    {
        $lines = array_values( $lines );
        $range = self::blockRange( $lines, $root );
        if ( $range !== null )
        {
            array_splice( $lines, $range[0], $range[1] - $range[0] + 1, $block );
            return $lines;
        }
        // an unterminated block of an earlier, broken write is left alone: it is not ours to guess where it ends
        return array_merge( $lines, $block );
    }

    /**
     * The parts that lines outside the block already run for this installation (written by hand, or by an older
     * guide): part => line. A line counts when it runs runcronjobs.php, names the installation root and is no comment.
     *
     * @param string[] $lines
     * @param string $root
     * @return array
     */
    public static function scheduledOutside( array $lines, $root )
    {
        $found = array();
        foreach ( self::strip( $lines, $root ) as $line )
        {
            $trimmed = trim( $line );
            if ( $trimmed === '' || $trimmed[0] === '#' || strpos( $trimmed, 'runcronjobs.php' ) === false )
                continue;
            if ( !self::namesRoot( $trimmed, $root ) )
                continue;
            $part = expCronjobRunner::partOfCrontabLine( $trimmed );
            if ( $part !== null && !isset( $found[$part] ) )
                $found[$part] = $trimmed;
        }
        return $found;
    }

    /**
     * Whether a crontab line names the installation root as a whole path (not /var/www/site as part of
     * /var/www/site2).
     */
    private static function namesRoot( $line, $root )
    {
        $quoted = preg_quote( rtrim( $root, '/' ), '#' );
        return (bool)preg_match( '#(^|[\s\'"=])' . $quoted . '/?($|[\s\'";&|])#', $line );
    }

    // ------------------------------------------------------------------ whose crontab

    /**
     * Whose crontab is written, and with which arguments.
     *
     * @param int|null $euid the user id of this process (null: unknown, no posix)
     * @param int|null $ownerUid the owner of the installation directory
     * @param string $ownerName
     * @return array ok, user, args (the crontab arguments before -l or -), why (when not ok)
     */
    public static function target( $euid, $ownerUid, $ownerName )
    {
        if ( $euid === null || $ownerUid === null )
            return array( 'ok' => false, 'user' => (string)$ownerName, 'args' => array(),
                          'why' => 'the user of this process or the owner of the installation cannot be told (no posix functions)' );
        if ( (int)$ownerUid === 0 )
            return array( 'ok' => false, 'user' => 'root', 'args' => array(),
                          'why' => 'the installation is owned by root; root\'s crontab is not written for a site, add the lines to the crontab of the user the site runs as' );
        if ( (int)$euid === (int)$ownerUid )
            return array( 'ok' => true, 'user' => (string)$ownerName, 'args' => array(), 'why' => '' );
        if ( (int)$euid === 0 )
            return array( 'ok' => true, 'user' => (string)$ownerName, 'args' => array( '-u', (string)$ownerName ), 'why' => '' );
        return array( 'ok' => false, 'user' => (string)$ownerName, 'args' => array(),
                      'why' => 'this process runs as another user than the owner of the installation (' . $ownerName . ') and is not root' );
    }

    /**
     * The owner of the installation directory.
     *
     * @param string $root
     * @return array uid (int|null), name
     */
    public static function owner( $root )
    {
        $uid = @fileowner( $root );
        if ( $uid === false )
            return array( 'uid' => null, 'name' => '' );
        $name = (string)$uid;
        if ( function_exists( 'posix_getpwuid' ) )
        {
            $info = @posix_getpwuid( $uid );
            if ( is_array( $info ) && isset( $info['name'] ) && $info['name'] !== '' )
                $name = $info['name'];
        }
        return array( 'uid' => (int)$uid, 'name' => $name );
    }

    // ------------------------------------------------------------------ the installation

    /**
     * The extensions active in the installation: ActiveExtensions[] of settings/override/site.ini.append.php and
     * ActiveAccessExtensions[] of the admin siteaccess.
     *
     * @param string $root
     * @param string $adminSiteaccess
     * @return string[]
     */
    public static function activeExtensions( $root, $adminSiteaccess )
    {
        $list = array();
        $files = array( rtrim( $root, '/' ) . '/settings/override/site.ini.append.php' => 'ActiveExtensions' );
        if ( $adminSiteaccess !== '' )
            $files[rtrim( $root, '/' ) . '/settings/siteaccess/' . $adminSiteaccess . '/site.ini.append.php'] = 'ActiveAccessExtensions';
        foreach ( $files as $file => $variable )
        {
            if ( !is_file( $file ) )
                continue;
            $groups = self::iniGroups( $file );
            if ( isset( $groups['ExtensionSettings'][$variable] ) && is_array( $groups['ExtensionSettings'][$variable] ) )
                $list = array_merge( $list, $groups['ExtensionSettings'][$variable] );
        }
        $list = array_values( array_unique( array_filter( array_map( 'trim', $list ), 'strlen' ) ) );
        $base = rtrim( $root, '/' ) . '/extension/';
        return array_values( array_filter( $list, function ( $extension ) use ( $base ) { return is_dir( $base . $extension ); } ) );
    }

    /**
     * The admin siteaccess of an installation: [CrontabSettings] AdminSiteAccess of settings/override, else "admin"
     * when it exists, else the first siteaccess of AvailableSiteAccessList[] whose name contains "admin", else "".
     *
     * @param string $root
     * @return string
     */
    public static function adminSiteaccess( $root )
    {
        $root = rtrim( $root, '/' );
        $override = $root . '/settings/override/cronjob.ini.append.php';
        if ( is_file( $override ) )
        {
            $groups = self::iniGroups( $override );
            if ( isset( $groups['CrontabSettings']['AdminSiteAccess'] ) && is_string( $groups['CrontabSettings']['AdminSiteAccess'] )
                 && $groups['CrontabSettings']['AdminSiteAccess'] !== '' )
                return $groups['CrontabSettings']['AdminSiteAccess'];
        }
        if ( is_dir( $root . '/settings/siteaccess/admin' ) )
            return 'admin';
        $site = $root . '/settings/override/site.ini.append.php';
        if ( is_file( $site ) )
        {
            $groups = self::iniGroups( $site );
            $list = isset( $groups['SiteAccessSettings']['AvailableSiteAccessList'] ) ? (array)$groups['SiteAccessSettings']['AvailableSiteAccessList'] : array();
            foreach ( $list as $siteaccess )
                if ( stripos( $siteaccess, 'admin' ) !== false && is_dir( $root . '/settings/siteaccess/' . $siteaccess ) )
                    return $siteaccess;
        }
        return '';
    }

    /**
     * The PHP command line binary of the lines: PhpCliPath, else the binary of this process when it is the command
     * line one, else what Setup > Cronjobs finds, else "php".
     *
     * @param string $configured PhpCliPath
     * @return string
     */
    public static function phpBinary( $configured = '' )
    {
        if ( $configured !== '' )
            return $configured;
        if ( PHP_SAPI === 'cli' && defined( 'PHP_BINARY' ) && PHP_BINARY !== '' && is_executable( PHP_BINARY ) )
            return PHP_BINARY;
        $found = class_exists( 'expCronjobRunner' ) ? expCronjobRunner::phpBinary() : false;
        return $found !== false ? $found : 'php';
    }

    /**
     * What the cron job of an installation is: its settings, parts and block.
     *
     * @param string $root
     * @param string|null $adminSiteaccess null: adminSiteaccess()
     * @return array root, admin_siteaccess, settings, entries, php
     */
    public static function describe( $root, $adminSiteaccess = null )
    {
        $root = rtrim( (string)$root, '/' );
        $admin = $adminSiteaccess === null ? self::adminSiteaccess( $root ) : (string)$adminSiteaccess;
        $settings = self::settings( $root, self::activeExtensions( $root, $admin ), $admin );
        $entries = self::entries( $settings, $admin );
        if ( $admin === '' )
        {
            // without an admin siteaccess the admin parts run with the default one
            foreach ( $entries as $i => $entry )
                $entries[$i]['siteaccess'] = '';
        }
        return array( 'root' => $root, 'admin_siteaccess' => $admin, 'settings' => $settings,
                      'entries' => $entries, 'php' => self::phpBinary( $settings['php'] ) );
    }

    // ------------------------------------------------------------------ writing

    /**
     * Adds or updates the block of an installation in the crontab of its owner.
     *
     * @param array $description as describe() gives it
     * @param bool $dryRun only say what would happen
     * @return array status (written, unchanged, dry-run, fallback, empty), user, block (the lines), skipped
     *               (part => the line outside the block that runs it), before, after (the crontab), message
     */
    public function add( array $description, $dryRun = false )
    {
        $root = $description['root'];
        $outside = array();
        $result = array( 'status' => '', 'user' => '', 'block' => array(), 'skipped' => array(), 'before' => array(),
                         'after' => array(), 'message' => '' );

        $state = $this->open( $root, $description['settings']['command'] );
        $result['user'] = $state['user'];
        if ( $state['ok'] )
            $outside = self::scheduledOutside( $state['lines'], $root );

        $entries = array();
        foreach ( $description['entries'] as $entry )
        {
            if ( isset( $outside[$entry['part']] ) )
                $result['skipped'][$entry['part']] = $outside[$entry['part']];
            else
                $entries[] = $entry;
        }
        $block = self::block( $entries, $root, $description['php'] );
        $result['block'] = $block;

        if ( !$state['ok'] )
            return array_merge( $result, array( 'status' => 'fallback', 'block' => self::block( $description['entries'], $root, $description['php'] ),
                                                'message' => $state['why'] ) );

        $after = self::merge( $state['lines'], $block, $root );
        $result['before'] = $state['lines'];
        $result['after'] = $after;
        if ( !$block && self::blockRange( $state['lines'], $root ) === null )
            return array_merge( $result, array( 'status' => 'empty', 'message' => $result['skipped'] ? 'every part runs already from lines outside the block' : 'no cronjob part to schedule' ) );
        if ( $after === $state['lines'] )
            return array_merge( $result, array( 'status' => 'unchanged', 'message' => 'the crontab of ' . $state['user'] . ' has these lines already' ) );
        if ( $dryRun )
            return array_merge( $result, array( 'status' => 'dry-run', 'message' => 'would write the crontab of ' . $state['user'] ) );

        $written = $this->write( $state, $after );
        if ( $written !== true )
            return array_merge( $result, array( 'status' => 'fallback', 'message' => $written ) );
        return array_merge( $result, array( 'status' => 'written', 'message' => 'wrote the crontab of ' . $state['user'] ) );
    }

    /**
     * Takes the block of an installation out of the crontab of its owner.
     *
     * @param array $description as describe() gives it
     * @param bool $dryRun
     * @return array status (removed, absent, dry-run, fallback), user, block (the lines taken out), before, after, message
     */
    public function remove( array $description, $dryRun = false )
    {
        $root = $description['root'];
        $result = array( 'status' => '', 'user' => '', 'block' => array(), 'skipped' => array(), 'before' => array(),
                         'after' => array(), 'message' => '' );
        $state = $this->open( $root, $description['settings']['command'] );
        $result['user'] = $state['user'];
        if ( !$state['ok'] )
            return array_merge( $result, array( 'status' => 'fallback', 'message' => $state['why'] ) );
        $range = self::blockRange( $state['lines'], $root );
        if ( $range === null )
            return array_merge( $result, array( 'status' => 'absent', 'message' => 'the crontab of ' . $state['user'] . ' has no lines of this installation\'s block' ) );
        $result['block'] = array_slice( array_values( $state['lines'] ), $range[0], $range[1] - $range[0] + 1 );
        $result['before'] = $state['lines'];
        $result['after'] = self::strip( $state['lines'], $root );
        if ( $dryRun )
            return array_merge( $result, array( 'status' => 'dry-run', 'message' => 'would remove the block from the crontab of ' . $state['user'] ) );
        $written = $this->write( $state, $result['after'] );
        if ( $written !== true )
            return array_merge( $result, array( 'status' => 'fallback', 'message' => $written ) );
        return array_merge( $result, array( 'status' => 'removed', 'message' => 'removed the block from the crontab of ' . $state['user'] ) );
    }

    /**
     * The crontab of the installation's owner as it is.
     *
     * @param array $description
     * @return array ok, user, why, lines, block (the installation's block in it), outside (part => line)
     */
    public function show( array $description )
    {
        $state = $this->open( $description['root'], $description['settings']['command'] );
        $state['block'] = array();
        $state['outside'] = array();
        if ( $state['ok'] )
        {
            $range = self::blockRange( $state['lines'], $description['root'] );
            if ( $range !== null )
                $state['block'] = array_slice( array_values( $state['lines'] ), $range[0], $range[1] - $range[0] + 1 );
            $state['outside'] = self::scheduledOutside( $state['lines'], $description['root'] );
        }
        return $state;
    }

    /**
     * Reads the crontab of the installation's owner.
     *
     * @return array ok, user, why, args, binary, lines
     */
    private function open( $root, $configuredCommand )
    {
        $owner = $this->owner !== null ? $this->owner : self::owner( $root );
        $euid = $this->euid !== null ? (int)$this->euid : ( function_exists( 'posix_geteuid' ) ? posix_geteuid() : null );
        $target = self::target( $euid, $owner['uid'], $owner['name'] );
        $state = array( 'ok' => false, 'user' => $target['user'], 'why' => $target['why'], 'args' => $target['args'],
                        'binary' => '', 'lines' => array() );
        if ( !$target['ok'] )
            return $state;

        $binary = self::findBinary( $this->command !== null ? $this->command : ( $configuredCommand !== '' ? $configuredCommand : 'crontab' ) );
        if ( $binary === false )
            return array_merge( $state, array( 'why' => 'there is no crontab command on this system (or it cannot be run)' ) );
        $state['binary'] = $binary;

        list( $status, $out, $err ) = call_user_func( $this->runner, array_merge( array( $binary ), $target['args'], array( '-l' ) ), null );
        if ( $status !== 0 )
        {
            // "no crontab for <user>" is an empty crontab; anything else (not allowed, no spool) is a reason to stop
            if ( stripos( $err . $out, 'no crontab for' ) === false )
                return array_merge( $state, array( 'why' => 'crontab -l failed (exit ' . $status . '): ' . trim( $err !== '' ? $err : $out ) ) );
            $out = '';
        }
        $lines = $out === '' ? array() : explode( "\n", rtrim( str_replace( "\r\n", "\n", $out ), "\n" ) );
        return array_merge( $state, array( 'ok' => true, 'why' => '', 'lines' => $lines ) );
    }

    /**
     * Installs the new crontab and reads it back.
     *
     * @return true|string true, or why it failed
     */
    private function write( array $state, array $lines )
    {
        $text = $lines ? implode( "\n", $lines ) . "\n" : '';
        list( $status, $out, $err ) = call_user_func( $this->runner, array_merge( array( $state['binary'] ), $state['args'], array( '-' ) ), $text );
        if ( $status !== 0 )
            return 'crontab - failed (exit ' . $status . '): ' . trim( $err !== '' ? $err : $out );
        list( $status, $out, $err ) = call_user_func( $this->runner, array_merge( array( $state['binary'] ), $state['args'], array( '-l' ) ), null );
        $read = $out === '' ? array() : explode( "\n", rtrim( str_replace( "\r\n", "\n", $out ), "\n" ) );
        if ( ( $status !== 0 && $lines ) || $read !== array_values( $lines ) )
            return 'the crontab read back differs from the one written';
        return true;
    }

    /**
     * The crontab binary: a path that can be run, or a name found on the PATH (and in /usr/bin, /bin).
     *
     * @param string $command
     * @return string|false
     */
    public static function findBinary( $command )
    {
        $command = trim( (string)$command );
        if ( $command === '' )
            return false;
        if ( strpos( $command, '/' ) !== false )
            return is_file( $command ) && is_executable( $command ) ? $command : false;
        $path = (string)getenv( 'PATH' );
        foreach ( array_unique( array_merge( explode( ':', $path ), array( '/usr/bin', '/bin', '/usr/local/bin' ) ) ) as $dir )
        {
            if ( $dir === '' )
                continue;
            $file = rtrim( $dir, '/' ) . '/' . $command;
            if ( is_file( $file ) && is_executable( $file ) )
                return $file;
        }
        return false;
    }

    /**
     * Runs a command without a shell.
     *
     * @param string[] $argv
     * @param string|null $stdin
     * @return array status, stdout, stderr
     */
    public static function runCommand( array $argv, $stdin )
    {
        if ( !function_exists( 'proc_open' ) )
            return array( 127, '', 'proc_open is disabled' );
        $pipes = array();
        $process = @proc_open( $argv, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        if ( !is_resource( $process ) )
            return array( 127, '', 'the command could not be started' );
        if ( $stdin !== null )
            fwrite( $pipes[0], $stdin );
        fclose( $pipes[0] );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $status = proc_close( $process );
        return array( (int)$status, (string)$out, (string)$err );
    }

    // ------------------------------------------------------------------ the installers

    /**
     * The installers' step (eZStepFinal, so the setup wizard, the kickstarter and exp:install): writes the cron job of
     * the new installation unless switched off. Never fails the installation.
     *
     * @param string $root
     * @return array the result of add() with status "disabled" when switched off, plus lines: what to print
     */
    public static function forNewInstallation( $root )
    {
        $root = rtrim( (string)$root, '/' );
        if ( !self::enabled( $root ) )
        {
            $description = self::describe( $root );
            $result = array( 'status' => 'disabled', 'user' => '', 'skipped' => array(),
                             'block' => self::block( $description['entries'], $root, $description['php'] ),
                             'message' => 'switched off ([CrontabSettings] Install=disabled or EXP_INSTALL_CRONTAB=0)' );
        }
        else
        {
            try
            {
                $setup = new self();
                $result = $setup->add( self::describe( $root ) );
            }
            catch ( Exception $e )
            {
                $result = array( 'status' => 'fallback', 'user' => '', 'skipped' => array(), 'block' => array(),
                                 'message' => $e->getMessage() );
            }
        }
        $result['lines'] = self::report( $result );
        return $result;
    }

    /**
     * What the installer prints about the cron job.
     *
     * @param array $result of add(), remove() or forNewInstallation()
     * @return string[]
     */
    public static function report( array $result )
    {
        $lines = array();
        $cron = array_values( array_filter( $result['block'], function ( $l ) { return $l !== '' && $l[0] !== '#'; } ) );
        switch ( $result['status'] )
        {
            case 'written':
            case 'unchanged':
                $lines[] = 'Cron job: ' . ( $result['status'] === 'written' ? 'added to' : 'already in' ) . ' the crontab of ' . $result['user']
                         . ' (' . count( $cron ) . ' line' . ( count( $cron ) === 1 ? '' : 's' ) . '; php bin/php/installcrontab.php --show)';
                break;
            case 'empty':
                $lines[] = 'Cron job: nothing to add (' . $result['message'] . ')';
                break;
            case 'disabled':
                $lines[] = 'Cron job: not written, ' . $result['message'] . '. To run the cronjobs, add to the crontab of the site\'s user:';
                break;
            default:
                $lines[] = 'Cron job: not written: ' . $result['message'] . '. Add these lines to the crontab of '
                         . ( $result['user'] !== '' && $result['user'] !== 'root' ? $result['user'] : 'the user the site runs as' ) . ':';
        }
        if ( !in_array( $result['status'], array( 'written', 'unchanged', 'empty' ), true ) )
            foreach ( $result['block'] as $line )
                $lines[] = '  ' . $line;
        foreach ( $result['skipped'] as $part => $line )
            $lines[] = 'Cron job: part ' . $part . ' left out, an existing line runs it: ' . $line;
        return $lines;
    }

    // ------------------------------------------------------------------ when the parts ran

    /**
     * Records that runcronjobs.php started or finished a part (var/log/cronjob-lastrun.json). Never fails the job.
     *
     * @param string $part '' or "global" for the scripts of [CronjobSettings]
     * @param string $siteaccess
     * @param string $phase "started" or "finished"
     * @param int|null $now
     * @param string|null $root
     * @return bool
     */
    public static function recordRun( $part, $siteaccess, $phase, $now = null, $root = null )
    {
        $part = $part === '' ? self::GLOBAL_PART : (string)$part;
        $file = ( $root === null ? self::root() : rtrim( $root, '/' ) ) . '/' . self::RUN_FILE;
        $dir = dirname( $file );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0775 ), true ) )
            return false;
        $handle = @fopen( $file, 'c+' );
        if ( $handle === false )
            return false;
        $ok = false;
        if ( @flock( $handle, LOCK_EX ) )
        {
            $runs = json_decode( (string)stream_get_contents( $handle ), true );
            $runs = self::addRun( is_array( $runs ) ? $runs : array(), $part, (string)$siteaccess, $phase, $now === null ? time() : (int)$now );
            ftruncate( $handle, 0 );
            rewind( $handle );
            $ok = fwrite( $handle, json_encode( $runs ) ) !== false;
            fflush( $handle );
            flock( $handle, LOCK_UN );
        }
        fclose( $handle );
        // a run as root keeps the file of the user the cron job runs as
        if ( function_exists( 'posix_geteuid' ) && posix_geteuid() === 0 )
        {
            $owner = @fileowner( $dir );
            if ( $owner !== false && $owner !== 0 )
                @chown( $file, $owner );
        }
        return $ok;
    }

    /**
     * The run record with one start or finish more.
     *
     * @param array $runs part => started, finished, siteaccess
     * @return array
     */
    public static function addRun( array $runs, $part, $siteaccess, $phase, $now )
    {
        $run = isset( $runs[$part] ) && is_array( $runs[$part] ) ? $runs[$part] : array( 'started' => 0, 'finished' => 0, 'siteaccess' => '' );
        if ( $phase === 'finished' )
            $run['finished'] = (int)$now;
        else
        {
            $run['started'] = (int)$now;
            $run['siteaccess'] = (string)$siteaccess;
        }
        $runs[$part] = $run;
        ksort( $runs );
        return $runs;
    }

    /**
     * The run record of the installation.
     *
     * @param string|null $root
     * @return array part => started, finished, siteaccess
     */
    public static function lastRuns( $root = null )
    {
        $file = ( $root === null ? self::root() : rtrim( $root, '/' ) ) . '/' . self::RUN_FILE;
        if ( !is_file( $file ) )
            return array();
        $runs = json_decode( (string)@file_get_contents( $file ), true );
        return is_array( $runs ) ? $runs : array();
    }

    /**
     * Whether each part ran when its schedule says it should have: "ok", "late" (no start for twice the interval of
     * its schedule and five minutes more, at least an hour), or "never".
     *
     * @param array $entries as entries() gives them
     * @param array $runs as lastRuns() gives them
     * @param int $now
     * @return array overall (ok; late when a part is late or never ran; never when no part ever ran), last (newest start, 0 for none), parts: each part, schedule, siteaccess,
     *               started, finished, state, allowed (seconds)
     */
    public static function runState( array $entries, array $runs, $now )
    {
        $parts = array();
        $last = 0;
        foreach ( $runs as $run )
            if ( isset( $run['started'] ) )
                $last = max( $last, (int)$run['started'] );
        $overall = 'ok';
        foreach ( $entries as $entry )
        {
            $allowed = self::allowedGap( $entry['schedule'], $now );
            $run = isset( $runs[$entry['part']] ) ? $runs[$entry['part']] : null;
            $started = $run ? (int)$run['started'] : 0;
            if ( $started === 0 )
                $state = 'never';
            else if ( $now - $started > $allowed )
                $state = 'late';
            else
                $state = 'ok';
            if ( $state !== 'ok' )
                $overall = $last === 0 ? 'never' : 'late';
            $parts[]= array( 'part' => $entry['part'], 'schedule' => $entry['schedule'], 'siteaccess' => $entry['siteaccess'],
                              'started' => $started, 'finished' => $run ? (int)$run['finished'] : 0, 'state' => $state,
                              'allowed' => $allowed );
        }
        return array( 'overall' => $overall, 'last' => $last, 'parts' => $parts );
    }

    /**
     * How long a part may go without a start: twice the interval of its schedule (from the next run to the one
     * after it) and five minutes, at least an hour; a day and a half when the schedule cannot be read.
     */
    public static function allowedGap( $schedule, $now )
    {
        $next = expCronjobRunner::nextRun( $schedule, $now );
        $after = $next !== false ? expCronjobRunner::nextRun( $schedule, $next ) : false;
        if ( $next === false || $after === false )
            return 129600;
        return max( 3600, 2 * ( $after - $next ) + 300 );
    }

    /**
     * The cron job's state for the dashboard block and fetch( 'setup', 'cronjob_status' ).
     *
     * @param string|null $root
     * @param int|null $now
     * @return array enabled, overall, last, parts (runState()), admin_siteaccess
     */
    public static function status( $root = null, $now = null )
    {
        $root = $root === null ? self::root() : rtrim( $root, '/' );
        $description = self::describe( $root );
        $state = self::runState( $description['entries'], self::lastRuns( $root ), $now === null ? time() : (int)$now );
        return array_merge( $state, array( 'enabled' => self::enabled( $root ), 'admin_siteaccess' => $description['admin_siteaccess'] ) );
    }

    /**
     * The installation root: two directories above this file, through symlinks (the editor vhost is a tree of them).
     */
    public static function root()
    {
        $root = realpath( __DIR__ . '/../..' );
        return $root !== false ? $root : rtrim( getcwd(), '/' );
    }

    // ------------------------------------------------------------------ helpers

    /**
     * The groups of one INI file at any path (eZINI reads below the installation root it runs in only): [Group],
     * Name=value, Name[]=value, Name[key]=value, an empty Name[] resetting the list, # comments, and the
     * <?php /* ... *\/ ?> wrapper of .ini.append.php files.
     *
     * @param string $file
     * @return array group => name => value or list
     */
    public static function iniGroups( $file )
    {
        $groups = array();
        $group = null;
        foreach ( preg_split( '/\r\n|\r|\n/', (string)@file_get_contents( $file ) ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' || $line[0] === '#' || strpos( $line, '<?php' ) === 0 || strpos( $line, '*/' ) === 0 )
                continue;
            if ( preg_match( '/^\[([^\]]+)\]$/', $line, $m ) )
            {
                $group = $m[1];
                if ( !isset( $groups[$group] ) )
                    $groups[$group] = array();
                continue;
            }
            if ( $group === null || !preg_match( '/^([A-Za-z0-9_.-]+)(\[([^\]]*)\])?(=(.*))?$/', $line, $m ) )
                continue;
            $name = $m[1];
            $isList = isset( $m[2] ) && $m[2] !== '';
            $hasValue = isset( $m[4] ) && $m[4] !== '';
            $value = $hasValue ? rtrim( $m[5] ) : '';
            if ( !$isList )
            {
                if ( $hasValue )
                    $groups[$group][$name] = $value;
                continue;
            }
            if ( !isset( $groups[$group][$name] ) || !is_array( $groups[$group][$name] ) )
                $groups[$group][$name] = array();
            if ( !$hasValue )
            {
                // Name[] alone empties the list
                if ( $m[3] === '' )
                    $groups[$group][$name] = array();
                continue;
            }
            if ( $m[3] !== '' )
                $groups[$group][$name][$m[3]] = $value;
            else
                $groups[$group][$name][] = $value;
        }
        return $groups;
    }

    private static function shellWord( $word )
    {
        $word = (string)$word;
        return preg_match( '#^[A-Za-z0-9_./:=@+-]+$#', $word ) ? $word : escapeshellarg( $word );
    }
}
