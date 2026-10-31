<?php
/**
 * @description The webhooks of the GraphQL API: list them, add or change one (with a new signing secret), remove one, send a test, deliver now, read the delivery log; a dry run unless --apply
 * @alias exp:graphql:webhooks
 *
 * File containing the exp:graphql:webhooks command (bin/php/expgraphqlwebhooks.php); guide: doc/eng-us/features/6.0/graphql.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expgraphqlwebhooks extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_ERRORS = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $this->script( array( 'description' => "The webhooks of the GraphQL API\n" .
                                               "Every event of the GraphQL event journal an end point asks for (publish, update, trash, delete, move,\n" .
                                               "hide, reveal, mutation) is POSTed to it as JSON, signed with HMAC-SHA-256 (X-Exponential-Signature,\n" .
                                               "X-Exponential-Timestamp), by the cronjob part graphqlwebhooks, with retries. The end points are\n" .
                                               "settings (graphql.ini [GraphQLWebhooks] EndpointList[] and a block [GraphQLWebhook_<name>] each,\n" .
                                               "written to settings/override); the signing secret is kept in the secret store (Setup > Secrets)\n" .
                                               "and shown once, when --add makes it. Nothing is written or sent without --apply.\n" .
                                               "\n" .
                                               "./console exp:graphql:webhooks                                    the list\n" .
                                               "./console exp:graphql:webhooks --add=shop --url=https://shop.example.com/hooks/exponential \\\n" .
                                               "      --events=publish,update,trash --classes=article --siteaccess=eng --apply\n" .
                                               "./console exp:graphql:webhooks --add=search --url=https://search.example.com/hook --payload=query \\\n" .
                                               "      --query=article_for_index --as=indexer --apply\n" .
                                               "./console exp:graphql:webhooks --test=shop --apply                 send a test event now\n" .
                                               "./console exp:graphql:webhooks --deliver --apply                   what the cronjob part does, now\n" .
                                               "./console exp:graphql:webhooks --log --limit=50\n" .
                                               "./console exp:graphql:webhooks --remove=shop --apply\n" .
                                               "\n" .
                                               "Exit code: 0 done (or shown); 1 a file could not be written, a secret not stored, or the test was\n" .
                                               "not answered with 2xx; 2 wrong options (an unknown webhook, a URL or an event that is not one).",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[list][add:][remove:][test:][deliver][log][url:][events:][siteaccess:][classes:][subtree:][payload:][query:][as:][secret-name:][limit:][format:][apply]', '',
                                   array( 'list' => 'list the webhooks (the default)',
                                          'add' => 'add the webhook of this name, or change it (lower case letters, digits and _)',
                                          'remove' => 'remove the webhook of this name (and its signing secret)',
                                          'test' => 'send a test event (ping) to the webhook of this name',
                                          'deliver' => 'read the journal and send the deliveries that are due, as the cronjob part does',
                                          'log' => 'the last lines of the delivery log',
                                          'url' => 'the end point (--add): http:// or https://',
                                          'events' => 'the events it wants (--add), comma separated: publish, update, trash, delete, move, hide, reveal, mutation; none: every event',
                                          'siteaccess' => 'only content of these siteaccesses (their root node and languages), comma separated',
                                          'classes' => 'only these class identifiers, comma separated',
                                          'subtree' => 'only content at or below these node ids, comma separated',
                                          'payload' => 'event (the default): the event and its ids; query: the answer of a persisted query',
                                          'query' => 'the name or SHA-256 of the persisted query (--payload=query)',
                                          'as' => 'the user the persisted query runs as (a login; default: anonymous)',
                                          'secret-name' => 'the name of the signing secret in the secret store, or env:VARIABLE (default graphql.webhook.<name>)',
                                          'limit' => 'lines of the delivery log (--log; default 20)',
                                          'format' => 'text (the default) or json (the list, --log)',
                                          'apply' => 'write and send; without it nothing is changed' ) );
        $format = isset( $options['format'] ) && $options['format'] ? (string)$options['format'] : 'text';
        if ( $format !== 'text' && $format !== 'json' )
            $this->usage( "--format must be text or json: $format" );
        $apply = !empty( $options['apply'] );
        $out = $this->cli();
        $webhooks = \expGraphQLWebhooks::fromSettings();
        if ( \expGraphQL::setting( 'Enabled', 'disabled' ) !== 'enabled' )
            $out->warning( 'WARN the GraphQL API is switched off: no events are written and nothing is sent (./console exp:graphql:enable)' );

        if ( isset( $options['add'] ) && $options['add'] !== false && $options['add'] !== '' )
            $this->add( (string)$options['add'], $options, $apply );
        if ( isset( $options['remove'] ) && $options['remove'] !== false && $options['remove'] !== '' )
            $this->remove( $webhooks, (string)$options['remove'], $apply );
        if ( isset( $options['test'] ) && $options['test'] !== false && $options['test'] !== '' )
            $this->test( $webhooks, (string)$options['test'], $apply );
        if ( !empty( $options['deliver'] ) )
            $this->deliver( $webhooks, $apply );
        if ( !empty( $options['log'] ) )
        {
            $limit = isset( $options['limit'] ) && ctype_digit( (string)$options['limit'] ) ? max( 1, (int)$options['limit'] ) : 20;
            $lines = $webhooks->recent( $limit );
            if ( $format === 'json' )
                $out->output( json_encode( array( 'schema' => \expGraphQLWebhooks::SCHEMA, 'deliveries' => $lines ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
            {
                foreach ( $lines as $line )
                    $out->output( sprintf( '%s  %-16s %-9s %-8s try %d  %3s %5d ms  %s', date( 'Y-m-d H:i:s', (int)$line['time'] ), $line['endpoint'], $line['event'],
                                           $line['result'], (int)$line['attempt'], $line['status'] ? (string)$line['status'] : '-', (int)$line['ms'],
                                           isset( $line['error'] ) ? (string)$line['error'] : '' ) );
                $out->output( 'PASS ' . count( $lines ) . ' lines of the delivery log' );
            }
            $this->shutdown( self::EXIT_OK );
        }

        $stats = $webhooks->stats();
        $list = array();
        foreach ( $webhooks->all() as $name => $endpoint )
        {
            $secret = $endpoint['secret'];
            $list[] = array( 'name' => $name, 'url' => $endpoint['url'], 'events' => $endpoint['events'], 'siteAccesses' => $endpoint['siteAccesses'],
                             'classes' => $endpoint['classes'], 'subtrees' => $endpoint['subtrees'], 'payload' => $endpoint['payload'],
                             'query' => $endpoint['query'], 'secret' => $secret, 'secretSet' => class_exists( '\expSecrets' ) && \expSecrets::has( $secret ),
                             'enabled' => $endpoint['enabled'], 'problem' => $endpoint['problem'],
                             'waiting' => isset( $stats[$name] ) ? $stats[$name]['waiting'] : 0, 'failed' => isset( $stats[$name] ) ? $stats[$name]['failed'] : 0,
                             'last' => isset( $stats[$name] ) ? $stats[$name]['last'] : null );
        }
        if ( $format === 'json' )
            $out->output( json_encode( array( 'schema' => \expGraphQLWebhooks::SCHEMA, 'webhooks' => $list ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        else
        {
            foreach ( $list as $w )
            {
                $out->output( sprintf( '%-16s %-8s %s', $w['name'], $w['problem'] !== '' ? 'BROKEN' : ( $w['enabled'] ? 'on' : 'off' ), $w['url'] ) );
                $out->output( '                 events ' . ( $w['events'] ? implode( ',', $w['events'] ) : 'all' )
                              . ( $w['siteAccesses'] ? '; siteaccesses ' . implode( ',', $w['siteAccesses'] ) : '' )
                              . ( $w['classes'] ? '; classes ' . implode( ',', $w['classes'] ) : '' )
                              . ( $w['subtrees'] ? '; subtrees ' . implode( ',', $w['subtrees'] ) : '' )
                              . '; payload ' . $w['payload'] . ( $w['payload'] === 'query' ? ' ' . $w['query'] : '' ) );
                $out->output( '                 secret ' . $w['secret'] . ( $w['secretSet'] ? ' (set)' : ' (NOT SET)' ) . '; ' . $w['waiting'] . ' waiting, '
                              . $w['failed'] . ' given up' . ( $w['last'] ? '; last ' . $w['last']['result'] . ' ' . date( 'Y-m-d H:i', (int)$w['last']['time'] ) : '' ) );
                if ( $w['problem'] !== '' )
                    $out->output( '                 problem: ' . $w['problem'] );
            }
            $out->output( 'PASS ' . count( $list ) . ' webhooks' );
        }
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * --add: the settings of the end point and, for a new one or a secret not set yet, a new signing secret.
     */
    protected function add( $name, array $options, $apply )
    {
        $out = $this->cli();
        if ( !preg_match( \expGraphQLWebhooks::NAME_PATTERN, $name ) )
            $this->usage( "--add: the name must be lower case letters, digits and _, starting with a letter: $name" );
        if ( empty( $options['url'] ) )
            $this->usage( '--add needs --url' );
        $settings = array( 'URL' => (string)$options['url'], 'Events' => Expgraphqlenable::listOption( $options, 'events' ),
                           'SiteAccessList' => Expgraphqlenable::listOption( $options, 'siteaccess' ), 'ClassList' => Expgraphqlenable::listOption( $options, 'classes' ),
                           'SubtreeList' => Expgraphqlenable::listOption( $options, 'subtree' ),
                           'Secret' => isset( $options['secret-name'] ) && $options['secret-name'] ? (string)$options['secret-name'] : '',
                           'Payload' => isset( $options['payload'] ) && $options['payload'] ? (string)$options['payload'] : 'event',
                           'Query' => isset( $options['query'] ) && $options['query'] ? (string)$options['query'] : '',
                           'QueryUser' => isset( $options['as'] ) && $options['as'] ? (string)$options['as'] : '' );
        $known = \expIniEditor::knownSiteAccesses();
        foreach ( $settings['SiteAccessList'] as $siteAccess )
            if ( !in_array( $siteAccess, $known, true ) )
                $this->usage( "--siteaccess: there is no siteaccess $siteAccess" );
        foreach ( $settings['SubtreeList'] as $node )
            if ( !ctype_digit( (string)$node ) )
                $this->usage( "--subtree: not a node id: $node" );
        if ( $settings['QueryUser'] !== '' && !\eZUser::fetchByName( $settings['QueryUser'] ) instanceof \eZUser )
            $this->usage( '--as: no user with the login ' . $settings['QueryUser'] );
        try
        {
            $changes = \expGraphQLWebhooks::addChanges( $name, $settings );
        }
        catch ( \expGraphQLError $e )
        {
            $this->usage( '--add: ' . $e->getMessage() );
        }
        $endpoint = \expGraphQLWebhooks::endpoint( $name, $settings );
        $env = strpos( $endpoint['secret'], 'env:' ) === 0;
        $needsSecret = !$env && !\expSecrets::has( $endpoint['secret'] );
        $result = \expGraphQLSetup::apply( $changes, !$apply );
        $failed = Expgraphqlenable::report( $out, $result, $apply );
        if ( !$apply )
        {
            if ( $needsSecret )
                $out->output( 'DRY RUN would make a signing secret and keep it in the secret store as ' . $endpoint['secret'] );
            $out->output( 'DRY RUN nothing written; run again with --apply' );
            $this->shutdown( self::EXIT_OK );
        }
        if ( $failed )
            $this->shutdown( self::EXIT_ERRORS );
        \expGraphQLWebhooks::fromSettings()->startCursor();
        if ( $env )
            $out->output( 'PASS the signing secret is read from the environment variable ' . substr( $endpoint['secret'], 4 ) );
        else if ( !$needsSecret )
            $out->output( 'PASS the signing secret ' . $endpoint['secret'] . ' is kept as it is (Setup > Secrets replaces it)' );
        else
        {
            $secret = \expGraphQLWebhooks::newSecret();
            if ( !\expGraphQLWebhooks::storeSecret( $endpoint, $secret ) )
            {
                $out->error( 'FAIL the signing secret could not be stored (the secret store: Setup > Secrets, the sodium extension)' );
                $this->shutdown( self::EXIT_ERRORS );
            }
            $out->output( 'PASS the signing secret, kept in the secret store as ' . $endpoint['secret'] . " and shown this once:\n\n    " . $secret
                          . "\n\nThe receiver checks X-Exponential-Signature: sha256=<hex HMAC-SHA-256(secret, X-Exponential-Timestamp + \".\" + body)>." );
        }
        $out->output( 'PASS the webhook ' . $name . ' gets what happens from now on; ./console exp:graphql:webhooks --test=' . $name . ' --apply sends a test' );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * --remove: its settings, and its secret when it is the default one in the store.
     */
    protected function remove( \expGraphQLWebhooks $webhooks, $name, $apply )
    {
        $out = $this->cli();
        $all = $webhooks->all();
        if ( !isset( $all[$name] ) )
            $this->usage( "--remove: no webhook $name" );
        $result = \expGraphQLSetup::apply( \expGraphQLWebhooks::removeChanges( $name ), !$apply );
        $failed = Expgraphqlenable::report( $out, $result, $apply );
        $secret = $all[$name]['secret'];
        $ownSecret = $secret === \expGraphQLWebhooks::defaultSecretName( $name ) && \expSecrets::has( $secret );
        if ( !$apply )
        {
            if ( $ownSecret )
                $out->output( 'DRY RUN would remove the signing secret ' . $secret . ' from the secret store' );
            $out->output( 'DRY RUN nothing written; run again with --apply' );
            $this->shutdown( self::EXIT_OK );
        }
        if ( $failed )
            $this->shutdown( self::EXIT_ERRORS );
        if ( $ownSecret && \expSecrets::remove( $secret ) )
            $out->output( 'PASS removed the signing secret ' . $secret );
        $out->output( "PASS the webhook $name is removed; deliveries still waiting for it are given up at the next run" );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * --test: the request of a test event, shown (dry run) or sent now.
     */
    protected function test( \expGraphQLWebhooks $webhooks, $name, $apply )
    {
        $out = $this->cli();
        $all = $webhooks->all();
        if ( !isset( $all[$name] ) )
            $this->usage( "--test: no webhook $name" );
        if ( $all[$name]['problem'] !== '' )
            $this->usage( "--test: the webhook $name cannot be used: " . $all[$name]['problem'] );
        if ( !$apply )
        {
            list( $body, $headers ) = $webhooks->request( $all[$name], \expGraphQLWebhooks::pingEvent(), 'test', 1, 'not-the-secret' );
            $out->output( 'DRY RUN would POST to ' . $all[$name]['url'] );
            foreach ( \expGraphQLWebhooks::maskSignature( $headers ) as $header )
                $out->output( '    ' . $header );
            $out->output( '    ' );
            $out->output( '    ' . $body );
            $out->output( 'DRY RUN nothing sent; run again with --apply' );
            $this->shutdown( self::EXIT_OK );
        }
        $result = $webhooks->sendTest( $name );
        if ( $result['error'] !== null )
        {
            $out->error( 'FAIL ' . $all[$name]['url'] . ': ' . $result['error'] . ' (' . $result['ms'] . ' ms)' );
            $this->shutdown( self::EXIT_ERRORS );
        }
        $out->output( 'PASS ' . $all[$name]['url'] . ' answered ' . $result['status'] . ' in ' . $result['ms'] . ' ms' );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * --deliver: the cronjob part's work, now.
     */
    protected function deliver( \expGraphQLWebhooks $webhooks, $apply )
    {
        $out = $this->cli();
        if ( !$apply )
        {
            $out->output( 'DRY RUN ' . count( $webhooks->outbox() ) . ' deliveries in the outbox; the journal is read from '
                          . ( $webhooks->cursor() !== null ? $webhooks->cursor() : 'its end (first run)' ) . '; run again with --apply to send' );
            $this->shutdown( self::EXIT_OK );
        }
        $dispatched = $webhooks->dispatch();
        $delivered = $webhooks->deliver();
        $out->output( sprintf( 'PASS %d events read, %d deliveries queued; %d sent, %d to try again, %d given up, %d waiting%s',
                               $dispatched['events'], $dispatched['queued'], $delivered['sent'], $delivered['failed'], $delivered['gaveUp'],
                               $delivered['waiting'], $delivered['busy'] ? ' (another run is sending)' : '' ) );
        $this->shutdown( $delivered['failed'] || $delivered['gaveUp'] ? self::EXIT_ERRORS : self::EXIT_OK );
    }

    /**
     * @param string $message
     */
    protected function usage( $message )
    {
        $this->cli()->error( "FAIL $message" );
        $this->shutdown( self::EXIT_USAGE );
    }
}

}
?>
