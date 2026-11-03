<?php
/**
 * File containing the expMigrationTrigger class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The signed HTTP trigger that runs the migrations of an installation that a deploy reaches by FTP only
 * (expMigrationRemoteDeploy): FTP sends files but runs nothing, so the destination offers the view migration/trigger,
 * and the source signs every request to it with a secret both sides know by name.
 *
 * The source (a remote with Transport=ftp or ftps and Trigger=<address of migration/trigger>) sends
 *
 *   POST <Trigger>
 *   Content-Type: application/json
 *   X-Exp-Migration-Trigger: v1.<issued unix time>.<nonce, 32 hex>.<HMAC-SHA256, base64url>
 *
 *   {"action":"status|dry-run|apply|rollback|heal", ...}
 *
 * The HMAC is taken over "migration-trigger/1|<issued>|<nonce>|<SHA-256 of the body, hex>" with the secret
 * (TriggerSecret on the source, [TriggerSettings] Secret on the destination: a name of the secret store, or
 * env:VARIABLE). It is signed afresh for every request, also when a request is tried again after a connection failure.
 *
 * The destination (migration.ini [TriggerSettings]) takes a request only when all of this holds, and answers with a
 * status and a JSON body (never a page):
 *
 *   405  not a POST                                      404  Trigger is not enabled
 *   403  REMOTE_ADDR is not in AllowedAddresses[]       413  the body is over MAX_BODY bytes
 *   503  the secret is not set                           401  no signature, a wrong one, expired or from the future
 *   429  more than MaxRequestsPerMinute                  409  the nonce was used before (a replay)
 *   400  the body is not a request it knows              502  the console script gave no report
 *   200  the console script's report (exp-migration/1)
 *
 * The address is the connection's (REMOTE_ADDR), never a forwarded header; AllowedAddresses[] holds addresses and
 * CIDR ranges, and an empty list takes no request at all. A nonce is used once: a file made exclusively under
 * var/<site>/migration/trigger/nonces, so every PHP process and Velocity worker shares the count; the files of
 * expired signatures are removed as new ones come.
 *
 * The request never carries a command line: the destination builds it from the action (exp:migration:status,
 * exp:migration:migrate, exp:migration:rollback, exp:migration:heal) with the checked run id, migration ids and fixes,
 * and runs it with its own PHP CLI ([TriggerSettings] Php) in its root, waiting for it (Timeout).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationTrigger
{
    /** The request header */
    const HEADER = 'X-Exp-Migration-Trigger';

    /** The header as PHP and Velocity put it into $_SERVER */
    const SERVER_KEY = 'HTTP_X_EXP_MIGRATION_TRIGGER';

    const VERSION = 'v1';

    /** The schema of the trigger's own answers (refusals); a run answers with its exp-migration/1 report */
    const SCHEMA = 'exp-migration-trigger/1';

    /** Seconds a signature may be issued in the future (clocks of two hosts) */
    const SKEW = 30;

    /** The largest body taken, in bytes */
    const MAX_BODY = 65536;

    /** The actions and the console script each runs */
    const ACTIONS = array( 'status' => 'bin/php/expmigrationstatus.php', 'dry-run' => 'bin/php/expmigrationmigrate.php',
                           'apply' => 'bin/php/expmigrationmigrate.php', 'rollback' => 'bin/php/expmigrationrollback.php',
                           'heal' => 'bin/php/expmigrationheal.php' );

    /** @var array the settings (settings()) */
    protected $settings;

    /** @var array root, nonce_dir, lookup (callable name => value, tests), now */
    protected $options;

    /**
     * @param array $settings as settings() returns them
     * @param array $options root (the installation, default eZSys::rootDir()), nonce_dir (default
     *        var/<site>/migration/trigger/nonces), lookup (callable name => value|null instead of expSecrets::get())
     */
    public function __construct( array $settings, array $options = array() )
    {
        $this->settings = $settings + self::defaults();
        $this->options = $options + array( 'root' => null, 'nonce_dir' => null, 'lookup' => null );
        if ( $this->options['root'] === null )
            $this->options['root'] = class_exists( 'eZSys' ) ? (string)eZSys::rootDir() : (string)getcwd();
        if ( $this->options['nonce_dir'] === null )
            $this->options['nonce_dir'] = rtrim( (string)eZSys::varDirectory(), '/' ) . '/migration/trigger/nonces';
    }

    /** @return array the defaults of [TriggerSettings] */
    public static function defaults()
    {
        return array( 'enabled' => false, 'secret' => '', 'addresses' => array(), 'lifetime' => 300, 'per_minute' => 30,
                      'php' => 'php', 'timeout' => 900, 'allow_root_user' => false );
    }

    /**
     * migration.ini [TriggerSettings] with the defaults of a missing setting.
     *
     * @param eZINI|null $ini
     * @return array enabled, secret (a name), addresses, lifetime, per_minute, php, timeout, allow_root_user
     */
    public static function settings( $ini = null )
    {
        $ini = $ini ?: eZINI::instance( 'migration.ini' );
        $get = function ( $name, $default ) use ( $ini ) {
            return $ini->hasVariable( 'TriggerSettings', $name ) && $ini->variable( 'TriggerSettings', $name ) !== '' ? $ini->variable( 'TriggerSettings', $name ) : $default;
        };
        $addresses = array();
        foreach ( (array)$get( 'AllowedAddresses', array() ) as $address )
            if ( ( $address = trim( (string)$address ) ) !== '' )
                $addresses[] = $address;
        return array(
            'enabled' => $get( 'Trigger', 'disabled' ) === 'enabled',
            'secret' => trim( (string)$get( 'Secret', '' ) ),
            'addresses' => $addresses,
            'lifetime' => max( 30, min( 3600, (int)$get( 'Lifetime', 300 ) ) ),
            'per_minute' => max( 1, (int)$get( 'MaxRequestsPerMinute', 30 ) ),
            'php' => trim( (string)$get( 'Php', 'php' ) ),
            'timeout' => max( 10, (int)$get( 'Timeout', 900 ) ),
            'allow_root_user' => $get( 'AllowRootUser', 'disabled' ) === 'enabled',
        );
    }

    // ------------------------------------------------------------------------------------------------ signature

    /**
     * A signature of one request.
     *
     * @param string $key the secret
     * @param string $body the request's body
     * @param int|null $issued unix time, default now
     * @param string|null $nonce 32 hex digits, default random
     * @return string
     */
    public static function sign( $key, $body, $issued = null, $nonce = null )
    {
        $issued = $issued === null ? time() : (int)$issued;
        $nonce = $nonce === null ? bin2hex( random_bytes( 16 ) ) : strtolower( (string)$nonce );
        return self::VERSION . '.' . $issued . '.' . $nonce . '.' . self::mac( $issued, $nonce, (string)$body, (string)$key );
    }

    /**
     * @param string $token
     * @return array|null issued, nonce, mac
     */
    public static function parse( $token )
    {
        if ( !is_string( $token ) || strlen( $token ) > 200
             || !preg_match( '/^' . self::VERSION . '\.([0-9]{1,12})\.([0-9a-f]{32})\.([A-Za-z0-9_-]{43})$/', trim( $token ), $m ) )
            return null;
        return array( 'issued' => (int)$m[1], 'nonce' => $m[2], 'mac' => $m[3] );
    }

    protected static function mac( $issued, $nonce, $body, $key )
    {
        $mac = hash_hmac( 'sha256', 'migration-trigger/1|' . (int)$issued . '|' . $nonce . '|' . hash( 'sha256', $body ), $key, true );
        return rtrim( strtr( base64_encode( $mac ), '+/', '-_' ), '=' );
    }

    /**
     * Why a signature is not valid for a body; '' when it is. Pure (the nonce is claimed by claim()).
     *
     * @param string $token
     * @param string $body
     * @param string $key
     * @param int $lifetime seconds
     * @param int|null $now
     * @return string
     */
    public static function problem( $token, $body, $key, $lifetime, $now = null )
    {
        $parts = self::parse( $token );
        if ( $parts === null )
            return 'no valid signature';
        $now = $now === null ? time() : (int)$now;
        if ( $parts['issued'] > $now + self::SKEW )
            return 'the signature was issued in the future';
        if ( $parts['issued'] < $now - (int)$lifetime )
            return 'the signature has expired';
        if ( !hash_equals( self::mac( $parts['issued'], $parts['nonce'], (string)$body, (string)$key ), $parts['mac'] ) )
            return 'the signature does not match';
        return '';
    }

    /**
     * Whether an address is in a list of addresses and CIDR ranges (IPv4 and IPv6; an IPv4-mapped IPv6 address counts
     * as its IPv4 address). Pure.
     *
     * @param string $address
     * @param string[] $allowed
     * @return bool
     */
    public static function addressAllowed( $address, array $allowed )
    {
        $address = strtolower( trim( (string)$address ) );
        if ( strpos( $address, '::ffff:' ) === 0 && filter_var( substr( $address, 7 ), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) )
            $address = substr( $address, 7 );
        $packed = filter_var( $address, FILTER_VALIDATE_IP ) ? inet_pton( $address ) : false;
        if ( $packed === false )
            return false;
        foreach ( $allowed as $entry )
        {
            $entry = strtolower( trim( (string)$entry ) );
            $bits = null;
            if ( strpos( $entry, '/' ) !== false )
            {
                list( $entry, $bits ) = explode( '/', $entry, 2 );
                if ( !ctype_digit( $bits ) )
                    continue;
                $bits = (int)$bits;
            }
            $net = filter_var( $entry, FILTER_VALIDATE_IP ) ? inet_pton( $entry ) : false;
            if ( $net === false || strlen( $net ) !== strlen( $packed ) )
                continue;
            $bits = $bits === null ? strlen( $net ) * 8 : $bits;
            if ( $bits < 0 || $bits > strlen( $net ) * 8 )
                continue;
            $bytes = intdiv( $bits, 8 );
            if ( substr( $net, 0, $bytes ) !== substr( $packed, 0, $bytes ) )
                continue;
            $rest = $bits % 8;
            if ( $rest === 0 )
                return true;
            $mask = ( 0xff << ( 8 - $rest ) ) & 0xff;
            if ( ( ord( $net[$bytes] ) & $mask ) === ( ord( $packed[$bytes] ) & $mask ) )
                return true;
        }
        return false;
    }

    /**
     * Counts a nonce as used; false when it was used before or cannot be counted. Also the rate limit: false with
     * $limited true when more than per_minute nonces were taken in the last minute.
     *
     * @param string $nonce
     * @param int $issued
     * @param int $now
     * @param bool $limited
     * @return bool
     */
    protected function claim( $nonce, $issued, $now, &$limited = false )
    {
        $limited = false;
        $dir = rtrim( (string)$this->options['nonce_dir'], '/' );
        if ( !is_dir( $dir ) && !@mkdir( $dir, eZDir::dirMode( 0770 ), true ) && !is_dir( $dir ) )
            return false;
        $recent = 0;
        $handle = @opendir( $dir );
        if ( $handle !== false )
        {
            $expired = $now - (int)$this->settings['lifetime'] - self::SKEW;
            while ( ( $name = readdir( $handle ) ) !== false )
            {
                if ( !preg_match( '/^([0-9]{1,12})-[0-9a-f]{32}$/', $name, $m ) )
                    continue;
                if ( (int)$m[1] < $expired )
                    @unlink( $dir . '/' . $name );
                else if ( (int)@filemtime( $dir . '/' . $name ) > $now - 60 )
                    $recent++;
            }
            closedir( $handle );
        }
        if ( $recent >= (int)$this->settings['per_minute'] )
        {
            $limited = true;
            return false;
        }
        // made exclusively: two requests with one nonce never both get it
        $file = $dir . '/' . (int)$issued . '-' . $nonce;
        $handle = @fopen( $file, 'x' );
        if ( $handle === false )
            return false;
        fclose( $handle );
        @touch( $file, $now );
        return true;
    }

    // ------------------------------------------------------------------------------------------------ requests

    /**
     * The body of a request from the arguments of a console script (expMigrationRemoteTransport::run()): the action
     * and what it may carry. Pure.
     *
     * @param string[] $args the script (relative to the installation) and its arguments
     * @return array|null null for a script the trigger does not run
     */
    public static function requestFromArgs( array $args )
    {
        $script = (string)array_shift( $args );
        $flags = array();
        $positional = array();
        foreach ( $args as $arg )
        {
            $arg = (string)$arg;
            if ( strpos( $arg, '--' ) === 0 )
            {
                $eq = strpos( $arg, '=' );
                $flags[$eq === false ? substr( $arg, 2 ) : substr( $arg, 2, $eq - 2 )] = $eq === false ? true : substr( $arg, $eq + 1 );
            }
            else
                $positional[] = $arg;
        }
        $only = isset( $flags['only'] ) && is_string( $flags['only'] ) ? array_values( array_filter( array_map( 'trim', explode( ',', $flags['only'] ) ), 'strlen' ) ) : array();
        switch ( $script )
        {
            case self::ACTIONS['status']:
                return array( 'action' => 'status', 'runs' => isset( $flags['runs'] ) ? (int)$flags['runs'] : 1 );
            case self::ACTIONS['apply']:
                if ( empty( $flags['apply'] ) )
                    return array( 'action' => 'dry-run', 'only' => $only );
                return array( 'action' => 'apply', 'run_id' => isset( $flags['run-id'] ) ? (string)$flags['run-id'] : '', 'only' => $only );
            case self::ACTIONS['rollback']:
                return array( 'action' => 'rollback', 'run_id' => isset( $positional[0] ) ? $positional[0] : '' );
            case self::ACTIONS['heal']:
                return array( 'action' => 'heal', 'fix' => isset( $flags['fix'] ) && is_string( $flags['fix'] ) ? explode( ',', $flags['fix'] ) : array(),
                              'stale_lock_age' => isset( $flags['stale-lock-age'] ) ? (int)$flags['stale-lock-age'] : 0 );
        }
        return null;
    }

    /**
     * The command line of a request on this installation; a string (why not) for a request it does not take. Pure.
     *
     * @param mixed $request the decoded body
     * @param string $php the PHP CLI
     * @param bool $allowRoot
     * @return string[]|string argv, or the reason
     */
    public static function commandOf( $request, $php, $allowRoot = false )
    {
        if ( !is_array( $request ) || !isset( $request['action'] ) || !is_string( $request['action'] ) || !isset( self::ACTIONS[$request['action']] ) )
            return 'the action must be one of ' . implode( ', ', array_keys( self::ACTIONS ) );
        $action = $request['action'];
        $args = array( $php, self::ACTIONS[$action] );
        $only = isset( $request['only'] ) ? $request['only'] : array();
        if ( !is_array( $only ) || count( $only ) > 500 )
            return 'only must be a list of migration ids';
        foreach ( $only as $id )
            if ( !is_string( $id ) || !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,190}$/', $id ) )
                return 'only must be a list of migration ids';
        $runId = isset( $request['run_id'] ) && is_string( $request['run_id'] ) ? $request['run_id'] : '';
        switch ( $action )
        {
            case 'status':
                $runs = isset( $request['runs'] ) && is_int( $request['runs'] ) ? max( 0, min( 50, $request['runs'] ) ) : 1;
                $args = array_merge( $args, array( '--format=json', '--runs=' . $runs ) );
                break;
            case 'dry-run':
            case 'apply':
                $args[] = '--format=json';
                if ( $action === 'apply' )
                {
                    if ( !expMigrationRecord::validRunId( $runId ) )
                        return 'apply needs a valid run_id';
                    $args[] = '--apply';
                    $args[] = '--run-id=' . $runId;
                }
                if ( $only )
                    $args[] = '--only=' . implode( ',', $only );
                break;
            case 'rollback':
                if ( !expMigrationRecord::validRunId( $runId ) )
                    return 'rollback needs a valid run_id';
                $args = array_merge( $args, array( $runId, '--apply', '--format=json' ) );
                break;
            case 'heal':
                $fix = isset( $request['fix'] ) && is_array( $request['fix'] ) ? $request['fix'] : array();
                $fixes = array();
                foreach ( $fix as $name )
                {
                    if ( !is_string( $name ) || !in_array( $name, expMigrationHeal::FIXES, true ) )
                        return 'fix must be a list of ' . implode( ', ', expMigrationHeal::FIXES );
                    $fixes[] = $name;
                }
                if ( !$fixes )
                    return 'heal needs at least one fix';
                $args = array_merge( $args, array( '--apply', '--format=json', '--fix=' . implode( ',', array_unique( $fixes ) ) ) );
                if ( isset( $request['stale_lock_age'] ) && is_int( $request['stale_lock_age'] ) && $request['stale_lock_age'] > 0 )
                    $args[] = '--stale-lock-age=' . $request['stale_lock_age'];
                break;
        }
        if ( $allowRoot )
            $args[] = '--allow-root-user';
        return $args;
    }

    /** @return string|null the secret's value */
    protected function secret()
    {
        $name = (string)$this->settings['secret'];
        if ( $name === '' )
            return null;
        if ( is_callable( $this->options['lookup'] ) )
            return call_user_func( $this->options['lookup'], $name );
        if ( strpos( $name, 'env:' ) === 0 )
        {
            $value = getenv( substr( $name, 4 ) );
            return $value === false ? null : (string)$value;
        }
        return class_exists( 'expSecrets' ) ? expSecrets::get( $name ) : null;
    }

    /** @return array a refusal: status and its body */
    protected static function refuse( $status, $error )
    {
        return array( 'status' => (int)$status, 'body' => array( 'schema' => self::SCHEMA, 'status' => (int)$status, 'error' => (string)$error ) );
    }

    /**
     * One request to the trigger.
     *
     * @param string $method
     * @param string $address REMOTE_ADDR
     * @param string|null $signature the header's value
     * @param string $body
     * @param int|null $now
     * @return array status (HTTP), body (array: the report of the run, or a refusal), action ('' when refused)
     */
    public function handle( $method, $address, $signature, $body, $now = null )
    {
        $now = $now === null ? time() : (int)$now;
        if ( strtoupper( (string)$method ) !== 'POST' )
            return self::refuse( 405, 'only POST' ) + array( 'action' => '' );
        if ( !$this->settings['enabled'] )
            return self::refuse( 404, 'the trigger is not enabled ([TriggerSettings] Trigger)' ) + array( 'action' => '' );
        if ( !$this->settings['addresses'] || !self::addressAllowed( $address, (array)$this->settings['addresses'] ) )
            return self::refuse( 403, 'this address may not use the trigger ([TriggerSettings] AllowedAddresses[])' ) + array( 'action' => '' );
        if ( strlen( (string)$body ) > self::MAX_BODY )
            return self::refuse( 413, 'the request is too big' ) + array( 'action' => '' );
        $key = $this->secret();
        if ( $key === null || strlen( $key ) < 16 )
            return self::refuse( 503, 'the secret of the trigger is not set or shorter than 16 characters ([TriggerSettings] Secret)' ) + array( 'action' => '' );
        $why = self::problem( $signature, (string)$body, $key, (int)$this->settings['lifetime'], $now );
        if ( $why !== '' )
            return self::refuse( 401, $why ) + array( 'action' => '' );
        $parts = self::parse( $signature );
        $limited = false;
        if ( !$this->claim( $parts['nonce'], $parts['issued'], $now, $limited ) )
            return $limited ? self::refuse( 429, 'too many requests; try again in a minute' ) + array( 'action' => '' )
                            : self::refuse( 409, 'the signature was used before' ) + array( 'action' => '' );
        $request = json_decode( (string)$body, true );
        $argv = self::commandOf( $request, (string)$this->settings['php'], (bool)$this->settings['allow_root_user'] );
        if ( is_string( $argv ) )
            return self::refuse( 400, $argv ) + array( 'action' => '' );
        $results = expMigrationRemoteProcess::runAll( array( 'trigger' => array( array( 'argv' => $argv, 'env' => array(), 'cwd' => $this->options['root'], 'stdin' => null ) ) ),
                                                      1, (int)$this->settings['timeout'] );
        $result = $results['trigger'];
        $report = expMigrationRemoteDeploy::parseReport( $result['stdout'] );
        if ( $report === null )
        {
            $lines = array_slice( array_filter( array_map( 'trim', preg_split( '/\r?\n/', (string)$result['stderr'] . "\n" . (string)$result['stdout'] ) ) ), -3 );
            return self::refuse( 502, 'the console script gave no report (exit ' . (int)$result['exit'] . '): ' . implode( ' | ', $lines ) ) + array( 'action' => $request['action'] );
        }
        return array( 'status' => 200, 'body' => $report, 'action' => $request['action'] );
    }

    // ------------------------------------------------------------------------------------------------ the source's side

    /**
     * The curl configuration of one signed request, given to curl on its standard input (curl -K -), so that neither
     * the signature nor the body appears on a command line. Pure apart from the signature's time and nonce.
     *
     * @param string $url
     * @param string $key
     * @param string $body
     * @param int|null $issued
     * @param string|null $nonce
     * @return string
     */
    public static function curlConfig( $url, $key, $body, $issued = null, $nonce = null )
    {
        $quote = function ( $value ) {
            return '"' . str_replace( array( '\\', '"', "\n", "\r", "\t" ), array( '\\\\', '\\"', '\\n', '\\r', '\\t' ), (string)$value ) . '"';
        };
        return 'url = ' . $quote( $url ) . "\n"
             . 'proto = "=https,http"' . "\n"
             . 'request = "POST"' . "\n"
             . 'header = ' . $quote( 'Content-Type: application/json' ) . "\n"
             . 'header = ' . $quote( self::HEADER . ': ' . self::sign( $key, $body, $issued, $nonce ) ) . "\n"
             . 'data-binary = ' . $quote( $body ) . "\n";
    }

    /**
     * Whether a Trigger setting is an address the source may send to: https://, or http:// to this host only. Pure.
     *
     * @param string $url
     * @return bool
     */
    public static function validUrl( $url )
    {
        $url = (string)$url;
        if ( preg_match( '/[\s"\\\\\x00-\x1f]/', $url ) || strlen( $url ) > 500 )
            return false;
        $parts = parse_url( $url );
        if ( !is_array( $parts ) || !isset( $parts['scheme'], $parts['host'] ) || isset( $parts['user'] ) || isset( $parts['pass'] ) )
            return false;
        if ( strtolower( $parts['scheme'] ) === 'https' )
            return true;
        $host = trim( strtolower( $parts['host'] ), '[]' );
        return strtolower( $parts['scheme'] ) === 'http' && ( $host === 'localhost' || $host === '::1' || preg_match( '/^127\.[0-9]+\.[0-9]+\.[0-9]+$/', $host ) === 1 );
    }
}
