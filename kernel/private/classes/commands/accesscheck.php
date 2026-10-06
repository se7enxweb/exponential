<?php
/**
 * @description Check what a user may do with a node or object (content/read, edit, ...) and which limitation refuses it
 * @alias exp:access:check
 *
 * File containing the exp:access:check command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Accesscheck extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Access check for a user\n" .
                                 "Whether a user may read, edit, remove ... a node or an object, decided as the views decide\n" .
                                 "it, and when not, the policies and the limitation that refused, those of extensions\n" .
                                 "included. Nobody signs in and nothing is changed.\n" .
                                 "\n" .
                                 "./console exp:access:check --user=editor --node=2\n" .
                                 "./console exp:access:check --user=14 --object=57 --function=edit --language=eng-GB\n" .
                                 "./console exp:access:check --user=anonymous --node=2 --json\n" .
                                 "\n" .
                                 "Functions: " . implode( ', ', \expContentAccessReport::$functions ) . " (default read).\n" .
                                 "Ends with ALLOWED (exit 0) or DENIED (exit 1); exit 2 when the user, node or object is not found.",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[user:][node:][object:][function:][language:][json]', '',
                                   array( 'user' => 'The user: its content object ID or its login; "anonymous" for the anonymous user',
                                          'node' => 'The node ID to check',
                                          'object' => 'The object ID to check (instead of a node)',
                                          'function' => 'The content function (default read)',
                                          'language' => 'A language code, for edit and translate',
                                          'json' => 'One JSON object instead of the text' ) );

        $userID = self::userID( isset( $options['user'] ) ? (string)$options['user'] : '' );
        $subject = null;
        if ( !empty( $options['node'] ) && ctype_digit( (string)$options['node'] ) )
            $subject = \eZContentObjectTreeNode::fetch( (int)$options['node'] );
        else if ( !empty( $options['object'] ) && ctype_digit( (string)$options['object'] ) )
            $subject = \eZContentObject::fetch( (int)$options['object'] );

        if ( !$userID || !$subject )
        {
            $cli->error( !$userID ? 'FAIL: give --user=<content object ID or login> of an existing user'
                                  : 'FAIL: give --node=<node ID> or --object=<object ID> of an existing node or object' );
            $this->shutdown( 2 );
            return;
        }

        $report = \expContentAccessReport::check( $subject, $userID, !empty( $options['function'] ) ? (string)$options['function'] : 'read',
                                                  !empty( $options['language'] ) ? (string)$options['language'] : false );
        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
            $this->shutdown( $report['error'] !== null ? 2 : ( $report['allowed'] ? 0 : 1 ) );
            return;
        }
        if ( $report['error'] !== null )
        {
            $cli->error( 'FAIL: ' . $report['error'] );
            $this->shutdown( 2 );
            return;
        }

        $object = $subject instanceof \eZContentObjectTreeNode ? $subject->attribute( 'object' ) : $subject;
        $user = \eZUser::accessUser( $report['user_id'] );
        $cli->output( sprintf( 'User      %d (%s)', $report['user_id'], $user ? $user->attribute( 'login' ) : '?' ) );
        $cli->output( sprintf( 'Subject   %s, object %d "%s"', $subject instanceof \eZContentObjectTreeNode ? 'node ' . (int)$subject->attribute( 'node_id' ) : 'object',
                               (int)$object->attribute( 'id' ), $object->attribute( 'name' ) ) );
        $cli->output( 'Function  content/' . $report['function'] . ( $report['language'] ? ' in ' . $report['language'] : '' ) );
        foreach ( $report['refused_by'] as $refusal )
            $cli->output( '  refused: ' . ( $refusal['policy'] !== null ? 'policy ' . $refusal['policy'] . ', ' : '' ) . $refusal['text'] );
        $cli->output( $report['allowed'] ? 'ALLOWED' : 'DENIED' );
        $this->shutdown( $report['allowed'] ? 0 : 1 );
    }

    /**
     * The content object ID of the user given as an ID, a login or "anonymous"; 0 when there is no such user.
     *
     * @param string $value
     * @return int
     */
    protected static function userID( $value )
    {
        $value = trim( $value );
        if ( $value === '' )
            return 0;
        if ( $value === 'anonymous' )
            return (int)\eZUser::anonymousId();
        $user = ctype_digit( $value ) ? \eZUser::fetch( (int)$value ) : \eZUser::fetchByName( $value );
        return $user instanceof \eZUser ? (int)$user->attribute( 'contentobject_id' ) : 0;
    }
}

}
