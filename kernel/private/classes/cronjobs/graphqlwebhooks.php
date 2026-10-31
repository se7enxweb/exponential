<?php
/**
 * The cronjob part graphqlwebhooks (cronjobs/graphqlwebhooks.php; [CronjobPart-graphqlwebhooks] and the frequent group
 * in cronjob.ini; doc/eng-us/features/6.0/graphql.md, "Webhooks"): reads the GraphQL event journal from where the last
 * run stopped, puts a delivery in the outbox for every end point that wants an event, and sends the deliveries that
 * are due (expGraphQLWebhooks). Does nothing while no end point is configured or the API is switched off.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Graphqlwebhooks extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        $quiet = !empty( $scope['isQuiet'] );
        if ( !class_exists( '\expGraphQLWebhooks' ) || \expGraphQL::setting( 'Enabled', 'disabled' ) !== 'enabled' || !\expGraphQLWebhooks::configuredNames() )
            return 0;
        $webhooks = \expGraphQLWebhooks::fromSettings();
        $dispatched = $webhooks->dispatch();
        $delivered = $webhooks->deliver();
        if ( !$quiet )
        {
            $cli->output( sprintf( 'GraphQL webhooks: %d events read, %d deliveries queued; %d sent, %d to try again, %d given up, %d waiting%s',
                                   $dispatched['events'], $dispatched['queued'], $delivered['sent'], $delivered['failed'], $delivered['gaveUp'],
                                   $delivered['waiting'], $delivered['busy'] ? ' (another run is sending)' : '' ) );
            if ( $dispatched['lost'] )
                $cli->warning( 'GraphQL webhooks: the journal had moved on past the last run; the events in between are not sent' );
        }
        return $delivered['sent'];
    }
}

}
?>
