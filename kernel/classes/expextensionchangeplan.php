<?php
/**
 * File containing the expExtensionChangePlan class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The change planner of Setup > Extensions: what a list of active extensions becomes when an administrator moves,
 * activates or deactivates one, what differs from the list in the file, and what is risky about it.
 *
 * Pure: it works on names and on the facts expExtensionCatalogue collects (each extension's declared dependencies,
 * designs and settings files), never on the disk or the database, so every rule here is tested without either
 * (tests/tests/kernel/classes/setup/ExtensionChangePlanTest.php). Writing is ezpActiveExtensions' job.
 *
 * Facts, per extension name (missing keys count as empty):
 *   requires, uses, extends  names from extension.xml <dependencies>
 *   designs                  design directories it provides
 *   ini_bases                settings files it ships as a full .ini (name without .ini)
 *   ini_appends              settings files it ships as .ini.append(.php) (name without suffix)
 *   installed                false when the directory is missing
 *
 * Context:
 *   access          names in ActiveAccessExtensions of any siteaccess (they count as present)
 *   design_users    design => list of siteaccesses that use it (SiteDesign or AdditionalSiteDesignList)
 *   core_designs    designs the kernel itself ships (design/<name>), which never depend on an extension
 *   ordering        true when site.ini [ExtensionSettings] ExtensionOrdering=enabled
 *
 * Guide: doc/guides/extensions-page.md
 */
class expExtensionChangePlan
{
    /**
     * Extensions whose removal changes more than their own features. Name => what goes with it.
     */
    public static $critical = array(
        'ezformtoken' => 'the CSRF protection of every form',
        'ezjscore'    => 'the scripts and styles of the administration',
    );

    /** @var array the list as the file has it */
    public $current = array();
    /** @var array the list as planned */
    public $planned = array();
    /** @var array name => facts */
    public $facts = array();
    /** @var array see the class comment */
    public $context = array();

    public function __construct( array $current, array $planned, array $facts = array(), array $context = array() )
    {
        $this->current = self::clean( $current );
        $this->planned = self::clean( $planned );
        $this->facts = $facts;
        $this->context = $context + array( 'access' => array(), 'design_users' => array(), 'core_designs' => array(), 'ordering' => true );
    }

    /**
     * Names as strings, without empty entries and repeats, in their order.
     */
    public static function clean( array $list )
    {
        $out = array();
        foreach ( $list as $name )
        {
            $name = trim( (string)$name );
            if ( $name !== '' && !in_array( $name, $out, true ) )
                $out[] = $name;
        }
        return $out;
    }

    /**
     * A posted list: only names of extensions that exist, each once. Anything else is dropped.
     */
    public static function fromPost( $posted, array $available )
    {
        if ( !is_array( $posted ) )
            return array();
        $list = array();
        foreach ( $posted as $name )
        {
            if ( is_string( $name ) && in_array( $name, $available, true ) && !in_array( $name, $list, true ) )
                $list[] = $name;
        }
        return $list;
    }

    /**
     * A fingerprint of a list, carried in the form to notice a change made elsewhere in between.
     */
    public static function fingerprint( array $list )
    {
        return md5( implode( "\n", self::clean( $list ) ) );
    }

    // ---- Edits: each takes a list and returns the new one ---------------------------------------------------------

    public static function moveUp( array $list, $name )
    {
        $list = self::clean( $list );
        $i = array_search( $name, $list, true );
        if ( $i === false || $i === 0 )
            return $list;
        $list[$i] = $list[$i - 1];
        $list[$i - 1] = $name;
        return $list;
    }

    public static function moveDown( array $list, $name )
    {
        $list = self::clean( $list );
        $i = array_search( $name, $list, true );
        if ( $i === false || $i === count( $list ) - 1 )
            return $list;
        $list[$i] = $list[$i + 1];
        $list[$i + 1] = $name;
        return $list;
    }

    /**
     * Moves $name to position $position (1 = loaded first), clamped to the list.
     */
    public static function moveTo( array $list, $name, $position )
    {
        $list = self::clean( $list );
        $i = array_search( $name, $list, true );
        if ( $i === false )
            return $list;
        array_splice( $list, $i, 1 );
        $to = max( 0, min( count( $list ), (int)$position - 1 ) );
        array_splice( $list, $to, 0, array( $name ) );
        return $list;
    }

