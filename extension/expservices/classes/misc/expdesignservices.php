<?php
/**
 * ezjscore/call/expdesign::<service> - designs, templates and template overrides (read only, policy setup/administrate):
 * which designs exist and are in use, the design bases in priority order, the overrides of override.ini and the
 * templates of a design. Template source can be read for templates inside a design directory only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expDesignServices extends expServiceBase
{
    public static $services = array(
        'designs' => array( 'summary' => 'The design directories of the site and of the extensions', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'list of name, path, extension' ),
        'current' => array( 'summary' => 'The standard design, the site design and the additional designs of this siteaccess', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'standard, site, additional' ),
        'bases' => array( 'summary' => 'The design bases in priority order (first wins)', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'list of paths' ),
        'extensions' => array( 'summary' => 'The extensions that provide designs for this siteaccess', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'list of extension names' ),
        'overrides' => array( 'summary' => 'The template overrides of override.ini', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'source' => 'string' ), 'returns' => 'paged list of name, source, match file, match' ),
        'override' => array( 'summary' => 'One override by name', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'name' => 'string' ), 'returns' => 'override' ),
        'overridecount' => array( 'summary' => 'How many overrides there are', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'count' ),
        'overridesfor' => array( 'summary' => 'The overrides of one template source (node/view/full.tpl)', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'source' => 'string' ), 'returns' => 'list of overrides with their conditions' ),
        'templates' => array( 'summary' => 'The templates of the resolved design, with the design base each comes from', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'limit' => 'int', 'offset' => 'int', 'prefix' => 'string' ), 'returns' => 'paged list of template, base' ),
        'templatecount' => array( 'summary' => 'How many templates the design resolves', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'count' ),
        'resolve' => array( 'summary' => 'Which file a template path resolves to and its overrides', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'template' => 'string' ), 'returns' => 'template, base_dir, overrides' ),
        'source' => array( 'summary' => 'The source of a template of a design directory (design/<name>/templates/..., read only)', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array( 'path' => 'string' ), 'returns' => 'path, size, modified, content' ),
        'cachestate' => array( 'summary' => 'Template cache settings: compile, cache, override cache and design location cache', 'access' => array( 'setup', 'administrate' ), 'write' => false,
            'args' => array(), 'returns' => 'settings' ),
    );

    public static function designs( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( glob( 'design/*', GLOB_ONLYDIR ) ?: array() as $d )
            $list[] = array( 'name' => basename( $d ), 'path' => $d, 'extension' => null );
        foreach ( glob( 'extension/*/design/*', GLOB_ONLYDIR ) ?: array() as $d )
        {
            $parts = explode( '/', $d );
            $list[] = array( 'name' => $parts[3], 'path' => $d, 'extension' => $parts[1] );
        }
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function current( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'site.ini' );
        return self::ok( array( 'standard' => eZTemplateDesignResource::designSetting( 'standard' ), 'site' => eZTemplateDesignResource::designSetting( 'site' ),
                                'additional' => array_values( (array)$ini->variable( 'DesignSettings', 'AdditionalSiteDesignList' ) ) ) );
    }

    public static function bases( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array_values( eZTemplateDesignResource::allDesignBases() ) );
    }

    public static function extensions( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array_values( eZTemplateDesignResource::designExtensions() ) );
    }

    protected static function overrideGroups()
    {
        $out = array();
        foreach ( eZINI::instance( 'override.ini' )->groups() as $name => $v )
            if ( isset( $v['Source'] ) )
                $out[$name] = array( 'name' => $name, 'source' => $v['Source'], 'match_file' => isset( $v['MatchFile'] ) ? $v['MatchFile'] : '',
                                     'match' => isset( $v['Match'] ) ? (object)$v['Match'] : (object)array(),
                                     'priority' => isset( $v['Priority'] ) && $v['Priority'] !== '' ? (int)$v['Priority'] : null );
        return $out;
    }

    public static function overrides( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $source = self::arg( $args, 2, 'string', '' );
        $all = array_values( self::overrideGroups() );
        if ( $source !== '' )
            $all = array_values( array_filter( $all, function ( $o ) use ( $source ) { return $o['source'] === $source; } ) );
        return self::page( array_slice( $all, $offset, $limit ), count( $all ), $offset, $limit );
    }

    public static function override( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        $all = self::overrideGroups();
        if ( !isset( $all[$name] ) )
            throw new expServiceException( "No override '$name'", 404 );
        return self::ok( $all[$name] );
    }

    public static function overridecount( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'count' => count( self::overrideGroups() ) ) );
    }

    public static function overridesfor( $args )
    {
        self::guard( __FUNCTION__ );
        $source = ltrim( self::arg( $args, 0, 'string' ), '/' );
        $list = array();
        foreach ( self::overrideGroups() as $o )
            if ( ltrim( $o['source'], '/' ) === $source )
                $list[] = $o;
        return self::ok( $list );
    }

    protected static function templateMap()
    {
        $map = eZTemplateDesignResource::overrideArray();
        return is_array( $map ) ? $map : array();
    }

    public static function templates( $args )
    {
        self::guard( __FUNCTION__ );
        list( $limit, $offset ) = self::paging( $args, 0, 1 );
        $prefix = ltrim( self::arg( $args, 2, 'string', '' ), '/' );
        $names = array_keys( self::templateMap() );
        sort( $names );
        $all = array();
        foreach ( $names as $n )
            if ( $prefix === '' || strpos( ltrim( $n, '/' ), $prefix ) === 0 )
                $all[] = $n;
        $map = self::templateMap();
        $items = array();
        foreach ( array_slice( $all, $offset, $limit ) as $n )
            $items[] = array( 'template' => ltrim( $n, '/' ), 'base_dir' => isset( $map[$n]['base_dir'] ) ? $map[$n]['base_dir'] : null,
                              'overrides' => isset( $map[$n]['custom_match'] ) ? count( $map[$n]['custom_match'] ) : 0 );
        return self::page( $items, count( $all ), $offset, $limit );
    }

    public static function templatecount( $args )
    {
        self::guard( __FUNCTION__ );
        return self::ok( array( 'count' => count( self::templateMap() ) ) );
    }

    public static function resolve( $args )
    {
        self::guard( __FUNCTION__ );
        $t = '/' . ltrim( self::arg( $args, 0, 'string' ), '/' );
        $map = self::templateMap();
        if ( !isset( $map[$t] ) )
            throw new expServiceException( 'The design has no such template', 404 );
        $overrides = array();
        foreach ( isset( $map[$t]['custom_match'] ) ? $map[$t]['custom_match'] : array() as $c )
            $overrides[] = array( 'name' => $c['override_name'], 'match_file' => $c['match_file'], 'conditions' => $c['conditions'] ? (object)$c['conditions'] : (object)array() );
        return self::ok( array( 'template' => ltrim( $t, '/' ), 'base_dir' => isset( $map[$t]['base_dir'] ) ? $map[$t]['base_dir'] : null, 'overrides' => $overrides ) );
    }

    public static function source( $args )
    {
        self::guard( __FUNCTION__ );
        $path = self::arg( $args, 0, 'string' );
        if ( strpos( $path, '..' ) !== false || !preg_match( '#^(design/[A-Za-z0-9_-]+|extension/[A-Za-z0-9_.-]+/design/[A-Za-z0-9_-]+)/(templates|override/templates)/[A-Za-z0-9_./-]+\.tpl$#', $path ) )
            throw new expServiceException( 'The path must be a .tpl file below design/<name>/templates or override/templates', 400 );
        if ( !is_file( $path ) )
            throw new expServiceException( 'No such template file', 404 );
        $size = filesize( $path );
        if ( $size > 512 * 1024 )
            throw new expServiceException( 'The template is larger than 512 KB', 422 );
        return self::ok( array( 'path' => $path, 'size' => (int)$size, 'modified' => self::iso( filemtime( $path ) ), 'content' => file_get_contents( $path ) ) );
    }

    public static function cachestate( $args )
    {
        self::guard( __FUNCTION__ );
        $ini = eZINI::instance( 'site.ini' );
        $get = function ( $g, $v ) use ( $ini ) { return $ini->hasVariable( $g, $v ) ? $ini->variable( $g, $v ) : null; };
        return self::ok( array( 'template_compile' => $get( 'TemplateSettings', 'TemplateCompile' ), 'template_cache' => $get( 'TemplateSettings', 'TemplateCache' ),
                                'delay_cache_clear' => $get( 'TemplateSettings', 'DelayedCacheClear' ), 'design_location_cache' => $get( 'DesignSettings', 'DesignLocationCache' ),
                                'override_cache' => $get( 'OverrideSettings', 'OverrideCache' ) ) );
    }
}
