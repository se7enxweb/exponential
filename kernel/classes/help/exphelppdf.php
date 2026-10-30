<?php
/**
 * A help page as a PDF file (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): the view exphelp/pdf/<page id> and
 * `./console exp:help pdf <page id>` ask here.
 *
 * Only pages of the help index are made (expHelpDocumentIndex::document() and source(): git-tracked, allowed, not
 * refused); anything else is null, which the view answers with a 404. The PDF is drawn by expHelpPdfWriter from the
 * same safe HTML the viewer shows (Viewer::rendered(), so its render cache is shared) and kept in
 * <help cache>/pdf/<key>.pdf. The key holds the page id, the index signature (it changes with every change below
 * doc/), the page's text, the address the links point to, the fonts, the page size and the writer's version, so a
 * changed page or setting makes a new file and an old one is never served.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpPdf
{
    /**
     * Whether PDFs are offered ([PdfSettings] Enabled, on unless set to false; the help itself must be on).
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return expHelp::isEnabled() && expHelp::setting( 'PdfSettings', 'Enabled', 'true' ) !== 'false';
    }

    /**
     * The cache key of a page's PDF.
     *
     * @param string $id page id
     * @param string $signature the index signature (expHelpDocumentIndex::signature())
     * @param string $source the page's text
     * @param string $baseUrl the address links and the page URL start with
     * @param array $fonts the font files (expHelpPdfWriter::defaultFonts())
     * @param string $pageSize
     * @return string 40 hex characters
     */
    public static function cacheKey( $id, $signature, $source, $baseUrl = '', array $fonts = array(), $pageSize = 'A4' )
    {
        ksort( $fonts );
        return sha1( implode( "\0", array( expHelpPdfWriter::VERSION, (string)$id, (string)$signature, md5( (string)$source ),
                                           (string)$baseUrl, json_encode( $fonts ), strtoupper( (string)$pageSize ) ) ) );
    }

    /**
     * The file name a page's PDF is downloaded as: its title as a slug (letters of any script kept), else the
     * file name of its id, with .pdf.
     *
     * @param array $document
     * @param string $id
     * @return string
     */
    public static function filename( array $document, $id )
    {
        $title = isset( $document['title'] ) ? trim( (string)$document['title'] ) : '';
        $name = $title !== '' ? expHelpMarkdownRenderer::slug( $title ) : '';
        if ( $name === '' || $name === 'section' )
            $name = preg_replace( '/\.[A-Za-z0-9]+$/', '', basename( (string)$id ) );
        $name = trim( preg_replace( '/[^\p{L}\p{N}\p{M}._-]+/u', '-', (string)$name ), '-.' );
        if ( function_exists( 'mb_substr' ) )
            $name = mb_substr( $name, 0, 80, 'UTF-8' );
        return ( $name === '' ? 'help' : $name ) . '.pdf';
    }

    /**
     * The ASCII form of a file name, for the plain filename parameter of Content-Disposition.
     *
     * @param string $filename
     * @return string
     */
    public static function asciiFilename( $filename )
    {
        $ascii = @iconv( 'UTF-8', 'ASCII//TRANSLIT', (string)$filename );
        $ascii = preg_replace( '/[^A-Za-z0-9._-]+/', '-', $ascii === false ? '' : $ascii );
        $ascii = trim( (string)$ascii, '-' );
        return $ascii === '' || $ascii === '.pdf' ? 'help.pdf' : $ascii;
    }

    /**
     * The settings of the writer: fonts, page size.
     *
     * @return array
     */
    public static function writerSettings()
    {
        return array( 'fonts' => expHelpPdfWriter::defaultFonts(),
                      'page_size' => (string)expHelp::setting( 'PdfSettings', 'PageSize', 'A4' ) );
    }

    /**
     * The PDF of a page, made or taken from the cache.
     *
     * @param string $id page id
     * @param string $baseUrl scheme and host for links and the page's address ('' = links into the administration
     *                        are left out, as on the command line without --base-url)
     * @param expHelpDocumentIndex|null $index
     * @return array|null array( file (path, or null when the cache cannot be written), data (when file is null),
     *                    filename, size, cached ) or null when the id is not a served page
     */
    public static function forPage( $id, $baseUrl = '', ?expHelpDocumentIndex $index = null )
    {
        $index = $index ?: expHelpDocumentIndex::instance();
        $id = (string)$id;
        $document = $id === '' ? null : $index->document( $id );
        $source = $document === null ? null : $index->source( $id );
        if ( $document === null || $source === null )
            return null;

        $settings = self::writerSettings();
        $baseUrl = rtrim( (string)$baseUrl, '/' );
        $key = self::cacheKey( $id, $index->signature(), $source . "\0" . self::notice( $id ), $baseUrl . "\0" . eZSys::indexDir(), $settings['fonts'], $settings['page_size'] );
        $cacheDir = $index->cacheDir();
        $dir = $cacheDir === null || $cacheDir === '' ? null : rtrim( (string)$cacheDir, '/' ) . '/pdf';
        $file = $dir === null ? null : $dir . '/' . $key . '.pdf';
        $filename = self::filename( $document, $id );
        if ( $file !== null && is_file( $file ) && filesize( $file ) > 0 )
            return array( 'file' => $file, 'data' => null, 'filename' => $filename, 'size' => filesize( $file ), 'cached' => true );

        $data = self::build( $index, $document, $id, $source, $baseUrl, $settings );

        if ( $dir !== null && !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        if ( $dir !== null && is_dir( $dir ) && is_writable( $dir ) )
        {
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, $data ) === strlen( $data ) )
            {
                $permissions = eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' );
                @chmod( $tmp, eZFile::fileMode( preg_match( '/^0?[0-7]{3}$/', (string)$permissions ) ? octdec( $permissions ) : 0664 ) );
                if ( @rename( $tmp, $file ) )
                    return array( 'file' => $file, 'data' => null, 'filename' => $filename, 'size' => strlen( $data ), 'cached' => false );
            }
            @unlink( $tmp );
        }
        return array( 'file' => null, 'data' => $data, 'filename' => $filename, 'size' => strlen( $data ), 'cached' => false );
    }

    /**
     * Draws the PDF of a page.
     *
     * @param expHelpDocumentIndex $index
     * @param array $document
     * @param string $id
     * @param string $source
     * @param string $baseUrl
     * @param array $settings writerSettings()
     * @return string
     */
    public static function build( expHelpDocumentIndex $index, array $document, $id, $source, $baseUrl, array $settings )
    {
        // the viewer's render cache when the index has a cache directory, else rendered here
        if ( $index->cacheDir() !== null && $index->cacheDir() !== '' )
            $rendered = \Exponential\View\Kernel\Exphelp\Viewer::rendered( $index, $document, $id, $source );
        else
        {
            $renderer = \Exponential\View\Kernel\Exphelp\Viewer::renderer( isset( $document['path'] ) ? (string)$document['path'] : '' );
            $markdown = isset( $document['suffix'] ) && in_array( $document['suffix'], array( 'md', 'markdown' ), true );
            $rendered = $markdown ? $renderer->render( $source, $id ) : $renderer->renderPlainText( $source, $id );
        }
        $html = isset( $rendered['html'] ) ? (string)$rendered['html'] : '';
        $title = isset( $document['title'] ) && $document['title'] !== '' ? (string)$document['title']
               : ( isset( $rendered['title'] ) && $rendered['title'] !== '' ? (string)$rendered['title'] : $id );
        $pageUrl = $baseUrl !== '' ? $baseUrl . expHelpLinks::absolute( expHelp::url( 'viewer', $id ) ) : '';

        $writer = new expHelpPdfWriter( array(
            'fonts' => $settings['fonts'],
            'page_size' => $settings['page_size'],
            'base_url' => $baseUrl,
            'image_resolver' => function ( $src ) use ( $index ) { return self::imageFile( $index, $src ); },
            'translate' => function ( $text, array $params ) { return ezpI18n::tr( expHelp::I18N_CONTEXT, $text, null, $params ); },
        ) );
        return $writer->render( array(
            'title' => $title,
            'summary' => isset( $document['summary'] ) ? (string)$document['summary'] : '',
            'path' => isset( $document['path'] ) ? (string)$document['path'] : '',
            'changed' => isset( $document['changed'] ) ? (int)$document['changed'] : ( isset( $document['mtime'] ) ? (int)$document['mtime'] : 0 ),
            'url' => $pageUrl,
            'notice' => self::notice( $id ),
        ), $html );
    }

    /**
     * The license notice of a page (expHelpLicense, when the installation has it), printed at the end of the PDF.
     *
     * @param string $id
     * @return string plain text, '' when there is none
     */
    public static function notice( $id )
    {
        if ( !class_exists( 'expHelpLicense' ) || !method_exists( 'expHelpLicense', 'noticeText' ) )
            return '';
        return trim( (string)expHelpLicense::noticeText( $id ) );
    }

    /**
     * The file of an image of a page: only images of the help served by exphelp/asset (the index decides); an
     * outside address is not fetched.
     *
     * @param expHelpDocumentIndex $index
     * @param string $src the image's address in the rendered page
     * @return string|null
     */
    public static function imageFile( expHelpDocumentIndex $index, $src )
    {
        $src = (string)$src;
        $at = strpos( $src, '/exphelp/asset/' );
        if ( $at === false || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $src ) === 1 )
            return null;
        $id = rawurldecode( preg_replace( '/[?#].*$/', '', substr( $src, $at + strlen( '/exphelp/asset/' ) ) ) );
        if ( $id === '' || strpos( $id, '..' ) !== false )
            return null;
        $file = $index->asset( $id );
        return $file !== null && is_file( $file ) ? $file : null;
    }
}

?>
