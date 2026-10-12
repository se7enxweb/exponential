<?php
/**
 * @description Unique logins: list the logins more than one user account has; --fix-index adds the unique index on ezuser.login once there are none
 * @alias exp:user-login-check
 *
 * File containing the exp:user-login-check command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 826e1d4647), Copyright (C) Mugo Web, GPL v2
 */

namespace Exponential\Command\Kernel
{

class Userlogincheck extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "Unique logins\n" .
                                 "Lists the logins that more than one user account has: rows the unique index on ezuser.login\n" .
                                 "would refuse on this database engine, and logins that differ only in case, which signing in\n" .
                                 "cannot tell apart. Without --fix-index nothing is changed.\n" .
                                 "\n" .
                                 "Logins are shown shortened (first letter and length); --show-logins shows them in full.\n" .
                                 "\n" .
                                 "./console exp:user-login-check\n" .
                                 "./console exp:user-login-check --json\n" .
                                 "./console exp:user-login-check --fix-index\n" .
                                 "\n" .
                                 "--fix-index adds the unique index only after a clean report and refuses otherwise.\n" .
                                 "Exit code: 0 clean (or index added), 1 duplicates found (or index refused), 2 error.",
                'use-session'    => false,
                'use-modules'    => false,
                'use-extensions' => true
            )
        );
        $options = $this->startup( '[fix-index][json][show-logins]', '', array(
            'fix-index'   => 'Add the unique index on ezuser.login when no duplicates are found',
            'json'        => 'One JSON object instead of the text',
            'show-logins' => 'Show the logins in full instead of shortened'
        ) );
        $showLogins = !empty( $options['show-logins'] );

        try
        {
            $db = \eZDB::instance();
            $duplicates = \expUserLoginIntegrity::duplicates( $db );
            $index = \expUserLoginIntegrity::indexStatus( $db );
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 2 );
            return;
        }

        $fix = null;
        if ( !empty( $options['fix-index'] ) )
        {
            $message = '';
            $added = \expUserLoginIntegrity::addUniqueIndex( $db, $message );
            $fix = array( 'added' => $added, 'message' => $message );
            $index = \expUserLoginIntegrity::indexStatus( $db );
        }

        $groups = array();
        foreach ( $duplicates as $group )
        {
            $groups[] = array(
                'login'        => $showLogins ? $group['login'] : self::mask( $group['login'] ),
                'spellings'    => $showLogins ? $group['logins'] : array_map( array( __CLASS__, 'mask' ), $group['logins'] ),
                'user_ids'     => $group['user_ids'],
                'blocks_index' => $group['blocks_index'],
            );
        }
        $exit = $fix !== null ? ( $fix['added'] ? 0 : 1 ) : ( $groups ? 1 : 0 );

        if ( !empty( $options['json'] ) )
        {
            $cli->output( json_encode( array( 'engine' => $index['engine'], 'index' => $index, 'duplicates' => $groups, 'fix_index' => $fix ),
                                       JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            $this->shutdown( $exit );
            return;
        }

        $cli->output( sprintf( '%-16s %s', 'Engine', $index['engine'] ) );
        $cli->output( sprintf( '%-16s %s', 'Login index', !$index['exists'] ? 'none' :
                               implode( ', ', $index['names'] ) . ( $index['unique'] ? ' (unique)' : ' (not unique)' ) ) );
        $cli->output( sprintf( '%-16s %d', 'Duplicate logins', count( $groups ) ) );
        foreach ( $groups as $group )
        {
            $cli->output( sprintf( '  %-20s accounts %s%s%s', $group['login'], implode( ', ', $group['user_ids'] ),
                                   count( $group['spellings'] ) > 1 ? '; spellings ' . implode( ', ', $group['spellings'] ) : '',
                                   $group['blocks_index'] ? '' : '; differ only in case (the index of this engine accepts them)' ) );
        }
        if ( $fix !== null )
            $cli->output( ( $fix['added'] ? 'PASS: ' : 'FAIL: ' ) . $fix['message'] );
        elseif ( $groups )
            $cli->output( 'FAIL: ' . count( $groups ) . ' login(s) belong to more than one account; resolve them before --fix-index' );
        else
            $cli->output( 'PASS: every login belongs to one account' . ( $index['unique'] ? '' : '; --fix-index adds the unique index' ) );
        $this->shutdown( $exit );
    }

    /**
     * A login shortened to its first character and its length, so the report shows no personal data.
     *
     * @param string $login
     * @return string
     */
    public static function mask( $login )
    {
        $login = (string)$login;
        if ( $login === '' )
            return '(empty)';
        $length = function_exists( 'mb_strlen' ) ? mb_strlen( $login, 'UTF-8' ) : strlen( $login );
        $first = function_exists( 'mb_substr' ) ? mb_substr( $login, 0, 1, 'UTF-8' ) : $login[0];
        return $first . str_repeat( '*', max( 0, min( $length, 12 ) - 1 ) ) . ' (' . $length . ')';
    }
}

}
