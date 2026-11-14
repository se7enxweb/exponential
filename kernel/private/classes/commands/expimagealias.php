<?php
/**
 * @description Image aliases made on demand: status, generate, regenerate, purge, resync and test, in the process or as jobs
 * @alias exp:image:alias
 *
 * File containing the exp:image:alias command (bin/php/expimagealias.php): the command line of Setup > Image aliases.
 * The work is done by the classes of kernel/classes/imagealias/ (expImageAliasAdmin for the dashboard, the scope of a
 * run and the test tools, expImageAliasJob for the runs and the jobs, expImageAliasThrottle for the pause), so a run
 * started here makes exactly what a job or the page makes.
 *
 * Started as root, the command becomes the owner of the installation's var/ directory first (as the content job
 * worker does), so every alias file, lock and log line it writes belongs to the user the web server writes as.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expimagealias extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    /** a run or a test had failures */
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;
    /** what was asked for is not there: the job queue, a staging source, the image */
    const EXIT_UNAVAILABLE = 3;
    /** a run in the process stopped because bulk runs are paused */
    const EXIT_PAUSED = 4;

    /** The machine output (--format=json). */
    const SCHEMA = 'exp-image-alias/1';

    const SUBCOMMANDS = array( 'status', 'generate', 'regenerate', 'purge', 'resync', 'test' );

    /** The options that narrow a run (the scope of Setup > Image aliases, Bulk runs) */
    const SCOPE_OPTIONS = array( 'node', 'subtree', 'class', 'attribute', 'alias', 'since', 'limit' );

    /** Which options each subcommand takes (format and the standard options go with every one) */
    const ALLOWED = array(
        'status' => array( 'job', 'pause', 'resume', 'cancel' ),
        'generate' => array( 'node', 'subtree', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'queue', 'wait', 'all' ),
        'regenerate' => array( 'node', 'subtree', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'queue', 'wait', 'all' ),
        'purge' => array( 'node', 'subtree', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'queue', 'wait', 'all' ),
        'resync' => array( 'node', 'subtree', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'overwrite', 'queue', 'all' ),
        'test' => array( 'node', 'object', 'alias', 'dry-run', 'source', 'path' ),
    );

    /** The checks of the dashboard (expImageAliasAdmin::buildDashboard()), as text */
    const CHECK_TEXTS = array(
        'render-mode' => 'render mode: templates make missing aliases while the page renders, as before',
        'no-webp' => 'format negotiation is on, but no converter here can write WebP',
        'source-no-url' => 'staging installation without a BaseURL for its source: missing images cannot be fetched',
        'source-paused' => 'the staging source is paused after failures in a row',
        'source-failing' => 'the staging source failed recently',
        'staging-no-source' => 'not production, but SyncFrom names no source: nothing is fetched',
        'no-queue' => 'the job queue (expqueue) is not available',
        'dead-jobs' => 'some image alias jobs failed for good (Setup > Job queue shows why)',
        'bulk-paused' => 'bulk runs are paused',
        'failures' => 'aliases failed in the last 24 hours (the fail lines of var/log/imagealias.log; Setup > Image aliases, Run history)',
        'key-old' => 'the signing key is more than a year old',
        'no-key' => 'no signing key yet: it is made with the first on-demand address',
    );

    /** @var string text or json */
    protected $format = 'text';

    /** @var float when the last progress line was written */
    protected $lastProgress = 0.0;

    public function run()
    {
        $root = dirname( __DIR__, 4 );
        $dropError = \expContentJobWorker::dropPrivileges( $root );

        $this->script( array( 'description' => self::helpText(),
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true,
                              'user' => true ) );
        $options = $this->startup( '[format:][node:][subtree:][object:][class:][attribute:][alias:][since:][limit:][dry-run][overwrite][queue][wait][all]' .
                                   '[job:][pause;][resume][cancel:][source;][path:]',
                                   '[subcommand]',
                                   array( 'format' => 'text (the default) or json (the machine output exp-image-alias/1)',
                                          'node' => 'the images of the object at this node',
                                          'subtree' => 'the images of every object below this node (the node included)',
                                          'object' => 'test: the images of this object',
                                          'class' => 'only objects of these classes (identifiers, separated by commas)',
                                          'attribute' => 'only these image attributes (identifiers, separated by commas)',
                                          'alias' => 'only these aliases (separated by commas; resync: original for the originals); test: make or check this alias',
                                          'since' => 'only objects changed since: a date (2026-11-01, "2026-11-01 14:30") or a time ago (2d, 12h, 30m, "1 week")',
                                          'limit' => 'at most this many images',
                                          'dry-run' => 'generate, regenerate, purge: count the images only; resync: say what would be fetched; test --alias: report only',
                                          'overwrite' => 'resync: replace files that differ from the source (the default fetches missing files only)',
                                          'queue' => 'queue the run as a job and print its id, instead of running it here',
                                          'wait' => 'a run here waits while bulk runs are paused, instead of stopping',
                                          'all' => 'regenerate, purge: allow a run over every image of the installation',
                                          'job' => 'status: one job of image aliases, with its progress',
                                          'pause' => 'status: pause bulk runs on every server (an optional reason: --pause="nightly import")',
                                          'resume' => 'status: let paused bulk runs go on',
                                          'cancel' => 'status: cancel this job (one that waits ends now, one that runs stops after its current image)',
                                          'source' => 'test: test the connection to a staging source (default the one SyncFrom names)',
                                          'path' => 'test --source: a storage path to ask the source for (default its base address)' ),
                                   false, array( 'user' => true ) );
        if ( $dropError !== null )
        {
            $this->error( 'FAIL ' . $dropError );
            $this->shutdown( self::EXIT_ERRORS );
        }
        \eZContentLanguage::setCronjobMode( true );

        $request = self::request( is_array( $options ) ? $options : array() );
        $this->format = $request['format'];
        if ( $request['errors'] )
            $this->usage( $request['errors'] );

        switch ( $request['command'] )
        {
            case 'status':
                $this->shutdown( $this->status( $request ) );
                break;
            case 'test':
                $this->shutdown( $this->test( $request ) );
                break;
            default:
                $this->shutdown( $this->runScope( $request ) );
        }
    }

    /**
     * @return string the description --help shows
     */
    public static function helpText()
    {
        return "Image aliases made on demand: the command line of Setup > Image aliases\n" .
               "\n" .
               "Usage: exp:image:alias <subcommand> [options]          (php bin/php/expimagealias.php ...)\n" .
               "\n" .
               "  status [--job=<id>] [--pause[=<reason>]] [--resume] [--cancel=<id>]\n" .
               "              mode, settings, converters, staging source, queue, pause, last 24 hours of the log, checks,\n" .
               "              the latest runs; --job shows one job; --pause, --resume pause or let go on the bulk runs of\n" .
               "              every server; --cancel cancels a job of image aliases (status is the default subcommand)\n" .
               "  generate    make the aliases that are missing\n" .
               "  regenerate  remove the aliases and make them again\n" .
               "  purge       remove the aliases (the originals stay); a page view makes them again on demand\n" .
               "  resync      fetch missing originals and aliases from the staging source (staging installations only)\n" .
               "  test --node=<id> | --object=<id> [--alias=<name> [--dry-run]] [--source[=<env>] [--path=<p>]]\n" .
               "              the images of one object: per alias the address a template gets, size, type, file there or\n" .
               "              made on demand, WebP/AVIF variants; --alias makes (or with --dry-run checks) that alias now;\n" .
               "              the connection to the staging source (SyncFrom, or --source=<env>)\n" .
               "\n" .
               "Scope of generate, regenerate, purge and resync (the same as the Bulk runs of the page):\n" .
               "  --node=<id> | --subtree=<id>, --class=a,b, --attribute=a,b, --alias=a,b, --since=<when>, --limit=<n>\n" .
               "  Without a scope a run covers every image; regenerate and purge then need --all.\n" .
               "  The aliases are those of the siteaccess (-s) and its RelatedSiteAccessList[], as for the jobs.\n" .
               "\n" .
               "Without --queue a run works here and shows its progress; it makes the aliases exactly as the jobs do\n" .
               "(the same lock per alias file, conversion slots, WebP/AVIF variants, stored data and log). With --queue it\n" .
               "is queued (expqueue, the bulk class) and the job id is printed; exp:image:alias status --job=<id> follows it.\n" .
               "A run here stops when bulk runs are paused (exit 4) unless --wait. Started as root, the command first\n" .
               "becomes the owner of var/, so the files it writes belong to the web server's user.\n" .
               "\n" .
               "  ./console exp:image:alias status --allow-root-user\n" .
               "  ./console exp:image:alias generate --subtree=93 --dry-run --allow-root-user\n" .
               "  ./console exp:image:alias generate --subtree=93 --allow-root-user\n" .
               "  ./console exp:image:alias regenerate --node=93 --alias=i160,i320 --queue --allow-root-user\n" .
               "  ./console exp:image:alias purge --class=image --since=2d --allow-root-user\n" .
               "  ./console exp:image:alias resync --subtree=2 --since=1w --overwrite --allow-root-user\n" .
               "  ./console exp:image:alias test --node=93 --allow-root-user\n" .
               "  ./console exp:image:alias status --pause=\"nightly import\" --allow-root-user\n" .
               "\n" .
               "The page: Setup > Image aliases (setup/aliases). Clearing every alias at once: exp:cache imagealias.\n" .
               "History: var/log/imagealias.log (run-start and run-end lines carry the run id printed here).\n" .
               "Exit codes: 0 done; 1 a run or test had failures; 2 wrong options; 3 not available (no job queue for\n" .
               "--queue, not a staging installation, no such image or job); 4 stopped: bulk runs are paused.";
    }

    // ------------------------------------------------------------------------------------------------ the request

    /**
     * The command line, checked without a database: the subcommand, the input of the scope
     * (expImageAliasAdmin::scopeFromInput()) and the other choices.
     *
     * @param array $options what getOptions() returned (arguments under 'arguments')
     * @return array command, format, errors (list of texts), input (for scopeFromInput()), scoped (a scope option was
     *               given), dry_run, overwrite, queue, wait, all, job, pause (false or the reason), resume, cancel,
     *               node, object, alias, source (false, true for SyncFrom, or an environment), path
     */
    public static function request( array $options )
    {
        $errors = array();
        $arguments = isset( $options['arguments'] ) && is_array( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $command = $arguments ? strtolower( trim( (string)$arguments[0] ) ) : 'status';
        if ( count( $arguments ) > 1 )
            $errors[] = 'one subcommand only, not: ' . implode( ' ', $arguments );
        if ( !in_array( $command, self::SUBCOMMANDS, true ) )
        {
            $errors[] = 'unknown subcommand "' . $command . '": one of ' . implode( ', ', self::SUBCOMMANDS );
            $command = 'status';
        }
        $given = function ( $name ) use ( $options ) {
            return isset( $options[$name] ) && $options[$name] !== null && $options[$name] !== false && $options[$name] !== '';
        };
        $text = function ( $name ) use ( $options ) {
            return isset( $options[$name] ) && is_string( $options[$name] ) ? trim( $options[$name] ) : '';
        };

        $format = $given( 'format' ) ? strtolower( $text( 'format' ) ) : 'text';
        if ( $format !== 'text' && $format !== 'json' )
        {
            $errors[] = '--format must be text or json';
            $format = 'text';
        }
        $all = array( 'node', 'subtree', 'object', 'class', 'attribute', 'alias', 'since', 'limit', 'dry-run', 'overwrite', 'queue', 'wait', 'all',
                      'job', 'pause', 'resume', 'cancel', 'source', 'path' );
        foreach ( $all as $name )
        {
            if ( $given( $name ) && !in_array( $name, self::ALLOWED[$command], true ) )
                $errors[] = "--$name does not go with $command" . ( $name === 'overwrite' && $command === 'generate' ? ' (regenerate makes the aliases again)' : '' );
        }
        foreach ( array( 'node', 'subtree', 'object', 'limit', 'job', 'cancel' ) as $name )
        {
            if ( $given( $name ) && in_array( $name, self::ALLOWED[$command], true ) && !self::positiveId( $options[$name] ) )
                $errors[] = "--$name must be a whole number from 1";
        }
        if ( $given( 'node' ) && $given( 'subtree' ) )
            $errors[] = '--node or --subtree, not both';
        if ( $command === 'status' && ( ( $given( 'pause' ) ? 1 : 0 ) + ( $given( 'resume' ) ? 1 : 0 ) + ( $given( 'cancel' ) ? 1 : 0 ) ) > 1 )
            $errors[] = 'one of --pause, --resume, --cancel at a time';

        $scoped = false;
        foreach ( self::SCOPE_OPTIONS as $name )
            $scoped = $scoped || $given( $name );
        $out = array( 'command' => $command, 'format' => $format, 'errors' => array(), 'input' => array(), 'scoped' => $scoped,
                      'dry_run' => $given( 'dry-run' ), 'overwrite' => $given( 'overwrite' ), 'queue' => $given( 'queue' ),
                      'wait' => $given( 'wait' ), 'all' => $given( 'all' ),
                      'job' => $given( 'job' ) ? (int)$options['job'] : 0,
                      'pause' => $given( 'pause' ) ? ( is_string( $options['pause'] ) ? trim( $options['pause'] ) : '' ) : false,
                      'resume' => $given( 'resume' ), 'cancel' => $given( 'cancel' ) ? (int)$options['cancel'] : 0,
                      'node' => $given( 'node' ) ? (int)$options['node'] : 0, 'object' => $given( 'object' ) ? (int)$options['object'] : 0,
                      'alias' => $text( 'alias' ),
                      'source' => $given( 'source' ) ? ( is_string( $options['source'] ) ? strtolower( trim( $options['source'] ) ) : true ) : false,
                      'path' => $text( 'path' ) );

        if ( in_array( $command, array( 'generate', 'regenerate', 'purge', 'resync' ), true ) )
        {
            if ( !$scoped && !$out['all'] && ( $command === 'regenerate' || $command === 'purge' ) )
                $errors[] = "$command without --node, --subtree, --class, --attribute, --alias, --since or --limit covers every image of the installation: add --all";
            if ( $out['queue'] && $out['dry_run'] && $command !== 'resync' )
                $errors[] = '--dry-run only counts the images; it queues nothing (leave out --queue)';
            $out['input'] = self::scopeInput( $command, $options );
        }
        if ( $command === 'test' )
        {
            if ( !$given( 'node' ) && !$given( 'object' ) && $out['source'] === false )
                $errors[] = 'test needs --node=<id>, --object=<id> or --source[=<environment>]';
            if ( $given( 'node' ) && $given( 'object' ) )
                $errors[] = '--node or --object, not both';
            if ( $out['alias'] !== '' && !\expImageAliasUrl::validAliasName( $out['alias'] ) )
                $errors[] = '--alias must be one alias name for test';
            if ( $out['alias'] !== '' && !$given( 'node' ) && !$given( 'object' ) )
                $errors[] = '--alias needs --node or --object';
            if ( $out['dry_run'] && $out['alias'] === '' )
                $errors[] = 'test changes nothing without --alias; --dry-run goes with --alias';
            if ( $out['path'] !== '' && $out['source'] === false )
                $errors[] = '--path goes with --source';
            if ( is_string( $out['source'] ) && !in_array( $out['source'], \expImageAliasStager::ENVIRONMENTS, true ) )
                $errors[] = '--source must be one of ' . implode( ', ', \expImageAliasStager::ENVIRONMENTS );
        }
        $out['errors'] = $errors;
        return $out;
    }

    /**
     * The input of expImageAliasAdmin::scopeFromInput() from the options of a run.
     *
     * @param string $command generate, regenerate, purge or resync
     * @param array $options
     * @return array action, target, node, class, attribute, alias (list), since, limit, dry_run, overwrite
     */
    public static function scopeInput( $command, array $options )
    {
        $value = function ( $name ) use ( $options ) {
            return isset( $options[$name] ) && is_string( $options[$name] ) ? trim( $options[$name] ) : '';
        };
        $input = array( 'action' => $command, 'target' => 'all', 'node' => '' );
        if ( $value( 'subtree' ) !== '' )
            $input = array( 'action' => $command, 'target' => 'subtree', 'node' => $value( 'subtree' ) );
        else if ( $value( 'node' ) !== '' )
            $input = array( 'action' => $command, 'target' => 'node', 'node' => $value( 'node' ) );
        $input['class'] = $value( 'class' );
        $input['attribute'] = $value( 'attribute' );
        $input['alias'] = preg_split( '/[\s,;]+/', $value( 'alias' ), -1, PREG_SPLIT_NO_EMPTY );
        $input['since'] = $value( 'since' );
        $input['limit'] = $value( 'limit' );
        $input['dry_run'] = !empty( $options['dry-run'] );
        $input['overwrite'] = !empty( $options['overwrite'] );
        return $input;
    }

    /**
     * @param mixed $value
     * @return bool a whole number from 1 (as text or number)
     */
    public static function positiveId( $value )
    {
        return ( is_int( $value ) && $value > 0 ) || ( is_string( $value ) && preg_match( '/^\d{1,10}$/', trim( $value ) ) && (int)$value > 0 );
    }

    /**
     * The scope errors of expImageAliasAdmin::scopeFromInput() as option texts.
     *
     * @param array $errors field => reason
     * @return string[]
     */
    public static function scopeErrors( array $errors )
    {
        $names = array( 'target' => 'node', 'node' => 'node', 'action' => 'subcommand', 'dry_run' => 'dry-run' );
        $out = array();
        foreach ( $errors as $field => $reason )
            $out[] = '--' . ( isset( $names[$field] ) ? $names[$field] : $field ) . ': ' . $reason;
        return $out;
    }

    // ------------------------------------------------------------------------------------------------ status

    /**
     * @param array $request
     * @return int exit code
     */
    protected function status( array $request )
    {
        $by = 'cli:' . self::osUser();
        if ( $request['pause'] !== false )
        {
            $was = \expImageAliasThrottle::bulkPause();
            if ( $was === null )
                \expImageAliasThrottle::pauseBulk( (string)$request['pause'], $by );
            return $this->done( $was === null ? 'PASS bulk runs paused on every server (exp:image:alias status --resume lets them go on)'
                                              : 'PASS bulk runs were paused already, since ' . date( 'Y-m-d H:i', (int)$was['since'] ) . ' by ' . $was['by'],
                                array( 'action' => 'pause', 'changed' => $was === null, 'pause' => \expImageAliasThrottle::bulkPause() ) );
        }
        if ( $request['resume'] )
        {
            $was = \expImageAliasThrottle::resumeBulk( $by );
            return $this->done( $was ? 'PASS bulk runs go on' : 'PASS bulk runs were not paused', array( 'action' => 'resume', 'changed' => $was ) );
        }
        if ( $request['cancel'] > 0 )
        {
            if ( !\expImageAliasAdmin::queueReadable() )
                return $this->fail( self::EXIT_UNAVAILABLE, 'the job queue (expqueue) is not available', array( 'action' => 'cancel' ) );
            $job = \expImageAliasAdmin::job( $request['cancel'] );
            if ( $job === null )
                return $this->fail( self::EXIT_UNAVAILABLE, 'no job of image aliases with the id ' . $request['cancel'], array( 'action' => 'cancel' ) );
            if ( $job['final'] )
                return $this->fail( self::EXIT_ERRORS, 'job ' . $job['id'] . ' has ended already (' . $job['state'] . ')', array( 'action' => 'cancel', 'job' => $job ) );
            $state = \expImageAliasAdmin::cancelJob( $request['cancel'] );
            if ( $state === null )
                return $this->fail( self::EXIT_ERRORS, 'job ' . $job['id'] . ' could not be cancelled', array( 'action' => 'cancel', 'job' => $job ) );
            return $this->done( 'PASS job ' . $job['id'] . ': ' . $state, array( 'action' => 'cancel', 'state' => $state, 'job' => \expImageAliasAdmin::job( $request['cancel'] ) ) );
        }
        if ( $request['job'] > 0 )
        {
            $job = \expImageAliasAdmin::job( $request['job'] );
            if ( $job === null )
                return $this->fail( self::EXIT_UNAVAILABLE, 'no job of image aliases with the id ' . $request['job'], array( 'action' => 'job' ) );
            if ( $this->format === 'json' )
                return $this->json( array( 'ok' => true, 'action' => 'job', 'job' => $job ), self::EXIT_OK );
            foreach ( self::jobLines( $job ) as $line )
                $this->output( $line );
            $this->output( ( $job['state'] === 'dead' || $job['state'] === 'failed' ? 'FAIL' : 'PASS' ) . ' job ' . $job['id'] . ' ' . $job['state'] );
            return $job['state'] === 'dead' || $job['state'] === 'failed' ? self::EXIT_ERRORS : self::EXIT_OK;
        }

        $dashboard = \expImageAliasAdmin::dashboard();
        $jobs = \expImageAliasAdmin::jobs( 5 );
        if ( $this->format === 'json' )
        {
            unset( $dashboard['staging']['alert_recipients'] );
            return $this->json( array( 'ok' => $dashboard['worst'] !== 'bad', 'action' => 'status', 'dashboard' => $dashboard, 'jobs' => $jobs ), self::EXIT_OK );
        }
        foreach ( self::dashboardLines( $dashboard, $jobs ) as $line )
            $this->output( $line );
        $this->output( ( $dashboard['worst'] === 'bad' ? 'FAIL' : ( $dashboard['worst'] === 'warn' ? 'WARN' : 'PASS' ) ) . ' image aliases '
                       . ( $dashboard['worst'] === 'ok' ? 'in order' : 'need attention (see checks)' ) );
        return self::EXIT_OK;
    }

    /**
     * The dashboard as lines of text (DB-less: from the arrays only).
     *
     * @param array $d expImageAliasAdmin::dashboard()
     * @param array[] $jobs expImageAliasAdmin::jobs()
     * @return string[]
     */
    public static function dashboardLines( array $d, array $jobs )
    {
        $onOff = function ( $value ) { return $value ? 'on' : 'off'; };
        $row = function ( $label, $value ) { return sprintf( '%-15s %s', $label, $value ); };
        $lines = array();
        $lines[] = $row( 'mode', $d['mode'] . ( $d['mode'] === \expImageAliasSettings::MODE_ON_DEMAND ? ' (address ' . $d['url_form'] . ', delivery ' . $d['delivery'] . ')' : '' )
                                . '; the command line itself always works in render mode' );
        $lines[] = $row( 'requests', 'wait at most ' . $d['max_wait'] . ' s, conversions at once ' . (int)$d['busy'] . ' of ' . (int)$d['max_parallel'] . ' busy' );
        $lines[] = $row( 'features', 'negotiation ' . $onOff( $d['negotiate'] ) . ', placeholder ' . $onOff( $d['placeholder'] ) . ', swap script '
                                    . $onOff( $d['swap_script'] ) . ', pre-generation ' . $onOff( $d['pre_generate'] )
                                    . ( !empty( $d['pre_generate_aliases'] ) ? ' (' . implode( ', ', $d['pre_generate_aliases'] ) . ')' : '' )
                                    . ( isset( $d['pre_generate_formats'] ) && !$d['pre_generate_formats'] ? ' without variants' : '' ) );
        $lines[] = $row( 'aliases', (int)$d['aliases'] . ' (this siteaccess and its related siteaccesses)' );
        $converters = array();
        foreach ( (array)$d['converter_rows'] as $c )
            $converters[] = $c['converter'] . ' ' . ( $c['formats'] ? implode( '+', $c['formats'] ) : '-' ) . ( $c['preferred'] ? ' (preferred)' : '' );
        $lines[] = $row( 'variants by', $converters ? implode( ', ', $converters ) : 'no converter writes WebP or AVIF' );
        $staging = $d['staging'];
        $stagingText = 'environment ' . $staging['environment'];
        if ( $staging['sync_from'] !== '' )
        {
            $source = is_array( $d['source'] ) ? $d['source'] : array();
            $stagingText .= ', source ' . $staging['sync_from'] . ' ' . ( isset( $source['base_url'] ) && $source['base_url'] !== '' ? $source['base_url'] : '(no BaseURL)' )
                          . ', ' . $d['source_state'];
            if ( !empty( $source['failures'] ) )
                $stagingText .= ', ' . (int)$source['failures'] . ' failures in a row';
            if ( !empty( $source['last_error'] ) )
                $stagingText .= ', last error ' . $source['last_error'];
        }
        else
            $stagingText .= $staging['environment'] === 'pro' ? ' (production: nothing is fetched)' : ', no source (SyncFrom)';
        $lines[] = $row( 'staging', $stagingText );
        if ( $d['queue']['available'] )
        {
            $lines[] = $row( 'queue', 'waiting ' . (int)$d['queue_total']['waiting'] . ', running ' . (int)$d['queue_total']['running']
                                    . ', failed for good ' . (int)$d['queue_total']['dead'] );
            foreach ( (array)$d['queue']['types'] as $type => $q )
                $lines[] = $row( '', sprintf( '%-20s waiting %d, running %d, failed %d%s', $type, $q['waiting'], $q['running'], $q['dead'],
                                              $q['registered'] ? '' : ' (type not registered)' ) );
        }
        else
            $lines[] = $row( 'queue', 'not available: runs go in this process' );
        $pause = $d['bulk_pause'];
        $lines[] = $row( 'bulk runs', $pause === null ? 'may run' : 'paused since ' . date( 'Y-m-d H:i', (int)$pause['since'] )
                                                                    . ( $pause['by'] !== '' ? ' by ' . $pause['by'] : '' )
                                                                    . ( $pause['reason'] !== '' ? ': ' . $pause['reason'] : '' ) );
        $c = $d['counts'];
        $lines[] = $row( 'last 24 hours', 'generated ' . (int)$c['generated'] . ', placeholders ' . (int)$c['placeholders'] . ', failures ' . (int)$c['failures']
                                         . ', staged ' . (int)$c['staged'] . ', runs ' . (int)$c['runs'] . ( $c['complete'] ? '' : ' (from the last ' . (int)$c['lines'] . ' lines)' ) );
        if ( !empty( $d['key']['exists'] ) )
            $lines[] = $row( 'signing key', ( $d['key_age_days'] === null ? 'there' : $d['key_age_days'] . ' days old' )
                                           . ( $d['previous_key_valid'] ? ', previous key valid until ' . date( 'Y-m-d H:i', (int)$d['key']['previous_until'] ) : '' ) );
        else
            $lines[] = $row( 'signing key', 'none yet' );
        $lines[] = $row( 'log', (string)$d['log_path'] );
        if ( $d['checks'] )
        {
            foreach ( $d['checks'] as $check )
                $lines[] = $row( 'check', $check['level'] . ': ' . ( isset( self::CHECK_TEXTS[$check['key']] ) ? self::CHECK_TEXTS[$check['key']] : $check['key'] ) );
        }
        else
            $lines[] = $row( 'checks', 'nothing needs attention' );
        if ( $jobs )
        {
            $lines[] = $row( 'latest runs', '' );
            foreach ( $jobs as $job )
                $lines[] = '  ' . self::jobSummary( $job );
        }
        return $lines;
    }

    /**
     * @param array $job expImageAliasAdmin::jobRow()
     * @return string one line
     */
    public static function jobSummary( array $job )
    {
        return sprintf( '#%d %-10s %-9s %s %s%s', $job['id'], $job['action'], $job['state'],
                        $job['total'] > 0 ? $job['done'] . '/' . $job['total'] : '-', $job['scope'],
                        $job['created'] ? '  ' . date( 'Y-m-d H:i', (int)$job['created'] ) : '' );
    }

    /**
     * @param array $job expImageAliasAdmin::jobRow()
     * @return string[]
     */
    public static function jobLines( array $job )
    {
        $row = function ( $label, $value ) { return sprintf( '%-10s %s', $label, $value ); };
        $lines = array( $row( 'job', '#' . $job['id'] . ' ' . $job['type'] . ' (' . $job['action'] . ')' ),
                        $row( 'state', $job['state'] . ( $job['can_cancel'] ? ' (can be cancelled: --cancel=' . $job['id'] . ')' : '' ) ),
                        $row( 'progress', $job['total'] > 0 ? $job['done'] . '/' . $job['total'] . ' (' . (int)$job['percent'] . ' %)' : '-' ),
                        $row( 'scope', $job['scope'] ),
                        $row( 'run', $job['run'] !== '' ? $job['run'] : '-' ) );
        if ( $job['message'] !== '' )
            $lines[] = $row( 'message', $job['message'] );
        foreach ( array( 'created', 'started', 'finished' ) as $key )
        {
            if ( $job[$key] > 0 )
                $lines[] = $row( $key, date( 'Y-m-d H:i:s', (int)$job[$key] ) );
        }
        if ( $job['counts'] )
            $lines[] = $row( 'counts', self::countsText( $job['counts'] ) );
        if ( $job['error'] !== '' )
            $lines[] = $row( 'error', $job['error'] );
        return $lines;
    }

    /**
     * @param array $counts name => number
     * @return string the counts that are not 0
     */
    public static function countsText( array $counts )
    {
        $parts = array();
        foreach ( $counts as $name => $count )
        {
            if ( (int)$count !== 0 )
                $parts[] = str_replace( '_', ' ', (string)$name ) . ' ' . (int)$count;
        }
        return $parts ? implode( ', ', $parts ) : 'nothing';
    }

    // ------------------------------------------------------------------------------------------------ runs

    /**
     * generate, regenerate, purge, resync.
     *
     * @param array $request
     * @return int exit code
     */
    protected function runScope( array $request )
    {
        $command = $request['command'];
        $known = \expImageAliasAdmin::knownAliases();
        $scope = \expImageAliasAdmin::scopeFromInput( $request['input'], $known );
        if ( $scope['errors'] )
            $this->usage( self::scopeErrors( $scope['errors'] ) );
        $options = $scope['options'];
        $describe = \expImageAliasStager::describeScope( $options );
        $scopeOptions = array_intersect_key( $options, array_flip( array( 'node', 'subtree', 'since', 'limit', 'class', 'attribute' ) ) );

        if ( $command === 'resync' && \expImageAliasStager::forRequest() === null )
            return $this->fail( self::EXIT_UNAVAILABLE, 'not a staging installation: image.ini [AliasStaging] Environment is pro, SyncFrom names no source or its BaseURL is empty',
                                array( 'action' => $command ) );

        if ( $scope['preview'] )
        {
            $count = \expImageAliasStager::countImages( $scopeOptions );
            $aliases = isset( $options['alias'] ) ? $options['alias'] : $known;
            if ( $this->format === 'json' )
                return $this->json( array( 'ok' => true, 'action' => $command, 'mode' => 'preview', 'scope' => $describe, 'images' => $count,
                                           'aliases' => $aliases ), self::EXIT_OK );
            $this->output( sprintf( '%-10s %s', 'scope', $describe ) );
            $this->output( sprintf( '%-10s %s', 'aliases', $aliases ? implode( ', ', $aliases ) : '-' ) );
            $this->output( "PASS dry run: $command would handle $count image" . ( $count === 1 ? '' : 's' ) . ', nothing changed' );
            return self::EXIT_OK;
        }

        if ( $request['queue'] )
        {
            $type = $command === 'resync' ? \expImageAliasJob::TYPE_RESYNC : \expImageAliasJob::TYPE_BULK;
            if ( !\expImageAliasJob::queueAvailable( $type ) )
                return $this->fail( self::EXIT_UNAVAILABLE, "the job queue cannot take $type (expqueue not installed, not active or the type not registered); run without --queue",
                                    array( 'action' => $command ) );
            $started = $command === 'resync' ? \expImageAliasJob::startResync( $options, true ) : \expImageAliasJob::startBulk( $options, true );
            if ( $started['mode'] !== 'queue' )
                return $this->fail( self::EXIT_ERRORS, 'the job could not be queued; the run went in this process: ' . self::countsText( (array)$started['summary']['counts'] ),
                                    array( 'action' => $command, 'run' => $started['run'], 'summary' => $started['summary'] ) );
            if ( $this->format === 'json' )
                return $this->json( array( 'ok' => true, 'action' => $command, 'mode' => 'queue', 'job' => $started['job'], 'run' => $started['run'],
                                           'scope' => $describe ), self::EXIT_OK );
            $this->output( sprintf( '%-10s %s', 'scope', $describe ) );
            $this->output( sprintf( '%-10s %s', 'run', $started['run'] ) );
            $this->output( 'PASS queued job ' . $started['job'] . ' (exp:image:alias status --job=' . $started['job'] . ' shows its progress)' );
            return self::EXIT_OK;
        }

        if ( $this->format === 'text' )
        {
            $this->output( sprintf( '%-10s %s', 'scope', $describe ) );
            if ( $command !== 'resync' )
                $this->output( sprintf( '%-10s %s', 'aliases', implode( ', ', isset( $options['alias'] ) ? $options['alias'] : $known ) ) );
        }
        $progress = $this->format === 'text' ? array( $this, 'progress' ) : null;
        if ( $command === 'resync' )
            $started = \expImageAliasJob::startResync( $options, false, $progress );
        else
            $started = \expImageAliasJob::startBulk( $options, false, $progress, array( 'on_pause' => $request['wait'] ? 'wait' : 'stop' ) );
        $summary = (array)$started['summary'];
        $result = isset( $summary['result'] ) ? (string)$summary['result'] : 'failed';
        $counts = isset( $summary['counts'] ) ? (array)$summary['counts'] : array();
        $failed = (int)( $counts['failed'] ?? 0 ) + (int)( $counts['variants_failed'] ?? 0 ) + (int)( $counts['timeout'] ?? 0 );
        if ( $result === 'paused' )
            $exit = self::EXIT_PAUSED;
        else if ( $result === 'inactive' )
            $exit = self::EXIT_UNAVAILABLE;
        else if ( $result !== 'done' || $failed > 0 )
            $exit = self::EXIT_ERRORS;
        else
            $exit = self::EXIT_OK;
        if ( $this->format === 'json' )
            return $this->json( array( 'ok' => $exit === self::EXIT_OK, 'action' => $command, 'mode' => 'sync', 'run' => $started['run'], 'scope' => $describe,
                                       'summary' => $summary ), $exit );
        $images = isset( $summary['images'] ) ? (int)$summary['images'] : 0;
        $this->output( sprintf( '%-10s %s', 'counts', self::countsText( $counts ) ) );
        if ( isset( $summary['response_cache'] ) )
            $this->output( sprintf( '%-10s %s', 'velocity', 'response cache ' . $summary['response_cache'] ) );
        $this->output( sprintf( '%-10s %s (var/log/imagealias.log)', 'run', $started['run'] ) );
        $time = isset( $summary['ms'] ) ? round( $summary['ms'] / 1000, 1 ) . ' s' : '';
        if ( $exit === self::EXIT_PAUSED )
            $this->output( "FAIL $command stopped after $images images: bulk runs are paused (exp:image:alias status --resume, or --wait)" );
        else if ( $exit === self::EXIT_OK )
            $this->output( "PASS $command: $images images in $time" );
        else
            $this->output( "FAIL $command: $result, $images images in $time, $failed failed" );
        return $exit;
    }

    /**
     * The progress of a run here: one line a second at most, and the last one.
     *
     * @param array $p done, total, path, counts
     */
    public function progress( array $p )
    {
        $now = microtime( true );
        $last = (int)$p['done'] >= (int)$p['total'];
        if ( !$last && $now - $this->lastProgress < 1.0 )
            return;
        $this->lastProgress = $now;
        $this->output( self::progressLine( $p ) );
    }

    /**
     * @param array $p done, total, path, counts
     * @return string
     */
    public static function progressLine( array $p )
    {
        $total = max( 0, (int)$p['total'] );
        $done = (int)$p['done'];
        $percent = $total > 0 ? (int)floor( min( $done, $total ) * 100 / $total ) : 100;
        return sprintf( '%' . strlen( (string)$total ) . 'd/%d %3d%%  %s  %s', $done, $total, $percent, (string)$p['path'],
                        self::countsText( isset( $p['counts'] ) ? (array)$p['counts'] : array() ) );
    }

    // ------------------------------------------------------------------------------------------------ test

    /**
     * @param array $request
     * @return int exit code
     */
    protected function test( array $request )
    {
        $exit = self::EXIT_OK;
        $report = array( 'ok' => true, 'action' => 'test', 'image' => null, 'made' => array(), 'source' => null );
        if ( $request['node'] > 0 || $request['object'] > 0 )
        {
            $this->loginForTest();
            // the addresses a page gets: the mode of the settings, not the command line's render mode (which would
            // make every missing alias while reporting it)
            \expImageAliasSettings::setMode( \expImageAliasSettings::configuredMode() );
            try
            {
                $image = \expImageAliasAdmin::testImage( $request['node'], $request['object'] );
            }
            finally
            {
                \expImageAliasSettings::setMode( null );
            }
            $report['image'] = $image;
            if ( !$image['ok'] )
            {
                $report['ok'] = false;
                $exit = $image['error'] === 'no-image' ? self::EXIT_ERRORS : self::EXIT_UNAVAILABLE;
                $this->text( 'FAIL ' . ( $image['error'] === 'no-image' ? 'the object has no image' : ( $image['error'] === 'no-access' ? 'no read access to the object (log in with -l)' : 'no such object' ) ) );
            }
            else
            {
                $this->text( sprintf( 'object %d "%s", node %d, version %d', $image['object']['id'], $image['object']['name'], $image['object']['node'], $image['object']['version'] ) );
                $found = false;
                foreach ( $image['images'] as $one )
                {
                    foreach ( self::imageLines( $one, $request['alias'] ) as $line )
                        $this->text( $line );
                    if ( $request['alias'] === '' )
                        continue;
                    foreach ( $one['aliases'] as $row )
                    {
                        if ( $row['alias'] !== $request['alias'] )
                            continue;
                        $found = true;
                        if ( $request['dry_run'] )
                            continue;
                        $made = \expImageAliasAdmin::makeOne( $one['attribute'], $one['version'], $one['language'], $row['alias'], $row['siteaccess'] );
                        $report['made'][] = $made + array( 'attribute' => $one['attribute'], 'language' => $one['language'], 'siteaccess' => $row['siteaccess'] );
                        $this->text( sprintf( '  make %s (%s): %s in %d ms%s', $row['alias'], $row['siteaccess'], $made['result'], $made['ms'],
                                              $made['variants'] ? ', variants ' . self::variantsText( $made['variants'] ) : '' ) );
                        if ( !$made['ok'] )
                            $exit = self::EXIT_ERRORS;
                    }
                }
                if ( $request['alias'] !== '' && !$found )
                {
                    $exit = self::EXIT_ERRORS;
                    $this->text( 'FAIL the alias ' . $request['alias'] . ' is not one of this siteaccess and its related siteaccesses' );
                }
            }
        }
        // the source: the one asked for, else (with an image test) the one SyncFrom names, if any
        $staging = \expImageAliasSettings::staging();
        $source = is_string( $request['source'] ) ? $request['source'] : $staging['sync_from'];
        if ( $source === '' && $request['source'] !== false )
        {
            $exit = max( $exit, self::EXIT_UNAVAILABLE );
            $this->text( 'FAIL no source to test: image.ini [AliasStaging] SyncFrom is empty (name one: --source=pro)' );
            $report['source'] = array( 'ok' => false, 'result' => 'no-source' );
        }
        else if ( $source === '' )
            $this->text( 'source     none: ' . ( $staging['environment'] === 'pro' ? 'production installation, nothing is fetched' : 'SyncFrom names no source' ) );
        else
        {
            $test = \expImageAliasAdmin::testSource( $source, $request['path'] );
            $report['source'] = $test;
            if ( $test['result'] === 'no-url' || $test['result'] === 'bad-path' )
                $this->text( 'source     ' . $test['environment'] . ': ' . ( $test['result'] === 'no-url' ? 'no BaseURL in image.ini [AliasSource_' . $test['environment'] . ']'
                                                                                                          : 'not a storage path the source maps: ' . $request['path'] ) );
            else
                $this->text( sprintf( 'source     %s %s: %s, status %d, %s, %s ms, credential %s', $test['environment'], $test['url'],
                                      $test['result'], $test['status'], $test['type'] !== '' ? $test['type'] : 'no type', $test['ms'], $test['credential'] ) );
            if ( !$test['ok'] )
                $exit = $exit === self::EXIT_OK ? self::EXIT_ERRORS : $exit;
        }
        $report['ok'] = $exit === self::EXIT_OK;
        if ( $this->format === 'json' )
            return $this->json( $report, $exit );
        if ( $exit === self::EXIT_OK )
            $this->output( 'PASS test' );
        else
            $this->output( 'FAIL test' );
        return $exit;
    }

    /**
     * One image of the test report as lines (DB-less).
     *
     * @param array $one an image of expImageAliasAdmin::testImage()
     * @param string $alias only this alias ('' all)
     * @return string[]
     */
    public static function imageLines( array $one, $alias = '' )
    {
        $lines = array( sprintf( 'image      %s (%s), attribute %d version %d %s', $one['identifier'], $one['name'], $one['attribute'], $one['version'], $one['language'] ) );
        if ( $one['original'] === null )
        {
            $lines[] = '  no original stored';
            return $lines;
        }
        $o = $one['original'];
        $lines[] = sprintf( '  %-24s %4dx%-4d %-12s %-9s %s', 'original', $o['width'], $o['height'], $o['mime'], $o['exists'] ? 'there' : 'MISSING', $o['url'] );
        foreach ( $one['aliases'] as $row )
        {
            if ( $alias !== '' && $row['alias'] !== $alias )
                continue;
            $lines[] = sprintf( '  %-24s %4dx%-4d %-12s %-9s %s%s', $row['alias'] . ( $row['siteaccess'] !== '' ? '@' . $row['siteaccess'] : '' ), $row['width'], $row['height'],
                                $row['mime'], $row['exists'] ? 'there' : 'on demand', $row['url'],
                                $row['formats'] ? '  [' . self::variantsText( $row['formats'] ) . ']' : '' );
        }
        return $lines;
    }

    /**
     * @param array $variants format => state
     * @return string
     */
    public static function variantsText( array $variants )
    {
        $parts = array();
        foreach ( $variants as $format => $state )
            $parts[] = $format . ' ' . $state;
        return implode( ', ', $parts );
    }

    /**
     * The test reads the object as a user: the one given with --login, else site.ini [UserSettings] UserCreatorID
     * (as the cronjobs and the content job command).
     */
    protected function loginForTest()
    {
        $user = \eZUser::currentUser();
        if ( $user && !$user->isAnonymous() )
            return;
        $userID = \eZINI::instance()->variable( 'UserSettings', 'UserCreatorID' );
        $user = \eZUser::fetch( $userID );
        if ( $user )
            \eZUser::setCurrentlyLoggedInUser( $user, $userID, \eZUser::NO_SESSION_REGENERATE );
    }

    // ------------------------------------------------------------------------------------------------ output

    /**
     * Writes a line in text mode only.
     *
     * @param string $line
     */
    protected function text( $line )
    {
        if ( $this->format === 'text' )
            $this->output( $line );
    }

    /**
     * @param string $message
     * @param array $data for the machine output
     * @return int EXIT_OK
     */
    protected function done( $message, array $data )
    {
        if ( $this->format === 'json' )
            return $this->json( array( 'ok' => true, 'message' => preg_replace( '/^PASS /', '', $message ) ) + $data, self::EXIT_OK );
        $this->output( $message );
        return self::EXIT_OK;
    }

    /**
     * @param int $exit
     * @param string $message
     * @param array $data for the machine output
     * @return int $exit
     */
    protected function fail( $exit, $message, array $data = array() )
    {
        if ( $this->format === 'json' )
            return $this->json( array( 'ok' => false, 'message' => $message ) + $data, $exit );
        $this->error( 'FAIL ' . $message );
        return $exit;
    }

    /**
     * @param array $data
     * @param int $exit
     * @return int $exit
     */
    protected function json( array $data, $exit )
    {
        $this->cli()->output( json_encode( array( 'schema' => self::SCHEMA, 'exit' => $exit ) + $data,
                                           JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) );
        return $exit;
    }

    /**
     * Ends with the usage errors (exit 2).
     *
     * @param string[] $errors
     */
    protected function usage( array $errors )
    {
        if ( $this->format === 'json' )
            $this->json( array( 'ok' => false, 'errors' => array_values( $errors ) ), self::EXIT_USAGE );
        else
        {
            foreach ( $errors as $error )
                $this->error( 'FAIL ' . $error );
            $this->error( 'exp:image:alias --help explains the subcommands and options' );
        }
        $this->shutdown( self::EXIT_USAGE );
    }

    /**
     * @return string the operating system user (after the switch from root), for the log of a pause
     */
    protected static function osUser()
    {
        if ( function_exists( 'posix_getpwuid' ) && function_exists( 'posix_geteuid' ) )
        {
            $pw = posix_getpwuid( posix_geteuid() );
            if ( $pw && isset( $pw['name'] ) )
                return (string)$pw['name'];
        }
        return (string)get_current_user();
    }
}

}
?>
