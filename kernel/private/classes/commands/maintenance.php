<?php
/**
 * The code of bin/php/maintenance.php, moved into a class (#207 stage 1). The file bin/php/maintenance.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\Command\Kernel
{

class Maintenance extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'action', 'cli', 'm', 'options', 'root', 'script', 'state', 't', 'u', 'until' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => "Maintenance mode: take the site offline and back\n\n" .
                                                               "  maintenance.php on|off|status [options]",
                                             'use-session' => false,
                                             'use-modules' => false,
                                             'use-extensions' => true ) );
        $script->startup();
        $options = $script->getOptions( '[message:][until:][allow-ip:][allow-admin]', '[action]',
                                        array( 'message' => 'What the page says, instead of the default text',
                                               'until' => 'When the site is expected back: 30m, 2h, or a date and time',
                                               'allow-ip' => 'Addresses that still see the site, comma separated',
                                               'allow-admin' => 'Keep the administration (/admin) reachable' ) );
        $script->initialize();

        $root = getcwd();
        $action = isset( $options['arguments'][0] ) ? $options['arguments'][0] : 'status';

        switch ( $action )
        {
            case 'on':
            {
                $until = 0;
                if ( $options['until'] )
                {
                    $u = trim( (string)$options['until'] );
                    if ( preg_match( '/^(\d+)\s*([mh])$/i', $u, $m ) )
                        $until = time() + (int)$m[1] * ( strtolower( $m[2] ) === 'h' ? 3600 : 60 );
                    elseif ( ( $t = strtotime( $u ) ) !== false )
                        $until = $t;
                    else
                    {
                        $cli->error( "--until: \"$u\" is not a time (30m, 2h, or a date and time)" );
                        $script->shutdown( 1 );
                    }
                }
                $state = array(
                    'reason' => 'manual',
                    'message' => (string)$options['message'],
                    'until' => $until,
                    'allow_ips' => $options['allow-ip'] ? array_values( array_filter( array_map( 'trim', explode( ',', $options['allow-ip'] ) ) ) ) : array(),
                    'allow_paths' => $options['allow-admin'] ? array( '/admin' ) : array(),
                );
                if ( !\expMaintenance::enable( $root, $state ) )
                {
                    $cli->error( 'Could not write ' . \expMaintenance::MARKER . ': is var/ writable by this user?' );
                    $script->shutdown( 1 );
                }
                $cli->output( 'Maintenance is ON: page requests get the maintenance page (503).' );
                break;
            }
            case 'off':
            {
                if ( !\expMaintenance::state( $root ) )
                    $cli->output( 'Maintenance was not on.' );
                elseif ( \expMaintenance::disable( $root ) )
                {
                    // Pages stored while the site was offline are the maintenance page
                    // at most (never stored: no-store), but a change made during the
                    // window must be seen at once
                    \expMaintenance::clearResponseCaches( $root );
                    $cli->output( 'Maintenance is OFF: the site answers again.' );
                }
                else
                {
                    $cli->error( 'Could not remove ' . \expMaintenance::MARKER );
                    $script->shutdown( 1 );
                }
                break;
            }
            case 'status':
            {
                $state = \expMaintenance::state( $root );
                if ( !$state )
                {
                    $cli->output( 'Maintenance is off.' );
                    break;
                }
                $cli->output( 'Maintenance is ON (' . ( $state['reason'] ?? 'unknown' ) . ( isset( $state['run'] ) ? ', setup run ' . $state['run'] : '' ) . ')' );
                if ( !empty( $state['since'] ) )
                    $cli->output( '  since    ' . date( 'Y-m-d H:i:s', (int)$state['since'] ) );
                if ( !empty( $state['until'] ) )
                    $cli->output( '  until    ' . date( 'Y-m-d H:i:s', (int)$state['until'] ) );
                if ( !empty( $state['message'] ) )
                    $cli->output( '  message  ' . $state['message'] );
                if ( !empty( $state['allow_ips'] ) )
                    $cli->output( '  allowed  ' . implode( ', ', (array)$state['allow_ips'] ) );
                if ( !empty( $state['allow_paths'] ) )
                    $cli->output( '  open     ' . implode( ', ', (array)$state['allow_paths'] ) );
                $cli->output( '  page     ' . ( !empty( $state['page'] ) ? $state['page'] : \expMaintenance::DEFAULT_PAGE ) );
                break;
            }
            default:
                $cli->error( "Unknown action \"$action\": on, off or status" );
                $script->shutdown( 1 );
        }

        $script->shutdown();
    }
}

}
