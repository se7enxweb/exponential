<?php
/**
 * File containing the velocity control script.
 *
 * @alias vc
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => (
    "Exponential Velocity - control the bundled application server\n\n" .
    "Commands:\n" .
    "  status     what it is doing (the default)\n" .
    "  start      start it\n" .
    "  stop       ask it to stop, and wait\n" .
    "  graceful   re-exec without dropping the listening socket\n" .
    "  restart    stop, then start\n" .
    "  kill       stop without asking, for a wedged worker\n" .
    "  command    print the command line it would run, and exit\n" .
    "             (--keep-global=Name[,Name] appends globals to keep between\n" .
    "              requests, after the built-in defaults; also on start/restart)\n" .
    "  config     read and write the settings it runs on\n" .
    "  cache      cache clear: re-render every cached page on its next request\n" .
    "             (after a template or stylesheet change; no restart needed)\n" .
    "  layout     the configuration tree and every file the server uses\n" .
    "  site|conf|mod enable|disable <name>   as a2ensite/a2enconf/a2enmod do\n" .
    "  ctl        the engine's qbixctl with this installation's tree, site and pid file:\n" .
    "             ctl status | ctl configtest | ctl layout | ctl ensite NAME ...\n" .
    "             (frankenphp: ctl caddyfile | validate | adapt | version | list-modules)\n" .
    "  install    put the engine's binary in place (frankenphp: download the pinned\n" .
    "             release and verify its SHA-256; --force, --from=<file>, --check,\n" .
    "             --trust-github-digest; qbix: nothing to do)\n\n" .
    "Engines ([ServerSettings] Engine is the default; --engine=<name>[,<name>] or --all\n" .
    "for start, stop, restart, graceful, kill and status reach the others):\n" .
    "  php         PHP's built-in web server: development, always works (shipped default)\n" .
    "  frankenphp  FrankenPHP (Caddy with PHP built in): production\n" .
    "  qbix        the bundled Qbix server: experimental, for tests\n" .
    "  Each has its own port, pid file and logs, so they can run side by side.\n\n" .
    "Configuration tree (Debian Apache style, /etc/vc or /etc/qbix):\n" .
    "  layout                               show it: vc.conf, ports.conf, envvars,\n" .
    "                                       sites/conf/mods-available and -enabled\n" .
    "  layout migrate                       write it from these settings now\n" .
    "  site enable|disable <name>           link or unlink sites-enabled/<name>.conf\n" .
    "  conf enable|disable <name>           link or unlink conf-enabled/<name>.conf\n" .
    "  mod enable|disable <name>            link or unlink mods-enabled/<name>.conf\n\n" .
    "Configuration:\n" .
    "  config list [Block]                  every setting, and which are overridden\n" .
    "  config get <Block> <Variable>        one value\n" .
    "  config set <Block> <Variable> <v>    write it to this installation\n" .
    "  config unset <Block> <Variable>      return it to the packaged value\n" .
    "  config paths                         which file is which\n\n" .
    "Through the console:\n" .
    "  ./bin/php/console exp:velocity status --allow-root-user\n" .
    "  ./bin/php/console exp:velocity restart --allow-root-user\n" .
    "  ./bin/php/console exp:vc status --allow-root-user   (exp:vc is shorthand for exp:velocity)\n\n" .
    "Directly:\n" .
    "  php bin/php/velocity.php start --allow-root-user\n" .
    "  php bin/php/velocity.php status --json --allow-root-user\n\n" .
    "Settings come from velocity.ini; override per installation in\n" .
    "settings/override/velocity.ini.append.php." ),
    'use-session' => false,
    'use-modules' => false,
    'use-extensions' => true ) );

$script->startup();

// GNU and BSD spellings alike (--name V, -name=V, -name, --no-name, --), as the
// engine's own console takes them; eZCLI reads only --name=V and --name. Names
// that take a value: this script's and eZScript's standard ones.
list( $velocityArgs, $velocityTail ) = expVelocity::normalizeCliArguments(
    array_slice( $_SERVER['argv'], 1 ),
    array( 'keep-global', 'siteaccess', 'login', 'password', 'engine', 'from' ),
    array( 'json', 'help', 'quiet', 'verbose', 'colors', 'no-colors', 'logfiles', 'no-logfiles',
           'allow-root-user', 'debug', 'force', 'check', 'trust-github-digest', 'all' ) );

$options = $script->getOptions( '[json][keep-global:][engine:][from:][force][check][trust-github-digest][all][https]', '[command]',
    array( 'json' => 'Report as JSON, for a caller that is not a person',
           'keep-global' => 'More globals to keep between requests (comma-separated), appended to the '
                          . 'built-in defaults and velocity.ini KeepGlobals[]; for start, restart and command',
           'engine' => 'php, frankenphp or qbix -- or several, comma-separated -- for this command only, '
                     . 'instead of [ServerSettings] Engine',
           'all' => 'start, stop, restart, graceful, kill or status every engine, one after the other',
           'from' => 'install: take the binary from this file instead of downloading it (still verified)',
           'force' => 'install: download again even if the binary is already there',
           'check' => 'install: re-hash the installed binary against its SHA-256',
           'trust-github-digest' => 'install: with no Sha256 pinned for the version, accept the digest '
                                  . 'GitHub publishes for the release' ),
    $velocityArgs );
// After "--": plain arguments, never read as options here.
$options['arguments'] = array_merge( $options['arguments'], $velocityTail );
$script->initialize();

$verbs = array( 'start', 'stop', 'graceful', 'restart', 'kill', 'status',
                'command', 'config', 'cache', 'layout', 'site', 'conf', 'mod', 'ctl', 'ssl', 'install' );
$verb = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';

if ( !in_array( $verb, $verbs, true ) )
{
    $cli->error( "Unknown command '$verb'. Try one of: " . implode( ', ', $verbs ) );
    $script->shutdown( 1 );
}

// Which engines this command is for: --all, a list in --engine, or one.
$engineList = !empty( $options['all'] ) ? expVelocity::engines()
            : array_values( array_filter( array_map( 'trim', explode( ',', (string)( $options['engine'] ?? '' ) ) ) ) );
try
{
    $velocity = expVelocity::create( 'velocity.ini', count( $engineList ) === 1 ? $engineList[0] : null );
    foreach ( $engineList as $engineName )
        expVelocity::create( 'velocity.ini', $engineName );
}
catch ( InvalidArgumentException $e )
{
    $cli->error( 'velocity: ' . $e->getMessage() );
    $script->shutdown( 1 );
}
$asJson = !empty( $options['json'] );
if ( !empty( $options['keep-global'] ) )
    $velocity->appendKeepGlobals( $options['keep-global'] );

// --https: TLS for this start, with a self-signed certificate unless
// [HTTPSSettings] names one. The frankenphp engine's; with --all it reaches
// that engine and leaves the others as they are.
if ( !empty( $options['https'] ) )
{
    if ( !in_array( $verb, array( 'start', 'restart', 'graceful' ), true ) )
    {
        $cli->error( 'velocity: --https goes with start, restart and graceful' );
        $script->shutdown( 1 );
    }
    if ( count( $engineList ) <= 1 && !method_exists( $velocity, 'forceHttps' ) )
    {
        $cli->error( 'velocity: --https is the frankenphp engine\'s (--engine=frankenphp); '
                     . ( $velocity->engineName() === 'php' ? 'the built-in server has no TLS'
                         : 'the ' . $velocity->engineName() . ' engine uses [HTTPSSettings] Enabled with Certificate and Key' ) );
        $script->shutdown( 1 );
    }
    if ( method_exists( $velocity, 'forceHttps' ) )
        $velocity->forceHttps();
}

/**
 * A message or note with this installation's paths made relative to it.
 */
