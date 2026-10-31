<?php
/**
 * File containing the expWebsiteSidebarMigration class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * From ezwt (the website toolbar) to expwt (the website sidebar, which replaces it; decision F7 of the expwt plan):
 *
 *   policies   every websitetoolbar/use and websitetoolbar/* becomes expwt/use, expwt/edit, expwt/create and
 *              expwt/structure in the same role, with the same limitations (the toolbar gave all of it); the Editor
 *              role gets those four and the Administrator role expwt/* where they do not hold them already
 *   settings   websitetoolbar.ini [WebsiteToolbarSettings] HiddenContentClasses[] of settings/override and of every
 *              siteaccess becomes expwt.ini [CreateSettings] HiddenClassList[] beside it; what has no counterpart
 *              (the eZODF buttons, the custom toolbar templates) is reported, not copied
 *   extension  ActiveExtensions[]=ezwt (settings/override/site.ini.append.php) and ActiveAccessExtensions[]=ezwt
 *              (siteaccesses) become expwt at the same place; ezwt is no longer loaded
 *
 * Whether the sidebar shows (expwt.ini [ExpwtSettings] Enabled) is left at expwt's default: off until expwt 1.0
 * (rollout behind a setting, F14); exp:wt:enable switches it on per siteaccess.
 *
 * The plans work on plain arrays and text and are tested without a database or files
 * (tests/tests/kernel/classes/expWebsiteSidebarMigrationTest.php); collect() reads the installation and apply()
 * writes it, used by the upgrade step 6.0.15-expwt-enable (expUpgradeStepWebsiteSidebar) and by
 * bin/php/expwtmigratefromezwt.php (exp:wt:migrate-from-ezwt, a dry run unless --apply).
 *
 * @since 6.0.15
 * @package kernel
 */
class expWebsiteSidebarMigration
{
    /** The expwt functions that websitetoolbar/use stood for */
    const FROM_TOOLBAR = array( 'use', 'edit', 'create', 'structure' );

    /** The expwt functions every Editor role gets */
    const EDITOR = array( 'use', 'edit', 'create', 'structure' );

    const EDITOR_ROLE = 'Editor';
    const ADMINISTRATOR_ROLE = 'Administrator';

    /**
     * Whether policies grant module/function in any form (*, module/*, module/function), limited or not.
     *
     * @param array $policies each: module, function
     * @return bool
     */
    public static function covers( array $policies, $module, $function )
    {
        foreach ( $policies as $policy )
        {
            if ( $policy['module'] === '*' || ( $policy['module'] === $module && ( $policy['function'] === '*' || $policy['function'] === $function ) ) )
                return true;
        }
        return false;
    }

    /**
     * @param array $roles each: id, name, policies (expEditorPolicies::policiesOf(): module, function, limitations)
     * @return array grants, each: role (id), name, function, limitations, why
     */
    public static function policyPlan( array $roles )
    {
        $plan = array();
        foreach ( $roles as $role )
        {
            $policies = $role['policies'];
            $add = function ( $function, array $limitations, $why ) use ( &$plan, &$policies, $role ) {
                foreach ( $policies as $p )
                {
                    if ( $p['module'] === '*' || ( $p['module'] === 'expwt' && ( $p['function'] === '*' || ( $p['function'] === $function
                         && self::sameLimitations( isset( $p['limitations'] ) ? $p['limitations'] : array(), $limitations ) ) ) ) )
                        return;
                }
                $policies[] = array( 'module' => 'expwt', 'function' => $function, 'limitations' => $limitations );
                $plan[] = array( 'role' => (int)$role['id'], 'name' => (string)$role['name'], 'function' => $function,
                                 'limitations' => $limitations, 'why' => $why );
            };
            foreach ( $role['policies'] as $policy )
            {
                if ( $policy['module'] !== 'websitetoolbar' || !in_array( $policy['function'], array( 'use', '*' ), true ) )
                    continue;
                $limitations = isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array();
                foreach ( self::FROM_TOOLBAR as $function )
                    $add( $function, $limitations, 'websitetoolbar/' . $policy['function'] );
            }
            if ( $role['name'] === self::EDITOR_ROLE )
            {
                foreach ( self::EDITOR as $function )
                {
                    if ( !self::covers( $policies, 'expwt', $function ) )
                        $add( $function, array(), 'the Editor role' );
                }
            }
            if ( $role['name'] === self::ADMINISTRATOR_ROLE && !self::covers( $policies, 'expwt', '*' ) )
            {
                $any = false;
                foreach ( $policies as $p )
                    $any = $any || $p['module'] === '*' || ( $p['module'] === 'expwt' && $p['function'] === '*' );
                if ( !$any )
                    $add( '*', array(), 'the Administrator role' );
            }
        }
        return $plan;
    }

