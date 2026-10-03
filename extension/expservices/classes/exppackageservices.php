<?php
/**
 * exppackage: the packages (package/list, package/read), read-only.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package expservices
 */

class expPackageServices extends expServiceBase
{
    public static $services = array(
        'list' => array( 'summary' => 'The packages: name, version, vendor, type, summary, installed',
            'access' => array( 'package', 'list' ), 'write' => false, 'args' => array( 'limit' => 'int', 'offset' => 'int', 'type' => 'string', 'repository' => 'string' ), 'returns' => 'paged list of package descriptors' ),
        'view' => array( 'summary' => 'One package: description, vendor, maintainers, dependencies, state',
            'access' => array( 'package', 'read' ), 'write' => false, 'args' => array( 'name' => 'string', 'repository' => 'string' ), 'returns' => 'package descriptor with description and dependencies' ),
        'files' => array( 'summary' => 'The file count and the simple file list of a package',
            'access' => array( 'package', 'read' ), 'write' => false, 'args' => array( 'name' => 'string', 'limit' => 'int', 'offset' => 'int' ), 'returns' => 'paged list of files' ),
        'types' => array( 'summary' => 'The package types',
            'access' => array( 'package', 'list' ), 'write' => false, 'args' => array(), 'returns' => 'list of id, name' ),
        'states' => array( 'summary' => 'The package states',
            'access' => array( 'package', 'list' ), 'write' => false, 'args' => array(), 'returns' => 'list of id, name' ),
        'repositories' => array( 'summary' => 'The package repositories',
            'access' => array( 'package', 'list' ), 'write' => false, 'args' => array(), 'returns' => 'list of id, name, type' ),
        'count' => array( 'summary' => 'The number of packages and of installed packages',
            'access' => array( 'package', 'list' ), 'write' => false, 'args' => array(), 'returns' => 'total, installed' ),
    );

    public static function exportPackage( eZPackage $p, $full = false )
    {
        $a = function ( $name ) use ( $p ) { return $p->attribute( $name ); };
        $out = array( 'name' => $a( 'name' ), 'summary' => $a( 'summary' ), 'version' => $a( 'version-number' ) . '-' . $a( 'release-number' ),
                      'vendor' => $a( 'vendor' ), 'type' => $a( 'type' ), 'state' => $a( 'state' ),
                      'installed' => (bool)$a( 'is_installed' ), 'extension' => $a( 'extension' ) );
        if ( $full )
        {
            $out += array( 'description' => $a( 'description' ), 'licence' => $a( 'licence' ), 'priority' => $a( 'priority' ),
                           'release_timestamp' => self::iso( $a( 'release-timestamp' ) ), 'file_count' => (int)$a( 'file-count' ),
                           'dependencies' => $a( 'dependencies' ), 'maintainers' => $a( 'maintainers' ) );
        }
        return $out;
    }

    protected static function fetch( $name, $repository = null )
    {
        $parameters = $repository ? array( 'repository_id' => $repository ) : array();
        $package = eZPackage::fetch( $name, false, $repository ?: false );
        if ( !$package instanceof eZPackage )
            throw new expServiceException( "No package '$name'", 404 );
        return $package;
    }

    public static function list( $args )
    {
        self::guard( __FUNCTION__ );
        $type = self::arg( $args, 2, 'string', null );
        $repository = self::arg( $args, 3, 'string', null );
        $parameters = $repository ? array( 'repository_id' => $repository ) : array();
        $filter = $type ? array( 'type' => $type ) : array();
        $items = array();
        foreach ( (array)eZPackage::fetchPackages( $parameters, $filter ) as $p )
            $items[] = self::exportPackage( $p );
        return self::pageOf( $items, $args, 0, 1 );
    }

    public static function view( $args )
    {
        self::guard( __FUNCTION__ );
        $name = self::arg( $args, 0, 'string' );
        return self::ok( self::exportPackage( self::fetch( $name, self::arg( $args, 1, 'string', null ) ), true ) );
    }

    public static function files( $args )
    {
        self::guard( __FUNCTION__ );
        $package = self::fetch( self::arg( $args, 0, 'string' ) );
        $files = array();
        foreach ( (array)$package->attribute( 'simple-file-list' ) as $f )
            $files[] = is_array( $f ) ? array( 'path' => isset( $f['path'] ) ? $f['path'] : null, 'type' => isset( $f['type'] ) ? $f['type'] : null ) : array( 'path' => (string)$f );
        return self::pageOf( $files, $args, 1, 2 );
    }

    public static function types( $args )
    {
        self::guard( __FUNCTION__ );
        $list = eZPackage::typeList();
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function states( $args )
    {
        self::guard( __FUNCTION__ );
        $list = eZPackage::stateList();
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function repositories( $args )
    {
        self::guard( __FUNCTION__ );
        $list = array();
        foreach ( eZPackage::packageRepositories() as $r )
            $list[] = array( 'id' => $r['id'], 'name' => $r['name'], 'type' => $r['type'] );
        return self::ok( $list, array( 'total' => count( $list ) ) );
    }

    public static function count( $args )
    {
        self::guard( __FUNCTION__ );
        $total = 0;
        $installed = 0;
        foreach ( (array)eZPackage::fetchPackages() as $p )
        {
            $total++;
            if ( $p->attribute( 'is_installed' ) )
                $installed++;
        }
        return self::ok( array( 'total' => $total, 'installed' => $installed ) );
    }
}
