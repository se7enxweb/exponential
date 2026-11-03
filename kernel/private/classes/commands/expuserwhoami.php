<?php
/**
 * @description Which account the console acts as (the user of [UserSettings] UserCreatorID, or --as), with its roles and whether it has every right
 * @alias exp:user:whoami
 *
 * File containing the exp:user:whoami command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expuserwhoami extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:whoami';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "Which account the console acts as\n" .
                             "The exp:user commands act as the user of [UserSettings] UserCreatorID, or as the user --as names;\n" .
                             "the audit records that user as who did it. This shows the account, its roles and whether it has\n" .
                             "every right (a role with a policy on every module). Run it before a script that changes accounts.\n" .
                             "Changes nothing.\n" .
                             "\n" .
                             "./console exp:user:whoami\n" .
                             "./console exp:user:whoami --as=jane --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[as:][format:]', '', array(
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
        $id = (int)$actor->attribute( 'contentobject_id' );
        $this->finish( array( 'mode' => 'whoami', 'account' => \expUserManager::summary( $actor ), 'roles' => \expUserManager::roleAssignments( $actor ),
                              'administrator' => in_array( $id, \expUserManager::administratorIDs(), true ),
                              'source' => isset( $options['as'] ) && $options['as'] !== false && $options['as'] !== '' ? 'as' : 'UserCreatorID',
                              'errors' => array(), 'warnings' => array() ), $format );
    }

    protected function lines( array $report )
    {
        $a = $report['account'];
        $lines = array( sprintf( 'The console acts as %s (id %d, %s), from %s%s', $a['login'], $a['id'], $a['enabled'] ? 'enabled' : 'disabled',
                                 $report['source'] === 'as' ? '--as' : '[UserSettings] UserCreatorID', $report['administrator'] ? '; it has every right' : '' ) );
        foreach ( $report['roles'] as $role )
            $lines[] = sprintf( '  %s%s', $role['role'], $role['limitation'] !== '' ? ' (' . $role['limitation'] . ')' : '' );
        return $lines;
    }
}

}
