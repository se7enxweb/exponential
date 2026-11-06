<?php
/**
 * The import of a URL map in the administration (content/urlimport), policy content/urltranslator_import:
 *
 *   /content/urlimport                                       the file (upload or paste), its format, the siteaccess,
 *                                                            language and conflict policy, old hosts
 *   /content/urlimport/(plan)/<token>[/(state)/<state>][/(offset)/<n>]
 *                                                            the preview: every row with its state, a filter by state,
 *                                                            import or skip per row, hosts to map, Dry run, Import
 *   /content/urlimport/(plan)/<token>/(report)/csv|json      the plan row by row, as a download
 *
 * The file and the plan stay on the server (expUrlMapImportSession, var/<site>/urlimport/<token>/, a day); the forms
 * post only the token's address, the decisions of the rows shown and the hosts, never the plan. Import asks "Verify
 * it's you" (site.ini [ReauthGroup_system] content/urlimport ImportButton). The import itself is expUrlMapImporter's:
 * one batch, listed and undone on content/urlimporthistory.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 * @since 6.0.15
 */

namespace Exponential\View\Kernel\Content
{

class UrlImport extends \Exponential\Runnable\ModuleView
{
    /** Rows of the preview on one page */
    const PAGE_SIZE = 50;

    /** The states that can be imported, and so ticked */
    const SELECTABLE = array( 'new', 'changed' );

    /**
     * The address of a preview.
     *
     * @param string $token
     * @param string $state 'all' or a state
     * @param int $offset
     * @return string module-relative
     */
    public static function planURL( $token, $state = 'all', $offset = 0 )
    {
        return 'content/urlimport/(plan)/' . $token . ( $state !== 'all' ? '/(state)/' . $state : '' ) . ( $offset > 0 ? '/(offset)/' . (int)$offset : '' );
    }

    /**
     * The state filter of the view parameters: 'all' or one of expUrlMapPlan::states(). Pure.
     *
     * @param mixed $state
     * @return string
     */
    public static function stateKey( $state )
    {
        return is_string( $state ) && in_array( $state, \expUrlMapPlan::states(), true ) ? $state : 'all';
    }

    /**
     * The rows of a page of the preview, for the template. Pure.
     *
     * @param array[] $items the plan's
     * @param string $state 'all' or a state
     * @param int $offset
     * @param int $limit
     * @return array rows, count (of the state), offset (corrected)
     */
    public static function page( array $items, $state, $offset, $limit = self::PAGE_SIZE )
    {
        $shown = $state === 'all' ? array_values( $items ) : array_values( array_filter( $items, function ( $item ) use ( $state ) { return $item['state'] === $state; } ) );
        $count = count( $shown );
        $offset = max( 0, (int)$offset );
        if ( $offset >= $count )
            $offset = 0;
        $rows = array();
        foreach ( array_slice( $shown, $offset, $limit ) as $item )
        {
            $rows[] = array( 'index' => (int)$item['index'], 'line' => (int)$item['line'], 'old_url' => (string)$item['old_url'],
                             'new_url' => (string)$item['new_url'], 'kind' => (string)$item['kind'], 'state' => (string)$item['state'],
                             'store' => (string)$item['store'], 'siteaccess' => (string)$item['siteaccess'], 'language' => (string)$item['language'],
                             'status' => (int)$item['status'], 'messages' => array_values( array_map( 'strval', (array)$item['messages'] ) ),
                             'decision' => (string)$item['decision'], 'action' => (string)$item['action'],
                             'selectable' => in_array( $item['state'], self::SELECTABLE, true ) );
        }
        return array( 'rows' => $rows, 'count' => $count, 'offset' => $offset );
    }

    /**
     * Applies the ticks of the rows a page showed: a shown row that can be imported is imported when ticked and
     * skipped when not; rows not shown keep their decision. Indexes are taken as whole numbers only.
     *
     * @param \expUrlMapPlan $plan
     * @param mixed $shown the posted RowIndexList
     * @param mixed $ticked the posted ImportRowList
     * @return int how many decisions changed
     */
    public static function applyDecisions( \expUrlMapPlan $plan, $shown, $ticked )
    {
        $ticked = array_flip( array_map( 'intval', array_filter( (array)$ticked, 'is_numeric' ) ) );
        $changed = 0;
        foreach ( array_unique( array_map( 'intval', array_filter( (array)$shown, 'is_numeric' ) ) ) as $index )
        {
            $item = $plan->item( $index );
            if ( !$item || !in_array( $item['state'], self::SELECTABLE, true ) )
                continue;
            $decision = isset( $ticked[$index] ) ? 'import' : 'skip';
            if ( $item['decision'] !== $decision && $plan->setDecision( $index, $decision ) )
                $changed++;
        }
        return $changed;
    }