    public static function deactivate( array $list, $name )
    {
        return array_values( array_filter( self::clean( $list ), function ( $n ) use ( $name ) { return $n !== $name; } ) );
    }

    /**
     * Adds $name where its dependencies want it: after everything it requires or uses and everything that extends
     * it, before everything it extends and everything that requires or uses it. With nothing to honour it goes at
     * the end. When the two
     * bounds contradict each other it also goes at the end, and the problems say why.
     */
    public static function activate( array $list, $name, array $facts = array() )
    {
        $list = self::clean( $list );
        if ( in_array( $name, $list, true ) )
            return $list;
        $own = self::factsOf( $facts, $name );
        $lower = 0;
        foreach ( array_merge( $own['requires'], $own['uses'] ) as $dep )
        {
            $i = array_search( $dep, $list, true );
            if ( $i !== false )
                $lower = max( $lower, $i + 1 );
        }
        $upper = null;
        foreach ( $own['extends'] as $dep )
        {
            $i = array_search( $dep, $list, true );
            if ( $i !== false )
                $upper = $upper === null ? $i : min( $upper, $i );
        }
        foreach ( $list as $i => $other )
        {
            $f = self::factsOf( $facts, $other );
            if ( in_array( $name, array_merge( $f['requires'], $f['uses'] ), true ) )
                $upper = $upper === null ? $i : min( $upper, $i );
            if ( in_array( $name, $f['extends'], true ) )
                $lower = max( $lower, $i + 1 );
        }
        $at = ( $upper !== null && $upper >= $lower ) ? $upper : count( $list );
        array_splice( $list, $at, 0, array( $name ) );
        return $list;
    }

    /**
     * Applies one posted action to a list: array( 'up'|'down'|'activate'|'deactivate'|'top'|'bottom', name ).
     */
    public static function apply( array $list, $action, $name, array $facts = array() )
    {
        switch ( $action )
        {
            case 'up':         return self::moveUp( $list, $name );
            case 'down':       return self::moveDown( $list, $name );
            case 'top':        return self::moveTo( $list, $name, 1 );
            case 'bottom':     return self::moveTo( $list, $name, count( $list ) );
            case 'activate':   return self::activate( $list, $name, $facts );
            case 'deactivate': return self::deactivate( $list, $name );
        }
        return self::clean( $list );
    }

    // ---- What differs ----------------------------------------------------------------------------------------------

    public function changed()
    {
        return $this->current !== $this->planned;
    }

    /**
     * added, removed and moved: moved are the extensions in both lists that changed their place relative to the
     * others, as few as possible (everything outside a longest common subsequence).
     */
    public function diff()
    {
        $added = array_values( array_diff( $this->planned, $this->current ) );
        $removed = array_values( array_diff( $this->current, $this->planned ) );
        $a = array_values( array_intersect( $this->current, $this->planned ) );
        $b = array_values( array_intersect( $this->planned, $this->current ) );
        $keep = self::lcs( $a, $b );
        $moved = array();
        foreach ( $b as $name )
        {
            if ( !in_array( $name, $keep, true ) )
                $moved[] = $name;
        }
        return array( 'added' => $added, 'removed' => $removed, 'moved' => $moved );
    }

    private static function lcs( array $a, array $b )
    {
        $n = count( $a );
        $m = count( $b );
        $t = array_fill( 0, $n + 1, array_fill( 0, $m + 1, 0 ) );
        for ( $i = $n - 1; $i >= 0; $i-- )
            for ( $j = $m - 1; $j >= 0; $j-- )
                $t[$i][$j] = $a[$i] === $b[$j] ? $t[$i + 1][$j + 1] + 1 : max( $t[$i + 1][$j], $t[$i][$j + 1] );
        $out = array();
        for ( $i = 0, $j = 0; $i < $n && $j < $m; )
        {
            if ( $a[$i] === $b[$j] ) { $out[] = $a[$i]; $i++; $j++; }
            else if ( $t[$i + 1][$j] >= $t[$i][$j + 1] ) $i++;
            else $j++;
        }
        return $out;
    }

