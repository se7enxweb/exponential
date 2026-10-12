<?php
/**
 * File containing the expPolicyOmitList class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 98854ab935), Copyright (C) Mugo Web, GPL v2
 */

/**
 * The modules and views that skip the policy check: site.ini [RoleSettings] PolicyOmitList[] without
 * the entries of [RoleSettings] PolicyOmitListExclude[].
 *
 * An INI array can only be added to or reset, and a reset in settings/override would also drop what
 * the active extensions add (ezjscore/call, ...). PolicyOmitListExclude[] takes single entries out
 * after every file is merged. New installations write PolicyOmitListExclude[]=user/register, so
 * registering needs the user/register policy (given to the Anonymous role when a site wants open
 * registration); an upgraded site has no exclusions and keeps user/register open as before.
 *
 * Every reader of the list in the kernel (ezpKernelWeb, expViewAccess, the benchmark) and ezjscore/run
 * asks this class. Nothing is kept between calls: the list is read from the INI instance each time.
 */
class expPolicyOmitList
{
    /**
     * The effective omit list.
     *
     * @param eZINI|null $ini site.ini; eZINI::instance() when null
     * @return string[] "module" or "module/view" entries, in their PolicyOmitList order
     */
    public static function entries( $ini = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance();

        $list = $ini->hasVariable( 'RoleSettings', 'PolicyOmitList' ) ? (array)$ini->variable( 'RoleSettings', 'PolicyOmitList' ) : array();
        $exclude = self::excluded( $ini );
        if ( !$exclude )
            return array_values( $list );

        $result = array();
        foreach ( $list as $entry )
        {
            if ( !in_array( (string)$entry, $exclude, true ) )
                $result[] = $entry;
        }
        return $result;
    }

    /**
     * The entries [RoleSettings] PolicyOmitListExclude[] takes out (empty strings ignored).
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function excluded( $ini = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance();
        if ( !$ini->hasVariable( 'RoleSettings', 'PolicyOmitListExclude' ) )
            return array();

        $result = array();
        foreach ( (array)$ini->variable( 'RoleSettings', 'PolicyOmitListExclude' ) as $entry )
        {
            $entry = trim( (string)$entry );
            if ( $entry !== '' )
                $result[] = $entry;
        }
        return $result;
    }

    /**
     * Whether a module, or one of its views, skips the policy check.
     *
     * @param string $moduleName
     * @param string $viewName '' for the module as a whole
     * @param eZINI|null $ini
     * @return bool
     */
    public static function omits( $moduleName, $viewName = '', $ini = null )
    {
        $entries = self::entries( $ini );
        return in_array( $moduleName, $entries, true )
            || ( $viewName !== '' && in_array( $moduleName . '/' . $viewName, $entries, true ) );
    }
}
