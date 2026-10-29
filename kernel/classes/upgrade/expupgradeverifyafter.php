<?php
/**
 * File containing the expUpgradeVerifyAfter class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The check after an upgrade (exp:upgrade:verify --after, run at the end of exp:upgrade --apply and of a rollback):
 *
 *   steps     every step detected again: no drift (recorded as done, effect missing), none failed
 *   urls      each smoke address answers 200 to 399: upgrade.ini [UpgradeSettings] SmokeURL[], else velocity.ini
 *             [DeploySettings] WarmUpURL[] (the addresses of Apache and of Velocity), else the front page and the
 *             sign-in page of [SiteSettings] SiteURL; --url overrides
 *   tests     the test suite upgrade-smoke of phpunit.xml (no database), when PHPUnit is installed
 *   errorlog  no new line of a fatal error in var/log/error.log and the site's log since the run started
 *   autoload  every registered step class loads, the kernel and extension autoload arrays are readable
 *
 * A check that cannot run says why and does not fail the verification; one that fails does (exit 1).
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeVerifyAfter
{
    /**
     * Runs the checks.
     *
     * @param array $options db, url (string[]), no-tests (bool), since (unix time; default an hour ago), stages
     * @return array ok (bool), checks => array( name => array( ok (bool|null), detail, items ) )
     */
    public static function run( array $options = array() )
    {
        $since = isset( $options['since'] ) && $options['since'] ? (int)$options['since'] : time() - 3600;
        $checks = array();
        $checks['steps'] = self::steps( $options );
        $checks['urls'] = self::urls( isset( $options['url'] ) ? expUpgrade::listOption( $options['url'] ) : array() );
        $checks['tests'] = !empty( $options['no-tests'] ) ? array( 'ok' => null, 'detail' => 'skipped (--no-tests)', 'items' => array() ) : self::tests();
        $checks['errorlog'] = self::errorLog( $since );
        $checks['autoload'] = self::autoload();
        $ok = !in_array( false, array_column( $checks, 'ok' ), true );
        expUpgrade::audit( 'system.upgrade.verify', array( 'object' => array( 'type' => 'upgrade', 'id' => 'after' ), 'verb' => 'verify',
                                                            'result' => $ok ? 'success' : 'failure',
                                                            'x' => array( 'failed' => array_keys( array_filter( $checks, function ( $c ) { return $c['ok'] === false; } ) ) ) ) );
        return array( 'ok' => $ok, 'since' => date( 'c', $since ), 'checks' => $checks );
    }

    /** Step detection: drift and failed steps fail. */
    public static function steps( array $options )
    {
        $stages = isset( $options['stages'] ) ? $options['stages'] : array( 'database', 'scripts' );
        $report = expUpgrade::verify( $stages, array_intersect_key( $options, array( 'db' => 1, 'registry' => 1 ) ) );
        $bad = $report['drift'];
        $pending = array();
        foreach ( $report['stages'] as $s )
            foreach ( $s['steps'] as $step )
            {
                if ( $step['state'] === 'failed' )
                    $bad[] = $step['id'] . ' failed';
                if ( $step['state'] === 'pending' )
                    $pending[] = $step['id'];
            }
        if ( $report['exit'] === expUpgrade::EXIT_UNREACHABLE || $report['exit'] === expUpgrade::EXIT_INTERNAL )
            return array( 'ok' => false, 'detail' => implode( '; ', $report['errors'] ), 'items' => array() );
        return array( 'ok' => !$bad, 'items' => array_merge( $bad, $pending ? array( 'pending: ' . implode( ', ', $pending ) ) : array() ),
                      'detail' => $bad ? count( $bad ) . ' step(s) drifted or failed' : 'no drift' . ( $pending ? ', ' . count( $pending ) . ' pending' : '' ) );
    }

    /**
     * The smoke addresses: the given ones, else SmokeURL[], else velocity.ini WarmUpURL[], else SiteURL's front page and
     * sign-in page.
     *
     * @param string[] $given
     * @return string[]
     */
    public static function smokeUrls( array $given = array() )
    {
        if ( $given )
            return $given;
        $upgrade = eZINI::instance( 'upgrade.ini' );
        if ( $upgrade->hasVariable( 'UpgradeSettings', 'SmokeURL' ) && array_filter( (array)$upgrade->variable( 'UpgradeSettings', 'SmokeURL' ) ) )
            return array_values( array_filter( (array)$upgrade->variable( 'UpgradeSettings', 'SmokeURL' ) ) );
        if ( eZINI::exists( 'velocity.ini' ) )
        {
            $velocity = eZINI::instance( 'velocity.ini' );
            if ( $velocity->hasVariable( 'DeploySettings', 'WarmUpURL' ) && array_filter( (array)$velocity->variable( 'DeploySettings', 'WarmUpURL' ) ) )
                return array_values( array_filter( (array)$velocity->variable( 'DeploySettings', 'WarmUpURL' ) ) );
        }
        $site = trim( (string)eZINI::instance()->variable( 'SiteSettings', 'SiteURL' ) );
        if ( $site === '' )
            return array();
        $base = ( preg_match( '#^https?://#', $site ) ? '' : 'https://' ) . rtrim( $site, '/' );
        return array( "$base/", "$base/user/login" );
    }

    /** Each smoke address answers 200 to 399. */
    public static function urls( array $given = array() )
    {
        $urls = self::smokeUrls( $given );
        if ( !$urls )
            return array( 'ok' => null, 'detail' => 'no address: set [UpgradeSettings] SmokeURL[] or SiteURL, or give --url', 'items' => array() );
        $items = array();
        $bad = 0;
        foreach ( $urls as $url )
        {
            $code = expUpgradeStepFinishSmoke::status( $url );
            $items[] = "$url: " . ( $code ?: 'no answer' );
            if ( $code < 200 || $code > 399 )
                $bad++;
        }
        return array( 'ok' => !$bad, 'detail' => $bad ? "$bad of " . count( $urls ) . ' address(es) did not answer as they should' : count( $urls ) . ' address(es) answered', 'items' => $items );
    }

    /** The test suite upgrade-smoke, in its own process. */
    public static function tests()
    {
        $root = rtrim( eZSys::rootDir(), '/' );
        $phpunit = "$root/vendor/bin/phpunit";
        if ( !is_file( $phpunit ) || !is_file( "$root/phpunit.xml" ) )
            return array( 'ok' => null, 'detail' => 'PHPUnit is not installed (composer install with the dev packages): the tests did not run', 'items' => array() );
        $process = proc_open( array( PHP_BINARY, $phpunit, '--testsuite', 'upgrade-smoke', '--no-progress' ), array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes, $root );
        if ( !is_resource( $process ) )
            return array( 'ok' => null, 'detail' => 'cannot start PHPUnit', 'items' => array() );
        $out = (string)stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $code = proc_close( $process );
        $summary = '';
        foreach ( preg_split( '/\r?\n/', $out ) as $line )
            if ( preg_match( '/^(OK \(|OK, but|Tests: |FAILURES!|ERRORS!)/', trim( $line ) ) )
                $summary .= ( $summary !== '' ? ' ' : '' ) . trim( $line );
        return array( 'ok' => $code === 0, 'detail' => $summary !== '' ? $summary : "PHPUnit ended with exit $code", 'items' => array() );
    }

    /**
     * New fatal lines in the error logs since a moment.
     *
     * @param int $since
     * @param string[]|null $files the logs (var/log/error.log and the site's)
     * @return array
     */
    public static function errorLog( $since, $files = null )
    {
        if ( $files === null )
        {
            $root = rtrim( eZSys::rootDir(), '/' );
            $files = array_unique( array( "$root/var/log/error.log", $root . '/' . trim( eZSys::varDirectory(), '/' ) . '/log/error.log' ) );
        }
        $items = array();
        $read = 0;
        foreach ( $files as $file )
        {
            if ( !is_readable( $file ) )
                continue;
            $read++;
            foreach ( self::fatalLines( $file, $since ) as $line )
                $items[] = basename( dirname( dirname( $file ) ) ) . '/' . basename( $file ) . ': ' . mb_substr( $line, 0, 300 );
        }
        if ( !$read )
            return array( 'ok' => null, 'detail' => 'no error log to read', 'items' => array() );
        return array( 'ok' => !$items, 'detail' => $items ? count( $items ) . ' fatal line(s) since ' . date( 'Y-m-d H:i', $since ) : 'no fatal line since ' . date( 'Y-m-d H:i', $since ),
                      'items' => array_slice( $items, 0, 20 ) );
    }

    /**
     * The fatal lines of an eZLog file written at or after $since ("[ Oct 28 2026 23:45:33 ] ..."). Reads the last
     * 2 MB only. Pure apart from reading the file.
     *
     * @param string $file
     * @param int $since
     * @return string[]
     */
    public static function fatalLines( $file, $since )
    {
        $size = filesize( $file );
        $handle = fopen( $file, 'r' );
        if ( !$handle )
            return array();
        if ( $size > 2097152 )
            fseek( $handle, $size - 2097152 );
        $text = (string)stream_get_contents( $handle );
        fclose( $handle );
        $out = array();
        $when = 0;
        foreach ( preg_split( '/\r?\n/', $text ) as $line )
        {
            if ( preg_match( '/^\[ ([A-Z][a-z]{2} +\d{1,2} \d{4} \d{2}:\d{2}:\d{2}) \]/', $line, $m ) )
                $when = (int)strtotime( $m[1] );
            if ( $when >= $since && preg_match( '/Fatal error|PHP Fatal|Unexpected error|\bCRITICAL\b|Uncaught /', $line ) )
                $out[] = trim( $line );
        }
        return $out;
    }

    /** Every registered step class loads; the autoload arrays are there. */
    public static function autoload()
    {
        $registry = expUpgradeRegistry::fromSettings();
        $problems = $registry->errors();
        $root = rtrim( eZSys::rootDir(), '/' );
        if ( !is_readable( "$root/autoload/ezp_kernel.php" ) )
            $problems[] = 'autoload/ezp_kernel.php is not readable';
        if ( eZExtension::activeExtensions() && !is_readable( "$root/var/autoload/ezp_extension.php" ) )
            $problems[] = 'var/autoload/ezp_extension.php is missing: php bin/php/ezpgenerateautoloads.php -e';
        return array( 'ok' => !$problems, 'detail' => $problems ? implode( '; ', $problems ) : count( $registry->steps() ) . ' step class(es) load, autoload arrays readable', 'items' => $problems );
    }
}
