<?php
/**
 * The code of kernel/pdf/list.php, moved into a class (#207 stage 1). The file kernel/pdf/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/pdf/list.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Pdf
{

class ListView extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];


        // Create new PDF Export
        if ( $Module->isCurrentAction( 'NewExport' ) )
        {
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirect( 'pdf', 'edit' ) );
        }
        //Remove existing PDF Export(s)
        else if ( $Module->isCurrentAction( 'RemoveExport' ) && $Module->hasActionParameter( 'DeleteIDArray' ) )
        {
            $deleteArray = $Module->actionParameter( 'DeleteIDArray' );
            foreach ( $deleteArray as $deleteID )
            {
                // remove draft if it exists:
                $pdfExport = \eZPDFExport::fetch( $deleteID, true, \eZPDFExport::VERSION_DRAFT );
                if ( $pdfExport )
                {
                    $pdfExport->remove();
                }
                // remove default version:
                $pdfExport = \eZPDFExport::fetch( $deleteID );
                if ( $pdfExport )
                {
                    $pdfExport->remove();
                }
            }
        }

        $exportArray = \eZPDFExport::fetchList();

        $pdfCount  = count( $exportArray );
        $pdfLimit  = \expAdminPagination::limit( 'pdf/list' );
        $pdfOffset = \expAdminPagination::offset( $Params );
        $exportArray = \expAdminPagination::page( $exportArray, $pdfOffset, $pdfLimit );
        $exportList = array();
        foreach( $exportArray as $export )
        {
            $exportList[$export->attribute( 'id' )] = $export;
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'pdfexport_list', $exportList );
        $tpl->setVariable( 'pdfexport_count', $pdfCount );
        $tpl->setVariable( 'limit', $pdfLimit );
        $tpl->setVariable( 'view_parameters', array( 'offset' => $pdfOffset ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:pdf/list.tpl" );
        $Result['path'] = array( array( 'url' => 'pdf/list',
                                        'text' => \ezpI18n::tr( 'kernel/pdf', 'PDF Export' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