    /** @return bool the same limitations, the values in any order */
    public static function sameLimitations( array $a, array $b )
    {
        $norm = function ( array $l ) {
            ksort( $l );
            foreach ( $l as $k => $v )
            {
                $v = array_map( 'strval', (array)$v );
                sort( $v );
                $l[$k] = $v;
            }
            return $l;
        };
        return $norm( $a ) === $norm( $b );
    }

    /**
     * The settings of ezwt as expwt settings.
     *
     * @param array $files path of a websitetoolbar.ini.append(.php) => its text
     * @param array $existing path of an expwt.ini.append.php => its text (the files there are already)
     * @return array writes (path => new text of the expwt.ini.append.php beside it), notes (what is not copied)
     */
    public static function settingsPlan( array $files, array $existing = array() )
    {
        $writes = array();
        $notes = array();
        foreach ( $files as $path => $text )
        {
            $hidden = array();
            if ( preg_match_all( '/^\s*HiddenContentClasses\[\]=(\S+)\s*$/m', $text, $m ) )
                $hidden = array_values( array_unique( $m[1] ) );
            $target = dirname( $path ) . '/expwt.ini.append.php';
            $current = isset( $existing[$target] ) ? $existing[$target] : ( isset( $writes[$target] ) ? $writes[$target] : null );
            $missing = array();
            foreach ( $hidden as $class )
            {
                if ( $current === null || !preg_match( '/^\s*HiddenClassList\[\]=' . preg_quote( $class, '/' ) . '\s*$/m', $current ) )
                    $missing[] = $class;
            }
            if ( $missing )
                $writes[$target] = self::withHiddenClasses( $current, $missing );
            if ( preg_match( '/^\s*(ODFDisplayClasses|HideODFContainerClasses)\[\]=\S/m', $text ) )
                $notes[] = "$path: the eZODF buttons have no counterpart in expwt (not copied)";
            if ( preg_match_all( '/^\s*CustomTemplateList\[\]=(\S+)\s*$/m', $text, $c ) )
                $notes[] = "$path: custom toolbar templates (" . implode( ', ', $c[1] ) . ') need an expwt panel (not copied)';
        }
        return array( 'writes' => $writes, 'notes' => $notes );
    }

    /**
     * @param string|null $text an expwt.ini.append.php there is, or null
     * @param string[] $classes
     * @return string the file with the classes in [CreateSettings] HiddenClassList[]
     */
    public static function withHiddenClasses( $text, array $classes )
    {
        $lines = '';
        foreach ( $classes as $class )
            $lines .= "HiddenClassList[]=$class\n";
        if ( $text === null || trim( $text ) === '' )
            return "<?php /* #?ini charset=\"utf-8\"?\n\n# copied from websitetoolbar.ini by exp:wt:migrate-from-ezwt\n[CreateSettings]\n" . $lines . "\n*/ ?>\n";
        if ( preg_match( '/^\[CreateSettings\]\s*$/m', $text, $m, PREG_OFFSET_CAPTURE ) )
        {
            $at = $m[0][1] + strlen( $m[0][0] );
            return substr( $text, 0, $at ) . "\n" . rtrim( $lines, "\n" ) . substr( $text, $at );
        }
        $close = strrpos( $text, '*/' );
        $block = "\n[CreateSettings]\n" . $lines;
        return $close === false ? rtrim( $text ) . "\n" . $block : substr( $text, 0, $close ) . $block . "\n" . substr( $text, $close );
    }

    /**
     * ezwt out, expwt in, at the same place of each list.
     *
     * @param string $text a site.ini.append(.php)
     * @return string|null the new text, null when it has no ezwt
     */
    public static function activationPlan( $text )
    {
        if ( !preg_match( '/^(\s*Active(?:Access)?Extensions\[\]=)ezwt\s*$/m', $text ) )
            return null;
        return preg_replace_callback( '/^(\s*)(Active(?:Access)?Extensions)\[\]=ezwt[ \t]*(\r?\n|$)/m', function ( $m ) use ( $text ) {
            $has = preg_match( '/^\s*' . $m[2] . '\[\]=expwt\s*$/m', $text );
            return $has ? '' : $m[1] . $m[2] . '[]=expwt' . $m[3];
        }, $text );
    }

