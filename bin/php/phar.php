#!/usr/bin/env php
<?php
/**
 * File containing the engine phar command.
 *
 * Discovered by the console as exp:phar.
 *
 *   php -d phar.readonly=0 bin/php/console exp:phar build
 *   bin/php/console exp:phar info
 *   bin/php/console exp:phar clean
 *
 * Building needs phar.readonly off, which is an ini setting and not something
 * this script can change for itself; the build verb says so rather than
 * failing obscurely.
 *
 * To run the installation from the archive, set EXP_ENGINE_PHAR in the
 * environment of whatever serves it. The autoloader reads kernel and library
 * classes from the archive when it is set and from disk when it is not, and
 * nothing else about the installation changes either way.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license   For full copyright and license information view LICENSE file.
 * @package   kernel
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array(
    'description' => "Exponential engine phar\n\nBuild and inspect the engine archive.",
    'use-session' => false,
    'use-modules' => false,
    'use-extensions' => false,
) );
$script->startup();

$options = $script->getOptions(
    '[json][output:]',
    '[verb]',
    array(
        'json'   => 'Print the result as JSON.',
        'output' => 'Write the archive somewhere other than dist/engine.phar.',
        'verb'   => 'build, info or clean (default: info)',
    ) );
$script->initialize();

require_once 'kernel/classes/expphar.php';

$verb = isset( $options['arguments'][0] ) ? $options['arguments'][0] : 'info';
$json = !empty( $options['json'] );

// Building needs phar.readonly off, and the console dispatches each command to
// a fresh PHP, so an ini flag typed on the console line never reaches here.
// Re-exec once with the setting rather than telling the caller to work that
// out. The guard variable stops a loop if the setting somehow does not take.
if ( $verb === 'build' && ini_get( 'phar.readonly' ) && getenv( 'EXP_PHAR_REEXEC' ) !== '1' )
{
    $command = escapeshellarg( PHP_BINARY ) . ' -d phar.readonly=0 '
             . escapeshellarg( __FILE__ ) . ' build --allow-root-user';
    if ( isset( $options['output'] ) && $options['output'] !== '' )
        $command .= ' --output=' . escapeshellarg( $options['output'] );
    if ( $json )
        $command .= ' --json';

    putenv( 'EXP_PHAR_REEXEC=1' );
    passthru( $command, $exit );
    $script->shutdown( $exit );
    return;
}

switch ( $verb )
{
    case 'build':
        $result = expPhar::build( array( 'output' => isset( $options['output'] ) ? $options['output'] : '' ) );
        break;
    case 'clean':
        $result = expPhar::clean();
        break;
    case 'info':
        $result = expPhar::info();
        break;
    default:
        $result = array( 'ok' => false, 'message' => "unknown verb '$verb'; use build, info or clean", 'data' => array() );
}

if ( $json )
{
    $cli->output( json_encode( $result ) );
}
else
{
    $cli->output( ( $result['ok'] ? '  ' : '  ERROR: ' ) . $result['message'] );
    foreach ( $result['data'] as $k => $v )
    {
        if ( is_array( $v ) )
            $v = implode( ', ', $v );
        elseif ( is_bool( $v ) )
            $v = $v ? 'yes' : 'no';
        $cli->output( sprintf( '    %-10s %s', $k, $v ) );
    }
}

$script->shutdown( $result['ok'] ? 0 : 1 );
