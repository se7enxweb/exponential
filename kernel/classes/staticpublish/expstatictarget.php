<?php
/**
 * File containing the expStaticTarget class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One target of static publishing: a siteaccess (or several that share one host by their URI prefix, SiteAccessList[]
 * with DefaultSiteAccess at the root of the copy and the others in the folders of their prefix) published as static
 * files to one place, as staticcache.ini
 * [StaticPublishSettings] TargetList[] names it and its block [Target_<id>] describes it. Reads and validates; the
 * settings missing from a block take the defaults of DEFAULTS. Validation (validate()) is pure: the siteaccesses it
 * checks against are passed in (siteAccessContext() reads them from the installation).
 *
 * A target is never an administration siteaccess: one that requires a login, uses an administration design or is
 * named admin or editor is refused, as is one the installation does not serve.
 */
class expStaticTarget
{
    const KINDS = array( 'github-pages', 'git' );
    const DELIVERIES = array( 'branch', 'actions' );
    const LINKS = array( 'relative', 'base-path' );
    const ASSETS = array( 'referenced', 'referenced-and-storage' );
    const EXTRAS = array( 'nojekyll', '404', 'robots', 'sitemap', 'cname', 'redirects', 'readme', 'manifest', 'license', 'workflow' );
    const POST_PROCESS = array( 'strip', 'minify', 'precompress', 'fingerprint' );
    const FORMS = array( 'endpoint', 'hide' );
    const TRIGGERS = array( 'manual', 'publish', 'schedule', 'cli' );
    const NOTIFY = array( 'failure', 'success' );
    const CREDENTIAL_KINDS = array( 'token', 'sshkey', 'app', 'none' );

    /** The defaults of every setting of a [Target_<id>] block */
    const DEFAULTS = array(
        'Name' => '',
        'SiteAccess' => '',
        'SiteAccessList' => array(),
        'DefaultSiteAccess' => '',
        'Kind' => 'github-pages',
        'Remote' => '',
        'Branch' => 'gh-pages',
        'Folder' => '',
        'Delivery' => 'branch',
        'Links' => 'relative',
        'BasePath' => '',
        'PublicURL' => '',
        'CustomDomain' => '',
        'Credential' => 'none',
        'CrawlURL' => '',
        'LiveURL' => '',
        'OutsideLinks' => 'keep',
        'AdminLinks' => 'keep',
        'MaxPages' => '10000',
        'MaxDepth' => '',
        'Assets' => 'referenced-and-storage',
        'Extras' => array( 'nojekyll', '404', 'robots', 'sitemap', 'manifest' ),
        'PostProcess' => array( 'strip' ),
        'Search' => 'disabled',
        'Forms' => 'hide',
        'ExcludePaths' => array(),
        'ExcludeSections' => array(),
        'Triggers' => array( 'manual', 'cli' ),
        'Schedule' => '',
        'Notify' => array( 'failure' ),
        'Webhook' => '',
        'License' => '',
    );

    /** The settings that hold a list */
    const LIST_SETTINGS = array( 'SiteAccessList', 'Extras', 'PostProcess', 'ExcludePaths', 'ExcludeSections', 'Triggers', 'Notify' );

    /** The pattern of a target id: also a folder name and part of a tag name */
    const ID_PATTERN = '#^[a-z0-9][a-z0-9_-]{0,49}$#';

    private $id;
    private $settings;

