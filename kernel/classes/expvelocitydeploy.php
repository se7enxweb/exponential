<?php
/**
 * File containing the expVelocityDeploy class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Everything a PHP code change needs before it is live, in the one order that
 * works: exp:velocity deploy.
 *
 * By hand this was six commands and a dozen cache clears, and the order is not
 * a detail. The caches that hold rendered output -- the content view cache,
 * the HTTP cache, template blocks, Velocity's response cache -- are cleared
 * only after PHP-FPM has been reloaded and Velocity restarted. Cleared before,
 * every page requested in between is rendered by the old code and cached
 * again, and the change looks as if it had not worked.
 *
 *   1  extension autoloads             bin/php/ezpgenerateautoloads.php -e
 *   2  kernel autoloads (--kernel)     bin/php/ezpgenerateautoloads.php -k, kernel/ and lib/ only
 *   3  INI caches                      bin/php/ezcache.php --clear-tag=ini
 *   4  template, template-override, translation and design_base caches,
 *      one id per call
 *   5  engine archive                  bin/php/phar.php build: rebuilt only when a file
 *                                      in kernel/, lib/ or autoload/ changed, and
 *                                      every file parsed first
 *   6  PHP-FPM                         wait until its OPcache has revalidated the files
 *                                      (no reload, see fpmRevalidateWait()), or
 *                                      systemctl reload <[DeploySettings] PhpFpmService>
 *   7  restart Velocity                bin/php/velocity.php restart
 *   8  content, exphttpcache, ezjscore-packer (--packer) and template-block
 *      caches, one id per call; the packer only ever right before
 *      template-block
 *   9  Velocity's response cache
 *  10  warm-up: [DeploySettings] WarmUpURL[] requested once each, in the
 *      background
 *
 * The first step that fails stops the run, and nothing after it is done: a
 * kernel file that does not parse stops it before PHP-FPM or Velocity is
 * touched, and a restart that did not come up leaves every cache as it was.
 *
 * @package kernel
 */
class expVelocityDeploy
{
    /** @var expVelocity */
    protected $velocity;

    /** @var array */
    protected $options;

    /** @var callable|null called with each finished step */
    protected $printer;

    /** @var string */
    protected $root;

    /**
     * @param expVelocity $velocity the engine to restart
     * @param array $options kernel, autoload, fpm, velocity (bool, default:
     *        kernel false, the others true), dry-run, rebuild-phar, packer,
     *        fpm-reload (bool: reload PHP-FPM even when its OPcache revalidates),
     *        engine (string, passed on to the restart)
     * @param callable|null $printer function( array $step, int $number, int $count )
     */
    public function __construct( expVelocity $velocity, array $options = array(), $printer = null )
    {
        $this->velocity = $velocity;
        $this->options = $options + array(
            'kernel' => false, 'autoload' => true, 'fpm' => true, 'velocity' => true,
            'dry-run' => false, 'rebuild-phar' => false, 'packer' => false, 'engine' => '',
            'fpm-reload' => false,
        );
        $this->printer = $printer;
        $this->root = rtrim( eZSys::rootDir(), '/' );
        if ( $this->root === '' )
            $this->root = getcwd();
    }

