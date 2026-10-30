<?php
/**
 * @description List the subtree notification subscriptions per user and subtree, or remove those whose content is gone (dry run unless --apply)
 * @alias exp:notification:subscriptions
 * @alias exp:notify:subscriptions
 *
 * File containing the exp:notification:subscriptions command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Notificationsubscriptions extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Notification subscriptions\n" .
                                 "  list                      the subtree subscriptions: user, node, path, class, last change below the node\n" .
                                 "  remove-missing            remove the subscriptions whose node no longer exists (lists them; --apply removes)\n" .
                                 "\n" .
                                 "remove-missing without --apply removes nothing: it lists what it would remove. --dry-run is still\n" .
                                 "accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).\n" .
                                 "\n" .
                                 "./console exp:notification:subscriptions list --user=editor --limit=20\n" .
                                 "./console exp:notification:subscriptions list --missing\n" .
                                 "./console exp:notification:subscriptions remove-missing\n" .
                                 "./console exp:notification:subscriptions remove-missing --apply",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            '[user:][q:][class:][missing][limit:][offset:][addresses][apply][dry-run][json]', '',
            array( 'user'      => 'only this user: a login or the user\'s content object id',
                   'q'         => 'only nodes whose name contains this',
                   'class'     => 'only nodes of this class identifier',
                   'missing'   => 'only subscriptions whose node is gone',
                   'limit'     => 'how many (default 50)',
                   'offset'    => 'skip this many',
                   'addresses' => 'also show the user\'s e-mail address (hidden by default)',
                   'apply'     => 'remove-missing: remove the subscriptions (the default lists them and removes nothing)',
                   'dry-run'   => 'remove-missing: the default, kept for scripts that pass it: list, remove nothing',
                   'json'      => 'list: JSON' ) );
        $apply = self::applyRequested( $options );
        if ( $apply === null )
        {
            $cli->error( 'FAIL: --apply and --dry-run exclude each other; without --apply the script changes nothing' );
            $this->shutdown( 2 );
            return;
        }
        $dryRun = !$apply;
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $args ? strtolower( (string)$args[0] ) : 'list';

        $userID = null;
        if ( isset( $options['user'] ) && $options['user'] !== '' && $options['user'] !== null && $options['user'] !== false )
        {
            if ( ctype_digit( (string)$options['user'] ) )
                $userID = (int)$options['user'];
            else
            {
                $user = \eZUser::fetchByName( $options['user'] );
                if ( !$user )
                {
                    $cli->error( 'FAIL: no user with the login ' . $options['user'] );
                    $this->shutdown( 1 );
                    return;
                }
                $userID = (int)$user->attribute( 'contentobject_id' );
            }
        }
        $filter = array( 'q' => isset( $options['q'] ) ? $options['q'] : '', 'class' => isset( $options['class'] ) ? $options['class'] : '',
                         'missing' => !empty( $options['missing'] ) );

        if ( $action === 'list' )
        {
            $limit = !empty( $options['limit'] ) ? (int)$options['limit'] : 50;
            $data = \expNotificationService::subscriptions( $userID, $filter, !empty( $options['offset'] ) ? (int)$options['offset'] : 0, $limit );
            if ( !empty( $options['addresses'] ) )
                foreach ( $data['rows'] as &$row )
                {
                    $u = \eZUser::fetch( $row['user_id'] );
                    $row['address'] = $u ? $u->attribute( 'email' ) : '';
                }
            unset( $row );
            if ( !empty( $options['json'] ) )
            {
                $cli->output( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
                $this->shutdown( 0 );
                return;
            }
            foreach ( $data['rows'] as $row )
                $cli->output( sprintf( '#%-5d %-16s node %-6d %-32s %-14s %s%s%s', $row['id'], $row['login'] !== '' ? $row['login'] : 'user ' . $row['user_id'],
                                       $row['node_id'], $row['missing'] ? '(content is gone)' : $row['name'], $row['class_identifier'],
                                       $row['last_change'] ? 'changed ' . date( 'Y-m-d', $row['last_change'] ) : '',
                                       $row['use_digest'] ? '  digest' : '',
                                       isset( $row['address'] ) ? '  ' . $row['address'] : '' ) );
            $cli->output( sprintf( 'PASS: %d of %d subscription(s) shown', count( $data['rows'] ), $data['total'] ) );
            $this->shutdown( 0 );
            return;
        }

        if ( $action === 'remove-missing' )
        {
            $data = \expNotificationService::subscriptions( $userID, array( 'missing' => true ), 0, 100000 );
            foreach ( $data['rows'] as $row )
            {
                $cli->output( sprintf( '%s #%d (user %d, node %d)', $dryRun ? 'would remove' : 'removing', $row['id'], $row['user_id'], $row['node_id'] ) );
                if ( !$dryRun )
                    \eZPersistentObject::removeObject( \eZSubtreeNotificationRule::definition(), array( 'id' => $row['id'] ) );
            }
            if ( $dryRun )
            {
                $cli->output( sprintf( 'PASS: %d subscription(s) would be removed (dry run, nothing changed; --apply writes)', count( $data['rows'] ) ) );
                $this->shutdown( 0 );
                return;
            }
            // Check what was stored: no subscription of a missing node is left.
            $left = \expNotificationService::subscriptions( $userID, array( 'missing' => true ), 0, 100000 );
            if ( $left['total'] > 0 )
            {
                $cli->error( sprintf( 'FAIL: %d subscription(s) of missing nodes still there', $left['total'] ) );
                $this->shutdown( 1 );
                return;
            }
            $cli->output( sprintf( 'PASS: %d subscription(s) removed', count( $data['rows'] ) ) );
            $this->shutdown( 0 );
            return;
        }

        $cli->error( 'FAIL: the actions are list and remove-missing' );
        $this->shutdown( 2 );
    }
}

}
