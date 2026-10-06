<?php
/**
 * File containing the eZPackageCatalog class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What the package pages say about the packages on disk: every repository with its counts, one card per package
 * (name, version, type, summary, maintainers, dependencies, install state, files, size, last change, whether the
 * installer takes it as a source), what a package carries (classes, objects, extensions, settings, files by kind),
 * and the search, filters, sort and page of the list.
 *
 * Reading is the only thing it does. scan() reads the repositories (and, unless told not to, the install state
 * from the database, as eZPackage::fetch() always has); everything else works on the arrays it returns, without a
 * database or settings, so it is tested with packages made under var/tmp.
 *
 * A directory of a repository with a package.xml that cannot be read is listed as a problem instead of ending the
 * page; a directory without a package.xml is counted, not listed (leftovers of interrupted exports and wizards).
 */
class eZPackageCatalog
{
    /** The sort keys of the list, each with its default direction. */
    static $sortKeys = array( 'name' => 'asc', 'changed' => 'desc', 'size' => 'desc', 'version' => 'desc',
                              'type' => 'asc', 'repository' => 'asc', 'state' => 'asc' );

    /** The states the list filters by. */
    static $states = array( 'installed', 'not_installed', 'import', 'no_items', 'installer' );

    /**
     * Every package of every repository (or of the repositories given), as cards, with the repositories' counts.
     *
     * @param array|null $repositories as eZPackage::packageRepositories(); null reads them
     * @param bool $dbAvailable read the install state from the database
     * @param string|null $vendor the setup wizard's repository (package.ini [RepositorySettings] Vendor); null reads it
     * @return array( 'cards' => name@repository => card, 'repositories' => id => repository with 'counts',
     *                'problems' => list of array( 'repository', 'directory', 'reason' ) )
     */
    static function scan( $repositories = null, $dbAvailable = true, $vendor = null )
    {
        if ( $repositories === null )
            $repositories = eZPackage::packageRepositories();
        if ( $vendor === null )
            $vendor = self::vendorRepository();

        $cards = array();
        $problems = array();
        $orphans = array();
        foreach ( $repositories as $repository )
        {
            $found = self::scanRepository( $repository, $dbAvailable );
            foreach ( $found['packages'] as $package )
            {
                $stats = self::directoryStats( $package->path() );
                $card = self::card( $package, $repository, $stats );
                $cards[$card['key']] = $card;
            }
            $problems = array_merge( $problems, $found['problems'] );
            $orphans[$repository['id']] = $found['orphans'];
        }
        $cards = self::link( $cards, $vendor );
        return array( 'cards' => $cards,
                      'repositories' => self::repositorySummary( $repositories, $cards, $orphans, $problems, $vendor ),
                      'problems' => $problems );
    }

    /**
     * The setup wizard's own repository, package.ini [RepositorySettings] Vendor.
     *
     * @return string
     */
    static function vendorRepository()
    {
        $ini = eZINI::instance( 'package.ini' );
        return $ini->hasVariable( 'RepositorySettings', 'Vendor' ) ? (string)$ini->variable( 'RepositorySettings', 'Vendor' ) : '';
    }

    /**
     * The packages of one repository directory.
     *
     * @param array $repository with 'id' and 'path'
     * @param bool $dbAvailable
     * @return array( 'packages' => eZPackage[], 'problems' => array, 'orphans' => int )
     */
    static function scanRepository( array $repository, $dbAvailable = true )
    {
        $result = array( 'packages' => array(), 'problems' => array(), 'orphans' => 0 );
        $path = (string)$repository['path'];
        if ( !is_dir( $path ) )
            return $result;
        $names = array();
        foreach ( new DirectoryIterator( $path ) as $item )
        {
            if ( $item->isDot() || !$item->isDir() || $item->isLink() )
                continue;
            $names[] = $item->getFilename();
        }
        sort( $names );
        foreach ( $names as $name )
        {
            if ( $name[0] === '.' )
                continue;
            if ( !is_file( $path . '/' . $name . '/' . eZPackage::definitionFilename() ) )
            {
                $result['orphans']++;
                continue;
            }
            if ( !eZPackageRequestGuard::isSafeName( $name ) )
            {
                $result['problems'][] = array( 'repository' => $repository['id'], 'directory' => $name, 'reason' => 'name' );
                continue;
            }
            $package = false;
            try
            {
                $package = self::fetchQuietly( $name, $repository, $dbAvailable );
            }
            catch ( Throwable $e )
            {
                $package = false;
            }
            if ( !$package instanceof eZPackage )
            {
                $result['problems'][] = array( 'repository' => $repository['id'], 'directory' => $name, 'reason' => 'definition' );
                continue;
            }
            if ( (string)$package->attribute( 'name' ) !== $name )
            {
                $result['problems'][] = array( 'repository' => $repository['id'], 'directory' => $name, 'reason' => 'mismatch' );
                continue;
            }
            $result['packages'][] = $package;
        }
        return $result;
    }

