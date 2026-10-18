<?php
/**
 * exphelp/viewer/<page id> (doc/features/6.0/help-system.md): one help page, rendered. Template design:exphelp/viewer.tpl.
 * An unknown id, or one expHelpDocumentIndex does not allow, is a 404; no id opens the dashboard.
 *
 * Template variables: help_page (document), help_html (rendered, safe), help_toc (level, text, anchor), help_tree,
 * help_prev, help_next (documents or null), help_edit_url (GitHub), help_breadcrumb (array of url, text).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Viewer extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $id = \expHelp::requestedId( $Params, $_GET );
        if ( $id === '' )
            return $Module->redirectTo( \expHelp::url( 'dashboard' ) );

        $index = \expHelpDocumentIndex::instance();
        $document = $index->document( $id );
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
        $tpl->setVariable( 'help_tree', $index->tree() );
        $tpl->setVariable( 'help_prev', $neighbours['prev'] );
        $tpl->setVariable( 'help_next', $neighbours['next'] );
        $tpl->setVariable( 'help_edit_url', $editBase !== '' && isset( $document['path'] ) ? $editBase . $document['path'] : '' );
        $tpl->setVariable( 'help_category', isset( $document['category'], $categories[$document['category']] ) ? $categories[$document['category']] : false );

        $title = isset( $document['title'] ) && $document['title'] !== '' ? $document['title'] : $id;
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/viewer.tpl' );
        $Result['path'] = self::breadcrumb( $index, $id, $title );
        return $Result;
    }

    /**
     * Help > each folder of the page (linked to its README.md or index.md when the help has one) > the page.
     *
     * @param \expHelpDocumentIndex $index
     * @param string $id
     * @param string $title
     * @return array the $Result['path'] entries
     */
    public static function breadcrumb( $index, $id, $title )
    {
        $path = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ) );
        $dirs = explode( '/', (string)$id );
        array_pop( $dirs );
        $dir = '';
        foreach ( $dirs as $segment )
        {
            $dir = $dir === '' ? $segment : $dir . '/' . $segment;
            $landing = null;
            foreach ( array( 'README.md', 'readme.md', 'index.md' ) as $file )
            {
                $candidate = $index->document( $dir . '/' . $file );
                if ( $candidate !== null && $dir . '/' . $file !== $id )
                {
                    $landing = $candidate;
                    $landing['id'] = $dir . '/' . $file;
                    break;
                }
            }
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
        $file = $dir . '/' . sha1( $index->signature() . "\0" . $id . "\0" . \eZSys::indexDir() . "\0" . md5( (string)$source ) ) . '.json';
        if ( is_file( $file ) )
        {
            $cached = json_decode( (string)@file_get_contents( $file ), true );
            if ( is_array( $cached ) && isset( $cached['html'], $cached['toc'] ) )
                return $cached;
        }
        $renderer = self::renderer();
        $rendered = $markdown ? $renderer->render( $source, $id ) : $renderer->renderPlainText( $source, $id );
        if ( !is_dir( $dir ) )
            \eZDir::mkdir( $dir, false, true );
        if ( is_dir( $dir ) && is_writable( $dir ) )
        {
            $json = json_encode( $rendered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( $json !== false && @file_put_contents( $tmp, $json ) !== false )
            {
                @chmod( $tmp, octdec( \eZINI::instance()->variable( 'FileSettings', 'StorageFilePermissions' ) ) );
                @rename( $tmp, $file );
            }
        }
        return $rendered;
    }

    /**
     * The renderer, with relative links resolved to the viewer and images to the asset view.
     *
     * @return \expHelpMarkdownRenderer
     */
    public static function renderer()
    {
        return new \expHelpMarkdownRenderer( array(
            'link_resolver' => array( 'expHelpLinks', 'link' ),
            'image_resolver' => array( 'expHelpLinks', 'image' ) ) );
    }
}

}
