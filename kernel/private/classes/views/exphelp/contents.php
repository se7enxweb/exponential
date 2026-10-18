<?php
/**
 * exphelp/contents (doc/features/6.0/help-system.md): every page of the help as one tree, all folders open, with a
 * filter box (exphelp.js) that narrows it as you type. Template design:exphelp/contents.tpl.
 *
 * Template variables: help_nav_html (expHelpNavigation, every folder open: safe HTML, not washed), help_nav_count
 * (the number of pages), help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Contents extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        if ( !\expHelp::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $navigation = new \expHelpNavigation( \expHelpDocumentIndex::instance() );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_nav_html', $navigation->html( '', true ) );
        $tpl->setVariable( 'help_nav_count', $navigation->count() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/contents.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/exphelp', 'All help pages' ) ) );
        return $Result;
    }
}

}