    /**
     * The steps of this run, in order, each with what it would do.
     *
     * @return array of array( id, label, command (for a person to read),
     *         skip (reason, or ''), run (callable returning array( ok, message )) )
     */
    public function steps()
    {
        $steps = array();
        $php = array( PHP_BINARY );
        $noAutoload = empty( $this->options['autoload'] ) ? 'skipped (--no-autoload)' : '';

        $steps[] = $this->commandStep( 'autoload-extensions', 'extension autoloads',
            array( 'bin/php/ezpgenerateautoloads.php', '-e', '-q' ), $noAutoload, 'var/autoload/ezp_extension.php' );

        if ( !empty( $this->options['kernel'] ) )
            $steps[] = $this->commandStep( 'autoload-kernel', 'kernel autoloads (kernel/ and lib/)',
                array( 'bin/php/ezpgenerateautoloads.php', '-k', '-q', '--exclude=' . implode( ',', $this->kernelExcludes() ) ),
                $noAutoload, 'autoload/ezp_kernel.php' );

        $steps[] = $this->commandStep( 'ini', 'INI caches',
            array( 'bin/php/ezcache.php', '--clear-tag=ini', '--allow-root-user' ) );
        // design_base: the list of design directories, which keeps the
        // extensions active when it was written; without it a newly activated
        // extension's templates are not found.
        foreach ( array( 'template', 'template-override', 'translation', 'design_base' ) as $id )
            $steps[] = $this->cacheStep( $id );

        $steps[] = $this->archiveStep();
        $steps[] = $this->fpmStep();
        $steps[] = $this->velocityStep();

        // --packer: the packed scripts and styles, always right before
        // template-block. Cached page heads name the packed files; with the
        // packer cleared alone they point to files that are gone, and the
        // admin loses its scripts and styles until template-block is cleared.
        $ids = array( 'content', 'exphttpcache' );
        if ( !empty( $this->options['packer'] ) )
            $ids[] = 'ezjscore-packer';
        $ids[] = 'template-block';
        foreach ( $ids as $id )
            $steps[] = $this->cacheStep( $id );

        $velocity = $this->velocity;
        $steps[] = array(
            'id' => 'velocity-cache', 'label' => 'Velocity response cache',
            'command' => 'exp:velocity cache clear', 'skip' => '',
            'run' => function () use ( $velocity ) {
                $r = $velocity->clearCache();
                return array( $r['ok'], $r['message'] );
            },
        );

        $steps[] = $this->warmUpStep();

        return $steps;
    }

    /**
     * The warm-up step: after every cache has been cleared, the pages of
     * [DeploySettings] WarmUpURL[] are requested once each, one after the
     * other, by a detached background process, so the first visitor does not
     * pay for compiling the templates and rebuilding the INI, override,
     * translation, design and packed-file caches -- and several first
     * visitors do not all rebuild the same caches at once. The deploy does
     * not wait for it. Anonymous requests only (a login page compiles the
     * admin's page layout, styles and scripts); a URL is requested as given.
     *
     * @return array
     */
    protected function warmUpStep()
    {
        $ini = eZINI::instance( 'velocity.ini' );
        $urls = array();
        if ( $ini->hasVariable( 'DeploySettings', 'WarmUpURL' ) )
            foreach ( (array)$ini->variable( 'DeploySettings', 'WarmUpURL' ) as $url )
                if ( preg_match( '@^https?://[^\s]+$@', trim( (string)$url ) ) )
                    $urls[] = trim( (string)$url );
        $step = array( 'id' => 'warm-up', 'label' => 'warm-up requests', 'skip' => '',
                       'command' => 'curl ' . ( $urls ? count( $urls ) . ' URLs in the background' : '(no [DeploySettings] WarmUpURL[])' ) );
        $curl = '';
        foreach ( array( '/usr/bin/curl', '/bin/curl', '/usr/local/bin/curl' ) as $candidate )
            if ( is_executable( $candidate ) )
            {
                $curl = $candidate;
                break;
            }
        $setsid = is_executable( '/usr/bin/setsid' ) ? '/usr/bin/setsid' : ( is_executable( '/bin/setsid' ) ? '/bin/setsid' : '' );
        if ( !$urls )
            $step['skip'] = 'no [DeploySettings] WarmUpURL[]';
        elseif ( $curl === '' || $setsid === '' )
            $step['skip'] = 'needs curl and setsid';
        $step['dry'] = function () use ( $urls ) { return implode( ' ', $urls ); };
        $self = $this;
        $step['run'] = function () use ( $self, $urls, $curl, $setsid ) {
            // setsid -f: the request process is not this one's child, so the deploy ends at once
            $command = array( $setsid, '-f', $curl, '-s', '-k', '--max-time', '180', '-A', 'Exponential deploy warm-up' );
            foreach ( $urls as $url )
            {
                $command[] = '-o';
                $command[] = '/dev/null';
                $command[] = $url;
            }
            // every descriptor on /dev/null: a pipe would keep this process waiting until curl is done
            $process = @proc_open( $command, array( 0 => array( 'file', '/dev/null', 'r' ), 1 => array( 'file', '/dev/null', 'w' ),
                                                    2 => array( 'file', '/dev/null', 'w' ) ), $pipes, $self->rootDir() );
            $code = is_resource( $process ) ? proc_close( $process ) : 127;
            if ( $code !== 0 )
                return array( false, 'could not start the warm-up (exit ' . $code . ')' );
            return array( true, count( $urls ) . ' URLs requested in the background' );
        };
        return $step;
    }

