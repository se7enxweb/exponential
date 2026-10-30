<?php
/**
 * @description Change password at next sign-in: show, set or clear the mark of a user account
 * @alias exp:user-password-change-required
 *
 * File containing the exp:user-password-change-required command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Userpasswordchangerequired extends \Exponential\Runnable\Command
{
    const EXIT_OK = 0;
    const EXIT_NOT_FOUND = 1;
    const EXIT_USAGE = 2;

    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Change password at next sign-in\n" .
                                 "Shows, sets or clears the mark that sends a user to the password change form after\n" .
                                 "their next sign-in, before any other page, until they set a new password (which clears\n" .
                                 "it). Guide: doc/eng-us/features/6.0/password-change-at-next-sign-in.md\n" .
                                 "\n" .
                                 "./console exp:user-password-change-required status --login=editor1\n" .
                                 "./console exp:user-password-change-required set --login=editor1\n" .
                                 "./console exp:user-password-change-required clear --user-id=14\n" .
                                 "./console exp:user-password-change-required status --login=editor1 --json\n" .
                                 "\n" .
                                 "In demo mode (EXP_DEMO_MODE=1) the mark of an administrator is kept but ignored.\n" .
                                 "Exit code: 0 done, 1 no such user, 2 usage error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[login:][user-id:][json]', '[ACTION]', array(
            'login'   => 'The login of the account',
            'user-id' => 'The user (content object) id of the account',
            'json'    => 'One JSON object instead of the text'
        ) );

        $action = isset( $options['arguments'][0] ) ? strtolower( trim( $options['arguments'][0] ) ) : 'status';
        $login = isset( $options['login'] ) && is_string( $options['login'] ) ? trim( $options['login'] ) : '';
        $userID = isset( $options['user-id'] ) && is_numeric( $options['user-id'] ) ? (int)$options['user-id'] : 0;
        if ( !in_array( $action, array( 'status', 'set', 'clear' ), true ) || ( $login === '' && $userID <= 0 ) )
        {
            $cli->error( 'Usage: ./console exp:user-password-change-required status|set|clear --login=<login>|--user-id=<id> [--json] (see --help)' );
            $this->shutdown( self::EXIT_USAGE );
            return;
        }

        $user = $userID > 0 ? \eZUser::fetch( $userID ) : \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser || $user->isAnonymous() )
        {
            $cli->error( 'FAIL: no such user account' );
            $this->shutdown( self::EXIT_NOT_FOUND );
            return;
        }

        if ( $action === 'set' )
            \expPasswordChangeRequired::set( $user, 'console' );
        elseif ( $action === 'clear' )
            \expPasswordChangeRequired::clear( $user, 'console' );

        $state = self::state( $user );
        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( array( 'action' => $action ) + $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( self::EXIT_OK );
            return;
        }
        $cli->output( sprintf( '%-24s %d', 'User id', $state['user_id'] ) );
        $cli->output( sprintf( '%-24s %s', 'Login', $state['login'] ) );
        $cli->output( sprintf( '%-24s %s', 'Marked', $state['marked'] ? 'yes' : 'no' ) );
        $cli->output( sprintf( '%-24s %s', 'Enforced at sign-in', $state['enforced'] ? 'yes'
                               : ( $state['marked'] ? 'no (an administrator in demo mode)' : 'no' ) ) );
        $cli->output( 'PASS: ' . ( $action === 'status' ? 'shown' : ( $action === 'set' ? 'the mark is set' : 'the mark is cleared' ) ) );
        $this->shutdown( self::EXIT_OK );
    }

    /**
     * @param \eZUser $user
     * @return array user_id, login, marked, enforced
     */
    public static function state( \eZUser $user )
    {
        return array(
            'user_id'  => (int)$user->attribute( 'contentobject_id' ),
            'login'    => (string)$user->attribute( 'login' ),
            'marked'   => \expPasswordChangeRequired::isMarked( $user ),
            'enforced' => \expPasswordChangeRequired::isRequired( $user ),
        );
    }
}

}