    /**
     * The lines the file will hold: the reset line, then one per extension.
     */
    public static function lines( array $list )
    {
        $lines = array( 'ActiveExtensions[]' );
        foreach ( self::clean( $list ) as $name )
            $lines[] = 'ActiveExtensions[]=' . $name;
        return $lines;
    }

    /**
     * The order the kernel loads $list in when ExtensionOrdering is enabled: eZExtension::extensionOrdering() on
     * the declared dependencies, with the same fallback (the list as it is) on a cycle.
     */
    public function effectiveOrder( ?array $list = null )
    {
        $list = $list === null ? $this->planned : self::clean( $list );
        if ( !$this->context['ordering'] || !$list || !class_exists( 'ezpTopologicalSort' ) )
            return $list;
        $set = array_flip( $list );
        $deps = array();
        foreach ( $list as $name )
        {
            if ( !isset( $deps[$name] ) )
                $deps[$name] = array();
            $f = self::factsOf( $this->facts, $name );
            foreach ( array_merge( $f['requires'], $f['uses'] ) as $dep )
                if ( isset( $set[$dep] ) )
                    $deps[$name][] = $dep;
            foreach ( $f['extends'] as $dep )
                if ( isset( $set[$dep] ) )
                    $deps[$dep][] = $name;
        }
        $sort = new ezpTopologicalSort( $deps );
        $sorted = $sort->sort();
        return $sorted !== false ? $sorted : $list;
    }

    // ---- Problems --------------------------------------------------------------------------------------------------

    /**
     * Problems of a list as it stands, per extension: array( name => array( array( code, severity, params ) ) ).
     *
     * Codes: not_installed (bad), requires_missing (bad), requires_later and extends_earlier (written in a place
     * its dependencies do not allow: warn, or info when ExtensionOrdering is enabled, because the kernel then
     * loads it in the right place anyway), settings_lose (info: it ships settings for a file another extension
     * ships in full, and that one loads earlier, so it wins on single values; judged on the order the kernel
     * really loads in).
     */
    public function problems( ?array $list = null )
    {
        $list = $list === null ? $this->planned : self::clean( $list );
        $present = array_merge( $list, $this->context['access'] );
        $pos = array_flip( $list );
        $placement = $this->context['ordering'] ? 'info' : 'warn';
        $loads = $this->effectiveOrder( $list );
        $out = array();
        foreach ( $list as $name )
        {
            $f = self::factsOf( $this->facts, $name );
            if ( !$f['installed'] )
                $out[$name][] = array( 'not_installed', 'bad', array() );
            foreach ( $f['requires'] as $dep )
            {
                if ( !in_array( $dep, $present, true ) )
                    $out[$name][] = array( 'requires_missing', 'bad', array( 'other' => $dep ) );
                else if ( isset( $pos[$dep] ) && $pos[$dep] > $pos[$name] )
                    $out[$name][] = array( 'requires_later', $placement, array( 'other' => $dep ) );
            }
            foreach ( $f['uses'] as $dep )
            {
                if ( isset( $pos[$dep] ) && $pos[$dep] > $pos[$name] )
                    $out[$name][] = array( 'requires_later', $placement, array( 'other' => $dep ) );
            }
            foreach ( $f['extends'] as $dep )
            {
                if ( isset( $pos[$dep] ) && $pos[$dep] < $pos[$name] )
                    $out[$name][] = array( 'extends_earlier', $placement, array( 'other' => $dep ) );
            }
            foreach ( $this->settingsLosses( $loads, $name ) as $loss )
                $out[$name][] = array( 'settings_lose', 'info', $loss );
        }
        return $out;
    }

    /**
     * Files $name ships as an append for which an extension loaded earlier in $list ships the full .ini: that one's
     * values win over $name's single values (an extension loaded earlier has the higher settings priority).
     */
    public function settingsLosses( array $list, $name )
    {
        $pos = array_flip( $list );
        if ( !isset( $pos[$name] ) )
            return array();
        $own = self::factsOf( $this->facts, $name );
        $out = array();
        foreach ( $own['ini_appends'] as $file )
        {
            foreach ( $list as $other )
            {
                if ( $other === $name || $pos[$other] > $pos[$name] )
                    continue;
                if ( in_array( $file, self::factsOf( $this->facts, $other )['ini_bases'], true ) )
                    $out[] = array( 'other' => $other, 'file' => $file . '.ini' );
            }
        }
        return $out;
    }