    /**
     * @param string $id
     * @param array $settings the block's settings; missing ones take DEFAULTS
     */
    public function __construct( $id, array $settings = array() )
    {
        $this->id = (string)$id;
        $merged = self::DEFAULTS;
        foreach ( $settings as $name => $value )
        {
            if ( !array_key_exists( $name, self::DEFAULTS ) )
                continue;
            if ( in_array( $name, self::LIST_SETTINGS, true ) )
                $value = array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$value ) ), 'strlen' ) );
            else
                $value = is_array( $value ) ? '' : trim( (string)$value );
            $merged[$name] = $value;
        }
        $this->settings = $merged;
    }

    /** @return eZINI */
    private static function ini( $ini )
    {
        return $ini instanceof eZINI ? $ini : eZINI::instance( 'staticcache.ini' );
    }

    /**
     * Whether static publishing is switched on ([StaticPublishSettings] Enabled=enabled).
     *
     * @param eZINI|null $ini staticcache.ini
     * @return bool
     */
    public static function enabled( $ini = null )
    {
        $ini = self::ini( $ini );
        return $ini->hasVariable( 'StaticPublishSettings', 'Enabled' ) && $ini->variable( 'StaticPublishSettings', 'Enabled' ) === 'enabled';
    }

    /**
     * The ids of [StaticPublishSettings] TargetList[], in their order, once each.
     *
     * @param eZINI|null $ini
     * @return string[]
     */
    public static function ids( $ini = null )
    {
        $ini = self::ini( $ini );
        if ( !$ini->hasVariable( 'StaticPublishSettings', 'TargetList' ) )
            return array();
        $ids = array();
        foreach ( (array)$ini->variable( 'StaticPublishSettings', 'TargetList' ) as $id )
        {
            $id = trim( (string)$id );
            if ( $id !== '' && !in_array( $id, $ids, true ) )
                $ids[] = $id;
        }
        return $ids;
    }

    /**
     * One target, or null when TargetList[] does not name it.
     *
     * @param string $id
     * @param eZINI|null $ini
     * @return expStaticTarget|null
     */
    public static function get( $id, $ini = null )
    {
        $ini = self::ini( $ini );
        if ( !in_array( (string)$id, self::ids( $ini ), true ) )
            return null;
        return self::fromIni( (string)$id, $ini );
    }

    /**
     * Every target of TargetList[].
     *
     * @param eZINI|null $ini
     * @return expStaticTarget[] id => target
     */
    public static function all( $ini = null )
    {
        $ini = self::ini( $ini );
        $targets = array();
        foreach ( self::ids( $ini ) as $id )
            $targets[$id] = self::fromIni( $id, $ini );
        return $targets;
    }

    /**
     * The target of a block [Target_<id>], whether TargetList[] names it or not (an empty one when there is no block).
     *
     * @param string $id
     * @param eZINI|null $ini
     * @return expStaticTarget
     */
    public static function fromIni( $id, $ini = null )
    {
        $ini = self::ini( $ini );
        $group = 'Target_' . $id;
        $settings = $ini->hasGroup( $group ) ? (array)$ini->group( $group ) : array();
        $target = new self( $id, $settings );
        $target->settings['_block'] = $ini->hasGroup( $group ) ? '1' : '';
        return $target;
    }

    /** @return string */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param string $name a setting of DEFAULTS
     * @return string|array
     */
    public function setting( $name )
    {
        return isset( $this->settings[$name] ) ? $this->settings[$name] : null;
    }

    /** @return array the settings (DEFAULTS filled in) */
    public function settings()
    {
        $settings = $this->settings;
        unset( $settings['_block'] );
        return $settings;
    }

    /** @return string the name, or the id */
    public function name()
    {
        return $this->settings['Name'] !== '' ? $this->settings['Name'] : $this->id;
    }

    /**
     * The siteaccess the target is named by in one word: the default siteaccess of the copy (the one at its root),
     * else the first of its siteaccesses; '' when none is set.
     *
     * @return string
     */
    public function siteAccess()
    {
        $list = $this->siteAccesses();
        $default = $this->defaultSiteAccess();
        return $default !== '' ? $default : ( $list ? $list[0] : '' );
    }

    /**
     * The siteaccesses the target publishes, in their order: SiteAccessList[] when it names any, else SiteAccess (the
     * form of one).
     *
     * @return string[]
     */
    public function siteAccesses()
    {
        return self::siteAccessesOf( $this->settings );
    }

    /**
     * The siteaccesses of a target's settings (SiteAccessList[], else SiteAccess), once each. Pure.
     *
     * @param array $settings
     * @return string[]
     */
    public static function siteAccessesOf( array $settings )
    {
        $list = array();
        foreach ( isset( $settings['SiteAccessList'] ) ? (array)$settings['SiteAccessList'] : array() as $name )
        {
            $name = trim( (string)$name );
            if ( $name !== '' && !in_array( $name, $list, true ) )
                $list[] = $name;
        }
        if ( !$list && isset( $settings['SiteAccess'] ) && trim( (string)$settings['SiteAccess'] ) !== '' )
            $list[] = trim( (string)$settings['SiteAccess'] );
        return $list;
    }

    /** @return bool whether the target publishes more than one siteaccess */
    public function isMultiSite()
    {
        return count( $this->siteAccesses() ) > 1;
    }

    /**
     * The siteaccess served at the root of the copy: DefaultSiteAccess; the one siteaccess of a target that publishes
     * one; '' for several without a DefaultSiteAccess (each is below its prefix, the root lists them).
     *
     * @return string
     */
    public function defaultSiteAccess()
    {
        $list = $this->siteAccesses();
        $default = (string)$this->settings['DefaultSiteAccess'];
        if ( $default !== '' )
            return $default;
        return count( $list ) === 1 ? $list[0] : '';
    }

    /**
     * Where each siteaccess of the target is in the copy: the default one at its root, the others in the folder of
     * their URI prefix (/ger/...), the default one first. Pure.
     *
     * @param array $context siteAccessContext() (prefixes: name => the URI prefix that selects it, without slashes;
     *                       a name not in it is taken to be selected by its own name)
     * @return array[] siteaccess, prefix ('' at the root, else the folder: ger), default (bool)
     */
    public function members( array $context = array() )
    {
        $default = $this->defaultSiteAccess();
        $out = array();
        foreach ( $this->siteAccesses() as $name )
        {
            $prefix = $name === $default ? '' : self::prefixOf( $context, $name );
            $member = array( 'siteaccess' => $name, 'prefix' => (string)$prefix, 'default' => $name === $default );
            if ( $name === $default )
                array_unshift( $out, $member );
            else
                $out[] = $member;
        }
        return $out;
    }

    /**
     * The URI prefix that selects a siteaccess on its host (bold, ger), from a siteAccessContext(); its name when the
     * context does not say; null when it is not selected by a part of the address. Pure.
     *
     * @param array $context
     * @param string $name
     * @return string|null
     */
    public static function prefixOf( array $context, $name )
    {
        if ( isset( $context['prefixes'] ) && is_array( $context['prefixes'] ) )
            return array_key_exists( $name, $context['prefixes'] ) && $context['prefixes'][$name] !== null && $context['prefixes'][$name] !== ''
                 ? trim( (string)$context['prefixes'][$name], '/' ) : null;
        return (string)$name;
    }

    /**
     * The siteaccess of a page path in the copy (/ger/about-us: ger, /about-us: the default one) and its path in that
     * siteaccess. Pure.
     *
     * @param string $copyPath
     * @param array $members members()
     * @return array|null siteaccess, prefix, path (/about-us); null when no siteaccess of the copy holds the path
     */
    public static function memberOf( $copyPath, array $members )
    {
        $copyPath = '/' . trim( (string)$copyPath, '/' );
        $best = null;
        foreach ( $members as $member )
        {
            $prefix = $member['prefix'] === '' ? '' : '/' . $member['prefix'];
            if ( $prefix === '' || $copyPath === $prefix || strpos( $copyPath, $prefix . '/' ) === 0 )
                if ( $best === null || strlen( $prefix ) > strlen( $best[1] ) )
                    $best = array( $member, $prefix );
        }
        if ( $best === null )
            return null;
        $path = (string)substr( $copyPath, strlen( $best[1] ) );
        return array( 'siteaccess' => $best[0]['siteaccess'], 'prefix' => $best[0]['prefix'], 'path' => $path === '' ? '/' : $path );
    }

    /** @return string relative or base-path */
    public function linkMode()
    {
        return $this->settings['Links'];
    }

    /**
     * The base path in its one form (/repo/), '/' when there is none.
     *
     * @return string
     */
    public function basePath()
    {
        return expStaticLinkRewriter::normaliseBasePath( $this->settings['BasePath'] );
    }

    /** @return string[] */
    public function extras()
    {
        return $this->settings['Extras'];
    }

    /** @return bool */
    public function hasExtra( $name )
    {
        return in_array( $name, $this->settings['Extras'], true );
    }

    /** @return string the public address with a trailing slash, '' when not set */
    public function publicUrl()
    {
        $url = $this->settings['PublicURL'];
        if ( $url === '' && $this->settings['CustomDomain'] !== '' )
            $url = 'https://' . $this->settings['CustomDomain'] . '/';
        return $url === '' ? '' : rtrim( $url, '/' ) . '/';
    }

    /**
     * The folder of the target's export and its git repository: <var>/<WorkDir>/<id>, WorkDir of
     * [StaticPublishSettings] (relative to the var directory unless absolute).
     *
     * @param string $varDir the var directory (eZSys::varDirectory())
     * @param string $workDir [StaticPublishSettings] WorkDir
     * @return string
     */
    public function directory( $varDir, $workDir = 'static-publish' )
    {
        return self::workDirectory( $varDir, $workDir ) . '/' . $this->id;
    }

    /** @return string '' or the folder of the branch the site is in (Folder, e.g. docs), without slashes */
    public function folder()
    {
        return trim( str_replace( '\\', '/', $this->settings['Folder'] ), '/' );
    }

    /**
     * The folder the build is written to: directory(), or its Folder in it (docs).
     *
     * @param string $varDir
     * @param string $workDir
     * @return string
     */
    public function outputDirectory( $varDir, $workDir = 'static-publish' )
    {
        $folder = $this->folder();
        return $this->directory( $varDir, $workDir ) . ( $folder !== '' ? '/' . $folder : '' );
    }

    /**
     * The folder all targets' exports are in.
     *
     * @param string $varDir
     * @param string $workDir
     * @return string
     */
    public static function workDirectory( $varDir, $workDir = 'static-publish' )
    {
        $workDir = str_replace( '\\', '/', trim( (string)$workDir ) );
        if ( $workDir === '' )
            $workDir = 'static-publish';
        $absolute = $workDir[0] === '/' || preg_match( '#^[a-zA-Z]:/#', $workDir );
        return rtrim( $absolute ? $workDir : rtrim( str_replace( '\\', '/', (string)$varDir ), '/' ) . '/' . $workDir, '/' );
    }

    /**
     * The siteaccesses the installation serves and which of them are for the administration.
     *
     * @return array siteaccesses (every name of RelatedSiteAccessList/AvailableSiteAccessList), admin (the names that
     *         require a login, use an administration design or are called admin or editor), urls (name => SiteURL)
     */
    public static function siteAccessContext()
    {
        $ini = eZINI::instance();
        $names = array();
        foreach ( array( 'RelatedSiteAccessList', 'AvailableSiteAccessList' ) as $variable )
            if ( $ini->hasVariable( 'SiteAccessSettings', $variable ) )
                foreach ( (array)$ini->variable( 'SiteAccessSettings', $variable ) as $name )
                    if ( is_string( $name ) && $name !== '' && !in_array( $name, $names, true ) )
                        $names[] = $name;
        $admin = array();
        $urls = array();
        foreach ( $names as $name )
        {
            $siteIni = eZSiteAccess::getIni( $name, 'site.ini' );
            $urls[$name] = $siteIni->hasVariable( 'SiteSettings', 'SiteURL' ) ? trim( (string)$siteIni->variable( 'SiteSettings', 'SiteURL' ) ) : '';
            $design = $siteIni->hasVariable( 'DesignSettings', 'SiteDesign' ) ? (string)$siteIni->variable( 'DesignSettings', 'SiteDesign' ) : '';
            if ( ( $siteIni->hasVariable( 'SiteAccessSettings', 'RequireUserLogin' ) && $siteIni->variable( 'SiteAccessSettings', 'RequireUserLogin' ) === 'true' )
                 || strpos( $design, 'admin' ) === 0 || self::adminName( $name ) )
                $admin[] = $name;
        }
        $get = function ( $name, $default ) use ( $ini )
        {
            return $ini->hasVariable( 'SiteAccessSettings', $name ) ? $ini->variable( 'SiteAccessSettings', $name ) : $default;
        };
        $prefixes = self::uriPrefixes( array( 'match_order' => $get( 'MatchOrder', 'uri' ), 'uri_match_type' => $get( 'URIMatchType', 'element' ),
                                              'uri_match_element' => $get( 'URIMatchElement', 1 ), 'uri_match_map' => $get( 'URIMatchMapItems', array() ),
                                              'host_uri_match_map' => $get( 'HostUriMatchMapItems', array() ) ), $names );
        return array( 'siteaccesses' => $names, 'admin' => $admin, 'urls' => $urls, 'prefixes' => $prefixes,
                      'default_access' => $ini->hasVariable( 'SiteSettings', 'DefaultAccess' ) ? (string)$ini->variable( 'SiteSettings', 'DefaultAccess' ) : '' );
    }

    /**
     * The URI prefix that selects each siteaccess on a host shared by several (site.ini [SiteAccessSettings]): its
     * name with MatchOrder uri and URIMatchType=element on the first element, its URIMatchMapItems key with
     * URIMatchType=map, else the URI of a HostUriMatchMapItems entry of it with MatchOrder host_uri. Pure.
     *
     * @param array $match match_order (list or a;b), uri_match_type, uri_match_element, uri_match_map (uri;siteaccess),
     *                     host_uri_match_map (host;uri;siteaccess[;method])
     * @param string[] $names
     * @return array name => prefix without slashes, or null when the siteaccess is not selected by a part of the path
     */
    public static function uriPrefixes( array $match, array $names )
    {
        $order = isset( $match['match_order'] ) ? $match['match_order'] : 'uri';
        $order = array_values( array_filter( array_map( 'trim', is_array( $order ) ? $order : explode( ';', (string)$order ) ) ) );
        $type = isset( $match['uri_match_type'] ) ? (string)$match['uri_match_type'] : 'element';
        $element = isset( $match['uri_match_element'] ) ? (int)$match['uri_match_element'] : 1;
        $out = array();
        foreach ( $names as $name )
        {
            $name = (string)$name;
            $prefix = null;
            if ( in_array( 'uri', $order, true ) )
            {
                if ( $type === 'element' && $element === 1 )
                    $prefix = $name;
                else if ( $type === 'map' )
                    foreach ( isset( $match['uri_match_map'] ) ? (array)$match['uri_match_map'] : array() as $item )
                    {
                        $parts = explode( ';', (string)$item );
                        if ( count( $parts ) >= 2 && trim( $parts[1] ) === $name && trim( $parts[0], " /" ) !== '' )
                        {
                            $prefix = trim( $parts[0], " /" );
                            break;
                        }
                    }
            }
            if ( $prefix === null && in_array( 'host_uri', $order, true ) )
                foreach ( isset( $match['host_uri_match_map'] ) ? (array)$match['host_uri_match_map'] : array() as $item )
                {
                    $parts = explode( ';', (string)$item );
                    if ( count( $parts ) >= 3 && trim( $parts[2] ) === $name && trim( $parts[1], " /" ) !== '' )
                    {
                        $prefix = trim( $parts[1], " /" );
                        break;
                    }
                }
            $out[$name] = $prefix;
        }
        return $out;
    }

    /** @return bool a name that is an administration or editing siteaccess by its name alone */
    public static function adminName( $name )
    {
        return (bool)preg_match( '#(^|_)(admin|editor|edit)$|^admin#i', (string)$name );
    }

    /**
     * Checks the target. Pure.
     *
     * @param array $context siteAccessContext(): siteaccesses, admin, urls
     * @return array errors, warnings (sentences)
     */
    public function validate( array $context )
    {
        $errors = array();
        $warnings = array();
        $s = $this->settings;

        if ( !preg_match( self::ID_PATTERN, $this->id ) )
            $errors[] = "The target id \"{$this->id}\" must be 1 to 50 lower case letters, digits, _ and -, starting with a letter or digit.";
        if ( isset( $s['_block'] ) && $s['_block'] === '' )
            $errors[] = "staticcache.ini has no block [Target_{$this->id}].";

        $siteaccesses = isset( $context['siteaccesses'] ) ? (array)$context['siteaccesses'] : array();
        $admin = isset( $context['admin'] ) ? (array)$context['admin'] : array();
        $urls = isset( $context['urls'] ) ? (array)$context['urls'] : array();
        $list = $this->siteAccesses();
        // the name of the setting a siteaccess came from, for the messages
        $named = $s['SiteAccessList'] ? 'SiteAccessList[]' : 'SiteAccess';
        if ( !$list )
            $errors[] = 'SiteAccess is not set: name the front siteaccess to publish (or several in SiteAccessList[]).';
        foreach ( $list as $name )
        {
            if ( !in_array( $name, $siteaccesses, true ) )
                $errors[] = "$named=$name is not a siteaccess of this installation.";
            else if ( in_array( $name, $admin, true ) || self::adminName( $name ) )
                $errors[] = "$named=$name is an administration or editing siteaccess; only front siteaccesses are published.";
            else if ( ( !isset( $urls[$name] ) || $urls[$name] === '' ) && $s['CrawlURL'] === '' )
                $errors[] = "The siteaccess $name has no SiteURL and the target no CrawlURL: there is no address to build from.";
        }
        if ( $s['SiteAccessList'] && $s['SiteAccess'] !== '' && !in_array( $s['SiteAccess'], $list, true ) )
            $warnings[] = "SiteAccess={$s['SiteAccess']} is not in SiteAccessList[] and is not published: SiteAccessList[] names the siteaccesses when it is set.";
        if ( $s['DefaultSiteAccess'] !== '' && !in_array( $s['DefaultSiteAccess'], $list, true ) )
            $errors[] = "DefaultSiteAccess={$s['DefaultSiteAccess']} is not one of the siteaccesses the target publishes (" . implode( ', ', $list ) . ').';
        else if ( count( $list ) > 1 )
        {
            // several siteaccesses in one copy on one host: each one but the default is in the folder of the URI prefix
            // that selects it on the live site, so that folder must exist and be its own
            $default = $this->defaultSiteAccess();
            $folders = array();
            foreach ( $list as $name )
            {
                if ( $name === $default )
                    continue;
                $prefix = self::prefixOf( $context, $name );
                if ( $prefix === null )
                    $errors[] = "The siteaccess $name is not selected by a part of the address (site.ini [SiteAccessSettings] MatchOrder with uri, "
                              . "or host_uri with a URI), so it has no folder in a copy shared with other siteaccesses on one host. Publish it as a target of its own, "
                              . 'or make it the DefaultSiteAccess at the root of the copy.';
                else if ( !preg_match( '#^[A-Za-z0-9._~-]+(/[A-Za-z0-9._~-]+)*$#', $prefix ) )
                    $errors[] = "The URI prefix $prefix of the siteaccess $name cannot be a folder of the copy.";
                else if ( isset( $folders[strtolower( $prefix )] ) )
                    $errors[] = "The siteaccesses {$folders[strtolower( $prefix )]} and $name are selected by the same URI prefix /$prefix/: one copy cannot hold both.";
                else
                    $folders[strtolower( $prefix )] = $name;
            }
            foreach ( $folders as $folder => $name )
                foreach ( $folders as $other => $otherName )
                    if ( $folder !== $other && strpos( $folder . '/', $other . '/' ) === 0 )
                        $errors[] = "The folder /$folder/ of the siteaccess $name lies in the folder /$other/ of the siteaccess $otherName.";
            foreach ( array( 'static-search', '.static-publish', '.well-known', '.github' ) as $reserved )
                if ( isset( $folders[$reserved] ) )
                    $errors[] = "The URI prefix /$reserved/ of the siteaccess {$folders[$reserved]} is a folder the build writes itself.";
        }

        foreach ( array( 'Kind' => self::KINDS, 'Delivery' => self::DELIVERIES, 'Links' => self::LINKS, 'Assets' => self::ASSETS, 'Forms' => self::FORMS,
                         'OutsideLinks' => expStaticLinkRewriter::LINK_ACTIONS, 'AdminLinks' => expStaticLinkRewriter::LINK_ACTIONS ) as $name => $allowed )
            if ( !in_array( $s[$name], $allowed, true ) )
                $errors[] = "$name={$s[$name]} is not one of " . implode( ', ', $allowed ) . '.';
        foreach ( array( 'Extras' => self::EXTRAS, 'PostProcess' => self::POST_PROCESS, 'Triggers' => self::TRIGGERS, 'Notify' => self::NOTIFY ) as $name => $allowed )
            foreach ( $s[$name] as $value )
                if ( !in_array( $value, $allowed, true ) )
                    $errors[] = "{$name}[]=$value is not one of " . implode( ', ', $allowed ) . '.';
        if ( !in_array( $s['Search'], array( 'enabled', 'disabled' ), true ) )
            $errors[] = "Search={$s['Search']} is not enabled or disabled.";

        $basePath = $s['BasePath'];
        if ( $basePath !== '' && ( !preg_match( '#^/([A-Za-z0-9._~-]+/)*$#', $basePath ) || preg_match( '#(^|/)\.\.?(/|$)#', $basePath ) ) )
            $errors[] = "BasePath=$basePath must start and end with / and hold only letters, digits, ., _, ~ and - between them (/my-site/).";
        if ( $s['Links'] === 'base-path' && expStaticLinkRewriter::normaliseBasePath( $basePath ) === '/' && $basePath === '' )
            $warnings[] = 'Links=base-path without a BasePath: the links start at the root of the host (/), as for a custom domain.';

        foreach ( array( 'PublicURL', 'CrawlURL', 'LiveURL', 'Webhook' ) as $name )
            if ( $s[$name] !== '' && !preg_match( '#^https?://[A-Za-z0-9.-]+(:[0-9]{1,5})?(/[^\s]*)?$#', $s[$name] ) )
                $errors[] = "$name={$s[$name]} is not an http or https address.";
        if ( $s['PublicURL'] !== '' && $basePath !== '' )
        {
            $publicPath = expStaticLinkRewriter::normaliseBasePath( (string)parse_url( $s['PublicURL'], PHP_URL_PATH ) );
            if ( $publicPath !== expStaticLinkRewriter::normaliseBasePath( $basePath ) )
                $warnings[] = "The path of PublicURL ($publicPath) is not BasePath ($basePath): the public site will not find its files below the base path.";
        }
        if ( $s['CustomDomain'] !== '' && !expStaticExtras::validDomain( $s['CustomDomain'] ) )
            $errors[] = "CustomDomain={$s['CustomDomain']} is not a host name (www.example.com).";
        if ( $s['CustomDomain'] !== '' && $basePath !== '' && $basePath !== '/' )
            $warnings[] = 'A custom domain serves the site at the root of the domain: BasePath should be empty or /.';

        if ( $s['Branch'] !== '' && ( !preg_match( '#^[A-Za-z0-9._/-]{1,100}$#', $s['Branch'] ) || preg_match( '#\.\.|//|^/|/$|\.lock$|^-#', $s['Branch'] ) ) )
            $errors[] = "Branch={$s['Branch']} is not a valid branch name.";
        if ( $s['Folder'] !== '' && expStaticManifest::normalisePath( trim( $s['Folder'], '/' ) ) === null )
            $errors[] = "Folder={$s['Folder']} must be a folder inside the repository (docs).";
        if ( $s['Kind'] === 'github-pages' && $s['Folder'] !== '' && trim( $s['Folder'], '/' ) !== 'docs' )
            $errors[] = 'GitHub Pages publishes from the root of the branch or from /docs: Folder must be empty or docs.';
        if ( $s['Remote'] !== '' && !preg_match( '#^(https?://|ssh://|git@|file://|/)[^\s]+$#', $s['Remote'] ) )
            $errors[] = "Remote={$s['Remote']} is not a git address (https://, ssh://, git@host:owner/repo.git or a path).";
        if ( preg_match( '#^https?://[^/@\s]+:[^/@\s]+@#', $s['Remote'] ) )
            $errors[] = 'Remote must not hold a password or token; name a credential in Credential instead.';

        $credential = $s['Credential'];
        $kind = strpos( $credential, ':' ) === false ? $credential : substr( $credential, 0, strpos( $credential, ':' ) );
        if ( !in_array( $kind, self::CREDENTIAL_KINDS, true ) )
            $errors[] = "Credential=$credential must be token:<secret name>, sshkey:<key id>, app:<installation> or none.";
        else if ( $kind !== 'none' && !preg_match( '#^[a-z]+:(env:)?[A-Za-z0-9_.-]{1,100}$#', $credential ) )
            $errors[] = "Credential=$credential names no secret, key or installation after $kind:.";

        if ( !ctype_digit( $s['MaxPages'] ) || (int)$s['MaxPages'] < 1 || (int)$s['MaxPages'] > 20000 )
            $errors[] = "MaxPages={$s['MaxPages']} must be a number from 1 to 20000.";
        if ( $s['MaxDepth'] !== '' && ( !ctype_digit( $s['MaxDepth'] ) || (int)$s['MaxDepth'] > 30 ) )
            $errors[] = "MaxDepth={$s['MaxDepth']} must be empty (staticcache.ini [CacheSettings] MaxCacheDepth) or a number from 0 to 30.";
        foreach ( $s['ExcludeSections'] as $section )
            if ( !ctype_digit( $section ) )
                $errors[] = "ExcludeSections[]=$section must be a section id.";
        foreach ( $s['ExcludePaths'] as $path )
            if ( $path === '' || $path[0] !== '/' )
                $errors[] = "ExcludePaths[]=$path must be a path of the site starting with / (* matches any part).";

        if ( $s['Schedule'] !== '' && !expStaticSchedule::valid( $s['Schedule'] ) )
            $errors[] = "Schedule={$s['Schedule']} is not a cron expression (five fields, for example 0 3 * * *, or @daily).";
        if ( in_array( 'schedule', $s['Triggers'], true ) && $s['Schedule'] === '' )
            $warnings[] = 'Triggers[]=schedule needs a Schedule: the target is not published on a schedule.';
        if ( $s['Webhook'] !== '' && !expStaticNotifier::validWebhook( $s['Webhook'] ) )
            $errors[] = "Webhook={$s['Webhook']} must be an https address (http only to localhost).";
        if ( in_array( 'license', $s['Extras'], true ) && $s['License'] === '' )
            $warnings[] = 'Extras[]=license needs License (an SPDX id such as CC-BY-4.0, or the text). No LICENSE file is written.';
        if ( $s['Forms'] === 'endpoint' && $this->publicUrl() === '' )
            $warnings[] = 'Forms=endpoint needs PublicURL (or CustomDomain): the form endpoint takes posts only from the address of the copy. The forms are hidden.';

        if ( !in_array( 'strip', $s['PostProcess'], true ) )
            $warnings[] = 'PostProcess[] does not name strip; it runs on every build all the same.';
        if ( $s['Kind'] === 'github-pages' && !in_array( 'nojekyll', $s['Extras'], true ) )
            $warnings[] = 'Without Extras[]=nojekyll GitHub Pages runs Jekyll, which leaves out every file and folder starting with _ or .';
        if ( in_array( 'sitemap', $s['Extras'], true ) && $this->publicUrl() === '' )
            $warnings[] = 'Extras[]=sitemap needs PublicURL (or CustomDomain): a sitemap lists absolute addresses. It is not written.';
        if ( in_array( 'cname', $s['Extras'], true ) && $s['CustomDomain'] === '' )
            $warnings[] = 'Extras[]=cname needs CustomDomain. No CNAME file is written.';
        if ( $s['Kind'] === 'github-pages' && $s['Delivery'] === 'actions' && trim( $s['Folder'], '/' ) !== '' )
            $warnings[] = 'Delivery=actions writes its workflow only with the build at the root of the branch: leave Folder empty.';
        if ( $s['Kind'] === 'github-pages' && $s['Remote'] !== '' && class_exists( 'expStaticGitHub' ) && expStaticGitHub::parseRemote( $s['Remote'] ) === null )
            $warnings[] = 'Remote is not a github.com repository: the GitHub steps (create the repository, Pages, the domain, the build status) are not available.';
        return array( 'errors' => $errors, 'warnings' => $warnings );
    }

    /**
     * Whether a page path is excluded by ExcludePaths[] (shell patterns, * matches any part, a pattern ending in /*
     * matches the page itself too).
     *
     * @param string $pagePath /about-us/team
     * @return bool
     */
    public function excludesPath( $pagePath )
    {
        $pagePath = '/' . trim( (string)$pagePath, '/' );
        foreach ( $this->settings['ExcludePaths'] as $pattern )
        {
            $pattern = '/' . trim( $pattern, '/' );
            if ( fnmatch( $pattern, $pagePath ) )
                return true;
            if ( substr( $pattern, -2 ) === '/*' && $pagePath === substr( $pattern, 0, -2 ) )
                return true;
        }
        return false;
    }

    /** @return array the target as data for a report (no credential value; Credential holds only a name) */
    public function toArray()
    {
        return array( 'id' => $this->id, 'name' => $this->name() ) + $this->settings();
    }

    /**
     * Targets as an INI migration (expMigrationIni: a file YYYYMMDDHHMMSS_<name>.ini of a migrations folder or a
     * site package), so they travel to another installation: each [Target_<id>] block in full and the id added to
     * TargetList[]. Credentials travel as their names (token:<secret>, sshkey:<secret>, app:<installation>); a value
     * never does: the receiving installation stores its own in Setup > Secrets. Static publishing itself is not
     * switched on. Pure.
     *
     * @param expStaticTarget[] $targets
     * @param string $description
     * @return string
     */
    public static function migrationIni( array $targets, $description = '' )
    {
        $ids = array();
        foreach ( $targets as $target )
            $ids[] = $target->id();
        $text = "#?ini charset=\"utf-8\"?\n# description: " . ( $description !== '' ? str_replace( array( "\r", "\n" ), ' ', (string)$description )
                                                                                   : 'Static publishing targets: ' . implode( ', ', $ids ) ) . "\n"
              . "# The credentials are names only: store their values in Setup > Secrets of this installation.\n\n"
              . "[staticcache.ini/StaticPublishSettings]\n";
        foreach ( $ids as $id )
            $text .= "TargetList[]=$id\n";
        foreach ( $targets as $target )
        {
            $text .= "\n[staticcache.ini/Target_" . $target->id() . "]\n";
            foreach ( $target->settings() as $name => $value )
            {
                if ( is_array( $value ) )
                {
                    $text .= $name . "[]\n";
                    foreach ( $value as $item )
                        $text .= $name . '[]=' . str_replace( array( "\r", "\n" ), ' ', (string)$item ) . "\n";
                }
                else
                    $text .= $name . '=' . str_replace( array( "\r", "\n" ), ' ', (string)$value ) . "\n";
            }
        }
        return $text;
    }
}
