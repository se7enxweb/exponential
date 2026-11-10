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
 *   /content/urlimport/(source)/sitemap|404|suggest          the helpers (since 6.0.15, U4): a map started from the old
 *                                                            sitemap (an address of a public host, or a file), from
 *                                                            this site's 404 log, or a map whose rows lack targets;
 *                                                            targets suggested (expUrlMapSuggester); downloaded as CSV
 *                                                            or planned as an import to preview
 *   /content/urlimport/(source)/examples                     Formats and examples: an example file of each format to
 *                                                            look at and copy, the columns and the kinds
 *                                                            (expUrlMapExamples, the files of the documentation)
 *   /content/urlimport/(example)/<id>                        an example file as a download
 *   /content/urlimport/(use)/<id>                            the first page with a text example in the form
 *
 * After an import, TestButton on the preview requests every old address on the site of its siteaccess
 * (expUrlMapTester, the first TEST_MAX_ROWS rows) and keeps the result with the import (test.json).
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

    /** The helper tabs of the first page: /content/urlimport/(source)/<source> */
    const SOURCES = array( 'sitemap', '404', 'suggest' );

    /** The most rows a helper makes in the administration (the console has no limit) */
    const HELPER_MAX_ROWS = 5000;

    /** The most rows the test after an import requests, and the rows it lists */
    const TEST_MAX_ROWS = 500;
    const TEST_LIST_ROWS = 100;

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
        {
            $locale = \eZLocale::instance()->localeCode();
            $example = isset( $userParameters['example'] ) ? (string)$userParameters['example'] : '';
            if ( $example !== '' )
            {
                $examplePath = \expUrlMapExamples::path( $example, $locale );
                if ( $examplePath === '' )
                    return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
                $this->sendExample( $example, $examplePath );
            }
            $source = isset( $userParameters['source'] ) ? (string)$userParameters['source'] : '';
            if ( $source === 'examples' )
                return $this->viewResult( null, $this->examplesPage( $Module, $locale ) );
            if ( in_array( $source, self::SOURCES, true ) )
                return $this->viewResult( null, $this->helperPage( $Module, $environment, $userId, $source ) );
            $use = isset( $userParameters['use'] ) ? \expUrlMapExamples::forImport( (string)$userParameters['use'], $locale ) : null;
            return $this->viewResult( null, $this->uploadPage( $Module, $environment, $userId, '', '', $use ) );
        }

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

        // after the import: request every old address on the site and compare where it lands (exp:url:test)
        if ( \expViewParameters::isPost() && $done && \eZHTTPTool::instance()->hasPostVariable( 'TestButton' ) )
        {
            $session->saveResult( 'test', self::runTest( $session, $environment ) );
            \expViewParameters::redirect( self::planURL( $token ) );
        }

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
        $tpl->setVariable( 'import_test', $done ? $session->result( 'test' ) : null );
        $tpl->setVariable( 'import_test_base', $done ? self::siteBase( (string)$session->options()['siteaccess'], $environment ) : '' );
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
     * @param array|null $example an example to start from (expUrlMapExamples::forImport()): its text and format
     * @return array the view's result
     */
    protected function uploadPage( $Module, \expUrlMapKernelEnvironment $environment, $userId, $error = '', $errorText = '', $example = null )
    {
        $http = \eZHTTPTool::instance();
        $siteaccesses = $environment->siteaccesses();
        $form = array( 'format' => '', 'siteaccess' => $environment->defaultSiteaccess(), 'language' => '', 'on_conflict' => \expUrlMapPlan::ON_CONFLICT_SKIP,
                       'hosts' => '', 'text' => '' );
        if ( $example )
        {
            $form['format'] = $example['format'];
            $form['text'] = $example['text'];
        }
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
     * The tab Formats and examples: an example file of each format to look at, copy, download or open in the import,
     * the columns and the kinds.
     *
     * @param \eZModule $Module
     * @param string $locale
     * @return array the view's result
     */
    protected function examplesPage( $Module, $locale )
    {
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'examples', \expUrlMapExamples::all( $locale ) );
        $tpl->setVariable( 'example_columns', \expUrlMapRow::COLUMNS );
        $tpl->setVariable( 'import_max_size', \expUrlMapImportSession::MAX_SIZE );

        $Module->setTitle( \ezpI18n::tr( 'design/admin/content/urlimport', 'Formats and examples' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlimport_examples.tpl' );
        $Result['path'] = self::path( \ezpI18n::tr( 'design/admin/content/urlimport', 'Formats and examples' ) );
        return $Result;
    }

    /**
     * Sends an example file as a download (content/urlimport/(example)/<id>) and ends the request.
     *
     * @param string $id one of expUrlMapExamples::definitions()
     * @param string $path its file (expUrlMapExamples::path())
     */
    protected function sendExample( $id, $path )
    {
        $definitions = \expUrlMapFormats::definitions();
        $format = \expUrlMapExamples::definitions()[$id]['format'];
        $mime = $definitions[$format]['mime'];
        while ( @ob_end_clean() );
        header( 'Content-Type: ' . $mime . ( \expUrlMapExamples::isText( $format ) ? '; charset=utf-8' : '' ) );
        header( 'Content-Disposition: attachment; filename="' . basename( $path ) . '"' );
        header( 'Content-Length: ' . filesize( $path ) );
        header( 'X-Content-Type-Options: nosniff' );
        readfile( $path );
        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        \eZExecution::cleanExit();
    }

    /**
     * The address of a helper tab.
     *
     * @param string $source sitemap, 404 or suggest
     * @return string module-relative
     */
    public static function sourceURL( $source )
    {
        return 'content/urlimport/(source)/' . $source;
    }

    /**
     * The fields of a helper form from what was posted, within their limits. Pure.
     *
     * @param array $post field => value (Address, Limit, AnyHost, MinHits, Days, Suggest, FillAbove, SiteAccess)
     * @param string[] $siteaccesses
     * @param string $defaultSiteaccess
     * @return array address, limit, any_host, min_hits, days, suggest, fill_above, siteaccess
     */
    public static function helperForm( array $post, array $siteaccesses, $defaultSiteaccess )
    {
        $int = function ( $name, $default, $min, $max ) use ( $post )
        {
            if ( !isset( $post[$name] ) || !is_scalar( $post[$name] ) || !preg_match( '/^\s*\d+\s*$/', (string)$post[$name] ) )
                return $default;
            return max( $min, min( $max, (int)$post[$name] ) );
        };
        $siteaccess = isset( $post['SiteAccess'] ) && is_string( $post['SiteAccess'] ) && in_array( $post['SiteAccess'], $siteaccesses, true ) ? $post['SiteAccess'] : (string)$defaultSiteaccess;
        $address = isset( $post['Address'] ) && is_string( $post['Address'] ) ? trim( $post['Address'] ) : '';
        return array( 'address' => mb_substr( $address, 0, 2000 ),
                      'limit' => $int( 'Limit', 1000, 1, self::HELPER_MAX_ROWS ),
                      'any_host' => !empty( $post['AnyHost'] ),
                      'min_hits' => $int( 'MinHits', 2, 1, 1000000 ),
                      'days' => $int( 'Days', 30, 1, 3650 ),
                      'suggest' => !empty( $post['Suggest'] ),
                      'fill_above' => $int( 'FillAbove', 90, 0, 100 ),
                      'siteaccess' => $siteaccess );
    }

    /**
     * The address of a site from its SiteURL (site.ini [SiteSettings]), with the scheme of the request. Pure.
     *
     * @param string $siteURL www.example.com, www.example.com/site or a full address
     * @param bool $ssl
     * @return string '' without a SiteURL
     */
    public static function baseFromSiteURL( $siteURL, $ssl )
    {
        $siteURL = trim( (string)$siteURL );
        if ( $siteURL === '' )
            return '';
        if ( !preg_match( '#^https?://#i', $siteURL ) )
            $siteURL = ( $ssl ? 'https://' : 'http://' ) . ltrim( $siteURL, '/' );
        return rtrim( $siteURL, '/' );
    }

    /**
     * The address of a siteaccess's site, which the test after an import requests.
     *
     * @param string $siteaccess
     * @param \expUrlMapKernelEnvironment $environment
     * @return string
     */
    protected static function siteBase( $siteaccess, \expUrlMapKernelEnvironment $environment )
    {
        $siteaccess = $siteaccess !== '' ? $siteaccess : $environment->defaultSiteaccess();
        $ini = in_array( $siteaccess, $environment->siteaccesses(), true ) ? \eZSiteAccess::getIni( $siteaccess, 'site.ini' ) : null;
        $ini = $ini ?: \eZINI::instance();
        return self::baseFromSiteURL( $ini->hasVariable( 'SiteSettings', 'SiteURL' ) ? (string)$ini->variable( 'SiteSettings', 'SiteURL' ) : '', \eZSys::isSSLNow() );
    }

    /**
     * Tests the rows of an import on the site of its siteaccess (expUrlMapTester), the first TEST_MAX_ROWS.
     *
     * @param \expUrlMapImportSession $session
     * @param \expUrlMapKernelEnvironment $environment
     * @return array base, counts, rows (the first TEST_LIST_ROWS that did not land), tested, limited, error
     */
    protected static function runTest( \expUrlMapImportSession $session, \expUrlMapKernelEnvironment $environment )
    {
        $siteaccess = (string)$session->options()['siteaccess'];
        $base = self::siteBase( $siteaccess, $environment );
        $out = array( 'base' => $base, 'counts' => null, 'rows' => array(), 'tested' => 0, 'limited' => false, 'error' => '' );
        if ( $base === '' )
        {
            $out['error'] = 'no-base';
            return $out;
        }
        try
        {
            $rows = array_values( array_filter( $session->rows(), function ( $row ) { return $row->newUrl() !== ''; } ) );
        }
        catch ( \expUrlMapFormatException $e )
        {
            $out['error'] = 'no-rows';
            return $out;
        }
        $out['limited'] = count( $rows ) > self::TEST_MAX_ROWS;
        $rows = array_slice( $rows, 0, self::TEST_MAX_ROWS );
        $out['tested'] = count( $rows );
        $prefix = $environment->pathPrefix( $siteaccess !== '' ? $siteaccess : $environment->defaultSiteaccess() );
        $tester = new \expUrlMapTester( $base, array(
            'concurrency' => 8, 'max_redirects' => 5, 'equivalent_prefixes' => $prefix['prefix'] !== '' ? array( '/' . $prefix['prefix'] ) : array(),
            'resolver' => function ( $target )
            {
                $node = \eZContentObjectTreeNode::fetch( (int)substr( (string)$target, 5 ) );
                if ( !$node instanceof \eZContentObjectTreeNode )
                    return null;
                $url = (string)$node->urlAlias();
                \eZURI::transformURI( $url, false, 'relative' );
                return '/' . ltrim( $url, '/' );
            } ), new \expUrlMapHttp( array( 'timeout' => 10, 'with_body' => false ) ) );
        $result = $tester->test( $rows );
        $out['counts'] = $result['counts'];
        foreach ( $result['results'] as $r )
        {
            if ( in_array( $r['state'], array( \expUrlMapTester::LANDED, \expUrlMapTester::SKIPPED ), true ) )
                continue;
            if ( count( $out['rows'] ) >= self::TEST_LIST_ROWS )
                break;
            $out['rows'][] = array( 'line' => $r['line'], 'old_url' => $r['old_url'], 'new_url' => $r['new_url'], 'state' => $r['state'],
                                    'status' => $r['status'], 'landed' => $r['landed'], 'message' => $r['message'] );
        }
        return $out;
    }

    /**
     * A helper tab: rows from the old sitemap, from the 404 log, or from a map whose rows lack targets; with
     * suggested targets; as a CSV download (DownloadMapButton) or as an import to preview (PreviewMapButton).
     *
     * @param \eZModule $Module
     * @param \expUrlMapKernelEnvironment $environment
     * @param int $userId
     * @param string $source sitemap, 404 or suggest
     * @return array the view's result
     */
    protected function helperPage( $Module, \expUrlMapKernelEnvironment $environment, $userId, $source )
    {
        $http = \eZHTTPTool::instance();
        $siteaccesses = $environment->siteaccesses();
        $form = self::helperForm( array( 'Suggest' => 1 ), $siteaccesses, $environment->defaultSiteaccess() );
        $error = '';
        $errorText = '';
        $download = \expViewParameters::isPost() && $http->hasPostVariable( 'DownloadMapButton' );
        if ( $download || ( \expViewParameters::isPost() && $http->hasPostVariable( 'PreviewMapButton' ) ) )
        {
            $post = array();
            foreach ( array( 'Address', 'Limit', 'AnyHost', 'MinHits', 'Days', 'Suggest', 'FillAbove', 'SiteAccess' ) as $field )
                if ( $http->hasPostVariable( $field ) )
                    $post[$field] = $http->postVariable( $field );
            $form = self::helperForm( $post, $siteaccesses, $environment->defaultSiteaccess() );
            $rows = null;
            try
            {
                $rows = $this->helperRows( $source, $form, $error );
            }
            catch ( \expUrlMapFormatException $e )
            {
                $error = 'error-read';
                $errorText = $e->getMessage();
            }
            if ( $error === '' && !$rows )
                $error = 'error-empty';
            if ( $error === '' && ( $form['suggest'] || $source === 'suggest' ) )
            {
                $suggester = new \expUrlMapSuggester( \expUrlMapSuggester::candidatesFromDatabase( array( 'siteaccess' => $form['siteaccess'] ) ) );
                $rows = $suggester->apply( $rows, array( 'fill_above' => $form['fill_above'] ) )['rows'];
            }
            if ( $error === '' )
            {
                $csv = \expUrlMapFormats::writer( \expUrlMapFormats::CSV )->writeString( $rows );
                $name = 'url-map-' . ( $source === '404' ? 'not-found' : $source ) . '-' . date( 'Y-m-d' ) . '.csv';
                if ( $download )
                {
                    while ( @ob_end_clean() );
                    header( 'Content-Type: text/csv; charset=utf-8' );
                    header( 'Content-Disposition: attachment; filename="' . $name . '"' );
                    header( 'Content-Length: ' . strlen( $csv ) );
                    header( 'Cache-Control: private, no-store, max-age=0' );
                    header( 'X-Content-Type-Options: nosniff' );
                    echo $csv;
                    // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
                    \eZExecution::cleanExit();
                }
                $session = null;
                try
                {
                    $session = \expUrlMapImportSession::create( $userId, $csv, $name, \expUrlMapFormats::CSV,
                                                                array( 'siteaccess' => $form['siteaccess'], 'language' => '', 'on_conflict' => \expUrlMapPlan::ON_CONFLICT_SKIP,
                                                                       'hosts' => array() ) );
                    $session->makePlan();
                }
                catch ( \RuntimeException $e )
                {
                    $error = 'error-store';
                    $errorText = $e->getMessage();
                    $session = null;
                }
                // outside the try: under Velocity the redirect's exit throws
                if ( $session )
                    \expViewParameters::redirect( self::planURL( $session->token() ) );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'module', $Module );
        $tpl->setVariable( 'helper_source', $source );
        $tpl->setVariable( 'helper_form', $form );
        $tpl->setVariable( 'helper_max_rows', self::HELPER_MAX_ROWS );
        $tpl->setVariable( 'helper_max_size', \expUrlMapImportSession::MAX_SIZE );
        $tpl->setVariable( 'helper_log_enabled', \expRequestLog::enabled() );
        $tpl->setVariable( 'helper_siteaccesses', $siteaccesses );
        $tpl->setVariable( 'helper_default_siteaccess', $environment->defaultSiteaccess() );
        $tpl->setVariable( 'helper_error', $error );
        $tpl->setVariable( 'helper_error_text', $errorText );

        $Module->setTitle( \ezpI18n::tr( 'design/admin/content/urlimport', 'Import a URL map' ) );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/urlimport_helper.tpl' );
        $Result['path'] = self::path( self::sourceTitle( $source ) );
        return $Result;
    }

    /**
     * The title of a helper tab.
     *
     * @param string $source
     * @return string
     */
    public static function sourceTitle( $source )
    {
        switch ( $source )
        {
            case 'sitemap':
                return \ezpI18n::tr( 'design/admin/content/urlimport', 'From the old sitemap' );
            case '404':
                return \ezpI18n::tr( 'design/admin/content/urlimport', 'From the 404 log' );
            default:
                return \ezpI18n::tr( 'design/admin/content/urlimport', 'Suggest targets' );
        }
    }

    /**
     * The rows a helper reads: the old sitemap (an address of a public host, or an uploaded file), this site's 404
     * log, or an uploaded map.
     *
     * @param string $source
     * @param array $form as helperForm() gives it
     * @param string $error set to error-no-sitemap, error-no-file or error-too-large
     * @return \expUrlMapRow[]|null
     * @throws \expUrlMapFormatException
     */
    protected function helperRows( $source, array $form, &$error )
    {
        $field = $source === 'sitemap' ? 'SitemapFile' : 'UrlMapFile';
        $upload = $source === '404' ? null : \eZHTTPFile::canFetch( $field, \expUrlMapImportSession::MAX_SIZE );
        if ( $upload === \eZHTTPFile::UPLOADEDFILE_EXCEEDS_MAX_SIZE || $upload === \eZHTTPFile::UPLOADEDFILE_EXCEEDS_PHP_LIMIT )
        {
            $error = 'error-too-large';
            return null;
        }
        $file = $upload === \eZHTTPFile::UPLOADEDFILE_OK ? \eZHTTPFile::fetch( $field ) : null;
        if ( $source === '404' )
        {
            $reader = new \expUrlMap404Source( array( 'min_hits' => $form['min_hits'], 'since' => time() - $form['days'] * 86400, 'limit' => $form['limit'] ) );
            return $reader->rows( \expUrlMap404Source::logFiles() );
        }
        if ( $source === 'sitemap' )
        {
            if ( !$file && $form['address'] === '' )
            {
                $error = 'error-no-sitemap';
                return null;
            }
            // an address typed here is fetched from public hosts only (no address of the server's own network)
            $sitemap = new \expUrlMapSitemapSource( array( 'limit' => $form['limit'], 'any_host' => $form['any_host'], 'max_sitemaps' => 50,
                                                           'max_bytes' => 20971520 ),
                                                    new \expUrlMapHttp( array( 'timeout' => 15, 'public_only' => true, 'max_bytes' => 20971520 ) ) );
            if ( $file )
                return $sitemap->rows( (string)$file->attribute( 'filename' ) );
            if ( !preg_match( '#^https?://#i', $form['address'] ) )
                throw new \expUrlMapFormatException( 'not an http or https address: ' . $form['address'] );
            return $sitemap->rows( $form['address'] );
        }
        if ( !$file )
        {
            $error = 'error-no-file';
            return null;
        }
        $path = (string)$file->attribute( 'filename' );
        $name = basename( str_replace( '\\', '/', (string)$file->attribute( 'original_filename' ) ) );
        $rows = \expUrlMapFormats::readFile( $path, \expUrlMapFormats::detect( $name, (string)file_get_contents( $path, false, null, 0, 65536 ) ) );
        return array_slice( $rows, 0, self::HELPER_MAX_ROWS );
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