    /**
     * Run every step, or with dry-run describe them.
     *
     * @return array ok, seconds, steps (each: id, label, command, status
     *         PASS|FAIL|SKIP|DRY|NOT RUN, seconds, message)
     */
    public function run()
    {
        $start = microtime( true );
        $steps = $this->steps();
        $count = count( $steps );
        $report = array();
        $failed = false;

        foreach ( $steps as $i => $step )
        {
            $entry = array( 'id' => $step['id'], 'label' => $step['label'], 'command' => $step['command'],
                            'status' => '', 'seconds' => 0.0, 'message' => '' );
            if ( $failed )
            {
                $entry['status'] = 'NOT RUN';
                $entry['message'] = 'an earlier step failed';
            }
            elseif ( $step['skip'] !== '' )
            {
                $entry['status'] = 'SKIP';
                $entry['message'] = $step['skip'];
            }
            elseif ( !empty( $this->options['dry-run'] ) )
            {
                $entry['status'] = 'DRY';
                $entry['message'] = isset( $step['dry'] ) ? call_user_func( $step['dry'] ) : '';
            }
            else
            {
                $t = microtime( true );
                try
                {
                    list( $ok, $message ) = call_user_func( $step['run'] );
                }
                catch ( Exception $e )
                {
                    list( $ok, $message ) = array( false, get_class( $e ) . ': ' . $e->getMessage() );
                }
                $entry['seconds'] = round( microtime( true ) - $t, 1 );
                $entry['status'] = $ok === null ? 'SKIP' : ( $ok ? 'PASS' : 'FAIL' );
                $entry['message'] = (string)$message;
                $failed = $ok === false;
            }
            $report[] = $entry;
            if ( $this->printer )
                call_user_func( $this->printer, $entry, $i + 1, $count );
        }

        $result = array( 'ok' => !$failed, 'dry-run' => !empty( $this->options['dry-run'] ),
                         'seconds' => round( microtime( true ) - $start, 1 ), 'steps' => $report );

        // Audit (doc/bc/6.0/audit.md, system.velocity.deploy): a deploy that ran (not a dry run)
        if ( empty( $this->options['dry-run'] ) && class_exists( 'expAuditHook' ) )
            expAuditHook::emit( 'system.velocity.deploy', function () use ( $report, $failed, $start ) {
                $steps = array();
                foreach ( $report as $entry )
                    $steps[(string)$entry['id']] = (string)$entry['status'];
                return array( 'object' => array( 'type' => 'velocity', 'id' => 'deploy' ), 'verb' => 'deploy',
                              'result' => $failed ? 'failed' : 'success', 'reason' => $failed ? 'error' : null,
                              'after' => array( 'steps' => $steps, 'ms' => (int)round( ( microtime( true ) - $start ) * 1000 ) ) );
            } );
        return $result;
    }

    /**
     * A step that runs one of the installation's PHP scripts.
     *
     * @param string $id
     * @param string $label
     * @param array $args the script and its arguments, relative to the root
     * @param string $skip
     * @param string $writes a PHP file the step writes, checked to parse afterwards
     * @return array
     */
    protected function commandStep( $id, $label, array $args, $skip = '', $writes = '' )
    {
        $root = $this->root;
        $self = $this;
        return array(
            'id' => $id, 'label' => $label, 'skip' => $skip,
            'command' => 'php ' . implode( ' ', $args ),
            'run' => function () use ( $self, $args, $writes, $root ) {
                list( $code, $output ) = $self->runCommand( array_merge( array( PHP_BINARY ), $args ) );
                $tail = trim( implode( ' ', array_slice( $output, -3 ) ) );
                // The autoload generator exits 0 even when it failed, and
                // prints why; an exception's message is its only sign.
                if ( $code !== 0 || preg_match( '/(Fatal error|Exception|Parse error)/', implode( "\n", $output ) ) )
                    return array( false, 'exit ' . $code . ( $tail !== '' ? ': ' . $tail : '' ) );
                if ( $writes !== '' )
                {
                    list( $lint, $lintOut ) = $self->runCommand( array( PHP_BINARY, '-n', '-l', $root . '/' . $writes ) );
                    if ( $lint !== 0 )
                        return array( false, $writes . ' does not parse: ' . trim( implode( ' ', $lintOut ) ) );
                    return array( true, $writes . ' written' );
                }
                return array( true, $tail );
            },
        );
    }

