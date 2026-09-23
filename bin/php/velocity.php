<?php
/**
 * File containing the velocity control script.
 *
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
    "  config     read and write the settings it runs on\n\n" .
    "Configuration:\n" .
    "  config list [Block]                  every setting, and which are overridden\n" .
    "  config get <Block> <Variable>        one value\n" .
    "  config set <Block> <Variable> <v>    write it to this installation\n" .
    "  config unset <Block> <Variable>      return it to the packaged value\n" .
    "  config paths                         which file is which\n\n" .
    "Through the console:\n" .
    "  ./bin/php/console exp:velocity status --allow-root-user\n" .
    "  ./bin/php/console exp:velocity restart --allow-root-user\n\n" .
    "Directly:\n" .
    "  php bin/php/velocity.php start --allow-root-user\n" .
    "  php bin/php/velocity.php status --json --allow-root-user\n\n" .
    "Settings come from velocity.ini; override per installation in\n" .
    "settings/override/velocity.ini.append.php." ),
    'use-session' => false,
    'use-modules' => false,
    'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( '[json]', '[command]',
    array( 'json' => 'Report as JSON, for a caller that is not a person' ) );
$script->initialize();

$verbs = array( 'start', 'stop', 'graceful', 'restart', 'kill', 'status',
                'command', 'config' );
$verb = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';

if ( !in_array( $verb, $verbs, true ) )
{
    $cli->error( "Unknown command '$verb'. Try one of: " . implode( ', ', $verbs ) );
    $script->shutdown( 1 );
}

$velocity = new expVelocity();
$asJson = !empty( $options['json'] );

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
