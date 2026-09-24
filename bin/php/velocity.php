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
    "             ctl status | ctl configtest | ctl layout | ctl ensite NAME ...\n\n" .
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

$options = $script->getOptions( '[json][keep-global:]', '[command]',
    array( 'json' => 'Report as JSON, for a caller that is not a person',
           'keep-global' => 'More globals to keep between requests (comma-separated), appended to the '
                          . 'built-in defaults and velocity.ini KeepGlobals[]; for start, restart and command' ) );
$script->initialize();

$verbs = array( 'start', 'stop', 'graceful', 'restart', 'kill', 'status',
                'command', 'config', 'cache', 'layout', 'site', 'conf', 'mod', 'ctl' );
$verb = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';

if ( !in_array( $verb, $verbs, true ) )
{
    $cli->error( "Unknown command '$verb'. Try one of: " . implode( ', ', $verbs ) );
    $script->shutdown( 1 );
}

$velocity = new expVelocity();
$asJson = !empty( $options['json'] );
if ( !empty( $options['keep-global'] ) )
    $velocity->appendKeepGlobals( $options['keep-global'] );

/**
 * Render a status array for a person to read.
 */
function velocityPrintStatus( eZCLI $cli, array $status )
{
    $cli->output( '  running    : ' . ( $status['running'] ? 'yes' : 'no' ) );
    if ( $status['running'] )
    {
        $cli->output( '  processes  : ' . $status['processes']
                      . ( $status['parent'] ? ' (parent ' . $status['parent'] . ')' : '' ) );
        $cli->output( '  listening  : ' . ( $status['listening']
                      ? implode( ', ', $status['listening'] ) : 'nothing yet' ) );
    }
    $cli->output( '  https      : ' . ( $status['https'] ? 'enabled' : 'disabled' ) );
    $cli->output( '  log        : ' . $status['log'] );
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
        else
            velocityPrintStatus( $cli, $status );
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
            $cli->output( $cli->stylize( 'emphasize', 'velocity: ' . $result['message'] ) );
        else
            $cli->error( 'velocity: ' . $result['message'] );

        $status = $velocity->status();
        velocityPrintStatus( $cli, $status );
        $script->shutdown( $result['ok'] ? 0 : 1 );
}
