<?php
/**
 * File containing the expExtensionCatalogue class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What Setup > Extensions knows about each extension: its metadata (expInfo), its declared dependencies
 * (extension.xml <dependencies>), the designs and settings files it ships, whether it is a git checkout, and which
 * siteaccesses activate it as an access extension or use one of its designs.
 *
 * gather() reads the disk; rows() and summary() only combine what was gathered with a plan, so they are tested
 * without it (tests/tests/kernel/classes/setup/ExtensionChangePlanTest.php). Nothing here writes.
 *
 * Guide: doc/guides/extensions-page.md
 */
class expExtensionCatalogue
{
    /** @var array name => expInfo row */
    public $info = array();
    /** @var array name => facts, as expExtensionChangePlan takes them, plus path and git */
    public $facts = array();
    /** @var array siteaccess => names in its ActiveAccessExtensions */
    public $accessBySiteaccess = array();
    /** @var array design => siteaccesses using it */
    public $designUsers = array();
    /** @var array designs the kernel ships */
    public $coreDesigns = array();
    /** @var bool site.ini [ExtensionSettings] ExtensionOrdering */
    public $ordering = true;

    /**
     * Reads everything for the extensions the installation can see. $current is ActiveExtensions of the override
     * file: an entry without a directory is kept as "not installed".
     */
    public static function gather( array $current = array() )
    {
        $catalogue = new self();
        $catalogue->info = expInfo::availableExtensions();
        foreach ( $catalogue->info as $name => $row )
            $catalogue->facts[$name] = self::readFacts( $name, eZExtension::extensionPath( $name ) );
        foreach ( $current as $name )
            if ( !isset( $catalogue->facts[$name] ) )
                $catalogue->facts[$name] = array( 'installed' => false, 'path' => false, 'git' => false );

        $siteINI = eZINI::instance();
        $catalogue->ordering = $siteINI->variable( 'ExtensionSettings', 'ExtensionOrdering' ) === 'enabled';
        foreach ( (array)$siteINI->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) as $sa )
        {
            $sa = (string)$sa;
            if ( $sa === '' || !preg_match( '/^[A-Za-z0-9_\-]+$/', $sa ) || !is_file( "settings/siteaccess/$sa/site.ini.append.php" ) )
                continue;
            $ini = new eZINI( 'site.ini.append.php', "settings/siteaccess/$sa", null, false, null, true );
            if ( $ini->hasVariable( 'ExtensionSettings', 'ActiveAccessExtensions' ) )
                $catalogue->accessBySiteaccess[$sa] = array_values( array_filter( (array)$ini->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ), 'strlen' ) );
            $designs = array();
            if ( $ini->hasVariable( 'DesignSettings', 'SiteDesign' ) )
                $designs[] = (string)$ini->variable( 'DesignSettings', 'SiteDesign' );
            if ( $ini->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) )
                $designs = array_merge( $designs, (array)$ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) );
            foreach ( array_unique( array_filter( $designs, 'strlen' ) ) as $design )
                $catalogue->designUsers[$design][] = $sa;
        }
        foreach ( is_dir( 'design' ) ? scandir( 'design' ) : array() as $design )
            if ( $design[0] !== '.' && is_dir( "design/$design" ) )
                $catalogue->coreDesigns[] = $design;
        return $catalogue;
    }

    /**
     * Facts of one extension directory.
     */
    public static function readFacts( $name, $path )
    {
        $facts = array( 'installed' => $path !== false && is_dir( $path ), 'path' => $path, 'git' => false,
                        'requires' => array(), 'uses' => array(), 'extends' => array(),
                        'designs' => array(), 'ini_bases' => array(), 'ini_appends' => array() );
        if ( !$facts['installed'] )
            return $facts;
        $facts['git'] = file_exists( "$path/.git" );
        $facts = array_merge( $facts, self::dependenciesFromXml( is_readable( "$path/extension.xml" ) ? file_get_contents( "$path/extension.xml" ) : '' ) );
        foreach ( is_dir( "$path/design" ) ? scandir( "$path/design" ) : array() as $design )
            if ( $design[0] !== '.' && is_dir( "$path/design/$design" ) )
                $facts['designs'][] = $design;
        foreach ( is_dir( "$path/settings" ) ? scandir( "$path/settings" ) : array() as $file )
        {
            if ( preg_match( '/^(.+)\.ini$/', $file, $m ) )
                $facts['ini_bases'][] = $m[1];
            else if ( preg_match( '/^(.+)\.ini\.append(\.php)?$/', $file, $m ) )
                $facts['ini_appends'][] = $m[1];
        }
        $facts['ini_appends'] = array_values( array_unique( $facts['ini_appends'] ) );
        return $facts;
    }

    /**
     * requires, uses and extends of an extension.xml, as eZExtension::extensionOrdering() reads them. An empty or
     * broken file declares nothing.
     */
    public static function dependenciesFromXml( $xml )
    {
        $out = array( 'requires' => array(), 'uses' => array(), 'extends' => array() );
        if ( trim( (string)$xml ) === '' )
            return $out;
        $useErrors = libxml_use_internal_errors( true );
        $doc = simplexml_load_string( $xml, 'SimpleXMLElement', LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $useErrors );
        if ( $doc === false )
            return $out;
        foreach ( $doc->dependencies as $dependencies )
        {
            foreach ( $dependencies as $type => $node )
            {
                if ( !isset( $out[$type] ) )
                    continue;
                foreach ( $node as $dependency )
                {
                    $name = trim( (string)$dependency['name'] );
                    if ( $name !== '' && !in_array( $name, $out[$type], true ) )
                        $out[$type][] = $name;
                }
            }
        }
        return $out;
    }

    /**
     * Every extension named in some siteaccess's ActiveAccessExtensions.
     */
    public function accessNames()
    {
        $names = array();
        foreach ( $this->accessBySiteaccess as $list )
            $names = array_merge( $names, $list );
        return array_values( array_unique( $names ) );
    }

    /**
     * The context expExtensionChangePlan takes.
     */
    public function context()
    {
        return array( 'access' => $this->accessNames(), 'design_users' => $this->designUsers,
                      'core_designs' => $this->coreDesigns, 'ordering' => $this->ordering );
    }

    public function plan( array $current, array $planned )
    {
        return new expExtensionChangePlan( $current, $planned, $this->facts, $this->context() );
    }

    /**
     * One row per extension, for the template: active ones in the planned order first, then the others by name
     * ($sort 'name' sorts all of them by name instead).
     *
     * Row keys: name, title, version, description, license, info_url, author, copyright, mtime, git, installed,
     * active, access (siteaccesses), position, effective (position the kernel loads it at), current_position,
     * requires, uses, extends, dependents (active extensions that require or use it), designs (=> siteaccesses),
     * problems (each: code, severity, other, file), problem_level ('', info, warn, bad), state ('', added,
     * removed, moved), search (lowercase text for the filter).
     */
    public function rows( expExtensionChangePlan $plan, $sort = 'order' )
    {
        $planned = $plan->planned;
        $positions = array_flip( $planned );
        $currentPositions = array_flip( $plan->current );
        $effective = array_flip( $plan->effectiveOrder() );
        $problems = $plan->problems();
        $diff = $plan->diff();
        $accessOf = array();
        foreach ( $this->accessBySiteaccess as $sa => $list )
            foreach ( $list as $name )
                $accessOf[$name][] = $sa;

        $names = array_keys( $this->info );
        foreach ( array_merge( $plan->current, $planned ) as $name )
            if ( !in_array( $name, $names, true ) )
                $names[] = $name;
        usort( $names, function ( $a, $b ) use ( $positions, $sort ) {
            if ( $sort !== 'name' )
            {
                $pa = isset( $positions[$a] ) ? $positions[$a] : PHP_INT_MAX;
                $pb = isset( $positions[$b] ) ? $positions[$b] : PHP_INT_MAX;
                if ( $pa !== $pb )
                    return $pa < $pb ? -1 : 1;
            }
            return strnatcasecmp( $a, $b );
        } );

        $rows = array();
        foreach ( $names as $name )
        {
            $info = isset( $this->info[$name] ) ? $this->info[$name] : array();
            $facts = isset( $this->facts[$name] ) ? $this->facts[$name] : array( 'installed' => false );
            $get = function ( $key ) use ( $info ) { return isset( $info[$key] ) && is_string( $info[$key] ) && $info[$key] !== '' ? $info[$key] : ''; };
            $dependents = array();
            foreach ( $planned as $other )
            {
                $f = isset( $this->facts[$other] ) ? $this->facts[$other] : array();
                if ( in_array( $name, array_merge( isset( $f['requires'] ) ? $f['requires'] : array(), isset( $f['uses'] ) ? $f['uses'] : array() ), true ) )
                    $dependents[] = $other;
            }
            $designs = array();
            foreach ( isset( $facts['designs'] ) ? $facts['designs'] : array() as $design )
                if ( !empty( $this->designUsers[$design] ) && !in_array( $design, $this->coreDesigns, true ) )
                    $designs[] = array( 'design' => $design, 'siteaccesses' => $this->designUsers[$design] );
            $rowProblems = array();
            $level = '';
            foreach ( isset( $problems[$name] ) ? $problems[$name] : array() as $p )
            {
                $rowProblems[] = array( 'code' => $p[0], 'severity' => $p[1],
                                        'other' => isset( $p[2]['other'] ) ? $p[2]['other'] : '',
                                        'file' => isset( $p[2]['file'] ) ? $p[2]['file'] : '' );
                $level = self::worse( $level, $p[1] );
            }
            $state = in_array( $name, $diff['added'], true ) ? 'added'
                   : ( in_array( $name, $diff['removed'], true ) ? 'removed'
                   : ( in_array( $name, $diff['moved'], true ) ? 'moved' : '' ) );
            $title = $get( 'name' );
            $rows[] = array(
                'name' => $name,
                'title' => $title !== $name ? $title : '',
                'version' => $get( 'version' ),
                'description' => $get( 'description' ),
                'license' => $get( 'license' ),
                'info_url' => preg_match( '#^https?://#i', $get( 'info_url' ) ) ? $get( 'info_url' ) : '',
                'author' => $get( 'author' ),
                'copyright' => $get( 'copyright' ),
                'mtime' => isset( $info['mtime_formatted'] ) && $info['mtime_formatted'] ? (string)$info['mtime_formatted'] : '',
                'git' => !empty( $facts['git'] ),
                'installed' => !isset( $facts['installed'] ) || $facts['installed'],
                'active' => isset( $positions[$name] ),
                'access' => isset( $accessOf[$name] ) ? $accessOf[$name] : array(),
                'position' => isset( $positions[$name] ) ? $positions[$name] + 1 : 0,
                'effective' => isset( $effective[$name] ) ? $effective[$name] + 1 : 0,
                'current_position' => isset( $currentPositions[$name] ) ? $currentPositions[$name] + 1 : 0,
                'requires' => isset( $facts['requires'] ) ? $facts['requires'] : array(),
                'uses' => isset( $facts['uses'] ) ? $facts['uses'] : array(),
                'extends' => isset( $facts['extends'] ) ? $facts['extends'] : array(),
                'dependents' => $dependents,
                'designs' => $designs,
                'problems' => $rowProblems,
                'problem_level' => $level,
                'state' => $state,
                'search' => mb_strtolower( implode( ' ', array( $name, $title, $get( 'description' ), $get( 'license' ), $get( 'author' ) ) ) ),
            );
        }
        return $rows;
    }

    /**
     * Counts for the overview: total, active, access, inactive, git, problems (rows with a warn or bad problem).
     */
    public static function summary( array $rows )
    {
        $s = array( 'total' => 0, 'active' => 0, 'access' => 0, 'inactive' => 0, 'git' => 0, 'problems' => 0 );
        foreach ( $rows as $row )
        {
            $s['total']++;
            if ( $row['active'] ) $s['active']++;
            else if ( $row['access'] ) $s['access']++;
            else $s['inactive']++;
            if ( $row['git'] ) $s['git']++;
            if ( $row['problem_level'] === 'warn' || $row['problem_level'] === 'bad' ) $s['problems']++;
        }
        return $s;
    }

    private static function worse( $a, $b )
    {
        $rank = array( '' => 0, 'info' => 1, 'warn' => 2, 'bad' => 3 );
        return $rank[$b] > $rank[$a] ? $b : $a;
    }
}