    /**
     * A step that clears one cache by id. One id per call: several in one
     * --clear-id were not all cleared reliably.
     *
     * @param string $id
     * @return array
     */
    protected function cacheStep( $id )
    {
        $step = $this->commandStep( 'cache-' . $id, $id . ' cache',
            array( 'bin/php/ezcache.php', '--clear-id=' . $id, '--allow-root-user' ) );
        return $step;
    }

    /**
     * Top-level directories the kernel autoload generator is told to leave
     * alone: all but kernel/ and lib/, which is where every kernel class is.
     *
     * Without them -k walks the whole installation -- .claude/worktrees held
     * several complete copies of it, and node_modules, extension_src and doc
     * thousands of files more -- only for the result to be the same.
     * Each is anchored at the end too ("name(/|$)"), since the generator
     * matches them as a prefix: "k" alone would also exclude kernel.
     *
     * @return array
     */
    public function kernelExcludes()
    {
        $excludes = array();
        foreach ( (array)@scandir( $this->root ) as $entry )
        {
            if ( $entry === '.' || $entry === '..' || in_array( $entry, array( 'kernel', 'lib' ), true ) )
                continue;
            if ( !is_dir( $this->root . '/' . $entry ) || !preg_match( '/^[\w.\-]+$/', $entry ) )
                continue;
            $excludes[] = preg_quote( $entry, '@' ) . '(/|$)';
        }
        return $excludes;
    }

    /**
     * The PHP-FPM step.
     *
     * @return array
     */
    protected function fpmStep()
    {
        $step = array( 'id' => 'fpm', 'label' => 'reload PHP-FPM', 'command' => 'systemctl reload', 'skip' => '' );
        if ( empty( $this->options['fpm'] ) )
        {
            $step['skip'] = 'skipped (--no-fpm)';
            $step['run'] = null;
            return $step;
        }

        list( $units, $note ) = $this->fpmServices();
        $step['command'] = $units ? 'systemctl reload ' . implode( ' ', $units ) : 'systemctl reload (none found)';

        // A reload is not needed when the FPM's OPcache looks at the files itself: then the
        // step waits until it has, and every pool of the master keeps serving.
        $wait = $units ? $this->fpmRevalidateWait() : null;
        if ( $wait !== null )
        {
            list( $seconds, $why ) = $wait;
            $step['label'] = 'PHP-FPM sees the new code';
            $step['command'] = $seconds > 0 ? 'wait ' . $seconds . ' s (no reload)' : 'nothing to wait for (no reload)';
            $step['dry'] = function () use ( $why ) { return $why; };
            $step['run'] = function () use ( $seconds, $why ) {
                if ( $seconds > 0 )
                    sleep( $seconds );
                return array( true, 'not reloaded: ' . $why . ( $seconds > 0 ? '; waited ' . $seconds . ' s' : '' ) );
            };
            return $step;
        }

        if ( !$units )
            $step['skip'] = $note;
        elseif ( function_exists( 'posix_geteuid' ) && posix_geteuid() !== 0 )
            $step['skip'] = 'not root, so not reloaded: run systemctl reload ' . implode( ' ', $units ) . ' as root';
        elseif ( $this->findSystemctl() === '' )
            $step['skip'] = 'no systemctl on this system: reload ' . implode( ', ', $units ) . ' by hand';

        $self = $this;
        $step['dry'] = function () use ( $note ) { return $note; };
        $step['run'] = function () use ( $self, $units, $note ) {
            $systemctl = $self->findSystemctl();
            foreach ( $units as $unit )
            {
                list( $code, $out ) = $self->runCommand( array( $systemctl, 'reload', $unit ) );
                if ( $code !== 0 )
                    return array( false, 'systemctl reload ' . $unit . ' failed (exit ' . $code . '): ' . trim( implode( ' ', $out ) ) );
                list( , $state ) = $self->runCommand( array( $systemctl, 'is-active', $unit ) );
                if ( trim( implode( '', $state ) ) !== 'active' )
                    return array( false, $unit . ' is ' . trim( implode( '', $state ) ) . ' after the reload' );
            }
            return array( true, 'reloaded ' . implode( ', ', $units ) . ' (' . $note . ')' );
        };
        return $step;
    }

