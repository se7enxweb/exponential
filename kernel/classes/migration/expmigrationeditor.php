<?php
/**
 * File containing the expMigrationEditor class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The migration editor of the administration (migration/editor): a YAML migration as text, the form of a step built
 * from its step type's own schema (expMigrationStepType::schema()), the check through the step types
 * (expMigrationYaml::validate(), what exp:migration:check does), a preview of the YAML, and saving into a migrations
 * folder of the installation.
 *
 * The text is the source of truth. A step added or changed in a form is put into the parsed document, which is
 * written again (expMigrationYamlWriter) under the comment lines of the header; other comments in the steps are not
 * kept then, the page says so.
 *
 * Saving: a new migration goes where exp:migration:generate puts one (the site's first DirectoryList folder or the
 * extension's ExtensionDirectory), never over a file; an existing one only when it is a YAML migration in one of the
 * searched migration folders and has not been applied (an applied migration is changed by a new one: the checksum
 * guard would refuse it). Nothing is written outside those folders.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationEditor
{
    /** The largest text the editor takes. */
    const MAX_BYTES = 262144;

    /** The key types that are written as YAML in a text field (a list, a map ...). */
    const STRUCTURED = array( 'list', 'map', 'any', 'condition' );

    /**
     * The step types for the forms: name, description, modes, mode (the default), keys (name, type, required,
     * default (as text), description, values, modes, kind, structured), example, values (reference values). Sorted.
     *
     * @param expMigrationStepRegistry $registry
     * @return array[] name => type
     */
    public static function stepTypes( expMigrationStepRegistry $registry )
    {
        $types = array();
        foreach ( $registry->types() as $name => $type )
        {
            $schema = $type->schema();
            $keys = array();
            foreach ( isset( $schema['keys'] ) ? (array)$schema['keys'] : array() as $key => $definition )
                $keys[] = array( 'name' => $key, 'type' => $definition['type'], 'required' => !empty( $definition['required'] ),
                                 'default' => array_key_exists( 'default', $definition ) ? self::text( $definition['default'] ) : '',
                                 'description' => isset( $definition['description'] ) ? (string)$definition['description'] : '',
                                 'values' => isset( $definition['values'] ) ? array_map( array( __CLASS__, 'text' ), (array)$definition['values'] ) : array(),
                                 'modes' => isset( $definition['modes'] ) ? array_values( (array)$definition['modes'] ) : array(),
                                 'kind' => isset( $definition['kind'] ) ? (string)$definition['kind'] : '',
                                 'structured' => in_array( $definition['type'], self::STRUCTURED, true ) );
            $types[$name] = array( 'name' => $name, 'description' => isset( $schema['description'] ) ? (string)$schema['description'] : '',
                                   'modes' => isset( $schema['modes'] ) ? array_values( (array)$schema['modes'] ) : array(),
                                   'mode' => isset( $schema['mode'] ) ? (string)$schema['mode'] : '',
                                   'keys' => $keys, 'example' => isset( $schema['example'] ) ? (string)$schema['example'] : '',
                                   'values' => array_keys( isset( $schema['values'] ) ? (array)$schema['values'] : array() ) );
        }
        ksort( $types );
        return $types;
    }

    /**
     * A value as the text of a form field: scalars as they are, true/false, lists and maps as YAML in one line. Pure.
     *
     * @param mixed $value
     * @return string
     */
    public static function text( $value )
    {
        if ( is_bool( $value ) )
            return $value ? 'true' : 'false';
        if ( $value === null )
            return '';
        if ( is_array( $value ) )
            return expMigrationYamlWriter::inline( $value );
        return (string)$value;
    }

    /**
     * A step from the fields of its form: each key of the schema from its text (empty: left out), the mode, and the
     * engine's keys (if, references, environments, engines, comment). The step is not checked here (check()).
     *
     * @param string $type the step type's name
     * @param array $schema its schema
     * @param array $fields mode, keys (key => text), if, references, environments, engines, comment (texts)
     * @param string[] $errors problems of the texts (a list that is no YAML ...) are added here
     * @return array the step
     */
    public static function stepFromForm( $type, array $schema, array $fields, array &$errors )
    {
        $step = array( 'type' => (string)$type );
        $mode = isset( $fields['mode'] ) ? trim( (string)$fields['mode'] ) : '';
        if ( $mode !== '' && !empty( $schema['modes'] ) )
            $step['mode'] = $mode;
        $texts = isset( $fields['keys'] ) && is_array( $fields['keys'] ) ? $fields['keys'] : array();
        foreach ( isset( $schema['keys'] ) ? (array)$schema['keys'] : array() as $key => $definition )
        {
            if ( !isset( $texts[$key] ) || trim( (string)$texts[$key] ) === '' )
                continue;
            if ( $mode !== '' && isset( $definition['modes'] ) && !in_array( $mode, (array)$definition['modes'], true ) )
                continue;
            $value = self::value( $definition['type'], (string)$texts[$key], $key, $errors );
            if ( $value !== null )
                $step[$key] = $value;
        }
        if ( isset( $fields['if'] ) && trim( (string)$fields['if'] ) !== '' )
        {
            $value = self::value( 'any', (string)$fields['if'], 'if', $errors );
            if ( $value !== null )
                $step['if'] = $value;
        }
        foreach ( array( 'environments', 'engines' ) as $key )
            if ( isset( $fields[$key] ) && trim( (string)$fields[$key] ) !== '' )
                $step[$key] = array_values( array_filter( array_map( 'trim', explode( ',', (string)$fields[$key] ) ), 'strlen' ) );
        if ( isset( $fields['references'] ) && trim( (string)$fields['references'] ) !== '' )
        {
            $references = array();
            foreach ( array_filter( array_map( 'trim', explode( ',', (string)$fields['references'] ) ), 'strlen' ) as $pair )
            {
                $parts = array_map( 'trim', explode( '=', $pair, 2 ) );
                if ( count( $parts ) !== 2 || $parts[0] === '' || $parts[1] === '' )
                    $errors[] = "references: name=value pairs separated by commas (folder=node_id), not $pair";
                else
                    $references[$parts[0]] = $parts[1];
            }
            if ( $references )
                $step['references'] = $references;
        }
        if ( isset( $fields['comment'] ) && trim( (string)$fields['comment'] ) !== '' )
            $step['comment'] = trim( preg_replace( '/\s+/', ' ', (string)$fields['comment'] ) );
        return $step;
    }

    /**
     * The value of a field's text for a key type: structured types and true/false read as YAML, whole numbers as
     * numbers, the rest as text. Pure but for the YAML library.
     *
     * @param string $type
     * @param string $text
     * @param string $key for the message
     * @param string[] $errors
     * @return mixed null when the text cannot be read
     */
    public static function value( $type, $text, $key, array &$errors )
    {
        $text = (string)$text;
        if ( in_array( $type, self::STRUCTURED, true ) || $type === 'scalar' )
        {
            $problems = array();
            $value = expMigrationYamlParser::parse( $text, $problems );
            if ( $problems )
            {
                $errors[] = "$key: not YAML: " . implode( '; ', $problems );
                return null;
            }
            return $value;
        }
        $trimmed = trim( $text );
        if ( $type === 'bool' )
        {
            if ( in_array( strtolower( $trimmed ), array( 'true', 'yes', '1', 'on' ), true ) )
                return true;
            if ( in_array( strtolower( $trimmed ), array( 'false', 'no', '0', 'off' ), true ) )
                return false;
            $errors[] = "$key: true or false, not $trimmed";
            return null;
        }
        if ( ( $type === 'int' || $type === 'identifier' ) && preg_match( '/^-?\d+$/', $trimmed ) && strlen( $trimmed ) < 19 )
            return (int)$trimmed;
        if ( $type === 'number' && is_numeric( $trimmed ) )
            return $trimmed + 0;
        return strpos( $text, "\n" ) !== false ? str_replace( "\r\n", "\n", $text ) : $trimmed;
    }

    /**
     * The fields of a step's form, from a step (to change a step of the text). Pure.
     *
     * @param array $schema
     * @param array $step
     * @return array mode, keys (key => text), if, references, environments, engines, comment; unknown (keys the
     *               schema does not have, which the form cannot show)
     */
    public static function formFromStep( array $schema, array $step )
    {
        $fields = array( 'mode' => isset( $step['mode'] ) ? (string)$step['mode'] : '', 'keys' => array(), 'if' => '', 'references' => '',
                         'environments' => '', 'engines' => '', 'comment' => isset( $step['comment'] ) ? (string)$step['comment'] : '', 'unknown' => array() );
        $keys = isset( $schema['keys'] ) ? (array)$schema['keys'] : array();
        foreach ( $step as $key => $value )
        {
            if ( $key === 'type' || $key === 'mode' || $key === 'comment' )
                continue;
            if ( $key === 'if' )
                $fields['if'] = self::text( $value );
            else if ( $key === 'environments' || $key === 'engines' )
                $fields[$key] = implode( ', ', array_map( 'strval', (array)$value ) );
            else if ( $key === 'references' && is_array( $value ) )
            {
                $pairs = array();
                foreach ( $value as $name => $of )
                    $pairs[] = "$name=" . self::text( $of );
                $fields['references'] = implode( ', ', $pairs );
            }
            else if ( isset( $keys[$key] ) )
                $fields['keys'][$key] = is_string( $value ) && strpos( $value, "\n" ) !== false ? $value : self::text( $value );
            else
                $fields['unknown'][] = (string)$key;
        }
        return $fields;
    }

    /**
     * The comment lines at the top of a text (the header) and the rest. Pure.
     *
     * @param string $text
     * @return array header (string[] the lines without "# "), body (string)
     */
    public static function split( $text )
    {
        $lines = preg_split( '/\r\n|\r|\n/', (string)$text );
        $header = array();
        while ( $lines && preg_match( '/^\s*#\s?(.*)$/', $lines[0], $m ) )
        {
            $header[] = rtrim( $m[1] );
            array_shift( $lines );
        }
        return array( 'header' => $header, 'body' => implode( "\n", $lines ) );
    }

    /**
     * The document of a text, parsed but not checked (so a step can be added to a text that does not pass yet).
     *
     * @param string $text
     * @param string[] $errors
     * @return array|null steps, down, transactional
     */
    public static function document( $text, array &$errors )
    {
        if ( strlen( (string)$text ) > self::MAX_BYTES )
        {
            $errors[] = 'the migration is larger than ' . self::MAX_BYTES . ' bytes';
            return null;
        }
        $parsed = expMigrationYamlParser::parse( (string)$text, $errors );
        if ( $errors )
            return null;
        return expMigrationYaml::document( $parsed, $errors );
    }

    /**
     * A document written as text under header lines. Pure.
     *
     * @param string[] $header
     * @param array $document steps, down, transactional
     * @return string
     */
    public static function render( array $header, array $document )
    {
        $value = array( 'steps' => isset( $document['steps'] ) ? array_values( $document['steps'] ) : array() );
        if ( isset( $document['down'] ) && $document['down'] !== null )
            $value['down'] = array_values( $document['down'] );
        if ( isset( $document['transactional'] ) && $document['transactional'] === false )
            $value['transactional'] = false;
        // the lists of steps always in block style, one step after the other, even when one would fit in a line
        $out = expMigrationYamlWriter::document( array(), $header );
        $out = substr( $out, 0, -strlen( "[]\n" ) );
        foreach ( $value as $key => $item )
            $out .= is_array( $item ) && $item ? "$key:\n" . expMigrationYamlWriter::block( $item, 4 ) : $key . ': ' . expMigrationYamlWriter::inline( $item ) . "\n";
        return $out;
    }

    /**
     * The text with a step added at the end of steps (or of down), or put in the place of the step at an index.
     *
     * @param string $text
     * @param array $step
     * @param int|null $index null: added at the end
     * @param string $list steps or down
     * @param string[] $errors
     * @return string|null the new text; null when the text cannot be read
     */
    public static function withStep( $text, array $step, $index = null, $list = 'steps', array &$errors = array() )
    {
        $document = self::document( $text, $errors );
        if ( $document === null )
            return null;
        $list = $list === 'down' ? 'down' : 'steps';
        $steps = $document[$list] === null ? array() : $document[$list];
        if ( $index === null )
            $steps[] = $step;
        else if ( isset( $steps[(int)$index] ) )
            $steps[(int)$index] = $step;
        else
        {
            $errors[] = "there is no step $index in $list";
            return null;
        }
        $document[$list] = $steps;
        return self::render( self::split( $text )['header'], $document );
    }

    /**
     * The text without the step at an index, or with it moved one place up or down.
     *
     * @param string $text
     * @param int $index
     * @param string $action remove, up, down
     * @param string $list steps or down
     * @param string[] $errors
     * @return string|null
     */
    public static function changeStep( $text, $index, $action, $list = 'steps', array &$errors = array() )
    {
        $document = self::document( $text, $errors );
        if ( $document === null )
            return null;
        $list = $list === 'down' ? 'down' : 'steps';
        $steps = $document[$list] === null ? array() : $document[$list];
        $index = (int)$index;
        if ( !isset( $steps[$index] ) )
        {
            $errors[] = "there is no step $index in $list";
            return null;
        }
        if ( $action === 'remove' )
            array_splice( $steps, $index, 1 );
        else if ( $action === 'up' && $index > 0 )
            list( $steps[$index - 1], $steps[$index] ) = array( $steps[$index], $steps[$index - 1] );
        else if ( $action === 'down' && $index < count( $steps ) - 1 )
            list( $steps[$index + 1], $steps[$index] ) = array( $steps[$index], $steps[$index + 1] );
        $document[$list] = $steps;
        return self::render( self::split( $text )['header'], $document );
    }

    /**
     * The check of a text, as exp:migration:check checks a YAML file: the header, the document and every step
     * through its step type.
     *
     * @param string $text
     * @param expMigrationStepRegistry $registry
     * @return array ok, errors, warnings, header (the parsed header), steps (array[] index, list, type, mode,
     *               summary), document (or null)
     */
    public static function check( $text, expMigrationStepRegistry $registry )
    {
        $result = array( 'ok' => false, 'errors' => array(), 'warnings' => array(), 'header' => array(), 'steps' => array(), 'document' => null );
        $headerErrors = array();
        $result['header'] = expMigrationDefinition::parseHeader( substr( (string)$text, 0, expMigrationDefinition::HEADER_BYTES ), $headerErrors );
        foreach ( $headerErrors as $error )
            $result['errors'][] = 'header: ' . $error;
        if ( empty( $result['header']['description'] ) )
            $result['warnings'][] = 'the header has no description (# description: ...)';
        $document = self::document( $text, $result['errors'] );
        if ( $document !== null )
        {
            $result['document'] = $document;
            foreach ( expMigrationYaml::validate( $document, $registry, $result['warnings'] ) as $error )
                $result['errors'][] = $error;
            foreach ( array( 'steps', 'down' ) as $list )
                foreach ( $document[$list] === null ? array() : $document[$list] as $i => $step )
                    $result['steps'][] = array( 'index' => $i, 'list' => $list,
                                                'type' => is_array( $step ) && isset( $step['type'] ) ? (string)$step['type'] : ( is_array( $step ) && isset( $step['foreach'] ) ? 'foreach' : ( is_array( $step ) && isset( $step['while'] ) ? 'while' : '' ) ),
                                                'mode' => is_array( $step ) && isset( $step['mode'] ) && is_string( $step['mode'] ) ? $step['mode'] : '',
                                                'summary' => is_array( $step ) ? self::summary( $step ) : '',
                                                'form' => is_array( $step ) && isset( $step['type'] ) && $registry->get( (string)$step['type'] ) !== null );
        }
        $result['ok'] = !$result['errors'];
        return $result;
    }

    /**
     * One line about a step: its keys and short values. Pure.
     *
     * @param array $step
     * @return string
     */
    public static function summary( array $step )
    {
        $parts = array();
        foreach ( $step as $key => $value )
        {
            if ( $key === 'type' || $key === 'mode' )
                continue;
            $parts[] = $key . ': ' . expMigrationStepSchema::show( $value );
        }
        $line = implode( ', ', $parts );
        return mb_strlen( $line ) > 160 ? mb_substr( $line, 0, 157 ) . '...' : $line;
    }

    /**
     * The YAML migrations the editor can open: id, source, path (relative to the root), applied (bool), editable.
     *
     * @param expMigrationLocator $locator
     * @param array $applied source/id => true (from exp_migration)
     * @param string $root
     * @return array[]
     */
    public static function files( expMigrationLocator $locator, array $applied, $root )
    {
        $files = array();
        foreach ( $locator->find() as $definition )
        {
            if ( $definition->type() !== 'yml' || !$definition->file() )
                continue;
            $key = $definition->source() . '/' . $definition->id();
            $files[] = array( 'id' => $definition->id(), 'source' => $definition->source(), 'description' => (string)$definition->description(),
                              'path' => expMigrationCheck::relative( $definition->file(), $root ), 'applied' => isset( $applied[$key] ),
                              'editable' => !isset( $applied[$key] ) && is_writable( $definition->file() ) );
        }
        usort( $files, function ( $a, $b ) { return strcmp( $b['id'], $a['id'] ); } );
        return $files;
    }

    /**
     * The file of a YAML migration by its id, when it is in a searched migrations folder: array( definition, file ),
     * or null.
     *
     * @param expMigrationLocator $locator
     * @param string $id
     * @param string $source '' for any
     * @return array|null
     */
    public static function find( expMigrationLocator $locator, $id, $source = '' )
    {
        if ( !preg_match( '/^\d{14}_[A-Za-z0-9][A-Za-z0-9_-]*$/', (string)$id ) )
            return null;
        $folders = array();
        foreach ( $locator->searchedDirectories() as $directory )
        {
            $real = realpath( $directory['path'] );
            if ( $real !== false )
                $folders[] = rtrim( $real, '/' ) . '/';
        }
        foreach ( $locator->find() as $definition )
        {
            if ( $definition->id() !== $id || ( $source !== '' && $definition->source() !== $source ) || $definition->type() !== 'yml' )
                continue;
            $file = realpath( (string)$definition->file() );
            if ( $file === false || !self::inside( $file, $folders ) )
                return null;
            return array( 'definition' => $definition, 'file' => $file );
        }
        return null;
    }

    /** @return bool a real path inside one of the folders (each ending in /). Pure. */
    public static function inside( $file, array $folders )
    {
        foreach ( $folders as $folder )
            if ( strpos( (string)$file, $folder ) === 0 && substr_count( substr( (string)$file, strlen( $folder ) ), '/' ) === 0 )
                return true;
        return false;
    }

    /**
     * Saves a text: a new migration (name given) through expMigrationGenerator, or an existing file (file given). The
     * text must pass check() first.
     *
     * @param string $text
     * @param array $options name, source (site or extension:<name>), directory (instead of the source's), file (an existing file, from find()), applied
     *                       (bool: the existing migration has run), registry, root, time, ini
     * @return array ok, errors, path (relative to the root), id
     */
    public static function save( $text, array $options )
    {
        $result = array( 'ok' => false, 'errors' => array(), 'path' => '', 'id' => '' );
        $registry = isset( $options['registry'] ) ? $options['registry'] : expMigrationStepRegistry::fromSettings();
        $check = self::check( $text, $registry );
        if ( !$check['ok'] )
        {
            $result['errors'] = $check['errors'];
            return $result;
        }
        $text = rtrim( str_replace( "\r\n", "\n", (string)$text ) ) . "\n";
        $root = rtrim( isset( $options['root'] ) && $options['root'] !== '' ? (string)$options['root'] : (string)eZSys::rootDir(), '/' );
        if ( !empty( $options['file'] ) )
        {
            if ( !empty( $options['applied'] ) )
            {
                $result['errors'][] = 'the migration has run already: change it with a new migration (the checksum guard refuses a changed file)';
                return $result;
            }
            $file = (string)$options['file'];
            $result['path'] = expMigrationCheck::relative( $file, $root );
            $temporary = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $temporary, $text ) !== strlen( $text ) || !@rename( $temporary, $file ) )
            {
                if ( file_exists( $temporary ) )
                    @unlink( $temporary );
                $result['errors'][] = $result['path'] . ' cannot be written';
                return $result;
            }
            $result['ok'] = true;
            return $result;
        }
        $skeleton = expMigrationGenerator::skeleton( isset( $options['name'] ) ? (string)$options['name'] : '', array( 'type' => 'yml', 'root' => $root )
                                                     + array_intersect_key( $options, array_flip( array( 'source', 'directory', 'time', 'ini' ) ) ) );
        if ( $skeleton['errors'] )
        {
            $result['errors'] = $skeleton['errors'];
            return $result;
        }
        $skeleton['content'] = $text;
        $errors = expMigrationGenerator::write( $skeleton );
        $result['errors'] = $errors;
        $result['ok'] = !$errors;
        $result['path'] = $skeleton['path'];
        $result['id'] = $skeleton['id'];
        return $result;
    }

    /**
     * The text of a new migration: the header and no steps. Pure.
     *
     * @param string $description
     * @param string $author
     * @return string
     */
    public static function newText( $description, $author = '' )
    {
        $header = array( 'description: ' . ( trim( (string)$description ) !== '' ? trim( preg_replace( '/\s+/', ' ', (string)$description ) ) : 'A new migration' ) );
        if ( trim( (string)$author ) !== '' )
            $header[] = 'author: ' . trim( preg_replace( '/\s+/', ' ', (string)$author ) );
        return self::render( $header, array( 'steps' => array(), 'down' => null, 'transactional' => true ) );
    }
}
