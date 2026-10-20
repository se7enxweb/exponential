<?php
/**
 * File containing the expSysCardPhpProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * PHP: version and SAPI, how long the branch has security support (syscard.ini [Php] SecuritySupportEnds[]),
 * OPcache and its JIT, the extensions that matter to Exponential and memory_limit.
 *
 * These are the facts of the PHP that runs the command. The web server's PHP (PHP-FPM, Velocity) can have its own
 * php.ini: Setup > System information shows that one.
 */
class expSysCardPhpProvider extends expSysCardBaseProvider
{
    protected $id = 'php';
    protected $section = 'php';
    protected $priority = 80;
    protected $budget = 0.5;

    /** The extensions shown when they are there; a missing one of the first group is a hint */
    const RECOMMENDED = array( 'intl', 'mbstring', 'curl', 'gd', 'xsl', 'sodium', 'apcu' );
    const DATABASE = array( 'mysqli', 'pdo_mysql', 'pgsql', 'pdo_pgsql', 'sqlite3', 'pdo_sqlite', 'mongodb', 'oci8' );
    const OTHER = array( 'imagick', 'redis', 'pcntl', 'posix', 'zip' );

    public function collect( expSysCardContext $context )
    {
        $facts = array();
        $version = (string)$context->get( 'php.version', function () { return PHP_VERSION; }, '' );
        $sapi = (string)$context->get( 'php.sapi', function () { return PHP_SAPI; }, '' );
        $severity = expSysCardFact::INFO;
        $hint = '';
        $branch = implode( '.', array_slice( explode( '.', $version ), 0, 2 ) );
        $ends = $context->ini( 'syscard.ini', 'Php', 'SecuritySupportEnds', array() );
        $warnDays = (int)$context->ini( 'syscard.ini', 'Php', 'WarnDays', 180 );
        $support = '';
        if ( is_array( $ends ) && isset( $ends[$branch] ) && ( $end = strtotime( $ends[$branch] . ' 23:59:59 UTC' ) ) )
        {
            $days = (int)floor( ( $end - $context->now ) / 86400 );
            if ( $days < 0 )
            {
                $severity = expSysCardFact::ERROR;
                $support = 'security support ended ' . $ends[$branch];
                $hint = sprintf( 'PHP %s has had no security fixes since %s: upgrade to a supported branch (doc/eng-us/help/os-support.md).', $branch, $ends[$branch] );
            }
            elseif ( $days <= $warnDays )
            {
                $severity = expSysCardFact::WARN;
                $support = 'security support ends ' . $ends[$branch];
                $hint = sprintf( 'PHP %s gets security fixes only until %s (%s): plan the upgrade to a newer branch.', $branch, $ends[$branch], self::counted( $days, 'day' ) );
            }
            else
            {
                $severity = expSysCardFact::OK;
                $support = 'supported until ' . $ends[$branch];
            }
        }
        $facts[] = $this->fact( 'version', 'PHP', trim( $version . ( $sapi !== '' ? ' (' . $sapi . ')' : '' ) . ( $support !== '' && $context->mode === expSysCardContext::MODE_INFO ? ', ' . $support : '' ) ), $severity, $hint );

        $extensions = array_map( 'strtolower', (array)$context->get( 'php.extensions', function () { return get_loaded_extensions(); }, array() ) );
        $has = function ( $name ) use ( $extensions ) { return in_array( strtolower( $name ), $extensions, true ); };
        $ini = function ( $name ) use ( $context ) { return $context->get( 'php.ini:' . $name, function () use ( $name ) { $v = ini_get( $name ); return $v === false ? null : $v; } ); };

        // OPcache
        $opcacheLoaded = $has( 'zend opcache' ) || $has( 'opcache' );
        $enabled = $opcacheLoaded && expSysCardContext::isOn( $ini( 'opcache.enable' ) );
        $cli = $opcacheLoaded && expSysCardContext::isOn( $ini( 'opcache.enable_cli' ) );
        if ( !$opcacheLoaded )
            $facts[] = $this->fact( 'opcache', 'OPcache', 'not loaded', expSysCardFact::WARN, 'OPcache is not loaded: every request compiles the PHP code again. Install and enable the opcache extension.' );
        elseif ( !$enabled )
            $facts[] = $this->fact( 'opcache', 'OPcache', 'off', expSysCardFact::WARN, 'OPcache is off (opcache.enable=0): every request compiles the PHP code again. Turn it on in php.ini.' );
        else
            $facts[] = $this->fact( 'opcache', 'OPcache', 'on' . ( $cli ? ', also for the CLI' : '' ) . ( $ini( 'opcache.memory_consumption' ) ? ', ' . (int)$ini( 'opcache.memory_consumption' ) . ' MiB' : '' ), expSysCardFact::OK );

        // JIT
        if ( $opcacheLoaded && version_compare( $version !== '' ? $version : '0', '8.0.0', '>=' ) )
        {
            $jit = (string)$ini( 'opcache.jit' );
            $buffer = self::bytes( (string)$ini( 'opcache.jit_buffer_size' ) );
            $off = !$enabled || $buffer <= 0 || in_array( strtolower( $jit ), array( '', '0', 'off', 'disable', 'disabled' ), true );
            $facts[] = $this->fact( 'jit', 'JIT', $off ? 'off' : $jit . ', ' . self::size( $buffer ) );
        }

        // Extensions
        $present = array();
        $missing = array();
        foreach ( self::RECOMMENDED as $name )
        {
            if ( $has( $name ) || ( $name === 'gd' && $has( 'imagick' ) ) )
                $present[] = $name;
            else
                $missing[] = $name;
        }
        foreach ( array_merge( self::DATABASE, self::OTHER ) as $name )
            if ( $has( $name ) )
                $present[] = $name;
        $facts[] = $this->fact( 'extensions', 'Extensions', empty( $present ) ? 'n/a' : implode( ' ', $present ),
                                empty( $missing ) ? expSysCardFact::INFO : expSysCardFact::WARN,
                                empty( $missing ) ? '' : 'Recommended PHP extensions are missing: ' . implode( ', ', $missing ) . '. Install them for full functionality (translations, image handling, the caches).' );
        if ( $context->mode === expSysCardContext::MODE_INFO )
            $facts[] = $this->fact( 'loaded', 'Loaded', self::counted( count( $extensions ), 'extension' ) );

        // memory_limit
        $limit = (string)$ini( 'memory_limit' );
        if ( $limit !== '' )
        {
            $bytes = self::bytes( $limit );
            $low = $bytes > 0 && $bytes < 256 * 1048576;
            $facts[] = $this->fact( 'memory_limit', 'memory_limit', $limit === '-1' ? 'unlimited' : $limit, $low ? expSysCardFact::WARN : expSysCardFact::INFO,
                                    $low ? 'memory_limit is ' . $limit . ': imports, image conversions and reindexing need at least 256M.' : '' );
        }
        return $facts;
    }
}
