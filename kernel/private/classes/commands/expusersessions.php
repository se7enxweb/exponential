<?php
/**
 * @description The tracked sessions of an account (Your devices): list them, end all of them or one, as an administrator does on user/sessions (ending is a dry run unless --apply)
 * @alias exp:user:sessions
 *
 * File containing the exp:user:sessions command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expusersessions extends ExpuserBase
{
    protected function name()
    {
        return 'exp:user:sessions';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The sessions of an account\n" .
                             "Lists the account's running sessions as Your devices shows them: id, how it signed in, device and\n" .
                             "browser, the approximate address and the last activity. --end ends all of them, --session=<id>\n" .
                             "ends one, as an administrator does on user/sessions; the person has to sign in again there. The\n" .
                             "audit records access.session.revoke. Without --apply nothing is ended.\n" .
                             "\n" .
                             "./console exp:user:sessions jane\n" .
                             "./console exp:user:sessions jane --end --apply\n" .
                             "./console exp:user:sessions jane --session=812 --apply",
        ) + self::SCRIPT );
        return (array)$this->startup( '[end][session:][apply][dry-run][as:][format:]', '[user]', array(
            'end' => 'End every tracked session of the account (Your devices)',
            'session' => 'End only the session with this id (from the list)',
            'apply' => 'Make the change; without it the command only lists what it would change (a dry run)',
            'dry-run' => 'The default, kept for scripts that pass it: list what would change, change nothing',
            'as' => 'Act as this user (login, e-mail or id; default the user of [UserSettings] UserCreatorID); the audit records it as who did it',
            'format' => 'text (default) or json (the machine output exp-user/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $apply = $this->apply( $options );
        $session = isset( $options['session'] ) && $options['session'] !== false ? trim( (string)$options['session'] ) : '';
        if ( $session !== '' && !ctype_digit( $session ) )
            $this->usage( "--session must be a session id from the list, not $session" );
        $this->database( $format );
        $actor = $this->actor( $options, $format );
        $user = $this->account( $options, $format );
        $list = \expUserManager::sessions( $user );
        $report = array( 'mode' => 'sessions', 'user' => \expUserManager::summary( $user ), 'tracking' => $list['tracking'], 'sessions' => $list['sessions'],
                         'errors' => array(), 'warnings' => array() );
        if ( !empty( $options['end'] ) || $session !== '' )
            $report = array( 'dry_run' => !$apply ) + \expUserManager::endSessionsOf( $user, $session !== '' ? (int)$session : null,
                                                                                   (int)$actor->attribute( 'contentobject_id' ), $apply ) + $report;
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array();
        if ( isset( $report['sessions'] ) )
        {
            $lines[] = sprintf( 'Account %s (id %d): %s', $report['user']['login'], $report['user']['id'],
                                $report['tracking'] ? count( $report['sessions'] ) . ' running session(s)' : 'sessions are not tracked ([UserSessionSettings] Tracking, or no table)' );
            foreach ( $report['sessions'] as $s )
                $lines[] = sprintf( '  %-7d %-12s %-30s %-18s last %s', $s['id'], $s['method'], self::cut( $s['label'], 30 ), $s['ip'],
                                    $s['last_active'] ? date( 'Y-m-d H:i', (int)$s['last_active'] ) : '-' );
        }
        $report['user'] = null;
        return array_merge( $lines, self::actionLines( $report ) );
    }
}

}
