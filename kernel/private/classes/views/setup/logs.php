<?php
/**
 * Setup > Log files (setup/logs; doc/eng-us/features/6.0/log-viewer.md): the page of the log viewer. It shows the end of a
 * log file (rendered here, so the page reads without JavaScript too), the navigator of every file in the left menu,
 * the search form, and the actions that are switched on. The page's script (page/setup_logs.js) follows the file
 * through setup/logsfeed, short requests only.
 *
 * The view is a permalink, setup/logs/view/<key>/(name)/value (expLogPermalink): the search (search, regex, case,
 * level, from, to, siteaccess, uri, ip, reference, rotations, scope, cursor), (archive)/N, (unmask)/1 (only with
 * setup/unmasklogs), (done), (ok) (the outcome of an action, to show). Options are view parameters only, a query
 * string is never read. The search form and the file chooser without JavaScript POST key (or file) and the criteria
 * (q, regex, case, levels[], ...) with the form token, and are answered 303 with the permalink.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Setup
{

class Logs extends \Exponential\Runnable\ModuleView
{
    /** The fields of the posted search form (and the file chooser) that the 303 carries into the permalink */
    const FORM_FIELDS = array( 'q', 'regex', 'case', 'levels', 'from', 'to', 'siteaccess', 'uri', 'ip', 'reference', 'scope',
                               'rotations', 'cursor', 'unmask' );

    /**
     * The permalink a posted search form or file chooser asks for: key (and rotation), or file (an id of the
     * navigator), with the criteria of the form. Null when the log does not exist or may not be opened.
     *
     * @param array $post usually $_POST
     * @param \expLogFileLocator $locator
     * @return string|null setup/logs/view/<key>/(name)/value...
     */
    public static function postedPermalink( array $post, $locator )
    {
        $in = array();
        foreach ( self::FORM_FIELDS as $field )
            if ( isset( $post[$field] ) )
                $in[$field] = $post[$field];
        $state = \expLogPermalink::state( $in, false );
        if ( isset( $post['key'] ) && is_string( $post['key'] ) && $post['key'] !== '' )
        {
            $rotation = isset( $post['rotation'] ) && is_scalar( $post['rotation'] ) ? (int)$post['rotation'] : 0;
            if ( $locator->resolveKey( $post['key'], $rotation ) === null )
                return null;
            $state['rotation'] = $rotation;
            return \expLogPermalink::build( $post['key'], $state );
        }
        $file = isset( $post['file'] ) && is_string( $post['file'] ) ? $locator->resolve( $post['file'] ) : null;
        if ( $file === null )
            return null;
        $state['rotation'] = $file['rotation'];
        return \expLogPermalink::build( $file['key'], $state );
    }

    /**
     * The search of a view as view parameters of the feed (setup/logsfeed): "/(file)/<id>/(q)/..." with every value
     * written as every view parameter is (expViewParameters::encode()), never a query string.
     *
     * @param string $fileID
     * @param array $criteria expLogSearch::criteria() plus from_text and to_text
     * @param string $scope
     * @param bool $rotations
     * @param bool $unmasked
     * @return string
     */
    public static function feedSearchSuffix( $fileID, array $criteria, $scope, $rotations, $unmasked )
    {
        $pairs = array( 'file' => (string)$fileID );
        foreach ( array( 'q', 'siteaccess', 'uri', 'ip', 'reference' ) as $name )
            $pairs[$name] = isset( $criteria[$name] ) && is_scalar( $criteria[$name] ) ? (string)$criteria[$name] : '';
        $pairs['regex'] = !empty( $criteria['regex'] ) ? '1' : '';
        $pairs['case'] = !empty( $criteria['case'] ) ? '1' : '';
        $pairs['levels'] = !empty( $criteria['levels'] ) ? implode( ',', (array)$criteria['levels'] ) : '';
        $pairs['from'] = isset( $criteria['from_text'] ) ? (string)$criteria['from_text'] : '';
        $pairs['to'] = isset( $criteria['to_text'] ) ? (string)$criteria['to_text'] : '';
        $pairs['scope'] = (string)$scope;
        $pairs['rotations'] = $rotations ? '1' : '';
        $pairs['unmask'] = $unmasked ? '1' : '';
        $suffix = '';
        foreach ( $pairs as $name => $value )
            if ( $value !== '' )
                $suffix .= '/(' . $name . ')/' . \expViewParameters::encode( $value );
        return $suffix;
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();

        $config = \expLogViewerConfig::instance();
        if ( !$config->enabled( 'LogViewerSettings', 'LogViewer' ) )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );
        $locator = new \expLogFileLocator( $config );

        // The search form and the file chooser without JavaScript are sent with POST (the form token included) and
        // answered 303 with the permalink of what was asked for. The options are view parameters only: a query
        // string (?file=, ?key=, ?q= ...) is never read.
        if ( \expViewParameters::redirectsPost( $_POST, array( 'key', 'file' ) ) )
        {
            $target = self::postedPermalink( $_POST, $locator );
            if ( $target === null )
                return $this->notFound( $Module );
            \expViewParameters::redirect( $target );
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
        // the link to copy and share: its request and client address filters as the masks of the settings show them,
        // so an unmasked viewer's link carries no address a masked viewer is not shown (the address bar keeps the
        // filters as typed, for this viewer's own reloads)
        $sharePermalink = $file !== null
                          ? \expLogPermalink::build( $file['key'], array( 'rotation' => $file['rotation'] )
                                                     + \expLogPermalink::shareable( $state, \expLogRedactor::fromConfig( $config, false ) ),
                                                     array( 'done', 'ok', 'unmask', 'wrap' ) )
                          : '';

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
        // the search for the feed (view parameters by file id) and the link to more results (the permalink with the cursor)
        $criteriaText = $criteria + array( 'from_text' => isset( $in['from'] ) && is_scalar( $in['from'] ) ? (string)$in['from'] : '',
                                           'to_text' => isset( $in['to'] ) && is_scalar( $in['to'] ) ? (string)$in['to'] : '' );
        $tpl->setVariable( 'log_feed_search', $file !== null ? self::feedSearchSuffix( $file['id'], $criteriaText, $scopeName, $rotations, $viewer->unmasked() ) : '' );
        $tpl->setVariable( 'log_more_url', $file !== null && $search !== null && !empty( $search['cursor'] )
                                           ? \expLogPermalink::build( $file['key'], array( 'rotation' => $file['rotation'], 'scope' => $scopeName, 'rotations' => $rotations,
                                                                                           'cursor' => (string)$search['cursor'], 'unmask' => $viewer->unmasked() ) + $state,
                                                                      array( 'done', 'ok', 'wrap' ) )
                                           : '' );
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
        $tpl->setVariable( 'log_share_permalink', $sharePermalink );
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
