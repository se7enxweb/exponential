<?php
/**
 * File containing the expDemoMode class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Demo mode: the environment variable EXP_DEMO_MODE=1 marks an installation as a public demo.
 *
 * While it is on:
 *  - the password of an administrator cannot be changed or reset by any path: user/password, "Forgot your
 *    password?", the user account in content/edit, exp:resetuserpassword (bin/php/resetuserpassword.php), the web
 *    services and, as the last line, eZUser::store(), which keeps the stored password of an administrator whatever
 *    the caller did (guardStore()). The installers themselves set the administrator password inside installing();
 *  - the installers (setup wizard, kickstarter, exp:install and the demo installers built on them) accept a
 *    well-known administrator password of any length, such as the demo's published publish or publishing
 *    (eZStepSiteAdmin::isWeakDefaultPassword(), isTooShortAdminPassword(), settingInstallationAdministratorPassword());
 *    an empty one never, and every other account keeps every rule;
 *  - the administration shows a banner (template operator exp_demo_mode()), and each process records once that the
 *    mode is on: var/log/security.log and the audit event system.demo.mode (announce()).
 *
 * It can only be switched on from outside the installation: the process environment, or the server's environment
 * for PHP (Apache SetEnv, a PHP-FPM pool's env[], the environment Exponential Velocity is started with). There is no
 * INI setting and no request parameter for it, and only the exact value "1" counts: "0", "true", "yes" and an empty
 * value leave it off. Nothing is cached between calls, so a persistent worker (Velocity, FrankenPHP) always answers
 * for the environment it has now. Under Exponential Velocity a value the engine took from an envvars file that other
 * installations share (/etc/vc/envvars) is not taken: it is set per installation (isSharedEnvvarsValue()).
 * Guide: doc/eng-us/bc/6.0/demo-mode.md.
 *
 * An administrator is an account with unlimited access to every function of every module (a policy for all modules
 * and all functions without a limitation, as the role Administrator has); see isAdministrator().
 *
 * @package kernel
 */
class expDemoMode
{
    /** The environment variable */
    const ENV = 'EXP_DEMO_MODE';

    /** The one value that switches demo mode on */
    const ON = '1';

    /** @var int depth of installing() calls in progress */
    protected static $installing = 0;

    /** @var int depth of settingInstallationAdministratorPassword() calls in progress */
    protected static $adminPassword = 0;

    /** @var string|null the process (pid) whose start was recorded by announce() */
    protected static $announcedFor = null;

    /** @var string|null the process (pid) that recorded a refused shared envvars value (noteSharedVelocityEnvvars()) */
    protected static $sharedNotedFor = null;

    /**
     * The raw value: getenv() first (the process environment, and under PHP-FPM the FastCGI parameters), then
     * $_SERVER (Apache SetEnv with mod_php and the like). Never $_GET, $_POST, $_COOKIE or an INI file.
     *
     * @return string the trimmed value, an empty string when it is not set
     */
    public static function value()
    {
        $value = getenv( self::ENV );
        if ( is_string( $value ) && trim( $value ) !== '' )
            $value = trim( $value );
        else if ( isset( $_SERVER[self::ENV] ) && is_scalar( $_SERVER[self::ENV] ) )
            $value = trim( (string)$_SERVER[self::ENV] );
        else
            return '';
        if ( $value !== '' && self::fromSharedVelocityEnvvars( $value ) )
        {
            self::noteSharedVelocityEnvvars();
            return '';
        }
        return $value;
    }

    /**
     * Whether the value in this Exponential Velocity worker came from an envvars file the engine read at start-up
     * that other installations share (/etc/vc/envvars, /etc/qbix/envvars): such a value is not taken, so a demo can
     * never switch demo mode on for every installation the host serves with Velocity. The per-installation places
     * are the environment that starts this installation's server and its site envvars file
     * (sites-available/<site>.envvars, passed by exp:velocity start; expVelocityConfigLayout::siteExportable()).
     * Outside Velocity (PHP-FPM, mod_php, the console) this is always false.
     *
     * @param string $value the value in force
     * @return bool
     */
    protected static function fromSharedVelocityEnvvars( $value )
    {
        if ( !class_exists( 'Q_WebServer_Layout', false ) || !isset( Q_WebServer_Layout::$exported ) )
            return false;
        $confDirs = class_exists( 'Q_Config', false ) ? (array)Q_Config::get( 'Q', 'webserver', 'confDirs', array() ) : array();
        return self::isSharedEnvvarsValue( (array)Q_WebServer_Layout::$exported, $confDirs, (string)getcwd(), $value );
    }

