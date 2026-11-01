<?php
/**
 * File containing the expMigrationGraph class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The migrations in the order they run.
 *
 * Order: by id, that is by the timestamp first and then by name; a migration comes after every migration it names in
 * requires, wherever that one's timestamp puts it (expDependencyOrder). With an environment, only the migrations
 * that run in it are kept (expMigrationDefinition::appliesTo()).
 *
 * Errors (errors()): an id defined twice (the first definition found counts: site before extensions before packages),
 * a requirement that names no migration, a requirement that does not run in the environment, an extension
 * requirement whose extension is not active (when the active extensions are given), and a cycle in requires. A
 * migration in a cycle, or waiting for one, is kept at the end of the order, so a listing still shows it.
 *
 * Pure: no settings, no database, no files.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationGraph
{
    /** @var expMigrationDefinition[] id => definition, in order */
    protected $migrations = array();

    /** @var string[] */
    protected $errors = array();

    /** @var string[] ids that cannot run because of a cycle */
    protected $blocked = array();

    /**
     * @param expMigrationDefinition[] $definitions as the locator found them
     * @param array $options 'environment' => string (null: every migration), 'extensions' => string[] the active
     *                       extensions (null: extension requirements are not checked), 'kernel' => string[] the ids of
     *                       the kernel's upgrade steps (expMigrationUpgradeSource): a requirement naming one is met by
     *                       the order, as the steps run before every other migration
     */
    public function __construct( array $definitions, array $options = array() )
    {
        $environment = isset( $options['environment'] ) ? (string)$options['environment'] : null;
        $extensions = isset( $options['extensions'] ) ? array_flip( (array)$options['extensions'] ) : null;
        $kernel = isset( $options['kernel'] ) ? array_flip( array_map( 'strval', (array)$options['kernel'] ) ) : array();

        $all = array();
        foreach ( $definitions as $definition )
        {
            $id = $definition->id();
            if ( isset( $all[$id] ) )
            {
                $this->errors[] = "the migration $id is defined twice (" . $all[$id]->source() . ': ' . $all[$id]->file() . ', '
                                . $definition->source() . ': ' . $definition->file() . ')';
                continue;
            }
            $all[$id] = $definition;
        }

        $byId = array();
        foreach ( $all as $id => $definition )
            if ( $environment === null || $definition->appliesTo( $environment ) )
                $byId[$id] = $definition;

        $requires = array();
        foreach ( $byId as $id => $definition )
        {
            $requires[$id] = array();
            foreach ( $definition->requiredMigrations() as $dep )
            {
                if ( $dep === $id )
                {
                    $this->errors[] = "the migration $id requires itself";
                    continue;
                }
                if ( !isset( $all[$dep] ) && isset( $kernel[$dep] ) )
                    continue;
                if ( !isset( $all[$dep] ) )
                    $this->errors[] = "the migration $id requires $dep, which does not exist";
                else if ( !isset( $byId[$dep] ) )
                    $this->errors[] = "the migration $id requires $dep, which does not run in the environment $environment";
                $requires[$id][] = $dep;
            }
            if ( $extensions !== null )
                foreach ( $definition->requiredExtensions() as $extension )
                    if ( !isset( $extensions[$extension] ) )
                        $this->errors[] = "the migration $id requires the extension $extension, which is not active";
        }

        $ids = array_map( 'strval', array_keys( $byId ) );
        sort( $ids, SORT_STRING );
        $blocked = array();
        foreach ( expDependencyOrder::sort( $ids, $requires, $blocked ) as $id )
            $this->migrations[$id] = $byId[$id];
        $this->blocked = $blocked;
        if ( $blocked )
        {
            $cycle = expDependencyOrder::cycle( $blocked, $requires );
            $this->errors[] = 'the migrations ' . implode( ', ', $blocked ) . ' wait for each other (a cycle in requires'
                            . ( $cycle ? ': ' . implode( ' -> ', $cycle ) : '' ) . ')';
        }
    }

    /**
     * @return expMigrationDefinition[] every migration, in the order they run
     */
    public function migrations()
    {
        return array_values( $this->migrations );
    }

    /** @return string[] the ids, in order */
    public function ids()
    {
        return array_map( 'strval', array_keys( $this->migrations ) );
    }

    /**
     * @param string $id
     * @return expMigrationDefinition|null
     */
    public function get( $id )
    {
        return isset( $this->migrations[$id] ) ? $this->migrations[$id] : null;
    }

    /**
     * The migrations a migration needs, directly or through others, in the order they run; the migration itself last.
     *
     * @param string $id
     * @return expMigrationDefinition[]
     */
    public function closure( $id )
    {
        if ( !isset( $this->migrations[$id] ) )
            return array();
        $need = array( $id => true );
        $stack = array( $id );
        while ( $stack )
        {
            $current = array_pop( $stack );
            foreach ( $this->migrations[$current]->requiredMigrations() as $dep )
                if ( isset( $this->migrations[$dep] ) && !isset( $need[$dep] ) )
                {
                    $need[$dep] = true;
                    $stack[] = $dep;
                }
        }
        $out = array();
        foreach ( $this->migrations as $mid => $definition )
            if ( isset( $need[$mid] ) )
                $out[] = $definition;
        return $out;
    }

    /** @return string[] the ids that cannot run because of a cycle */
    public function blocked()
    {
        return $this->blocked;
    }

    /** @return string[] */
    public function errors()
    {
        return $this->errors;
    }
}
