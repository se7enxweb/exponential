<?php
/**
 * File containing the expMigrationStepFile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "file": attaches a file of the migrations folder to a file attribute (ezbinaryfile, ezmedia) of an
 * object.
 *
 *   - type: file
 *     object: remote:annual-report      (or node:)
 *     field: file                       the attribute
 *     file: files/annual-report-2026.pdf   next to the migration, below its folder
 *     name: Annual report 2026.pdf      the file name visitors get (default: the file's own)
 *     language: eng-GB
 *
 * The file is read from the folder of the migration file (a path below it; never .., an absolute path or a URL) and
 * stored the way the datatype's fromString() stores an imported file. Like the content step, the change is a new
 * version published through the publish operation ("in_place: true" writes into the published version instead);
 * workflows run only with "workflows: true".
 *
 * A step of this type does not undo itself: the file before it is not known to the step (give the migration a
 * "down:").
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepFile extends expMigrationStepContentBase
{
    /** The datatypes a step of this type writes. */
    const DATATYPES = array( 'ezbinaryfile', 'ezmedia' );

    /** @return string */
    public function name()
    {
        return 'file';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Attaches a file of the migrations folder to a file attribute (ezbinaryfile, ezmedia) of an object',
            'keys' => $this->targetKeys() + array(
                'file' => array( 'type' => 'string', 'required' => true,
                                 'description' => 'the file: a path below the folder of the migration file (files/report.pdf)' ),
                'name' => array( 'type' => 'string', 'description' => 'the file name visitors get (default: the name of the file)' ),
            ) + $this->commonKeys(),
            'values' => $this->valueTexts(),
            'example' => "- type: file\n  object: remote:annual-report\n  field: file\n  file: files/annual-report-2026.pdf\n  name: Annual report 2026.pdf\n",
        );
    }

    /**
     * The keys that name the object and its attribute.
     *
     * @return array
     */
    protected function targetKeys()
    {
        return array(
            'object' => array( 'type' => 'identifier', 'kind' => 'object', 'description' => 'the object (or node:)' ),
            'node' => array( 'type' => 'identifier', 'kind' => 'node', 'description' => 'a node of the object (or object:)' ),
            'field' => array( 'type' => 'string', 'required' => true,
                              'description' => 'the identifier of the attribute (' . implode( ', ', static::DATATYPES ) . ')' ),
        );
    }

    /**
     * The keys of the version: language, in_place, missing, workflows, notify.
     *
     * @return array
     */
    protected function commonKeys()
    {
        return array(
            'language' => array( 'type' => 'string', 'description' => 'the translation that gets the file (default: the default language of content)' ),
            'in_place' => array( 'type' => 'bool', 'default' => false, 'description' => 'true: the published version is changed in place, without a new version' ),
            'missing' => array( 'type' => 'string', 'default' => 'fail', 'values' => array( 'fail', 'skip' ),
                                'description' => 'what happens when the object is not there: fail (the default) or skip' ),
        ) + self::operationKeys();
    }

    /** @return array */
    protected function valueTexts()
    {
        return array(
            'object_id' => 'the id of the object',
            'node_id' => 'the id of its main node',
            'remote_id' => 'its remote id',
            'version' => 'its current version',
            'name' => 'its name',
            'path' => 'the url path of its main node',
            'changed' => 'true when the step changed the object',
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        if ( isset( $step['object'] ) === isset( $step['node'] ) )
            $errors[] = 'name the object by object: or by node: (one of them)';
        if ( isset( $step['field'] ) && !expMigrationReferences::contains( $step['field'] ) && !preg_match( '/^[a-z0-9_]+$/', (string)$step['field'] ) )
            $errors[] = 'field: ' . expMigrationStepSchema::show( $step['field'] ) . ' is not an attribute identifier (lower case letters, digits and _)';
        if ( isset( $step['file'] ) && !expMigrationReferences::contains( $step['file'] ) )
        {
            $problem = self::fileProblem( (string)$step['file'] );
            if ( $problem !== null )
                $errors[] = "file: $problem";
        }
        if ( isset( $step['language'] ) && !expMigrationReferences::contains( $step['language'] ) )
        {
            $problem = expMigrationIdentifier::problem( 'language', $step['language'] );
            if ( $problem !== null )
                $errors[] = "language: $problem";
        }
        return $errors;
    }

    /**
     * The problem of a path of file:, or null: relative, below the migration's folder. Pure.
     *
     * @param string $path
     * @return string|null
     */
    public static function fileProblem( $path )
    {
        $path = trim( $path );
        if ( $path === '' )
            return 'a path below the folder of the migration';
        if ( strpos( $path, "\0" ) !== false || preg_match( '#^[a-z][a-z0-9+.-]*:#i', $path ) )
            return "$path is no path (a URL or a stream is not read)";
        if ( $path[0] === '/' || $path[0] === '\\' || preg_match( '#^[A-Za-z]:#', $path ) )
            return "$path is absolute: name the file by its path below the folder of the migration";
        foreach ( preg_split( '#[/\\\\]+#', $path ) as $part )
            if ( $part === '..' )
                return "$path leaves the folder of the migration (..)";
        return null;
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        return array( 'attach ' . $step['file'] . ' to ' . $step['field'] . ' of ' . $this->target( $step ) . $this->planFlags( $step ) );
    }

    /**
     * The end of a plan line: the language, in place, workflows. Pure.
     *
     * @param array $step
     * @return string
     */
    protected function planFlags( array $step )
    {
        return ( isset( $step['language'] ) ? ' in ' . $step['language'] : '' ) . ( !empty( $step['in_place'] ) ? ', in place' : ', as a new version' )
             . ( !empty( $step['workflows'] ) ? ', with workflows' : '' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $object = self::findObject( $step );
        if ( $object === null )
        {
            if ( !isset( $step['missing'] ) || $step['missing'] !== 'skip' )
                throw new RuntimeException( $this->target( $step ) . ' is not on this installation (missing: skip lets the step pass)' );
            return self::result( array( $this->target( $step ) . ' is not there: skipped (missing: skip)' ),
                                 array( 'object_id' => null, 'node_id' => null, 'remote_id' => null, 'version' => null, 'name' => null, 'path' => null, 'changed' => false ) );
        }
        $file = isset( $step['file'] ) ? self::migrationPath( (string)$step['file'], $context ) : null;
        $lines = array();
        $object = $this->change( $object, $step, function ( eZContentObjectAttribute $attribute ) use ( $step, $file, &$lines ) {
            $lines = $this->write( $attribute, $step, $file );
        } );
        return self::result( $lines, self::objectValues( $object ) + array( 'changed' => true ) );
    }

    /**
     * Writes the step into the attribute of the version.
     *
     * @param eZContentObjectAttribute $attribute
     * @param array $step
     * @param string|null $file the file, resolved
     * @return string[] what was done
     * @throws RuntimeException
     */
    protected function write( eZContentObjectAttribute $attribute, array $step, $file )
    {
        $name = isset( $step['name'] ) && trim( (string)$step['name'] ) !== '' ? basename( trim( (string)$step['name'] ) ) : basename( (string)$file );
        if ( !$attribute->dataType()->fromString( $attribute, $file . '|' . $name ) )
            throw new RuntimeException( 'the file ' . $step['file'] . ' was not stored in ' . $step['field'] );
        $attribute->store();
        $content = $attribute->content();
        if ( !is_object( $content ) || (string)$content->attribute( 'original_filename' ) !== $name )
            throw new RuntimeException( 'the file ' . $step['file'] . ' was not stored in ' . $step['field'] );
        return array( 'attached ' . $step['file'] . ' as "' . $name . '" to ' . $step['field'] );
    }

    /**
     * A file of the migrations folder: file: below the folder of the migration file, checked to be there and to stay
     * below it (no link leads out of it).
     *
     * @param string $path
     * @param expMigrationContext|null $context
     * @return string the real path
     * @throws RuntimeException
     */
    public static function migrationPath( $path, $context )
    {
        $problem = self::fileProblem( $path );
        if ( $problem !== null )
            throw new RuntimeException( "file: $problem" );
        $migration = $context instanceof expMigrationContext ? $context->migrationFile() : null;
        if ( $migration === null || $migration === '' )
            throw new RuntimeException( "file: $path is read next to the migration file, and this step runs outside one" );
        $folder = realpath( dirname( $migration ) );
        $real = $folder !== false ? realpath( $folder . '/' . trim( $path ) ) : false;
        if ( $real === false || !is_file( $real ) || !is_readable( $real ) )
            throw new RuntimeException( "file: $path is not a readable file in " . ( $folder !== false ? $folder : dirname( $migration ) ) );
        if ( strpos( $real, rtrim( $folder, '/' ) . '/' ) !== 0 )
            throw new RuntimeException( "file: $path leads out of the folder of the migration" );
        return $real;
    }

    /**
     * Changes the attribute of a step in a new version (published through the publish operation) or in place.
     *
     * @param eZContentObject $object
     * @param array $step
     * @param callable $write function ( eZContentObjectAttribute $attribute ), throws on failure
     * @return eZContentObject the object afterwards
     * @throws RuntimeException
     */
    protected function change( eZContentObject $object, array $step, callable $write )
    {
        $language = self::locale( isset( $step['language'] ) ? $step['language'] : null );
        $has = in_array( $language, self::locales( $object ), true );
        $id = (int)$object->attribute( 'id' );
        $db = eZDB::instance();
        if ( !empty( $step['in_place'] ) )
        {
            if ( !$has )
                throw new RuntimeException( 'the object ' . $id . " has no translation $language to change in place (in_place: false adds it)" );
            $versionNumber = (int)$object->attribute( 'current_version' );
            $version = $object->version( $versionNumber );
            $db->begin();
            try
            {
                $write( $this->attributeOf( $version, $language, $step ) );
                $version->resetDataMap();
                $object->resetDataMap();
                $object->setName( $object->contentClass()->contentObjectName( $object, $versionNumber, $language ), $versionNumber, $language );
                $object->store();
            }
            catch ( Throwable $e )
            {
                // nothing of a transaction that failed half way is committed by a later commit()
                if ( $db->transactionCounter() > 0 )
                    $db->rollback();
                throw $e;
            }
            $db->commit();
            eZContentCacheManager::clearContentCacheIfNeeded( $id );
            eZContentOperationCollection::registerSearchObject( $id );
            eZContentObject::clearCache( array( $id ) );
            return eZContentObject::fetch( $id );
        }
        $db->begin();
        try
        {
            $version = $object->createNewVersionIn( $language, $has ? false : $object->initialLanguageCode() );
            if ( !$version instanceof eZContentObjectVersion )
            {
                $db->rollback();
                throw new RuntimeException( "no new version of the object $id in $language could be made" );
            }
            $version->setAttribute( 'modified', time() );
            $version->store();
            // a failure leaves the transaction to the run, which rolls the migration back
            $write( $this->attributeOf( $version, $language, $step ) );
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        return self::publish( $id, (int)$version->attribute( 'version' ), $step );
    }

    /**
     * The attribute of field: in a version, of a datatype of DATATYPES.
     *
     * @param eZContentObjectVersion $version
     * @param string $language
     * @param array $step
     * @return eZContentObjectAttribute
     * @throws RuntimeException
     */
    protected function attributeOf( eZContentObjectVersion $version, $language, array $step )
    {
        foreach ( $version->contentObjectAttributes( $language ) as $attribute )
        {
            if ( (string)$attribute->attribute( 'contentclass_attribute_identifier' ) !== (string)$step['field'] )
                continue;
            $datatype = (string)$attribute->attribute( 'data_type_string' );
            if ( !in_array( $datatype, static::DATATYPES, true ) )
                throw new RuntimeException( 'the attribute ' . $step['field'] . " is of the datatype $datatype, not " . implode( ' or ', static::DATATYPES ) );
            return $attribute;
        }
        throw new RuntimeException( 'the class of ' . $this->target( $step ) . ' has no attribute ' . $step['field'] );
    }

    /**
     * @param array $step
     * @return string
     */
    protected function target( array $step )
    {
        return isset( $step['object'] ) ? 'the object ' . self::named( $step['object'] ) : 'the object of node ' . self::named( isset( $step['node'] ) ? $step['node'] : null );
    }
}