    /**
     * The decision of fromSharedVelocityEnvvars(), pure for the tests. A value counts as shared when the engine
     * exported it from an envvars file (it is in $exported with the value in force) and one of the configuration
     * directories it read is outside the installation (or the directories are unknown). A tree inside the
     * installation (var/vc/qbix/etc, used by an account without root) belongs to this installation alone.
     *
     * @param array $exported Q_WebServer_Layout::$exported: name => value the engine put into the environment
     * @param array $confDirs the configuration directories the engine read (Q_Config Q/webserver/confDirs)
     * @param string $root the installation directory
     * @param string $value the value in force
     * @return bool
     */
    public static function isSharedEnvvarsValue( array $exported, array $confDirs, $root, $value )
    {
        if ( !isset( $exported[self::ENV] ) || trim( (string)$exported[self::ENV] ) !== trim( (string)$value ) )
            return false;
        $confDirs = array_values( array_filter( array_map( 'strval', $confDirs ), 'strlen' ) );
        if ( !$confDirs || (string)$root === '' )
            return true;
        $rootReal = realpath( (string)$root );
        $rootPrefix = rtrim( $rootReal !== false ? $rootReal : (string)$root, '/' ) . '/';
        foreach ( $confDirs as $dir )
        {
            $real = realpath( $dir );
            $path = rtrim( $real !== false ? $real : $dir, '/' ) . '/';
            if ( strpos( $path, $rootPrefix ) !== 0 )
                return true;
        }
        return false;
    }

    /**
     * Records once per process that a shared envvars value was not taken: a warning in the debug output and a line in
     * var/log/security.log (demo/refused ...), so whoever wonders why demo mode is off under Velocity finds the reason.
     */
    protected static function noteSharedVelocityEnvvars()
    {
        $process = (string)getmypid();
        if ( self::$sharedNotedFor === $process )
            return;
        self::$sharedNotedFor = $process;
        $line = 'demo/refused pid=' . preg_replace( '/[^0-9]/', '', $process ) . ' ' . self::ENV
              . '=1 came from an envvars file other installations share (the Velocity configuration directory), so demo mode'
              . ' stays off; set it per installation: sites-available/<site>.envvars or the environment that starts this server';
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeWarning( $line, __METHOD__ );
        try
        {
            self::writeSecurityLine( $line );
        }
        catch ( Throwable $e )
        {
        }
    }

    /**
     * @return bool demo mode is on (EXP_DEMO_MODE is exactly "1"); read again on every call
     */
    public static function enabled()
    {
        return self::value() === self::ON;
    }

