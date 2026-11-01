<?php
/**
 * File containing the expMigrationSql class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * SQL migrations: the files YYYYMMDDHHMMSS_name.sql (every SQL engine), .<engine>.sql (mysql, pgsql or postgresql,
 * sqlite, oracle) and .mongodb.json of one id are one migration; the engine's own file runs, else the portable one.
 *
 *   -- description: An index for the news list
 *   -- requires: 20261030090000_news_class
 *   -- transactional: no                    no transaction around it (default: yes)
 *   CREATE INDEX exp_news_list ON ezcontentobject ( published );
 *   -- down
 *   DROP INDEX exp_news_list;
 *
 * Statements end with ";" at the end of a line. The part after a line "-- down" undoes the migration
 * (exp:migration:rollback); without it the migration can only be undone by restoring its caption. A .mongodb.json file
 * is a list of commands, or { "up": [...], "down": [...] } (the commands of the step type sql).
 *
 * A migration of this type is a YAML migration of one step of the type sql that names its file (expMigrationStepSql),
 * so it runs, plans, logs and records the same way: the runner, the dry run and exp:migration:check need nothing of
 * their own for it.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationSql
{
    /**
     * The migration of SQL files.
     *
     * @param expMigrationDefinition $definition a definition of type sql
     * @param expMigrationStepRegistry|null $registry
     * @return expMigrationYaml
     * @throws RuntimeException with every problem of the files
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
     * Reads and checks the files of an SQL migration (the lint of exp:migration:check).
     *
     * @param expMigrationDefinition $definition
     * @param expMigrationStepRegistry|null $registry
     * @param string[] $errors
     * @param string[] $warnings
     * @return expMigrationYaml|null null when there is an error
     */
    public static function read( expMigrationDefinition $definition, $registry, array &$errors, array &$warnings )
    {
        if ( $definition->type() !== 'sql' )
        {
            $errors[] = $definition->id() . ' is not an SQL migration (.' . $definition->type() . ')';
            return null;
        }
        $withDown = array();
        $withoutDown = array();
        foreach ( $definition->files() as $engine => $file )
        {
            try
            {
                $parts = expMigrationStepSql::readFile( $file );
            }
            catch ( RuntimeException $e )
            {
                $errors[] = $e->getMessage();
                continue;
            }
            if ( !$parts['up'] )
                $warnings[] = basename( $file ) . ': no statements';
            if ( $parts['down'] !== null )
                $withDown[] = basename( $file );
            else
                $withoutDown[] = basename( $file );
        }
        if ( $errors )
            return null;
        if ( $withDown && $withoutDown )
            $warnings[] = implode( ', ', $withoutDown ) . ' ha' . ( count( $withoutDown ) === 1 ? 's' : 've' ) . ' no "-- down" part: a rollback fails on '
                        . ( count( $withoutDown ) === 1 ? 'its engine' : 'their engines' );
        $step = array( 'type' => 'sql', 'file' => $definition->id() . '.sql' );
        $document = array(
            'steps' => array( $step ),
            'down' => $withDown ? array( $step + array( 'part' => 'down' ) ) : null,
            'transactional' => $definition->transactional(),
        );
        $registry = $registry ?: expMigrationStepRegistry::fromSettings();
        foreach ( expMigrationYaml::validate( $document, $registry, $warnings ) as $error )
            $errors[] = $error;
        return $errors ? null : new expMigrationYaml( $definition, $document, $registry );
    }
}
