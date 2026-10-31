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
 *              expwt/structure in the same role, with the same limitations (the toolbar gave all of it); several
 *              toolbar policies of one role give one grant per function with the joined limitations where that grants
 *              nothing more, and nothing the role holds already is granted again; the Editor role gets those four
 *              and the Administrator role expwt/* where they do not hold them already
 *   settings   websitetoolbar.ini [WebsiteToolbarSettings] HiddenContentClasses[] of settings/override and of every
 *              siteaccess becomes expwt.ini [CreateSettings] HiddenClassList[] beside it; what has no counterpart
 *              (the eZODF buttons, the custom toolbar templates) is reported, not copied
 *   extension  ActiveExtensions[]=ezwt (settings/override/site.ini.append.php) and ActiveAccessExtensions[]=ezwt
 *              (siteaccesses) become expwt at the same place, or are removed where the file activates expwt already;
 *              ezwt is no longer loaded
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
     * Several websitetoolbar policies of one role give one expwt grant per function where their limitations can be
     * joined without granting more (see mergeLimitations()), else one grant per distinct limitation set. A grant
     * that the role already holds, the same or broader (subsumes()), is not planned again.
     *
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
                         && self::subsumes( isset( $p['limitations'] ) ? (array)$p['limitations'] : array(), $limitations ) ) ) ) )
                        return;
                }
                $policies[] = array( 'module' => 'expwt', 'function' => $function, 'limitations' => $limitations );
                $plan[] = array( 'role' => (int)$role['id'], 'name' => (string)$role['name'], 'function' => $function,
                                 'limitations' => $limitations, 'why' => $why );
            };
            $sources = array();
            $from = array();
            foreach ( $role['policies'] as $policy )
            {
                if ( $policy['module'] !== 'websitetoolbar' || !in_array( $policy['function'], array( 'use', '*' ), true ) )
                    continue;
                $sources[] = isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array();
                $key = 'websitetoolbar/' . $policy['function'];
                $from[$key] = isset( $from[$key] ) ? $from[$key] + 1 : 1;
            }
            $why = array();
            foreach ( $from as $key => $count )
                $why[] = $count > 1 ? "$count $key policies" : $key;
            foreach ( self::FROM_TOOLBAR as $function )
            {
                // what the role (and this plan) does not already give, joined where that grants nothing more
                $held = array();
                foreach ( $policies as $p )
                {
                    if ( $p['module'] === 'expwt' && $p['function'] === $function )
                        $held[] = isset( $p['limitations'] ) ? (array)$p['limitations'] : array();
                }
                $todo = array();
                foreach ( $sources as $limitations )
                {
                    $covered = false;
                    foreach ( $held as $h )
                        $covered = $covered || self::subsumes( $h, $limitations );
                    if ( !$covered )
                        $todo[] = $limitations;
                }
                foreach ( self::mergeLimitations( $todo ) as $limitations )
                    $add( $function, $limitations, implode( ', ', $why ) );
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
     * Whether a policy limited by $broad grants at least everything a policy limited by $narrow grants: each
     * limitation of $broad is also one of $narrow, with the same values or more (no limitations: everything).
     * Node and Subtree in one policy match when either does (eZContentObject::checkAccess()), so a $narrow with both
     * is subsumed only by a $broad without either or with both.
     *
     * @return bool
     */
    public static function subsumes( array $broad, array $narrow )
    {
        if ( isset( $narrow['Node'] ) && isset( $narrow['Subtree'] ) && ( isset( $broad['Node'] ) xor isset( $broad['Subtree'] ) ) )
            return false;
        foreach ( $broad as $identifier => $values )
        {
            if ( !isset( $narrow[$identifier] ) )
                return false;
            if ( array_diff( array_map( 'strval', (array)$narrow[$identifier] ), array_map( 'strval', (array)$values ) ) )
                return false;
        }
        return true;
    }

    /**
     * The fewest limitation sets that grant exactly what the given ones grant together.
     *
     * The kernel grants a policy when every one of its limitations matches (AND) and a limitation matches when one of
     * its values does (OR); two policies of a role give either one's rights (OR). So two sets with the same
     * identifiers that differ in the values of at most one identifier are one set with the union of those values; a
     * set that another one subsumes is dropped. Sets with both Node and Subtree are not joined, as the kernel lets
     * either of those two match (eZContentObject::checkAccess()). Anything else stays one set each.
     *
     * @param array[] $sets each: identifier => values
     * @return array[]
     */
    public static function mergeLimitations( array $sets )
    {
        $out = array();
        foreach ( $sets as $set )
        {
            $set = (array)$set;
            foreach ( $set as $k => $v )
                $set[$k] = array_values( array_map( 'strval', (array)$v ) );
            $out[] = $set;
        }
        do
        {
            $changed = false;
            $count = count( $out );
            for ( $i = 0; $i < $count && !$changed; ++$i )
            {
                for ( $j = 0; $j < $count && !$changed; ++$j )
                {
                    if ( $i === $j )
                        continue;
                    if ( self::subsumes( $out[$i], $out[$j] ) )
                    {
                        array_splice( $out, $j, 1 );
                        $changed = true;
                    }
                    else if ( $j > $i && ( $merged = self::joined( $out[$i], $out[$j] ) ) !== null )
                    {
                        $out[$i] = $merged;
                        array_splice( $out, $j, 1 );
                        $changed = true;
                    }
                }
            }
        }
        while ( $changed );
        return $out;
    }

    /** @return array|null one set granting what $a and $b grant together, or null when there is none */
    private static function joined( array $a, array $b )
    {
        $keys = array_keys( $a );
        sort( $keys );
        $bk = array_keys( $b );
        sort( $bk );
        if ( $keys !== $bk || ( isset( $a['Node'] ) && isset( $a['Subtree'] ) ) )
            return null;
        $differ = array();
        foreach ( $keys as $k )
        {
            $x = $a[$k];
            $y = $b[$k];
            sort( $x );
            sort( $y );
            if ( $x !== $y )
                $differ[] = $k;
        }
        if ( count( $differ ) > 1 )
            return null;
        if ( $differ )
        {
            $k = $differ[0];
            $values = array_values( array_unique( array_merge( $a[$k], $b[$k] ) ) );
            sort( $values, SORT_NATURAL );
            $a[$k] = $values;
        }
        return $a;
    }

    /**
     * @param array $limitations identifier => values
     * @return string "Class 1, 2, 5; Section 1", or "" without limitations
     */
    public static function limitationText( array $limitations )
    {
        $parts = array();
        foreach ( $limitations as $identifier => $values )
            $parts[] = $identifier . ' ' . implode( ', ', array_map( 'strval', (array)$values ) );
        return implode( '; ', $parts );
    }

    /** @return string a grant of policyPlan() as one line, without the verb */
    public static function grantText( array $grant )
    {
        $limits = self::limitationText( $grant['limitations'] );
        return sprintf( 'expwt/%s to %s (%d)%s (from %s)', $grant['function'], $grant['name'], $grant['role'],
                        $limits !== '' ? ' limited to ' . $limits : '', $grant['why'] );
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
        $change = self::activationChange( $text );
        return $change === null ? null : $change['text'];
    }

    /**
     * ezwt out of ActiveExtensions[] and ActiveAccessExtensions[]: where the file activates expwt already (before or
     * after ezwt, in either list), every ezwt line is removed (action "remove"); else the first ezwt line of each list
     * becomes expwt at the same place and any further one is removed (action "replace").
     *
     * @param string $text a site.ini.append(.php)
     * @return array|null text (the new text), action ("replace" or "remove"); null when it has no ezwt
     */
    public static function activationChange( $text )
    {
        if ( !preg_match( '/^[ \t]*Active(?:Access)?Extensions\[\]=ezwt[ \t]*\r?$/m', $text ) )
            return null;
        $active = (bool)preg_match( '/^[ \t]*Active(?:Access)?Extensions\[\]=expwt[ \t]*\r?$/m', $text );
        $placed = array();
        $new = preg_replace_callback( '/^([ \t]*)(Active(?:Access)?Extensions)\[\]=ezwt[ \t]*(\r?\n|\r?$)/m',
            function ( $m ) use ( $active, &$placed ) {
                if ( $active || isset( $placed[$m[2]] ) )
                    return '';
                $placed[$m[2]] = true;
                return $m[1] . $m[2] . '[]=expwt' . $m[3];
            }, $text );
        return array( 'text' => $new, 'action' => $active ? 'remove' : 'replace' );
    }

    /** @return string the line for an activation change of $path, as planned or as done */
    private static function activationText( array $change, $path, $done )
    {
        if ( $change['action'] === 'remove' )
            return ( $done ? 'removed' : 'remove' ) . " ezwt (expwt already active) from $path";
        return ( $done ? 'replaced' : 'replace' ) . " ezwt by expwt in $path";
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
        {
            $change = self::activationChange( $text );
            if ( $change !== null )
                $lines[] = self::activationText( $change, $path, false );
        }
        $settings = self::settingsPlan( $files['toolbar'], $files['expwt'] );
        foreach ( array_keys( $settings['writes'] ) as $path )
            $lines[] = "copy the hidden classes of ezwt to $path";
        foreach ( self::policyPlan( self::roles() ) as $grant )
            $lines[] = 'grant ' . self::grantText( $grant );
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
            $change = self::activationChange( $text );
            if ( $change === null )
                continue;
            copy( $path, $path . '.before-expwt' );
            file_put_contents( $path, $change['text'] );
            $done[] = self::activationText( $change, $path, true );
        }
        $byId = array();
        foreach ( eZRole::fetchList() as $role )
            $byId[(int)$role->attribute( 'id' )] = $role;
        foreach ( self::policyPlan( self::roles() ) as $grant )
        {
            if ( !isset( $byId[$grant['role']] ) )
                continue;
            $byId[$grant['role']]->appendPolicy( 'expwt', $grant['function'], $grant['limitations'] );
            $done[] = 'granted ' . self::grantText( $grant );
        }
        if ( $done )
        {
            eZRole::expireCache();
            eZCache::clearByTag( 'ini' );
        }
        return $done;
    }
}
