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
 *              nothing more, and nothing the role holds already is granted again; the Editor role gets expwt/* without
 *              limitations in place of narrower expwt grants (F22), the Administrator role expwt/* where it does not
 *              hold it; the role of visitors who are not signed in (Anonymous) never gets anything
 *   settings   websitetoolbar.ini [WebsiteToolbarSettings] HiddenContentClasses[] of settings/override and of every
 *              siteaccess becomes expwt.ini [CreateSettings] HiddenClassList[] beside it; what has no counterpart
 *              (the eZODF buttons, the custom toolbar templates) is reported, not copied
 *   extension  ActiveExtensions[]=ezwt (settings/override/site.ini.append.php) and ActiveAccessExtensions[]=ezwt
 *              (siteaccesses) become expwt at the same place, or are removed where the file activates expwt already;
 *              ezwt is no longer loaded
 *
 * Where ezwt was active, the sidebar is switched on: expwt.ini [ExpwtSettings] Enabled=enabled beside the
 * site.ini that activated it, unless that file sets Enabled already (F18); elsewhere it stays at expwt's default.
 *
 * The schedule of earlier expwt versions (F21): an entry in the site data table per draft to publish later
 * (expwt_publish_<object>_<version>) and per page to take down (expwt_unpublish_<node>), carried out by expwt's own
 * cronjob, which is gone. Each entry moves to the kernel's mechanism (expContentSchedule): its dates go into the
 * date fields of the draft (or, for a take-down, of the published version), the draft is published through
 * content/publish as the user who scheduled it (the wait-until-date event holds it until its date, approvals apply)
 * and the entry is removed. An entry that cannot move yet (the class has no date field the kernel reads, the user is
 * gone) stays and is reported as a note; one whose draft was published or removed meanwhile is removed.
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
    const ANONYMOUS_ROLE = 'Anonymous';

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
            // never the role of visitors who are not signed in: expwt/use there is the site's own decision
            // (expwt.ini ShowToAnonymous=policy), never one this migration makes
            if ( self::isAnonymousRole( $role ) )
                continue;
            // F22: the Editor role holds expwt/* without limitations (the kernel policies of what an editor changes
            // still apply), in place of the narrower expwt grants it may hold
            if ( $role['name'] === self::EDITOR_ROLE )
            {
                foreach ( self::editorPlan( $role ) as $step )
                    $plan[] = $step;
                continue;
            }
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
    /**
     * The role of visitors who are not signed in: named Anonymous, or assigned to the anonymous user (roles() marks
     * it, anonymous: true).
     *
     * @param array $role
     * @return bool
     */
    public static function isAnonymousRole( array $role )
    {
        return !empty( $role['anonymous'] ) || strcasecmp( (string)$role['name'], self::ANONYMOUS_ROLE ) === 0;
    }

    /**
     * F22: the Editor role's steps to expwt/* without limitations: nothing when it holds that already (or every
     * module, *); else the grant, and every other expwt policy of the role taken away (expwt/<function>, or expwt/*
     * with limitations), which the grant covers. Running it again plans nothing.
     *
     * @param array $role id, name, policies (each: id, module, function, limitations)
     * @return array steps: role, name, function, limitations, why, and remove (the policy's id) for a removal
     */
    public static function editorPlan( array $role )
    {
        foreach ( $role['policies'] as $p )
        {
            $limited = !empty( $p['limitations'] );
            if ( ( $p['module'] === '*' || ( $p['module'] === 'expwt' && $p['function'] === '*' ) ) && !$limited )
                return array();
        }
        $steps = array( array( 'role' => (int)$role['id'], 'name' => (string)$role['name'], 'function' => '*',
                               'limitations' => array(), 'why' => 'the Editor role, without limitations' ) );
        foreach ( $role['policies'] as $p )
        {
            if ( $p['module'] !== 'expwt' || !isset( $p['id'] ) )
                continue;
            $steps[] = array( 'role' => (int)$role['id'], 'name' => (string)$role['name'], 'function' => (string)$p['function'],
                              'limitations' => isset( $p['limitations'] ) ? (array)$p['limitations'] : array(),
                              'why' => 'replaced by expwt/* without limitations', 'remove' => (int)$p['id'] );
        }
        return $steps;
    }

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
     * F18: where ezwt was active, the sidebar is switched on: for every site.ini.append(.php) that activates ezwt
     * (ActiveExtensions[] in settings/override: every siteaccess; ActiveAccessExtensions[] in a siteaccess: that one),
     * expwt.ini [ExpwtSettings] Enabled=enabled beside it, unless that file sets Enabled already (whatever to: the
     * installation's choice stays). Pure.
     *
     * @param array $site path of a site.ini.append(.php) => its text
     * @param array $existing path of an expwt.ini.append.php => its text (the files there are, or will be, there)
     * @return array path of an expwt.ini.append.php => its new text
     */
    public static function enablePlan( array $site, array $existing = array() )
    {
        $writes = array();
        foreach ( $site as $path => $text )
        {
            if ( self::activationChange( $text ) === null )
                continue;
            $target = dirname( $path ) . '/expwt.ini.append.php';
            $current = isset( $writes[$target] ) ? $writes[$target] : ( isset( $existing[$target] ) ? $existing[$target] : null );
            if ( $current !== null && preg_match( '/^\s*Enabled\s*=/m', $current ) )
                continue;
            $writes[$target] = self::withEnabled( $current );
        }
        return $writes;
    }

    /**
     * @param string|null $text an expwt.ini.append.php there is, or null
     * @return string the file with [ExpwtSettings] Enabled=enabled
     */
    public static function withEnabled( $text )
    {
        $line = "# switched on where ezwt was active, by exp:wt:migrate-from-ezwt\nEnabled=enabled\n";
        if ( $text === null || trim( $text ) === '' )
            return "<?php /* #?ini charset=\"utf-8\"?\n\n[ExpwtSettings]\n" . $line . "\n*/ ?>\n";
        if ( preg_match( '/^\[ExpwtSettings\]\s*$/m', $text, $m, PREG_OFFSET_CAPTURE ) )
        {
            $at = $m[0][1] + strlen( $m[0][0] );
            return substr( $text, 0, $at ) . "\n" . rtrim( $line, "\n" ) . substr( $text, $at );
        }
        $close = strrpos( $text, '*/' );
        $block = "\n[ExpwtSettings]\n" . $line;
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

    /** @return array id, name, policies, anonymous (the role is assigned to the anonymous user) of every role */
    public static function roles()
    {
        $anonymous = array();
        $anonymousId = (int)eZINI::instance()->variable( 'UserSettings', 'AnonymousUserID' );
        if ( $anonymousId > 0 )
            $anonymous = array_map( 'intval', (array)eZRole::fetchIDListByUser( array( $anonymousId ) ) );
        $out = array();
        foreach ( eZRole::fetchList() as $role )
            $out[] = array( 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ),
                            'policies' => expEditorPolicies::policiesOf( $role ),
                            'anonymous' => in_array( (int)$role->attribute( 'id' ), $anonymous, true ) );
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
        foreach ( array_keys( self::enablePlan( $files['site'], $files['expwt'] ) ) as $path )
            $lines[] = "switch the sidebar on (Enabled=enabled) in $path, where ezwt was active";
        foreach ( self::policyPlan( self::roles() ) as $grant )
            $lines[] = ( isset( $grant['remove'] ) ? 'take away ' : 'grant ' ) . self::grantText( $grant );
        foreach ( self::scheduleChecked() as $step )
        {
            if ( $step['state'] !== 'blocked' )
                $lines[] = self::scheduleText( $step, false );
        }
        return $lines;
    }

    /** @return string[] what was not copied, and the schedule entries that cannot move yet */
    public static function notes( $root = '.' )
    {
        $files = self::collect( $root );
        $settings = self::settingsPlan( $files['toolbar'], $files['expwt'] );
        $notes = $settings['notes'];
        foreach ( self::scheduleChecked() as $step )
        {
            if ( $step['state'] === 'blocked' )
                $notes[] = self::scheduleText( $step, false );
        }
        return $notes;
    }

    // ------------------------------------------------------------------------------------------ schedule (F21)

    /** The names of the site data rows of the schedule of earlier expwt versions */
    const SCHEDULE_PUBLISH = 'expwt_publish_';
    const SCHEDULE_TAKE_DOWN = 'expwt_unpublish_';

    /**
     * The schedule entries of earlier expwt versions as steps. Pure.
     *
     * @param array $entries name => value (the JSON text of the row, or its decoded array)
     * @return array each: name, kind (publish, takedown, or drop for an entry that cannot be read), object, version,
     *               node, user, at, until, state (move; drop for kind drop), why
     */
    public static function schedulePlan( array $entries )
    {
        $out = array();
        ksort( $entries, SORT_STRING );
        foreach ( $entries as $name => $value )
        {
            $name = (string)$name;
            $data = is_array( $value ) ? $value : json_decode( (string)$value, true );
            $step = array( 'name' => $name, 'kind' => 'drop', 'object' => null, 'version' => null, 'node' => null,
                           'user' => null, 'at' => null, 'until' => null, 'state' => 'drop', 'why' => 'it cannot be read' );
            if ( is_array( $data ) && isset( $data['type'], $data['user'], $data['at'] ) && (int)$data['user'] > 0 && (int)$data['at'] > 0 )
            {
                if ( $data['type'] === 'publish' && strpos( $name, self::SCHEDULE_PUBLISH ) === 0
                     && isset( $data['object'], $data['version'] ) && (int)$data['object'] > 0 && (int)$data['version'] > 0 )
                {
                    $step = array( 'kind' => 'publish', 'object' => (int)$data['object'], 'version' => (int)$data['version'],
                                   'user' => (int)$data['user'], 'at' => (int)$data['at'],
                                   'until' => !empty( $data['until'] ) ? (int)$data['until'] : null, 'state' => 'move', 'why' => '' ) + $step;
                }
                else if ( $data['type'] === 'unpublish' && strpos( $name, self::SCHEDULE_TAKE_DOWN ) === 0
                          && isset( $data['node'] ) && (int)$data['node'] > 0 )
                {
                    $step = array( 'kind' => 'takedown', 'node' => (int)$data['node'], 'user' => (int)$data['user'],
                                   'at' => (int)$data['at'], 'state' => 'move', 'why' => '' ) + $step;
                }
            }
            $out[] = $step;
        }
        return $out;
    }

    /**
     * A step after checking it against the installation, pure: the facts are given.
     *
     * @param array $step of schedulePlan()
     * @param array $facts exists (the object or node is there), draft (the version is still a draft), user (the user is
     *                     there), publish, takeDown (expContentSchedule::forObject()), now
     * @return array the step with state move, drop or blocked and why
     */
    public static function scheduleDecide( array $step, array $facts )
    {
        if ( $step['state'] !== 'move' )
            return $step;
        if ( empty( $facts['exists'] ) )
            return array( 'state' => 'drop', 'why' => $step['kind'] === 'publish' ? 'the draft is gone' : 'the page is gone' ) + $step;
        if ( $step['kind'] === 'publish' && empty( $facts['draft'] ) )
            return array( 'state' => 'drop', 'why' => 'the version is no longer a draft (published, sent to a workflow or archived)' ) + $step;
        if ( empty( $facts['user'] ) )
            return array( 'state' => 'blocked', 'why' => 'the user who scheduled it is gone' ) + $step;
        $waits = $step['kind'] === 'publish' && $step['at'] > (int)$facts['now'];
        if ( $waits && empty( $facts['publish'] ) )
            return array( 'state' => 'blocked', 'why' => 'its class has no date field a wait-until-date event of a content/publish workflow lists' ) + $step;
        $until = $step['kind'] === 'publish' ? $step['until'] : $step['at'];
        if ( $until !== null && empty( $facts['takeDown'] ) )
            return array( 'state' => 'blocked', 'why' => 'its class has no date field in content.ini [HideSettings] HideDateAttributeList, or the page is not below a node of RootNodeList' ) + $step;
        return $step;
    }

    /** @return string a step as one line */
    public static function scheduleText( array $step, $done )
    {
        $date = function ( $ts ) { return $ts ? date( 'Y-m-d H:i', (int)$ts ) : ''; };
        if ( $step['state'] === 'drop' )
            return ( $done ? 'removed' : 'remove' ) . " the sidebar's schedule entry {$step['name']} ({$step['why']})";
        if ( $step['state'] === 'blocked' )
            return "the sidebar's schedule entry {$step['name']} cannot move to the kernel's schedule yet: {$step['why']}; it stays until it can";
        if ( $step['kind'] === 'publish' )
            return ( $done ? 'moved' : 'move' ) . " the sidebar's schedule entry {$step['name']} to the kernel's schedule: publish object {$step['object']} version {$step['version']} at "
                . $date( $step['at'] ) . ( $step['until'] ? ', take down at ' . $date( $step['until'] ) : '' );
        return ( $done ? 'moved' : 'move' ) . " the sidebar's schedule entry {$step['name']} to the kernel's schedule: take node {$step['node']} down at " . $date( $step['at'] );
    }

    /** @return array name => JSON text, every schedule entry of earlier expwt versions */
    public static function scheduleEntries()
    {
        $out = array();
        foreach ( array( self::SCHEDULE_PUBLISH, self::SCHEDULE_TAKE_DOWN ) as $prefix )
        {
            $count = (int)eZSiteData::fetchListCount( $prefix . '*' );
            for ( $offset = 0; $offset < $count; $offset += 500 )
            {
                foreach ( (array)eZSiteData::fetchList( $prefix . '*', $offset, 500 ) as $row )
                    $out[(string)$row->attribute( 'name' )] = (string)$row->attribute( 'value' );
            }
        }
        return $out;
    }

    /**
     * The steps of schedulePlan() checked against the installation (scheduleDecide()).
     *
     * @return array each step with the objects found: object (eZContentObject), version, node
     */
    public static function scheduleChecked()
    {
        $out = array();
        $now = time();
        foreach ( self::schedulePlan( self::scheduleEntries() ) as $step )
        {
            $facts = array( 'exists' => false, 'draft' => false, 'user' => false, 'publish' => null, 'takeDown' => null, 'now' => $now );
            $found = array( 'objectFound' => null, 'versionFound' => null, 'nodeFound' => null );
            if ( $step['kind'] === 'publish' )
            {
                $object = eZContentObject::fetch( $step['object'] );
                $version = $object instanceof eZContentObject ? $object->version( $step['version'] ) : null;
                if ( $version instanceof eZContentObjectVersion )
                {
                    $facts['exists'] = true;
                    $facts['draft'] = in_array( (int)$version->attribute( 'status' ), array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ), true );
                    $found = array( 'objectFound' => $object, 'versionFound' => $version ) + $found;
                }
            }
            else if ( $step['kind'] === 'takedown' )
            {
                $node = eZContentObjectTreeNode::fetch( $step['node'] );
                $object = $node instanceof eZContentObjectTreeNode ? $node->object() : null;
                if ( $object instanceof eZContentObject )
                {
                    $facts['exists'] = true;
                    $found = array( 'objectFound' => $object, 'versionFound' => $object->currentVersion(), 'nodeFound' => $node ) + $found;
                }
            }
            if ( $facts['exists'] )
            {
                $facts['user'] = $step['user'] && eZUser::fetch( $step['user'] ) instanceof eZUser;
                $parentPath = null;
                if ( $found['nodeFound'] )
                {
                    $parent = $found['nodeFound']->attribute( 'parent' );
                    $parentPath = $parent instanceof eZContentObjectTreeNode ? (string)$parent->attribute( 'path_string' ) : '';
                }
                $can = expContentSchedule::forObject( $found['objectFound'], $parentPath );
                $facts['publish'] = $can['publish'];
                $facts['takeDown'] = $can['takeDown'];
            }
            $step = self::scheduleDecide( $step, $facts );
            $out[] = $step + $found + array( 'can' => $facts );
        }
        return $out;
    }

    /**
     * Moves every schedule entry that can move to the kernel's mechanism and removes the stale ones.
     *
     * @return string[] what was done
     */
    public static function applySchedule()
    {
        $done = array();
        $now = time();
        $before = eZUser::currentUserID();
        foreach ( self::scheduleChecked() as $step )
        {
            if ( $step['state'] === 'blocked' )
                continue;
            if ( $step['state'] === 'move' )
            {
                $user = eZUser::fetch( $step['user'] );
                eZUser::setCurrentlyLoggedInUser( $user, (int)$step['user'], eZUser::NO_SESSION_REGENERATE );
                try
                {
                    $line = self::scheduleText( $step, true ) . self::moveScheduleStep( $step, $now );
                }
                catch ( Exception $e )
                {
                    $line = "FAILED to move the sidebar's schedule entry {$step['name']}: " . $e->getMessage() . ' (it stays)';
                    eZDebug::writeError( $line, __METHOD__ );
                    $done[] = $line;
                    continue;
                }
            }
            else
                $line = self::scheduleText( $step, true );
            $row = eZSiteData::fetchByName( $step['name'] );
            if ( $row instanceof eZSiteData )
                $row->remove();
            $done[] = $line;
        }
        $back = eZUser::fetch( $before );
        if ( $back instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $back, $before, eZUser::NO_SESSION_REGENERATE );
        return $done;
    }

    /** @return string what came of it, appended to the step's line */
    private static function moveScheduleStep( array $step, $now )
    {
        $can = $step['can'];
        $object = $step['objectFound'];
        $id = (int)$object->attribute( 'id' );
        if ( $step['kind'] === 'takedown' )
        {
            $plan = expContentSchedule::plan( null, $can['takeDown'], null, $step['at'], 0, 0 );
            expContentSchedule::setDates( $step['versionFound'], $plan['writes'] );
            eZContentCacheManager::clearContentCacheIfNeeded( $id );
            eZContentObject::clearCache( array( $id ) );
            return ' (the hide cronjob takes it down)';
        }
        $version = $step['versionFound'];
        $current = $can['publish'] ? expContentSchedule::dateOf( $version, $can['publish']['identifier'] ) : 0;
        $plan = expContentSchedule::plan( $can['publish'], $can['takeDown'], $step['at'], $step['until'], $now, $current );
        if ( $plan['error'] !== null )
            throw new RuntimeException( $plan['error'] );
        expContentSchedule::setDates( $version, $plan['writes'] );
        $result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => (int)$step['version'] ) );
        eZContentObject::clearCache( array( $id ) );
        $status = expGraphQLWriter::workflowStatus( $result, 'PUBLISHED' );
        if ( $status === 'PENDING' )
            return $plan['deferred'] ? ' (waiting in the workflow until its date)' : ' (waiting in the workflow)';
        return $status === 'PUBLISHED' ? ' (it was due: published)' : ' (the workflow stopped it: ' . strtolower( $status ) . ')';
    }

    /**
     * Copies a settings file to <file>.before-expwt with the original's mode, owner and group: a settings file can
     * hold credentials, and a plain copy() would leave the backup readable by everybody.
     *
     * @param string $path
     */
    private static function backup( $path )
    {
        $backup = $path . '.before-expwt';
        if ( !copy( $path, $backup ) )
            return;
        $perms = @fileperms( $path );
        if ( $perms !== false )
            @chmod( $backup, eZFile::fileMode( $perms & 0777 ) );
        $owner = @fileowner( $path );
        $group = @filegroup( $path );
        if ( $owner !== false )
            @chown( $backup, $owner );
        if ( $group !== false )
            @chgrp( $backup, $group );
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
                self::backup( $path );
            file_put_contents( $path, $text );
            $done[] = "wrote $path";
        }
        // F18: where ezwt was active, the sidebar is switched on (planned from the files as they were, before the
        // activation changes below take ezwt out; on top of the hidden classes just written)
        $written = $files['expwt'];
        foreach ( $settings['writes'] as $path => $text )
            $written[$path] = $text;
        foreach ( self::enablePlan( $files['site'], $written ) as $path => $text )
        {
            if ( is_file( $path ) && !isset( $settings['writes'][$path] ) )
                self::backup( $path );
            file_put_contents( $path, $text );
            $done[] = "switched the sidebar on in $path";
        }
        foreach ( $files['site'] as $path => $text )
        {
            $change = self::activationChange( $text );
            if ( $change === null )
                continue;
            self::backup( $path );
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
            if ( isset( $grant['remove'] ) )
            {
                eZPolicy::removeByID( (int)$grant['remove'] );
                $done[] = 'took away ' . self::grantText( $grant );
                continue;
            }
            $byId[$grant['role']]->appendPolicy( 'expwt', $grant['function'], $grant['limitations'] );
            $done[] = 'granted ' . self::grantText( $grant );
        }
        if ( $done )
        {
            eZRole::expireCache();
            eZCache::clearByTag( 'ini' );
        }
        foreach ( self::applySchedule() as $line )
            $done[] = $line;
        return $done;
    }
}