    /**
     * Whether an account counts as an administrator: unlimited access to every function of every module. When the
     * roles cannot be read the account counts as one, so an error never opens the password of an administrator.
     *
     * @param eZUser|int|null $user
     * @return bool
     */
    public static function isAdministrator( $user )
    {
        if ( !( $user instanceof eZUser ) )
        {
            if ( !is_numeric( $user ) || (int)$user <= 0 || !class_exists( 'eZUser' ) )
                return false;
            $user = eZUser::fetch( (int)$user );
            if ( !( $user instanceof eZUser ) )
                return false;
        }
        try
        {
            return self::accessArrayIsUnlimited( $user->accessArray() );
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'Could not read the roles of user ' . (int)$user->attribute( 'contentobject_id' )
                                       . ', so demo mode treats the account as an administrator: ' . $e->getMessage(), __METHOD__ );
            return true;
        }
    }

    /**
     * @param mixed $accessArray eZUser::accessArray()
     * @return bool it grants every function of every module without limitation ('*' => '*' => '*')
     */
    public static function accessArrayIsUnlimited( $accessArray )
    {
        if ( !is_array( $accessArray ) || !isset( $accessArray['*']['*'] ) || !is_array( $accessArray['*']['*'] ) )
            return false;
        $functions = $accessArray['*']['*'];
        if ( !isset( $functions['*'] ) )
            return false;
        return $functions['*'] === '*' || ( is_array( $functions['*'] ) && in_array( '*', $functions['*'], true ) );
    }

    /**
     * @param eZUser|int|null $user
     * @return bool demo mode is on and the account's password may not be changed or reset
     */
    public static function protectsPassword( $user )
    {
        return self::enabled() && self::isAdministrator( $user );
    }

    /**
     * @return string the message every refusal shows, translated
     */
    public static function refusalText()
    {
        $text = 'This installation is a public demo: the passwords of administrators cannot be changed or reset.';
        return class_exists( 'ezpI18n' ) ? ezpI18n::tr( 'kernel/user/demo', $text ) : $text;
    }

    /**
     * Records a refusal: the audit (reason demo_mode, never a password) and the debug output.
     *
     * @param eZUser|int|null $user
     * @param string $path where it was refused (user/password, user/forgotpassword, console, services, store ...)
     * @param string $event access.user.password.change.failed or access.user.password.reset.failed
     */
    public static function refused( $user, $path, $event = 'access.user.password.change.failed' )
    {
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( $event, array( 'object' => expAuditHook::user( $user ), 'result' => 'refused',
                                               'reason' => 'demo_mode', 'after' => array( 'path' => (string)$path ) ) );
        if ( class_exists( 'eZDebug' ) )
        {
            $id = $user instanceof eZUser ? (int)$user->attribute( 'contentobject_id' ) : (int)$user;
            eZDebug::writeWarning( "Demo mode (EXP_DEMO_MODE=1): the password of administrator $id was not changed ($path)", __METHOD__ );
        }
    }

    /**
     * Runs an installer's work that sets the administrator password: the guard of eZUser::store() lets it through.
     * The scope ends with the call, also on an exception, so nothing of it reaches the next request of a
     * persistent worker.
     *
     * @param callable $code
     * @return mixed what $code returns
     */
    public static function installing( $code )
    {
        self::$installing++;
        try
        {
            return $code();
        }
        finally
        {
            self::$installing--;
        }
    }

    /**
     * @return bool installing() is running
     */
    public static function isInstalling()
    {
        return self::$installing > 0;
    }

    /**
     * Runs the installation's own setting of the administrator password (the setup wizard, the kickstarter and
     * exp:install, through eZStepCreateSites): in demo mode, inside installing(), eZUser::validatePassword() then
     * accepts a non-empty password of any length, so the demo's published password (publish) is installed. Every
     * other account and every later password change keep MinPasswordLength. The scope ends with the call.
     *
     * @param callable $code
     * @return mixed what $code returns
     */
    public static function settingInstallationAdministratorPassword( $code )
    {
        self::$adminPassword++;
        try
        {
            return $code();
        }
        finally
        {
            self::$adminPassword--;
        }
    }

    /**
     * Whether eZUser::validatePassword() lets a short (non-empty) password through: demo mode is on and the
     * installation is setting its administrator password (installing() and settingInstallationAdministratorPassword()).
     *
     * @return bool
     */
    public static function acceptsShortInstallationAdministratorPassword()
    {
        return self::$adminPassword > 0 && self::isInstalling() && self::enabled();
    }

    /**
     * Whether a store may write the password it carries. Pure, for the guard and the tests.
     *
     * @param array|false|null $stored the stored row (password_hash, password_hash_type), false or null: none yet
     * @param string|null $hash the hash the store writes
     * @param mixed $hashType its type
     * @param bool $enabled demo mode is on
     * @param bool $installing installing() is running
     * @param callable $isAdministrator called (only when it matters) to say whether the account is an administrator
     * @return bool
     */
    public static function storeAllowed( $stored, $hash, $hashType, $enabled, $installing, $isAdministrator )
    {
        if ( !$enabled || $installing || !is_array( $stored ) )
            return true;
        $unchanged = (string)$stored['password_hash'] === (string)$hash
                     && (string)$stored['password_hash_type'] === (string)$hashType;
        if ( $unchanged )
            return true;
        return !$isAdministrator();
    }

    /**
     * The guard of eZUser::store(): while demo mode is on, an administrator's stored password stays as it is. A store
     * that would change it writes everything else and keeps the stored hash and its type; the refusal is recorded.
     *
     * @param eZUser $user the account about to be stored
     * @return bool true when the store writes the password it carries, false when the stored one was put back
     */
    public static function guardStore( eZUser $user )
    {
        if ( !self::enabled() || self::isInstalling() )
            return true;
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( $id <= 0 )
            return true;
        $stored = eZPersistentObject::fetchObject( eZUser::definition(), null, array( 'contentobject_id' => $id ), false );
        $allowed = self::storeAllowed( $stored, $user->attribute( 'password_hash' ), $user->attribute( 'password_hash_type' ),
                                       true, false, function () use ( $user ) { return expDemoMode::isAdministrator( $user ); } );
        if ( $allowed )
            return true;
        $user->setAttribute( 'password_hash', $stored['password_hash'] );
        $user->setAttribute( 'password_hash_type', $stored['password_hash_type'] );
        self::refused( $user, 'store' );
        return false;
    }

    /**
     * Records once per process that demo mode is on: a line in var/log/security.log and the audit event
     * system.demo.mode. The kernel calls it at the start of every web request and every script; a persistent worker
     * records it once (a forked worker has its own pid, so it records it too).
     *
     * @return bool something was recorded
     */
    public static function announce()
    {
        if ( !self::claimAnnouncement() )
            return false;
        $process = (string)getmypid();
        try
        {
            self::writeSecurityLine( self::securityLine( $process, PHP_SAPI ) );
        }
        catch ( Throwable $e )
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeWarning( 'Could not write the demo mode line to security.log: ' . $e->getMessage(), __METHOD__ );
        }
        if ( class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'system.demo.mode', array( 'object' => array( 'type' => 'installation' ), 'severity' => 'warning',
                                                           'after' => array( 'sapi' => PHP_SAPI, 'pid' => (int)$process ) ) );
        return true;
    }

    /**
     * The security.log line, without the date and address prefix, in the form the log viewer groups by its first word
     * (<category>/<rule>, as the request classifier writes it): demo/mode on pid=<pid> sapi=<sapi> ...
     *
     * @param string $process the pid
     * @param string $sapi PHP_SAPI
     * @return string
     */
    public static function securityLine( $process, $sapi )
    {
        return 'demo/mode on pid=' . preg_replace( '/[^0-9]/', '', (string)$process )
             . ' sapi=' . preg_replace( '/[^A-Za-z0-9_.\-]/', '', (string)$sapi )
             . ' ' . self::ENV . '=1: administrator passwords cannot be changed or reset, the installers accept a'
             . ' well-known administrator password; never use it in production';
    }

    /**
     * Appends a line to security.log in eZDebug's log directory (var/log by default), with the date and the masked
     * address in front, as the request classifier does (rotated like eZDebug's logs).
     *
     * @param string $line
     */
    protected static function writeSecurityLine( $line )
    {
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) && is_string( $_SERVER['REMOTE_ADDR'] ) && PHP_SAPI !== 'cli' ? $_SERVER['REMOTE_ADDR'] : '';
        if ( class_exists( 'expRequestClassifier' ) && class_exists( 'eZDebug' ) )
        {
            $file = rtrim( eZDebug::instance()->logDirectory(), '/' ) . '/security.log';
            expRequestClassifier::appendLine( $file, expRequestClassifier::prefix( $ip, true ) . $line );
        }
        else if ( class_exists( 'eZLog' ) )
            eZLog::write( $line, 'security.log' );
    }

    /**
     * Whether this process still has to record that demo mode is on; the first call of a process (while the mode is
     * on) says yes and claims it, every later one says no.
     *
     * A web SAPI that starts every request afresh (PHP-FPM, mod_php, LiteSpeed, PHP's built-in server) forgets the
     * static between requests of the same process, which recorded a line and an audit event for every request. There
     * the claim is also kept as an empty marker file per process in the cache directory (processMarker()), so each
     * process records once; clearing the caches lets the running processes record again. The console and the
     * persistent workers of Velocity and FrankenPHP keep the static and need no file.
     *
     * @return bool
     */
    public static function claimAnnouncement()
    {
        if ( !self::enabled() )
            return false;
        $process = (string)getmypid();
        if ( self::$announcedFor === $process )
            return false;
        self::$announcedFor = $process;
        if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' )
            return true;
        $marker = self::processMarker( $process );
        if ( $marker === null )
            return true;
        if ( is_file( $marker ) )
            return false;
        if ( !is_dir( dirname( $marker ) ) )
            @mkdir( dirname( $marker ), 0770, true );
        @touch( $marker );
        return true;
    }

    /**
     * The marker file of a web process that recorded demo mode (claimAnnouncement()): <cache directory>/demo-mode/
     * <pid>-<start time>, the start time from /proc so a reused pid is a new process; null when there is no cache
     * directory yet.
     *
     * @param string $process the pid
     * @return string|null
     */
    protected static function processMarker( $process )
    {
        if ( !class_exists( 'eZSys' ) )
            return null;
        try
        {
            $dir = (string)eZSys::cacheDirectory();
        }
        catch ( Throwable $e )
        {
            return null;
        }
        if ( $dir === '' )
            return null;
        $start = '';
        $stat = @file_get_contents( '/proc/self/stat' );
        if ( is_string( $stat ) && ( $close = strrpos( $stat, ')' ) ) !== false )
        {
            $fields = explode( ' ', trim( substr( $stat, $close + 2 ) ) );
            $start = isset( $fields[19] ) ? preg_replace( '/[^0-9]/', '', $fields[19] ) : '';
        }
        return rtrim( $dir, '/' ) . '/demo-mode/' . preg_replace( '/[^0-9]/', '', (string)$process ) . ( $start !== '' ? '-' . $start : '' );
    }

    /**
     * Forgets the recorded process (tests).
     */
    public static function resetAnnounced()
    {
        self::$announcedFor = null;
        self::$sharedNotedFor = null;
    }
}
?>
