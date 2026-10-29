<?php
/**
 * The help classes as a content class package (doc/eng-us/specifications/6.0/help-content-package.md, CJW #16564):
 * the eight help_* classes of expHelpContentSchema in the class group "Help", as one package "exp_help_classes" of
 * the type contentclass, exported as an .ezpkg that Setup > Packages and the setup wizard install.
 *
 * - parameters(), provides(), fileName(): the package's definition, from the schema alone (no database);
 * - build(): writes the package directory and the .ezpkg from the classes of this installation (they must exist:
 *   ./console exp:help:content classes first). The class XML is the kernel's own (eZContentClassPackageHandler::
 *   classDOMTree()), so the package installs exactly as any other class package;
 * - verifyDirectory(), verifyArchive(): compare a built package with the schema (no database): every class with
 *   its group and every attribute with its datatype and required flag.
 *
 * Not in the package, on purpose: the sections, the object state group, the folder tree and the roles. They hold
 * ids of the installation and are made, idempotently, by ./console exp:help:content install, which also completes
 * classes a package installed.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentPackage
{
    /** The package name (the directory, the provides of the setup wizard) */
    const NAME = 'exp_help_classes';

    /** The vendor, the repository directory of var/storage/packages/ the package belongs in */
    const VENDOR = '7x';

    const SUMMARY = 'Help maintained as content: the eight help_* content classes';

    const DESCRIPTION = 'The content classes of the help system (DITA 1.3 topic types): help_manual, help_section, help_concept, help_task, help_reference, help_troubleshooting, help_screenshot and help_glossary_entry, in the class group Help. Sections, object states, the folder tree and the roles are made by ./console exp:help:content install, not by the package.';

    /**
     * The release of the package: the Exponential version it is built from, split as the 7x packages are
     * ("6.0" and "15" for 6.0.15), or the version given.
     *
     * @param string $version MAJOR.MINOR.PATCH (default: this installation's version)
     * @return array array( 'number' => '6.0', 'release' => '15', 'version' => '6.0.15' )
     */
    public static function release( $version = '' )
    {
        $version = trim( (string)$version );
        if ( $version === '' && class_exists( 'ExponentialSDK' ) )
            $version = ExponentialSDK::version( true, false, false );
        if ( !preg_match( '/^(\d+)\.(\d+)(?:\.(\d+))?/', $version, $m ) )
            throw new InvalidArgumentException( 'The package version is MAJOR.MINOR.PATCH, such as 6.0.15' );
        $patch = isset( $m[3] ) && $m[3] !== '' ? $m[3] : '0';
        return array( 'number' => $m[1] . '.' . $m[2], 'release' => $patch, 'version' => $m[1] . '.' . $m[2] . '.' . $patch );
    }

    /**
     * The class identifiers the package provides, in the order they are installed (topics before the maps that
     * name them in their relation lists).
     *
     * @return string[]
     */
    public static function provides()
    {
        return array_keys( expHelpContentSchema::classes() );
    }

    /**
     * The package parameters for eZPackage::create(), without the classes.
     *
     * @param string $version
     * @return array
     */
    public static function parameters( $version = '' )
    {
        $release = self::release( $version );
        return array(
            'summary' => self::SUMMARY,
            'description' => self::DESCRIPTION,
            'vendor' => self::VENDOR,
            'type' => 'contentclass',
            'install_type' => 'install',
            'version-number' => $release['number'],
            'release-number' => $release['release'],
            'licence' => 'GPL',
            'state' => 'stable',
        );
    }

    /**
     * The file name of the archive.
     *
     * @param string $version
     * @return string exp_help_classes-6.0.15.ezpkg
     */
    public static function fileName( $version = '' )
    {
        $release = self::release( $version );
        return self::NAME . '-' . $release['version'] . '.ezpkg';
    }

    /**
     * Writes the package from this installation's classes: <dir>/exp_help_classes/ (package.xml, ezcontentclass/)
     * and <dir>/exp_help_classes-<version>.ezpkg. Needs the database; the classes must exist.
     *
     * @param string $dir the output directory
     * @param string $version
     * @param array $maintainer array( name, email ) for the package's maintainer and change log, or empty
     * @return array array( 'directory', 'archive', 'classes' => identifiers, 'version' )
     * @throws RuntimeException when a class is missing or the archive cannot be written
     */
    public static function build( $dir, $version = '', array $maintainer = array() )
    {
        $dir = rtrim( (string)$dir, '/' );
        $release = self::release( $version );
        $classes = array();
        $missing = array();
        foreach ( self::provides() as $identifier )
        {
            $class = eZContentClass::fetchByIdentifier( $identifier, true, eZContentClass::VERSION_STATUS_DEFINED );
            if ( $class instanceof eZContentClass )
                $classes[$identifier] = $class;
            else
                $missing[] = $identifier;
        }
        if ( $missing )
            throw new RuntimeException( 'Missing classes ' . implode( ', ', $missing ) . ': run ./console exp:help:content classes --apply -s <siteaccess> first' );

        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) )
            throw new RuntimeException( "Cannot create the directory $dir" );
        $packageDir = $dir . '/' . self::NAME;
        if ( file_exists( $packageDir ) )
            eZDir::recursiveDelete( $packageDir );

        $parameters = self::parameters( $release['version'] );
        $package = eZPackage::create( self::NAME, array( 'summary' => $parameters['summary'],
                                                         'description' => $parameters['description'],
                                                         'vendor' => $parameters['vendor'],
                                                         'type' => $parameters['type'] ), $dir, 'local' );
        $package->setRelease( $parameters['version-number'], $parameters['release-number'], time(),
                              $parameters['licence'], $parameters['state'] );
        $package->setAttribute( 'install_type', $parameters['install_type'] );
        if ( isset( $maintainer[0], $maintainer[1] ) )
        {
            $package->appendMaintainer( (string)$maintainer[0], (string)$maintainer[1], 'lead' );
            $package->appendChange( (string)$maintainer[0], (string)$maintainer[1],
                                    'The help classes of Exponential ' . $release['version'] );
        }
        foreach ( $classes as $identifier => $class )
            eZContentClassPackageHandler::addClass( $package, $class->attribute( 'id' ), $identifier );
        $package->store();

        $archive = $dir . '/' . self::fileName( $release['version'] );
        if ( file_exists( $archive ) )
            unlink( $archive );
        $written = $package->exportToArchive( $archive, 'ezpkg' );
        if ( !$written || !is_file( $archive ) )
            throw new RuntimeException( "Cannot write $archive" );
        // the kernel's parsed copy of package.xml is not part of the package
        if ( is_dir( $packageDir . '/' . $package->cacheDirectory() ) )
            eZDir::recursiveDelete( $packageDir . '/' . $package->cacheDirectory() );
        return array( 'directory' => $packageDir, 'archive' => $archive, 'classes' => array_keys( $classes ),
                      'version' => $release['version'] );
    }

    /**
     * Compares a package directory (package.xml and ezcontentclass/class-<identifier>.xml) with the schema.
     *
     * @param string $dir
     * @return string[] the differences; empty when the package matches
     */
    public static function verifyDirectory( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        $read = function ( $relative ) use ( $dir ) {
            $file = $dir . '/' . $relative;
            return is_file( $file ) ? (string)file_get_contents( $file ) : null;
        };
        return self::verify( $read );
    }

    /**
     * Compares an .ezpkg (a gzip compressed tar file) with the schema, without unpacking it to disk.
     *
     * @param string $file
     * @return string[] the differences; empty when the package matches
     */
    public static function verifyArchive( $file )
    {
        if ( !is_file( $file ) )
            return array( "No file $file" );
        $entries = self::readArchive( $file );
        if ( $entries === null )
            return array( 'Not a package archive (a gzip compressed tar file)' );
        $files = array();
        foreach ( $entries as $path => $content )
        {
            // an export puts the files below a directory named after the package, or at the top
            $files[preg_replace( '#^(\./)?(' . preg_quote( self::NAME, '#' ) . '/)?#', '', $path )] = $content;
        }
        $read = function ( $relative ) use ( $files ) {
            return isset( $files[$relative] ) ? $files[$relative] : null;
        };
        return self::verify( $read );
    }

    /** The largest archive verifyArchive() reads, unpacked */
    const MAX_ARCHIVE_SIZE = 33554432;

    /**
     * The regular files of a gzip compressed tar file (ustar, with GNU long names and pax paths), read in PHP: the
     * phar stream wrapper is switched off in Exponential (expPhar), and no program is started.
     *
     * @param string $file
     * @return array|null path => contents; null when the file is no such archive
     */
    public static function readArchive( $file )
    {
        $data = @file_get_contents( (string)$file, false, null, 0, self::MAX_ARCHIVE_SIZE );
        if ( !is_string( $data ) || strlen( $data ) < 2 )
            return null;
        if ( substr( $data, 0, 2 ) === "\x1f\x8b" )
        {
            $data = @gzdecode( $data, self::MAX_ARCHIVE_SIZE );
            if ( !is_string( $data ) )
                return null;
        }
        $files = array();
        $length = strlen( $data );
        $offset = 0;
        $longName = null;
        while ( $offset + 512 <= $length )
        {
            $header = substr( $data, $offset, 512 );
            if ( trim( $header, "\0" ) === '' )
                break;
            if ( substr( $header, 257, 5 ) !== 'ustar' && $offset === 0 )
                return null;
            $size = octdec( trim( substr( $header, 124, 12 ), " \0" ) );
            $type = $header[156];
            $name = rtrim( substr( $header, 0, 100 ), "\0" );
            $prefix = rtrim( substr( $header, 345, 155 ), "\0" );
            if ( $prefix !== '' )
                $name = $prefix . '/' . $name;
            $body = substr( $data, $offset + 512, $size );
            $offset += 512 + (int)ceil( $size / 512 ) * 512;
            if ( $type === 'L' )
            {
                $longName = rtrim( $body, "\0" );
                continue;
            }
            if ( $type === 'x' )
            {
                if ( preg_match( '/^\d+ path=(.*)$/m', $body, $m ) )
                    $longName = $m[1];
                continue;
            }
            if ( $longName !== null )
            {
                $name = $longName;
                $longName = null;
            }
            if ( $type === '0' || $type === "\0" )
                $files[$name] = $body;
        }
        return $files;
    }

    /**
     * @param callable $read relative path => contents or null
     * @return string[]
     */
    protected static function verify( $read )
    {
        $problems = array();
        $xml = $read( 'package.xml' );
        if ( $xml === null )
            return array( 'No package.xml' );
        $package = self::load( $xml );
        if ( $package === null )
            return array( 'package.xml is not well-formed XML' );
        $root = $package->documentElement;
        $text = function ( DOMElement $parent, $name ) {
            foreach ( $parent->childNodes as $child )
                if ( $child instanceof DOMElement && $child->localName === $name )
                    return trim( $child->textContent );
            return '';
        };
        if ( $text( $root, 'name' ) !== self::NAME )
            $problems[] = 'The package name is "' . $text( $root, 'name' ) . '", not ' . self::NAME;
        $type = $root->getElementsByTagName( 'type' )->item( 0 );
        if ( !$type instanceof DOMElement || $type->getAttribute( 'value' ) !== 'contentclass' )
            $problems[] = 'The package type is not contentclass';

        $provided = array();
        foreach ( $root->getElementsByTagName( 'provide' ) as $provide )
            if ( $provide->getAttribute( 'type' ) === 'ezcontentclass' )
                $provided[] = $provide->getAttribute( 'value' );
        $items = array();
        foreach ( $root->getElementsByTagName( 'item' ) as $item )
            if ( $item->getAttribute( 'type' ) === 'ezcontentclass' )
                $items[$item->getAttribute( 'filename' )] = $item->getAttribute( 'sub-directory' );

        $schema = expHelpContentSchema::classes();
        foreach ( $schema as $identifier => $definition )
        {
            if ( !in_array( $identifier, $provided, true ) )
                $problems[] = "package.xml does not provide the class $identifier";
            $itemName = 'class-' . $identifier;
            if ( !isset( $items[$itemName] ) )
            {
                $problems[] = "package.xml has no install item $itemName";
                continue;
            }
            $classXml = $read( $items[$itemName] . '/' . $itemName . '.xml' );
            if ( $classXml === null )
            {
                $problems[] = "No file {$items[$itemName]}/$itemName.xml";
                continue;
            }
            $problems = array_merge( $problems, self::verifyClass( $identifier, $definition, $classXml ) );
        }
        foreach ( array_diff( $provided, array_keys( $schema ) ) as $extra )
            $problems[] = "package.xml provides the class $extra, which is not a help class";
        return $problems;
    }

    /**
     * Compares one class file with its definition in the schema.
     *
     * @param string $identifier
     * @param array $definition expHelpContentSchema::classes() entry
     * @param string $xml the class file
     * @return string[]
     */
    public static function verifyClass( $identifier, array $definition, $xml )
    {
        $problems = array();
        $doc = self::load( $xml );
        if ( $doc === null )
            return array( "class-$identifier.xml is not well-formed XML" );
        $class = $doc->documentElement;
        $own = null;
        foreach ( $class->childNodes as $child )
            if ( $child instanceof DOMElement && $child->localName === 'identifier' )
                $own = trim( $child->textContent );
        if ( $own !== $identifier )
            $problems[] = "class-$identifier.xml defines the class \"$own\"";
        if ( ( $class->getAttribute( 'is-container' ) === 'true' ) !== (bool)$definition['container'] )
            $problems[] = "$identifier: is-container should be " . ( $definition['container'] ? 'true' : 'false' );

        $groups = array();
        foreach ( $class->getElementsByTagName( 'group' ) as $group )
            $groups[] = $group->getAttribute( 'name' );
        if ( !in_array( expHelpContentSchema::CLASS_GROUP, $groups, true ) )
            $problems[] = "$identifier: not in the class group " . expHelpContentSchema::CLASS_GROUP;

        $attributes = array();
        foreach ( $class->getElementsByTagName( 'attribute' ) as $attribute )
        {
            $name = '';
            foreach ( $attribute->childNodes as $child )
                if ( $child instanceof DOMElement && $child->localName === 'identifier' )
                    $name = trim( $child->textContent );
            $attributes[$name] = array( 'type' => $attribute->getAttribute( 'datatype' ),
                                        'required' => $attribute->getAttribute( 'required' ) === 'true' );
        }
        foreach ( $definition['attributes'] as $name => $attribute )
        {
            if ( !isset( $attributes[$name] ) )
            {
                $problems[] = "$identifier: no attribute $name";
                continue;
            }
            if ( $attributes[$name]['type'] !== $attribute['type'] )
                $problems[] = "$identifier/$name: datatype {$attributes[$name]['type']}, the schema has {$attribute['type']}";
            if ( $attributes[$name]['required'] !== (bool)$attribute['required'] )
                $problems[] = "$identifier/$name: required should be " . ( $attribute['required'] ? 'true' : 'false' );
        }
        foreach ( array_diff( array_keys( $attributes ), array_keys( $definition['attributes'] ) ) as $extra )
            $problems[] = "$identifier: attribute $extra is not in the schema";
        return $problems;
    }

    /**
     * @param string $xml
     * @return DOMDocument|null
     */
    protected static function load( $xml )
    {
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors( true );
        $ok = $doc->loadXML( (string)$xml, LIBXML_NONET );
        libxml_clear_errors();
        libxml_use_internal_errors( $previous );
        return $ok && $doc->documentElement ? $doc : null;
    }
}
