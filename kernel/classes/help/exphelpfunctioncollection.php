<?php
/**
 * The exphelp fetch functions (kernel/exphelp/function_definition.php).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
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
     * fetch( 'exphelp', 'license', hash( 'id', $page_id ) )
     *
     * @param string $id page id below doc/; '' for the help as a whole
     * @return array array( 'result' => expHelpLicense::forPage(), or false when the notice is switched off )
     */
    public static function fetchLicense( $id = '' )
    {
        return array( 'result' => expHelpLicense::isEnabled() ? expHelpLicense::forPage( (string)$id ) : false );
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

    /**
     * fetch( 'exphelp', 'site_topic', hash( 'object_id', ... ) ): a help topic for the site's own page
     *
     * @param int $object_id
     * @return array array( 'result' => expHelpContentSite::topic(), or false when the reader may not see it )
     */
    public static function fetchSiteTopic( $object_id )
    {
        $topic = class_exists( 'expHelpContentSite' ) ? ( new expHelpContentSite() )->topic( (int)$object_id ) : null;
        return array( 'result' => $topic !== null ? $topic : false );
    }

    /**
     * fetch( 'exphelp', 'site_map', hash( 'object_id', ... ) ): a manual or chapter for the site's own page
     *
     * @param int $object_id
     * @return array array( 'result' => expHelpContentSite::map(), or false when the reader may not see it )
     */
    public static function fetchSiteMap( $object_id )
    {
        $map = class_exists( 'expHelpContentSite' ) ? ( new expHelpContentSite() )->map( (int)$object_id ) : null;
        return array( 'result' => $map !== null ? $map : false );
    }
}

?>