function velocityShortPaths( $text )
{
    $root = rtrim( eZSys::rootDir(), '/' );
    return $root !== '' ? str_replace( $root . '/', '', (string)$text ) : (string)$text;
}

/**
 * Render a status array for a person to read: a header with the engine and
 * its state, the addresses to open (full URLs, so a terminal makes them
 * clickable), then the details, paths relative to the installation.
 */
function velocityPrintStatus( eZCLI $cli, array $status, $velocity = null )
{
    $rel = function ( $path ) use ( $velocity )
    {
        return $velocity !== null ? $velocity->relativePath( $path ) : (string)$path;
    };
    $row = function ( $label, $value ) use ( $cli )
    {
        $cli->output( sprintf( '    %-10s %s', $label, $value ) );
    };

    $cli->output( '' );
    if ( $velocity !== null )
    {
        $name = $velocity->engineName() . '  ' . $velocity->role() . ( $velocity->isDefault() ? ', default' : '' );
        $state = $status['running'] ? $cli->stylize( 'success', 'running' ) : $cli->stylize( 'warning', 'stopped' );
        $dot = $status['running'] ? $cli->stylize( 'success', '●' ) : '○';
        $cli->output( '  ' . $dot . ' ' . $cli->stylize( 'emphasize', $name ) . '   ' . $state );
    }
    else
        $cli->output( '  ' . ( $status['running'] ? 'running' : 'stopped' ) );

    if ( $velocity !== null )
    {
        $urls = $velocity->urls();
        if ( !$status['running'] )
            $urls = array_slice( $urls, 0, 1 );
        foreach ( $urls as $url )
            $row( $url[0], $cli->stylize( 'link', $url[1] ) );
        if ( !$status['running'] )
            $row( '', 'start: exp:velocity start --engine=' . $velocity->engineName() );
    }

    $cli->output( '' );
    if ( $status['running'] )
    {
        $process = $status['processes'] . ' process' . ( $status['processes'] == 1 ? '' : 'es' )
                 . ( $status['parent'] ? ', parent ' . $status['parent'] : '' )
                 . ' · port ' . ( $status['listening'] ? implode( ', ', $status['listening'] ) : 'not listening yet' );
        if ( isset( $status['server'] ) )
            $process .= ' · ' . $status['threads'] . ( $status['server'] === 'php' ? ' workers' : ' threads' );
        $row( 'Process', $process );
    }
    if ( isset( $status['server'] ) )
    {
        $row( 'Version', ( $status['version'] !== '' ? $status['version'] : 'binary missing' )
                         . '  (' . $rel( $status['binary'] ) . ', ' . $status['binarySource'] . ')' );
        if ( $status['running'] && $status['admin'] !== 'none' )
        {
            // Caddy writes a socket as unix/<path>; shown as the path it is.
            $admin = strpos( $status['admin'], 'unix/' ) === 0
                   ? velocityShortPaths( substr( $status['admin'], 5 ) ) . ' (unix socket)' : $status['admin'];
            $row( 'Admin API', $admin . ( $status['adminReachable'] ? '' : ' (not answering)' ) );
        }
        $row( $status['server'] === 'php' ? 'Router' : 'Config', $rel( $status['caddyfile'] ) );
    }
    if ( $velocity !== null && $velocity->engineName() === 'php' )
        $row( 'HTTPS', 'none: the built-in server speaks plain HTTP only' );
    elseif ( $velocity !== null && $velocity->engineName() === 'frankenphp' )
    {
        if ( $status['https'] )
            $row( 'HTTPS', 'on, port ' . $velocity->configuredHttpsPort() . ' · '
                . ( !empty( $status['selfSigned'] )
                    ? 'self-signed certificate ' . velocityShortPaths( $status['certificate'] ) . ' (a browser warns once)'
                    : 'certificate ' . velocityShortPaths( (string)$status['certificate'] ) ) );
        else
            $row( 'HTTPS', 'off; port ' . $velocity->configuredHttpsPort() . ' with start --https or [FrankenPHPSettings] HTTPS=enabled'
                . ' (self-signed unless [HTTPSSettings] Certificate and Key)' );
    }
    elseif ( $velocity !== null )
        $row( 'HTTPS', $status['https'] ? 'on, port ' . $velocity->configuredHttpsPort()
            : 'off; port ' . $velocity->configuredHttpsPort() . ' once [HTTPSSettings] Enabled=true with Certificate and Key' );
    else
        $row( 'HTTPS', $status['https'] ? 'on' : 'off' );
    $row( 'Log', $rel( $status['log'] ) );

    $notes = $status['notes'] ?? array();
    foreach ( array_values( $notes ) as $n => $note )
        $row( $n === 0 ? 'Not used' : '', '· ' . velocityShortPaths( $note ) );
}