    /**
     * Whether the PHP-FPM step can do without a reload, and how long it then
     * waits instead: [DeploySettings] PhpFpmReload (auto, always, never), or
     * always with the option fpm-reload.
     *
     * A reload costs every site of the master, not only this one. SIGUSR2
     * asks each child of every pool to finish its request, and the master
     * starts no new child until the last has (process_control_timeout, 300 s
     * on Plesk): a request that comes in meanwhile waits for the longest one
     * running anywhere on that PHP version. Measured on alpha (#374): one
     * open 25 s event stream made the next page wait 22.4 s after a reload,
     * and a 135 s request on another pool turned waiting requests into 504s.
     *
     * With auto it is left out when the FPM's OPcache revalidates the files
     * (opcache.validate_timestamps on, revalidate_freq at most 60 s, as read
     * from the FPM binary and the pool files): every changed PHP file, the
     * autoload arrays among them, is then recompiled within revalidate_freq
     * seconds, and the step waits that long (plus one) before the caches of
     * rendered output are cleared. Without OPcache nothing is waited for.
     * What a reload is still needed for: a changed php.ini or pool file, a
     * new PHP extension, PHP files removed or renamed in place (realpath
     * cache) -- deploy --fpm-reload, or PhpFpmReload=always.
     *
     * @return array|null array( seconds to wait, why ), or null: reload
     */
    public function fpmRevalidateWait()
    {
        $ini = eZINI::instance( 'velocity.ini' );
        $mode = $ini->hasVariable( 'DeploySettings', 'PhpFpmReload' )
              ? strtolower( trim( (string)$ini->variable( 'DeploySettings', 'PhpFpmReload' ) ) ) : 'auto';
        if ( !empty( $this->options['fpm-reload'] ) || $mode === 'always' )
            return null;
        if ( $mode === 'never' )
            return array( 0, '[DeploySettings] PhpFpmReload=never' );

        $pools = $this->fpmPoolFiles();
        if ( !$pools )
            return null;
        $longest = 0;
        $notes = array();
        foreach ( $pools as $pool )
        {
            $opcache = $this->fpmOpcacheSettings( $pool );
            if ( $opcache === null )
                return null;    // not known: reload, as before
            if ( !$opcache['enable'] )
            {
                $notes[] = basename( $pool ) . ': no OPcache';
                continue;
            }
            if ( !$opcache['validate_timestamps'] || $opcache['revalidate_freq'] > 60 )
                return null;
            $longest = max( $longest, $opcache['revalidate_freq'] + 1 );
            $notes[] = basename( $pool ) . ': OPcache revalidates every ' . $opcache['revalidate_freq'] . ' s';
        }
        return array( $longest, implode( ', ', $notes ) . ' (a reload holds every pool of the master until its longest request ends; --fpm-reload forces one)' );
    }

    /**
     * The pool files the PHP-FPM step found for this installation (see fpmServices()).
     *
     * @return array
     */
    public function fpmPoolFiles()
    {
        list( $units, $note ) = $this->fpmServices();
        if ( !$units || strpos( $note, 'auto: pool ' ) !== 0 )
            return array();
        return array_values( array_filter( array_map( 'trim', explode( ', ', substr( $note, strlen( 'auto: pool ' ) ) ) ) ) );
    }

