<?php
/**
 * The code of kernel/setup/extensions.php, moved into a class (#207 stage 1). The file kernel/setup/extensions.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * Setup > Extensions: one list of every extension, in which the active ones are moved, activated and deactivated
 * and the result is written to ActiveExtensions in one step after a review (doc/guides/extensions-page.md). The view
 * is thin: what is known about each extension comes from expExtensionCatalogue, what a change means from
 * expExtensionChangePlan, and the write from ezpActiveExtensions.
 */
/*
 * The original header of kernel/setup/extensions.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'updateAutoload' ) ) {
function updateAutoload( $tpl = null )
{
    $autoloadGenerator = new eZAutoloadGenerator();
    $warnings = array();
    try
    {
        $autoloadGenerator->buildAutoloadArrays();

        $messages = $autoloadGenerator->getMessages();
        foreach( $messages as $message )
        {
            eZDebug::writeNotice( $message, 'eZAutoloadGenerator' );
        }

        $warnings = $autoloadGenerator->getWarnings();
        foreach ( $warnings as &$warning )
        {
            eZDebug::writeWarning( $warning, "eZAutoloadGenerator" );

            // For web output we want to mark some of the important parts of
            // the message
            $pattern = '@^Class\s+(\w+)\s+.* file\s(.+\.php).*\n(.+\.php)\s@';
            preg_match( $pattern, $warning, $m );

            if ( isset( $m[1], $m[2], $m[3] ) )
            {
                $warning = str_replace( $m[1], '<strong>'.$m[1].'</strong>', $warning );
                $warning = str_replace( $m[2], '<em>'.$m[2].'</em>', $warning );
                $warning = str_replace( $m[3], '<em>'.$m[3].'</em>', $warning );
            }
        }
        unset( $warning );

        if ( $tpl !== null )
        {
            $tpl->setVariable( 'warning_messages', $warnings );
        }
    }
    catch ( Exception $e )
    {
        eZDebug::writeError( $e->getMessage() );
        $warnings[] = $e->getMessage();
    }
    return $warnings;
}
}
}

namespace Exponential\View\Kernel\Setup
{

class Extensions extends \Exponential\Runnable\ModuleView
{
    /** The session key of the notice shown once after a change was applied. */
    const NOTICE = 'ExpSetupExtensionsNotice';

    /** Filters of the list. */
    public static $filters = array( 'all', 'active', 'inactive', 'problems' );

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $module = $Params['Module'];

        // Direct extension download via view parameters: /setup/extensions/<name>/<format>
        $downloadName   = isset( $Params['ExtensionName'] ) ? $Params['ExtensionName'] : false;
        $downloadFormat = isset( $Params['ExtensionFormat'] ) ? strtolower( $Params['ExtensionFormat'] ) : false;
        $validFormats   = array( 'tar.gz', 'tar.bz2', 'zip', 'ezpkg' );

        if ( $downloadName && $downloadFormat &&
             in_array( $downloadFormat, $validFormats ) &&
             \eZExtension::extensionPath( $downloadName ) !== false )
        {
            $temporaryExportPath = \eZPackage::temporaryExportPath();
            $archiveFile         = $temporaryExportPath . '/' . $downloadName . '.' . $downloadFormat;

            $package = \eZPackage::create( $downloadName . '_' . time() );
            $package->setAttribute( 'is_active', true );
            \eZPackage::packageHandler( 'ezextension' )->addExtension( $package, $downloadName );
            $package->exportToArchive( $archiveFile, $downloadFormat );

            $mimeTypes = array(
                'tar.gz'  => 'application/x-gzip',
                'tar.bz2' => 'application/x-bzip2',
                'zip'     => 'application/zip',
                'ezpkg'   => 'application/octet-stream',
            );
            $contentType = isset( $mimeTypes[$downloadFormat] ) ? $mimeTypes[$downloadFormat] : 'application/octet-stream';
            $downloadFileName = preg_replace( '/[^a-zA-Z0-9_-]/', '_', $downloadName ) . '.' . $downloadFormat;

            if ( file_exists( $archiveFile ) )
            {
                header( 'Content-Type: ' . $contentType );
                header( 'Content-Disposition: attachment; filename="' . $downloadFileName . '"' );
                header( 'Content-Length: ' . filesize( $archiveFile ) );
                readfile( $archiveFile );
                unlink( $archiveFile );
                $package->removeFiles( $package->path() );
                \eZExecution::cleanExit();
            }
        }

        // The JSON reorder of the former loading order card (6.0.15 before the redesign), kept for anything that
        // still posts it: it changes only the order, and only when the list holds exactly the active extensions.
        if ( $http->hasPostVariable( 'ReorderExtensions' ) )
        {
            $this->legacyReorder( $http );
        }

        $tpl = \eZTemplate::factory();
        $current = \ezpActiveExtensions::current();
        $catalogue = \expExtensionCatalogue::gather( $current );
        $available = array_keys( $catalogue->info );

        $siteINI = \eZINI::instance();
        $selectedAccessExtensionArray = (array)$siteINI->variable( 'ExtensionSettings', 'ActiveAccessExtensions' );

        // The form of the former page and of the standard design: the ticked boxes, written at once as before.
        if ( $module->isCurrentAction( 'ActivateExtensions' ) && !$http->hasPostVariable( 'ExtensionPlanForm' ) )
        {
            $checked = $http->hasPostVariable( 'ActiveExtensionList' ) ? (array)$http->postVariable( 'ActiveExtensionList' ) : array();
            $shown = $http->hasPostVariable( 'ShownExtensionList' ) ? (array)$http->postVariable( 'ShownExtensionList' ) : $available;
            $list = \ezpActiveExtensions::merge( $current, $checked, $shown, $available, $selectedAccessExtensionArray );
            $notice = $this->applyList( $current, $list, $tpl );
            if ( $notice['ok'] )
            {
                $tpl->setVariable( 'save_message', $notice['message'] );
                $current = \ezpActiveExtensions::current();
                $catalogue = \expExtensionCatalogue::gather( $current );
            }
            else
            {
                $tpl->setVariable( 'save_error', $notice['error'] );
            }
        }

        if ( $module->isCurrentAction( 'GenerateAutoloadArrays' ) )
        {
            updateAutoload( $tpl );
            $tpl->setVariable( 'save_message', \ezpI18n::tr( 'design/admin/setup/extensions', 'The autoload arrays of the extensions were regenerated.' ) );
        }

        // The plan the form carries: the order of the active extensions as edited so far. Without one, the file.
        $planned = $current;
        $base = \expExtensionChangePlan::fingerprint( $current );
        $stale = false;
        if ( $http->hasPostVariable( 'ExtensionPlanForm' ) )
        {
            if ( $http->hasPostVariable( 'ExtensionDiscardButton' ) )
                return $this->viewResult( null, $module->redirectToView( 'extensions' ) );
            $postedBase = (string)$http->postVariable( 'ExtensionBase', '' );
            $planned = \expExtensionChangePlan::fromPost( $http->postVariable( 'ExtensionPlan', array() ), array_merge( $available, $current ) );
            if ( $postedBase !== $base )
            {
                // Changed elsewhere since the form was drawn: start again from the file, and say so.
                $stale = true;
                $planned = $current;
            }
            $action = (string)$http->postVariable( 'ExtensionAction', '' );
            if ( !$stale && preg_match( '/^(up|down|top|bottom|activate|deactivate):(.+)$/', $action, $m ) && in_array( $m[2], array_merge( $available, $current ), true ) )
                $planned = \expExtensionChangePlan::apply( $planned, $m[1], $m[2], $catalogue->facts );
        }

        $plan = $catalogue->plan( $current, $planned );
        $risks = $plan->risks();
        $needsAck = \expExtensionChangePlan::needsAcknowledgement( $risks );
        $review = !$stale && $plan->changed() && ( $http->hasPostVariable( 'ExtensionReviewButton' ) || $http->hasPostVariable( 'ExtensionApplyButton' ) );

        if ( !$stale && $http->hasPostVariable( 'ExtensionApplyButton' ) && $plan->changed() )
        {
            if ( $needsAck && !$http->hasPostVariable( 'ExtensionAcknowledgeRisks' ) )
            {
                $tpl->setVariable( 'ack_missing', true );
            }
            else
            {
                $notice = $this->applyList( $current, $plan->planned, null, $plan->diff() );
                if ( $notice['ok'] )
                {
                    $http->setSessionVariable( self::NOTICE, $notice );
                    return $this->viewResult( null, $module->redirectToView( 'extensions' ) );
                }
                $tpl->setVariable( 'save_error', $notice['error'] );
            }
        }

        // The notice of the change just applied, once
        $notice = false;
        if ( $http->hasSessionVariable( self::NOTICE ) )
        {
            $notice = $http->sessionVariable( self::NOTICE );
            $http->removeSessionVariable( self::NOTICE );
            if ( is_array( $notice ) && !empty( $notice['warnings'] ) )
                $tpl->setVariable( 'warning_messages', $notice['warnings'] );
        }

        // Filter, search and sort: posted with the form (they keep the plan), or on the address
        $userParameters = isset( $Params['UserParameters'] ) ? (array)$Params['UserParameters'] : array();
        $filter = (string)$http->postVariable( 'ExtensionFilter', isset( $userParameters['show'] ) ? $userParameters['show'] : 'all' );
        $filter = in_array( $filter, self::$filters, true ) ? $filter : 'all';
        $search = trim( mb_substr( (string)$http->postVariable( 'ExtensionSearch', '' ), 0, 100 ) );
        $sort = (string)$http->postVariable( 'ExtensionSort', isset( $userParameters['sort'] ) ? $userParameters['sort']
                : ( $http->hasGetVariable( 'SortBy' ) ? strtolower( $http->getVariable( 'SortBy' ) ) : 'order' ) );
        $sort = $sort === 'name' ? 'name' : 'order';

        $rows = $catalogue->rows( $plan, $sort );
        foreach ( $rows as $i => $row )
        {
            foreach ( $row['problems'] as $j => $problem )
                $rows[$i]['problems'][$j]['text'] = self::problemText( $problem['code'], $problem );
            $rows[$i]['hidden'] = !self::matches( $row, $filter, $search );
        }

        $riskRows = array();
        foreach ( $risks as $risk )
            $riskRows[] = array( 'code' => $risk[0], 'severity' => $risk[1], 'text' => self::riskText( $risk[0], $risk[2] ) );

        $velocityRunning = null;
        try
        {
            if ( class_exists( 'expVelocity' ) )
                $velocityRunning = \expVelocity::create()->isRunning();
        }
        catch ( \Throwable $e )
        {
            $velocityRunning = null;
        }

        $tpl->setVariable( 'extension_rows', $rows );
        $tpl->setVariable( 'extension_summary', \expExtensionCatalogue::summary( $rows ) );
        $tpl->setVariable( 'extension_plan', array(
            'planned' => $plan->planned,
            'base' => $base,
            'changed' => $plan->changed(),
            'diff' => $plan->diff(),
            'risks' => $riskRows,
            'needs_ack' => $needsAck,
            'lines' => \expExtensionChangePlan::lines( $plan->planned ),
            'effective_moves' => count( array_diff_assoc( $plan->effectiveOrder(), $plan->planned ) ),
        ) );
        $tpl->setVariable( 'extension_review', $review );
        $tpl->setVariable( 'extension_stale', $stale );
        $tpl->setVariable( 'extension_filter', $filter );
        $tpl->setVariable( 'extension_search', $search );
        $tpl->setVariable( 'extension_sort_mode', $sort );
        $tpl->setVariable( 'extension_ordering', $catalogue->ordering );
        $tpl->setVariable( 'extension_access_map', $catalogue->accessBySiteaccess );
        $tpl->setVariable( 'extension_notice', $notice );
        $tpl->setVariable( 'velocity_running', $velocityRunning );
        $tpl->setVariable( 'settings_file', \ezpActiveExtensions::DIR . '/' . \ezpActiveExtensions::FILE );

        // The variables of the former page, kept for override templates written against it.
        $selectedExtensions = array_values( array_unique( array_merge( $current, $selectedAccessExtensionArray ) ) );
        $sortedNames = array_column( $rows, 'name' );
        $tpl->setVariable( 'available_extension_array', array_values( array_intersect( $sortedNames, $available ) ) );
        $tpl->setVariable( 'extension_count', count( $available ) );
        $tpl->setVariable( 'limit', count( $available ) );
        $tpl->setVariable( 'view_parameters', array( 'offset' => 0, 'sort' => $sort, 'dir' => 'asc' ) );
        $tpl->setVariable( 'extension_sort', array( 'field' => $sort, 'direction' => 'asc', 'opposite' => 'desc' ) );
        $tpl->setVariable( 'selected_extension_array', $selectedExtensions );
        $tpl->setVariable( 'access_extension_array', array_values( array_diff( $selectedAccessExtensionArray, $current ) ) );
        $tpl->setVariable( 'active_extension_order', $current );
        $tpl->setVariable( 'extension_positions', \ezpActiveExtensions::positions( $current ) );
        $tpl->setVariable( 'extension_info', $catalogue->info );
        $tpl->setVariable( 'sort_by', $sort );
        $tpl->setVariable( 'sort_order', 'asc' );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:setup/extensions.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/setup', 'Extension configuration' ) ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * Writes $list and does what a new list needs: the INI, override, design and active extension caches, the
     * siteaccess settings, the user info cache when the modules changed, and the autoload arrays.
     *
     * @return array ok, message or error, backup, warnings, velocity, diff
     */
    protected function applyList( array $current, array $list, $tpl = null, $diff = null )
    {
        $moduleINI = \eZINI::instance( 'module.ini' );
        $oldModules = $moduleINI->variable( 'ModuleSettings', 'ModuleList' );
        $writer = new \ezpActiveExtensions();
        if ( !$writer->write( $list ) )
            return array( 'ok' => false, 'error' => $writer->error );

        // The order decides which extension's settings, templates and design files win, so these caches are
        // built on it.
        \eZCache::clearByTag( 'ini' );
        \eZCache::clearByID( array( 'template-override', 'design_base', 'active_extensions' ) );
        \eZSiteAccess::reInitialise();
        $moduleINI = \eZINI::instance( 'module.ini' );
        if ( $moduleINI->variable( 'ModuleSettings', 'ModuleList' ) != $oldModules )
        {
            // evaluated policy wildcards in the user info cache follow the active modules
            \eZCache::clearByID( 'user_info_cache' );
        }
        $warnings = updateAutoload( $tpl );

        $velocity = null;
        try
        {
            if ( class_exists( 'expVelocity' ) )
                $velocity = \expVelocity::create()->isRunning();
        }
        catch ( \Throwable $e )
        {
            $velocity = null;
        }

        return array(
            'ok' => true,
            'message' => \ezpI18n::tr( 'design/admin/setup/extensions', 'The active extensions were saved; a copy of the previous settings is in %file.',
                                      null, array( '%file' => $writer->backup ) ),
            'backup' => $writer->backup,
            'warnings' => $warnings,
            'velocity' => $velocity,
            'diff' => $diff !== null ? $diff : array( 'added' => array_values( array_diff( $list, $current ) ),
                                                      'removed' => array_values( array_diff( $current, $list ) ), 'moved' => array() ),
        );
    }

    /**
     * The JSON answer to the former loading order card's reorder.
     */
    protected function legacyReorder( \eZHTTPTool $http )
    {
        $order = $http->hasPostVariable( 'ExtensionOrder' ) ? (array)$http->postVariable( 'ExtensionOrder' ) : array();
        $current = \ezpActiveExtensions::current();
        $list = \ezpActiveExtensions::reorder( $current, $order );
        $response = array( 'ok' => false );
        if ( $list === false )
        {
            $response['error'] = \ezpI18n::tr( 'design/admin/setup/extensions', 'The active extensions changed since this page was loaded. Reload the page and try again.' );
            $response['order'] = $current;
        }
        else if ( $list === $current )
        {
            $response = array( 'ok' => true, 'order' => $current, 'message' => '' );
        }
        else
        {
            $notice = $this->applyList( $current, $list );
            $response = $notice['ok']
                ? array( 'ok' => true, 'order' => \ezpActiveExtensions::current(),
                         'message' => \ezpI18n::tr( 'design/admin/setup/extensions', 'Loading order saved; a copy of the previous settings is in %file.',
                                                   null, array( '%file' => $notice['backup'] ) ) )
                : array( 'ok' => false, 'error' => $notice['error'], 'order' => \ezpActiveExtensions::current() );
        }
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: no-store' );
        echo json_encode( $response );
        \eZExecution::cleanExit();
    }

    /**
     * Whether a row is shown for a filter and a search.
     */
    public static function matches( array $row, $filter, $search )
    {
        if ( $filter === 'active' && !$row['active'] )
            return false;
        if ( $filter === 'inactive' && $row['active'] )
            return false;
        if ( $filter === 'problems' && $row['problem_level'] !== 'warn' && $row['problem_level'] !== 'bad' )
            return false;
        return $search === '' || mb_strpos( $row['search'], mb_strtolower( $search ) ) !== false;
    }

    public static function problemText( $code, array $p )
    {
        $other = isset( $p['other'] ) ? $p['other'] : '';
        $ctx = 'design/admin/setup/extensions';
        switch ( $code )
        {
            case 'not_installed':
                return \ezpI18n::tr( $ctx, 'Listed in ActiveExtensions, but its directory is missing.' );
            case 'requires_missing':
                return \ezpI18n::tr( $ctx, 'Requires %other, which is not active.', null, array( '%other' => $other ) );
            case 'requires_later':
                return \ezpI18n::tr( $ctx, 'Needs %other, which is written later in the list.', null, array( '%other' => $other ) );
            case 'extends_earlier':
                return \ezpI18n::tr( $ctx, 'Extends %other, which is written earlier in the list.', null, array( '%other' => $other ) );
            case 'settings_lose':
                return \ezpI18n::tr( $ctx, 'Changes %file, which %other ships in full and loads earlier: single values of %other win.', null,
                                     array( '%other' => $other, '%file' => isset( $p['file'] ) ? $p['file'] : '' ) );
        }
        return $code;
    }

    public static function riskText( $code, array $p )
    {
        $ctx = 'design/admin/setup/extensions';
        $name = isset( $p['name'] ) ? $p['name'] : '';
        $other = isset( $p['other'] ) ? $p['other'] : '';
        switch ( $code )
        {
            case 'removes_required':
                return \ezpI18n::tr( $ctx, '%name is deactivated, but %other stays active and requires it.', null, array( '%name' => $name, '%other' => $other ) );
            case 'removes_design':
                return \ezpI18n::tr( $ctx, '%name is deactivated, but the siteaccesses %siteaccesses use its design %design, which no other active extension provides.', null,
                                     array( '%name' => $name, '%design' => $p['design'], '%siteaccesses' => $p['siteaccesses'] ) );
            case 'removes_critical':
                return \ezpI18n::tr( $ctx, '%name is deactivated: this switches off %what.', null,
                                     array( '%name' => $name, '%what' => \ezpI18n::tr( $ctx, $p['what'] ) ) );
            case 'new_problem':
                return $name . ': ' . self::problemText( $p['problem'], $p );
            case 'no_effect':
                return \ezpI18n::tr( $ctx, 'Only the written order changes. The declared dependencies decide the loading order of these extensions, so they load in the same order as before.' );
        }
        return $code;
    }
}

}
