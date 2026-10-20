<?php
/**
 * exphelp/pdf/<page id> (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): the help page as a PDF file to
 * download. Only pages the viewer serves (expHelpDocumentIndex: git-tracked, allowed); anything else is a 404, as is
 * the view when [PdfSettings] Enabled=false. The file is made by expHelpPdf (cached per page and index signature).
 *
 * Sent as an attachment named after the page's title, with nosniff, a private cache lifetime and a Content Security
 * Policy that allows nothing (a PDF needs no script, style or frame of the site).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Pdf extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelpPdf::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $id = \expHelp::requestedId( $Params, $_GET );
        $pdf = $id === '' ? null : \expHelpPdf::forPage( $id, \eZSys::serverURL() );
        if ( $pdf === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        foreach ( self::headers( $pdf['filename'], $pdf['size'] ) as $header )
            header( $header );
        if ( $pdf['file'] !== null )
            readfile( $pdf['file'] );
        else
            echo $pdf['data'];
        \eZExecution::cleanExit();
    }

    /**
     * The response headers of a PDF download.
     *
     * @param string $filename UTF-8 (expHelpPdf::filename())
     * @param int $size
     * @return string[]
     */
    public static function headers( $filename, $size )
    {
        return array(
            'Content-Type: application/pdf',
            'Content-Length: ' . (int)$size,
            'Content-Disposition: attachment; filename="' . \expHelpPdf::asciiFilename( $filename ) . '"; filename*=UTF-8\'\'' . rawurlencode( $filename ),
            'X-Content-Type-Options: nosniff',
            'Cache-Control: private, max-age=600',
            "Content-Security-Policy: default-src 'none'; sandbox",
        );
    }
}

}
