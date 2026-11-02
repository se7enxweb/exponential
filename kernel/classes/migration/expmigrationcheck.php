<?php
/**
 * File containing the expMigrationCheck class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The lint of exp:migration:check: whether migration files would run, without a database and without running
 * anything of them. Checked:
 *
 *   names        YYYYMMDDHHMMSS_name.type with a valid date, a known type, an engine only on SQL files
 *   headers      known keys only, valid values (expMigrationDefinition); a missing description is a warning
 *   order        every requirement exists (and runs in the environment, when one is given), no cycle, no id twice
 *   extensions   the extensions a migration requires are active (only for the installation's directories)
 *   PHP          the file parses, declares exactly one class that extends expMigrationPhp, no class name is
 *                declared by two files, no method PHP would refuse with a fatal error (expMigrationPhp::methodClashes(),
 *                read before the file loads: a helper named definition() or file(), a hook with another signature),
 *                and the class loads (loading declares it; nothing of it is run)
 *   YAML         the file is YAML (symfony/yaml), its document and every step are valid: a registered step type,
 *                its mode and keys against the type's schema, conditions, loops, references and placeholders
 *                (expMigrationYaml); a migration without steps is a warning
 *   SQL, INI     every file of an SQL migration reads (statements, a "-- down" part, the JSON of MongoDB), every block
 *                of an INI migration names its file and every line is a setting (expMigrationSql, expMigrationIni)
 *
 * The report (exp-migration/1, mode "check"): checked (count), migrations (id, source, type, files, ok, errors,
 * warnings), errors, warnings, exit (0 when nothing is wrong, 1 otherwise).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationCheck
{
    /**
     * Checks migrations.
     *
     * @param array $options paths (files or folders to check instead of the installation's migration directories,
     *                       relative to root or absolute), environment (null: every migration), root, locator
     *                       (expMigrationLocator, for a test), extensions (string[] the active ones; null: not checked),
     *                       registry (expMigrationStepRegistry, for a test; default the step types of migration.ini)
     * @return array the report
     */
    public static function run( array $options = array() )
    {
        $root = rtrim( isset( $options['root'] ) && $options['root'] !== '' ? (string)$options['root'] : (string)eZSys::rootDir(), '/' );
        $report = array( 'schema' => expMigrationRunner::REPORT, 'mode' => 'check', 'checked' => 0, 'directories' => array(),
                         'migrations' => array(), 'errors' => array(), 'warnings' => array(), 'exit' => expUpgrade::EXIT_OK );
        $paths = isset( $options['paths'] ) ? array_values( array_filter( (array)$options['paths'], 'strlen' ) ) : array();
        $files = array();
        $extensions = array_key_exists( 'extensions', $options ) ? $options['extensions'] : null;
        if ( isset( $options['locator'] ) )
            $locator = $options['locator'];
        else if ( $paths )
        {
            $directories = array();
            foreach ( $paths as $path )
            {
                $absolute = $path[0] === '/' ? rtrim( $path, '/' ) : $root . '/' . rtrim( strpos( $path, './' ) === 0 ? substr( $path, 2 ) : $path, '/' );
                if ( is_file( $absolute ) )
                {
                    $files[] = realpath( $absolute );
                    $absolute = dirname( $absolute );
                }
                else if ( !is_dir( $absolute ) )
                {
                    $report['errors'][] = "$path: no such file or folder";
                    continue;
                }
                $directories[$absolute] = array( 'source' => 'site', 'path' => $absolute );
            }
            $locator = new expMigrationLocator( array_values( $directories ) );
        }
        else
        {
            $locator = expMigrationLocator::fromSettings();
            if ( $extensions === null && class_exists( 'eZExtension' ) )
                $extensions = eZExtension::activeExtensions();
        }
        foreach ( $locator->searchedDirectories() as $directory )
            $report['directories'][] = array( 'source' => $directory['source'], 'path' => self::relative( $directory['path'], $root ),
                                              'exists' => is_dir( $directory['path'] ) );

        $definitions = $locator->find();
        if ( $files )
            $definitions = array_values( array_filter( $definitions, function ( $definition ) use ( $files ) {
                foreach ( $definition->files() as $file )
                    if ( in_array( realpath( $file ), $files, true ) )
                        return true;
                return false;
            } ) );
        $byId = array();
        foreach ( $locator->errors() as $error )
            $report['errors'][] = self::relative( $error, $root );
        $graph = new expMigrationGraph( $definitions, array( 'environment' => isset( $options['environment'] ) && $options['environment'] !== '' ? (string)$options['environment'] : null,
                                                             'extensions' => $extensions ) );
        foreach ( $graph->errors() as $error )
            $report['errors'][] = self::relative( $error, $root );

        $classes = array();
        foreach ( $definitions as $definition )
        {
            $entry = array( 'id' => $definition->id(), 'source' => $definition->source(), 'type' => $definition->type(),
                            'files' => array_values( array_map( function ( $file ) use ( $root ) { return expMigrationCheck::relative( $file, $root ); }, $definition->files() ) ),
                            'ok' => true, 'errors' => array(), 'warnings' => array() );
            foreach ( $definition->errors() as $error )
                $entry['errors'][] = self::relative( $error, $root );
            if ( trim( (string)$definition->description() ) === '' )
                $entry['warnings'][] = 'no description in the header';
            if ( in_array( $definition->type(), array( 'yml', 'sql', 'ini' ), true ) )
                self::checkYaml( $definition, $entry, isset( $options['registry'] ) ? $options['registry'] : null );
            else
                self::checkPhp( $definition, $entry, $classes );
            $entry['ok'] = !$entry['errors'];
            foreach ( $entry['errors'] as $error )
                if ( !in_array( $error, $report['errors'], true ) )
                    $report['errors'][] = $definition->id() . ': ' . $error;
            foreach ( $entry['warnings'] as $warning )
                $report['warnings'][] = $definition->id() . ': ' . $warning;
            $byId[] = $entry;
        }
        $report['migrations'] = $byId;
        $report['checked'] = count( $byId );
        if ( !$report['checked'] && !$report['errors'] )
            $report['warnings'][] = 'no migrations found';
        if ( $report['errors'] )
            $report['exit'] = expUpgrade::EXIT_FAILED;
        return $report;
    }

    /**
     * The checks of a PHP migration.
     *
     * @param expMigrationDefinition $definition
     * @param array $entry its errors are added
     * @param array $classes class (lower case) => file, of the files checked before
     */
    protected static function checkPhp( expMigrationDefinition $definition, array &$entry, array &$classes )
    {
        $file = (string)$definition->file();
        $code = is_file( $file ) ? file_get_contents( $file ) : false;
        if ( $code === false )
        {
            $entry['errors'][] = 'the file cannot be read';
            return;
        }
        $syntax = self::syntaxError( $code );
        if ( $syntax !== null )
        {
            $entry['errors'][] = 'PHP syntax: ' . $syntax;
            return;
        }
        $declared = expMigrationPhp::classesIn( $code );
        if ( !$declared )
        {
            $entry['errors'][] = 'no class: a PHP migration declares one class that extends expMigrationPhp';
            return;
        }
        foreach ( $declared as $class )
        {
            $key = strtolower( $class );
            if ( isset( $classes[$key] ) )
            {
                $entry['errors'][] = "the class $class is declared by " . basename( $classes[$key] ) . ' too: give each migration its own class name or namespace';
                return;
            }
            $classes[$key] = $file;
        }
        if ( strpos( $code, 'namespace ' ) === false )
            $entry['warnings'][] = 'no namespace: two migrations of different sites or extensions could declare the same class';
        try
        {
            expMigrationPhp::load( $definition );
        }
        catch ( Throwable $e )
        {
            $entry['errors'][] = $e->getMessage();
        }
    }

    /**
     * The checks of a YAML migration: the YAML, the document, every step against its type's schema, conditions,
     * loops, references and placeholders (expMigrationYaml::read()). Nothing is run.
     *
     * @param expMigrationDefinition $definition
     * @param array $entry its errors and warnings are added
     * @param expMigrationStepRegistry|null $registry null: the step types of migration.ini
     */
    protected static function checkYaml( expMigrationDefinition $definition, array &$entry, $registry = null )
    {
        $errors = array();
        $warnings = array();
        // SQL and INI migrations are read as the YAML migration of their steps
        $reader = array( 'yml' => 'expMigrationYaml', 'sql' => 'expMigrationSql', 'ini' => 'expMigrationIni' )[$definition->type()];
        $reader::read( $definition, $registry, $errors, $warnings );
        foreach ( $errors as $error )
            $entry['errors'][] = $error;
        foreach ( $warnings as $warning )
            $entry['warnings'][] = $warning;
    }

    /**
     * The first syntax error of PHP code, or null. Pure (the tokenizer parses, nothing is run).
     *
     * @param string $code
     * @return string|null
     */
    public static function syntaxError( $code )
    {
        try
        {
            token_get_all( (string)$code, TOKEN_PARSE );
        }
        catch ( ParseError $e )
        {
            return $e->getMessage() . ' on line ' . $e->getLine();
        }
        return null;
    }

    /**
     * A path (or a message that contains one) relative to the installation's root. Pure.
     *
     * @param string $text
     * @param string $root
     * @return string
     */
    public static function relative( $text, $root )
    {
        $root = rtrim( (string)$root, '/' );
        return $root === '' ? (string)$text : str_replace( $root . '/', '', (string)$text );
    }
}