    /**
     * What is risky about going from the current list to the planned one, as a list of
     * array( code, severity, params ); severity 'bad' asks for an explicit confirmation.
     *
     * Codes: removes_required (another active extension requires it), removes_design (a siteaccess uses a design
     * only it provides), removes_critical, new_problem (a problem the planned list has and the current one has not:
     * requires_missing, and requires_later and extends_earlier where the kernel does not correct them, and
     * settings_lose), no_effect (only the order changed, and the kernel loads the extensions in the same order as
     * before, because their declared dependencies decide).
     */
    public function risks()
    {
        $out = array();
        $diff = $this->diff();
        foreach ( $diff['removed'] as $name )
        {
            foreach ( $this->planned as $other )
            {
                if ( in_array( $name, self::factsOf( $this->facts, $other )['requires'], true ) && !in_array( $name, $this->context['access'], true ) )
                    $out[] = array( 'removes_required', 'bad', array( 'name' => $name, 'other' => $other ) );
            }
            foreach ( self::factsOf( $this->facts, $name )['designs'] as $design )
            {
                if ( in_array( $design, $this->context['core_designs'], true ) || empty( $this->context['design_users'][$design] ) )
                    continue;
                $others = false;
                foreach ( array_merge( $this->planned, $this->context['access'] ) as $other )
                    if ( $other !== $name && in_array( $design, self::factsOf( $this->facts, $other )['designs'], true ) )
                        $others = true;
                if ( !$others )
                    $out[] = array( 'removes_design', 'bad', array( 'name' => $name, 'design' => $design,
                                                                  'siteaccesses' => implode( ', ', $this->context['design_users'][$design] ) ) );
            }
            if ( isset( self::$critical[$name] ) )
                $out[] = array( 'removes_critical', 'bad', array( 'name' => $name, 'what' => self::$critical[$name] ) );
        }

        $before = $this->problems( $this->current );
        foreach ( $this->problems( $this->planned ) as $name => $problems )
        {
            foreach ( $problems as $problem )
            {
                // what the kernel corrects by itself is not a risk; a settings file that starts to lose is
                if ( $problem[0] === 'not_installed' || ( $problem[1] === 'info' && $problem[0] !== 'settings_lose' ) )
                    continue;
                $had = isset( $before[$name] ) && in_array( $problem, $before[$name], true );
                if ( !$had )
                    $out[] = array( 'new_problem', $problem[0] === 'requires_missing' ? 'bad' : 'warn',
                                    array( 'name' => $name, 'problem' => $problem[0] ) + $problem[2] );
            }
        }

        // A pure reorder the kernel undoes: with ExtensionOrdering the declared dependencies decide, and an order
        // that changes only the file changes nothing that is loaded.
        if ( $this->context['ordering'] && $diff['moved'] && !$diff['added'] && !$diff['removed']
             && $this->effectiveOrder( $this->current ) === $this->effectiveOrder( $this->planned ) )
            $out[] = array( 'no_effect', 'info', array() );
        return $out;
    }

    /**
     * Whether any risk needs the explicit confirmation.
     */
    public static function needsAcknowledgement( array $risks )
    {
        foreach ( $risks as $risk )
            if ( $risk[1] === 'bad' )
                return true;
        return false;
    }

    private static function factsOf( array $facts, $name )
    {
        $f = isset( $facts[$name] ) && is_array( $facts[$name] ) ? $facts[$name] : array();
        foreach ( array( 'requires', 'uses', 'extends', 'designs', 'ini_bases', 'ini_appends' ) as $key )
            $f[$key] = isset( $f[$key] ) ? array_values( (array)$f[$key] ) : array();
        $f['installed'] = !isset( $f['installed'] ) || $f['installed'];
        return $f;
    }
}
