<?php
/**
 * Setup > Log files (setup/logs; doc/features/6.0/log-viewer.md): the page of the log viewer. It shows the end of a
 * log file (rendered here, so the page reads without JavaScript too), the navigator of every file in the left menu,
 * the search form, and the actions that are switched on. The page's script (page/setup_logs.js) follows the file
 * through setup/logsfeed, short requests only.
 *
 * GET: file (path relative to the root), and the search: q, regex, case, levels[], from, to, siteaccess, uri, ip,
 * reference, rotations, scope (file | kernel | all), cursor; unmask=1 (only with setup/unmasklogs); done, ok (the
 * outcome of an action, to show).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logs extends \Exponential\Runnable\ModuleView
{
    /** The search fields read from the request */
    const SEARCH_FIELDS = array( 'q', 'regex', 'case', 'levels', 'from', 'to', 'siteaccess', 'uri', 'ip', 'reference' );

    /** The outcomes of actions the page may report (done=<action>) */
    const DONE = array( 'rotate', 'truncate', 'delete' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $askUnmask = $http->hasGetVariable( 'unmask' ) && (string)$http->getVariable( 'unmask' ) === '1';
        $viewer = \expLogViewer::forCurrentUser( $askUnmask );
        if ( !$viewer->enabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        $config = $viewer->config();
        $locator = $viewer->locator();

        $error = '';
        $fileID = $http->hasGetVariable( 'file' ) && is_string( $http->getVariable( 'file' ) ) ? (string)$http->getVariable( 'file' ) : '';
        $file = $fileID !== '' ? $locator->resolve( $fileID ) : null;
        if ( $fileID !== '' && $file === null )
            $error = 'not_allowed';
        if ( $file === null )
        {
            $default = $locator->defaultID();
            $file = $default !== null ? $locator->resolve( $default ) : null;
        }

        // the search, when the page was opened with one
        $in = array();
        foreach ( self::SEARCH_FIELDS as $field )
            if ( $http->hasGetVariable( $field ) )
                $in[$field] = $http->getVariable( $field );
        $criteria = \expLogSearch::criteria( $in, $config->int( 'SearchSettings', 'MaxPatternLength' ) );
        $searching = $criteria['q'] !== '' || $criteria['levels'] || $criteria['from'] !== null || $criteria['to'] !== null
                     || $criteria['siteaccess'] !== '' || $criteria['uri'] !== '' || $criteria['ip'] !== '' || $criteria['reference'] !== ''
                     || $criteria['invalid'];
        $scopeName = $http->hasGetVariable( 'scope' ) && in_array( $http->getVariable( 'scope' ), array( 'file', 'kernel', 'all' ), true )
                     ? (string)$http->getVariable( 'scope' ) : ( $criteria['reference'] !== '' ? 'kernel' : 'file' );
        $rotations = $http->hasGetVariable( 'rotations' ) ? (bool)$http->getVariable( 'rotations' ) : $criteria['reference'] !== '';
        $cursor = $http->hasGetVariable( 'cursor' ) && is_string( $http->getVariable( 'cursor' ) ) ? (string)$http->getVariable( 'cursor' ) : '';

        $limit = \expLogRateLimit::forSession( $config );
        $initial = null;
        $search = null;
        $related = null;
        if ( $file !== null )
        {
            $viewer->auditRead( $file, $limit, $viewer->unmasked() );
            if ( $searching )
            {
                if ( $criteria['reference'] !== '' && $criteria['q'] === '' && !$criteria['invalid'] )
                {
                    $ref = $viewer->reference( $criteria['reference'] );
                    $search = array( 'entries' => $ref['entries'], 'cursor' => $ref['cursor'], 'stopped' => $ref['stopped'],
                                     'error' => $ref['error'], 'invalid' => array(), 'scanned' => 0, 'ms' => 0, 'files' => array() );
                    $related = $ref['related'];
                }
                else
                    $search = $viewer->search( $viewer->searchFiles( $file['id'], $scopeName, $rotations ), $criteria, $cursor );
            }
            else
                $initial = $viewer->tail( $file['id'], -1 );
        }

        $done = $http->hasGetVariable( 'done' ) && in_array( $http->getVariable( 'done' ), self::DONE, true ) ? (string)$http->getVariable( 'done' ) : '';
        $token = \expLogViewer::formToken();
        $client = $config->clientSettings();
        $navigator = \expLogViewer::navigatorForCurrentUser();

        $siteAccesses = array();
        $ini = \eZINI::instance();
        if ( $ini->hasVariable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) )
            $siteAccesses = array_values( array_unique( (array)$ini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) );

        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE;
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'log_groups', $navigator );
        $tpl->setVariable( 'log_file', $file !== null ? \expLogViewer::fileInfo( $file ) : null );
        $tpl->setVariable( 'log_error', $error );
        $tpl->setVariable( 'log_initial', $initial );
        $tpl->setVariable( 'log_search', $search );
        $tpl->setVariable( 'log_related', $related );
        $tpl->setVariable( 'log_searching', $searching );
        $tpl->setVariable( 'log_criteria', $criteria + array( 'from_text' => isset( $in['from'] ) && is_scalar( $in['from'] ) ? (string)$in['from'] : '',
                                                              'to_text' => isset( $in['to'] ) && is_scalar( $in['to'] ) ? (string)$in['to'] : '' ) );
        $tpl->setVariable( 'log_scope', $scopeName );
        $tpl->setVariable( 'log_rotations', $rotations );
        $tpl->setVariable( 'log_levels', \expLogParser::$levels );
        $tpl->setVariable( 'log_actions', $viewer->actions() );
        $tpl->setVariable( 'log_can_unmask', \expLogViewer::can( 'unmasklogs' ) );
        $tpl->setVariable( 'log_unmasked', $viewer->unmasked() );
        $tpl->setVariable( 'log_masking', $viewer->redactor()->masking() );
        $tpl->setVariable( 'log_client', $client );
        $tpl->setVariable( 'log_token_field', $token['field'] );
        $tpl->setVariable( 'log_token', $token['token'] );
        $tpl->setVariable( 'log_done', $done );
        $tpl->setVariable( 'log_done_ok', $http->hasGetVariable( 'ok' ) && (string)$http->getVariable( 'ok' ) === '1' );
        $tpl->setVariable( 'log_siteaccesses', $siteAccesses );
        $tpl->setVariable( 'log_max_download', $config->int( 'ActionSettings', 'MaxDownloadBytes' ) );
        // the data the script starts from, as JSON in a script element of type application/json
        $tpl->setVariable( 'log_state_json', json_encode( array(
            'file' => $file !== null ? \expLogViewer::fileInfo( $file ) : null,
            'initial' => $initial, 'search' => $search, 'related' => $related, 'searching' => $searching,
            'client' => $client, 'unmasked' => $viewer->unmasked(),
        ), $flags ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ) );
        return $Result;
    }
}

}
