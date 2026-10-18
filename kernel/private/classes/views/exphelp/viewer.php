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

        $renderer = self::renderer();
        $rendered = isset( $document['suffix'] ) && in_array( $document['suffix'], array( 'md', 'markdown' ), true )
            ? $renderer->render( $source, $id )
            : $renderer->renderPlainText( $source, $id );
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

        $title = isset( $document['title'] ) && $document['title'] !== '' ? $document['title'] : $id;
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/viewer.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => $title ) );
        return $Result;
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
