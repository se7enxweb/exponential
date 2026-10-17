<?php
/**
 * File containing the expRequestClassifier class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The request classifier: tells a probe, a traversal, an injection, a scanner or a burst of not-found answers from
 * an ordinary request, and writes one line for it to var/log/security.log. Classification only: nothing is blocked
 * (a listener may do that later, see expRequestVerdictListenerInterface).
 *
 * Settings: settings/requestclassifier.ini. The rule sets are classes (expRequestRuleSetInterface), run in the order
 * of RuleSets[]; the first verdict wins. An extension adds one with RuleSets[]=<id> and [RuleSet-<id>] Class=<class>,
 * and extends a built-in set with patterns in its [RuleSet-<id>] group (expRequestPatternRuleSet).
 *
 * Cheap by design: no database; each pattern set is one combined regular expression per subject, compiled once per
 * settings version and kept in a static, in APCu when there is one, and in a PHP file in the cache directory
 * (Velocity resets class statics between requests; the file, in OPcache, does not need compiling again). Without
 * PCRE's JIT the patterns are matched one by one, which is faster there (see
 * expRequestPatternRuleSet::combinedIsFaster()). The not-found counter is only touched by a 404 or 410.
 *
 * Used by expRequestLog (kernel/classes/requestlog): classify() once per request, log() for a verdict.
 *
 * @package kernel
 */
class expRequestClassifier
{
    /** Changes whenever compiled cache files of an older version must not be used */
    const COMPILE_VERSION = 2;

    /** The built-in rule sets: identifier => class */
    const BUILT_IN = array(
        'probes' => 'expRequestRuleSetProbes',
        'traversal' => 'expRequestRuleSetTraversal',
        'injection' => 'expRequestRuleSetInjection',
        'scanner' => 'expRequestRuleSetScanner',
        'threshold' => 'expRequestRuleSetThreshold',
    );

    /** @var expRequestClassifier|null */
    protected static $instance = null;
    /** @var array Compiled pattern sets by settings hash */
    protected static $compiledCache = array();
    /** @var array Warnings already given in this process: message => true */
    protected static $warned = array();

    /** @var array */
    protected $config;
    /** @var expRequestRuleSetInterface[]|null */
    protected $ruleSets = null;
    /** @var expRequestVerdictListenerInterface[]|null */
    protected $listeners = null;
    /** @var string|null */
    protected $salt = null;

    /**
     * @param array|null $config null reads settings/requestclassifier.ini (see configFromINI()); an array (tests)
     *        holds the keys of defaults(), missing ones get the default
     */
    public function __construct( ?array $config = null )
    {
        $this->config = self::normalize( $config === null ? self::configFromINI() : $config );
    }

    /**
     * The classifier of the current request, from the INI settings.
     *
     * @return expRequestClassifier
     */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Forgets the instance (and, with $all, the compiled patterns and the warnings given): for tests and after a
     * settings change.
     *
     * @param bool $all
     * @return void
     */
    public static function reset( $all = false )
    {
        self::$instance = null;
        if ( $all )
        {
            self::$compiledCache = array();
            self::$warned = array();
        }
    }

    /**
     * The settings and their defaults.
     *
     * @return array
     */
    public static function defaults()
    {
        return array(
            'enabled' => true,
            'logFile' => 'security.log',
            'ruleSets' => array_keys( self::BUILT_IN ),
            // [RuleSet-<id>] groups: id => settings (Class, Patterns, Disabled, Excludes, Confidence ...)
            'sets' => array(),
            'listeners' => array(),
            'threshold' => array( 'max' => 30, 'window' => 60, 'statuses' => array( 404, 410 ) ),
            'maskIP' => true,
            'maxURILength' => 300,
            'compileCache' => true,
            // null: <cache directory>/requestclassifier, log directory of eZDebug, salt file in the cache directory
            'cacheDir' => null,
            'logDir' => null,
            'salt' => null,
        );
    }

