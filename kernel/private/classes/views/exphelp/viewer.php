<?php
/**
 * exphelp/viewer/<page id> (doc/en/us/features/6.0/help-system.md): one help page, rendered. Template design:exphelp/viewer.tpl.
 * An unknown id, or one expHelpDocumentIndex does not allow, is a 404; no id opens the dashboard.
 *
 * Template variables: help_page (document), help_html (rendered, safe), help_toc (level, text, anchor), help_tree,
 * help_nav_html (the navigation tree, expHelpNavigation: safe HTML, not washed), help_prev, help_next (documents or
 * null), help_edit_url (GitHub), help_category, help_breadcrumb (array of url, text),
 * help_pdf_enabled (bool: the "Download as PDF" link, exphelp/pdf/<page id>), help_page_lang (the HTML language of the
 * page: en-US, de), help_page_dir (ltr, rtl), help_fallback (the page is not translated into the reader's language yet and is shown in
 * another), help_reader_language and help_page_language (their names, translated), help_reading (the "Reading in"
 * control, expHelp::reading()), help_original_url, help_original_language and help_original_view (readingVariables():
 * ?lang=en-us shows the English original of a page to a reader of another language). An address that names a file
 * (doc/en/us/<id>, de/<translated path>) redirects to the page id.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Viewer extends \Exponential\Runnable\ModuleView
{
    /** part of the render cache key: raise it when the rendered HTML changes (2: images carry width and height; 3: links resolve from the file, so a translation reaches the original's images; 4: anchors map to the shown language's headings) */
    const RENDER_VERSION = 4;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $id = \expHelp::requestedId( $Params, $_GET );
        if ( $id === '' )
            return $Module->redirectTo( \expHelp::url( 'dashboard' ) );

        $readerIndex = \expHelpDocumentIndex::instance();
        $index = $readerIndex;
        // ?lang=en-us: the page in another language for this request (the English original of a translated page)
        $original = method_exists( 'expHelp', 'requestedLanguage' )
            ? \expHelp::requestedLanguage( $_GET, $readerIndex->languages(), $readerIndex->language() ) : null;
        if ( $original !== null )
            $index = \expHelpDocumentIndex::forLanguage( $original, $readerIndex->languages() );
        $document = $index->document( $id );
        // an address that names a file instead of a page id (doc/en/us/<id>, en/us/<id>, de/<translated path>, the
        // old doc/<id>): the page's own address, so a link keeps working and shows the reader's language
        if ( $document === null )
        {
            $canonical = self::canonicalId( $index, $id );
            if ( $canonical !== null && $index->document( $canonical ) !== null )
                return $Module->redirectTo( \expHelp::url( 'viewer', $canonical ) );
        }
        $source = $document === null ? null : $index->source( $id );
        if ( $document === null || $source === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $rendered = self::rendered( $index, $document, $id, $source );
        $categories = \expHelp::translatedNames( $index->categories() );
        $neighbours = $index->neighbours( $id );
        $editBase = (string)\expHelp::setting( 'HelpSettings', 'EditBaseURL', '' );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_page', $document );
        $tpl->setVariable( 'help_html', $rendered['html'] );
        $tpl->setVariable( 'help_toc', $rendered['toc'] );
        // a server process started before the class existed (a Velocity worker) shows the page without the tree
        $tpl->setVariable( 'help_nav_html', class_exists( 'expHelpNavigation' ) ? ( new \expHelpNavigation( $index ) )->html( $id ) : '' );
        $tpl->setVariable( 'help_prev', $neighbours['prev'] );
        $tpl->setVariable( 'help_next', $neighbours['next'] );
        $tpl->setVariable( 'help_edit_url', self::editUrl( $editBase, $document ) );
        // Print and PDF (expHelpPdf): the download is offered when [PdfSettings] Enabled is not false
        $tpl->setVariable( 'help_pdf_enabled', class_exists( 'expHelpPdf' ) && \expHelpPdf::isEnabled() );
        $tpl->setVariable( 'help_category', isset( $document['category'], $categories[$document['category']] ) ? $categories[$document['category']] : false );
        // the language of the page and, when it is not the reader's (not translated yet), the note that says so
        foreach ( self::languageVariables( $index, $document ) as $name => $value )
            $tpl->setVariable( $name, $value );
        // the reader's language with the switch, and the links between a page and its English original
        foreach ( self::readingVariables( $readerIndex, $document, $id, $original ) as $name => $value )
            $tpl->setVariable( $name, $value );

        $title = isset( $document['title'] ) && $document['title'] !== '' ? $document['title'] : $id;
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/viewer.tpl' );
        $Result['path'] = self::breadcrumb( $index, $id, $title );
        return $Result;
    }

    /**
     * The page id an address names when it is not an id: a file name (doc/en/us/guides/x.md), a path in a language
     * directory (en/us/guides/x.md, de/anleitungen/x.md through the language's path map). Null when it names no
     * other id.
     *
     * @param \expHelpDocumentIndex $index
     * @param string $id
     * @return string|null
     */
    public static function canonicalId( $index, $id )
    {
        $id = \expHelpAllowlist::normalize( $id );
        if ( $id === null || strpos( $id, 'ext/' ) === 0 )
            return null;
        // a project file named by its path in the repository (README.md, bin/php/README.md): project/<path>
        if ( class_exists( 'expHelpProjectFiles' ) && !\expHelpProjectFiles::isProjectId( $id ) && $index->allowlist()->isProjectPath( $id ) )
            return \expHelpProjectFiles::idOf( $id );
        $canonical = $index->languages()->idOfValue( $id );
        return $canonical !== '' && $canonical !== $id ? $canonical : null;
    }

    /**
     * The template variables of a page's language: help_page_lang (its HTML language, en-US, de), help_fallback
     * (whether the page is shown in another language than the reader's), help_reader_language and
     * help_page_language (translated names, for the note).
     *
     * @param \expHelpDocumentIndex $index
     * @param array $document
     * @return array
     */
    public static function languageVariables( $index, array $document )
    {
        $languages = $index->languages();
        $pageLanguage = isset( $document['language'] ) && $document['language'] !== '' ? (string)$document['language'] : $languages->defaultDirectory();
        $name = function ( $directory ) use ( $languages ) {
            return \ezpI18n::tr( 'kernel/exphelp', $languages->name( $directory ) );
        };
        return array( 'help_page_lang' => \expHelpLanguage::htmlLang( $pageLanguage ),
                      'help_page_dir' => \expHelpLanguage::htmlDir( $pageLanguage ),
                      'help_fallback' => !empty( $document['fallback'] ),
                      'help_reader_language' => $name( $index->language() ),
                      'help_page_language' => $name( $pageLanguage ) );
    }

    /**
     * The "Edit on GitHub" address of a page: EditBaseURL followed by its path. None for an extension's page (it is
     * in the extension's own repository, not the one EditBaseURL names), and none without EditBaseURL.
     *
     * @param string $editBase
     * @param array $document
     * @return string
     */
    public static function editUrl( $editBase, array $document )
    {
        $path = isset( $document['path'] ) ? (string)$document['path'] : '';
        if ( $editBase === '' || $path === '' || strncmp( $path, 'extension/', 10 ) === 0 )
            return '';
        return $editBase . $path;
    }

    /**
     * The template variables of the "Reading in" control and of the original: help_reading (expHelp::reading(), false
     * in a worker started before it existed), help_original_url (the page in the default language, ?lang=<key>,
     * for a reader of another language; '' otherwise), help_original_language (its name), help_original_view (false,
     * or hash( name, reader_name, back_url ) when this request shows the page in another language than the reader's).
     *
     * @param \expHelpDocumentIndex $readerIndex the index of the reader's language
     * @param array $document the page shown
     * @param string $id
     * @param string|null $original the language this request shows instead of the reader's (?lang=)
     * @return array
     */
    public static function readingVariables( $readerIndex, array $document, $id, $original )
    {
        $variables = array( 'help_reading' => false, 'help_original_url' => '', 'help_original_language' => '', 'help_original_view' => false );
        if ( !method_exists( 'expHelp', 'reading' ) || !method_exists( $readerIndex, 'languageCoverage' ) )
            return $variables;
        $languages = $readerIndex->languages();
        $default = $languages->defaultDirectory();
        $variables['help_reading'] = \expHelp::reading( $readerIndex, \expHelp::url( 'viewer', $id ) );
        $variables['help_original_language'] = \expHelp::languageName( $languages, $default );
        if ( $original !== null )
            $variables['help_original_view'] = array( 'name' => \expHelp::languageName( $languages, $original ),
                                                      'reader_name' => \expHelp::languageName( $languages, $readerIndex->language() ),
                                                      'back_url' => \expHelp::url( 'viewer', $id ) );
        elseif ( $readerIndex->language() !== $default && isset( $document['language'] ) && $document['language'] !== '' )
            $variables['help_original_url'] = \expHelp::url( 'viewer', $id, '', array( 'lang' => \expHelpLanguage::key( $default ) ) );
        return $variables;
    }

    /**
     * Help > each folder of the page (linked to its README.md or index.md when the help has one) > the page. The
     * pages of an extension: Help > Extensions (exphelp/extensions) > the extension's overview, or its index
     * (exphelp/extensions/<name>) > ... > the page. A project file (project/...): Help > the page.
     *
     * @param \expHelpDocumentIndex $index
     * @param string $id
     * @param string $title
     * @return array the $Result['path'] entries
     */
    public static function breadcrumb( $index, $id, $title )
    {
        $path = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ) );
        // the project files (project/...) stand at the top of the page tree, in no folder: Help > the page
        if ( class_exists( 'expHelpProjectFiles' ) && \expHelpProjectFiles::isProjectId( (string)$id ) )
        {
            $path[] = array( 'url' => false, 'text' => $title );
            return $path;
        }
        $dirs = explode( '/', (string)$id );
        array_pop( $dirs );
        $dir = '';
        foreach ( $dirs as $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            if ( $dir === 'ext' )
            {
                $path[] = array( 'url' => 'exphelp/extensions', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Extensions' ) );
                continue;
            }
            $landing = null;
            foreach ( array( 'README.md', 'readme.md', 'index.md' ) as $file )
            {
                $candidate = $index->document( $dir . '/' . $file );
                if ( $candidate !== null && $dir . '/' . $file !== $id )
                {
                    // an extension's doc/README.md or doc/index.md before the README at its top
                    $top = (bool)preg_match( '#^extension/[^/]+/[^/]+$#', isset( $candidate['path'] ) ? (string)$candidate['path'] : '' );
                    if ( $landing !== null && $top )
                        continue;
                    $landing = $candidate;
                    $landing['id'] = $dir . '/' . $file;
                    if ( !$top )
                        break;
                }
            }
            if ( $landing === null && preg_match( '#^ext/([^/]+)$#', $dir, $m ) )
                $path[] = array( 'url' => 'exphelp/extensions/' . rawurlencode( $m[1] ), 'text' => $m[1] );
            else
                $path[] = $landing !== null
                    ? array( 'url' => ltrim( \expHelp::url( 'viewer', $landing['id'] ), '/' ),
                             'text' => isset( $landing['title'] ) && $landing['title'] !== '' ? $landing['title'] : $segment )
                    : array( 'url' => false, 'text' => ucfirst( str_replace( array( '-', '_' ), ' ', $segment ) ) );
        }
        $path[] = array( 'url' => false, 'text' => $title );
        return $path;
    }

    /**
     * The rendered page, cached per page and index signature in <help cache>/render/ (a long changelog takes 0.3 s).
     *
     * @param \expHelpDocumentIndex $index
     * @param array $document
     * @param string $id
     * @param string $source
     * @return array the renderer's result
     */
    public static function rendered( $index, array $document, $id, $source )
    {
        $markdown = isset( $document['suffix'] ) && in_array( $document['suffix'], array( 'md', 'markdown' ), true );
        $dir = rtrim( (string)$index->cacheDir(), '/' ) . '/render';
        // the siteaccess prefix is in the links, so it is part of the key
        $file = $dir . '/' . sha1( self::RENDER_VERSION . "\0" . $index->signature() . "\0" . $id . "\0" . \eZSys::indexDir() . "\0" . md5( (string)$source ) ) . '.json';
        if ( is_file( $file ) )
        {
            $cached = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $cached ) && isset( $cached['html'], $cached['toc'] ) )
                return $cached;
        }
        $renderer = self::renderer( isset( $document['path'] ) ? (string)$document['path'] : '' );
        $rendered = $markdown ? $renderer->render( $source, $id ) : $renderer->renderPlainText( $source, $id );
        if ( !is_dir( $dir ) )
            \eZDir::mkdir( $dir, false, true );
        if ( is_dir( $dir ) && is_writable( $dir ) )
        {
            $json = json_encode( $rendered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( $json !== false && @file_put_contents( $tmp, $json ) !== false )
            {
                @chmod( $tmp, \eZFile::fileMode( octdec( \eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) ) );
                @rename( $tmp, $file );
            }
        }
        return $rendered;
    }

    /**
     * The renderer, with relative links resolved to the viewer and images to the asset view, with their size (so the
     * page keeps their room while they load). Links and images are checked against the file the page was read from
     * (a translation in doc/de/ links the original's images in doc/en/us/).
     *
     * @param string $sourcePath the page's file, relative to the installation root ('' : its id)
     * @return \expHelpMarkdownRenderer
     */
    public static function renderer( $sourcePath = '' )
    {
        return new \expHelpMarkdownRenderer( array(
            'link_resolver' => array( 'expHelpLinks', 'link' ),
            'image_resolver' => array( 'expHelpLinks', 'sizedImage' ),
            'source_path' => (string)$sourcePath ) );
    }
}

}