    // ---------------------------------------------------------------------------------------------- installation

    /**
     * @param string $root the installation's directory
     * @return bool expwt is in the extension directory (active or not)
     */
    public static function isInstalled( $root = '.' )
    {
        return is_file( rtrim( $root, '/' ) . '/extension/expwt/modules/expwt/module.php' );
    }

    /** @return array the settings files the plans read: site (path => text), toolbar, expwt */
    public static function collect( $root = '.' )
    {
        $root = rtrim( $root, '/' );
        $dirs = array_merge( array( $root . '/settings/override' ), (array)glob( $root . '/settings/siteaccess/*', GLOB_ONLYDIR ) );
        $out = array( 'site' => array(), 'toolbar' => array(), 'expwt' => array() );
        foreach ( $dirs as $dir )
        {
            foreach ( array( 'site' => 'site.ini', 'toolbar' => 'websitetoolbar.ini', 'expwt' => 'expwt.ini' ) as $kind => $base )
            {
                foreach ( array( '.append.php', '.append' ) as $suffix )
                {
                    $file = $dir . '/' . $base . $suffix;
                    if ( is_file( $file ) )
                        $out[$kind][$file] = (string)file_get_contents( $file );
                }
            }
        }
        return $out;
    }

    /** @return array id, name, policies of every role */
    public static function roles()
    {
        $out = array();
        foreach ( eZRole::fetchList() as $role )
            $out[] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ),
                            'policies' => expEditorPolicies::policiesOf( $role ) );
        return $out;
    }

    /**
     * Everything that is missing, one line each (empty: in place).
     *
     * @param string $root
     * @return string[]
     */
    public static function changes( $root = '.' )
    {
        $files = self::collect( $root );
        $lines = array();
        foreach ( $files['site'] as $path => $text )
            if ( self::activationPlan( $text ) !== null )
                $lines[] = "replace ezwt by expwt in $path";
        $settings = self::settingsPlan( $files['toolbar'], $files['expwt'] );
        foreach ( array_keys( $settings['writes'] ) as $path )
            $lines[] = "copy the hidden classes of ezwt to $path";
        foreach ( self::policyPlan( self::roles() ) as $grant )
            $lines[] = sprintf( 'grant expwt/%s to %s (%d)%s: %s', $grant['function'], $grant['name'], $grant['role'],
                                $grant['limitations'] ? ' with ' . implode( ', ', array_keys( $grant['limitations'] ) ) : '', $grant['why'] );
        return $lines;
    }

    /** @return string[] what was not copied */
    public static function notes( $root = '.' )
    {
        $files = self::collect( $root );
        $settings = self::settingsPlan( $files['toolbar'], $files['expwt'] );
        return $settings['notes'];
    }

    /**
     * Writes the settings (a copy of each file it changes next to it, <file>.before-expwt) and grants the policies.
     *
     * @param string $root
     * @return string[] what was done
     */
    public static function apply( $root = '.' )
    {
        $done = array();
        $files = self::collect( $root );
        $settings = self::settingsPlan( $files['toolbar'], $files['expwt'] );
        foreach ( $settings['writes'] as $path => $text )
        {
            if ( is_file( $path ) )
                copy( $path, $path . '.before-expwt' );
            file_put_contents( $path, $text );
            $done[] = "wrote $path";
        }
        foreach ( $files['site'] as $path => $text )
        {
            $new = self::activationPlan( $text );
            if ( $new === null )
                continue;
            copy( $path, $path . '.before-expwt' );
            file_put_contents( $path, $new );
            $done[] = "replaced ezwt by expwt in $path";
        }
        $byId = array();
        foreach ( eZRole::fetchList() as $role )
            $byId[(int)$role->attribute( 'id' )] = $role;
        foreach ( self::policyPlan( self::roles() ) as $grant )
        {
            if ( !isset( $byId[$grant['role']] ) )
                continue;
            $byId[$grant['role']]->appendPolicy( 'expwt', $grant['function'], $grant['limitations'] );
            $done[] = sprintf( 'granted expwt/%s to %s (%d)', $grant['function'], $grant['name'], $grant['role'] );
        }
        if ( $done )
        {
            eZRole::expireCache();
            eZCache::clearByTag( 'ini' );
        }
        return $done;
    }
}
