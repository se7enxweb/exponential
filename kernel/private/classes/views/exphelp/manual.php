<?php
/**
 * exphelp/manual/<remote_id> (doc/features/6.0/help-content.md): a manual of help maintained as content, its metadata
 * and table of contents (chapters and topics the user may see). Without a remote_id: the list of manuals.
 * Template design:exphelp/manual.tpl.
 *
 * Template variables: help_manual (the manual, or false for the list), help_manual_toc (nested: kind chapter|topic,
 * title, shortdesc, help_id, url, preview, children), help_manuals (visible manuals), help_json_url, help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Manual extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpContentProvider::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $provider = \expHelpContentProvider::instance();
        $remoteId = \expHelp::requestedId( $Params, $_GET );
        $manual = false;
        $toc = array();
        if ( $remoteId !== '' )
        {
            $manual = $provider->manual( $remoteId );
            if ( $manual === null )
                return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
            $toc = $provider->toc( $manual );
        }
        $manuals = array();
        foreach ( $provider->manuals() as $item )
            $manuals[] = array( 'title' => $item['title'], 'subtitle' => $item['subtitle'], 'shortdesc' => $item['shortdesc'],
                                'remote_id' => $item['remote_id'], 'edition' => $item['edition'], 'preview' => $item['preview'] );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_manual', $manual === false ? false : $manual );
        $tpl->setVariable( 'help_manual_toc', $toc );
        $tpl->setVariable( 'help_manuals', $manuals );
        $tpl->setVariable( 'help_json_url', $manual ? ltrim( \expHelp::url( 'context', 'manual/' . $manual['remote_id'] ), '/' ) : '' );

        $path = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                       array( 'url' => $manual ? 'exphelp/manual' : false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Manuals' ) ) );
        if ( $manual )
            $path[] = array( 'url' => false, 'text' => $manual['title'] );
        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/manual.tpl' );
        $Result['path'] = $path;
        return $Result;
    }
}

}
