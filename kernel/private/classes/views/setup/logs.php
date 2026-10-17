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
    /** The fields of the old query string (setup/logs?file=...&q=...) that a redirect carries into the permalink */
    const LEGACY_FIELDS = array( 'q', 'regex', 'case', 'levels', 'from', 'to', 'siteaccess', 'uri', 'ip', 'reference', 'scope',
                                 'rotations', 'cursor', 'offset', 'follow', 'wrap', 'unmask', 'done', 'ok' );

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $config = \expLogViewerConfig::instance();
        if ( !$config->enabled( 'LogViewerSettings', 'LogViewer' ) )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        $locator = new \expLogFileLocator( $config );

        // The old links (?file=<path>) and the search form without JavaScript (?key=...&q=...): the same view as a
        // permalink. 301 for an old link, 303 for a form.
        if ( !empty( $_GET ) && ( isset( $_GET['file'] ) || isset( $_GET['key'] ) ) )
        {
            $legacy = isset( $_GET['file'] ) && !isset( $_GET['key'] );
            $in = array();
            foreach ( self::LEGACY_FIELDS as $field )
                if ( isset( $_GET[$field] ) )
                    $in[$field] = $_GET[$field];
            $state = \expLogPermalink::state( $in, false );
            $target = null;
            if ( $legacy )
            {
                $file = is_string( $_GET['file'] ) ? $locator->resolve( $_GET['file'] ) : null;
                if ( $file !== null )
                {
                    $state['rotation'] = $file['rotation'];
                    $target = \expLogPermalink::build( $file['key'], $state );
                }
            }
            else
            {
                // the archive asked for: archive=N (rotation=N is the same)
                $archive = isset( $_GET['archive'] ) && is_scalar( $_GET['archive'] ) ? (int)$_GET['archive']
                           : ( isset( $_GET['rotation'] ) && is_scalar( $_GET['rotation'] ) ? (int)$_GET['rotation'] : 0 );
                if ( is_string( $_GET['key'] ) && $locator->resolveKey( $_GET['key'], $archive ) !== null )
                {
                    $state['rotation'] = $archive;
                    $target = \expLogPermalink::build( (string)$_GET['key'], $state );
                }
            }
            if ( $target !== null )
            {
                $url = '/' . $target;
                \eZURI::transformURI( $url );
                if ( method_exists( '\eZExecution', 'discardOutputBuffers' ) )
                    \eZExecution::discardOutputBuffers();
                header( $legacy ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See Other' );
                header( 'Location: ' . $url );
                header( 'Cache-Control: no-store' );
                \eZExecution::cleanExit();
            }
            return $this->notFound( $Module );
        }

        // setup/logs/view/<key>/(name)/value...; setup/logs alone opens the default log
        $mode = isset( $Params['Mode'] ) ? (string)$Params['Mode'] : '';
        $key = isset( $Params['Key'] ) ? (string)$Params['Key'] : '';
        if ( $mode !== '' && $mode !== 'view' )
            return $this->notFound( $Module );
        $state = \expLogPermalink::state( $Params, true );
        if ( $mode === 'view' && $key === '' )
            return $Module->redirectTo( '/setup/logs' );

        $askUnmask = $state['unmask'];
        $viewer = new \expLogViewer( $config, $locator, $askUnmask && \expLogViewer::can( 'unmasklogs' ) );
        $error = '';
        if ( $mode === 'view' )
        {
            $file = $locator->resolveKey( $key, $state['rotation'] );
            if ( $file === null )
                return $this->notFound( $Module );
        }
        else
        {
            $file = null;
            $defaultKey = (string)$config->get( 'LogViewerSettings', 'DefaultKey' );
            if ( $defaultKey !== '' )
                $file = $locator->resolveKey( $defaultKey, 0 );
            if ( $file === null )
            {
                $default = $locator->defaultID();
                $file = $default !== null ? $locator->resolve( $default ) : null;
            }
        }

        // the search, when the view asks for one
        $in = \expLogPermalink::criteriaInput( $state );
        $criteria = \expLogSearch::criteria( $in, $config->int( 'SearchSettings', 'MaxPatternLength' ) );
        $searching = \expLogPermalink::searching( $state ) || $criteria['invalid'];
        $scopeName = $state['scope'] !== 'file' ? $state['scope'] : ( $criteria['reference'] !== '' ? 'kernel' : 'file' );
        $rotations = $state['rotations'] || $criteria['reference'] !== '';
        $cursor = $state['cursor'];

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

        $done = $state['done'];
        $token = \expLogViewer::formToken();
        $client = $config->clientSettings();
        $navigator = \expLogViewer::navigatorForCurrentUser();
        // wrapping is the viewer's preference (on by default); a (wrap)/0|1 in a shared link only changes this one page
        $wrap = isset( $Params['wrap'] ) && $Params['wrap'] !== false ? $state['wrap'] : $viewer->wrapLines();
        $permalink = $file !== null ? \expLogPermalink::build( $file['key'], array( 'rotation' => $file['rotation'] ) + $state, array( 'done', 'ok', 'unmask', 'wrap' ) ) : '';

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
        $tpl->setVariable( 'log_done_ok', $state['ok'] );
        // the permalink of this view (without the outcome of an action and without unmask), its key and its state
        $tpl->setVariable( 'log_permalink', $permalink );
        $tpl->setVariable( 'log_key', $file !== null ? $file['key'] : '' );
        $tpl->setVariable( 'log_view_state', $state );
        $tpl->setVariable( 'log_wrap', $wrap );
        $tpl->setVariable( 'log_siteaccesses', $siteAccesses );
        $tpl->setVariable( 'log_max_download', $config->int( 'ActionSettings', 'MaxDownloadBytes' ) );
        // the data the script starts from, as JSON in a script element of type application/json
        $tpl->setVariable( 'log_state_json', json_encode( array(
            'file' => $file !== null ? \expLogViewer::fileInfo( $file ) : null,
            'initial' => $initial, 'search' => $search, 'related' => $related, 'searching' => $searching,
            'client' => $client, 'unmasked' => $viewer->unmasked(),
            'key' => $file !== null ? $file['key'] : '', 'view' => $state, 'permalink' => $permalink, 'wrap' => $wrap,
        ), $flags ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/logs.tpl' );
        $Result['left_menu'] = 'design:setup/logs_menu.tpl';
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'kernel/setup', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Log files' ) ) );
        return $Result;
    }

    /**
     * A key, a rotation or an old link the viewer may not open: the kernel's "not found" page (404), the same for a
     * key that does not exist and for one that is not allowed, so the answer tells nothing about the disk.
     *
     * @param \eZModule $Module
     * @return array
     */
    protected function notFound( $Module )
    {
        return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
    }
}

}