/**
 * The application's backend pages, once: they are the same on every engine,
 * so they are shown as links on one that runs -- the default when it does.
 *
 * @param array $engines [expVelocity, status] pairs
 */
function velocityPrintAdmin( eZCLI $cli, array $engines )
{
    $serving = null;
    foreach ( $engines as $engine )
        if ( $engine[1]['running'] && ( $serving === null || $engine[0]->isDefault() ) )
            $serving = $engine[0];
    if ( $serving === null || !$serving->adminPaths() )
        return;

    $base = rtrim( $serving->urls()[0][1], '/' );
    $cli->output( '' );
    $cli->output( '  ' . $cli->stylize( 'emphasize', 'Exponential' ) . '   the same on every engine; here on '
                  . $serving->engineName() );
    foreach ( $serving->adminPaths() as $page )
        $cli->output( sprintf( '    %-10s %s', $page[0], $cli->stylize( 'link', $base . $page[1] ) ) );
}

/**
 * One line per engine for `status --all`, before the details of those that
 * run: engine, role, state and the address to open.
 */
function velocityPrintOverview( eZCLI $cli, array $engines )
{
    $cli->output( '' );
    $cli->output( sprintf( '    %-11s %-24s %-8s %s', 'Engine', 'Role', 'State', 'URL' ) );
    foreach ( $engines as $engine )
    {
        list( $velocity, $status ) = $engine;
        $role = $velocity->role() . ( $velocity->isDefault() ? ', default' : '' );
        $state = $status['running'] ? 'running' : 'stopped';
        $url = $velocity->urls();
        $cli->output( '  ' . ( $status['running'] ? $cli->stylize( 'success', '●' ) : '○' ) . ' '
            . sprintf( '%-11s %-24s ', $velocity->engineName(), $role )
            . $cli->stylize( $status['running'] ? 'success' : 'warning', sprintf( '%-8s', $state ) ) . ' '
            . $cli->stylize( 'link', $url[0][1] ) );
    }
    $running = array();
    foreach ( $engines as $engine )
        if ( $engine[1]['running'] )
            $running[] = $engine[0]->engineName();
    if ( $running )
    {
        $commands = array();
        foreach ( $running as $name )
            $commands[] = 'exp:velocity stop --engine=' . $name;
        if ( count( $running ) > 1 )
            $commands[] = 'exp:velocity stop --all';
        $cli->output( '' );
        foreach ( $commands as $n => $command )
            $cli->output( sprintf( '    %-10s %s', $n === 0 ? 'Stop' : '', $command ) );
    }
}

