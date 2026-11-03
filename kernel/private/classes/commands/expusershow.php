<?php
/**
 * @description Show one user account: groups, roles with their limitation, enabled or locked, last visit, registration state, two-step sign-in and active sessions (never a password hash)
 * @alias exp:user:show
 *
 * File containing the exp:user:show command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expusershow extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:show';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Show one user account\n" .
                             "Names the account by its login, its e-mail address (when one account has it) or its id, and\n" .
                             "shows its groups (the main one first), its roles with where they come from and their limitation,\n" .
                             "whether it is enabled, unactivated or locked, its sign-ins and last visit, the state of its\n" .
                             "registration, two-step sign-in, active sessions, whether a new password is required, and whether\n" .
                             "the account is protected from being disabled or removed. Never a password hash. Changes nothing.\n" .
                             "\n" .
                             "./console exp:user:show admin\n" .
                             "./console exp:user:show jane@example.com --format=json\n" .
                             "./console exp:user:show 14",
        ) + self::SCRIPT );
        return (array)$this->startup( '[as:][format:]', '[user]', array(
            'as' => 'Act as this user (login, e-mail or id; default the user of [UserSettings] UserCreatorID); the audit records it as who did it',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $this->database( $format );
        $actor = $this->actor( $options, $format );
        $user = $this->account( $options, $format );
        $this->finish( array( 'mode' => 'show', 'account' => \expUserManager::account( $user, $actor ), 'errors' => array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        return isset( $report['account'] ) ? self::showLines( $report['account'] ) : array();
    }

    /**
     * The lines of an account. Pure.
     *
     * @param array $a see expUserManager::account()
     * @return string[]
     */
    public static function showLines( array $a )
    {
        $state = $a['enabled'] ? 'enabled' : ( $a['unactivated'] ? 'unactivated' : 'disabled' );
        if ( $a['locked'] )
            $state .= ', locked';
        $lines = array(
            sprintf( 'Account %s (id %d)', $a['login'], $a['id'] ),
            '  Name:          ' . ( $a['name'] !== '' ? $a['name'] : '-' ) . ' (class ' . ( $a['class'] !== '' ? $a['class'] : '-' ) . ', node ' . $a['node_id'] . ', object ' . $a['object'] . ')',
            '  E-mail:        ' . ( $a['email'] !== '' ? $a['email'] : '-' ),
            '  State:         ' . $state . ( $a['protected'] !== '' ? ' (protected: ' . $a['protected'] . ')' : '' ) . ( $a['administrator'] ? ', administrator' : '' ),
            '  Registration:  ' . $a['registration'],
            sprintf( '  Sign-ins:      %d, last visit %s, %d failed attempt(s)', $a['login_count'], $a['last_visit'] ? date( 'Y-m-d H:i', (int)$a['last_visit'] ) : 'never',
                     $a['failed_attempts'] ),
            '  Password:      ' . ( $a['password_changed'] ? 'changed ' . date( 'Y-m-d', (int)$a['password_changed'] ) : 'change not recorded' )
                                . ( $a['password_change_required'] ? ', a new one is required at the next sign-in' : '' ),
            '  Two-step:      ' . ( is_array( $a['two_factor'] ) ? ( !empty( $a['two_factor']['active'] ) ? 'on (' . $a['two_factor']['method'] . ')' : 'off' ) : 'not available' ),
            '  Sessions:      ' . ( $a['sessions'] === null ? 'not tracked' : $a['sessions'] . ' active' ),
        );
        $lines[] = '  Groups:';
        foreach ( $a['groups'] as $group )
            $lines[] = sprintf( '    %s (node %d)%s', $group['name'], $group['node_id'], $group['main'] ? ', main' : '' );
        if ( !$a['groups'] )
            $lines[] = '    none';
        $lines[] = '  Roles:';
        foreach ( $a['roles'] as $role )
            $lines[] = sprintf( '    %s%s, via %s', $role['role'], $role['limitation'] !== '' ? ' (' . $role['limitation'] . ')' : '',
                                $role['via'] === $a['id'] ? 'the account' : $role['via_name'] . ' (object ' . $role['via'] . ')' );
        if ( !$a['roles'] )
            $lines[] = '    none';
        return $lines;
    }
}

}