    /**
     * eZPackage::fetch() of one package of one repository, with the parser's warnings about a broken package.xml
     * kept off the page.
     */
    private static function fetchQuietly( $name, array $repository, $dbAvailable )
    {
        $previous = libxml_use_internal_errors( true );
        try
        {
            // by the repository's own path, so it is this repository's copy that is read even when another one
            // has a package of the same name, and so a repository given by path (tests) works the same
            $package = eZPackage::fetch( $name, $repository['path'], false, $dbAvailable );
            if ( $package )
                $package->setCurrentRepositoryInformation( $repository );
        }
        finally
        {
            libxml_clear_errors();
            libxml_use_internal_errors( $previous );
        }
        return $package;
    }

    /**
     * The files of a directory: how many, their bytes and the newest modification time. Hidden files and
     * directories (the kernel's .cache) are left out; links are not followed but counted: eZDir::recursiveDelete(),
     * which removes a package, follows a link to a directory and empties its target, so a package with a link in
     * it is not offered for removal (eZPackageRemovalPlan).
     *
     * @param string $path
     * @return array( 'files' => int, 'bytes' => int, 'changed' => int|false, 'links' => int )
     */
    static function directoryStats( $path )
    {
        $stats = array( 'files' => 0, 'bytes' => 0, 'changed' => false, 'links' => 0 );
        if ( !is_dir( $path ) )
            return $stats;
        $links = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ),
                function ( $file ) use ( &$links )
                {
                    if ( $file->isLink() )
                    {
                        $links++;
                        return false;
                    }
                    return $file->getFilename()[0] !== '.';
                } ) );
        foreach ( $iterator as $file )
        {
            if ( !$file->isFile() )
                continue;
            $stats['files']++;
            $stats['bytes'] += (int)$file->getSize();
            $mtime = (int)$file->getMTime();
            if ( $stats['changed'] === false || $mtime > $stats['changed'] )
                $stats['changed'] = $mtime;
        }
        $stats['links'] = $links;
        return $stats;
    }

    /**
     * The card of one package.
     *
     * @param eZPackage $package
     * @param array $repository with 'id', 'name', 'type'
     * @param array $stats from directoryStats()
     * @return array
     */
    static function card( eZPackage $package, array $repository, array $stats )
    {
        $name = (string)$package->attribute( 'name' );
        $installType = (string)$package->attribute( 'install_type' );
        $items = $package->attribute( 'install' );
        $itemCount = is_array( $items ) ? count( $items ) : 0;
        if ( $installType !== 'install' )
            $state = 'import';
        else if ( $itemCount === 0 )
            $state = 'no_items';
        else
            $state = $package->attribute( 'is_installed' ) ? 'installed' : 'not_installed';

        $maintainers = array();
        foreach ( (array)$package->attribute( 'maintainers' ) as $maintainer )
        {
            if ( isset( $maintainer['name'] ) && trim( (string)$maintainer['name'] ) !== '' )
                $maintainers[] = array( 'name' => (string)$maintainer['name'],
                                        'role' => isset( $maintainer['role'] ) ? (string)$maintainer['role'] : '' );
        }

        $requires = array();
        $dependencies = $package->attribute( 'dependencies' );
        if ( is_array( $dependencies ) && isset( $dependencies['requires'] ) && is_array( $dependencies['requires'] ) )
        {
            foreach ( $dependencies['requires'] as $require )
            {
                if ( isset( $require['type'] ) && $require['type'] === 'ezpackage' && isset( $require['name'] ) && $require['name'] !== '' )
                    $requires[] = array( 'name' => (string)$require['name'],
                                         'min_version' => isset( $require['min-version'] ) ? (string)$require['min-version'] : '',
                                         'present' => null );
            }
        }

        $version = trim( (string)$package->attribute( 'version-number' ) . '-' . (string)$package->attribute( 'release-number' ), '-' );
        $card = array(
            'key' => $name . '@' . $repository['id'],
            'name' => $name,
            'summary' => (string)$package->attribute( 'summary' ),
            'version' => $version,
            'type' => (string)$package->attribute( 'type' ),
            'vendor' => (string)$package->attribute( 'vendor' ),
            'repository_id' => (string)$repository['id'],
            'repository_name' => isset( $repository['name'] ) ? (string)$repository['name'] : (string)$repository['id'],
            'repository_type' => isset( $repository['type'] ) ? (string)$repository['type'] : 'global',
            'maintainers' => $maintainers,
            'requires' => $requires,
            'required_by' => array(),
            'install_type' => $installType,
            'install_items' => $itemCount,
            'state' => $state,
            'installed' => $state === 'installed',
            'files' => (int)$stats['files'],
            'links' => isset( $stats['links'] ) ? (int)$stats['links'] : 0,
            'bytes' => (int)$stats['bytes'],
            'changed' => $stats['changed'],
            'release_timestamp' => (int)$package->attribute( 'release-timestamp' ),
            'packaging_timestamp' => (int)$package->attribute( 'packaging-timestamp' ),
            'licence' => (string)$package->attribute( 'licence' ),
            'package_state' => (string)$package->attribute( 'state' ),
            'installer_source' => false,
            'installer_reasons' => array(),
        );
        $card['search'] = self::searchText( $card );
        return $card;
    }

    /**
     * The lower-case text the list's search looks in.
     */
    static function searchText( array $card )
    {
        $words = array( $card['name'], $card['summary'], $card['type'], $card['vendor'], $card['repository_id'], $card['version'] );
        foreach ( $card['maintainers'] as $maintainer )
            $words[] = $maintainer['name'];
        foreach ( $card['requires'] as $require )
            $words[] = $require['name'];
        $text = implode( ' ', $words );
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
    }

    /**
     * The links between the cards: whether each required package is there, which packages require each one, and
     * which packages the installer takes as its sources:
     * - every package in the setup wizard's repository ($vendor, package.ini [RepositorySettings] Vendor), which
     *   the published packages are built from;
     * - every site package (type "site"), which the setup wizard offers;
     * - every package such a package requires, and what those require in turn.
     *
     * @param array $cards key => card
     * @param string $vendor
     * @return array the cards
     */
    static function link( array $cards, $vendor )
    {
        $byName = array();
        foreach ( $cards as $key => $card )
            $byName[$card['name']][] = $key;

        foreach ( $cards as $key => $card )
        {
            foreach ( $card['requires'] as $i => $require )
            {
                $cards[$key]['requires'][$i]['present'] = isset( $byName[$require['name']] );
                if ( isset( $byName[$require['name']] ) )
                    foreach ( $byName[$require['name']] as $requiredKey )
                        if ( !in_array( $card['name'], $cards[$requiredKey]['required_by'], true ) )
                            $cards[$requiredKey]['required_by'][] = $card['name'];
            }
        }

        $queue = array();
        foreach ( $cards as $key => $card )
        {
            if ( $vendor !== '' && $card['repository_id'] === (string)$vendor )
                $cards[$key]['installer_reasons'][] = 'vendor_repository';
            if ( $card['type'] === 'site' )
                $cards[$key]['installer_reasons'][] = 'site_package';
            if ( $cards[$key]['installer_reasons'] )
                $queue[] = $key;
        }
        $seen = array_flip( $queue );
        while ( $queue )
        {
            $key = array_shift( $queue );
            foreach ( $cards[$key]['requires'] as $require )
            {
                if ( !isset( $byName[$require['name']] ) )
                    continue;
                foreach ( $byName[$require['name']] as $requiredKey )
                {
                    if ( !in_array( 'required_by_source', $cards[$requiredKey]['installer_reasons'], true ) )
                        $cards[$requiredKey]['installer_reasons'][] = 'required_by_source';
                    if ( !isset( $seen[$requiredKey] ) )
                    {
                        $seen[$requiredKey] = true;
                        $queue[] = $requiredKey;
                    }
                }
            }
        }
        foreach ( $cards as $key => $card )
        {
            $cards[$key]['installer_source'] = (bool)$card['installer_reasons'];
            sort( $cards[$key]['required_by'] );
        }
        return $cards;
    }

    /**
     * The repositories with what they hold.
     *
     * @return array id => repository + 'counts' => array( packages, installed, not_installed, import, no_items,
     *               installer, bytes, files, orphans, problems ) + 'is_vendor'
     */
    static function repositorySummary( array $repositories, array $cards, array $orphans, array $problems, $vendor )
    {
        $result = array();
        foreach ( $repositories as $repository )
        {
            $id = (string)$repository['id'];
            $repository['counts'] = array( 'packages' => 0, 'installed' => 0, 'not_installed' => 0, 'import' => 0, 'no_items' => 0,
                                           'installer' => 0, 'bytes' => 0, 'files' => 0,
                                           'orphans' => isset( $orphans[$id] ) ? (int)$orphans[$id] : 0, 'problems' => 0 );
            $repository['is_vendor'] = $vendor !== '' && $id === (string)$vendor;
            $result[$id] = $repository;
        }
        foreach ( $cards as $card )
        {
            $id = $card['repository_id'];
            if ( !isset( $result[$id] ) )
                continue;
            $counts =& $result[$id]['counts'];
            $counts['packages']++;
            $counts[$card['state']]++;
            if ( $card['installer_source'] )
                $counts['installer']++;
            $counts['bytes'] += $card['bytes'];
            $counts['files'] += $card['files'];
            unset( $counts );
        }
        foreach ( $problems as $problem )
            if ( isset( $result[$problem['repository']] ) )
                $result[$problem['repository']]['counts']['problems']++;
        return $result;
    }

    /**
     * The totals over the repositories of repositorySummary().
     */
    static function totals( array $repositories )
    {
        $totals = array( 'repositories' => count( $repositories ), 'packages' => 0, 'installed' => 0, 'not_installed' => 0,
                         'import' => 0, 'no_items' => 0, 'installer' => 0, 'bytes' => 0, 'files' => 0, 'orphans' => 0, 'problems' => 0 );
        foreach ( $repositories as $repository )
            foreach ( $repository['counts'] as $key => $value )
                $totals[$key] += $value;
        return $totals;
    }

    /**
     * The list's state, from view parameters (or a form), each value checked against what is offered.
     *
     * @param array $params search, type, state, sort, dir
     * @param string[] $types the types the cards have
     * @return array( 'search', 'type', 'state', 'sort', 'dir' )
     */
    static function normaliseQuery( array $params, array $types = array() )
    {
        $get = function ( $name ) use ( $params ) { return isset( $params[$name] ) && is_scalar( $params[$name] ) ? trim( (string)$params[$name] ) : ''; };
        $search = $get( 'search' );
        if ( function_exists( 'mb_substr' ) )
            $search = mb_substr( $search, 0, 100, 'UTF-8' );
        else
            $search = substr( $search, 0, 100 );
        $type = $get( 'type' );
        $state = $get( 'state' );
        $sort = $get( 'sort' );
        $dir = $get( 'dir' );
        if ( !isset( self::$sortKeys[$sort] ) )
            $sort = 'name';
        if ( $dir !== 'asc' && $dir !== 'desc' )
            $dir = self::$sortKeys[$sort];
        return array(
            'search' => preg_replace( '/[\x00-\x1F\x7F]/', '', $search ),
            'type' => in_array( $type, $types, true ) ? $type : '',
            'state' => in_array( $state, self::$states, true ) ? $state : '',
            'sort' => $sort,
            'dir' => $dir,
        );
    }

    /**
     * The types the cards have, sorted ("" for a package without one is left out).
     */
    static function types( array $cards )
    {
        $types = array();
        foreach ( $cards as $card )
            if ( $card['type'] !== '' )
                $types[$card['type']] = true;
        $types = array_keys( $types );
        sort( $types );
        return $types;
    }

    /**
     * The cards of one repository ('' for all), matching the search (every word), the type and the state.
     *
     * @return array the matching cards, keys kept
     */
    static function filter( array $cards, $repositoryID, array $query )
    {
        $words = array();
        if ( $query['search'] !== '' )
        {
            $lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $query['search'], 'UTF-8' ) : strtolower( $query['search'] );
            $words = preg_split( '/\s+/', $lower, -1, PREG_SPLIT_NO_EMPTY );
        }
        $result = array();
        foreach ( $cards as $key => $card )
        {
            if ( (string)$repositoryID !== '' && $card['repository_id'] !== (string)$repositoryID )
                continue;
            if ( $query['type'] !== '' && $card['type'] !== $query['type'] )
                continue;
            if ( $query['state'] === 'installer' )
            {
                if ( !$card['installer_source'] )
                    continue;
            }
            else if ( $query['state'] !== '' && $card['state'] !== $query['state'] )
                continue;
            $match = true;
            foreach ( $words as $word )
                if ( strpos( $card['search'], $word ) === false )
                    $match = false;
            if ( $match )
                $result[$key] = $card;
        }
        return $result;
    }

    /**
     * The cards in the list's order; ties are broken by name, then repository.
     */
    static function sort( array $cards, $sort, $dir )
    {
        $sign = $dir === 'desc' ? -1 : 1;
        $order = array( 'installed' => 0, 'not_installed' => 1, 'no_items' => 2, 'import' => 3 );
        uasort( $cards, function ( $a, $b ) use ( $sort, $sign, $order )
        {
            switch ( $sort )
            {
                case 'changed': $c = (int)$a['changed'] <=> (int)$b['changed']; break;
                case 'size': $c = $a['bytes'] <=> $b['bytes']; break;
                case 'version': $c = version_compare( $a['version'], $b['version'] ); break;
                case 'type': $c = strcasecmp( $a['type'], $b['type'] ); break;
                case 'repository': $c = strcasecmp( $a['repository_id'], $b['repository_id'] ); break;
                case 'state': $c = $order[$a['state']] <=> $order[$b['state']]; break;
                default: $c = 0;
            }
            $c *= $sign;
            if ( $c === 0 )
                $c = strcasecmp( $a['name'], $b['name'] ) * ( $sort === 'name' ? $sign : 1 );
            if ( $c === 0 )
                $c = strcasecmp( $a['repository_id'], $b['repository_id'] );
            return $c;
        } );
        return $cards;
    }

    /**
     * What a package carries, from its definition: the classes, the content object items, the extensions, the
     * settings files, the other install items by type, its documents, and its files by kind.
     *
     * @param eZPackage $package
     * @param array|null $files eZPackageFileBrowser::allFiles() of it, or null to read them
     * @return array
     */
    static function contents( eZPackage $package, $files = null )
    {
        $classes = array();
        $dependencies = $package->attribute( 'dependencies' );
        if ( is_array( $dependencies ) && isset( $dependencies['provides'] ) && is_array( $dependencies['provides'] ) )
            foreach ( $dependencies['provides'] as $provide )
                if ( isset( $provide['type'] ) && $provide['type'] === 'ezcontentclass' && isset( $provide['value'] ) && $provide['value'] !== '' )
                    $classes[(string)$provide['value']] = true;

        $objectItems = 0;
        $extensions = array();
        $other = array();
        foreach ( (array)$package->attribute( 'install' ) as $item )
        {
            $type = isset( $item['type'] ) ? (string)$item['type'] : '';
            $file = isset( $item['filename'] ) ? (string)$item['filename'] : '';
            if ( $type === 'ezcontentclass' )
            {
                // the item file is class-<identifier>; the provides list above names the same classes
                if ( strpos( $file, 'class-' ) === 0 )
                    $classes[substr( $file, 6 )] = true;
            }
            else if ( $type === 'ezcontentobject' )
                $objectItems++;
            else if ( $type === 'ezextension' )
                $extensions[preg_replace( '/^extension-/', '', $file )] = true;
            else if ( $type !== '' )
                $other[$type] = ( isset( $other[$type] ) ? $other[$type] : 0 ) + 1;
        }
        $classes = array_keys( $classes );
        sort( $classes );
        $extensions = array_keys( $extensions );
        sort( $extensions );
        ksort( $other );

        $settings = array();
        // read directly: attribute() warns for a package that names no settings files (the key is never set then)
        $settingsFiles = isset( $package->Parameters['settings-files'] ) ? $package->Parameters['settings-files'] : array();
        foreach ( is_array( $settingsFiles ) ? $settingsFiles : array() as $settingsFile )
            $settings[] = (string)$settingsFile;

        if ( $files === null )
            $files = eZPackageFileBrowser::allFiles( $package );
        $kinds = array();
        $objectFiles = 0;
        foreach ( $files as $file )
        {
            $kind = $file['kind'] === 'unknown-xml' ? eZPackageFileBrowser::resolvedKind( $package, $file ) : $file['kind'];
            if ( !isset( $kinds[$kind] ) )
                $kinds[$kind] = array( 'files' => 0, 'bytes' => 0 );
            $kinds[$kind]['files']++;
            $kinds[$kind]['bytes'] += (int)$file['size'];
            if ( $kind === 'object' )
                $objectFiles++;
        }
        ksort( $kinds );

        $documents = array();
        foreach ( (array)$package->attribute( 'documents' ) as $document )
            if ( isset( $document['name'] ) )
                $documents[] = (string)$document['name'];

        return array(
            'classes' => $classes,
            'object_items' => $objectItems,
            'object_files' => $objectFiles,
            'extensions' => $extensions,
            'settings' => $settings,
            'other_items' => $other,
            'documents' => $documents,
            'kinds' => $kinds,
            'files' => count( $files ),
        );
    }
}

?>