    /**
     * The settings from settings/requestclassifier.ini and site.ini [RequestLogSettings]; the defaults where a
     * setting or the file is missing.
     *
     * @return array
     */
    public static function configFromINI()
    {
        $config = array();
        try
        {
            $ini = eZINI::instance( 'requestclassifier.ini' );
            $get = function ( $group, $name ) use ( $ini ) {
                return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : null;
            };
            if ( ( $v = $get( 'ClassifierSettings', 'Classification' ) ) !== null )
                $config['enabled'] = $v === 'enabled';
            if ( ( $v = $get( 'ClassifierSettings', 'LogFile' ) ) !== null )
                $config['logFile'] = $v;
            if ( ( $v = $get( 'ClassifierSettings', 'RuleSets' ) ) !== null )
                $config['ruleSets'] = $v;
            if ( ( $v = $get( 'ClassifierSettings', 'Listeners' ) ) !== null )
                $config['listeners'] = $v;
            if ( ( $v = $get( 'ClassifierSettings', 'CompileCache' ) ) !== null )
                $config['compileCache'] = $v !== 'disabled';
            $threshold = array();
            if ( ( $v = $get( 'ThresholdSettings', 'Max404PerMinute' ) ) !== null )
                $threshold['max'] = $v;
            if ( ( $v = $get( 'ThresholdSettings', 'WindowSeconds' ) ) !== null )
                $threshold['window'] = $v;
            if ( ( $v = $get( 'ThresholdSettings', 'Statuses' ) ) !== null )
                $threshold['statuses'] = $v;
            $config['threshold'] = $threshold;
            $sets = array();
            foreach ( isset( $config['ruleSets'] ) ? (array)$config['ruleSets'] : array_keys( self::BUILT_IN ) as $id )
            {
                if ( is_string( $id ) && $id !== '' && $ini->hasGroup( 'RuleSet-' . $id ) )
                    $sets[$id] = $ini->group( 'RuleSet-' . $id );
            }
            $config['sets'] = $sets;
        }
        catch ( Throwable $e )
        {
            self::warnOnce( 'settings/requestclassifier.ini could not be read: ' . $e->getMessage() );
        }
        try
        {
            $site = eZINI::instance();
            if ( $site->hasVariable( 'RequestLogSettings', 'MaskIP' ) )
                $config['maskIP'] = $site->variable( 'RequestLogSettings', 'MaskIP' ) !== 'disabled';
            if ( $site->hasVariable( 'RequestLogSettings', 'MaxURILength' ) )
                $config['maxURILength'] = $site->variable( 'RequestLogSettings', 'MaxURILength' );
        }
        catch ( Throwable $ignored )
        {
        }
        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    protected static function normalize( array $config )
    {
        $d = self::defaults();
        $c = $config + $d;
        $c['enabled'] = (bool)$c['enabled'];
        $c['logFile'] = basename( trim( (string)$c['logFile'] ) );
        if ( $c['logFile'] === '' || $c['logFile'] === '.' || $c['logFile'] === '..' )
            $c['logFile'] = $d['logFile'];
        $ids = array();
        foreach ( (array)$c['ruleSets'] as $id )
        {
            $id = trim( (string)$id );
            if ( $id !== '' && !in_array( $id, $ids, true ) )
                $ids[] = $id;
        }
        $c['ruleSets'] = $ids;
        $c['sets'] = is_array( $c['sets'] ) ? $c['sets'] : array();
        $c['listeners'] = array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$c['listeners'] ) ), 'strlen' ) );
        $t = ( is_array( $c['threshold'] ) ? $c['threshold'] : array() ) + $d['threshold'];
        $statuses = array();
        foreach ( (array)$t['statuses'] as $s )
        {
            if ( (int)$s >= 100 && (int)$s <= 599 )
                $statuses[] = (int)$s;
        }
        $c['threshold'] = array(
            'max' => max( 0, (int)$t['max'] ),
            'window' => max( 1, (int)$t['window'] ),
            'statuses' => $statuses ? array_values( array_unique( $statuses ) ) : $d['threshold']['statuses'],
        );
        $c['maskIP'] = (bool)$c['maskIP'];
        $c['maxURILength'] = max( 40, (int)$c['maxURILength'] );
        $c['compileCache'] = (bool)$c['compileCache'];
        return $c;
    }

    /**
     * @return bool [ClassifierSettings] Classification=enabled
     */
    public function enabled()
    {
        return $this->config['enabled'];
    }

    /**
     * @return array the settings in use (normalized)
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @return array max, window, statuses
     */
    public function thresholdSettings()
    {
        return $this->config['threshold'];
    }

    /**
     * The classifier's directory in the cache directory (compiled patterns, the not-found counter, the salt).
     *
     * @return string
     */
    public function cacheDirectory()
    {
        if ( $this->config['cacheDir'] !== null && $this->config['cacheDir'] !== '' )
            return rtrim( (string)$this->config['cacheDir'], '/' );
        return rtrim( eZSys::cacheDirectory(), '/' ) . '/requestclassifier';
    }

    /**
     * The rule sets of RuleSets[], created on first use. A set whose class does not exist or does not implement
     * expRequestRuleSetInterface is skipped with one warning.
     *
     * @return expRequestRuleSetInterface[] identifier => set
     */
    public function ruleSets()
    {
        if ( $this->ruleSets !== null )
            return $this->ruleSets;
        $sets = array();
        $patternDefinitions = array();
        foreach ( $this->config['ruleSets'] as $id )
        {
            $settings = isset( $this->config['sets'][$id] ) && is_array( $this->config['sets'][$id] ) ? $this->config['sets'][$id] : array();
            $class = isset( $settings['Class'] ) && trim( (string)$settings['Class'] ) !== ''
                ? trim( (string)$settings['Class'] ) : ( isset( self::BUILT_IN[$id] ) ? self::BUILT_IN[$id] : '' );
            if ( $class === '' )
            {
                self::warnOnce( "Request classifier: the rule set '$id' has no Class ([RuleSet-$id] in requestclassifier.ini), skipped" );
                continue;
            }
            if ( !class_exists( $class ) )
            {
                self::warnOnce( "Request classifier: the class '$class' of the rule set '$id' does not exist, skipped" );
                continue;
            }
            if ( !is_a( $class, 'expRequestRuleSetInterface', true ) )
            {
                self::warnOnce( "Request classifier: the class '$class' of the rule set '$id' does not implement expRequestRuleSetInterface, skipped" );
                continue;
            }
            try
            {
                $set = new $class( $id, $settings, $this );
            }
            catch ( Throwable $e )
            {
                self::warnOnce( "Request classifier: the rule set '$id' ($class) could not be created: " . $e->getMessage() );
                continue;
            }
            $sets[$id] = $set;
            if ( $set instanceof expRequestPatternRuleSet )
                $patternDefinitions[$id] = array( get_class( $set ), $settings, self::classVersion( $set ) );
        }
        if ( $patternDefinitions )
        {
            foreach ( $this->compiledSets( $patternDefinitions, $sets ) as $id => $compiled )
            {
                $sets[$id]->setCompiled( $compiled );
                foreach ( $compiled['invalid'] as $rule => $reason )
                    self::warnOnce( "Request classifier: the pattern '$rule' of the rule set '$id' was left out ($reason)" );
            }
        }
        return $this->ruleSets = $sets;
    }

    /**
     * The fingerprint of the built-in patterns (builtInFingerprint()), part of the compiled cache's version.
     * Whoever changes a built-in pattern updates it: expRequestClassifierTest fails until it is, and the new
     * value makes every installation compile again.
     */
    const BUILT_IN_FINGERPRINT = 'd808b31661ba950e';

    /**
     * The fingerprint of the definitions of the built-in pattern sets without settings.
     *
     * @return string
     */
    public static function builtInFingerprint()
    {
        $definitions = array();
        foreach ( self::BUILT_IN as $id => $class )
        {
            if ( is_a( $class, 'expRequestPatternRuleSet', true ) )
            {
                $set = new $class( $id );
                $definitions[$id] = $set->definition();
            }
        }
        return substr( sha1( serialize( $definitions ) ), 0, 16 );
    }

    /**
     * What the built-in patterns of a set depend on: BUILT_IN_FINGERPRINT for a built-in class, else the
     * modification times of its class file and those of its parents, so a changed class compiles again without a
     * cache clear.
     *
     * @param object $set
     * @return string
     */
    protected static function classVersion( $set )
    {
        $class = get_class( $set );
        // the built-in classes: their fingerprint, no file access (a test keeps it equal to builtInFingerprint())
        if ( in_array( $class, self::BUILT_IN, true ) )
            return self::BUILT_IN_FINGERPRINT;
        $version = '';
        for ( $class = new ReflectionClass( $set ); $class; $class = $class->getParentClass() )
        {
            $file = $class->getFileName();
            $version .= $class->getName() . ':' . ( $file ? (int)@filemtime( $file ) : 0 ) . ';';
        }
        return $version;
    }

    /**
     * The compiled expressions of the pattern sets: from the static cache, else from the cache file of this settings
     * version, else compiled now (and the file written). The version is a hash of the classes (and their file
     * times) and the settings, so a request that finds the file builds no definition at all.
     *
     * @param array $keys id => array( class, settings, class version )
     * @param expRequestPatternRuleSet[] $sets id => set
     * @return array id => compiled
     */
    protected function compiledSets( array $keys, array $sets )
    {
        $hash = sha1( self::COMPILE_VERSION . "\n" . PHP_VERSION_ID . "\n" . serialize( $keys ) );
        if ( isset( self::$compiledCache[$hash] ) )
            return self::$compiledCache[$hash];

        // APCu first where there is one (Velocity's workers and PHP-FPM each have their own; the value is a pure
        // function of the hash, so neither can see a wrong one), then the file
        $apcu = $this->config['compileCache'] && function_exists( 'apcu_enabled' ) && apcu_enabled();
        if ( $apcu )
        {
            $data = apcu_fetch( 'expRequestClassifier:' . $hash, $found );
            if ( $found && is_array( $data ) && !array_diff_key( $keys, $data ) )
                return self::$compiledCache[$hash] = $data;
        }
        $file = $this->config['compileCache'] ? $this->cacheDirectory() . '/compiled-' . $hash . '.php' : null;
        if ( $file !== null && is_file( $file ) )
        {
            $data = @include $file;
            if ( is_array( $data ) && isset( $data['hash'], $data['sets'] ) && $data['hash'] === $hash
                 && !array_diff_key( $keys, $data['sets'] ) )
            {
                if ( $apcu )
                    apcu_store( 'expRequestClassifier:' . $hash, $data['sets'], 86400 );
                return self::$compiledCache[$hash] = $data['sets'];
            }
        }

        $compiled = array();
        foreach ( $keys as $id => $key )
            $compiled[$id] = expRequestPatternRuleSet::compileDefinition( $sets[$id]->definition() );
        if ( $file !== null )
            $this->writeCompiledFile( $file, array( 'hash' => $hash, 'sets' => $compiled ) );
        if ( $apcu )
            apcu_store( 'expRequestClassifier:' . $hash, $compiled, 86400 );
        return self::$compiledCache[$hash] = $compiled;
    }

    /**
     * Writes a compiled cache file atomically (a temporary file renamed over it), with the log file permissions.
     *
     * @param string $file
     * @param array $data
     * @return bool
     */
    protected function writeCompiledFile( $file, array $data )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
        {
            if ( class_exists( 'eZDir' ) )
                @eZDir::mkdir( $dir, false, true );
            else
                @mkdir( $dir, 0777, true );
        }
        $tmp = $dir . '/.compiled-' . getmypid() . '-' . mt_rand() . '.tmp';
        $php = "<?php\n// Compiled request classifier patterns, written by expRequestClassifier. Safe to delete.\nreturn "
             . var_export( $data, true ) . ";\n";
        if ( @file_put_contents( $tmp, $php ) === false )
            return false;
        self::chmodLike( $tmp );
        if ( !@rename( $tmp, $file ) )
        {
            @unlink( $tmp );
            return false;
        }
        if ( function_exists( 'opcache_invalidate' ) )
            @opcache_invalidate( $file, true );
        return true;
    }

    /**
     * The verdict for a request: the first one of the rule sets in RuleSets[] order, null for an ordinary request
     * (and always null with Classification=disabled). A rule set that throws is skipped.
     *
     * @param expRequestContext $context
     * @return expRequestVerdict|null
     */
    public function classify( expRequestContext $context )
    {
        if ( !$this->config['enabled'] )
            return null;
        foreach ( $this->ruleSets() as $id => $set )
        {
            try
            {
                $verdict = $set->evaluate( $context );
            }
            catch ( Throwable $e )
            {
                self::warnOnce( "Request classifier: the rule set '$id' failed: " . $e->getMessage() );
                continue;
            }
            if ( $verdict instanceof expRequestVerdict )
            {
                if ( $verdict->ruleSet === '' )
                    $verdict->ruleSet = (string)$id;
                return $verdict;
            }
        }
        return null;
    }

    /**
     * The salted hash a client is counted by; the address itself is never stored.
     *
     * @param string $ip
     * @return string 40 hex characters
     */
    public function ipKey( $ip )
    {
        return sha1( (string)$ip . "\n" . $this->salt() );
    }

    /**
     * The salt of ipKey(): [ClassifierSettings] salt in a test, else a random value kept in the cache directory
     * (made on first use; a cache clear makes a new one, which only restarts the counting) plus the form secret of
     * site.ini.
     *
     * @return string
     */
    protected function salt()
    {
        if ( $this->salt !== null )
            return $this->salt;
        if ( $this->config['salt'] !== null )
            return $this->salt = (string)$this->config['salt'];
        $secret = '';
        try
        {
            $secret = (string)eZINI::instance()->variable( 'HTMLForms', 'Secret' );
        }
        catch ( Throwable $ignored )
        {
        }
        $file = $this->cacheDirectory() . '/salt.txt';
        $random = is_file( $file ) ? trim( (string)@file_get_contents( $file ) ) : '';
        if ( !preg_match( '/^[0-9a-f]{32,}$/', $random ) )
        {
            $random = bin2hex( random_bytes( 32 ) );
            $dir = dirname( $file );
            if ( !is_dir( $dir ) && class_exists( 'eZDir' ) )
                @eZDir::mkdir( $dir, false, true );
            // first writer wins; a second process reads what the first one wrote
            $fp = @fopen( $file, 'x' );
            if ( $fp )
            {
                fwrite( $fp, $random );
                fclose( $fp );
                self::chmodLike( $file );
            }
            else if ( is_file( $file ) )
            {
                $read = trim( (string)@file_get_contents( $file ) );
                if ( preg_match( '/^[0-9a-f]{32,}$/', $read ) )
                    $random = $read;
            }
        }
        return $this->salt = $random . "\n" . $secret;
    }

    /**
     * The security.log line of a verdict, without the date and address prefix:
     * <category>/<rule> confidence=<level> <status> <method> <uri> siteaccess=<sa> namespace=<path> ua=<hash> "<ua>"
     *
     * @param expRequestVerdict $verdict
     * @param expRequestContext $context
     * @param int $maxURILength
     * @return string
     */
    public static function securityLine( expRequestVerdict $verdict, expRequestContext $context, $maxURILength = 300 )
    {
        return $verdict->key()
            . ' confidence=' . $verdict->confidence
            . ' ' . ( $context->status ? (int)$context->status : '-' )
            . ' ' . $context->method
            . ' ' . self::uriForLog( $context->uri, $maxURILength )
            . ' siteaccess=' . ( $context->siteaccess !== '' ? self::token( $context->siteaccess, 64 ) : '-' )
            . ' namespace=' . self::uriForLog( $context->namespacePath, $maxURILength )
            . ' ' . self::userAgentForLog( $context->userAgent );
    }

    /**
     * The eZLog prefix: [ Oct 17 2026 09:00:00 ] [203.0.x.x] (followed by a space).
     *
     * @param string $ip
     * @param bool $mask
     * @param int|null $time
     * @return string
     */
    public static function prefix( $ip, $mask = true, $time = null )
    {
        $ip = $mask ? self::maskIP( $ip ) : preg_replace( '/[^0-9A-Fa-f:.]/', '', (string)$ip );
        return '[ ' . date( 'M d Y H:i:s', $time === null ? time() : (int)$time ) . ' ] [' . ( $ip !== '' ? $ip : '-' ) . '] ';
    }

    /**
     * An address masked as the log viewer masks it (expLogRedactor): IPv4 a.b.x.x, IPv6 the first two groups x:x.
     *
     * @param string $ip
     * @return string
     */
    public static function maskIP( $ip )
    {
        $ip = trim( (string)$ip );
        if ( $ip === '' )
            return '';
        if ( class_exists( 'expLogRedactor' ) )
        {
            $redactor = new expLogRedactor( false, true, false );
            $masked = $redactor->ip( $ip );
        }
        else
        {
            $masked = preg_replace( '/^(\d{1,3}\.\d{1,3})\.\d{1,3}\.\d{1,3}$/', '$1.x.x', $ip );
            if ( $masked === $ip && strpos( $ip, ':' ) !== false )
                $masked = preg_match( '/^([0-9a-f]{1,4}:[0-9a-f]{1,4}:)/i', $ip, $m ) ? $m[1] . 'x:x' : '::x';
        }
        // whatever was not recognized as an address is not written as it is
        if ( $masked === $ip && preg_match( '/\d/', $ip ) && !preg_match( '/x/', $masked ) )
            return 'x';
        return preg_replace( '/[^0-9A-Fa-f:.x]/', '', $masked );
    }

    /**
     * A URI (or path) for a log line: control characters taken out, spaces and quotes encoded, the query passed
     * through expLogRedactor (secrets, e-mail and IP addresses) and the whole capped at $max characters.
     *
     * @param string $uri
     * @param int $max
     * @return string
     */
    public static function uriForLog( $uri, $max = 300 )
    {
        $uri = (string)$uri;
        $q = strpos( $uri, '?' );
        $path = $q === false ? $uri : substr( $uri, 0, $q );
        $query = $q === false ? null : (string)substr( $uri, $q + 1 );
        if ( $query !== null && $query !== '' && class_exists( 'expLogRedactor' ) )
        {
            $redactor = new expLogRedactor( true, true, true );
            $query = $redactor->text( str_ireplace( '%40', '@', $query ) );
        }
        $uri = $path . ( $query !== null ? '?' . $query : '' );
        $uri = preg_replace( '/[\x00-\x1f\x7f]/', '', $uri );
        $uri = str_replace( array( ' ', '"' ), array( '%20', '%22' ), $uri );
        $max = max( 10, (int)$max );
        if ( strlen( $uri ) > $max )
            $uri = substr( $uri, 0, $max ) . '...';
        return $uri === '' ? '/' : $uri;
    }

    /**
     * ua=<first 8 hex of sha1(UA)> "<UA cut to 60 characters, quotes and control characters taken out>"
     *
     * @param string $userAgent
     * @return string
     */
    public static function userAgentForLog( $userAgent )
    {
        $userAgent = (string)$userAgent;
        if ( $userAgent === '' )
            return 'ua=- ""';
        $short = preg_replace( '/[\x00-\x1f\x7f"\\\\]/', '', $userAgent );
        if ( strlen( $short ) > 60 )
            $short = function_exists( 'mb_strcut' ) ? mb_strcut( $short, 0, 60, 'UTF-8' ) : substr( $short, 0, 60 );
        return 'ua=' . substr( sha1( $userAgent ), 0, 8 ) . ' "' . $short . '"';
    }

    /**
     * @param string $value
     * @param int $max
     * @return string a token without spaces
     */
    protected static function token( $value, $max )
    {
        return substr( preg_replace( '/[^A-Za-z0-9_.\-]/', '', (string)$value ), 0, $max );
    }

    /**
     * Writes the security.log line of a verdict (rotated like eZDebug's logs) and tells the listeners.
     *
     * @param expRequestVerdict $verdict
     * @param expRequestContext $context
     * @return bool whether the line was written
     */
    public static function log( expRequestVerdict $verdict, expRequestContext $context )
    {
        return self::instance()->write( $verdict, $context );
    }

    /**
     * log() for this instance.
     *
     * @param expRequestVerdict $verdict
     * @param expRequestContext $context
     * @return bool
     */
    public function write( expRequestVerdict $verdict, expRequestContext $context )
    {
        $line = self::prefix( $context->ip, $this->config['maskIP'] )
              . self::securityLine( $verdict, $context, $this->config['maxURILength'] );
        $written = self::appendLine( $this->logFile(), $line );
        foreach ( $this->listeners() as $listener )
        {
            try
            {
                $listener->onVerdict( $verdict, $context );
            }
            catch ( Throwable $e )
            {
                self::warnOnce( 'Request classifier: the listener ' . get_class( $listener ) . ' failed: ' . $e->getMessage() );
            }
        }
        return $written;
    }

    /**
     * The path of security.log: [ClassifierSettings] LogFile in eZDebug's log directory (var/log by default).
     *
     * @return string
     */
    public function logFile()
    {
        if ( $this->config['logDir'] !== null && $this->config['logDir'] !== '' )
            $dir = rtrim( (string)$this->config['logDir'], '/' );
        else
            $dir = class_exists( 'eZDebug' ) ? rtrim( eZDebug::instance()->logDirectory(), '/' ) : 'var/log';
        return $dir . '/' . $this->config['logFile'];
    }

    /**
     * Appends one line to a log file, rotating it first when it is over eZDebug::maxLogSize(); a new file gets the
     * log file permissions.
     *
     * @param string $file
     * @param string $line
     * @return bool
     */
    public static function appendLine( $file, $line )
    {
        $dir = dirname( $file );
        if ( !is_dir( $dir ) )
        {
            if ( class_exists( 'eZDir' ) )
                @eZDir::mkdir( $dir, false, true );
            else
                @mkdir( $dir, 0777, true );
        }
        clearstatcache( true, $file );
        $existed = is_file( $file );
        if ( $existed && class_exists( 'eZDebug' ) && filesize( $file ) > eZDebug::maxLogSize() )
        {
            if ( eZDebug::rotateLog( $file ) )
                $existed = false;
        }
        $written = @file_put_contents( $file, rtrim( (string)$line, "\r\n" ) . "\n", FILE_APPEND | LOCK_EX );
        if ( $written !== false && !$existed )
            self::chmodLike( $file );
        return $written !== false;
    }

    /**
     * The listeners of Listeners[]; a class that does not exist or does not implement
     * expRequestVerdictListenerInterface is skipped with one warning.
     *
     * @return expRequestVerdictListenerInterface[]
     */
    public function listeners()
    {
        if ( $this->listeners !== null )
            return $this->listeners;
        $listeners = array();
        foreach ( $this->config['listeners'] as $class )
        {
            if ( !class_exists( $class ) || !is_a( $class, 'expRequestVerdictListenerInterface', true ) )
            {
                self::warnOnce( "Request classifier: the listener '$class' does not exist or does not implement expRequestVerdictListenerInterface, skipped" );
                continue;
            }
            try
            {
                $listeners[] = new $class();
            }
            catch ( Throwable $e )
            {
                self::warnOnce( "Request classifier: the listener '$class' could not be created: " . $e->getMessage() );
            }
        }
        return $this->listeners = $listeners;
    }

    /**
     * Gives a new file the permissions of a log file (site.ini [FileSettings] LogFilePermissions, limited by the
     * installation's file mode limit), so the web server and a persistent worker running as another user can both
     * write it.
     *
     * @param string $file
     * @return void
     */
    public static function chmodLike( $file )
    {
        try
        {
            $permissions = class_exists( 'eZINI' ) ? octdec( eZINI::instance()->variable( 'FileSettings', 'LogFilePermissions' ) ) : 0666;
            if ( $permissions )
                @chmod( $file, class_exists( 'eZFile' ) ? eZFile::fileMode( $permissions ) : $permissions );
        }
        catch ( Throwable $ignored )
        {
        }
    }

    /**
     * Writes a warning once per process (eZDebug), and keeps it for warnings().
     *
     * @param string $message
     * @return void
     */
    protected static function warnOnce( $message )
    {
        if ( isset( self::$warned[$message] ) )
            return;
        self::$warned[$message] = true;
        if ( class_exists( 'eZDebug' ) )
            eZDebug::writeWarning( $message, __CLASS__ );
    }

    /**
     * The warnings given in this process (tests).
     *
     * @return string[]
     */
    public static function warnings()
    {
        return array_keys( self::$warned );
    }
}
