<?php
/**
 * File containing the expViewCountConfiguration class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What one run of the view counter reads: the siteaccesses it counts, grouped by the access log each one reads, and
 * the settings of logfile.ini [ViewCountSettings].
 *
 * Which siteaccesses (the first that applies):
 *
 * 1. the list given on the command line (exp:viewcount:update --siteaccess-list=site,bold, --all-siteaccesses);
 * 2. logfile.ini [ViewCountSettings] SiteAccessList[];
 * 3. [ViewCountSettings] AllSiteAccesses=enabled: every siteaccess of AvailableSiteAccessList[] with an access log
 *    set, except the administrations;
 * 4. else the siteaccess the run is for (runcronjobs.php -s <siteaccess>), as the cronjob always did. When that is
 *    an administration, its settings name the log, but "/" and the URL aliases are those of DefaultAccess.
 *
 * Each siteaccess reads its own logfile.ini [AccessLogFileSettings] (StorageDir, LogFileName, SitePrefix[],
 * PathPrefix[], Host, Port, Hosts[]) and site.ini (IndexPage, PathPrefix, PathPrefixExclude[], SiteLanguageList[]),
 * with its siteaccess settings, so one siteaccess can have a log of its own and others share one. Siteaccesses
 * reading the same log are one job: the log is read once, and each line goes to the siteaccess the kernel's
 * siteaccess matching finds for it (MatchSiteAccess=auto, the default).
 */
class expViewCountConfiguration
{
    /** @var array the run's settings, see settings() */
    private $settings;

    /** @var array list of jobs: hash log, sites (name => expViewCountSite), match (bool) */
    private $jobs = array();

    /** @var array siteaccess => why it is not counted */
    private $skipped = array();

    /** @var array the mode and the siteaccesses asked for */
    private $source = array();

    /**
     * @param array $settings see settings()
     * @param expViewCountSite[] $sites the counted siteaccesses
     * @param array $skipped siteaccess => reason
     * @param array $source hash mode (single, list, all), requested (string[])
     */
    public function __construct( array $settings, array $sites, array $skipped = array(), array $source = array() )
    {
        $this->settings = $settings + self::defaults();
        $this->skipped = $skipped;
        $this->source = $source + array( 'mode' => 'single', 'requested' => array() );
        $groups = array();
        foreach ( $sites as $site )
        {
            $key = self::logKey( $site->logFile );
            if ( !isset( $groups[$key] ) )
                $groups[$key] = array( 'log' => $key, 'sites' => array() );
            $groups[$key]['sites'][$site->name] = $site;
        }
        foreach ( $groups as $group )
        {
            $mode = $this->settings['match'];
            $group['match'] = $mode === 'enabled' || ( $mode === 'auto' && count( $group['sites'] ) > 1 );
            $this->jobs[] = $group;
        }
    }

    /** @return array the defaults of the settings */
    public static function defaults()
    {
        return array(
            'state_dir' => '',
            'lock_timeout' => 60,
            'rotated_suffixes' => array( '.1', '.1.gz', '.0' ),
            'max_line_length' => 65536,
            'line_regexp' => '',
            'view_modes' => array(),
            'match' => 'auto',
            'filter' => array(),
        );
    }

