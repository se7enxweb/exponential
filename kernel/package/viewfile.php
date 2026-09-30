<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * package/viewfile/<PackageName>/<FileIndex>: streams one raw file out of a package's own
 * directory - an image the contents browser shows inline (package/view/full.tpl's <img>), or
 * anything else offered as a plain download. <FileIndex> is the file's position in
 * eZPackageFileBrowser::allFiles()'s own sorted list, not its path (a relative path can carry
 * slashes of its own, which a bare URL segment cannot hold safely); eZPackageFileBrowser::
 * filePath() is the one gate that actually reads it off disk: no path traversal, no absolute
 * path, no following a symlink out of the package's own directory. Kernel-only, no dependency on
 * any extension.
 *
 * IMPORTANT: eZExecution::cleanExit() ends the request by throwing under some web server
 * integrations, so the send-and-exit below must never sit inside a try/catch(Exception) - that
 * would swallow the exit and the page gets appended to the file.
 */

$module = $Params['Module'];
$packageName = $Params['PackageName'];
$fileIndex = isset( $Params['FileIndex'] ) && ctype_digit( (string)$Params['FileIndex'] ) ? (int)$Params['FileIndex'] : -1;

$package = eZPackage::fetch( $packageName );
if ( !is_object( $package ) || !$package->attribute( 'can_read' ) || $fileIndex < 0 )
{
    header( 'HTTP/1.1 404 Not Found' );
    eZExecution::cleanExit();
}

$allFiles = eZPackageFileBrowser::allFiles( $package );
$fileRow = isset( $allFiles[$fileIndex] ) ? $allFiles[$fileIndex] : null;
$realPath = $fileRow ? eZPackageFileBrowser::filePath( $package, $fileRow['path'] ) : false;
if ( $realPath === false )
{
    header( 'HTTP/1.1 404 Not Found' );
    eZExecution::cleanExit();
}

$kind = eZPackageFileBrowser::resolvedKind( $package, $fileRow );
$mimeMap = array(
    'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
);
$ext = strtolower( (string)pathinfo( $fileRow['path'], PATHINFO_EXTENSION ) );
// Every kind but a real image is offered as data, never as a type a browser would try to render
// as markup or execute - an .xml/.txt item included, even though this same file is shown
// pretty-printed as text on the contents browser page itself.
$type = ( $kind === 'image' && isset( $mimeMap[$ext] ) ) ? $mimeMap[$ext] : 'application/octet-stream';

header( 'Cache-Control: private, no-store, max-age=0' );
header( 'Pragma: no-cache' );
header( 'X-Content-Type-Options: nosniff' );
// A package is uploaded content: an SVG in it may carry script. Inside the browser's <img> it never
// runs, but opened directly under the admin's own origin it would; the sandbox stops that.
header( "Content-Security-Policy: default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox" );
header( 'Content-Type: ' . $type );
header( 'Content-Length: ' . filesize( $realPath ) );
if ( $kind !== 'image' )
    header( 'Content-Disposition: attachment; filename="' . str_replace( array( '"', '\\' ), '_', basename( $fileRow['path'] ) ) . '"' );

while ( @ob_end_clean() );

$fh = fopen( $realPath, 'rb' );
while ( $fh && !feof( $fh ) )
    echo fread( $fh, 1048576 );
if ( $fh )
    fclose( $fh );

eZExecution::cleanExit();
