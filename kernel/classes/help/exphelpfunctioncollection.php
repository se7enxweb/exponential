<?php
/**
 * The exphelp fetch functions (kernel/exphelp/function_definition.php).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpFunctionCollection
{
    /**
     * fetch( 'exphelp', 'context', hash( 'module', ..., 'view', ..., 'design', ... ) )
     *
     * @param string $module
     * @param string $view
     * @param string $design
     * @return array array( 'result' => expHelpContextRegistry::lookup() or false when the user may not read the help )
     */
    public static function fetchContext( $module = '', $view = '', $design = '' )
    {
        if ( !expHelp::isEnabled() || !expHelp::canRead() )
            return array( 'result' => false );
        return array( 'result' => expHelpContextRegistry::instance()->lookup( (string)$module, (string)$view, (string)$design ) );
    }

    /**
     * fetch( 'exphelp', 'context_menu', hash( 'module', ..., 'view', ..., 'design', ... ) ): the header's Help menu
     *
     * @param string $module
     * @param string $view
     * @param string $design
     * @return array array( 'result' => expHelpContextRegistry::menu() (topics, links) or false when the user may not
     *               read the help )
     */
    public static function fetchContextMenu( $module = '', $view = '', $design = '' )
    {
        if ( !expHelp::isEnabled() || !expHelp::canRead() )
            return array( 'result' => false );
        return array( 'result' => expHelpContextRegistry::instance()->menu( (string)$module, (string)$view, (string)$design ) );
    }
}

?>