    /**
     * The configuration of the INI files, for the siteaccess the run is for.
     *
     * @param string[]|null $siteAccesses the list given on the command line, null for none
     * @param bool $all --all-siteaccesses
     * @param array $overrides log_file: one access log for every counted siteaccess in place of its
     *                         [AccessLogFileSettings] (a trial run on a copy, an older rotated log); state_dir: the
     *                         state directory in place of StateDir
     * @return expViewCountConfiguration
     */
    public static function fromINI( $siteAccesses = null, $all = false, array $overrides = array() )
    {
        $ini = eZINI::instance( 'logfile.ini' );
        $value = function ( $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( 'ViewCountSettings', $name ) ? $ini->variable( 'ViewCountSettings', $name ) : $default;
        };
        $list = function ( $values )
        {
            return array_values( array_unique( array_filter( array_map( 'trim', array_map( 'strval', (array)$values ) ), 'strlen' ) ) );
        };
        $match = (string)$value( 'MatchSiteAccess', 'auto' );
        $settings = array(
            'state_dir' => trim( (string)$value( 'StateDir', '' ) ),
            'lock_timeout' => (int)$value( 'LockTimeout', 60 ),
            'rotated_suffixes' => $list( $value( 'RotatedSuffixes', array( '.1', '.1.gz', '.0' ) ) ),
            'max_line_length' => (int)$value( 'MaxLineLength', 65536 ),
            'line_regexp' => trim( (string)$value( 'LineRegexp', '' ) ),
            'view_modes' => $list( $value( 'ViewModes', array() ) ),
            'match' => in_array( $match, array( 'auto', 'enabled', 'disabled' ), true ) ? $match : 'auto',
            'filter' => expViewCountRequestFilter::fromINI( $ini )->settings(),
        );
        if ( isset( $overrides['state_dir'] ) && trim( (string)$overrides['state_dir'] ) !== '' )
            $settings['state_dir'] = trim( (string)$overrides['state_dir'] );
        $logOverride = isset( $overrides['log_file'] ) ? trim( (string)$overrides['log_file'] ) : '';
        if ( $logOverride !== '' && $logOverride[0] !== '/' )
            $logOverride = eZSys::rootDir() . '/' . $logOverride;
        if ( $settings['state_dir'] === '' )
            $settings['state_dir'] = eZSys::varDirectory() . '/viewcount';
        elseif ( $settings['state_dir'][0] !== '/' )
            $settings['state_dir'] = eZSys::rootDir() . '/' . $settings['state_dir'];

        $current = eZSiteAccess::current();
        $currentName = $current && isset( $current['name'] ) ? (string)$current['name'] : '';
        $siteINI = eZINI::instance( 'site.ini' );
        $available = $list( $siteINI->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) );

        $mode = 'single';
        $requested = array();
        if ( $siteAccesses !== null && $list( $siteAccesses ) )
        {
            $mode = 'list';
            $requested = $list( $siteAccesses );
        }
        elseif ( $all || $value( 'AllSiteAccesses', 'disabled' ) === 'enabled' )
        {
            $mode = 'all';
            $requested = $available;
        }
        elseif ( $list( $value( 'SiteAccessList', array() ) ) )
        {
            $mode = 'list';
            $requested = $list( $value( 'SiteAccessList', array() ) );
        }
        else
        {
            $requested = array( $currentName );
        }

        $adminList = array();
        if ( $siteINI->hasVariable( 'AdminSiteAccessMenu', 'AdminSiteAccessList' ) )
            $adminList = $list( $siteINI->variable( 'AdminSiteAccessMenu', 'AdminSiteAccessList' ) );