    /**
     * The OPcache settings a pool's children run with: the FPM binary's own
     * (php-fpm -i), then the pool file's php_value / php_admin_value lines.
     *
     * @param string $pool pool configuration file
     * @return array|null enable, validate_timestamps (bool), revalidate_freq (int), or null when unknown
     */
    public function fpmOpcacheSettings( $pool )
    {
        $binaries = array(
            '@^(/opt/plesk/php/\d+\.\d+)/etc/php-fpm\.d/@' => '$1/sbin/php-fpm',
            '@^/etc/php-fpm\.d/@'                          => '/usr/sbin/php-fpm',
            '@^/etc/php/(\d+\.\d+)/fpm/pool\.d/@'          => '/usr/sbin/php-fpm$1',
            '@^/etc/opt/remi/(php\d+)/php-fpm\.d/@'        => '/opt/remi/$1/root/usr/sbin/php-fpm',
        );
        $binary = '';
        foreach ( $binaries as $pattern => $path )
            if ( preg_match( $pattern, $pool, $m ) )
            {
                $binary = str_replace( '$1', $m[1] ?? '', $path );
                break;
            }
        if ( $binary === '' || !is_executable( $binary ) )
            return null;
        list( $code, $lines ) = $this->runCommand( array( $binary, '-i' ) );
        if ( $code !== 0 )
            return null;
        $values = array();
        foreach ( $lines as $line )
            if ( preg_match( '/^opcache\.(enable|validate_timestamps|revalidate_freq) => (\S+) => (\S+)/', $line, $m ) )
                $values[$m[1]] = $m[3];
        foreach ( preg_split( '/\r?\n/', (string)@file_get_contents( $pool ) ) as $line )
            if ( preg_match( '/^\s*php_(?:admin_)?(?:value|flag)\[opcache\.(enable|validate_timestamps|revalidate_freq)\]\s*=\s*"?([^"\s;]+)/', $line, $m ) )
                $values[$m[1]] = $m[2];
        if ( !isset( $values['enable'], $values['validate_timestamps'], $values['revalidate_freq'] ) )
            return null;
        $on = function ( $v ) { return in_array( strtolower( $v ), array( '1', 'on', 'true', 'yes' ), true ); };
        return array( 'enable' => $on( $values['enable'] ), 'validate_timestamps' => $on( $values['validate_timestamps'] ),
                      'revalidate_freq' => (int)$values['revalidate_freq'] );
    }

    /**
     * The PHP-FPM services that serve this installation, and how that was
     * decided: [DeploySettings] PhpFpmService, or with "auto" the pool
     * configuration that names this installation.
     *
     * @return array array( array of unit names, note )
     */
    public function fpmServices()
    {
        $ini = eZINI::instance( 'velocity.ini' );
        $setting = $ini->hasVariable( 'DeploySettings', 'PhpFpmService' )
                 ? trim( (string)$ini->variable( 'DeploySettings', 'PhpFpmService' ) ) : 'auto';

        if ( $setting === '' || in_array( strtolower( $setting ), array( 'disabled', 'none', 'false' ), true ) )
            return array( array(), '[DeploySettings] PhpFpmService=disabled: nothing to reload' );

        if ( strtolower( $setting ) !== 'auto' )
        {
            $units = array_values( array_filter( array_map( 'trim', explode( ',', $setting ) ) ) );
            return array( $units, '[DeploySettings] PhpFpmService=' . $setting );
        }

        $root = realpath( $this->root ) ?: $this->root;
        $domain = preg_match( '@^/var/www/vhosts/([^/]+)/@', $root . '/', $m ) ? $m[1] : '';
        $patterns = array(
            '@^/opt/plesk/php/(\d+)\.(\d+)/etc/php-fpm\.d/@' => 'plesk-php$1$2-fpm',
            '@^/etc/php-fpm\.d/@'                            => 'php-fpm',
            '@^/etc/php/(\d+\.\d+)/fpm/pool\.d/@'            => 'php$1-fpm',
            '@^/etc/opt/remi/php(\d+)/php-fpm\.d/@'          => 'php$1-php-fpm',
        );
        $pools = array_merge(
            (array)glob( '/opt/plesk/php/*/etc/php-fpm.d/*.conf' ),
            (array)glob( '/etc/php-fpm.d/*.conf' ),
            (array)glob( '/etc/php/*/fpm/pool.d/*.conf' ),
            (array)glob( '/etc/opt/remi/php*/php-fpm.d/*.conf' ) );

        $units = array();
        $found = array();
        foreach ( $pools as $pool )
        {
            if ( !is_string( $pool ) || !is_readable( $pool ) )
                continue;
            $byName = $domain !== '' && basename( $pool, '.conf' ) === $domain;
            if ( !$byName && !preg_match( '@' . preg_quote( $root, '@' ) . '(/|"|\'|\s|$)@m', (string)@file_get_contents( $pool ) ) )
                continue;
            foreach ( $patterns as $pattern => $unit )
            {
                if ( preg_match( $pattern, $pool, $parts ) )
                {
                    $name = str_replace( array( '$1', '$2' ), array( $parts[1] ?? '', $parts[2] ?? '' ), $unit );
                    $units[$name] = true;
                    $found[] = $pool;
                    break;
                }
            }
        }

        if ( !$units )
            return array( array(), 'no PHP-FPM pool found for this installation'
                . ( $domain !== '' ? ' (none named ' . $domain . '.conf, none naming ' . $root . ')' : ' (none naming ' . $root . ')' )
                . ': set [DeploySettings] PhpFpmService to the service, or to disabled' );

        return array( array_keys( $units ), 'auto: pool ' . implode( ', ', $found ) );
    }

