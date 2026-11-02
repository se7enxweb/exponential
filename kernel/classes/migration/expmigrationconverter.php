<?php
/**
 * File containing the expMigrationConverter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Converts YAML migrations of the migration bundle of the Symfony based platform (a list of steps with type, mode,
 * match, attributes and references, the "reference bundle" of the migrations plan) into the YAML migrations of
 * Exponential (exp:migration:convert). One way, step by step; no code of the bundle is used, only its documented file
 * format is read.
 *
 * What converts: content (create, update, delete), content_type (create, update, delete), content_type_group,
 * location (create, update: priority, sorting, hiding and moving, delete), role (create, update, delete, assign and
 * unassign), section, user, user_group, language, tag, sql, reference (set) and trash (purge). Placeholders
 * "reference:<name>" and "[reference:<name>]" become ${ref.<name>}; the references a step sets keep their names.
 *
 * What does not convert (PHP and service calls, HTTP, mail, processes, loops, files, migration definitions, keys the
 * kernel's step types have no counterpart for) is never guessed: it goes into the list of what to do by hand, which
 * exp:migration:convert writes at the top of the new file. Every converted step is checked against the step types
 * (expMigrationYaml::validate()); a step that does not pass goes into that list too, with the reason. convert() is
 * pure; run() reads the files and, with apply, writes the new migrations.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationConverter
{
    /** The format the converter reads. */
    const FORMAT = 'reference-bundle-yaml';

    /** The step types of the bundle that convert, with their modes. */
    const CONVERTS = array(
        'content' => array( 'create', 'update', 'delete' ),
        'content_type' => array( 'create', 'update', 'delete' ),
        'content_type_group' => array( 'create', 'update', 'delete' ),
        'location' => array( 'create', 'update', 'delete' ),
        'role' => array( 'create', 'update', 'delete' ),
        'section' => array( 'create', 'update', 'delete' ),
        'user' => array( 'create', 'update', 'delete' ),
        'user_group' => array( 'create', 'update', 'delete' ),
        'language' => array( 'create', 'delete' ),
        'tag' => array( 'create', 'update', 'delete' ),
        'sql' => array( 'exec', '' ),
        'reference' => array( 'set' ),
        'trash' => array( 'purge' ),
    );

    /** The names of the reference values of the bundle and the value of the kernel's step that stands for them. */
    const VALUE_NAMES = array(
        'content_id' => 'object_id', 'object_id' => 'object_id', 'location_id' => 'node_id', 'main_location_id' => 'node_id',
        'content_remote_id' => 'remote_id', 'remote_id' => 'remote_id', 'location_remote_id' => 'remote_id', 'name' => 'name',
        'path' => 'path', 'parent_location_id' => 'parent_node_id', 'identifier' => 'identifier', 'content_type_id' => 'class_id',
        'content_type_identifier' => 'identifier', 'section_id' => 'section_id', 'section_identifier' => 'identifier',
        'role_id' => 'role_id', 'role_identifier' => 'name', 'login' => 'login', 'email' => 'email', 'user_id' => 'object_id',
        'user_group_id' => 'object_id', 'language_id' => 'language_id', 'language_code' => 'locale', 'tag_id' => 'tag_id',
        'keyword' => 'keyword', 'content_type_group_id' => 'group_id', 'version' => 'version', 'version_no' => 'version',
        'is_hidden' => 'hidden',
    );

    /**
     * Converts a file or the YAML files of a folder (*.yml, *.yaml, by name) into new migrations of the site or an
     * extension (exp:migration:convert). The new file keeps the date and time at the start of the old name (the
     * order stays), or takes the time of now (one second apart per file); an existing file is never replaced.
     *
     * @param array $options path (a file or folder), apply (write the files), source, directory (instead of the
     *                       source's), author, root, registry, time
     *                       (the Unix time for names without one), ini
     * @return array mode, files (array[]: from, id, path, converted, skipped, manual, errors, written, content),
     *               converted, skipped, errors, warnings
     */
    public static function run( array $options )
    {
        $apply = !empty( $options['apply'] );
        $report = array( 'mode' => $apply ? 'convert' : 'convert-dry-run', 'format' => self::FORMAT, 'files' => array(), 'converted' => 0, 'skipped' => 0,
                         'errors' => array(), 'warnings' => array() );
        $root = rtrim( isset( $options['root'] ) && $options['root'] !== '' ? (string)$options['root'] : (string)eZSys::rootDir(), '/' );
        $path = isset( $options['path'] ) ? (string)$options['path'] : '';
        if ( $path === '' )
        {
            $report['errors'][] = 'name the file or folder to convert';
            return $report;
        }
        $absolute = $path[0] === '/' ? $path : "$root/$path";
        $sources = array();
        if ( is_dir( $absolute ) )
        {
            foreach ( (array)scandir( $absolute ) as $name )
                if ( preg_match( '/\.ya?ml$/', (string)$name ) && is_file( "$absolute/$name" ) )
                    $sources[] = "$absolute/$name";
            if ( !$sources )
                $report['errors'][] = "$path has no .yml or .yaml file";
        }
        else if ( is_file( $absolute ) )
            $sources[] = $absolute;
        else
            $report['errors'][] = "$path is neither a file nor a folder";
        $registry = isset( $options['registry'] ) ? $options['registry'] : expMigrationStepRegistry::fromSettings();
        $now = isset( $options['time'] ) ? (int)$options['time'] : time();
        foreach ( $sources as $n => $file )
        {
            $base = basename( $file );
            $entry = array( 'from' => expMigrationCheck::relative( $file, $root ), 'id' => '', 'path' => '', 'converted' => 0, 'skipped' => 0,
                            'manual' => array(), 'errors' => array(), 'written' => false, 'content' => '' );
            $text = @file_get_contents( $file );
            $converted = $text === false ? array( 'steps' => array(), 'manual' => array(), 'errors' => array( 'cannot be read' ), 'converted' => 0, 'skipped' => 0 )
                                         : self::convert( $text, $registry );
            foreach ( array( 'manual', 'errors', 'converted', 'skipped' ) as $key )
                $entry[$key] = $converted[$key];
            $time = $now + $n;
            $name = preg_replace( '/\.ya?ml$/', '', $base );
            if ( preg_match( '/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})_?(.*)$/', $name, $m ) && checkdate( (int)$m[2], (int)$m[3], (int)$m[1] ) )
            {
                $time = mktime( (int)$m[4], (int)$m[5], (int)$m[6], (int)$m[2], (int)$m[3], (int)$m[1] );
                $name = $m[7];
            }
            if ( !$entry['errors'] && !$converted['steps'] )
                $entry['errors'][] = 'no step converts: every step is in the list of what to do by hand';
            if ( !$entry['errors'] )
            {
                $skeleton = expMigrationGenerator::skeleton( $name !== '' ? $name : 'converted', array( 'type' => 'yml', 'time' => $time, 'root' => $root )
                                                             + array_intersect_key( $options, array_flip( array( 'source', 'directory', 'ini' ) ) ) );
                $entry['errors'] = $skeleton['errors'];
                $entry['id'] = $skeleton['id'];
                $entry['path'] = $skeleton['path'];
                $comments = array( 'description: Converted from ' . $base );
                if ( isset( $options['author'] ) && trim( (string)$options['author'] ) !== '' )
                    $comments[] = 'author: ' . trim( preg_replace( '/\s+/', ' ', (string)$options['author'] ) );
                $comments[] = '';
                $comments[] = 'Converted by exp:migration:convert. Check it before the run: ./console exp:migration:check and ./console exp:migration:dry-run';
                if ( $entry['manual'] )
                {
                    $comments[] = '';
                    $comments[] = 'Not converted, to be done by hand:';
                    foreach ( $entry['manual'] as $line )
                        $comments[] = '- ' . $line;
                }
                $skeleton['content'] = expMigrationEditor::render( $comments, array( 'steps' => $converted['steps'] ) );
                $entry['content'] = $skeleton['content'];
                if ( !$entry['errors'] && $apply )
                {
                    $entry['errors'] = expMigrationGenerator::write( $skeleton );
                    $entry['written'] = !$entry['errors'];
                }
            }
            $report['converted'] += $entry['converted'];
            $report['skipped'] += $entry['skipped'];
            foreach ( $entry['errors'] as $error )
                $report['errors'][] = $entry['from'] . ': ' . $error;
            $report['files'][] = $entry;
        }
        return $report;
    }

    /**
     * Converts the text of a file of the bundle. Pure but for the step types' schemas (the registry).
     *
     * @param string $text
     * @param expMigrationStepRegistry $registry
     * @return array steps (array[], the kernel's), manual (string[]: what to do by hand), errors (string[]: the file
     *               could not be read), converted (int), skipped (int)
     */
    public static function convert( $text, expMigrationStepRegistry $registry )
    {
        $report = array( 'steps' => array(), 'manual' => array(), 'errors' => array(), 'converted' => 0, 'skipped' => 0 );
        $parsed = expMigrationYamlParser::parse( $text, $report['errors'] );
        if ( $report['errors'] )
            return $report;
        if ( $parsed === null )
        {
            $report['errors'][] = 'the file is empty';
            return $report;
        }
        if ( !is_array( $parsed ) || !expMigrationYaml::isList( $parsed ) )
        {
            $report['errors'][] = 'not a migration of the reference bundle: the file is a list of steps, each with a type';
            return $report;
        }
        foreach ( $parsed as $i => $step )
        {
            $label = 'step ' . ( $i + 1 );
            if ( !is_array( $step ) || !isset( $step['type'] ) || !is_string( $step['type'] ) )
            {
                $report['manual'][] = "$label: not a step (no type)";
                $report['skipped']++;
                continue;
            }
            $manual = array();
            $made = self::convertStep( self::placeholders( $step ), $manual );
            $label .= ' (' . $step['type'] . ( isset( $step['mode'] ) && is_string( $step['mode'] ) ? '/' . $step['mode'] : '' ) . ')';
            foreach ( $manual as $line )
                $report['manual'][] = "$label: $line";
            if ( !$made )
            {
                $report['skipped']++;
                continue;
            }
            foreach ( $made as $new )
            {
                $warnings = array();
                $problems = expMigrationYaml::validate( array( 'steps' => array( $new ), 'down' => null, 'transactional' => true ), $registry, $warnings );
                if ( $problems )
                {
                    $report['manual'][] = "$label: converted, but the step does not pass the check, so it is left out: " . implode( '; ', $problems );
                    $report['skipped']++;
                    continue;
                }
                $report['steps'][] = $new;
                $report['converted']++;
            }
        }
        return $report;
    }

    /**
     * Placeholders of the bundle as the kernel's: "reference:<name>" (a whole value) and "[reference:<name>]" (in a
     * text) become ${ref.<name>}. Pure.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function placeholders( $value )
    {
        if ( is_array( $value ) )
        {
            $out = array();
            foreach ( $value as $key => $item )
                $out[$key] = self::placeholders( $item );
            return $out;
        }
        if ( !is_string( $value ) )
            return $value;
        if ( preg_match( '/^reference:([A-Za-z_][A-Za-z0-9_]*)$/', $value, $m ) )
            return '${ref.' . $m[1] . '}';
        return preg_replace( '/\[reference:([A-Za-z_][A-Za-z0-9_]*)\]/', '${ref.$1}', $value );
    }

    /**
     * One step of the bundle as zero, one or more steps of the kernel. Pure.
     *
     * @param array $step with the placeholders converted
     * @param string[] $manual what to do by hand is added here
     * @return array[] the kernel's steps
     */
    public static function convertStep( array $step, array &$manual )
    {
        $type = $step['type'];
        $mode = isset( $step['mode'] ) && is_string( $step['mode'] ) ? $step['mode'] : '';
        if ( !isset( self::CONVERTS[$type] ) )
        {
            $manual[] = "the step type $type has no counterpart that converts; write it by hand: " . expMigrationStepSchema::show( $step );
            return array();
        }
        if ( !in_array( $mode, self::CONVERTS[$type], true ) )
        {
            $manual[] = 'the mode ' . ( $mode === '' ? '(none)' : $mode ) . ' does not convert (' . implode( ', ', array_filter( self::CONVERTS[$type] ) ) . ')';
            return array();
        }
        $rest = $step;
        unset( $rest['type'], $rest['mode'] );
        $common = self::common( $rest, $manual );
        $method = 'step' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $type ) ) );
        $steps = self::$method( $mode, $rest, $manual );
        foreach ( array_keys( $rest ) as $key )
            $manual[] = "the key $key has no counterpart and is left out: " . expMigrationStepSchema::show( $rest[$key] );
        foreach ( $steps as $i => $made )
        {
            if ( $i === 0 && isset( $common['references'] ) )
                $made['references'] = $common['references'];
            if ( isset( $common['comment'] ) )
                $made['comment'] = $common['comment'];
            $steps[$i] = $made;
        }
        return $steps;
    }

    /**
     * The keys every step of the bundle can have: references (a list of identifier/attribute or a map), comment;
     * if (conditions) is written by hand. Removes them from the step.
     *
     * @param array $rest
     * @param string[] $manual
     * @return array references, comment
     */
    protected static function common( array &$rest, array &$manual )
    {
        $common = array();
        if ( isset( $rest['references'] ) )
        {
            $references = array();
            if ( is_array( $rest['references'] ) )
            {
                foreach ( $rest['references'] as $key => $reference )
                {
                    $name = is_array( $reference ) && isset( $reference['identifier'] ) ? $reference['identifier'] : ( is_string( $key ) ? $key : null );
                    $attribute = is_array( $reference ) && isset( $reference['attribute'] ) ? $reference['attribute'] : ( is_string( $reference ) ? $reference : null );
                    $value = is_string( $attribute ) && isset( self::VALUE_NAMES[$attribute] ) ? self::VALUE_NAMES[$attribute] : null;
                    if ( !is_string( $name ) || $value === null )
                    {
                        $manual[] = 'the reference ' . expMigrationStepSchema::show( $reference ) . ' has no value that converts';
                        continue;
                    }
                    $references[$name] = $value;
                }
            }
            if ( $references )
                $common['references'] = $references;
            unset( $rest['references'] );
        }
        foreach ( array( 'comment', 'description' ) as $key )
            if ( isset( $rest[$key] ) && is_string( $rest[$key] ) )
            {
                $common['comment'] = $rest[$key];
                unset( $rest[$key] );
            }
        if ( isset( $rest['if'] ) )
        {
            $manual[] = 'the condition if: is written by hand in the kernel\'s form: ' . expMigrationStepSchema::show( $rest['if'] );
            unset( $rest['if'] );
        }
        return $common;
    }

    /** @return mixed the value of a key, removed from the step; null when missing */
    protected static function take( array &$rest, $key )
    {
        if ( !array_key_exists( $key, $rest ) )
            return null;
        $value = $rest[$key];
        unset( $rest[$key] );
        return $value;
    }

    /**
     * Moves keys of the bundle to keys of the kernel: from => to.
     *
     * @param array $rest
     * @param array $out
     * @param array $keys
     */
    protected static function move( array &$rest, array &$out, array $keys )
    {
        foreach ( $keys as $from => $to )
        {
            $value = self::take( $rest, $from );
            if ( $value !== null )
                $out[$to] = $value;
        }
    }

    /**
     * What match: (or the older keys at the top of a step) names, as an identifier of the kernel: array( key, value )
     * with key object or node (or the kind's own key). Removes it from the step.
     *
     * @param array $rest
     * @param array $keys bundle key => array( kernel key, prefix ('' or remote: ...) )
     * @param string[] $manual
     * @return array|null
     */
    protected static function match( array &$rest, array $keys, array &$manual )
    {
        $match = self::take( $rest, 'match' );
        if ( !is_array( $match ) )
        {
            $match = array();
            foreach ( array_keys( $keys ) as $key )
                if ( array_key_exists( $key, $rest ) )
                    $match[$key] = self::take( $rest, $key );
        }
        if ( count( $match ) !== 1 )
        {
            $manual[] = 'match: names one thing by one key (' . implode( ', ', array_keys( $keys ) ) . '); write it by hand: ' . expMigrationStepSchema::show( $match );
            return null;
        }
        $key = key( $match );
        $value = current( $match );
        if ( !isset( $keys[$key] ) || is_array( $value ) )
        {
            $manual[] = "match: $key " . expMigrationStepSchema::show( $value ) . ' does not convert (' . implode( ', ', array_keys( $keys ) ) . ')';
            return null;
        }
        list( $to, $prefix ) = $keys[$key];
        return array( $to, $prefix === '' || strpos( (string)$value, '${' ) === 0 ? $value : $prefix . $value );
    }

    /** @return mixed a location of the bundle (an id or a remote id) as a node identifier */
    protected static function node( $value )
    {
        if ( is_int( $value ) || ( is_string( $value ) && ( ctype_digit( $value ) || strpos( $value, '${' ) === 0 ) ) )
            return $value;
        return is_string( $value ) ? 'remote:' . $value : $value;
    }

    /** Sets a translated value of the bundle (a text, or a map language => text) as the key for one text or for many. */
    protected static function translated( array &$out, $value, $one, $many )
    {
        if ( is_array( $value ) )
            $out[$many] = $value;
        else if ( $value !== null )
            $out[$one] = $value;
    }

    protected static function stepContent( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'content', 'mode' => $mode );
        if ( $mode === 'create' )
        {
            self::move( $rest, $out, array( 'content_type' => 'class' ) );
            $parent = self::take( $rest, 'parent_location' );
            if ( $parent === null )
                $parent = self::take( $rest, 'main_location' );
            if ( is_array( $parent ) )
            {
                $manual[] = 'more than one parent location: the first is the main one, add the others with a location step';
                $parent = reset( $parent );
            }
            if ( $parent !== null )
                $out['parent'] = self::node( $parent );
            self::move( $rest, $out, array( 'remote_id' => 'remote_id', 'section' => 'section', 'always_available' => 'always_available' ) );
        }
        else
        {
            $match = self::match( $rest, array( 'content_id' => array( 'object', '' ), 'object_id' => array( 'object', '' ),
                                                'content_remote_id' => array( 'object', 'remote:' ), 'remote_id' => array( 'object', 'remote:' ),
                                                'location_id' => array( 'node', '' ), 'location_remote_id' => array( 'node', 'remote:' ) ), $manual );
            if ( $match === null )
                return array();
            $out[$match[0]] = $match[1];
        }
        self::move( $rest, $out, array( 'lang' => 'language' ) );
        $attributes = self::take( $rest, 'attributes' );
        if ( is_array( $attributes ) && $mode !== 'delete' )
        {
            $fields = array();
            foreach ( $attributes as $identifier => $value )
            {
                if ( is_array( $value ) && array_key_exists( 'value', $value ) && count( $value ) === 1 )
                    $value = $value['value'];
                if ( is_array( $value ) )
                    $manual[] = "the field $identifier has a structured value; the kernel's content step takes the datatype's text form (fromString()): " . expMigrationStepSchema::show( $value );
                else
                    $fields[$identifier] = $value;
            }
            if ( $fields )
                $out['fields'] = $fields;
        }
        foreach ( array( 'priority', 'is_hidden', 'sort_field', 'sort_order', 'location_remote_id', 'owner', 'modification_date', 'publication_date', 'new_remote_id', 'object_states' ) as $key )
            if ( array_key_exists( $key, $rest ) )
                $manual[] = "$key: set it with a location, section or content step of its own: " . expMigrationStepSchema::show( self::take( $rest, $key ) );
        return array( $out );
    }

    protected static function stepContentType( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'content_class', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'identifier' => array( 'class', '' ), 'content_type_identifier' => array( 'class', '' ),
                                                'content_type_id' => array( 'class', '' ), 'id' => array( 'class', '' ) ), $manual );
            if ( $match === null )
                return array();
            $out['class'] = $match[1];
            self::move( $rest, $out, array( 'new_identifier' => 'identifier' ) );
        }
        else
            self::move( $rest, $out, array( 'identifier' => 'identifier' ) );
        if ( $mode === 'delete' )
            return array( $out );
        self::translated( $out, self::take( $rest, 'name' ), 'name', 'names' );
        self::translated( $out, self::take( $rest, 'description' ), 'description', 'descriptions' );
        self::move( $rest, $out, array( 'lang' => 'language', 'name_pattern' => 'name_pattern', 'url_name_pattern' => 'url_alias_pattern',
                                        'is_container' => 'container', 'default_always_available' => 'always_available',
                                        'always_available' => 'always_available', 'default_sort_field' => 'sort_field',
                                        'default_sort_order' => 'sort_order' ) );
        $group = self::take( $rest, 'content_type_group' );
        if ( $group !== null )
            $out['groups'] = (array)$group;
        $attributes = self::take( $rest, 'attributes' );
        if ( is_array( $attributes ) )
        {
            $converted = array();
            foreach ( $attributes as $attribute )
                if ( is_array( $attribute ) )
                    $converted[] = self::attribute( $attribute, $manual );
            if ( $mode === 'create' )
                $out['attributes'] = $converted;
            else
            {
                $update = array();
                foreach ( $converted as $attribute )
                    if ( isset( $attribute['identifier'] ) )
                        $update[$attribute['identifier']] = array_diff_key( $attribute, array( 'identifier' => true ) );
                if ( $update )
                    $out['update_attributes'] = $update;
                $manual[] = 'attributes: written as update_attributes; move those that are new to the class to add_attributes';
            }
        }
        $remove = self::take( $rest, 'remove_attributes' );
        if ( $remove !== null && $mode === 'update' )
            $out['remove_attributes'] = array_values( (array)$remove );
        return array( $out );
    }

    /** @return array an attribute of a content type of the bundle as the kernel's */
    protected static function attribute( array $attribute, array &$manual )
    {
        $out = array();
        self::move( $attribute, $out, array( 'identifier' => 'identifier', 'type' => 'type', 'required' => 'required', 'searchable' => 'searchable',
                                             'info-collector' => 'information_collector', 'category' => 'category', 'position' => 'placement',
                                             'field-settings' => 'settings' ) );
        self::translated( $out, self::take( $attribute, 'name' ), 'name', 'names' );
        self::translated( $out, self::take( $attribute, 'description' ), 'description', 'descriptions' );
        $noTranslation = self::take( $attribute, 'disable-translation' );
        if ( $noTranslation !== null )
            $out['translatable'] = !$noTranslation;
        if ( isset( $out['type'] ) && is_string( $out['type'] ) && strpos( $out['type'], 'ez' ) !== 0 )
            $manual[] = 'the attribute ' . ( isset( $out['identifier'] ) ? $out['identifier'] : '?' ) . ' has the field type ' . $out['type'] . ': name the datatype of this installation';
        foreach ( $attribute as $key => $value )
            $manual[] = 'the attribute ' . ( isset( $out['identifier'] ) ? $out['identifier'] : '?' ) . ": $key has no counterpart: " . expMigrationStepSchema::show( $value );
        return $out;
    }

    protected static function stepContentTypeGroup( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'content_class_group', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'content_type_group_identifier' => array( 'group', '' ), 'content_type_group_id' => array( 'group', '' ),
                                                'identifier' => array( 'group', '' ), 'id' => array( 'group', '' ) ), $manual );
            if ( $match === null )
                return array();
            $out['group'] = $match[1];
        }
        if ( $mode !== 'delete' )
        {
            $name = self::take( $rest, 'identifier' );
            if ( $name === null )
                $name = self::take( $rest, 'name' );
            if ( $name !== null )
                $out['name'] = $name;
        }
        return array( $out );
    }

    protected static function stepLocation( $mode, array &$rest, array &$manual )
    {
        if ( $mode === 'create' )
        {
            $match = self::match( $rest, array( 'content_id' => array( 'object', '' ), 'object_id' => array( 'object', '' ),
                                                'content_remote_id' => array( 'object', 'remote:' ), 'remote_id' => array( 'object', 'remote:' ) ), $manual );
            if ( $match === null )
                return array();
            $steps = array();
            foreach ( (array)self::take( $rest, 'parent_location' ) as $parent )
            {
                $out = array( 'type' => 'location', 'mode' => 'create', 'object' => $match[1], 'parent' => self::node( $parent ) );
                self::move( $rest, $out, array( 'priority' => 'priority', 'sort_field' => 'sort_field', 'sort_order' => 'sort_order' ) );
                $steps[] = $out;
            }
            if ( !$steps )
                $manual[] = 'parent_location is missing';
            return $steps;
        }
        $match = self::match( $rest, array( 'location_id' => array( 'node', '' ), 'location_remote_id' => array( 'node', 'remote:' ) ), $manual );
        if ( $match === null )
            return array();
        if ( $mode === 'delete' )
            return array( array( 'type' => 'location', 'mode' => 'delete', 'node' => $match[1] ) );
        $steps = array();
        $out = array( 'type' => 'location', 'mode' => 'update', 'node' => $match[1] );
        self::move( $rest, $out, array( 'priority' => 'priority', 'sort_field' => 'sort_field', 'sort_order' => 'sort_order' ) );
        if ( count( $out ) > 3 )
            $steps[] = $out;
        $parent = self::take( $rest, 'parent_location' );
        if ( $parent !== null )
            $steps[] = array( 'type' => 'location', 'mode' => 'move', 'node' => $match[1], 'parent' => self::node( $parent ) );
        $hidden = self::take( $rest, 'is_hidden' );
        if ( $hidden !== null )
            $steps[] = array( 'type' => 'location', 'mode' => $hidden ? 'hide' : 'unhide', 'node' => $match[1] );
        if ( !$steps )
            $manual[] = 'the update changes nothing the kernel\'s location step changes';
        return $steps;
    }

    protected static function stepRole( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'role', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'identifier' => array( 'role', '' ), 'role_identifier' => array( 'role', '' ),
                                                'role_id' => array( 'role', '' ), 'id' => array( 'role', '' ) ), $manual );
            if ( $match === null )
                return array();
            $out['role'] = $match[1];
            self::move( $rest, $out, array( 'new_name' => 'name' ) );
        }
        else
            self::move( $rest, $out, array( 'name' => 'name' ) );
        if ( $mode === 'delete' )
            return array( $out );
        $policies = self::take( $rest, 'policies' );
        if ( is_array( $policies ) )
        {
            $converted = array();
            foreach ( $policies as $policy )
            {
                if ( !is_array( $policy ) || !isset( $policy['module'] ) )
                    continue;
                $one = array( 'module' => $policy['module'], 'function' => isset( $policy['function'] ) ? $policy['function'] : '*' );
                foreach ( isset( $policy['limitations'] ) ? (array)$policy['limitations'] : array() as $limitation )
                    if ( is_array( $limitation ) && isset( $limitation['identifier'] ) )
                        $one['limitations'][$limitation['identifier']] = array_values( (array)( isset( $limitation['values'] ) ? $limitation['values'] : array() ) );
                $converted[] = $one;
            }
            $out[$mode === 'create' ? 'policies' : 'add_policies'] = $converted;
            if ( $mode === 'update' )
                $manual[] = 'policies: the bundle replaces the role\'s policies, the kernel adds them (add_policies); list those to take away under remove_policies';
        }
        $steps = $mode === 'update' && count( $out ) <= 3 ? array() : array( $out );
        $assign = self::take( $rest, 'assign' );
        foreach ( is_array( $assign ) ? $assign : array() as $assignment )
        {
            if ( !is_array( $assignment ) || !isset( $assignment['type'], $assignment['ids'] ) )
                continue;
            $one = array( 'type' => 'role', 'mode' => 'assign', 'role' => $mode === 'create' ? ( isset( $out['name'] ) ? $out['name'] : '' ) : $out['role'] );
            $one[$assignment['type'] === 'user' ? 'users' : 'groups'] = array_values( (array)$assignment['ids'] );
            foreach ( isset( $assignment['limitations'] ) ? (array)$assignment['limitations'] : array() as $limitation )
            {
                $identifier = is_array( $limitation ) && isset( $limitation['identifier'] ) ? strtolower( $limitation['identifier'] ) : '';
                $values = is_array( $limitation ) && isset( $limitation['values'] ) ? array_values( (array)$limitation['values'] ) : array();
                if ( ( $identifier === 'subtree' || $identifier === 'section' ) && count( $values ) === 1 )
                    $one[$identifier] = $identifier === 'subtree' ? self::subtreeNode( $values[0] ) : $values[0];
                else
                    $manual[] = 'the assignment limitation ' . expMigrationStepSchema::show( $limitation ) . ' does not convert (one Subtree or Section)';
            }
            $steps[] = $one;
        }
        $unassign = self::take( $rest, 'unassign' );
        if ( $unassign !== null )
            $manual[] = 'unassign: write a role step with mode unassign: ' . expMigrationStepSchema::show( $unassign );
        return $steps;
    }

    /** @return mixed a subtree limitation value (a path string /1/2/42/ or a node) as a node identifier */
    protected static function subtreeNode( $value )
    {
        if ( is_string( $value ) && preg_match( '#^/(\d+/)+$#', $value ) )
        {
            $parts = array_filter( explode( '/', $value ), 'strlen' );
            return (int)end( $parts );
        }
        return self::node( $value );
    }

    protected static function stepSection( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'section', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'section_identifier' => array( 'section', '' ), 'section_id' => array( 'section', '' ),
                                                'identifier' => array( 'section', '' ), 'id' => array( 'section', '' ) ), $manual );
            if ( $match === null )
                return array();
            $out['section'] = $match[1];
        }
        if ( $mode !== 'delete' )
            self::move( $rest, $out, array( 'identifier' => 'identifier', 'name' => 'name' ) );
        return array( $out );
    }

    protected static function stepUser( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'user', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'login' => array( 'user', '' ), 'username' => array( 'user', '' ), 'id' => array( 'user', '' ),
                                                'user_id' => array( 'user', '' ), 'email' => array( 'user', '' ) ), $manual );
            if ( $match === null )
                return array();
            if ( strpos( (string)$match[1], '@' ) !== false )
            {
                $manual[] = 'the user is named by an e-mail address; the kernel names a user by login, id or remote:<remote id>';
                return array();
            }
            $out['user'] = $match[1];
        }
        else
            self::move( $rest, $out, array( 'username' => 'login' ) );
        if ( $mode === 'delete' )
            return array( $out );
        self::move( $rest, $out, array( 'email' => 'email', 'password' => 'password', 'lang' => 'language', 'enabled' => 'enabled' ) );
        foreach ( array( 'first_name', 'last_name' ) as $field )
        {
            $value = self::take( $rest, $field );
            if ( $value !== null )
                $out['fields'][$field] = $value;
        }
        $groups = self::take( $rest, 'groups' );
        if ( $groups !== null )
            $out['groups'] = array_values( (array)$groups );
        return array( $out );
    }

    protected static function stepUserGroup( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'user_group', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'id' => array( 'group', '' ), 'content_id' => array( 'group', '' ),
                                                'remote_id' => array( 'group', 'remote:' ), 'content_remote_id' => array( 'group', 'remote:' ) ), $manual );
            if ( $match === null )
                return array();
            $out['group'] = $match[1];
        }
        else
        {
            $parent = self::take( $rest, 'parent_group_id' );
            if ( $parent !== null )
            {
                $out['parent'] = $parent;
                $manual[] = 'parent_group_id is the id of the parent group\'s object in the bundle; check that the kernel\'s parent (a node) names the same group';
            }
            self::move( $rest, $out, array( 'remote_id' => 'remote_id' ) );
        }
        if ( $mode !== 'delete' )
            self::move( $rest, $out, array( 'name' => 'name', 'description' => 'description', 'lang' => 'language' ) );
        return array( $out );
    }

    protected static function stepLanguage( $mode, array &$rest, array &$manual )
    {
        $locale = self::take( $rest, 'lang' );
        if ( $locale === null )
        {
            $manual[] = 'lang (the language code) is missing';
            return array();
        }
        if ( $mode === 'delete' )
            return array( array( 'type' => 'language', 'mode' => 'delete', 'language' => $locale ) );
        $out = array( 'type' => 'language', 'mode' => 'create', 'locale' => $locale );
        self::move( $rest, $out, array( 'name' => 'name' ) );
        return array( $out );
    }

    protected static function stepTag( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'tag', 'mode' => $mode );
        if ( $mode !== 'create' )
        {
            $match = self::match( $rest, array( 'tag_id' => array( 'tag', '' ), 'id' => array( 'tag', '' ), 'tag_remote_id' => array( 'tag', 'remote:' ),
                                                'remote_id' => array( 'tag', 'remote:' ) ), $manual );
            if ( $match === null )
                return array();
            $out['tag'] = $match[1];
        }
        else
        {
            $parent = self::take( $rest, 'parent_tag_id' );
            $out['parent'] = $parent === null ? 0 : $parent;
            self::move( $rest, $out, array( 'remote_id' => 'remote_id' ) );
        }
        if ( $mode !== 'delete' )
        {
            self::move( $rest, $out, array( 'lang' => 'language', 'main_language' => 'main_language', 'always_available' => 'always_available' ) );
            self::translated( $out, self::take( $rest, 'keywords' ), 'keyword', 'keywords' );
        }
        return array( $out );
    }

    protected static function stepSql( $mode, array &$rest, array &$manual )
    {
        $out = array( 'type' => 'sql' );
        $dbserver = self::take( $rest, 'dbserver' );
        $sql = self::take( $rest, 'sql' );
        foreach ( array( 'mysql', 'postgresql', 'sqlite', 'oracle' ) as $engine )
            self::move( $rest, $out, array( $engine => $engine ) );
        if ( is_string( $sql ) )
        {
            if ( is_string( $dbserver ) && in_array( $dbserver, array( 'mysql', 'postgresql', 'sqlite', 'oracle' ), true ) )
                $out[$dbserver] = $sql;
            else
                $out['sql'] = $sql;
        }
        if ( count( $out ) === 1 )
        {
            $manual[] = 'no SQL to convert';
            return array();
        }
        return array( $out );
    }

    protected static function stepReference( $mode, array &$rest, array &$manual )
    {
        $name = self::take( $rest, 'identifier' );
        $value = self::take( $rest, 'value' );
        self::take( $rest, 'overwrite' );
        if ( !is_string( $name ) || $value === null )
        {
            $manual[] = 'a reference set needs identifier and value';
            return array();
        }
        return array( array( 'type' => 'reference', 'set' => array( $name => $value ) ) );
    }

    protected static function stepTrash( $mode, array &$rest, array &$manual )
    {
        return array( array( 'type' => 'trash', 'mode' => 'purge', 'all' => true ) );
    }
}
