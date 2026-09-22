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
    "  command    print the command line it would run, and exit\n\n" .
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

$verbs = array( 'start', 'stop', 'graceful', 'restart', 'kill', 'status', 'command' );
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

switch ( $verb )
{
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