/**
 * Print a settings listing for a person to read.
 */
function velocityPrintConfig( eZCLI $cli, array $rows )
{
    $block = null;
    foreach ( $rows as $row )
    {
        if ( $row['block'] !== $block )
        {
            $block = $row['block'];
            $cli->output( '' );
            $cli->output( '  [' . $block . ']' );
        }
        // Say which values this installation chose. Without that the listing
        // reads as though every line were deliberate, when most are defaults.
        $mark = $row['overridden'] ? ' *' : '  ';
        $cli->output( sprintf( '  %s %-26s %s',
            $mark, $row['variable'], $row['value'] === '' ? "''" : $row['value'] ) );
    }
    $cli->output( '' );
    $cli->output( '  * set by this installation; everything else is the packaged default' );
}

// Several engines: the same verb for each, one after the other. One that
// fails does not stop the rest; the exit code says whether all succeeded.
if ( count( $engineList ) > 1 )
{
    if ( !in_array( $verb, array( 'start', 'stop', 'restart', 'graceful', 'kill', 'status' ), true ) )
    {
        $cli->error( "velocity: --all and several engines work with start, stop, restart, graceful, kill and status, not $verb" );
        $script->shutdown( 1 );
    }
    $allOk = true;
    $report = array();
    $overview = array();
    foreach ( $engineList as $engineName )
    {
        $engine = expVelocity::create( 'velocity.ini', $engineName );
        if ( !empty( $options['keep-global'] ) )
            $engine->appendKeepGlobals( $options['keep-global'] );
        if ( !empty( $options['https'] ) && method_exists( $engine, 'forceHttps' ) )
            $engine->forceHttps();
        if ( $verb === 'status' )
        {
            $status = $engine->status();
            $report[$engineName] = array( 'ok' => true, 'role' => $engine->role(),
                                          'default' => $engine->isDefault(), 'data' => $status );
            $overview[] = array( $engine, $status );
            continue;
        }
        $result = $engine->$verb();
        // For every engine at once, one that is already running is where
        // start was meant to take it, not a failure.
        if ( $verb === 'start' && !$result['ok'] && $result['message'] === 'already running' )
            $result['ok'] = true;
        $allOk = $allOk && $result['ok'];
        $report[$engineName] = $result + array( 'role' => $engine->role(), 'default' => $engine->isDefault() );
        if ( !$asJson )
        {
            $line = 'velocity ' . $engineName . ': ' . velocityShortPaths( $result['message'] );
            $result['ok'] ? $cli->output( $cli->stylize( 'emphasize', $line ) ) : $cli->error( $line );
        }
    }
    if ( $asJson )
        $cli->output( json_encode( array( 'ok' => $allOk, 'data' => $report ) ) );
    elseif ( $overview )
    {
        // An overview of all, then the details of those that run.
        velocityPrintOverview( $cli, $overview );
        foreach ( $overview as $engine )
            if ( $engine[1]['running'] )
                velocityPrintStatus( $cli, $engine[1], $engine[0] );
        velocityPrintAdmin( $cli, $overview );
        $cli->output( '' );
    }
    elseif ( in_array( $verb, array( 'start', 'restart', 'graceful' ), true ) )
    {
        // Where to go now: the address of each engine that runs.
        $overview = array();
        foreach ( $engineList as $engineName )
        {
            $engine = expVelocity::create( 'velocity.ini', $engineName );
            $overview[] = array( $engine, $engine->status() );
        }
        velocityPrintOverview( $cli, $overview );
        velocityPrintAdmin( $cli, $overview );
        $cli->output( '' );
    }
    $script->shutdown( $allOk ? 0 : 1 );
}