        $sites = array();
        $skipped = array();
        foreach ( $requested as $name )
        {
            if ( $mode !== 'single' && !in_array( $name, $available, true ) )
            {
                $skipped[$name] = 'not in AvailableSiteAccessList';
                continue;
            }
            $logINI = $name === '' || $name === $currentName ? $ini : eZSiteAccess::getIni( $name, 'logfile.ini' );
            $nameSiteINI = $name === '' || $name === $currentName ? $siteINI : eZSiteAccess::getIni( $name, 'site.ini' );
            $admin = $name !== '' && self::isAdministration( $name, $nameSiteINI, $adminList );
            if ( $admin && $mode !== 'single' )
            {
                $skipped[$name] = 'an administration';
                continue;
            }
            $logFile = $logOverride !== '' ? $logOverride : self::logFile( $logINI );
            if ( $logFile === '' )
            {
                $skipped[$name !== '' ? $name : '(default)'] = 'no access log: logfile.ini [AccessLogFileSettings] StorageDir and LogFileName are empty';
                continue;
            }
            // a run for an administration (runcronjobs.php -s admin) counts the views of the default siteaccess
            $profileName = $name;
            $profileINI = $nameSiteINI;
            if ( $admin || $name === '' )
            {
                $profileName = (string)$siteINI->variable( 'SiteSettings', 'DefaultAccess' );
                $profileINI = $profileName !== '' && $profileName !== $currentName ? eZSiteAccess::getIni( $profileName, 'site.ini' ) : $siteINI;
                if ( $profileName === '' )
                    $profileName = $name !== '' ? $name : 'default';
            }
            $sites[] = self::site( $profileName, $logFile, $logINI, $profileINI );
        }
        return new self( $settings, $sites, $skipped, array( 'mode' => $mode, 'requested' => $requested ) );
    }

    /**
     * A site of the INI files of a siteaccess.
     *
     * @param string $name
     * @param string $logFile
     * @param eZINI $logINI its logfile.ini
     * @param eZINI $siteINI its site.ini
     * @return expViewCountSite
     */
    public static function site( $name, $logFile, eZINI $logINI, eZINI $siteINI )
    {
        $get = function ( eZINI $ini, $group, $name, $default )
        {
            return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default;
        };
        $list = function ( $values )
        {
            return array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$values ) ), 'strlen' ) );
        };
        $pathPrefixes = $list( $get( $logINI, 'AccessLogFileSettings', 'PathPrefix', array() ) );
        if ( !$pathPrefixes )
            $pathPrefixes = $list( array( $get( $siteINI, 'SiteAccessSettings', 'PathPrefix', '' ) ) );
        $port = trim( (string)$get( $logINI, 'AccessLogFileSettings', 'Port', '' ) );
        return new expViewCountSite( array(
            'name' => (string)$name,
            'logFile' => $logFile,
            'sitePrefixes' => $list( $get( $logINI, 'AccessLogFileSettings', 'SitePrefix', array() ) ),
            'pathPrefixes' => $pathPrefixes,
            'pathPrefixExclude' => $list( $get( $siteINI, 'SiteAccessSettings', 'PathPrefixExclude', array() ) ),
            'indexPage' => (string)$get( $siteINI, 'SiteSettings', 'IndexPage', '' ),
            'languages' => $list( $get( $siteINI, 'RegionalSettings', 'SiteLanguageList', array() ) ),
            'host' => strtolower( trim( (string)$get( $logINI, 'AccessLogFileSettings', 'Host', '' ) ) ),
            'port' => ctype_digit( $port ) ? (int)$port : null,
            'hosts' => $list( $get( $logINI, 'AccessLogFileSettings', 'Hosts', array() ) ),
        ) );
    }

    /**
     * The access log of a logfile.ini: StorageDir/LogFileName, '' when either is empty. A relative StorageDir is
     * below the installation.
     *
     * @param eZINI $logINI
     * @return string
     */
    public static function logFile( eZINI $logINI )
    {
        $dir = $logINI->hasVariable( 'AccessLogFileSettings', 'StorageDir' ) ? trim( (string)$logINI->variable( 'AccessLogFileSettings', 'StorageDir' ) ) : '';
        $name = $logINI->hasVariable( 'AccessLogFileSettings', 'LogFileName' ) ? trim( (string)$logINI->variable( 'AccessLogFileSettings', 'LogFileName' ) ) : '';
        if ( $dir === '' || $name === '' )
            return '';
        if ( $dir[0] !== '/' )
            $dir = eZSys::rootDir() . '/' . $dir;
        return rtrim( $dir, '/' ) . '/' . ltrim( $name, '/' );
    }

    /**
     * The key of a log: its real path when it exists (two names of one file are one log), else the path as written.
     *
     * @param string $file
     * @return string
     */
    public static function logKey( $file )
    {
        $real = @realpath( $file );
        return $real !== false ? $real : (string)$file;
    }

    /**
     * Whether a siteaccess is an administration, as the siteaccess menu decides it (ezpSiteAccessMenu).
     *
     * @param string $name
     * @param eZINI $siteINI its site.ini
     * @param string[] $adminList [AdminSiteAccessMenu] AdminSiteAccessList[]
     * @return bool
     */
    public static function isAdministration( $name, eZINI $siteINI, array $adminList )
    {
        $designs = array( (string)$siteINI->variable( 'DesignSettings', 'SiteDesign' ) );
        if ( $siteINI->hasVariable( 'DesignSettings', 'AdditionalSiteDesignList' ) )
            $designs = array_merge( $designs, array_map( 'strval', (array)$siteINI->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) ) );
        $front = $siteINI->hasVariable( 'SiteSettings', 'FrontSiteAccess' ) ? (string)$siteINI->variable( 'SiteSettings', 'FrontSiteAccess' ) : '';
        return ezpSiteAccessMenu::isAdministration( $name, $adminList, array( 'site_design' => $designs[0], 'site_designs' => $designs,
                                                                               'front_site_access' => $front ) );
    }

    /** @return array the settings: state_dir, lock_timeout, rotated_suffixes, max_line_length, line_regexp, view_modes, match, filter */
    public function settings()
    {
        return $this->settings;
    }

    /** @return array list of hash log, sites (name => expViewCountSite), match */
    public function jobs()
    {
        return $this->jobs;
    }

    /** @return array siteaccess => why it is not counted */
    public function skipped()
    {
        return $this->skipped;
    }

    /** @return array hash mode (single, list, all), requested */
    public function source()
    {
        return $this->source;
    }
}
