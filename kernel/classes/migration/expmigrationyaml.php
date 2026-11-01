<?php
/**
 * File containing the expMigrationYaml class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A YAML migration: a file YYYYMMDDHHMMSS_name.yml with its header in "#" comment lines (expMigrationDefinition)
 * and a list of steps, each of a step type registered in migration.ini [StepSettings] StepTypeList
 * (expMigrationStepType). The engine runs the steps in order and does what is common to all of them:
 *
 *   # description: The news folder and its first article
 *   # requires: 20261030090000_news_class
 *   steps:
 *       - type: reference                   the step type
 *         set: { parent: "${idmap.node.news}" }
 *       - type: content                     (a step type of an extension or of a later kernel version)
 *         mode: ensure                      create, update, ensure, delete ... (what the type takes)
 *         if: { environment: "!production" }
 *         environments: [ staging ]         the step only runs in these environments (!name: not in that one)
 *         engines: [ postgresql ]           ... and on these database engines
 *         parent: "${ref.parent}"
 *         references: { article: node_id }  reference name => a value of the step's result
 *       - foreach: [ eng-US, ger-DE ]       a loop: its steps run for each item (${loop.item}, ${loop.key},
 *         as: language                      ${loop.index}; "as" and "key" rename item and key)
 *         steps: [ ... ]
 *       - while: { value: "${ref.left}", gt: 0 }   a loop that runs while a condition is true, at most "max"
 *         max: 50                                   times ([StepSettings] MaxLoopIterations when not given)
 *         steps: [ ... ]
 *   down:                                   optional: the steps that undo the migration (exp:migration:rollback)
 *       - ...
 *   transactional: true                     false: no transaction around the migration
 *
 * The document is either that map or the list of steps alone. Placeholders (${ref.name}, ${loop.item},
 * ${idmap.kind.name}, ${run.environment}) are replaced in the values of a step right before it runs
 * (expMigrationReferences); a reference set by a step is there for the steps and migrations after it in the run.
 *
 * Everything that can be checked without running is checked when the migration is loaded (load(), the lint of
 * exp:migration:check): the YAML, the document's keys, every step's type, mode and keys against the type's schema,
 * conditions, loops, references and placeholders. A YAML migration without "down:" can be undone by itself only when
 * every step's type can revert its step (expMigrationRevertibleStepType) and none is a loop.
 *
 * The state is record-based, as for a PHP migration without detect().
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationYaml implements expMigration
{
    /** The keys of a document written as a map. */
    const DOCUMENT_KEYS = array( 'steps', 'down', 'transactional' );

    /** The keys of a step the engine reads; the others are the step type's. */
    const STEP_KEYS = array( 'type', 'mode', 'if', 'references', 'environments', 'engines', 'comment' );

    /** The keys of a loop. */
    const LOOP_KEYS = array( 'foreach', 'as', 'key', 'while', 'max', 'steps', 'if', 'comment' );

    /** The most turns of a while loop without max, when [StepSettings] MaxLoopIterations names none. */
    const MAX_ITERATIONS = 1000;

    /** How deep loops can nest. */
    const MAX_DEPTH = 8;

    /** @var expMigrationDefinition */
    protected $definition;

    /** @var array steps, down (null without), transactional */
    protected $document;

    /** @var expMigrationStepRegistry */
    protected $registry;

    /** @var array environment => array( map, errors ): the id maps read */
    protected $idmaps = array();

    /**
     * @param expMigrationDefinition $definition
     * @param array $document a valid document (document(), validate())
     * @param expMigrationStepRegistry $registry
     */
    public function __construct( expMigrationDefinition $definition, array $document, expMigrationStepRegistry $registry )
    {
        $this->definition = $definition;
        $this->document = $document + array( 'steps' => array(), 'down' => null, 'transactional' => true );
        $this->registry = $registry;
    }

    /**
     * Reads a YAML migration file and makes its migration.
     *
     * @param expMigrationDefinition $definition a definition of type yml
     * @param expMigrationStepRegistry|null $registry null: the step types of migration.ini
     * @return expMigrationYaml
     * @throws RuntimeException with every problem of the file
     */
    public static function load( expMigrationDefinition $definition, $registry = null )
    {
        $errors = array();
        $warnings = array();
        $migration = self::read( $definition, $registry, $errors, $warnings );
        if ( $migration === null )
            throw new RuntimeException( implode( '; ', $errors ) );
        return $migration;
    }

    /**
     * Reads and checks a YAML migration file (the lint of exp:migration:check).
     *
     * @param expMigrationDefinition $definition
     * @param expMigrationStepRegistry|null $registry
     * @param string[] $errors
     * @param string[] $warnings
     * @return expMigrationYaml|null null when there is an error
     */
    public static function read( expMigrationDefinition $definition, $registry, array &$errors, array &$warnings )
    {
        if ( $definition->type() !== 'yml' )
        {
            $errors[] = $definition->id() . ' is not a YAML migration (.' . $definition->type() . ')';
            return null;
        }
        $file = $definition->file();
        $text = $file !== null && is_file( $file ) ? @file_get_contents( $file ) : false;
        if ( $text === false )
        {
            $errors[] = $definition->id() . ': the file is missing or cannot be read';
            return null;
        }
        $registry = $registry ?: expMigrationStepRegistry::fromSettings();
        $document = self::fromText( (string)$text, $registry, $errors, $warnings );
        return $document === null ? null : new self( $definition, $document, $registry );
    }

    /**
     * The checked document of a YAML text. Pure but for the parser.
     *
     * @param string $text
     * @param expMigrationStepRegistry $registry
     * @param string[] $errors
     * @param string[] $warnings
     * @return array|null
     */
    public static function fromText( $text, expMigrationStepRegistry $registry, array &$errors, array &$warnings = array() )
    {
        $parseErrors = array();
        $parsed = expMigrationYamlParser::parse( $text, $parseErrors );
        if ( $parseErrors )
        {
            foreach ( $parseErrors as $error )
                $errors[] = $error;
            return null;
        }
        $documentErrors = array();
        $document = self::document( $parsed, $documentErrors );
        if ( $document !== null )
            $documentErrors = array_merge( $documentErrors, self::validate( $document, $registry, $warnings ) );
        foreach ( $documentErrors as $error )
            $errors[] = $error;
        return $documentErrors ? null : $document;
    }

    /**
     * The document of a parsed file: its steps, down steps and transactional flag. Pure.
     *
     * @param mixed $parsed
     * @param string[] $errors
     * @return array|null
     */
    public static function document( $parsed, array &$errors = array() )
    {
        $document = array( 'steps' => array(), 'down' => null, 'transactional' => true );
        if ( $parsed === null )
            return $document;
        if ( !is_array( $parsed ) )
        {
            $errors[] = 'the document is a list of steps, or a map with steps (and down, transactional)';
            return null;
        }
        if ( self::isList( $parsed ) )
        {
            $document['steps'] = $parsed;
            return $document;
        }
        $ok = true;
        foreach ( array_keys( $parsed ) as $key )
        {
            if ( in_array( $key, self::DOCUMENT_KEYS, true ) )
                continue;
            $ok = false;
            if ( in_array( $key, expMigrationDefinition::KEYS, true ) )
                $errors[] = "$key: the header is written in comment lines at the top of the file (# $key: ...), not as a YAML key";
            else
                $errors[] = "the unknown key $key (" . implode( ', ', self::DOCUMENT_KEYS ) . ')';
        }
        if ( !array_key_exists( 'steps', $parsed ) )
        {
            $errors[] = 'steps is missing';
            return null;
        }
        foreach ( array( 'steps', 'down' ) as $key )
        {
            if ( !array_key_exists( $key, $parsed ) || ( $key === 'down' && $parsed[$key] === null ) )
                continue;
            if ( $parsed[$key] === null )
                $parsed[$key] = array();
            if ( !is_array( $parsed[$key] ) || !self::isList( $parsed[$key] ) )
            {
                $errors[] = "$key: a list of steps";
                $ok = false;
                continue;
            }
            $document[$key] = $parsed[$key];
        }
        if ( array_key_exists( 'transactional', $parsed ) )
        {
            if ( !is_bool( $parsed['transactional'] ) )
            {
                $errors[] = 'transactional: true or false';
                $ok = false;
            }
            else
                $document['transactional'] = $parsed['transactional'];
        }
        return $ok ? $document : null;
    }

    /**
     * The problems of a document. Pure.
     *
     * @param array $document
     * @param expMigrationStepRegistry $registry
     * @param string[] $warnings
     * @return string[]
     */
    public static function validate( array $document, expMigrationStepRegistry $registry, array &$warnings = array() )
    {
        $errors = array();
        foreach ( $registry->errors() as $error )
            $errors[] = $error;
        if ( !$document['steps'] )
            $warnings[] = 'no steps: the migration does nothing';
        self::validateSteps( $document['steps'], 'steps', $registry, $errors, 0 );
        if ( $document['down'] !== null )
        {
            if ( !$document['down'] )
                $warnings[] = 'down is empty: rolling the migration back does nothing';
            self::validateSteps( $document['down'], 'down', $registry, $errors, 0 );
        }
        return $errors;
    }

    /**
     * @param array $steps
     * @param string $where
     * @param expMigrationStepRegistry $registry
     * @param string[] $errors
     * @param int $depth
     * @param string[] $loopVars the loop variables the steps can use (${loop.<name>})
     */
    protected static function validateSteps( array $steps, $where, expMigrationStepRegistry $registry, array &$errors, $depth, array $loopVars = array() )
    {
        foreach ( $steps as $i => $step )
        {
            $at = $where . "[$i]";
            if ( !is_array( $step ) || !$step || self::isList( $step ) )
            {
                $errors[] = "$at: a step is a map (type: ... or a loop: foreach: ... / while: ...)";
                continue;
            }
            if ( isset( $step['comment'] ) && !is_string( $step['comment'] ) )
                $errors[] = "$at.comment: a text";
            if ( array_key_exists( 'if', $step ) )
            {
                foreach ( expMigrationCondition::validate( $step['if'], "$at.if" ) as $error )
                    $errors[] = $error;
                self::checkLoopNames( $step['if'], $loopVars, "$at.if", $errors );
            }
            if ( array_key_exists( 'foreach', $step ) || array_key_exists( 'while', $step ) )
            {
                self::validateLoop( $step, $at, $registry, $errors, $depth, $loopVars );
                continue;
            }
            $own = $step;
            unset( $own['if'] );
            self::checkLoopNames( $own, $loopVars, $at, $errors );
            self::validateStep( $step, $at, $registry, $errors );
        }
    }

    /**
     * Placeholders ${loop.<name>} that name no variable of the loops around them. Pure.
     *
     * @param mixed $value
     * @param string[] $loopVars
     * @param string $where
     * @param string[] $errors
     */
    protected static function checkLoopNames( $value, array $loopVars, $where, array &$errors )
    {
        foreach ( expMigrationReferences::used( $value, 'loop' ) as $name )
            if ( !in_array( $name, $loopVars, true ) )
                $errors[] = "$where: \${loop.$name} names no variable of a loop around it ("
                            . ( $loopVars ? implode( ', ', $loopVars ) : 'it is in no loop' ) . ')';
    }

    /**
     * @param array $step
     * @param string $at
     * @param expMigrationStepRegistry $registry
     * @param string[] $errors
     * @param int $depth
     * @param string[] $loopVars the variables of the loops around this one
     */
    protected static function validateLoop( array $step, $at, expMigrationStepRegistry $registry, array &$errors, $depth, array $loopVars = array() )
    {
        if ( array_key_exists( 'foreach', $step ) && array_key_exists( 'while', $step ) )
            $errors[] = "$at: a loop is foreach or while, not both";
        foreach ( array_diff( array_keys( $step ), self::LOOP_KEYS ) as $key )
            $errors[] = "$at: the unknown key $key of a loop (" . implode( ', ', self::LOOP_KEYS ) . ')'
                        . ( $key === 'type' ? '; a loop has no type, its steps have' : '' );
        if ( $depth >= self::MAX_DEPTH )
            $errors[] = "$at: loops nest " . self::MAX_DEPTH . ' deep at most';
        if ( array_key_exists( 'foreach', $step ) )
        {
            $items = $step['foreach'];
            if ( is_string( $items ) )
            {
                if ( !preg_match( '/^\$\{[^}]*\}$/', $items ) )
                    $errors[] = "$at.foreach: a list, a map or one placeholder (\${ref.list})";
                expMigrationReferences::validate( $items, "$at.foreach", $errors );
            }
            else if ( !is_array( $items ) )
                $errors[] = "$at.foreach: a list, a map or one placeholder (\${ref.list})";
            else
                expMigrationReferences::validate( $items, "$at.foreach", $errors );
            $as = isset( $step['as'] ) ? $step['as'] : 'item';
            $key = isset( $step['key'] ) ? $step['key'] : 'key';
            foreach ( array( 'as' => $as, 'key' => $key ) as $field => $name )
                if ( !expMigrationReferences::validName( $name ) || $name === 'index' )
                    $errors[] = "$at.$field: a name of letters, digits and _ (not index, which is the turn's number)";
            if ( $as === $key )
                $errors[] = "$at: as and key name the same variable";
            if ( array_key_exists( 'max', $step ) )
                $errors[] = "$at.max: only a while loop has a maximum";
            self::checkLoopNames( $items, $loopVars, "$at.foreach", $errors );
            $inner = array_values( array_unique( array_merge( $loopVars, array( (string)$as, (string)$key, 'index' ) ) ) );
        }
        else
        {
            foreach ( expMigrationCondition::validate( $step['while'], "$at.while" ) as $error )
                $errors[] = $error;
            $inner = array_values( array_unique( array_merge( $loopVars, array( 'index' ) ) ) );
            self::checkLoopNames( $step['while'], $inner, "$at.while", $errors );
            if ( array_key_exists( 'max', $step ) && ( !is_int( $step['max'] ) || $step['max'] < 1 ) )
                $errors[] = "$at.max: a whole number above 0";
            foreach ( array( 'as', 'key' ) as $field )
                if ( array_key_exists( $field, $step ) )
                    $errors[] = "$at.$field: only a foreach loop names its item and key";
        }
        if ( !isset( $step['steps'] ) || !is_array( $step['steps'] ) || !$step['steps'] || !self::isList( $step['steps'] ) )
        {
            $errors[] = "$at.steps: a loop has a list of steps";
            return;
        }
        self::validateSteps( $step['steps'], "$at.steps", $registry, $errors, $depth + 1, $inner );
    }

    /**
     * @param array $step
     * @param string $at
     * @param expMigrationStepRegistry $registry
     * @param string[] $errors
     */
    protected static function validateStep( array $step, $at, expMigrationStepRegistry $registry, array &$errors )
    {
        if ( !isset( $step['type'] ) || !is_string( $step['type'] ) || $step['type'] === '' )
        {
            $errors[] = "$at: type is missing (" . ( $registry->names() ? implode( ', ', $registry->names() ) : 'no step types are registered' ) . ')';
            return;
        }
        $type = $registry->get( $step['type'] );
        if ( $type === null )
        {
            $errors[] = "$at: the unknown step type {$step['type']} ("
                        . ( $registry->names() ? 'registered: ' . implode( ', ', $registry->names() ) : 'no step types are registered' )
                        . '; an extension adds one in migration.ini [StepSettings] StepTypeList)';
            return;
        }
        $at .= ' (' . $step['type'] . ')';
        foreach ( array( 'environments', 'engines' ) as $field )
        {
            if ( !array_key_exists( $field, $step ) )
                continue;
            $names = self::names( $step[$field] );
            if ( $names === null || !$names )
                $errors[] = "$at.$field: a list of names";
            else if ( $field === 'engines' )
                foreach ( $names as $name )
                    if ( !in_array( ltrim( $name, '!' ), expMigrationDefinition::ENGINES, true ) )
                        $errors[] = "$at.engines: the unknown engine $name (" . implode( ', ', expMigrationDefinition::ENGINES ) . ')';
        }
        $schema = $type->schema();
        if ( array_key_exists( 'references', $step ) )
        {
            $values = isset( $schema['values'] ) ? (array)$schema['values'] : array();
            if ( !is_array( $step['references'] ) || !$step['references'] || self::isList( $step['references'] ) )
                $errors[] = "$at.references: a map of reference name => value of the step"
                            . ( $values ? ' (' . implode( ', ', array_keys( $values ) ) . ')' : '' );
            else
                foreach ( $step['references'] as $name => $value )
                {
                    if ( !expMigrationReferences::validName( $name ) )
                        $errors[] = "$at.references.$name: a reference name is letters, digits and _, not starting with a digit";
                    if ( !is_string( $value ) || $value === '' )
                        $errors[] = "$at.references.$name: the name of a value of the step";
                    else if ( !isset( $values['*'] ) && !array_key_exists( $value, $values ) )
                        $errors[] = "$at.references.$name: the step gives no value $value ("
                                    . ( $values ? implode( ', ', array_keys( $values ) ) : 'it gives none' ) . ')';
                }
        }
        $own = self::ownKeys( $step );
        foreach ( expMigrationStepSchema::check( $schema, $own, $at ) as $error )
            $errors[] = $error;
        try
        {
            foreach ( (array)$type->validate( $own ) as $error )
                $errors[] = "$at: $error";
        }
        catch ( Throwable $e )
        {
            $errors[] = "$at: " . $e->getMessage();
        }
    }

    // ---- expMigration -----------------------------------------------------------------------------------------

    /** @return string */
    public function id()
    {
        return $this->definition->id();
    }

    /** @return string */
    public function source()
    {
        return $this->definition->source();
    }

    /** @return string */
    public function description()
    {
        return $this->definition->description();
    }

    /** @return string[] */
    public function environments()
    {
        return $this->definition->environments();
    }

    /** @return string[] */
    public function requires()
    {
        return $this->definition->requires();
    }

    /** @return bool */
    public function maintenance()
    {
        return $this->definition->maintenance();
    }

    /** @return string[] */
    public function engines()
    {
        return $this->definition->engines();
    }

    /** @return string|null */
    public function checksum()
    {
        return $this->definition->checksumFor();
    }

    /** @return string|null */
    public function file()
    {
        return $this->definition->file();
    }

    /** @return expMigrationDefinition */
    public function definition()
    {
        return $this->definition;
    }

    /** @return array the steps as written */
    public function steps()
    {
        return $this->document['steps'];
    }

    /** @return array|null the down steps as written, null without */
    public function downSteps()
    {
        return $this->document['down'];
    }

    /**
     * Whether the migration runs in a transaction where the engine has one: not when the document says
     * "transactional: false" or the header "transactional: no".
     *
     * @return bool
     */
    public function transactional()
    {
        return (bool)$this->document['transactional'] && $this->definition->transactional();
    }

    /**
     * The caches the steps touch (each step type's caches()), for the cache clearing after a run.
     *
     * @return string[] sorted
     */
    public function caches()
    {
        $caches = array();
        $this->collectCaches( $this->document['steps'], $caches );
        $caches = array_values( array_unique( $caches ) );
        sort( $caches );
        return $caches;
    }

    /**
     * @param array $steps
     * @param string[] $caches
     */
    protected function collectCaches( array $steps, array &$caches )
    {
        foreach ( $steps as $step )
        {
            if ( isset( $step['steps'] ) && ( array_key_exists( 'foreach', $step ) || array_key_exists( 'while', $step ) ) )
            {
                $this->collectCaches( (array)$step['steps'], $caches );
                continue;
            }
            $type = $this->registry->get( isset( $step['type'] ) ? $step['type'] : null );
            if ( $type )
                foreach ( (array)$type->caches( expMigrationStepSchema::withDefaults( $type->schema(), self::ownKeys( $step ) ) ) as $cache )
                    $caches[] = (string)$cache;
        }
    }

    /**
     * @param expMigrationContext $context
     * @return string
     */
    public function state( $context )
    {
        $record = $context->record( $this->source(), $this->id() );
        if ( !$record )
            return expMigration::PENDING;
        if ( $record['state'] === expMigration::DONE )
            return expMigration::DONE;
        if ( $record['state'] === expMigration::FAILED )
            return expMigration::FAILED;
        return expMigration::PENDING;
    }

    /**
     * What up() would do: every step's plan, loops and conditions as far as they can be told before the run.
     *
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( $context )
    {
        $this->tellFile( $context );
        $lines = array();
        $pending = array();
        $idmapErrors = array();
        $scope = $this->scope( $context, array(), $idmapErrors );
        foreach ( $idmapErrors as $error )
            $lines[] = "the id map: $error";
        $planned = array();
        $this->planSteps( $this->document['steps'], 'steps', $context, $scope, $lines, '', $pending, $planned, true );
        return $lines ?: array( 'nothing: the migration has no steps' );
    }

    /**
     * The plan of steps. The references a "reference" step sets are known before the run (when its values are), so
     * the steps after it are planned with them; those other steps set are only known when they run ($pending).
     *
     * @param array $steps
     * @param string $where
     * @param expMigrationContext $context
     * @param array $scope
     * @param string[] $lines
     * @param string $indent
     * @param string[] $pending the references steps set when they run
     * @param array $planned name => value of the references known before the run
     * @param bool $certain whether the steps run for sure (false inside a condition or loop that cannot be told yet)
     */
    protected function planSteps( array $steps, $where, $context, array $scope, array &$lines, $indent, array &$pending, array &$planned, $certain )
    {
        foreach ( $steps as $i => $step )
        {
            $at = $where . "[$i]";
            $label = $at . ( isset( $step['type'] ) ? ' (' . $step['type'] . ')' : '' );
            $skip = $this->skipReason( $step, $context );
            if ( $skip !== null )
            {
                $lines[] = $indent . "skip $label: $skip";
                continue;
            }
            $scope['ref'] = array_merge( $context->references(), $planned );
            $scope['pending'] = $pending;
            $inner = $indent;
            $sure = $certain;
            if ( array_key_exists( 'if', $step ) )
            {
                $result = expMigrationCondition::evaluate( $step['if'], $scope, false );
                if ( $result === false )
                {
                    $lines[] = $indent . "skip $label: the condition is false";
                    continue;
                }
                if ( $result === null )
                {
                    $lines[] = $indent . 'run if ' . expMigrationCondition::describe( $step['if'] ) . ' (decided when the migration runs):';
                    $inner .= '  ';
                    $sure = false;
                }
            }
            if ( array_key_exists( 'foreach', $step ) )
            {
                $unresolved = array();
                $items = expMigrationReferences::interpolate( $step['foreach'], $scope, false, $unresolved );
                $as = isset( $step['as'] ) ? (string)$step['as'] : 'item';
                $keyName = isset( $step['key'] ) ? (string)$step['key'] : 'key';
                if ( $unresolved || !is_array( $items ) )
                {
                    $lines[] = $inner . 'run for each item of ' . expMigrationStepSchema::show( $step['foreach'] ) . ', as ' . $as . ' (known when the migration runs):';
                    $this->planSteps( $step['steps'], "$at.steps", $context, $scope, $lines, $inner . '  ', $pending, $planned, false );
                    continue;
                }
                $lines[] = $inner . 'run for each of ' . count( $items ) . ' item(s), as ' . $as . ':';
                $index = 0;
                foreach ( $items as $key => $item )
                {
                    $loopScope = $scope;
                    $loopScope['loop'] = array_merge( isset( $scope['loop'] ) ? (array)$scope['loop'] : array(),
                                                      array( $as => $item, $keyName => $key, 'index' => $index ) );
                    $this->planSteps( $step['steps'], "$at.steps", $context, $loopScope, $lines, $inner . '  ', $pending, $planned, $sure );
                    $index++;
                }
                continue;
            }
            if ( array_key_exists( 'while', $step ) )
            {
                $lines[] = $inner . 'run while ' . expMigrationCondition::describe( $step['while'] ) . ', at most ' . $this->maxIterations( $step ) . ' times:';
                $this->planSteps( $step['steps'], "$at.steps", $context, $scope, $lines, $inner . '  ', $pending, $planned, false );
                continue;
            }
            $type = $this->registry->get( $step['type'] );
            if ( $type === null )
            {
                $lines[] = $inner . "$label: the step type is not registered";
                continue;
            }
            $unresolved = array();
            $own = expMigrationStepSchema::withDefaults( $type->schema(), expMigrationReferences::interpolate( self::ownKeys( $step ), $scope, false, $unresolved ) );
            foreach ( (array)$type->plan( $own, $context ) as $line )
                $lines[] = $inner . (string)$line;
            if ( $type instanceof expMigrationStepReference && isset( $own['set'] ) && is_array( $own['set'] ) )
            {
                foreach ( $own['set'] as $name => $value )
                {
                    if ( $sure && !expMigrationReferences::contains( $value ) )
                        $planned[(string)$name] = $value;
                    else
                        $pending[] = (string)$name;
                }
            }
            if ( isset( $step['references'] ) && is_array( $step['references'] ) )
                foreach ( array_keys( $step['references'] ) as $name )
                    $pending[] = (string)$name;
        }
    }

    /**
     * Runs the steps.
     *
     * @param expMigrationContext $context
     * @return string[] what was done
     * @throws RuntimeException at the first step that fails, with where it is
     */
    public function up( $context )
    {
        $this->tellFile( $context );
        $lines = array();
        $this->runSteps( $this->document['steps'], 'steps', $context, array(), $lines );
        return $lines;
    }

    /**
     * Tells the context which migration's steps run, so a step finds the files next to it (expMigrationContext::
     * migrationFile()).
     *
     * @param expMigrationContext|null $context
     */
    protected function tellFile( $context )
    {
        if ( $context instanceof expMigrationContext )
            $context->setMigrationFile( $this->file() );
    }

    /**
     * Whether down() can undo the migration: it has "down:", or every step is of a type that can revert it and none is
     * a loop (a type that can revert only some of its steps, expMigrationPartlyRevertibleStepType, is asked for each).
     *
     * @return bool
     */
    public function reversible()
    {
        if ( $this->document['down'] !== null )
            return true;
        foreach ( $this->document['steps'] as $step )
        {
            if ( !is_array( $step ) || array_key_exists( 'foreach', $step ) || array_key_exists( 'while', $step ) )
                return false;
            $type = $this->registry->get( isset( $step['type'] ) ? $step['type'] : null );
            if ( !$type instanceof expMigrationRevertibleStepType )
                return false;
            if ( $type instanceof expMigrationPartlyRevertibleStepType
                 && !$type->revertible( expMigrationStepSchema::withDefaults( $type->schema(), self::ownKeys( $step ) ) ) )
                return false;
        }
        return true;
    }

    /**
     * Undoes the migration: the "down:" steps, else the revert() of every step, the last first.
     *
     * @param expMigrationContext $context
     * @return string[]
     * @throws RuntimeException
     */
    public function down( $context )
    {
        if ( !$this->reversible() )
            throw new RuntimeException( $this->id() . ' cannot be undone by itself (no down: and a step that cannot revert itself); restore the caption taken before it' );
        $this->tellFile( $context );
        $lines = array();
        if ( $this->document['down'] !== null )
        {
            $this->runSteps( $this->document['down'], 'down', $context, array(), $lines );
            return $lines;
        }
        $idmapErrors = array();
        $steps = $this->document['steps'];
        for ( $i = count( $steps ) - 1; $i >= 0; $i-- )
        {
            $step = $steps[$i];
            $at = "steps[$i] (" . $step['type'] . ')';
            $scope = $this->scope( $context, array(), $idmapErrors );
            if ( $idmapErrors )
                throw new RuntimeException( 'the id map: ' . implode( '; ', $idmapErrors ) );
            if ( $this->skipReason( $step, $context ) !== null
                 || ( array_key_exists( 'if', $step ) && !expMigrationCondition::evaluate( $step['if'], $scope, true ) ) )
                continue;
            $type = $this->registry->get( $step['type'] );
            try
            {
                $own = $this->resolvedKeys( $type, $step, $scope, $at );
                foreach ( (array)$type->revert( $own, $context ) as $line )
                    $lines[] = (string)$line;
            }
            catch ( Throwable $e )
            {
                throw new RuntimeException( "$at: revert: " . $e->getMessage(), 0, $e );
            }
        }
        return $lines;
    }

    // ---- running ----------------------------------------------------------------------------------------------

    /**
     * @param array $steps
     * @param string $where
     * @param expMigrationContext $context
     * @param array $loop the loop variables
     * @param string[] $lines
     * @throws RuntimeException
     */
    protected function runSteps( array $steps, $where, $context, array $loop, array &$lines )
    {
        foreach ( $steps as $i => $step )
        {
            $at = $where . "[$i]";
            $label = $at . ( isset( $step['type'] ) ? ' (' . $step['type'] . ')' : '' );
            $skip = $this->skipReason( $step, $context );
            if ( $skip !== null )
            {
                $lines[] = "skipped $label: $skip";
                continue;
            }
            $idmapErrors = array();
            $scope = $this->scope( $context, $loop, $idmapErrors );
            if ( $idmapErrors )
                throw new RuntimeException( 'the id map: ' . implode( '; ', $idmapErrors ) );
            if ( array_key_exists( 'if', $step ) )
            {
                try
                {
                    $true = expMigrationCondition::evaluate( $step['if'], $scope, true );
                }
                catch ( Throwable $e )
                {
                    throw new RuntimeException( "$label.if: " . $e->getMessage(), 0, $e );
                }
                if ( !$true )
                {
                    $lines[] = "skipped $label: the condition is false";
                    continue;
                }
            }
            if ( array_key_exists( 'foreach', $step ) )
            {
                $this->runForeach( $step, $at, $context, $loop, $scope, $lines );
                continue;
            }
            if ( array_key_exists( 'while', $step ) )
            {
                $this->runWhile( $step, $at, $context, $loop, $lines );
                continue;
            }
            $this->runStep( $step, $label, $context, $scope, $lines );
        }
    }

    /**
     * @param array $step
     * @param string $at
     * @param expMigrationContext $context
     * @param array $loop
     * @param array $scope
     * @param string[] $lines
     */
    protected function runForeach( array $step, $at, $context, array $loop, array $scope, array &$lines )
    {
        try
        {
            $items = expMigrationReferences::interpolate( $step['foreach'], $scope, true );
        }
        catch ( Throwable $e )
        {
            throw new RuntimeException( "$at.foreach: " . $e->getMessage(), 0, $e );
        }
        if ( !is_array( $items ) )
            throw new RuntimeException( "$at.foreach: " . expMigrationStepSchema::show( $step['foreach'] ) . ' is not a list or a map but ' . expMigrationStepSchema::show( $items ) );
        $as = isset( $step['as'] ) ? (string)$step['as'] : 'item';
        $keyName = isset( $step['key'] ) ? (string)$step['key'] : 'key';
        $index = 0;
        foreach ( $items as $key => $item )
        {
            $this->runSteps( $step['steps'], "$at.steps", $context, array_merge( $loop, array( $as => $item, $keyName => $key, 'index' => $index ) ), $lines );
            $index++;
        }
    }

    /**
     * @param array $step
     * @param string $at
     * @param expMigrationContext $context
     * @param array $loop
     * @param string[] $lines
     */
    protected function runWhile( array $step, $at, $context, array $loop, array &$lines )
    {
        $max = $this->maxIterations( $step );
        $turn = 0;
        while ( true )
        {
            $inner = array_merge( $loop, array( 'index' => $turn ) );
            $idmapErrors = array();
            $scope = $this->scope( $context, $inner, $idmapErrors );
            try
            {
                $again = expMigrationCondition::evaluate( $step['while'], $scope, true );
            }
            catch ( Throwable $e )
            {
                throw new RuntimeException( "$at.while: " . $e->getMessage(), 0, $e );
            }
            if ( !$again )
                break;
            if ( $turn >= $max )
                throw new RuntimeException( "$at: the loop ran $max times and its condition is still true (max)" );
            $this->runSteps( $step['steps'], "$at.steps", $context, $inner, $lines );
            $turn++;
        }
    }

    /**
     * @param array $step
     * @param string $label
     * @param expMigrationContext $context
     * @param array $scope
     * @param string[] $lines
     */
    protected function runStep( array $step, $label, $context, array $scope, array &$lines )
    {
        $type = $this->registry->get( $step['type'] );
        if ( $type === null )
            throw new RuntimeException( "$label: the step type is not registered" );
        try
        {
            $own = $this->resolvedKeys( $type, $step, $scope, $label );
            $context->notify( 'migration/yaml/step/before', array( $context->runId(), $this->id(), $label, $own, $context ) );
            $result = $type->apply( $own, $context );
            $result = self::result( $result );
            foreach ( $result['lines'] as $line )
                $lines[] = $line;
            // the object a step created or changed is reindexed after the run (expMigrationAfterRun)
            if ( $context instanceof expMigrationContext && isset( $result['values']['object_id'] ) && empty( $result['values']['purged'] ) )
                $context->touchObject( $result['values']['object_id'] );
            if ( isset( $step['references'] ) && is_array( $step['references'] ) )
            {
                foreach ( $step['references'] as $name => $key )
                {
                    if ( !array_key_exists( (string)$key, $result['values'] ) )
                        throw new RuntimeException( "references.$name: the step gave no value $key ("
                                                    . ( $result['values'] ? implode( ', ', array_keys( $result['values'] ) ) : 'it gave none' ) . ')' );
                    $context->setReference( (string)$name, $result['values'][(string)$key] );
                }
            }
            $context->notify( 'migration/yaml/step/after', array( $context->runId(), $this->id(), $label, $result, $context ) );
        }
        catch ( Throwable $e )
        {
            $message = $e->getMessage();
            throw new RuntimeException( strpos( $message, $label ) === 0 ? $message : "$label: $message", 0, $e );
        }
    }

    /**
     * A step's own keys with their placeholders replaced, the defaults filled in, checked against the schema.
     *
     * @param expMigrationStepType $type
     * @param array $step
     * @param array $scope
     * @param string $label
     * @return array
     * @throws RuntimeException
     */
    protected function resolvedKeys( expMigrationStepType $type, array $step, array $scope, $label )
    {
        $schema = $type->schema();
        $own = expMigrationStepSchema::withDefaults( $schema, expMigrationReferences::interpolate( self::ownKeys( $step ), $scope, true ) );
        $errors = expMigrationStepSchema::check( $schema, $own, $label, true );
        if ( $errors )
            throw new RuntimeException( implode( '; ', $errors ) );
        return $own;
    }

    /**
     * Why a step does not run in this environment or on this engine, or null.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string|null
     */
    protected function skipReason( array $step, $context )
    {
        if ( isset( $step['environments'] ) && !expMigrationRunner::appliesTo( (array)self::names( $step['environments'] ), $context->environment() ) )
            return 'not for the environment ' . ( $context->environment() !== '' ? $context->environment() : '(none)' );
        if ( isset( $step['engines'] ) && !expMigrationRunner::appliesTo( (array)self::names( $step['engines'] ), $context->engine() ) )
            return 'not for ' . $context->engine();
        return null;
    }

    /**
     * The scope of the placeholders.
     *
     * @param expMigrationContext $context
     * @param array $loop
     * @param string[] $errors problems of the id map
     * @return array
     */
    protected function scope( $context, array $loop, array &$errors )
    {
        return array(
            'ref' => $context->references(),
            'loop' => $loop,
            'idmap' => $this->idmap( $context, $errors ),
            'run' => array( 'id' => $context->runId(), 'environment' => $context->environment(), 'engine' => $context->engine() ),
        );
    }

    /**
     * The id map of the run's environment, from the migrations repository of the run.
     *
     * @param expMigrationContext $context
     * @param string[] $errors
     * @return array
     */
    protected function idmap( $context, array &$errors )
    {
        $environment = (string)$context->environment();
        if ( !isset( $this->idmaps[$environment] ) )
        {
            $repository = $context->option( 'repository' );
            if ( $repository === null || $repository === '' )
                $repository = $context->root() . '/migrations';
            $loadErrors = array();
            $map = expMigrationIdMap::load( (string)$repository, $environment, $loadErrors );
            $this->idmaps[$environment] = array( 'map' => $map, 'errors' => $loadErrors );
        }
        foreach ( $this->idmaps[$environment]['errors'] as $error )
            $errors[] = $error;
        return $this->idmaps[$environment]['map'];
    }

    /**
     * @param array $step
     * @return int the most turns of a while loop
     */
    protected function maxIterations( array $step )
    {
        if ( isset( $step['max'] ) && is_int( $step['max'] ) && $step['max'] > 0 )
            return $step['max'];
        return self::defaultMaxIterations();
    }

    /**
     * migration.ini [StepSettings] MaxLoopIterations, else MAX_ITERATIONS.
     *
     * @return int
     */
    public static function defaultMaxIterations()
    {
        try
        {
            $ini = eZINI::instance( 'migration.ini' );
            if ( $ini->hasVariable( 'StepSettings', 'MaxLoopIterations' ) && (int)$ini->variable( 'StepSettings', 'MaxLoopIterations' ) > 0 )
                return (int)$ini->variable( 'StepSettings', 'MaxLoopIterations' );
        }
        catch ( Throwable $e )
        {
        }
        return self::MAX_ITERATIONS;
    }

    // ---- helpers ----------------------------------------------------------------------------------------------

    /**
     * A step's keys without the engine's, with its mode. Pure.
     *
     * @param array $step
     * @return array
     */
    public static function ownKeys( array $step )
    {
        $own = array();
        foreach ( $step as $key => $value )
            if ( $key === 'mode' || !in_array( $key, self::STEP_KEYS, true ) )
                $own[$key] = $value;
        return $own;
    }

    /**
     * A result of apply() in its full form. Pure.
     *
     * @param mixed $result
     * @return array lines, values
     */
    public static function result( $result )
    {
        if ( !is_array( $result ) )
            return array( 'lines' => array(), 'values' => array() );
        if ( array_key_exists( 'lines', $result ) || array_key_exists( 'values', $result ) )
            return array( 'lines' => array_values( array_map( 'strval', isset( $result['lines'] ) ? (array)$result['lines'] : array() ) ),
                          'values' => isset( $result['values'] ) ? (array)$result['values'] : array() );
        return array( 'lines' => array_values( array_map( 'strval', $result ) ), 'values' => array() );
    }

    /**
     * The names of an environments: or engines: key: a list, or a comma-separated text. Pure.
     *
     * @param mixed $value
     * @return string[]|null null when it is neither
     */
    public static function names( $value )
    {
        if ( is_string( $value ) )
            $value = explode( ',', $value );
        if ( !is_array( $value ) )
            return null;
        $names = array();
        foreach ( $value as $name )
        {
            if ( !is_string( $name ) )
                return null;
            if ( ( $name = trim( $name ) ) !== '' )
                $names[] = $name;
        }
        return $names;
    }

    /**
     * Whether an array is a list (keys 0, 1, 2 ...). Pure.
     *
     * @param array $value
     * @return bool
     */
    public static function isList( array $value )
    {
        return !$value || array_keys( $value ) === range( 0, count( $value ) - 1 );
    }
}