switch ( $verb )
{
    case 'config':
    {
        $config = new expVelocityConfig();
        $action = isset( $options['arguments'][1] )
            ? strtolower( trim( $options['arguments'][1] ) ) : 'list';
        $block = $options['arguments'][2] ?? null;
        $variable = $options['arguments'][3] ?? null;
        $value = $options['arguments'][4] ?? null;

        if ( $action === 'paths' )
        {
            $paths = $config->paths();
            if ( $asJson )
                $cli->output( json_encode( array( 'ok' => true, 'data' => $paths ) ) );
            else
            {
                $cli->output( '' );
                foreach ( $paths as $what => $where )
                    $cli->output( sprintf( '  %-38s %s', $what, $where ) );
                $cli->output( '' );
            }
            $script->shutdown( 0 );
        }

        if ( $action === 'list' )
        {
            // `config list Block` reads better than requiring a flag, so the
            // third argument is the block when there is one.
            $rows = $config->listAll( $block );
            if ( $asJson )
                $cli->output( json_encode( array( 'ok' => true, 'data' => $rows ) ) );
            else if ( !$rows )
                $cli->error( 'velocity: no settings found'
                    . ( $block ? " in [$block]" : '' ) );
            else
                velocityPrintConfig( $cli, $rows );
            $script->shutdown( $rows ? 0 : 1 );
        }

        if ( $block === null || $variable === null )
        {
            $cli->error( "velocity: $action needs a block and a variable, "
                . 'for example: config get ServerSettings Port' );
            $script->shutdown( 1 );
        }

        if ( $action === 'get' )
        {
            $result = $config->get( $block, $variable );
            if ( $asJson )
                $cli->output( json_encode( $result ) );
            else if ( empty( $result['ok'] ) )
                $cli->error( 'velocity: ' . $result['message'] );
            else
                $cli->output( $result['value'] );
            $script->shutdown( empty( $result['ok'] ) ? 1 : 0 );
        }

        if ( $action === 'set' )
        {
            if ( $value === null )
            {
                $cli->error( 'velocity: set needs a value, '
                    . 'for example: config set ServerSettings Port 8088' );
                $script->shutdown( 1 );
            }
            $result = $config->set( $block, $variable, $value );
        }
        else if ( $action === 'unset' )
        {
            $result = $config->unsetVariable( $block, $variable );
        }
        else
        {
            $cli->error( "velocity: unknown config action '$action'. "
                . 'Try list, get, set, unset or paths.' );
            $script->shutdown( 1 );
        }

        if ( $asJson )
            $cli->output( json_encode( $result ) );
        else if ( empty( $result['ok'] ) )
            $cli->error( 'velocity: ' . $result['message'] );
        else
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) );
        $script->shutdown( empty( $result['ok'] ) ? 1 : 0 );
    }
    break;

    case 'ctl':
        // The engine's own control (qbixctl), with this installation's
        // configuration tree, site file and pid file filled in. Word forms
        // (configtest, layout) rather than -t / -S: single-dash flags are
        // this script's own options.
        $script->shutdown( $velocity->ctl( array_slice( $options['arguments'], 1 ) ) );
        break;

    case 'ssl':
    {
        if ( !$velocity->supports( 'ssl' ) )
        {
            $cli->error( 'velocity: ssl is for the qbix engine; the ' . $velocity->engineName()
                . ' engine takes its certificate from [HTTPSSettings] (graceful after replacing it)' );
            $script->shutdown( 1 );
        }
        // The engine's ssl:show and ssl:renew, with this installation's tree
        // filled in. A renewal is picked up by the running server within its
        // watch interval (a minute), with no restart.
        $action = isset( $options['arguments'][1] ) ? strtolower( trim( $options['arguments'][1] ) ) : 'show';
        if ( !in_array( $action, array( 'show', 'renew' ), true ) )
        {
            $cli->error( "Usage: ssl show|renew [host...]" );
            $script->shutdown( 1 );
        }
        $args = array_merge( array( 'ssl:' . $action ), array_slice( $options['arguments'], 2 ) );
        if ( $asJson && $action === 'show' )
            $args[] = '--json';
        $script->shutdown( $velocity->ctl( $args ) );
        break;
    }

    case 'cache':
    {
        $action = isset( $options['arguments'][1] ) ? strtolower( trim( $options['arguments'][1] ) ) : '';
        if ( $action !== 'clear' )
        {
            $cli->error( "Usage: cache clear" );
            $script->shutdown( 1 );
        }
        $result = $velocity->clearCache();
        if ( $asJson )
            $cli->output( json_encode( $result ) );
        elseif ( $result['ok'] )
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) );
        else
            $cli->error( 'velocity: ' . $result['message'] );
        $script->shutdown( $result['ok'] ? 0 : 1 );
    }
    break;

    case 'layout':
    {
        $action = isset( $options['arguments'][1] ) ? strtolower( trim( $options['arguments'][1] ) ) : 'show';
        if ( !$velocity->supports( 'layout' ) && $action !== 'migrate' )
        {
            // No configuration tree on this engine: the files it uses instead.
            $assets = $velocity->assets();
            if ( $asJson )
            {
                $cli->output( json_encode( array( 'ok' => true, 'data' => array( 'confDir' => null, 'assets' => $assets ) ) ) );
                $script->shutdown( 0 );
            }
            $cli->output( '  configuration : not used by the ' . $velocity->engineName() . ' engine (generated Caddyfile)' );
            $cli->output( '' );
            $cli->output( '  Files the server uses:' );
            foreach ( $assets as $name => $path )
                $cli->output( sprintf( '    %-16s %s', $name, $path === null ? '-' : $path ) );
            $script->shutdown( 0 );
        }
        if ( $action === 'migrate' )
        {
            $result = $velocity->migrateLayout();
            if ( $asJson )
                $cli->output( json_encode( $result ) );
            else
            {
                foreach ( (array)( $result['data']['actions'] ?? array() ) as $line )
                    $cli->output( '  ' . $line );
                $result['ok'] ? $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) )
                              : $cli->error( 'velocity: ' . $result['message'] );
            }
            $script->shutdown( $result['ok'] ? 0 : 1 );
        }
        $info = $velocity->layout()->describe();
        if ( $asJson )
        {
            $cli->output( json_encode( array( 'ok' => true, 'data' => $info ) ) );
            $script->shutdown( 0 );
        }
        $cli->output( '  configuration : ' . ( $info['confDir'] ?? 'disabled (single file)' ) );
        $cli->output( '  site          : ' . $info['site'] );
        $cli->output( '  metadata      : ' . $info['stateDir'] );
        foreach ( $info['pairs'] as $pair => $lists )
            $cli->output( sprintf( '  %-13s : %s', $pair, $lists['available']
                ? implode( ', ', array_map( function ( $f ) use ( $lists ) {
                      return in_array( $f, $lists['enabled'], true ) ? $f . ' (enabled)' : $f; }, $lists['available'] ) )
                : 'none' ) );
        $cli->output( '  envvars       : ' . ( $info['envvars'] ? implode( ', ', $info['envvars'] ) : 'none' ) );
        $cli->output( '  keep globals  : ' . implode( ', ', $velocity->keepGlobals() ) );
        $cli->output( '' );
        $cli->output( '  Files the server uses:' );
        foreach ( $info['assets'] as $name => $path )
            $cli->output( sprintf( '    %-16s %s', $name, $path === null ? '-' : $path ) );
        $script->shutdown( 0 );
    }
    break;

    case 'site':
    case 'conf':
    case 'mod':
    {
        $action = isset( $options['arguments'][1] ) ? strtolower( trim( $options['arguments'][1] ) ) : '';
        $name = isset( $options['arguments'][2] ) ? trim( $options['arguments'][2] ) : '';
        if ( !in_array( $action, array( 'enable', 'disable' ), true ) || $name === '' )
        {
            $cli->error( "Usage: $verb enable|disable <name>" );
            $script->shutdown( 1 );
        }
        if ( !$velocity->supports( 'layout' ) )
        {
            $cli->error( "velocity: $verb enable|disable works on the /etc/vc tree of the qbix engine; the "
                . $velocity->engineName() . ' engine takes own directives from [FrankenPHPSettings] SiteInclude' );
            $script->shutdown( 1 );
        }
        $result = $velocity->layout()->toggle( $verb, $name, $action === 'enable' );
        if ( $asJson )
            $cli->output( json_encode( $result ) );
        elseif ( $result['ok'] )
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) );
        else
            $cli->error( 'velocity: ' . $result['message'] );
        $script->shutdown( $result['ok'] ? 0 : 1 );
    }
    break;

    case 'install':
    {
        $installOptions = array(
            'force' => !empty( $options['force'] ),
            'from' => !empty( $options['from'] ) ? $options['from'] : null,
            'check' => !empty( $options['check'] ),
            'trustGithubDigest' => !empty( $options['trust-github-digest'] ),
        );
        if ( !$asJson )
            $installOptions['progress'] = function ( $line ) use ( $cli ) { $cli->output( '  ' . $line ); };
        $result = $velocity->install( $installOptions );
        if ( $asJson )
            $cli->output( json_encode( $result ) );
        elseif ( $result['ok'] )
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) );
        else
            $cli->error( 'velocity: ' . $result['message'] );
        $script->shutdown( $result['ok'] ? 0 : 1 );
    }
    break;

    case 'command':
        $line = implode( ' ', array_map( 'escapeshellarg', $velocity->command() ) );
        if ( $asJson )
            $cli->output( json_encode( array( 'ok' => true, 'command' => $velocity->command() ) ) );
        else
            $cli->output( $line );
        $script->shutdown( 0 );
        break;

    case 'status':
        $status = $velocity->status();
        if ( $asJson )
            $cli->output( json_encode( array( 'ok' => true, 'data' => $status ) ) );
        elseif ( trim( (string)( $options['engine'] ?? '' ) ) === '' )
        {
            // Without --engine: every engine, so that one running beside the
            // default -- left over from a test -- is seen and can be stopped.
            // The details of each that runs; the default's when none does.
            $overview = array();
            foreach ( expVelocity::engines() as $engineName )
            {
                $engine = $engineName === $velocity->engineName() ? $velocity : expVelocity::create( 'velocity.ini', $engineName );
                $overview[] = array( $engine, $engine === $velocity ? $status : $engine->status() );
            }
            velocityPrintOverview( $cli, $overview );
            $shown = 0;
            foreach ( $overview as $engine )
                if ( $engine[1]['running'] )
                {
                    velocityPrintStatus( $cli, $engine[1], $engine[0] );
                    ++$shown;
                }
            if ( !$shown )
                velocityPrintStatus( $cli, $status, $velocity );
            velocityPrintAdmin( $cli, $overview );
            $cli->output( '' );
        }
        else
        {
            velocityPrintStatus( $cli, $status, $velocity );
            velocityPrintAdmin( $cli, array( array( $velocity, $status ) ) );
            $cli->output( '' );
        }
        // The default engine's state, as before: what monitoring checks.
        $script->shutdown( $status['running'] ? 0 : 1 );
        break;

    default:
        $result = $velocity->$verb();

        if ( $asJson )
        {
            $cli->output( json_encode( $result ) );
            $script->shutdown( $result['ok'] ? 0 : 1 );
            break;
        }

        if ( $result['ok'] )
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . velocityShortPaths( $result['message'] ) ) );
        else
            $cli->error( 'velocity: ' . velocityShortPaths( $result['message'] ) );

        $status = $velocity->status();
        velocityPrintStatus( $cli, $status, $velocity );
        velocityPrintAdmin( $cli, array( array( $velocity, $status ) ) );
        $cli->output( '' );
        $script->shutdown( $result['ok'] ? 0 : 1 );
}