    /**
     * The hosts of the rows the planner did not know, each once, in the order of the file. Pure.
     *
     * @param array[] $items
     * @return string[]
     */
    public static function unknownHosts( array $items )
    {
        $hosts = array();
        foreach ( $items as $item )
        {
            if ( $item['state'] !== \expUrlMapPlan::STATE_UNKNOWN_HOST )
                continue;
            $host = \expUrlMapRow::parseUrl( (string)$item['old_url'] )['host'];
            if ( $host !== '' )
                $hosts[strtolower( $host )] = true;
        }
        return array_keys( $hosts );
    }

    public function run( array $scope )
    {
        $Params = $scope['Params'];

        $Module = $Params['Module'];
        $userParameters = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
        $userId = (int)\eZUser::currentUserID();
        $token = isset( $userParameters['plan'] ) ? (string)$userParameters['plan'] : '';
        $environment = new \expUrlMapKernelEnvironment();

        if ( $token === '' )
            return $this->viewResult( null, $this->uploadPage( $Module, $environment, $userId ) );

        $session = \expUrlMapImportSession::load( $token, $userId );
        if ( !$session )
            return $this->viewResult( null, $this->uploadPage( $Module, $environment, $userId, 'error-expired' ) );

        $report = isset( $userParameters['report'] ) ? (string)$userParameters['report'] : '';
        if ( $report === 'csv' || $report === 'json' )
            $this->sendReport( $session, $report );

        $state = self::stateKey( isset( $userParameters['state'] ) ? $userParameters['state'] : '' );
        $offset = isset( $Params['Offset'] ) && is_numeric( $Params['Offset'] ) ? (int)$Params['Offset'] : 0;
        $plan = $session->plan();
        $error = '';
        $errorText = '';
        if ( !$plan )
        {
            try
            {
                $plan = $session->makePlan();
            }
            catch ( \expUrlMapFormatException $e )
            {
                return $this->viewResult( null, $this->uploadPage( $Module, $environment, $userId, 'error-format', $e->getMessage() ) );
            }
        }
        $done = $session->result( 'result' );

        if ( \expViewParameters::isPost() && !$done )
        {
            $http = \eZHTTPTool::instance();
            if ( $http->hasPostVariable( 'CancelImportButton' ) )
            {
                $session->remove();
                \expViewParameters::redirect( 'content/urlimport' );
            }
            if ( $http->hasPostVariable( 'RowIndexList' ) )
            {
                self::applyDecisions( $plan, $http->postVariable( 'RowIndexList' ), $http->hasPostVariable( 'ImportRowList' ) ? $http->postVariable( 'ImportRowList' ) : array() );
                $session->savePlan( $plan );
            }
            if ( $http->hasPostVariable( 'ReplanButton' ) )
            {
                $options = $session->options();
                $mapped = $http->hasPostVariable( 'HostMap' ) ? (array)$http->postVariable( 'HostMap' ) : array();
                foreach ( $mapped as $host => $siteaccess )
                {
                    if ( is_string( $siteaccess ) && in_array( $siteaccess, $environment->siteaccesses(), true ) )
                        $options['hosts'][strtolower( (string)$host )] = $siteaccess;
                }
                $onConflict = $http->hasPostVariable( 'OnConflict' ) ? (string)$http->postVariable( 'OnConflict' ) : $options['on_conflict'];
                $options['on_conflict'] = $onConflict === \expUrlMapPlan::ON_CONFLICT_OVERWRITE ? $onConflict : \expUrlMapPlan::ON_CONFLICT_SKIP;
                try
                {
                    $session->makePlan( $options );
                }
                catch ( \expUrlMapFormatException $e )
                {
                    $error = 'error-format';
                    $errorText = $e->getMessage();
                }
                if ( $error === '' )
                    \expViewParameters::redirect( self::planURL( $token, $state ) );
            }
            elseif ( $http->hasPostVariable( 'DryRunButton' ) || $http->hasPostVariable( 'ImportButton' ) )
            {
                $dryRun = $http->hasPostVariable( 'DryRunButton' );
                $result = null;
                if ( !$plan->actionable() )
                    $error = 'error-nothing';
                else
                {
                    try
                    {
                        $result = ( new \expUrlMapImporter( array( 'user_id' => $userId ) ) )->apply( $plan, array( 'dry_run' => $dryRun ) );
                    }
                    catch ( \RuntimeException $e )
                    {
                        $error = 'error-import';
                        $errorText = $e->getMessage();
                    }
                }
                if ( $result !== null )
                {
                    $session->saveResult( $dryRun ? 'dryrun' : 'result', $result );
                    \expViewParameters::redirect( self::planURL( $token, $dryRun ? $state : 'all' ) );
                }
            }
            elseif ( $error === '' )
                \expViewParameters::redirect( self::planURL( $token, $state, $offset ) );
        }

        $items = $plan->items();
        $page = self::page( $items, $state, $offset );
        $counts = $plan->counts();
        $meta = $session->meta();

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'import_token', $token );
        $tpl->setVariable( 'import_meta', array( 'source_name' => $meta['source_name'], 'format' => $meta['format'], 'size' => (int)$meta['size'],
                                                  'created' => (int)$meta['created'], 'expires' => (int)$meta['created'] + \expUrlMapImportSession::TTL ) );
        $tpl->setVariable( 'import_options', $session->options() );
        $tpl->setVariable( 'import_counts', $counts );
        $tpl->setVariable( 'import_states', \expUrlMapPlan::states() );
        $tpl->setVariable( 'import_state', $state );
        $tpl->setVariable( 'import_rows', $page['rows'] );
        $tpl->setVariable( 'import_row_count', $page['count'] );
        $tpl->setVariable( 'import_limit', self::PAGE_SIZE );
        $tpl->setVariable( 'import_page_uri', self::planURL( $token, $state ) );
        $tpl->setVariable( 'import_unknown_hosts', self::unknownHosts( $items ) );
        $tpl->setVariable( 'import_siteaccesses', $environment->siteaccesses() );
        $tpl->setVariable( 'import_dry_run', $done ? null : $session->result( 'dryrun' ) );
        $tpl->setVariable( 'import_result', $done );
        $tpl->setVariable( 'import_error', $error );
        $tpl->setVariable( 'import_error_text', $errorText );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $page['offset'] ) );

        $Module->setTitle( \ezpI18n::tr( 'design/admin/content/urlimport', 'Import a URL map' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlimport_preview.tpl' );
        $Result['path'] = self::path( \ezpI18n::tr( 'design/admin/content/urlimport', 'Preview' ) );
        return $this->viewResult( $Result, null );
    }

    /**
     * The breadcrumb: URL translator > Import a URL map [> $last].
     *
     * @param string|null $last
     * @return array
     */
    public static function path( $last = null )
    {
        $path = array( array( 'url' => '/content/urltranslator', 'text' => \ezpI18n::tr( 'kernel/content/urlalias_global', 'Global URL aliases' ) ),
                       array( 'url' => $last === null ? false : '/content/urlimport', 'text' => \ezpI18n::tr( 'design/admin/content/urlimport', 'Import a URL map' ) ) );
        if ( $last !== null )
            $path[] = array( 'url' => false, 'text' => $last );
        return $path;
    }

    /**
     * The first page: the file and the options. A posted file (PreviewButton) is kept and planned, then the browser
     * goes on to its preview.
     *
     * @param \eZModule $Module
     * @param \expUrlMapKernelEnvironment $environment
     * @param int $userId
     * @param string $error
     * @param string $errorText
     * @return array the view's result
     */
    protected function uploadPage( $Module, \expUrlMapKernelEnvironment $environment, $userId, $error = '', $errorText = '' )
    {
        $http = \eZHTTPTool::instance();
        $siteaccesses = $environment->siteaccesses();
        $form = array( 'format' => '', 'siteaccess' => $environment->defaultSiteaccess(), 'language' => '', 'on_conflict' => \expUrlMapPlan::ON_CONFLICT_SKIP,
                       'hosts' => '', 'text' => '' );
        if ( \expViewParameters::isPost() && $http->hasPostVariable( 'PreviewButton' ) )
        {
            foreach ( array( 'format' => 'Format', 'siteaccess' => 'SiteAccess', 'language' => 'Language', 'on_conflict' => 'OnConflict',
                             'hosts' => 'Hosts', 'text' => 'UrlMapText' ) as $key => $field )
                $form[$key] = $http->hasPostVariable( $field ) && is_string( $http->postVariable( $field ) ) ? (string)$http->postVariable( $field ) : $form[$key];
            $content = null;
            $name = '';
            $upload = \eZHTTPFile::canFetch( 'UrlMapFile', \expUrlMapImportSession::MAX_SIZE );
            if ( $upload === \eZHTTPFile::UPLOADEDFILE_OK )
            {
                $file = \eZHTTPFile::fetch( 'UrlMapFile' );
                $content = $file ? (string)@file_get_contents( $file->attribute( 'filename' ) ) : null;
                $name = $file ? basename( str_replace( '\\', '/', (string)$file->attribute( 'original_filename' ) ) ) : '';
            }
            elseif ( $upload === \eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE || $upload === \eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
                $error = 'error-too-large';
            elseif ( trim( $form['text'] ) !== '' )
            {
                $content = $form['text'];
                $name = '';
            }
            if ( $error === '' && ( $content === null || trim( $content ) === '' ) )
                $error = 'error-no-file';
            if ( $error === '' && strlen( $content ) > \expUrlMapImportSession::MAX_SIZE )
                $error = 'error-too-large';
            $format = '';
            if ( $error === '' )
            {
                $format = in_array( $form['format'], \expUrlMapFormats::readable(), true ) ? $form['format']
                        : (string)\expUrlMapFormats::detect( $name !== '' ? $name : 'pasted', substr( $content, 0, 65536 ) );
                if ( $form['siteaccess'] !== '' && !in_array( $form['siteaccess'], $siteaccesses, true ) )
                    $error = 'error-siteaccess';
            }
            $session = null;
            if ( $error === '' )
            {
                try
                {
                    $session = \expUrlMapImportSession::create( $userId, $content, $name !== '' ? $name : \ezpI18n::tr( 'design/admin/content/urlimport', 'Pasted text' ), $format,
                                                                array( 'siteaccess' => $form['siteaccess'], 'language' => $form['language'], 'on_conflict' => $form['on_conflict'],
                                                                       'hosts' => \expUrlMapImportSession::parseHosts( $form['hosts'] ) ) );
                    $session->makePlan();
                }
                catch ( \expUrlMapFormatException $e )
                {
                    $error = 'error-format';
                    $errorText = $e->getMessage();
                    if ( $session )
                        $session->remove();
                    $session = null;
                }
                catch ( \RuntimeException $e )
                {
                    $error = 'error-store';
                    $errorText = $e->getMessage();
                    $session = null;
                }
            }
            if ( $session )
                \expViewParameters::redirect( self::planURL( $session->token() ) );
        }

        $languages = array();
        foreach ( \eZContentLanguage::fetchList() as $language )
            $languages[] = array( 'locale' => (string)$language->attribute( 'locale' ), 'name' => (string)$language->attribute( 'name' ) );
        $formats = array();
        foreach ( \expUrlMapFormats::definitions() as $name => $definition )
            if ( $definition['reader'] !== null )
                $formats[] = array( 'name' => $name, 'label' => $definition['label'] );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'import_form', $form );
        $tpl->setVariable( 'import_formats', $formats );
        $tpl->setVariable( 'import_siteaccesses', $siteaccesses );
        $tpl->setVariable( 'import_default_siteaccess', $environment->defaultSiteaccess() );
        $tpl->setVariable( 'import_languages', $languages );
        $tpl->setVariable( 'import_max_size', \expUrlMapImportSession::MAX_SIZE );
        $tpl->setVariable( 'import_error', $error );
        $tpl->setVariable( 'import_error_text', $errorText );
        $tpl->setVariable( 'import_tables', \expUrlMapBatch::tablesExist() );

        $Module->setTitle( \ezpI18n::tr( 'design/admin/content/urlimport', 'Import a URL map' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlimport.tpl' );
        $Result['path'] = self::path();
        return $Result;
    }

    /**
     * Sends the plan, row by row, as a CSV or JSON download and ends the request.
     *
     * @param \expUrlMapImportSession $session
     * @param string $format csv or json
     */
    protected function sendReport( \expUrlMapImportSession $session, $format )
    {
        $plan = $session->plan();
        if ( !$plan )
            return;
        $data = $plan->toArray();
        if ( $format === 'csv' )
        {
            $body = "\xEF\xBB\xBF";
            foreach ( $plan->reportTable() as $cells )
                $body .= implode( ',', array_map( function ( $cell ) { return \expUrlMapCsvWriter::field( (string)$cell, ',' ); }, $cells ) ) . "\r\n";
        }
        else
        {
            $result = $session->result( 'result' );
            if ( $result )
                $data['result'] = $result;
            $body = json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR ) . "\n";
        }
        $name = 'url-map-plan-' . substr( $session->token(), 0, 8 ) . '.' . $format;
        while ( @ob_end_clean() );
        header( 'Content-Type: ' . ( $format === 'csv' ? 'text/csv' : 'application/json' ) . '; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( 'Content-Length: ' . strlen( $body ) );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'X-Content-Type-Options: nosniff' );
        echo $body;
        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        \eZExecution::cleanExit();
    }
}

}
