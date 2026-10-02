<?php
/**
 * The view content/subitemsexport/<NodeID>: the admin subitems list of a node as a CSV download.
 *
 * GET columns=a,b,c (column keys, in order; default: the user's saved choice for the node, else
 * its default columns), sort=<column key> and order=0|1. Every child the user can read, up to
 * [SubitemsSettings] CSVLimit in subitems.ini; only the columns the user may see.
 * Guide: doc/bc/6.0/subitems-table-options.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

class Subitemsexport extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $registry = \expSubitemsColumnRegistry::instance();

        if ( !$registry->csvExportEnabled() )
            return $module->handleError( \eZError::KERNEL_MODULE_VIEW_NOT_FOUND, 'kernel' );

        if ( !\eZUser::currentUser()->isRegistered() )
            return $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $nodeID = isset( $Params['NodeID'] ) && ctype_digit( (string)$Params['NodeID'] ) ? (int)$Params['NodeID'] : 0;
        $parent = $nodeID > 0 ? \eZContentObjectTreeNode::fetch( $nodeID ) : null;
        if ( !$parent instanceof \eZContentObjectTreeNode )
            return $module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        if ( !$parent->canRead() )
            return $module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        $http = \eZHTTPTool::instance();
        $keys = $http->hasGetVariable( 'columns' ) && is_string( $http->getVariable( 'columns' ) ) ? $http->getVariable( 'columns' ) : '';
        if ( trim( $keys ) === '' )
        {
            $pref = \expSubitemsPreference::forPart( \expSubitemsPreference::load(),
                                                     \expSubitemsPreference::navigationPart( $parent ),
                                                     $registry->defaults( $parent ) );
            $keys = $pref['visible'];
        }
        $columns = $registry->resolveColumns( $keys, $parent, true );
        unset( $columns['thumbnail'] ); // an image has no text
        if ( !$columns )
            $columns = $registry->resolveColumns( array( 'name', 'nodeid' ), $parent, true );

        $sortKey = $http->hasGetVariable( 'sort' ) && is_string( $http->getVariable( 'sort' ) ) ? $http->getVariable( 'sort' ) : '';
        $ascending = $http->hasGetVariable( 'order' ) && (string)$http->getVariable( 'order' ) === '1';

        $fileName = \expSubitemsCSVExport::fileName( $parent->attribute( 'name' ) );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
        header( 'Cache-Control: private, no-store, max-age=0' );
        header( 'Pragma: no-cache' );
        header( 'X-Content-Type-Options: nosniff' );

        while ( @ob_end_clean() );

        // no catch( Exception ) around the download and cleanExit(): under Velocity cleanExit() throws
        $out = fopen( 'php://output', 'w' );
        $rows = \expSubitemsCSVExport::export( $out, $parent, $registry, $columns, $sortKey, $ascending );
        fclose( $out );

        // Audit (doc/bc/6.0/audit.md, data.export.csv): what left the system, never the values
        if ( class_exists( 'expAuditHook' ) )
            \expAuditHook::emit( 'data.export.csv', array( 'object' => \expAuditHook::node( $parent ), 'verb' => 'export',
                'after' => array( 'node' => (int)$nodeID, 'rows' => (int)$rows, 'columns' => array_values( array_map( 'strval', array_keys( $columns ) ) ),
                                  'file' => (string)$fileName ) ) );

        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
