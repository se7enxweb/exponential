<?php
/**
 * Setup > Log files > Error digest (setup/logsdigest; doc/en/us/features/6.0/log-viewer.md, "Error digest"): the distinct
 * errors and warnings of the last hours, per log, normalized and counted (expLogDigest), each linked to the log's view
 * filtered to it. Policy setup/logs.
 *
 *   setup/logsdigest                    every kernel log, the last [DigestSettings] Hours (24)
 *   setup/logsdigest/<key>              one log
 *   .../(hours)/N                       another window (1 to 168 hours)
 *   .../(known)/1                       list the groups marked known too
 * POST (form token): KnownButton=<group id> marks a group known for this user, UnknownButton=<group id> lists it again
 * (the preference admin_logviewer_known); then the same digest is opened again.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logsdigest extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $config = \expLogViewerConfig::instance();
        if ( !$config->enabled( 'LogViewerSettings', 'LogViewer' ) )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        $locator = new \expLogFileLocator( $config );
        $viewer = new \expLogViewer( $config, $locator );
        $digest = new \expLogDigest( $config );

        $key = isset( $Params['Key'] ) && is_string( $Params['Key'] ) ? (string)$Params['Key'] : '';
        $hours = isset( $Params['hours'] ) && preg_match( '/^\d{1,3}$/', (string)$Params['hours'] ) ? max( 1, min( 168, (int)$Params['hours'] ) ) : $digest->setting( 'Hours' );
        $showKnown = isset( $Params['known'] ) && (string)$Params['known'] === '1';
        $self = self::path( $key, $hours, $showKnown, $digest->setting( 'Hours' ) );

        // mark a group known, or list it again
        if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' )
        {
            if ( !\expLogViewer::checkFormToken() )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            foreach ( array( 'KnownButton' => true, 'UnknownButton' => false ) as $button => $known )
            {
                if ( !$http->hasPostVariable( $button ) || !is_string( $http->postVariable( $button ) ) )
                    continue;
                $id = (string)$http->postVariable( $button );
                if ( preg_match( '/^[0-9a-f]{12}$/', $id ) )
                    \eZPreferences::setValue( \expLogDigest::KNOWN_PREFERENCE,
                                              \expLogDigest::withKnown( \eZPreferences::value( \expLogDigest::KNOWN_PREFERENCE ), $id, $known ) );
            }
            return $Module->redirectTo( '/' . $self );
        }

        // the logs: one key, or every current kernel log
        $files = array();
        if ( $key !== '' )
        {
            $file = $locator->resolveKey( $key, 0 );
            if ( $file === null )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
            $files[] = $file;
        }
        else
        {
            $groups = $locator->listFiles();
            foreach ( $groups[\expLogFileLocator::GROUP_KERNEL] as $info )
            {
                $file = $locator->resolve( $info['id'] );
                if ( $file !== null )
                    $files[] = $file;
            }
        }

        $known = \expLogDigest::knownFrom( \eZPreferences::value( \expLogDigest::KNOWN_PREFERENCE ) );
        $since = time() - $hours * 3600;
        $limit = \expLogRateLimit::forSession( $config );
        $logs = array();
        $hidden = 0;
        foreach ( $files as $file )
        {
            $viewer->auditRead( $file, $limit, false );
            $one = $digest->digestFile( $file, $since, $known, $showKnown );
            foreach ( $one['groups'] as &$g )
            {
                $g['link'] = \expLogDigest::link( $g, $since, $one['grouping'] );
                $g['entry_link'] = \expLogDigest::entryLink( $g );
            }
            unset( $g );
            $one['view_link'] = \expLogPermalink::build( $one['key'] );
            $hidden += $one['hidden'];
            $logs[] = $one;
        }

        $token = \expLogViewer::formToken();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'digest_logs', $logs );
        $tpl->setVariable( 'digest_key', $key );
        $tpl->setVariable( 'digest_hours', $hours );
        $tpl->setVariable( 'digest_hour_choices', array( 1, 6, 24, 72, 168 ) );
        $tpl->setVariable( 'digest_since', $since );
        $tpl->setVariable( 'digest_show_known', $showKnown );
        $tpl->setVariable( 'digest_hidden', $hidden );
        $tpl->setVariable( 'digest_known_count', count( $known ) );
        $tpl->setVariable( 'digest_levels', $digest->levels() );
        $tpl->setVariable( 'digest_self', $self );
        $tpl->setVariable( 'digest_toggle_known', self::path( $key, $hours, !$showKnown, $digest->setting( 'Hours' ) ) );
        $tpl->setVariable( 'digest_base', 'setup/logsdigest' . ( $key !== '' ? '/' . \expLogPermalink::encode( $key ) : '' ) );
        $tpl->setVariable( 'digest_token_field', $token['field'] );
        $tpl->setVariable( 'digest_token', $token['token'] );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs_digest.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => 'setup/logs', 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Error digest' ) ) );
        return $Result;
    }

    /**
     * The path of a digest (for ezurl), its defaults left out. Pure.
     *
     * @param string $key '' for every kernel log
     * @param int $hours
     * @param bool $showKnown
     * @param int $defaultHours
     * @return string
     */
    public static function path( $key, $hours, $showKnown, $defaultHours = 24 )
    {
        $path = 'setup/logsdigest' . ( $key !== '' ? '/' . \expLogPermalink::encode( $key ) : '' );
        if ( (int)$hours !== (int)$defaultHours )
            $path .= '/(hours)/' . (int)$hours;
        if ( $showKnown )
            $path .= '/(known)/1';
        return $path;
    }
}

}
