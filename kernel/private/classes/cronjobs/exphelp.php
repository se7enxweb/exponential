<?php
/**
 * @description Help maintained as content: removes old answers to "Was this helpful?" and records the DITA export of the help in git when exphelp.ini [ContentSettings] DitaHistoryDirectory is set
 *
 * The code of cronjobs/exphelp.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Cronjob\Kernel
{

class Exphelp extends \Exponential\Runnable\CronjobPart
{
    /**
     * - "Was this helpful?": the answers older than FeedbackKeepDays are removed (expHelpContentFeedback::prune()).
     * - The history of the help in git (delivery path 4 of the plan): with DitaHistoryDirectory set, the DITA export of
     *   every manual is written into it (only what changed) and committed (expHelpContentDitaHistory). The export holds
     *   the Released objects the user DitaHistoryLogin (default admin) may read.
     */
    public function run( array $scope )
    {
        $cli = isset( $scope['cli'] ) ? $scope['cli'] : \eZCLI::instance();
        if ( !class_exists( 'expHelpContentProvider' ) || !\expHelpContentProvider::isEnabled() )
        {
            $cli->output( 'Skipped: help maintained as content is not installed or switched off' );
            return true;
        }
        if ( \expHelpContentTables::available( \expHelpContentTables::FEEDBACK ) )
            $cli->output( sprintf( 'Was this helpful?: %d answers older than %d days removed', \expHelpContentFeedback::prune(), \expHelpContentFeedback::keepDays() ) );

        $dir = \expHelpContentDitaHistory::directory();
        if ( $dir === '' )
        {
            $cli->output( 'DITA history: off ([ContentSettings] DitaHistoryDirectory is empty)' );
            return true;
        }
        $login = (string)\expHelpContentSchema::setting( 'DitaHistoryLogin', 'admin' );
        $user = \eZUser::fetchByName( $login );
        if ( !$user instanceof \eZUser )
        {
            $cli->error( "DITA history: no user $login ([ContentSettings] DitaHistoryLogin)" );
            return true;
        }
        $previous = \eZUser::currentUser();
        \eZUser::setCurrentlyLoggedInUser( $user, (int)$user->attribute( 'contentobject_id' ), \eZUser::NO_SESSION_REGENERATE );
        \expHelpContentProvider::resetInstance();
        try
        {
            $export = ( new \expHelpContentDita( \expHelpContentProvider::instance(), null ) )->build();
            $problems = \expHelpContentDita::validate( $export );
            if ( $problems )
            {
                foreach ( $problems as $problem )
                    $cli->error( 'DITA history: ' . $problem['file'] . ': ' . $problem['message'] );
                return true;
            }
            $result = \expHelpContentDita::write( $export, $dir, false );
            $commit = ( new \expHelpContentDitaHistory( $dir ) )->commit( 'Help export of ' . gmdate( 'Y-m-d H:i' ) . ' UTC', true );
            $cli->output( sprintf( 'DITA history: %d files written, %d removed, %d unchanged; git %s: %s%s', count( $result['written'] ),
                                   count( $result['removed'] ), $result['unchanged'], $commit['status'], $commit['message'],
                                   $commit['commit'] !== '' ? ' (' . $commit['commit'] . ')' : '' ) );
        }
        finally
        {
            \eZUser::setCurrentlyLoggedInUser( $previous, (int)$previous->attribute( 'contentobject_id' ), \eZUser::NO_SESSION_REGENERATE );
            \expHelpContentProvider::resetInstance();
        }
        return true;
    }
}

}