    /**
     * @return string the systemctl binary, or ''
     */
    public function findSystemctl()
    {
        foreach ( array( '/usr/bin/systemctl', '/bin/systemctl', '/usr/sbin/systemctl' ) as $candidate )
            if ( is_executable( $candidate ) )
                return $candidate;
        return '';
    }

    /**
     * The engine archive step: brought up to date before any service is
     * touched, so a kernel file that does not parse stops the deploy while
     * PHP-FPM and Velocity still run the old code. The restart after it then
     * finds the archive current. Only when the Velocity restart will read one.
     *
     * @return array
     */
    protected function archiveStep()
    {
        $archive = $this->velocity->enginePhar();
        $step = array( 'id' => 'engine-archive', 'label' => 'engine archive',
                       'command' => 'php bin/php/phar.php build' . ( !empty( $this->options['rebuild-phar'] ) ? ' --force' : '' ),
                       'skip' => '' );
        if ( empty( $this->options['velocity'] ) )
            $step['skip'] = 'not needed (--no-velocity)';
        elseif ( $archive === '' || !class_exists( 'expPhar' ) )
            $step['skip'] = 'not used: Velocity reads the engine from the files on disk (EnginePhar=disabled)';
        elseif ( !is_file( $archive ) )
            $step['skip'] = 'no archive at ' . $archive . ' (exp:phar build builds the first one)';

        $options = $this->options;
        $step['dry'] = function () use ( $archive, $options ) {
            if ( !empty( $options['rebuild-phar'] ) )
                return 'would be rebuilt (--rebuild-phar)';
            $check = expPhar::check( $archive, false );
            return $check['current'] ? 'current, would not be rebuilt' : 'would be rebuilt: ' . $check['reason'];
        };

        $self = $this;
        $velocity = $this->velocity;
        $step['run'] = function () use ( $self, $velocity, $archive, $options ) {
            if ( !$velocity->isRunning() )
                return array( null, 'not needed: Velocity is not running' );
            $args = array( PHP_BINARY, '-d', 'phar.readonly=0', 'bin/php/phar.php', 'build', '--json',
                           '--allow-root-user', '--output=' . $archive );
            if ( !empty( $options['rebuild-phar'] ) )
                $args[] = '--force';
            list( $code, $output ) = $self->runCommand( $args );
            $result = null;
            foreach ( array_reverse( $output ) as $line )
                if ( $line !== '' && $line[0] === '{' && is_array( $result = json_decode( $line, true ) ) )
                    break;
            if ( !is_array( $result ) )
                return array( false, 'exit ' . $code . ': ' . trim( implode( ' ', array_slice( $output, -3 ) ) ) );
            if ( empty( $result['ok'] ) )
            {
                $bad = !empty( $result['data']['unparsable'] ) ? ': ' . implode( ', ', $result['data']['unparsable'] ) : '';
                return array( false, $result['message'] . $bad . ' -- nothing was reloaded or restarted' );
            }
            return array( true, empty( $result['data']['rebuilt'] ) ? 'engine.phar is current, not rebuilt'
                                : 'engine.phar rebuilt: ' . ( $result['data']['reason'] ?? '' ) );
        };
        return $step;
    }

