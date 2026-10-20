<?php
/**
 * exphelp/topic/<help_id> (doc/eng-us/features/6.0/help-content.md): one help topic maintained as content, rendered.
 * Template design:exphelp/topic.tpl. A topic the user may not see (not Released and not editable by them, or no
 * content/read) is a 404, as an unknown one: the answer does not tell whether it exists.
 *
 * Template variables: help_page (expHelpContentProvider::document()), help_topic (the topic: type, fields ...),
 * help_html (rendered, safe), help_toc, help_related (documents), help_figures (figures: image_url, alt_text,
 * caption, topic_field), help_topics (every visible topic, documents, for the side list), help_manuals (visible
 * manuals: title, remote_id), help_edit_url (content/edit of the object when the user may edit it, else ''),
 * help_json_url (the context help JSON of the topic), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Topic extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $helpId = \expHelp::requestedId( $Params );
        if ( $helpId === '' )
            return $Module->redirectTo( \expHelp::url( 'dashboard' ) );

        $provider = \expHelpContentProvider::instance();
        $topic = $provider->topic( $helpId );
        if ( $topic === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $rendered = $provider->render( $topic );
        $relations = $provider->relations( $topic );
        $related = array();
        foreach ( $relations['related'] as $item )
            $related[] = \expHelpContentProvider::document( $item );
        $topics = array();
        foreach ( $provider->topics() as $item )
            $topics[] = \expHelpContentProvider::document( $item );
        usort( $topics, function ( $a, $b ) { return strcasecmp( $a['title'], $b['title'] ); } );
        $manuals = array();
        foreach ( $provider->manuals() as $manual )
            $manuals[] = array( 'title' => $manual['title'], 'remote_id' => $manual['remote_id'], 'preview' => $manual['preview'] );

        $object = $provider->repository()->object( $topic['object_id'] );
        $editUrl = $object instanceof \eZContentObject && $object->canEdit()
            ? 'content/edit/' . (int)$topic['object_id'] : '';

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_page', \expHelpContentProvider::document( $topic ) );
        $tpl->setVariable( 'help_topic', $topic );
        $tpl->setVariable( 'help_html', $rendered['html'] );
        $tpl->setVariable( 'help_toc', $rendered['toc'] );
        $tpl->setVariable( 'help_related', $related );
        $tpl->setVariable( 'help_figures', $relations['figures'] );
        $tpl->setVariable( 'help_topics', $topics );
        $tpl->setVariable( 'help_manuals', $manuals );
        $tpl->setVariable( 'help_edit_url', $editUrl );
        $tpl->setVariable( 'help_json_url', ltrim( \expHelp::url( 'context', $topic['help_id'] ), '/' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/topic.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help topics' ) ),
                                 array( 'url' => false, 'text' => $topic['title'] !== '' ? $topic['title'] : $topic['help_id'] ) );
        return $Result;
    }
}

}
