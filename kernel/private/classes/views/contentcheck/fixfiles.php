<?php
/**
 * The contentcheck/fixfiles view (doc/eng-us/features/6.0/content-checks.md, "Resolving"): POST only. The missing files
 * list posts an action (FixAction with the ticked Keys[], or one row's ItemAction "<action>:<key>") and NodeID; this
 * view answers with the preview, which shows what will happen to each field version (expContentCheckFileFix::plan())
 * and posts back with ConfirmButton, the confirmation token and, for replace, one file per field version. Then the
 * first batch is done; while some are left, the page shows the progress and posts the rest on (ContinueButton, by
 * itself unless the user turns that off); at the end the browser goes back to the list with the outcome.
 *
 * Needs contentcheck/files and contentcheck/fixfiles (the module's view functions), and on every object
 * content/edit (review: content/read); refused without the form tokens (403), with another method than POST (405),
 * and on a siteaccess that is not an administration one (404). Each field version done is recorded in the audit
 * (content.file.*).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Contentcheck
{

class Fixfiles extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $module = $Params['Module'];
        if ( !\expContentCheck::isAvailableHere() )
            return $module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? (string)$_SERVER['REQUEST_METHOD'] : '';
        $decision = self::decide( $method, $_POST );
        $back = self::backURI( (int)$decision['node_id'] );

        switch ( $decision['step'] )
        {
            case 'refused':
                if ( !headers_sent() )
                {
                    http_response_code( $decision['reason'] === 'method' ? 405 : 403 );
                    if ( $decision['reason'] === 'method' )
                        header( 'Allow: POST' );
                }
                return $this->page( 'design:contentcheck/fixrefused.tpl', array( 'reason' => $decision['reason'], 'node_id' => (int)$decision['node_id'] ) );

            case 'cancel':
                return $module->redirectTo( $back );

            case 'execute':
                $uploads = $decision['action'] === 'replace' ? self::uploads( $decision['keys'] ) : array();
                $outcome = \expContentCheckFileFix::execute( $decision['action'], $decision['keys'],
                                                             isset( $_POST[\expContentCheck::CONFIRM_FIELD] ) ? $_POST[\expContentCheck::CONFIRM_FIELD] : '',
                                                             $uploads );
                $done = $decision['done'] + count( $outcome['fixed'] );
                $failed = $decision['failed'] + count( $outcome['failed'] );
                if ( $outcome['remaining'] && $outcome['reason'] === '' )
                {
                    $remaining = \expContentCheckFiles::keyList( $outcome['remaining'], \expContentCheckFileFix::maxItems() );
                    $plan = \expContentCheckFileFix::plan( $decision['action'], $remaining );
                    return $this->page( 'design:contentcheck/fixprogress.tpl',
                                        array( 'action' => $decision['action'], 'node_id' => (int)$decision['node_id'],
                                               'keys' => $plan['allowed'], 'done' => $done, 'failed' => $failed,
                                               'total' => max( $decision['total'], $done + $failed + count( $plan['allowed'] ) ),
                                               'last_failed' => self::failedItems( $outcome ),
                                               'auto' => $decision['auto'],
                                               'confirm_token' => $plan['allowed'] ? \expContentCheck::confirmToken( $plan['fingerprint'] ) : '',
                                               'confirm_field' => \expContentCheck::CONFIRM_FIELD,
                                               'form_token' => \expContentCheck::formToken(),
                                               'token_field' => \expContentCheck::TOKEN_FIELD ) );
                }
                \eZHTTPTool::instance()->setSessionVariable( \expContentCheckFiles::FEEDBACK,
                                                             array( 'action' => $decision['action'], 'reason' => $outcome['reason'],
                                                                    'fixed' => $done, 'failed' => $failed,
                                                                    'failed_items' => self::failedItems( $outcome ) ) );
                return $module->redirectTo( $back );

            case 'confirm':
            default:
                if ( $decision['all_results'] )
                    $decision['keys'] = self::allResults( $_POST, \expContentCheckFileFix::maxItems() + 1 );
                $plan = \expContentCheckFileFix::plan( $decision['action'], $decision['keys'] );
                return $this->page( 'design:contentcheck/fixconfirm.tpl',
                                    array( 'plan' => $plan, 'action' => $decision['action'], 'node_id' => (int)$decision['node_id'],
                                           'nothing_selected' => !$decision['keys'] || $decision['action'] === '',
                                           'batch_size' => \expContentCheckFileFix::batchSize(),
                                           'max_items' => \expContentCheckFileFix::maxItems(),
                                           'confirm_token' => $plan['allowed'] ? \expContentCheck::confirmToken( $plan['fingerprint'] ) : '',
                                           'confirm_field' => \expContentCheck::CONFIRM_FIELD,
                                           'form_token' => \expContentCheck::formToken(),
                                           'token_field' => \expContentCheck::TOKEN_FIELD ) );
        }
    }

    /**
     * What a request to the view leads to, from its method and its posted fields only.
     *
     * @param string $method the request method
     * @param array $post the posted fields
     * @param string|null $formToken the expected form token (tests); null: this session's
     * @return array array( 'step' => refused|cancel|confirm|execute, 'reason' => '' | method | token, 'action' => one
     *               of the actions or '', 'keys' => from keyList(), 'node_id' => int, 'done', 'failed', 'total' => int
     *               (the progress so far), 'auto' => bool )
     */
    public static function decide( $method, array $post, $formToken = null )
    {
        $decision = array( 'step' => 'refused', 'reason' => '', 'action' => '', 'keys' => array(),
                           'node_id' => \expContentCheck::id( isset( $post['NodeID'] ) ? $post['NodeID'] : null, Unreferenced::rootNodeID() ),
                           'done' => 0, 'failed' => 0, 'total' => 0, 'auto' => false, 'all_results' => false );
        if ( strtoupper( (string)$method ) !== 'POST' )
            return array_merge( $decision, array( 'reason' => 'method' ) );
        if ( !\expContentCheck::matches( isset( $post[\expContentCheck::TOKEN_FIELD] ) ? $post[\expContentCheck::TOKEN_FIELD] : null,
                                         $formToken === null ? \expContentCheck::formToken() : (string)$formToken )
             || !\expContentCheck::checkEzFormToken( $post ) )
            return array_merge( $decision, array( 'reason' => 'token' ) );
        if ( isset( $post['CancelButton'] ) )
            return array_merge( $decision, array( 'step' => 'cancel' ) );

        $max = \expContentCheckFileFix::maxItems() + 1;
        // one row's button: "<action>:<attribute id>-<version>"
        if ( isset( $post['ItemAction'] ) && is_string( $post['ItemAction'] ) && preg_match( '/^([a-z]{1,20}):([0-9]{1,10}-[0-9]{1,6})$/', $post['ItemAction'], $m ) )
        {
            $decision['action'] = in_array( $m[1], \expContentCheckFileFix::ACTIONS, true ) ? $m[1] : '';
            $decision['keys'] = \expContentCheckFiles::keyList( array( $m[2] ), $max );
        }
        else
        {
            $action = isset( $post['FixAction'] ) && is_string( $post['FixAction'] ) ? $post['FixAction'] : '';
            $decision['action'] = in_array( $action, \expContentCheckFileFix::ACTIONS, true ) ? $action : '';
            $decision['keys'] = \expContentCheckFiles::keyList( isset( $post['Keys'] ) && is_array( $post['Keys'] ) ? $post['Keys'] : array(), $max );
        }
        foreach ( array( 'done' => 'Done', 'failed' => 'Failed', 'total' => 'Total' ) as $name => $field )
        {
            if ( isset( $post[$field] ) && is_scalar( $post[$field] ) && preg_match( '/^[0-9]{1,6}$/', (string)$post[$field] ) )
                $decision[$name] = (int)$post[$field];
        }
        $decision['auto'] = !empty( $post['AutoContinue'] );
        $decision['step'] = ( isset( $post['ConfirmButton'] ) || isset( $post['ContinueButton'] ) ) && $decision['keys'] && $decision['action'] !== ''
                            ? 'execute' : 'confirm';
        // every result of the list with its filters instead of the ticked rows: always the preview first
        $decision['all_results'] = isset( $post['AllResultsButton'] ) && !isset( $post['ConfirmButton'] ) && $decision['action'] !== '';
        if ( $decision['all_results'] )
            $decision['step'] = 'confirm';
        return $decision;
    }

    /**
     * The replacement files posted for some field versions: ReplaceFile_<attribute id>_<version>.
     *
     * @param array $keys
     * @return array key => array( 'tmp', 'name', 'uploaded' => true )
     */
    public static function uploads( array $keys )
    {
        $uploads = array();
        foreach ( array_keys( $keys ) as $key )
        {
            $field = 'ReplaceFile_' . str_replace( '-', '_', $key );
            if ( !isset( $_FILES[$field] ) || !is_array( $_FILES[$field] ) || !isset( $_FILES[$field]['error'], $_FILES[$field]['tmp_name'] ) )
                continue;
            if ( (int)$_FILES[$field]['error'] !== UPLOAD_ERR_OK || !is_string( $_FILES[$field]['tmp_name'] ) )
                continue;
            $uploads[$key] = array( 'tmp' => $_FILES[$field]['tmp_name'], 'name' => isset( $_FILES[$field]['name'] ) ? (string)$_FILES[$field]['name'] : '',
                                    'uploaded' => true );
        }
        return $uploads;
    }

    /**
     * The field versions of a batch that failed, for the page: object, field, version, why.
     *
     * @param array $outcome from expContentCheckFileFix::execute()
     * @return array
     */
    protected static function failedItems( array $outcome )
    {
        $items = array();
        foreach ( $outcome['plan']['items'] as $item )
        {
            if ( isset( $outcome['failed'][$item['key']] ) )
                $items[] = array( 'object_name' => $item['object_name'], 'field_name' => $item['field_name'], 'version' => $item['version'],
                                  'why' => $outcome['failed'][$item['key']] );
        }
        return array_slice( $items, 0, 50 );
    }

    /**
     * The field versions of every page of the missing files with the posted filters (NodeID, Problem[], Version[]),
     * at most $max, that the user may resolve: "Preview for all results".
     *
     * @param array $post
     * @param int $max
     * @return array from expContentCheckFiles::keyList()
     */
    public static function allResults( array $post, $max )
    {
        $input = array( 'NodeID' => isset( $post['NodeID'] ) ? $post['NodeID'] : null, 'Limit' => 200 );
        foreach ( array( 'Problem', 'Version' ) as $name )
        {
            if ( isset( $post[$name] ) )
                $input[$name] = $post[$name];
        }
        $params = \expContentCheckFiles::parameters( $input, Unreferenced::rootNodeID() );
        $keys = array();
        // every finding of every page (the list the user saw, kept for a minute), described in slices for can_fix
        $all = \expContentCheckFiles::findAll( $params );
        foreach ( array_chunk( $all['error'] === '' ? $all['rows'] : array(), 200 ) as $slice )
        {
            foreach ( \expContentCheckFiles::describe( $slice ) as $row )
            {
                if ( !empty( $row['can_fix'] ) )
                    $keys[] = $row['key'];
            }
            if ( count( array_unique( $keys ) ) >= $max )
                break;
        }
        return \expContentCheckFiles::keyList( array_values( array_unique( $keys ) ), (int)$max );
    }

    /**
     * Where the list of missing files is: the broken relations with every target and the missing files ticked.
     *
     * @param int $nodeID
     * @return string
     */
    public static function backURI( $nodeID )
    {
        $nodeID = (int)$nodeID > 0 ? (int)$nodeID : Unreferenced::rootNodeID();
        return '/contentcheck/brokenrelations/' . $nodeID .
               \expContentCheckFiles::querySuffix( '',\expContentCheckBrokenRelations::KINDS, \expContentCheckFiles::parameters( array(), $nodeID ) );
    }

    /**
     * @param string $template
     * @param array $variables
     * @return array the view's result
     */
    protected function page( $template, array $variables )
    {
        $tpl = \eZTemplate::factory();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $tpl->setVariable( 'back_uri', self::backURI( isset( $variables['node_id'] ) ? (int)$variables['node_id'] : 0 ) );
        return array( 'content' => $tpl->fetch( $template ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Setup' ), 'url' => 'setup/menu' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Broken relations' ), 'url' => 'contentcheck/brokenrelations' ),
                                       array( 'text' => \ezpI18n::tr( 'kernel/contentcheck', 'Resolve' ), 'url' => false ) ) );
    }
}

}