    /**
     * The Velocity restart step.
     *
     * @return array
     */
    protected function velocityStep()
    {
        $step = array( 'id' => 'velocity', 'label' => 'restart Velocity',
                       'command' => 'php bin/php/velocity.php restart', 'skip' => '' );
        $args = array( PHP_BINARY, 'bin/php/velocity.php', 'restart', '--json', '--allow-root-user' );
        if ( $this->options['engine'] !== '' )
        {
            $args[] = '--engine=' . $this->options['engine'];
            $step['command'] .= ' --engine=' . $this->options['engine'];
        }
        // --rebuild-phar is the archive step's; the restart finds the result current.

        if ( empty( $this->options['velocity'] ) )
            $step['skip'] = 'skipped (--no-velocity)';

        $velocity = $this->velocity;
        $step['dry'] = function () use ( $velocity ) {
            return $velocity->isRunning() ? 'running: would be restarted' : 'not running: would be left stopped';
        };

        $self = $this;
        $step['run'] = function () use ( $self, $velocity, $args ) {
            // Not started when it was not running: a deploy brings the running
            // services up to date, it does not decide which ones should run.
            if ( !$velocity->isRunning() )
                return array( null, 'not running, so not restarted (exp:velocity start starts it)' );

            list( $code, $output ) = $self->runCommand( $args );
            $result = null;
            foreach ( array_reverse( $output ) as $line )
                if ( ( $line = trim( $line ) ) !== '' && $line[0] === '{' && is_array( $result = json_decode( $line, true ) ) )
                    break;
            if ( !is_array( $result ) )
                return array( false, 'exit ' . $code . ': ' . trim( implode( ' ', array_slice( $output, -3 ) ) ) );
            $message = isset( $result['message'] ) ? (string)$result['message'] : '';
            $root = rtrim( eZSys::rootDir(), '/' );
            if ( $root !== '' )
                $message = str_replace( $root . '/', '', $message );
            return array( $code === 0 && !empty( $result['ok'] ), $message );
        };
        return $step;
    }

    /** @return string the installation root, for the step closures */
    public function rootDir()
    {
        return $this->root;
    }

    /**
     * Run a command in the installation root, without a shell.
     *
     * Public for the step closures, which PHP binds to no object here.
     *
     * @param array $command the program and its arguments
     * @return array array( exit code, output lines, stdout and stderr together )
     */
    public function runCommand( array $command )
    {
        $process = @proc_open( $command, array( 0 => array( 'file', '/dev/null', 'r' ),
                                                1 => array( 'pipe', 'w' ),
                                                2 => array( 'redirect', 1 ) ),
                               $pipes, $this->root );
        if ( !is_resource( $process ) )
            return array( 127, array( 'could not run ' . $command[0] ) );
        $output = stream_get_contents( $pipes[1] );
        fclose( $pipes[1] );
        $code = proc_close( $process );
        // Without colours, and without what every script says when run as
        // root, so the last lines are the ones worth showing.
        $output = preg_replace( '/\e\[[0-9;]*m/', '', (string)$output );
        $lines = array();
        foreach ( preg_split( '/\r?\n/', rtrim( $output ) ) as $line )
        {
            if ( trim( $line ) === '' || strpos( $line, 'With great power comes great responsibility' ) !== false
                 || strpos( $line, 'seconds to break the script' ) !== false )
                continue;
            $lines[] = rtrim( $line );
        }
        return array( $code, $lines );
    }
}
